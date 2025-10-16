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

$rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%conversion_%' OR `cfg_name` LIKE '%server_%' OR `cfg_name` LIKE '%thumb%' OR `cfg_name`='log_audio_conversion' OR `cfg_name`='file_approval' OR `cfg_name`='user_subscriptions' OR `cfg_name`='conversion_audio_que' OR `cfg_name`='conversion_source_audio' OR `cfg_name`='conversion_audio_previews';");
while (!$rs->EOF) {
    $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
    @$rs->MoveNext();
}

if (isset($_SERVER['argv'][1]) and isset($_SERVER['argv'][2])) {
    $file_key   = $class_filter->clr_str($_SERVER['argv'][1]);
    $user_key   = $class_filter->clr_str($_SERVER['argv'][2]);
    $file_name  = html_entity_decode($class_database->singleFieldValue('db_audiofiles', 'file_name', 'file_key', $file_key), ENT_QUOTES, 'UTF-8');
    $src_folder = $cfg["upload_files_dir"] . '/' . $user_key . '/a/';
    $dst_folder = $cfg["media_files_dir"] . '/' . $user_key . '/a/';
    $src        = $src_folder . $file_name;

    if (file_exists($src) && is_file($src)) {
        $conv = new VAudio();
        $conv->log_setup($file_key, ($cfg["log_audio_conversion"] == 1 ? true : false));

        if ($conv->load($src)) {
            $format = strtolower(substr($file_name, -3));
            $conv->log("Loading audio: " . $src . "\n" . $conv->get_data_string() . "\n");

            $mp3_full_processed = false;
            $mp3_prev_processed = false;
            $dst_mp3_full       = $dst_folder . gs($file_key) . '.mp3';
            $dst_mp3_prev       = $dst_folder . $file_key . '.mp3';
            $eid                = gs($file_key);
            $pvl                = 30;

            $li = "---------------------------------------------";
            $ls = "\n\n" . $li . "\n";
            $le = "\n" . $li . "\n";
            $conv->log($ls . 'Starting MP3 conversion!' . $le);

            if ($format == 'mp3' and $cfg['conversion_mp3_redo'] == 1) {
                if ($cfg["conversion_audio_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mp3($src, $file_key, $user_key, 1)) {
                    $conv->log($ls . 'Creating preview ' . $src . ' to ' . $dst_mp3_prev . $le);
                    $mp3_prev_processed = true;
                }
                $conv->log($ls . 'Copying ' . $src . ' to ' . $dst_mp3_full . $le);
                copy($src, $dst_mp3_full);

                $mp3_copy           = true;
                $mp3_full_processed = true;
            } else {
                if ($cfg["conversion_audio_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mp3($src, $file_key, $user_key, 1)) {
                    $mp3_prev_processed = true;
                }
                if ($conv->convert_to_mp3($src, $file_key, $user_key)) {
                    $mp3_full_processed = true;
                }
            }

            if ($mp3_full_processed === true) {
                $conv->log($ls . 'Starting AAC conversion!' . $le);
                if ($cfg["conversion_audio_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_aac($src, $file_key, $user_key, 1)) {
                }
                if ($conv->convert_to_aac($src, $file_key, $user_key)) {
                }
            }

            if ($mp3_full_processed === true) {
                $is_hd     = 0;
                $is_mobile = 1;
                $is_pv     = $mp3_prev_processed ? 1 : 0;

                $conv->log($ls . 'Creating large audio thumbnail (640x480)' . $le);
                $thumbs = $conv->create_thumbs(0, $file_key, $user_key);

                $conv->log($ls . 'Creating small audio thumbnail (320x240)' . $le);
                $thumbs = $conv->create_thumbs(1, $file_key, $user_key);

                $sql = sprintf("UPDATE `db_audiofiles` SET `has_preview`='%s', `file_mobile`='%s', `file_hd`='%s', `file_duration`='%s' WHERE `file_key`='%s' LIMIT 1;", $is_pv, $is_mobile, $is_hd, (float) $conv->data['duration_seconds'], $file_key);

                $conv->log($ls . "Executing update query: " . $sql . $le);
                $db->query($sql);
                if ($db->affected_rows()) {
                    $conv->log($ls . 'Database data updated!' . $le);
                    if ($cfg["conversion_source_audio"] == 0) {
                        $conv->log($ls . 'Deleting source audio ' . $src . $le);
                        VFileinfo::doDelete($src);
                    }
                    if ($cfg["conversion_audio_que"] == 1) {
                        $que = sprintf("UPDATE `db_audioque` SET `state`='2', `end_time`='%s' WHERE `file_key`='%s' AND `usr_key`='%s' AND `state`='1' LIMIT 1;", date("Y-m-d H:i:s"), $file_key, $user_key);
                        $db->query($que);
                        if ($cfg["file_approval"] == 0) {
                            $act = sprintf("UPDATE `db_audiofiles` SET `approved`='1' WHERE `file_key`='%s' LIMIT 1;", $file_key);
                            $db->execute($act);
                        }
                    }
                    /* admin and subscribers notification */
                    $db_approved = ($cfg["file_approval"] == 1 ? 0 : 1);
                    $type        = 'audio';
                    $usr_id      = $class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $file_key);

                    $notify = $db_approved == 1 ? VUpload::notifySubscribers($usr_id, $type, $file_key, '', $user_key) : VUpload::notifySubscribers(0, $type, $file_key, '', $user_key);

                    $conv->log_clean = true;
                } else {
                    $conv->log($ls . 'Failed to execute audio update query!' . $le);
                }
            }
        } else {
            $conv->log($ls . 'Failed to load audio: ' . $src . $le);
        }
    }
}
function gs($k)
{global $cfg;return md5($cfg["global_salt_key"] . $k);}
