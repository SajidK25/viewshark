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

set_time_limit(0);

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

$type      = null;
$user_key  = null;
$file_key  = null;
$thumbnail = null;

if ($i = rawurldecode($_GET["_"]) and $d = json_decode(secured_decrypt($i), true)) {
    $type     = $class_filter->clr_str($d[0]);
    $user_key = (int) $d[1];
    $file_key = (int) $d[2];

    if ($type and $user_key and $file_key) {
        $tbl = sprintf("db_%sfiles", $type);
        $get = $db->execute(sprintf("SELECT `db_id`, `thumb_cache`, `thumb_server` FROM `%s` WHERE `file_key`='%s' LIMIT 1;", $tbl, $file_key));

        if ($get->fields["db_id"]) {
            $thumb_server = $get->fields["thumb_server"];
            $thumb_cache  = $get->fields["thumb_cache"];
            $thumb_cache  = $thumb_cache > 1 ? $thumb_cache : null;
            $thumbnail    = VGenerate::thumbSigned($type, $file_key, array($user_key, $thumb_cache), (3600 * 24), 0, 0);

            if ($thumbnail) {
                header("Location: " . $thumbnail);
            }
        }
    }
} else {
    header("Location: " . $cfg["global_images_url"] . '/logo-mail.png');
}
