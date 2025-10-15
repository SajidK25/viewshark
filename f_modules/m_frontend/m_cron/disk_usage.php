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

$main_dir = realpath(dirname(__FILE__) . '/../../../');

set_time_limit(0);
set_include_path($main_dir);

include_once 'f_core/config.core.php';
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$cfg[] = $class_database->getConfigurations('backend_email');

$bytes     = disk_free_space("/");
$si_prefix = array('B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB');
$base      = 1024;
$class     = min((int) log($bytes, $base), count($si_prefix) - 1);

$data = sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class];
$free = sprintf('%1.2f', $bytes / pow($base, $class));

if ($si_prefix[$class] == 'GB') {
    if ($free < 5) {
        VNotify::queInit('disk_usage', array($cfg['backend_email']), $data);
    }
}
if ($si_prefix[$class] == 'MB') {
    if ($free < 900) {
        VNotify::queInit('disk_usage', array($cfg['backend_email']), $data);
    }
}
