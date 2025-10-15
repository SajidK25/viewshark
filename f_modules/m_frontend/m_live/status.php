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

include_once 'f_core/config.core.php';

$host = array('127.0.0.1');

if ($cfg["live_module"] == 0 or !in_array($_SERVER[REM_ADDR], $host)) {
    exit;
}


$data = ['ls' => 0];

if (isset($_POST['a']) and isset($_GET['l'])) {
    $get_file_key = $class_filter->clr_str($_GET['l']);
    $post_file_key= secured_decrypt($class_filter->clr_str($_POST['a']));

    if ($post_file_key and $post_file_key == $get_file_key) {
        $rs = $db->execute(sprintf("SELECT `stream_live` FROM `db_livefiles` WHERE `file_key`='%s' LIMIT 1;", $post_file_key));

        if ($rs->fields["stream_live"] == 1) {
            $data = ['ls' => 1];
        }
    }
}

$json = json_encode($data);

echo $json; return $json;
