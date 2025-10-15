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

include_once 'f_core/config.core.php';
include_once 'f_core/f_classes/class.conversion.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');

$host = array('127.0.0.1');

if ($cfg["live_module"] == 0 or $cfg["live_uploads"] == 0 or !in_array($_SERVER[REM_ADDR], $host)) {
    error("error[publish][host_not_in_array]");
}

$error  = false;
$check  = false;
$notify = true;
$type   = 'live';
$app    = $class_filter->clr_str($_GET["app"]);
$name   = $class_filter->clr_str($_GET["name"]);
$name   = str_replace(array('_360p', '_480p', '_720p', '_src', '_hls'), array('', '', '', '', ''), $name);
$apps   = array('cast', 'live', 'play', 'sbr', 'mbr', 'srv1', 'srv2', 'srv1-local-off', 'srv2-local', 'vods', 'vods1-local', 'vods2-local', 'elive', 'esrv1', 'evods');

if ($app == 'srv1' or $app == 'srv1-local' or $app == 'esrv1' or $app == 'evods' or $app == 'vods' or $app == 'vods1-local') {exit;}

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

    if (!$error) {
        if (!in_array($app, $apps)) {
            $error = 'app_not_in_array';
        }
    }
    if ($error) {
        error("error[auth][$error]");
    }
}

if ($name != '') {
    $q = sprintf("SELECT A.`db_id`, A.`usr_id`, A.`stream_vod`, A.`file_key`, A.`stream_live` FROM `db_%sfiles` A WHERE
    A.`usr_id`='%s' AND
    A.`stream_key`='%s' AND A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1'
    ORDER BY A.`db_id` DESC
    LIMIT 1;",
        $type, $usr_id, $name);

    $r = $db->execute($q);

    if ($db_id = $r->fields["db_id"] and $r->fields["stream_live"] == 1 and ($app == 'live' or $app == 'elive')) {
        $db->execute(sprintf("UPDATE `db_%sfiles` SET `stream_ended`='0', `stream_end`='00:00:00 00:00:00', `stream_key_active`='1', `stream_key_old`='', `file_duration`='1' WHERE `db_id`='%s' LIMIT 1;", $type, $db_id));
        $notify = false;
    } elseif (!$r->fields["db_id"]) {
        error("error[auth][db_id1]");
    }
}

if ($name != '') {
    $q = sprintf("SELECT A.`db_id`, A.`usr_id`, A.`stream_vod`, A.`file_key`, A.`stream_live`, A.`stream_end`, A.`mail_sent` FROM `db_%sfiles` A WHERE
    A.`usr_id`='%s' AND
    A.`stream_key`='%s' AND A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1'
    ORDER BY A.`db_id` DESC
    LIMIT 1;", $type, $usr_id, $name);

    $r = $db->execute($q);

    if ($db_id = $r->fields["db_id"]) {
        $svod = $r->fields["stream_vod"];
        $send = $r->fields["stream_end"];
        $mail = $r->fields["mail_sent"];

        $p = unserialize($class_database->singleFieldValue('db_accountuser', 'usr_perm', 'usr_id', (int) $usr_id));

        if ($check and $p["perm_upload_l"] == 1) {
            if ($send != '0000-00-00 00:00:00' and strtotime($send) > 0 and (strtotime(date("Y-m-d H:i:s")) - strtotime($send)) > 600) {
                error("error[auth][strtotime]");
            } elseif ($send != '0000-00-00 00:00:00' and strtotime($send) > 0 and (strtotime(date("Y-m-d H:i:s")) - strtotime($send)) < 900) {
                $notify = false;
            }
            if (($app == 'evods' or $app == 'vods' or $app == 'vods1-local' or $app == 'vods2-local') and ($svod == 0 or $p["perm_live_vod"] == 0 or $cfg["live_vod"] == 0)) {
                //error("error[auth][app_vods]");

                header("HTTP/1.0 404 Not Found");exit;
            }

            $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_live`='1' WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
            $db->execute(sprintf("UPDATE `db_%sfiles` SET
            `mail_sent`='1', `stream_live`='1', `stream_start`='%s', `stream_ended`='0', `stream_end`='', `stream_key_active`='1', `stream_key_old`='', `file_duration`='1' WHERE
            `db_id`='%s' AND `usr_id`='%s' LIMIT 1;", $type, date("Y-m-d H:i:s"), $db_id, $usr_id));

            if (!$mail and $db->Affected_Rows() > 0) {
                VUpload::notifySubscribers($usr_id, $type, $r->fields["file_key"], $r->fields["usr_key"]);
            }
        } else {
            error("error[auth][perm_upload_l]");
        }
    } else {
        error("error[auth][db_id2]");
    }
}

function error($type)
{
    error_log(date("Y-m-d H:i:s") . ": $type\n", 3, LIVE_AUTH_LOG);
    error_log(date("Y-m-d H:i:s") . ": Req: " . VServer::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n\n", 3, LIVE_AUTH_LOG);

    header("HTTP/1.0 404 Not Found");
    exit;
}
