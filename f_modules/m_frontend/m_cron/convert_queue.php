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

$cfg[] = $class_database->getConfigurations('conversion_video_que,conversion_short_que,conversion_image_que,conversion_audio_que,conversion_document_que');
$conv  = new VQue();

if ($cfg["video_module"] == 1 and $cfg["conversion_video_que"] == 1 and $conv->load("video")) {
    if ($conv->check()) {
        $conv->startConversion();
        echo "\n";
    }
}

if ($cfg["short_module"] == 1 and $cfg["conversion_short_que"] == 1 and $conv->load("short")) {
    if ($conv->check()) {
        $conv->startConversion();
        echo "\n";
    }
}

if ($cfg["image_module"] == 1 and $cfg["conversion_image_que"] == 1 and $conv->load("image")) {
    if ($conv->check()) {
        $conv->startConversion();
        echo "\n";
    }
}

if ($cfg["audio_module"] == 1 and $cfg["conversion_audio_que"] == 1 and $conv->load("audio")) {
    if ($conv->check()) {
        $conv->startConversion();
        echo "\n";
    }
}

if ($cfg["document_module"] == 1 and $cfg["conversion_document_que"] == 1 and $conv->load("doc")) {
    if ($conv->check()) {
        $conv->startConversion();
        echo "\n";
    }
}
