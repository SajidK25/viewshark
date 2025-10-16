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
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

require 'cfg.php';

$cmd = 'ls ' . $path . '/*out.mp4';

exec($cmd, $out);

if ($out[0]) {
    foreach ($out as $file) {
        if (file_exists($file)) {
            $a       = explode("-", $file);
            $l       = str_replace('out.mp4', 'p.mp4', $a[1]);
            $preview = $a[0] . $l;

            if (!file_exists($preview)) {
                $cmd = 'ffmpeg -y -t 30 -i ' . $file . ' -codec copy -movflags +faststart ' . $preview;

                exec(escapeshellcmd($cmd) . ' >/dev/null &');
            }
        }
    }
}
