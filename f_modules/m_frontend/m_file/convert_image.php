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
include_once 'f_core/f_classes/class_thumb/ThumbLib.inc.php';
include_once 'f_core/f_classes/class.conversion.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');

$rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%conversion_%' OR `cfg_name` LIKE '%server_%' OR `cfg_name` LIKE '%thumb%' OR `cfg_name`='file_approval' OR `cfg_name`='user_subscriptions' OR `cfg_name`='conversion_image_que' OR `cfg_name`='conversion_source_image' OR `cfg_name`='conversion_image_previews';");
while (!$rs->EOF) {
    $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
    @$rs->MoveNext();
}

if (isset($_SERVER['argv'][1]) and isset($_SERVER['argv'][2])) {
    $image_id       = $class_filter->clr_str($_SERVER['argv'][1]);
    $user_key       = $class_filter->clr_str($_SERVER['argv'][2]);
    $file_name      = html_entity_decode($class_database->singleFieldValue('db_imagefiles', 'file_name', 'file_key', $image_id), ENT_QUOTES, 'UTF-8');
    $src_folder     = $cfg["upload_files_dir"] . '/' . $user_key . '/i/';
    $dst_folder     = $cfg["media_files_dir"] . '/' . $user_key . '/i/';
    $dst_folder_tmb = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $image_id . '/';
    $src            = $src_folder . $file_name;
    $tmp_dir        = $cfg["media_files_dir"] . '/' . $user_key . '/t/' . $image_id . '/out/';

    if (!is_dir($dst_folder_tmb)) {
        @mkdir($dst_folder_tmb);
    }
    if (!is_dir($tmp_dir)) {
        @mkdir($tmp_dir);
    }

    if (file_exists($src) && is_file($src)) {
        $th_large = false;
        $th_prev  = false;
        $conv     = new VImage();
        $conv->log_setup($image_id, ($cfg["log_image_conversion"] == 1 ? true : false));

        list($width, $height) = getimagesize($src);

        if ($cfg["conversion_image_type"] == 3) {
            $width  = $width >= $cfg["conversion_image_from_w"] ? $cfg["conversion_image_to_w"] : $width;
            $height = $height >= $cfg["conversion_image_from_h"] ? $cfg["conversion_image_to_h"] : $height;
        }

        if ($cfg["conversion_image_previews"] == 1) {
            $th_prev = true;
            if ($conv->createThumbs_ffmpeg($src, $dst_folder, $image_id, 640, 480, $image_id, $user_key)) {
            }
        }

        if ($conv->createThumbs($src, $dst_folder, gs($image_id), $width, $height)) {
            $th_large = true;
        }
        if ($conv->createThumbs_ffmpeg($src, $dst_folder_tmb, 1, 320, 240, $image_id, $user_key)) {
            $th_small = true;
        }
        if ($conv->createThumbs_ffmpeg($src, $dst_folder_tmb, 0, 640, 480, $image_id, $user_key)) {
            $th_mid = true;
        }
        if ($th_large === true) {
            $ani = $conv->is_ani($src) ? 1 : 0;
            $db->query(sprintf("UPDATE `db_imagefiles` SET %s `file_mobile`='1', `file_hd`='%s' WHERE `file_key`='%s' LIMIT 1;", ($th_prev === true ? "`has_preview`='1'," : null), $ani, $image_id));
            if ($cfg["conversion_image_que"] == 1) {
                $db->query(sprintf("UPDATE `db_imageque` SET `state`='2', `end_time`='%s' WHERE `file_key`='%s' AND `usr_key`='%s' AND `state`='1' LIMIT 1;", date("Y-m-d H:i:s"), $image_id, $user_key));
                if ($cfg["file_approval"] == 0) {
                    $db->query(sprintf("UPDATE `db_imagefiles` SET `approved`='1' WHERE `file_key`='%s' LIMIT 1;", $image_id));
                }
            }
            /* admin and subscribers notification */
            $db_approved = ($cfg["file_approval"] == 1 ? 0 : 1);
            $type        = 'image';
            $usr_id      = $class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $image_id);

            $notify = $db_approved == 1 ? VUpload::notifySubscribers($usr_id, $type, $image_id, '', $user_key) : VUpload::notifySubscribers(0, $type, $image_id, '', $user_key);
        }
        if ($cfg['conversion_source_image'] == 0) {
            VFileinfo::doDelete($src);
        }
    } else {
        exit;
    }
}
function gs($k)
{global $cfg;return md5($cfg["global_salt_key"] . $k);}
