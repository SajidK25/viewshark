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

$log = $path . '/.clear_vods.log';
error_log(sprintf("[%s] clear_vods cron task...\n\n", date("Y-m-d H:i:s")), 3, $log);

$cmd = 'ls ' . $path . '/*out.mp4';

exec($cmd, $out);

if ($out[0]) {
    foreach ($out as $file) {
        if (file_exists($file)) {
            $base = str_replace(array($path . '/', '.mp4'), array('', ''), $file);
            $sql  = sprintf("SELECT `db_id` FROM `db_livefiles` WHERE `file_name`='%s' LIMIT 1;", $base);
            $rs   = $db->execute($sql);

            if (!$rs->fields["db_id"]) {
                $a       = explode("-", $file);
                $l       = str_replace('out.mp4', 'p.mp4', $a[1]);
                $preview = $a[0] . $l;

                //echo sprintf("can delete %s\n", $file);
                //echo sprintf("can delete %s\n", $preview);

                if (unlink($file)) {
                    error_log(sprintf("[%s] unlinked %s\n", date("Y-m-d H:i:s"), $file), 3, $log);
                } else {
                    error_log(sprintf("[%s] failed unlink %s\n", date("Y-m-d H:i:s"), $file), 3, $log);
                }

                if (file_exists($preview)) {
                    if (unlink($preview)) {
                        error_log(sprintf("[%s] unlinked %s\n", date("Y-m-d H:i:s"), $preview), 3, $log);
                    } else {
                        error_log(sprintf("[%s] failed unlink %s\n", date("Y-m-d H:i:s"), $preview), 3, $log);
                    }

                }
            }
        }
    }
}
