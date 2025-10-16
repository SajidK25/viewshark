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
include_once 'f_core/f_classes/class.be.servers.php';

$queue = new fileQueue();

if ($cfg["video_module"] == 1 and $queue->load("video")) {
    if ($queue->check()) {
        $queue->startTransfer();
        echo "\n";
    }
}

if ($cfg["image_module"] == 1 and $queue->load("image")) {
    if ($queue->check()) {
        $queue->startTransfer();
        echo "\n";
    }
}

if ($cfg["audio_module"] == 1 and $queue->load("audio")) {
    if ($queue->check()) {
        $queue->startTransfer();
        echo "\n";
    }
}

if ($cfg["document_module"] == 1 and $queue->load("doc")) {
    if ($queue->check()) {
        $queue->startTransfer();
        echo "\n";
    }
}
