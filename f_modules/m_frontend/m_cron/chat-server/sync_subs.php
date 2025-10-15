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

require 'cfg.php';

$url  = $base . '/syncsubs?s=';
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

/* follows */
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die('Could not connect: ' . mysqli_error());
}

echo "Connected successfully\n";

if (is_object($list->followers)) {
    foreach ($list->followers as $usr_id => $users) {
        if ($usr_id > 0) {
            $sql = sprintf("SELECT `db_id` FROM `db_livefollows` WHERE `channel_id`='%s' LIMIT 1;", $usr_id);
            $r   = mysqli_query($conn, $sql);
            $n   = $r->num_rows;
            $v   = $r->fetch_assoc();

            if ($n > 0) {
                $sql = sprintf("UPDATE `db_livefollows` SET `follow_list`='%s' WHERE `db_id`='%s' LIMIT 1;", json_encode($users), $v["db_id"]);
            } else {
                $sql = sprintf("INSERT INTO `db_livefollows` (`channel_id`, `follow_list`) VALUES ('%s', '%s');", $usr_id, json_encode($users));
            }

            if (mysqli_query($conn, $sql)) {
                echo "db_livefollows records updated successfully for channel_id $usr_id\n";
            } else {
                echo "Error updating record: " . mysqli_error($conn) . "\n";
            }

        }
    }
}

mysqli_close($conn);

/* subs */
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (!$conn) {
    die('Could not connect: ' . mysqli_error());
}

echo "Connected successfully\n";

if (is_object($list->subscribers)) {
    foreach ($list->subscribers as $usr_id => $users) {
        if ($usr_id > 0) {
            $sql = sprintf("SELECT `db_id` FROM `db_livesubs` WHERE `channel_id`='%s' LIMIT 1;", $usr_id);
            $r   = mysqli_query($conn, $sql);
            $n   = $r->num_rows;
            $v   = $r->fetch_assoc();

            if ($n > 0) {
                $sql = sprintf("UPDATE `db_livesubs` SET `sub_list`='%s' WHERE `db_id`='%s' LIMIT 1;", json_encode($users), $v["db_id"]);
            } else {
                $sql = sprintf("INSERT INTO `db_livesubs` (`channel_id`, `sub_list`) VALUES ('%s', '%s');", $usr_id, json_encode($users));
            }

            if (mysqli_query($conn, $sql)) {
                echo "db_livesubs records updated successfully for channel_id $usr_id\n";
            } else {
                echo "Error updating record: " . mysqli_error($conn) . "\n";
            }

        }
    }
}

mysqli_close($conn);
