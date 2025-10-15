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

include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('backend', 'language.members.entries');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.import');

$error_message  = null;
$notice_message = null;

$logged_in = VLogin::checkBackend(VHref::getKey("be_members"));
$cfg       = $class_database->getConfigurations('video_player,audio_player,numeric_delimiter,paypal_log_file,public_channels,channel_bulletins,event_map,user_image_allowed_extensions,user_image_max_size,channel_backgrounds,channel_bg_allowed_extensions,channel_bg_max_size,username_format,signup_min_username,signup_max_username,username_format_dott,username_format_underscore,username_format_dash,list_reserved_users,signup_domain_restriction,list_email_domains,affiliate_module,affiliate_payout_share,affiliate_payout_figure,affiliate_payout_currency,affiliate_payout_units,affiliate_geo_maps,sub_shared_revenue,subscription_payout_currency');
switch ($_GET["do"]) {
    case "make-affiliate":
    case "clear-affiliate":
    case "make-partner":
    case "clear-partner":
        echo $html = VbeMembers::affiliateConfirmation();
        break;
    case "usr-make-affiliate":
    case "usr-clear-affiliate":
    case "usr-make-partner":
    case "usr-clear-partner":
        echo $html = VbeMembers::affiliateConfirmationEmail();
        break;
    case "affiliate-payout":
        echo $html = VbeMembers::affiliatePayout();
        break;
    case "usr-save-affiliate":
        echo $html = ($_POST) ? VbeMembers::affiliatePayoutSave() : null;
        break;
    case "savecustomtext":
    case "savecustominput":
    case "savecustomlink":
    case "savecustomselect":
        $do_section     = 1;
        $notice_message = ($_POST) ? VbeMembers::saveCustomFields($_GET["do"]) : null;
        break;
    case "savecustomtextedit":
    case "savecustominputedit":
    case "savecustomlinkedit":
    case "savecustomselectedit":
        $do_section     = 1;
        $notice_message = ($_POST) ? VbeMembers::saveEditedFields($_GET["do"], 0) : null;
        break;
    case "removecustomfield":
        $do_section     = 1;
        $notice_message = ($_POST) ? VbeMembers::saveEditedFields($_GET["do"], 1) : null;
        break;
    case "send-email":
    case "change-email":
    case "user-perm":
    case "folder-info":
    case "sub-info":
    case "paid-sub":
    case "del-account":
    case "user-activity":
        $do_section = 1;
        $fn         = str_replace('-', '', $_GET["do"]);
        $fname      = 'userAction_' . $fn;
        echo $html  = VbeMembers::$fname();
        break;
    case "sub-change":
        echo $html = VbeMembers::pkDate(intval($_GET["pk"]));
        break;
    case "sub-update":
    case "paid-sub-update":
    case "sub-reset":
    case "usr-update":
    case "passw-update":
    case "usr-email":
    case "perm-update":
    case "act-update":
    case "usr-delete":
        echo $html = VbeMembers::subUpdate($_GET["do"]);
        break;
    case "autocomplete":
        $html       = VGenerate::processAutoComplete('accounts');
        $do_section = 1;
        break;
    default:
        $do_section     = 0;
        $notice_message = ($_POST and $_GET["do"] == '') ? VbeSettings::doSettings() : null;
}
$cfg        = $class_database->getConfigurations('keep_entries_open,paypal_log_file');
$menu_entry = ($_GET["s"] != '' and ($_GET["do"] == '' or $do_section == 0)) ? VMenuparse::sectionDisplay('backend', $class_filter->clr_str($_GET["s"])) : null;
$page       = ($_GET["s"] == '' and $_GET["do"] == '') ? $class_smarty->displayPage('backend', 'backend_tpl_members', $error_message, $notice_message) : null;
