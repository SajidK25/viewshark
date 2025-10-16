<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);

set_time_limit(0);

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';
include_once 'f_core/f_classes/class_thumb/ThumbLib.inc.php';
include_once 'f_core/f_classes/class.conversion.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');

$rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%conversion_%' OR `cfg_name` LIKE '%server_%' OR `cfg_name` LIKE '%thumb%' OR `cfg_name`='file_approval' OR `cfg_name`='user_subscriptions' OR `cfg_name`='conversion_document_que' OR `cfg_name`='conversion_source_doc' OR `cfg_name`='conversion_doc_previews';");
while (!$rs->EOF) {
    $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
    @$rs->MoveNext();
}

if (isset($_SERVER['argv'][1]) and isset($_SERVER['argv'][2])) {
    $doc_id         = $class_filter->clr_str($_SERVER['argv'][1]);
    $user_key       = $class_filter->clr_str($_SERVER['argv'][2]);
    $file_name      = html_entity_decode($class_database->singleFieldValue('db_docfiles', 'file_name', 'file_key', $doc_id), ENT_QUOTES, 'UTF-8');
    $src_folder     = $cfg["upload_files_dir"] . '/' . $user_key . '/d/';
    $dst_folder     = $cfg["media_files_dir"] . '/' . $user_key . '/d/';
    $dst_folder_tmb = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $doc_id . '/';
    $src            = $src_folder . $file_name;

    if (file_exists($src) && is_file($src)) {
        $conv = new VDocument();
        $conv->log_setup($doc_id, ($cfg["log_doc_conversion"] == 1 ? true : false));
        if ($conv->load($src)) {
            $conv->log("Loading document: " . $src . "\n");
            $db_q               = null;
            $pdf_processed_full = false;
            $pdf_processed_prev = false;
            $swf_processed      = false;
            $eid                = gs($doc_id);

            $li = "---------------------------------------------";
            $ls = "\n\n" . $li . "\n";
            $le = "\n" . $li . "\n";
            $conv->log($ls . 'Starting PDF conversion!' . $le);

            if ($cfg["conversion_doc_previews"] == 1 and $conv->convert_to_pdf($src, $doc_id, $user_key, 1)) {
                $pdf_processed_prev = true;
                $db_q .= ", `has_preview`='1'";
            }
            if ($conv->convert_to_pdf($src, $doc_id, $user_key)) {
                $pdf_processed_full = true;
                $db_q .= ", `file_pdf`='1'";
            }

            if ($cfg["conversion_pdf2swf_bypass"] == 0) {
                $conv->log($ls . 'Starting SWF conversion!' . $le);
                if ($conv->convert_to_swf($doc_id, $user_key)) {
                    $swf_processed = true;
                    $db_q .= ", `file_swf`='1'";
                }
            } else {
                $conv->log($ls . 'Not converting to SWF. Disabled in admin!' . $le);
            }

            if ($pdf_processed_full === true) {
                $conv->log($ls . 'Creating small document thumbnail (320x240)' . $le);
                if ($conv->createThumbs_ffmpeg($dst_folder_tmb, 1, 320, 240, $doc_id, $user_key)) {
                    $thumbnails = true;
                }

                $conv->log($ls . 'Creating large document thumbnail (640x480)' . $le);
                if ($conv->createThumbs_ffmpeg($dst_folder_tmb, 0, 640, 480, $doc_id, $user_key)) {
                    $thumbnails = true;
                }
            }

            if ($pdf_processed_full === true and $thumbnails === true) {
                $sql = sprintf("UPDATE `db_docfiles` SET `file_mobile`='1', `file_hd`='0' %s WHERE `file_key`='%s' LIMIT 1;", $db_q, $doc_id);
                $conv->log($ls . "Executing update query: " . $sql . $le);

                $db->query($sql);
                if ($cfg["conversion_document_que"] == 1) {
                    $db->query(sprintf("UPDATE `db_docque` SET `state`='2', `end_time`='%s' WHERE `file_key`='%s' AND `usr_key`='%s' AND `state`='1' LIMIT 1;", date("Y-m-d H:i:s"), $doc_id, $user_key));
                    if ($cfg["file_approval"] == 0) {
                        $db->query(sprintf("UPDATE `db_docfiles` SET `approved`='1' WHERE `file_key`='%s' LIMIT 1;", $doc_id));
                    }
                }
                /* admin and subscribers notification */
                $db_approved = ($cfg["file_approval"] == 1 ? 0 : 1);
                $type        = 'doc';
                $usr_id      = $class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $doc_id);

                $notify = $db_approved == 1 ? VUpload::notifySubscribers($usr_id, $type, $doc_id, '', $user_key) : VUpload::notifySubscribers(0, $type, $doc_id, '', $user_key);
            }
            if ($cfg['conversion_source_doc'] == 0) {
                VFileinfo::doDelete($src);
            }
        }
    } else {
        exit;
    }
}
function gs($k)
{global $cfg;return md5($cfg["global_salt_key"] . $k);}
