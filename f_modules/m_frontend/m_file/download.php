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

$p         = $class_filter->clr_str($_GET['p']);
$type      = $p[0];
$tbl       = $type == 'v' ? 'video' : ($type == 'i' ? 'image' : ($type == 'a' ? 'audio' : ($type == 'd' ? 'doc' : ($type == 'l' ? 'live' : null))));
$file_key  = strrev(substr($p, -10));
$usr_key   = substr($p, 2, 10);
$_file_src = $class_database->singleFieldValue('db_' . $tbl . 'files', 'file_name', 'file_key', $file_key);

switch ($type) {
    case "v":
        switch ($p[1]) {
            case "1":$ext = '.360p.mp4';
                break;
            case "6":$ext = '.480p.mp4';
                break;
            case "2":$ext = '.720p.mp4';
                break;
            case "5":$ext = '.1080p.mp4';
                break;
            case "3":$ext = '.mob.mp4';
                break;
            case "4":$ext = '';
                break;
        }
        break;
    case "l":
        switch ($p[1]) {
            case "1":$ext = '.jpg';
                break;
            case "4":$ext = '';
                break;
        }
        break;
    case "i":
        switch ($p[1]) {
            case "1":$ext = '.jpg';
                break;
            case "4":$ext = '';
                break;
        }
        break;
    case "a":
        switch ($p[1]) {
            case "1":$ext = '.mp3';
                break;
            case "4":$ext = '';
                break;
        }
        break;
    case "d":
        switch ($p[1]) {
            case "1":$ext = '.pdf';
                break;
            case "5":$ext = '.swf';
                break;
            case "4":$ext = '';
                break;
        }
        break;
}

$cf = $db->execute(sprintf("SELECT
                                                 A.`upload_server`,
                                                 B.`server_type`, B.`cf_enabled`, B.`cf_signed_url`, B.`cf_signed_expire`, B.`cf_key_pair`, B.`cf_key_file`,
                                                 B.`s3_bucketname`, B.`s3_accesskey`, B.`s3_secretkey`, B.`cf_dist_type`
                                                 FROM
                                                 `db_%sfiles` A, `db_servers` B
                                                 WHERE
                                                 A.`file_key`='%s' AND
                                                 A.`upload_server`>'0' AND
                                                 A.`upload_server`=B.`server_id`
                                                 LIMIT 1", $tbl, $file_key));

$server_type       = $cf->fields["server_type"];
$cf_enabled        = $cf->fields["cf_enabled"];
$cf_signed_url     = $cf->fields["cf_signed_url"];
$cf_signed_expire  = $cf->fields["cf_signed_expire"];
$cf_key_pair       = $cf->fields["cf_key_pair"];
$cf_key_file       = $cf->fields["cf_key_file"];
$s3_bucket         = $cf->fields["s3_bucketname"];
$aws_access_key_id = $cf->fields["s3_accesskey"];
$aws_secret_key    = $cf->fields["s3_secretkey"];
$dist_type         = $cf->fields["cf_dist_type"];

$a_url = VGenerate::fileURL($tbl, $file_key, 'upload') . '/' . $usr_key . '/' . $type . '/' . $file_key . $ext;

if ($server_type == 's3' and $cf_enabled == 1 and $cf_signed_url == 1) {
    $file_path = $usr_key . '/' . $type . '/' . $file_key . $ext;

    if ($server_type == 's3' and $dist_type == 'r') {
        $a_url = VbeServers::getS3SignedURL($aws_access_key_id, $aws_secret_key, $file_path, $s3_bucket, $cf_signed_expire);
    } else {
        $a_url = VbeServers::getSignedURL($a_url, $cf_signed_expire, $cf_key_pair, $cf_key_file);
    }
}

$_file_name = ($p[1] != '4' ? md5($cfg["global_salt_key"] . $file_key) . $ext : $_file_src);
$_file_loc  = ($p[1] != '4' ? $cfg["media_files_dir"] : $cfg["upload_files_dir"]);

$file       = $_file_loc . '/' . $usr_key . '/' . $type . '/' . $_file_name;
$_full_name = $_file_name;
$_file_name = sprintf("%s%s", $tbl, $ext);

if ($type == 'l') {
    $lq         = $db->execute(sprintf("SELECT A.`srv_host`, A.`srv_port`, B.`file_name` FROM `db_liveservers` A, `db_livefiles` B WHERE A.`srv_id`=B.`vod_server` AND B.`file_key`='%s' LIMIT 1", $file_key));
    $h          = $lq->fields["srv_host"];
    $p          = $lq->fields["srv_port"];
    $n          = $lq->fields["file_name"];
    $file       = sprintf("https://%s:%s/vod/%s.mp4", $h, $p, $n);
    $_file_name = $cfg["website_shortname"] . ' livestream';
}

echo '<br><br><a class="dl2" href="' . str_replace($cfg["media_files_dir"], $cfg["media_files_url"], $file) . '" download="' . $_full_name . '" target="_blank">' . $_file_name . '</a>';
exit;
