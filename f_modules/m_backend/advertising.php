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
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('backend', 'language.advertising');
include_once $class_language->setLanguageFile('backend', 'language.import');
include_once $class_language->setLanguageFile('frontend', 'language.account');

$error_message  = null;
$notice_message = null;
$logged_in      = VLogin::checkBackend(VHref::getKey("be_advertising"));

switch ($_GET["do"]) {
    case "file_upload":
        echo $ht = VbeAdvertising::beAdvFileUpload();
        break;
}

$menu_entry = ($_GET["s"] != '' and $_GET["do"] != 'file_upload') ? VMenuparse::sectionDisplay('backend', $class_filter->clr_str($_GET["s"])) : null;
$page       = ($_GET["s"] == '') ? $class_smarty->displayPage('backend', 'backend_tpl_advertising', $error_message, $notice_message) : null;
