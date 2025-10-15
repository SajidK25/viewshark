<?php
/*******************************************************************************************************************
| Software Name        : ViewShark
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) ViewShark
| Website              : https://www.viewshark.com
| E-mail               : support@viewshark.com || viewshark@gmail.com
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the ViewShark End-User License Agreement, available online at:
| https://www.viewshark.com/support/license/
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2013-2024 viewshark.com. All rights reserved.
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
                `cfg_name`='log_video_conversion' OR
                `cfg_name`='file_approval' OR
                `cfg_name`='user_subscriptions' OR
                `cfg_name`='conversion_video_que' OR
                `cfg_name`='conversion_video_previews' OR
                `cfg_name`='conversion_source_video';");
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
    $file_name  = html_entity_decode($class_database->singleFieldValue('db_videofiles', 'file_name', 'file_key', $video_id), ENT_QUOTES, 'UTF-8');
    $src_folder = $cfg["upload_files_dir"] . '/' . $user_key . '/v/';
    $dst_folder = $cfg["media_files_dir"] . '/' . $user_key . '/v/';
    $src        = $src_folder . $file_name;

    if (file_exists($src) && is_file($src)) {
        $conv = new VVideo();
        $conv->log_setup($video_id, ($cfg["log_video_conversion"] == 1 ? true : false));

        if ($conv->load($src)) {
            $conv->log("Loading video: " . $src . "\n" . $conv->get_data_string() . "\n");

            $flv_360p_processed = false;
            $flv_480p_processed = false;

            $li = "---------------------------------------------";
            $ls = "\n\n" . $li . "\n";
            $le = "\n" . $li . "\n";

            $mp4_360p_full_processed  = false;
            $mp4_360p_prev_processed  = false;
            $mp4_480p_full_processed  = false;
            $mp4_480p_prev_processed  = false;
            $mp4_720p_full_processed  = false;
            $mp4_720p_prev_processed  = false;
            $mp4_1080p_full_processed = false;
            $mp4_1080p_prev_processed = false;

            $eid = gs($video_id);
            $pvl = 30;

            /* MP4/360p */
            if ($cfg['conversion_mp4_360p_active'] == '1') {
                $conv->log($ls . 'Starting MP4/360p conversion!' . $le);
                $dst_mp4_nomov_prev = $dst_folder . $video_id . '.360p.nomov.mp4';
                $dst_mp4_nomov_full = $dst_folder . $eid . '.360p.nomov.mp4';
                if ($cfg["conversion_video_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mp4($src, $dst_mp4_nomov_prev, true, '360p')) {
                    $mp4_360p_prev_processed = true;
                }
                if ($conv->convert_to_mp4($src, $dst_mp4_nomov_full, '', '360p')) {
                    $mp4_360p_full_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to MP4/360p (disabled in admin)!' . $le);
            }
            if ($mp4_360p_prev_processed === true) {
                $conv->log($ls . 'MP4/360p Preview Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $video_id . '.360p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_prev, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_prev, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_prev);
                }
            }
            if ($mp4_360p_full_processed === true) {
                $conv->log($ls . 'MP4/360p Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $eid . '.360p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_full, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_full, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_full);
                }
            }

            /* MP4/480p */
            if ($cfg['conversion_mp4_480p_active'] == '1') {
                $conv->log($ls . 'Starting MP4/480p conversion!' . $le);
                $dst_mp4_nomov_prev = $dst_folder . $video_id . '.480p.nomov.mp4';
                $dst_mp4_nomov_full = $dst_folder . $eid . '.480p.nomov.mp4';
                if ($cfg["conversion_video_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mp4($src, $dst_mp4_nomov_prev, true, '480p')) {
                    $mp4_480p_prev_processed = true;
                }
                if ($conv->convert_to_mp4($src, $dst_mp4_nomov_full, '', '480p')) {
                    $mp4_480p_full_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to MP4/480p (disabled in admin)!' . $le);
            }
            if ($mp4_480p_prev_processed === true) {
                $conv->log($ls . 'MP4/480p Preview Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $video_id . '.480p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_prev, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_prev, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_prev);
                }
            }
            if ($mp4_480p_full_processed === true) {
                $conv->log($ls . 'MP4/480p Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $eid . '.480p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_full, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_full, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_full);
                }
            }

            /* MP4/720p */
            if ($cfg['conversion_mp4_720p_active'] == '1') {
                $conv->log($ls . 'Starting MP4/720p conversion!' . $le);
                $dst_mp4_nomov_prev = $dst_folder . $video_id . '.720p.nomov.mp4';
                $dst_mp4_nomov_full = $dst_folder . $eid . '.720p.nomov.mp4';
                if ($cfg["conversion_video_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mp4($src, $dst_mp4_nomov_prev, true, '720p')) {
                    $mp4_720p_prev_processed = true;
                }
                if ($conv->convert_to_mp4($src, $dst_mp4_nomov_full, '', '720p')) {
                    $mp4_720p_full_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to MP4/720p (disabled in admin)!' . $le);
            }
            if ($mp4_720p_prev_processed === true) {
                $conv->log($ls . 'MP4/720p Preview Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $video_id . '.720p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_prev, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_prev, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_prev);
                }
            }
            if ($mp4_720p_full_processed === true) {
                $conv->log($ls . 'MP4/720p Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $eid . '.720p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_full, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_full, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_full);
                }
            }
            /* MP4/1080p */
            if ($cfg['conversion_mp4_1080p_active'] == '1') {
                $conv->log($ls . 'Starting MP4/1080p conversion!' . $le);
                $dst_mp4_nomov_prev = $dst_folder . $video_id . '.1080p.nomov.mp4';
                $dst_mp4_nomov_full = $dst_folder . $eid . '.1080p.nomov.mp4';
                if ($cfg["conversion_video_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mp4($src, $dst_mp4_nomov_prev, true, '1080p')) {
                    $mp4_1080p_prev_processed = true;
                }
                if ($conv->convert_to_mp4($src, $dst_mp4_nomov_full, '', '1080p')) {
                    $mp4_1080p_full_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to MP4/1080p (disabled in admin)!' . $le);
            }
            if ($mp4_1080p_prev_processed === true) {
                $conv->log($ls . 'MP4/1080p Preview Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $video_id . '.1080p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_prev, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_prev, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_prev);
                }
            }
            if ($mp4_1080p_full_processed === true) {
                $conv->log($ls . 'MP4/1080p Processed! Relocating moov atom ...' . $le);
                $dst_mp4 = $dst_folder . $eid . '.1080p.mp4';
                if (!$conv->fix_moov_atom($dst_mp4_nomov_full, $dst_mp4)) {
                    $conv->log($ls . 'Failed to relocate moov atom...copying original mp4 file!' . $le);
                    rename($dst_mp4_nomov_full, $dst_mp4);
                    VFileinfo::doDelete($dst_mp4_nomov_full);
                }
            }
            /* Mobile MP4 */
            $mobile_prev_processed = false;
            $mobile_full_processed = false;
            if ($cfg['conversion_mp4_ipad_active'] == '1') {
                $conv->log($ls . 'Starting mobile/ipad conversion!' . $le);
                $dst_mobile_nomob_prev = $dst_folder . $video_id . '.mob.nomob.mp4';
                $dst_mobile_nomob_full = $dst_folder . $eid . '.mob.nomob.mp4';
                if ($cfg["conversion_video_previews"] == 1 and $conv->data['duration_seconds'] > $pvl and $conv->convert_to_mobile($src, $dst_mobile_nomob_prev, true)) {
                    $mobile_prev_processed = true;
                }
                if ($conv->convert_to_mobile($src, $dst_mobile_nomob_full)) {
                    $mobile_full_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to mobile (disabled in admin)!' . $le);
            }

            if ($mobile_prev_processed === true) {
                $conv->log($ls . 'Mobile Processed! Relocating moov atom ...' . $le);
                $dst_mobile = $dst_folder . $video_id . '.mob.mp4';
                if (!$conv->fix_moov_atom($dst_mobile_nomob_prev, $dst_mobile)) {
                    $conv->log($ls . 'Failed to relocated moov atom ... copying original mobile file!' . $le);
                    rename($dst_mobile_nomob_prev, $dst_mobile);
                    VFileinfo::doDelete($dst_mobile_nomob_prev);
                }
            }
            if ($mobile_full_processed === true) {
                $conv->log($ls . 'Mobile Processed! Relocating moov atom ...' . $le);
                $dst_mobile = $dst_folder . $eid . '.mob.mp4';
                if (!$conv->fix_moov_atom($dst_mobile_nomob_full, $dst_mobile)) {
                    $conv->log($ls . 'Failed to relocated moov atom ... copying original mobile file!' . $le);
                    rename($dst_mobile_nomob_full, $dst_mobile);
                    VFileinfo::doDelete($dst_mobile_nomob_full);
                }
            }

            $vpx_360p_processed  = false;
            $vpx_480p_processed  = false;
            $vpx_720p_processed  = false;
            $vpx_1080p_processed = false;

            /* WEBM/360p */
            if ($cfg['conversion_vpx_360p_active'] == '1') {
                $conv->log($ls . 'Starting WEBM/360p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.360p.webm';

                if ($conv->convert_to_vpx($src, $dst_webm, '', '360p')) {
                    $conv->log($ls . 'WEBM/360p Processed!' . $le);

                    $vpx_360p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to WEBM/360p (disabled in admin)!' . $le);
            }
            /* WEBM/480p */
            if ($cfg['conversion_vpx_480p_active'] == '1') {
                $conv->log($ls . 'Starting WEBM/480p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.480p.webm';

                if ($conv->convert_to_vpx($src, $dst_webm, '', '480p')) {
                    $conv->log($ls . 'WEBM/480p Processed!' . $le);

                    $vpx_480p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to WEBM/480p (disabled in admin)!' . $le);
            }
            /* WEBM/720p */
            if ($cfg['conversion_vpx_720p_active'] == '1') {
                $conv->log($ls . 'Starting WEBM/720p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.720p.webm';

                if ($conv->convert_to_vpx($src, $dst_webm, '', '720p')) {
                    $conv->log($ls . 'WEBM/720p Processed!' . $le);

                    $vpx_720p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to WEBM/720p (disabled in admin)!' . $le);
            }
            /* WEBM/1080p */
            if ($cfg['conversion_vpx_1080p_active'] == '1') {
                $conv->log($ls . 'Starting WEBM/1080p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.1080p.webm';

                if ($conv->convert_to_vpx($src, $dst_webm, '', '1080p')) {
                    $conv->log($ls . 'WEBM/1080p Processed!' . $le);

                    $vpx_1080p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to WEBM/1080p (disabled in admin)!' . $le);
            }

            $ogv_360p_processed  = false;
            $ogv_480p_processed  = false;
            $ogv_720p_processed  = false;
            $ogv_1080p_processed = false;

            /* OGV/360p */
            if ($cfg['conversion_ogv_360p_active'] == '1') {
                $conv->log($ls . 'Starting OGV/360p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.360p.ogv';

                if ($conv->convert_to_ogv($src, $dst_webm, '', '360p')) {
                    $conv->log($ls . 'OGV/360p Processed!' . $le);

                    $ogv_360p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to OGV/360p (disabled in admin)!' . $le);
            }
            /* OGV/480p */
            if ($cfg['conversion_ogv_480p_active'] == '1') {
                $conv->log($ls . 'Starting OGV/480p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.480p.ogv';

                if ($conv->convert_to_ogv($src, $dst_webm, '', '480p')) {
                    $conv->log($ls . 'OGV/480p Processed!' . $le);

                    $ogv_480p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to OGV/480p (disabled in admin)!' . $le);
            }
            /* OGV/720p */
            if ($cfg['conversion_ogv_720p_active'] == '1') {
                $conv->log($ls . 'Starting OGV/720p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.720p.ogv';

                if ($conv->convert_to_ogv($src, $dst_webm, '', '720p')) {
                    $conv->log($ls . 'OGV/720p Processed!' . $le);

                    $ogv_720p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to OGV/720p (disabled in admin)!' . $le);
            }
            /* OGV/1080p */
            if ($cfg['conversion_ogv_1080p_active'] == '1') {
                $conv->log($ls . 'Starting OGV/1080p conversion!' . $le);
                $dst_webm = $dst_folder . $video_id . '.1080p.ogv';

                if ($conv->convert_to_ogv($src, $dst_webm, '', '1080p')) {
                    $conv->log($ls . 'OGV/1080p Processed!' . $le);

                    $ogv_1080p_processed = true;
                }
            } else {
                $conv->log($ls . 'Not converting to OGV/1080p (disabled in admin)!' . $le);
            }

            $conv->log($ls . 'Extracting large thumbnail (640x360)' . $le);
            $thumbs = $conv->extract_thumbs(array($src, 'thumb'), $video_id, $user_key);

            $cfg["thumbs_width"]  = 320;
            $cfg["thumbs_height"] = 180;
            $conv->log($ls . 'Extracting smaller thumbnails (' . $cfg["thumbs_width"] . 'x' . $cfg["thumbs_height"] . ')' . $le);
            $thumbs    = $conv->extract_thumbs($src, $video_id, $user_key);
            $is_hd     = ($mp4_720p_full_processed === true or $mp4_720p_prev_processed === true or $mp4_1080p_full_processed === true or $mp4_1080p_prev_processed === true) ? 1 : 0;
            $is_mobile = ($mobile_prev_processed === true or $mobile_full_processed === true) ? 1 : 0;

            if ($mp4_360p_prev_processed) {
                $_sfp     = $dst_folder . $video_id . '.360p.mp4';
                $previews = $conv->extract_preview_thumbs($_sfp, $video_id, $user_key);
            }
            if ($mp4_360p_full_processed) {
                $_sfp     = $dst_folder . md5($cfg["global_salt_key"] . $video_id) . '.360p.mp4';
                $previews = $conv->extract_preview_thumbs($_sfp, $video_id, $user_key, 1);
            }

            $is_fp = ($mp4_360p_prev_processed or $mp4_480p_prev_processed or $mp4_720p_prev_processed or $mp4_1080p_prev_processed) ? 1 : 0;
            $vpv   = file_exists($cfg["media_files_dir"] . "/" . $user_key . "/v/" . md5($video_id . "_preview") . ".mp4");
            //upload to another server here
            $sql = sprintf("UPDATE `db_videofiles` SET `has_preview`='%s', `thumb_preview`='%s', `file_mobile`='%s', `file_hd`='%s', `file_duration`='%s' WHERE `file_key`='%s' LIMIT 1;", $is_fp, (int) $vpv, $is_mobile, $is_hd, (float) $conv->data['duration_seconds'], $video_id);

            $conv->log($ls . "Executing update query: " . $sql . $le);
            $db->query($sql);
            if (!$db->affected_rows()) {
                require 'f_core/config.database.php';
                $db = &ADONewConnection($cfg_dbtype);
                if (!$db->Connect($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname)) {
                    $conv->log($ls . 'Failed database connection, reconnecting!' . $le);
                }

                $db->query($sql);
            }
            if ($db->affected_rows()) {
                $conv->log($ls . 'Database data updated!' . $le);
                if ($cfg["conversion_source_video"] == 0 and (file_exists($dst_folder . $video_id . '.360p.mp4') or file_exists($dst_folder . md5($cfg["global_salt_key"] . $video_id) . '.360p.mp4'))) {
                    $conv->log($ls . 'Deleting source video ' . $src . $le);
                    VFileinfo::doDelete($src);
                }
                if ($cfg["conversion_video_que"] == 1) {
                    $que = sprintf("UPDATE `db_videoque` SET `state`='2', `end_time`='%s' WHERE `file_key`='%s' AND `usr_key`='%s' AND `state`='1' LIMIT 1;", date("Y-m-d H:i:s"), $video_id, $user_key);
                    $db->execute($que);
                    if ($cfg["file_approval"] == 0) {
                        $act = sprintf("UPDATE `db_videofiles` SET `approved`='1' WHERE `file_key`='%s' LIMIT 1;", $video_id);
                        $db->execute($act);
                    }
                }
                /* admin and subscribers notification */
                $db_approved = ($cfg["file_approval"] == 1 ? 0 : 1);
                $type        = 'video';
                $usr_id      = $class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $video_id);

                $notify          = $db_approved == 1 ? VUpload::notifySubscribers($usr_id, $type, $video_id, '', $user_key) : VUpload::notifySubscribers(0, $type, $video_id, '', $user_key);
                $conv->log_clean = true;
            } else {
                $conv->log($ls . 'Failed to execute video update query!' . $le);
            }
        } else {
            $conv->log($ls . 'Failed to load video: ' . $src . $le);
        }
    }
}

function gs($k)
{
    global $cfg;
    return md5($cfg["global_salt_key"] . $k);
}
