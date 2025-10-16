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

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

$host = array('127.0.0.1');

if ($cfg["live_module"] == 0 or $cfg["live_uploads"] == 0 or !in_array($_SERVER[REM_ADDR], $host)) {
    error("error[done][host_not_in_array]");
}

$type = 'live';
$pcfg = $class_database->getConfigurations('live_vod');
$app  = $class_filter->clr_str($_GET["app"]);
$name = $class_filter->clr_str($_GET["name"]);
$name = str_replace(array('_360p', '_480p', '_720p', '_src', '_hls'), array('', '', '', '', ''), $name);
$apps = array('cast', 'live', 'play', 'sbr', 'mbr', 'srv1', 'srv1-local', 'elive', 'esrv1');

if (strlen($_GET["q"]) === 32 and ctype_xdigit($_GET["q"])) {
    $pwd = $class_filter->clr_str(strrev($_GET["q"]));
    $q   = sprintf("SELECT A.`db_id`, A.`usr_id`, A.`stream_vod`, A.`file_key`, A.`stream_live`, B.`usr_key` FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`stream_key`='%s' AND A.`stream_ended`='0' AND A.`stream_key_active`='1' AND A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' LIMIT 1;", $type, $name);
    $r   = $db->execute($q);

    $usr_id  = $r->fields["usr_id"];
    $usr_key = $r->fields["usr_key"];
    $pass    = md5($usr_id . $usr_key . $cfg["global_salt_key"]);
    $check   = $pass == $pwd ? true : false;

    if (!$check) {
        $error = 'md5_check';
    }
    if (!$error) {
        if (!in_array($app, $apps)) {
            $error = 'app_not_in_array_md5';
        }
    }
    if ($error) {
        error("error[auth][$error]");
    }
} else {
    $pwd = $class_filter->clr_str(urlencode($_GET["q"]));
    $dec = explode(':', secured_decrypt(rawurldecode($pwd)));

    if ($app == 'live') {
        if (isset($dec[0]) and isset($dec[1]) and isset($dec[2])) {
            $usr_id   = (int) $dec[0];
            $usr_key  = (int) $dec[1];
            $salt_key = $class_filter->clr_str($dec[2]);

            if ($salt_key === $cfg["global_salt_key"]) {
                $check = true;
            } else {
                $error = 'salt_key_check';
            }
        } else {
            $error = 'secured_decrypt';
        }
    }

    if (!$error) {
        if (!in_array($app, $apps)) {
            $error = 'app_not_in_array';
        }
    }
    if ($error) {
        error("error[done][$error]");
    }
}

if ($name != '' and $app == 'live') {
    $q = sprintf("SELECT A.`db_id`, A.`usr_id`, A.`stream_vod`, A.`file_key`, A.`stream_live`, A.`stream_start`, A.`stream_vod` FROM
    `db_%sfiles` A WHERE
    A.`usr_id`='%s' AND A.`stream_key`='%s' AND A.`stream_key_active`='1' AND A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1'
    ORDER BY A.`db_id` DESC
    LIMIT 1;", $type, $usr_id, $name);

    $r = $db->execute($q);

    if ($db_id = $r->fields["db_id"]) {
        $p = unserialize($class_database->singleFieldValue('db_accountuser', 'usr_perm', 'usr_id', (int) $usr_id));

        if ($check and $p["perm_upload_l"] == 1) {
            $ss = strtotime($r->fields["stream_start"]);
            $se = date("Y-m-d H:i:s");

            $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_live`='0' WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
            $db->execute(sprintf("UPDATE `db_livefiles` SET
            `stream_key_active`='0', `stream_live`='0', `stream_end`='%s', `stream_ended`='1', `file_duration`='%s' WHERE
            `db_id`='%s' AND `usr_id`='%s' LIMIT 1;",
                $se, abs((strtotime($se) - $ss) / 60 * 60), $db_id, $usr_id));

            if ($r->fields["stream_vod"] == 0 or $pcfg["live_vod"] == 0) {
                //or delete db entry (?!)
                $db->execute(sprintf("UPDATE `db_livefiles` SET `active`='0' WHERE `file_key`='%s' LIMIT 1;", $r->fields["file_key"]));
            }
        } else {
            error("error[done][perm_upload_l]");
        }
    } else {
        error("error[done][db_id]");
    }
}

function error($type)
{
    error_log(date("Y-m-d H:i:s") . ": $type\n", 3, LIVE_DONE_LOG);
    error_log(date("Y-m-d H:i:s") . ": Req: " . VServer::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n\n", 3, LIVE_DONE_LOG);

    header("HTTP/1.0 404 Not Found");
    exit;
}
