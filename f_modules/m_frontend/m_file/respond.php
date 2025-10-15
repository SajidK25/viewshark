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

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.respond');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');

$error_message  = null;
$notice_message = null;

$responses = new VResponses();

$get_type  = $responses::$type;
$get_key   = $responses::$file_key;
$logged_in = VLogin::checkFrontend(VHref::getKey("respond") . '?' . $get_type[0] . '=' . $get_key);
$cfg       = $class_database->getConfigurations('file_responses');

$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

$for = isset($_GET["l"]) ? 'live' : (isset($_GET["v"]) ? 'video' : (isset($_GET["i"]) ? 'image' : (isset($_GET["a"]) ? 'audio' : (isset($_GET["d"]) ? 'doc' : (isset($_GET["b"]) ? 'blog' : null)))));

$smarty->assign('file_type', $for[0]);
$smarty->assign('file_key', $class_filter->clr_str($_GET[$for[0]]));

$files     = new VFiles;
$responses = new VResponses;

switch ($_GET["do"]) {
    case "submit-response":
        $do = VResponses::submitResponse();
        break;
    default:break;
}
if (!isset($_GET["s"]) and !isset($_GET["do"])) {
    $smarty->assign('c_section', VHref::getKey("respond"));
}

$display_page = (!isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_respond', $error_message, $notice_message) : null;
