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

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.upload');
include_once $class_language->setLanguageFile('frontend', 'language.account');

$upload_type   = $_GET["t"] != 'document' ? $class_filter->clr_str($_GET["t"]) : 'doc';
$upload_short  = $upload_type == 'short';
$upload_module = $class_filter->clr_str($_GET["t"]);

$post_name = 'file';
$user_id   = intval($_POST["UFUID"]);
$user_key  = $class_filter->clr_str($_POST["UFSUID"]);

if (isset($_GET["t"])) {
    if ($upload_type != 'video' and $upload_type != 'short' and $upload_type != 'audio' and $upload_type != 'image' and $upload_type != 'doc') {
        header("Location: " . VHref::getKey("upload") . '?t=video');
        exit;
    }
}

$cfg = $class_database->getConfigurations('file_approval,conversion_' . $upload_module . '_que,' . $upload_module . '_limit');

$upload_file_size  = intval($_POST["UFSIZE"]);
$upload_file_limit = $cfg[$upload_module . "_limit"] * 1024 * 1024;

switch ($_GET["do"]) {
    case "reload-stats":
        if ($cfg["paid_memberships"] == 1) {
            echo $ht = VUseraccount::subscriptionStats(1);
            echo $ht = VUpload::subscriptionCheck($upload_type, 1);
        }
        break;
}

if (($upload_file_size > $upload_file_limit) or ($cfg["paid_memberships"] == 1 and VUpload::subscriptionLimit($upload_type))) {
    exit();
}

if (isset($_GET["r"])) {
    $responses = new VResponses();
}

if (!isset($_GET["do"])) {
    $db_id         = VUpload::dbUpdate($upload_type, '', $user_id, $user_key, $upload_short);
    $do_conversion = ($db_id != '' and $cfg["conversion_" . $upload_module . "_que"] == 0) ? VUpload::initConversion($db_id, $upload_type, '', $upload_short) : null;
}
