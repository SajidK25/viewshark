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

$main_dir = realpath(dirname(__FILE__) . '/../../../../');
set_include_path($main_dir);

include_once 'filter.php';

$class_filter = new VFilter;

//include_once $class_language->setLanguageFile('frontend', 'language.global');

$host = array('127.0.0.1');

$_POST = $HTTP_RAW_POST_DATA = file_get_contents('php://input');

$post = json_decode($_POST);

if ($_POST and in_array($_SERVER["REMOTE_ADDR"], $host)) {
    require 'cfg.php';

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    if (!$conn) {
        die('Could not connect: ' . mysqli_error($conn));
    }
    echo "Connected successfully\n";

    $cid   = $class_filter->clr_str($post->a);
    $sid   = $class_filter->clr_str($post->e);
    $type  = is_array($post->b) ? $post->b : $class_filter->clr_str($post->b);
    $user  = $class_filter->clr_str($post->c);
    $user2 = $class_filter->clr_str($post->d);
    $pk    = $class_filter->clr_str($post->g);

    switch ($type) {
        case "follow":
        case "unfollow":
            $text = $type == 'follow' ? $user . ' is now following' : $user . ' has unfollowed';
            $sql  = sprintf("SELECT `db_id`, `chat_user`, `channel_id`, `channel_owner`, `usr_id`, `logged_in` FROM `db_livechat` WHERE `stream_id`='%s' AND `chat_id`='%s' ORDER BY `db_id` DESC LIMIT 1;", $sid, $cid);
            $r    = mysqli_query($conn, $sql);
            $rn   = $r->num_rows;
            $rv   = $r->fetch_assoc();

            if ($rn > 0) {
                $ch_id = $rv["channel_id"];
                $sql   = sprintf("SELECT `db_id` FROM `db_livenotifications` WHERE `type`='follow' AND `channel_id`='%s' AND `text` LIKE '%s' LIMIT 1;", $ch_id, $user . '%');
                $rr    = mysqli_query($conn, $sql);

                if ($rr->num_rows == 0 and $type == 'follow') {
                    $q = sprintf("INSERT INTO `db_livenotifications` (`type`, `channel_id`, `text`, `displayed`) VALUES ('%s', '%s', '%s', '0');", $type, $ch_id, $text);
                    mysqli_query($conn, $q);
                }
            }

            mysqli_close($conn);
            break;

        default:
            if (is_array($type) and ($type[0] == 'subscribe' or $type[0] == 'unsubscribe')) {
                $text = $type[0] == 'subscribe' ? $user2 . ' has subscribed with a ' . $pk . ' subscription' : $user2 . ' has unsubscribed';
                $sql  = sprintf("SELECT `db_id`, `chat_user`, `channel_id`, `channel_owner`, `usr_id`, `logged_in` FROM `db_livechat` WHERE `stream_id`='%s' AND `chat_id`='%s' ORDER BY `db_id` DESC LIMIT 1;", $sid, $cid);
                $r    = mysqli_query($conn, $sql);
                $rn   = $r->num_rows;
                $rv   = $r->fetch_assoc();

                if ($rn > 0) {
                    $ch_id = $rv["channel_id"];

                    if ($type[0] == 'subscribe') {
                        $q = sprintf("INSERT INTO `db_livenotifications` (`type`, `channel_id`, `text`, `displayed`) VALUES ('%s', '%s', '%s', '0');", $type[0], $ch_id, $text);
                        mysqli_query($conn, $q);
                    }
                }
            }
            mysqli_close($conn);
            break;

    }
}

if ($conn) {
    mysqli_close($conn);
}
