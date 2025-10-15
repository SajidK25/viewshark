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
include_once 'f_core/f_classes/class.be.servers.php';
include_once 'f_core/f_classes/class_ftp/ftp_class.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');

if (isset($_SERVER['argv'][1]) and isset($_SERVER['argv'][2]) and isset($_SERVER['argv'][3])) {
    $file_type   = $class_filter->clr_str($_SERVER['argv'][1]);
    $file_id     = $class_filter->clr_str($_SERVER['argv'][2]);
    $user_key    = $class_filter->clr_str($_SERVER['argv'][3]);
    $server_id   = intval($_SERVER['argv'][4]);
    $do_tmb      = intval($_SERVER['argv'][5]);
    $data        = $db->execute(sprintf("SELECT `file_title`, `thumb_cache` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $file_type, $file_id));
    $file_title  = $data->fields["file_title"];
    $thumb_cache = $data->fields["thumb_cache"];
    $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;

    switch ($file_type[0]) {
        case "v":
            $_ext = '.360p.mp4';
            break;
        case "i":
            $_ext = '.jpg';
            break;
        case "a":
            $_ext = '.mp3';
            break;
        case "d":
            $_ext = '.pdf';
            break;
    }

    $file_name  = $do_tmb == 0 ? md5($cfg["global_salt_key"] . $file_id) . $_ext : $file_id . '/0' . $thumb_cache . '.jpg';
    $src_folder = $cfg["media_files_dir"] . '/' . $user_key . '/' . ($do_tmb == 0 ? $file_type[0] : 't') . '/';
    $src        = $src_folder . $file_name;

    if (file_exists($src) && is_file($src)) {
        $xfer = new fileTransfer();

        if ($xfer->load($src, $file_type)) {
            $xfer->log_setup($file_id, true);
            $xfer->log("Preparing " . ($do_tmb == 1 ? 'thumb' : $file_type) . ": " . $file_title . "\n\n" . $xfer->get_data_string() . "\n");

            $xfer->doTransfer($file_id, $user_key, $src, $server_id, $do_tmb);
        } else {
            $xfer->log('Failed to load file: ' . $src);
        }
    }
}
