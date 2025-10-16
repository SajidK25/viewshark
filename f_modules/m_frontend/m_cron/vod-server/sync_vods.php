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

$url = $base . '/syncvods?s=';

$date = date("Y-m-d");
$tk   = md5($date . $ssk);
$url .= $tk;

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
$data = curl_exec($curl);
curl_close($curl);

$list = json_decode($data);

$log = $path . '/.sync_vods.log';
error_log(sprintf("[%s] sync_vods cron task...\n\n", date("Y-m-d H:i:s")), 3, $log);

if ($list[0]) {
    foreach ($list as $filename) {
        $vod = $path . '/' . $filename . '.mp4';
        if (file_exists($vod)) {
            if (unlink($vod)) {
                error_log(sprintf("[%s] unlinked %s\n", date("Y-m-d H:i:s"), $vod), 3, $log);
            } else {
                error_log(sprintf("[%s] failed unlink %s\n", date("Y-m-d H:i:s"), $vod), 3, $log);
            }
        }

        $flv = $path . '/' . $filename . '.flv';
        if (file_exists($flv)) {
            if (unlink($flv)) {
                error_log(sprintf("[%s] unlinked %s\n", date("Y-m-d H:i:s"), $flv), 3, $log);
            } else {
                error_log(sprintf("[%s] failed unlink %s\n", date("Y-m-d H:i:s"), $flv), 3, $log);
            }
        }

        $ff  = explode("-", $filename);
        $_ff = str_replace('out', 'p', $ff[1]);

        $pv = $path . '/' . $ff[0] . $_ff . '.mp4';
        if (file_exists($pv)) {
            if (unlink($pv)) {
                error_log(sprintf("[%s] unlinked %s\n", date("Y-m-d H:i:s"), $pv), 3, $log);
            } else {
                error_log(sprintf("[%s] failed unlink %s\n", date("Y-m-d H:i:s"), $pv), 3, $log);
            }
        }

    }
}
