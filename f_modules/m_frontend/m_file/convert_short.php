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
include_once 'f_core/f_classes/class.conversion.php';
include_once 'f_core/f_classes/class_moovrelocator/moovrelocator.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');

$flv_formats = array('flv', 'vp3', 'vp5', 'vp6', 'vp6a', 'vp6f');
$rs          = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE
                `cfg_name` LIKE '%server_%' OR
                `cfg_name` LIKE '%thumb%' OR
                `cfg_name`='log_short_conversion' OR
                `cfg_name`='file_approval' OR
                `cfg_name`='user_subscriptions' OR
                `cfg_name`='conversion_short_que' OR
                `cfg_name`='conversion_short_previews' OR
                `cfg_name`='conversion_source_short';
                ");
while (!$rs->EOF) {
    $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
    @$rs->MoveNext();
}
$rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");
while (!$rs->EOF) {
    $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
    @$rs->MoveNext();
}

if (isset($_SERVER['argv'][1]) and isset($_SERVER['argv'][2])) {
    $video_id   = $class_filter->clr_str($_SERVER['argv'][1]);
    $user_key   = $class_filter->clr_str($_SERVER['argv'][2]);
    $file_name  = html_entity_decode($class_database->singleFieldValue('db_shortfiles', 'file_name', 'file_key', $video_id), ENT_QUOTES, 'UTF-8');
    $src_folder = $cfg["upload_files_dir"] . '/' . $user_key . '/s/';
    $dst_folder = $cfg["media_files_dir"] . '/' . $user_key . '/s/';
    $src        = $src_folder . $file_name;

    if (file_exists($src) && is_file($src)) {
        $conv = new VVideo();
        $conv->log_setup($video_id, ($cfg["log_short_conversion"] == 1 ? true : false));

        if ($conv->load($src)) {
            $conv->log("Loading video short: " . $src . "\n" . $conv->get_data_string() . "\n");

            $flv_360p_processed = false;
            $flv_480p_processed = false;

            $li = "---------------------------------------------";
            $ls = "\n\n" . $li . "\n";
            $le = "\n" . $li . "\n";

            $mp4_short_full_processed = false;
            $mp4_short_prev_processed = false;

            $eid                                = gs($video_id);
            $pvl                                = 7;
            $cfg['conversion_mp4_short_active'] = 1;

            /* MP4/short */
            if ($cfg['conversion_mp4_short_active'] == '1') {
                $conv->log($ls . 'Starting MP4/shortvideo conversion!' . $le);
                $dst_mp4_nomov_prev = $dst_folder . $video_id . '.short.nomov.mp4';
                $dst_mp4_nomov_full = $dst_folder . $eid . '.short.nomov.mp4';

                if ($conv->convert_to_mp4_short($src, $dst_mp4_nomov_full, '', 'short')) {
                    $mp4_short_full_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to MP4/short (disabled in admin)!' . $le);
            }
            if ($mp4_short_prev_processed === true) {
                $conv->log($ls . 'MP4/shortvideo Preview Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $video_id . '.short.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_prev, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_prev, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_prev);
                }
            }
            if ($mp4_short_full_processed === true) {
                $conv->log($ls . 'MP4/shortvideo Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $eid . '.short.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_full, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_full, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_full);
                }
            }

            $conv->log($ls . 'Extracting large thumbnail (360x640)' . $le);
            $thumbs = $conv->extract_thumbs_short(array($dst_mp4, 'thumb'), $video_id, $user_key);

            $cfg["thumbs_width"]  = 180;
            $cfg["thumbs_height"] = 320;
            $conv->log($ls . 'Extracting smaller thumbnails (' . $cfg["thumbs_width"] . 'x' . $cfg["thumbs_height"] . ')' . $le);
            $thumbs    = $conv->extract_thumbs_short($dst_mp4, $video_id, $user_key);
            $is_hd     = 1;
            $is_mobile = 1;

            if ($mp4_short_full_processed) {
                $_sfp     = $dst_folder . md5($cfg["global_salt_key"] . $video_id) . '.short.mp4';
                $previews = $conv->extract_preview_thumbs_short($_sfp, $video_id, $user_key, 1);
            }

            $is_fp = $mp4_short_prev_processed ? 1 : 0;
            $vpv   = file_exists($cfg["media_files_dir"] . "/" . $user_key . "/s/" . md5($video_id . "_preview") . ".mp4");
            //upload to another server here
            $duration = (float) $conv->data['duration_seconds'];
            $duration = $duration > 60 ? 60 : $duration;
            $sql      = sprintf("UPDATE `db_shortfiles` SET `has_preview`='%s', `thumb_preview`='%s', `file_mobile`='%s', `file_hd`='%s', `file_duration`='%s' WHERE `file_key`='%s' LIMIT 1;", $is_fp, (int) $vpv, $is_mobile, $is_hd, $duration, $video_id);

            $conv->log($ls . "Executing update query: " . $sql . $le);
            $db->query($sql);
            if (!$db->Affected_Rows()) {
                require 'f_core/config.database.php';
                $db = &ADONewConnection($cfg_dbtype);
                if (!$db->Connect($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname)) {
                    $conv->log($ls . 'Failed database connection, reconnecting!' . $le);
                }

                $db->query($sql);
            }
            if ($db->Affected_Rows()) {
                $conv->log($ls . 'Database data updated!' . $le);
                if ($cfg["conversion_source_short"] == 0 and (file_exists($dst_folder . $video_id . '.short.mp4') or file_exists($dst_folder . md5($cfg["global_salt_key"] . $video_id) . '.short.mp4'))) {
                    $conv->log($ls . 'Deleting source video short ' . $src . $le);
                    VFileinfo::doDelete($src);
                }
                if ($cfg["conversion_short_que"] == 1) {
                    $que = sprintf("UPDATE `db_shortque` SET `state`='2', `end_time`='%s' WHERE `file_key`='%s' AND `usr_key`='%s' AND `state`='1' LIMIT 1;", date("Y-m-d H:i:s"), $video_id, $user_key);
                    $db->execute($que);
                    if ($cfg["file_approval"] == 0) {
                        $act = sprintf("UPDATE `db_shortfiles` SET `approved`='1' WHERE `file_key`='%s' LIMIT 1;", $video_id);
                        $db->execute($act);
                    }
                }
                /* admin and subscribers notification */
                $db_approved = ($cfg["file_approval"] == 1 ? 0 : 1);
                $type        = 'short';
                $usr_id      = $class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $video_id);

                $notify          = $db_approved == 1 ? VUpload::notifySubscribers($usr_id, $type, $video_id, '', $user_key) : VUpload::notifySubscribers(0, $type, $video_id, '', $user_key);
                $conv->log_clean = true;
            } else {
                $conv->log($ls . 'Failed to execute video short update query!' . $le);
            }
        } else {
            $conv->log($ls . 'Failed to load video short: ' . $src . $le);
        }
    }
}

function gs($k)
{
    global $cfg;
    return md5($cfg["global_salt_key"] . $k);
}
