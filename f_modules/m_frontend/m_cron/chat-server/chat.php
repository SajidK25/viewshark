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
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

$main_dir = realpath(dirname(__FILE__) . '/../../../../');
set_include_path($main_dir);

include_once 'filter.php';

$class_filter = new VFilter;

$host = array('127.0.0.1');

$_POST = $HTTP_RAW_POST_DATA = file_get_contents('php://input');
//$_POST = $HTTP_RAW_POST_DATA;
$post = json_decode($_POST);

if ($_POST and in_array($_SERVER["REMOTE_ADDR"], $host)) {
    require 'cfg.php';

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    if (!$conn) {
        die('Could not connect: ' . mysqli_error());
    }
    echo "Connected successfully\n";

    $salt     = $cfg["live_chat_salt"];
    $p_chatid = $class_filter->clr_str($post->a);
    $p_fkey   = $class_filter->clr_str($post->b);
    $p_nick   = $class_filter->clr_str($post->c);
    $p_dnick  = $class_filter->clr_str($post->cd);
    $p_ip     = $class_filter->clr_str($post->d);
    $p_chid   = $class_filter->clr_str($post->e);
    $p_uid    = $class_filter->clr_str($post->f);
    $p_cua    = $class_filter->clr_str($post->g);
    $p_own    = $class_filter->clr_str($post->h);
    $p_ukey   = $class_filter->clr_str($post->i);
    $p_badge  = $class_filter->clr_str($post->j);
    $p_live   = (int) $post->k;
    $p_first  = (int) $post->first;
    $p_inc    = (int) $post->inc;
    $cip      = $p_ip;
    $p_fp     = $p_cua;

    $q  = sprintf("SELECT `db_id` FROM `db_livechat` WHERE `channel_id`='%s' AND `chat_id`='%s' AND `stream_id`='%s' LIMIT 1;", $p_chid, $p_chatid, $p_fkey);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    $v  = $r->fetch_assoc();

    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livechat` (`first`, `chat_id`, `channel_id`, `channel_owner`, `usr_id`, `usr_key`, `stream_id`, `chat_user`, `chat_display`, `is_live`, `chat_ip`, `chat_fp`, `chat_time`, `badge`, `logged_in`, `usr_profileinc`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
            $p_first, $p_chatid, $p_chid, $p_own, $p_uid, $p_ukey, $p_fkey, $p_nick, $p_dnick, $p_live, $p_ip, $p_fp, date("Y-m-d H:i:s"), $p_badge, (substr($p_nick, 0, 5) === "Guest" ? 0 : 1), $p_inc);
        $r = mysqli_query($conn, $q);
    } else {
        $q = sprintf("UPDATE `db_livechat` SET `is_live`='%s', `chat_display`='%s', `usr_profileinc`='%s', `first`='%s', `chat_ip`='%s', `chat_fp`='%s' WHERE `db_id`='%s' LIMIT 1;", $p_live, $p_dnick, $p_inc, $p_first, $p_ip, $p_fp, $v["db_id"]);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_livemods` WHERE `channel_id`='%s' LIMIT 1;", $p_chid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livemods` (`channel_id`, `mod_list`) VALUES ('%s', '[]');", $p_chid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_livevips` WHERE `channel_id`='%s' LIMIT 1;", $p_chid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livevips` (`channel_id`, `vip_list`) VALUES ('%s', '[]');", $p_chid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_livebans` WHERE `channel_id`='%s' LIMIT 1;", $p_chid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livebans` (`channel_id`, `ban_list`) VALUES ('%s', '[]');", $p_chid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_livefollows` WHERE `channel_id`='%s' LIMIT 1;", $p_chid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livefollows` (`channel_id`, `follow_list`) VALUES ('%s', '[]');", $p_chid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_livesubs` WHERE `channel_id`='%s' LIMIT 1;", $p_chid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livesubs` (`channel_id`, `sub_list`) VALUES ('%s', '[]');", $p_chid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_livesettings` WHERE `channel_id`='%s' LIMIT 1;", $p_chid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livesettings` (`channel_id`) VALUES ('%s');", $p_chid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `db_id` FROM `db_liveignore` WHERE `usr_id`='%s' LIMIT 1;", $p_uid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_liveignore` (`usr_id`, `ignore_list`) VALUES ('%s', '[]');", $p_uid);
        $r = mysqli_query($conn, $q);
    }

    $q  = sprintf("SELECT `color_class`, `color_code`, `timestamps`, `modicons` FROM `db_livecolors` WHERE `usr_id`='%s' LIMIT 1", $p_uid);
    $r  = mysqli_query($conn, $q);
    $rn = $r->num_rows;
    if ($rn == 0) {
        $q = sprintf("INSERT INTO `db_livecolors` (`usr_id`, `color_class`, `modicons`, `timestamps`) VALUES ('%s', '%s', '0', '0');", $p_uid, 'c' . rand(1, 15));
        $r = mysqli_query($conn, $q);
    }

    mysqli_close($conn);
}
