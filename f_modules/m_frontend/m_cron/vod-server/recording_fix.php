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
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

require 'cfg.php';

$main_dir = realpath(dirname(__FILE__) . '/../../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

$commands = array();
$found    = 0;
exec("ps ax", $commands);
if (count($commands) > 0) {
    foreach ($commands as $command) {
        if (strpos($command, 'ffmpeg') === false) {
        } else {
            $found = 1;
        }
    }
}

if ($found) {
    exit;
}
$ffmpeg = '/usr/bin/ffmpeg';
$log    = $path . '/.recording_fix.log';
$cmd    = 'ls ' . $path . '/*out.mp4';

error_log(sprintf("[%s] recording_fix cron task...\n\n", date("Y-m-d H:i:s")), 3, $log);

exec($cmd, $out);

if ($out[0]) {
    foreach ($out as $file) {
        if (!is_file($file) or (is_file($file) and filesize($file) == 0)) {
            $flv = str_replace(".mp4", ".flv", $file);

            if (file_exists($flv) and filesize($flv) > 0) {
                $cmd_ffmpeg = sprintf("%s -y -i %s -codec copy -movflags +faststart %s", $ffmpeg, $flv, $file);
                error_log(sprintf("[%s] %s\n", date("Y-m-d H:i:s"), $cmd_ffmpeg), 3, $log);

                exec($cmd_ffmpeg . ' 2>&1', $out_ffmpeg);
                $result = implode("\n", $out_ffmpeg);

                VFileinfo::write($log, $result . "\n\n\n", true);
            }
        }
    }
}
