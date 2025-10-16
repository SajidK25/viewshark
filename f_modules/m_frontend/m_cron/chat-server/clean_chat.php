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

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die('Could not connect: ' . mysqli_error());
}
echo "Connected successfully\n";

$sql = sprintf("DELETE FROM `db_livenotifications` WHERE `displayed`='1';");

if (mysqli_query($conn, $sql)) {
    echo "db_livenotifications updated successfully\n";
} else {
    echo "Error updating table db_livenotifications: " . mysqli_error($conn) . "\n";
}

$sql = "DELETE FROM `db_livechat` WHERE `chat_user` LIKE 'Guest%';";

if (mysqli_query($conn, $sql)) {
    echo "db_livechat records updated successfully\n";
} else {
    echo "Error updating db_livechat: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
