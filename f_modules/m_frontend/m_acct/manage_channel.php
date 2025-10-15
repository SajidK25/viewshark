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
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.manage.channel');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('paid_memberships,backend_email,backend_username,channel_backgrounds,channel_comments,file_counts,numeric_delimiter,channel_views,channel_bg_max_size,channel_bg_allowed_extensions,user_subscriptions,user_friends,user_blocking,internal_messaging,approve_friends');
$logged_in        = VLogin::checkFrontend(VHref::getKey('manage_channel'));
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

$channel = new VChannel;

switch ($_GET["s"]) {
    case "channel-menu-entry1": //general setup
        echo ($_POST ? VChannel::postChanges('ch_setup') : VChannel::manage_general());
        break;
    case "channel-menu-entry2": //channel modules
        echo ($_POST ? VChannel::postChanges('ch_modules') : VChannel::manage_modules());
        break;
    case "channel-menu-entry3": //channel art
        if (isset($_GET["do"])) {
            switch ($_GET["do"]) {
                case "edit-crop":$method = 'edit_crop';
                    break;
                case "edit-gcrop":$method = 'edit_crop';
                    break;
                case "delete-crop":$method = 'html_delete_crop';
                    break;
            }
        } else {
            $method = 'manage_art';
        }
        echo ($_POST ? VChannel::postChanges('ch_art') : VChannel::$method());
        break;
    case "channel-menu-entry4":
        break;
}
if (!isset($_GET["s"]) and !isset($_GET["do"])) {
    $smarty->assign('c_section', VHref::getKey("manage_channel"));
}

echo $display_page = (!isset($_GET["s"])) ? $class_smarty->displayPage('frontend', 'tpl_manage_channel', $error_message, $notice_message) : null;
