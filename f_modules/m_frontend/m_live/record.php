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
set_include_path($main_dir);

sleep(2);

include_once 'f_core/config.core.php';

$host = array('127.0.0.1');

if ($cfg["live_module"] == 0 or $cfg["live_uploads"] == 0 or !in_array($_SERVER[REM_ADDR], $host)) {
    error("error[record][host_not_in_array]");
}

$type   = 'live';
$app    = $class_filter->clr_str($_GET["app"]);
$name   = $class_filter->clr_str($_GET["name"]);
$url    = $class_filter->clr_str($_GET["tcurl"]);
$_srv   = parse_url($url, PHP_URL_HOST);
$_rs    = $db->execute(sprintf("SELECT `srv_id` FROM `db_%sservers` WHERE `srv_type`='vod' AND `srv_slug`='%s' AND `srv_active`='1' LIMIT 1;", $type, $app));
$srv_id = (int) $_rs->fields["srv_id"];
$fpath  = explode("/", $class_filter->clr_str($_GET["path"]));
$fstill = explode(".", $fpath[3]);
$fname  = $fstill[0];

$name = str_replace(array('_360p', '_480p', '_720p', '_src', '_hls'), array('', '', '', '', ''), $name);
$apps = array('cast', 'live', 'play', 'sbr', 'mbr', 'vods', 'vods1-local', 'vods2-local', 'elive', 'evods');

if ($srv_id == 0) {
    error("error[record][srv_id]");
}
if (!in_array($app, $apps)) {
    error("error[record][app_not_in_array]");
}

if ($name != '' and $fname != '') {
    $q = sprintf("SELECT A.`db_id`, A.`usr_id`, A.`stream_vod`, A.`file_key`, A.`stream_live`, B.`usr_key` FROM
        `db_%sfiles` A, `db_accountuser` B WHERE
        A.`usr_id`=B.`usr_id` AND A.`stream_key`='%s' AND A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1'
        ORDER BY A.`db_id` DESC
        LIMIT 1;", $type, $name);

    $r = $db->execute($q);

    if ($db_id = $r->fields["db_id"]) {
        $usr_id  = $r->fields["usr_id"];
        $usr_key = $r->fields["usr_key"];
        $svod    = $r->fields["stream_vod"];

        $p    = unserialize($class_database->singleFieldValue('db_accountuser', 'usr_perm', 'usr_id', (int) $usr_id));
        $pcfg = $class_database->getConfigurations('conversion_live_previews');

        if ($p["perm_upload_l"] == 1) {
            if (($app == 'vods' or $app == 'evods') and ($svod == 0 or $p["perm_live_vod"] == 0 or $cfg["live_vod"] == 0)) {
                header("HTTP/1.0 404 Not Found");exit;
            }

            $q = sprintf("UPDATE `db_livefiles` SET `vod_server`='%s', `stream_key_old`='%s', `file_name`='%s', `has_preview`='%s',
                        `stream_key_active`='0', `stream_live`='0', `stream_end`='%s', `stream_ended`='1' WHERE
                        `db_id`='%s' AND `usr_id`='%s' LIMIT 1;",
                $srv_id, $name, $fname, $pcfg["conversion_live_previews"], date("Y-m-d H:i:s"), $db_id, $usr_id);

            $db->execute($q);
        } else {
            error("error[record][perm_upload_l]");
        }
    } else {
        error("error[record][db_id]");
    }
}

function error($type)
{
    error_log(date("Y-m-d H:i:s") . ": $type\n", 3, LIVE_REC_LOG);
    error_log(date("Y-m-d H:i:s") . ": Req: " . VServer::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n\n", 3, LIVE_REC_LOG);

    header("HTTP/1.0 404 Not Found");
    exit;
}
