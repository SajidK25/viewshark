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
define('_ISADMIN', true);

set_time_limit(0);

$main_dir = realpath(dirname(__FILE__) . '/../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';
include_once 'f_core/f_classes/class.conversion.php';
include_once $class_language->setLanguageFile('frontend', 'language.global');

$error_message  = null;
$notice_message = null;

if (isset($_SERVER['argv'][1]) and isset($_SERVER['argv'][2])) {
    $pcfg                 = $class_database->getConfigurations('thumbs_nr,log_video_conversion,thumbs_method,thumbs_width,thumbs_height,server_path_php');
    $cfg["thumbs_method"] = 'rand';
    $type                 = 'video';
    $user_id              = null;
    $is_short             = false;
    $video_id             = $class_filter->clr_str($_SERVER['argv'][1]);
    $user_key             = $class_filter->clr_str($_SERVER['argv'][2]);
    $preview_reset        = (int) $_SERVER['argv'][3];
    $user_id              = $class_database->singleFieldValue('db_videofiles', 'usr_id', 'file_key', $video_id);
    if ($user_id) {
        $type = 'video';
    } else {
        $user_id = $class_database->singleFieldValue('db_shortfiles', 'usr_id', 'file_key', $video_id);

        if ($user_id) {
            $type     = 'short';
            $is_short = true;
        }
    }

    if (!$user_id) {
        exit;
    }

    if ($user_key == '') {
        $user_key = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $user_id);
    }

    $vid             = md5($cfg["global_salt_key"] . $video_id);
    $file_name_360p  = $vid . '.360p.mp4';
    $file_name_480p  = $vid . '.480p.mp4';
    $file_name_720p  = $vid . '.720p.mp4';
    $file_name_short = $vid . '.short.mp4';

    $src_folder = $cfg["media_files_dir"] . '/' . $user_key . '/v/';
    $src_360p   = $src_folder . $file_name_360p;
    $src_480p   = $src_folder . $file_name_480p;
    $src_720p   = $src_folder . $file_name_720p;
    $src_short  = $src_folder . $file_name_short;

    $src = is_file($src_720p) ? $src_720p : (is_file($src_480p) ? $src_480p : (is_file($src_360p) ? $src_360p : false));
    $src = ($is_short and is_file($src_short)) ? $src_short : $src;

    if ($src && is_file($src)) {
        $li = "---------------------------------------------";
        $ls = "\n\n" . $li . "\n";
        $le = "\n" . $li . "\n";

        $conv = new VVideo();
        //$conv->log_setup($video_id, ($pcfg["log_video_conversion"] == 1 ? TRUE : FALSE));
        $conv->log_setup($video_id, false);

        if ($conv->load($src)) {
            if ($preview_reset == 0) {
                $tcache = $class_database->singleFieldValue('db_' . $type . 'files', 'thumb_cache', 'file_key', $video_id);
                $db->execute(sprintf("UPDATE `db_%sfiles` SET `thumb_cache`=`thumb_cache`+1 WHERE `file_key`='%s' LIMIT 1;", $type, $video_id));
                if ($db->Affected_Rows()) {
                    $old_index = $tcache;
                    $old_index = $old_index > 1 ? $old_index : null;

                    $old_file_0 = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/0' . $old_index . '.jpg';
                    $old_file_1 = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/1' . $old_index . '.jpg';
                    $old_file_2 = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/2' . $old_index . '.jpg';
                    $old_file_3 = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/3' . $old_index . '.jpg';

                    if (is_file($old_file_0)) {unlink($old_file_0);}
                    if (is_file($old_file_1)) {unlink($old_file_1);}
                    if (is_file($old_file_2)) {unlink($old_file_2);}
                    if (is_file($old_file_3)) {unlink($old_file_3);}
                }

                $fn = !$is_short ? 'extract_thumbs' : 'extract_thumbs_short';
                $conv->log($ls . 'Extracting large thumbnail (640x360)' . $le);
                $thumbs = $conv->$fn(array($src, 'thumb'), $video_id, $user_key);
                $conv->log($ls . 'Extracting smaller thumbnails (' . $pcfg["thumbs_width"] . 'x' . $pcfg["thumbs_height"] . ')' . $le);
                $thumbs = $conv->$fn($src, $video_id, $user_key);
                $conv->log($ls . 'Extracting preview thumbnails (' . $pcfg["thumbs_width"] . 'x' . $pcfg["thumbs_height"] . ')' . $le);
                $fn     = !$is_short ? 'extract_preview_thumbs' : 'extract_preview_thumbs_short';
                $thumbs = $conv->$fn($src, $video_id, $user_key);
            } elseif ($preview_reset == 2) {
                $conv->log($ls . 'Extracting video preview thumbnails (' . $pcfg["thumbs_width"] . 'x' . $pcfg["thumbs_height"] . ')' . $le);
                $fn     = !$is_short ? 'extract_preview_thumbs' : 'extract_preview_thumbs_short';
                $thumbs = $conv->$fn($src, $video_id, $user_key, 2);
            }
            if (is_file($cfg["media_files_dir"] . "/" . $user_key . "/v/" . md5($video_id . "_preview") . ".mp4")) {
                $db->execute(sprintf("UPDATE `db_videofiles` SET `thumb_preview`='1' WHERE `file_key`='%s' LIMIT 1;", $video_id));
            }
        }
    }
}
