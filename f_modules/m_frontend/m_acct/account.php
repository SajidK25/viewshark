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

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

include_once 'f_core/f_classes/class_recaptcha/autoload.php';
include_once 'f_core/f_classes/class_thumb/ThumbLib.inc.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('paid_memberships,backend_email,backend_username,signup_domain_restriction,list_email_domains,signup_min_password,signup_max_password,email_change_captcha,keep_entries_open,user_image_max_size,user_image_allowed_extensions,user_image_width,user_image_height,activity_logging,file_favorites,file_rating,file_comments,channel_comments,file_respnses,approve_friends,file_counts,numeric_delimiter,channel_views,recaptcha_site_key,recaptcha_secret_key,affiliate_module,affiliate_tracking_id,affiliate_view_id,affiliate_maps_api_key,affiliate_token_script,affiliate_payout_figure,affiliate_payout_currency,affiliate_payout_units,affiliate_payout_share,affiliate_requirements_type,affiliate_requirements_min');
$logged_in        = VLogin::checkFrontend(VHref::getKey('account'));
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;
$notice_message   = ($_POST and $_GET["do"] == '') ? VUseraccount::doChanges() : null;
$user_key         = $class_filter->clr_str($_SESSION["USER_KEY"]);
$files            = new VFiles;

$smarty->assign('page_display', 'tpl_account');

switch ($_GET["s"]) {
    case "account-menu-entry1":
    case "account-menu-entry13":
        $tpl_page = 'tpl_overview.tpl';
        switch ($_GET["do"]) {
            case "loading":$smarty->display('tpl_frontend/tpl_acct/tpl_overview_image.tpl');
                break;
            case "cancel":VUseraccount::cancelProfileImage();
                break;
            case "upload":VUseraccount::changeProfileImage($user_key);
                break;
            case "save":VUseraccount::saveProfileImage($user_key);
                break;
            case "make-affiliate":echo VAffiliate::affiliateRequest();
                break;
            case "make-affiliate-email":$html = $_POST ? VAffiliate::affiliateRequestEmail() : null;
                break;
        }
        break;
    case "account-menu-entry2":
        $tpl_page = 'tpl_profile_setup.tpl';
        break;
    case "account-menu-entry3":
        $tpl_page = '';
        break;
    case "account-menu-entry4":
        $tpl_page = 'tpl_email_opts.tpl';
        if ($_POST) {
            switch ($_GET["do"]) {
                case "emchange":VUseraccount::changeEmail();
                    break;
            }
        }
        break;
    case "account-menu-entry5":$tpl_page = 'tpl_activity.tpl';
        break;
    case "account-menu-entry6":
        $tpl_page = 'tpl_manage_acct.tpl';
        if ($_POST) {
            switch ($_GET["do"]) {
                case "cpass":VUseraccount::changePassword();
                    break;
                case "purge":VUseraccount::purgeAccount();
                    break;
            }
        }
        break;
}
if (!isset($_GET["s"]) and !isset($_GET["do"])) {
    VAffiliate::allowRequest();
    $smarty->assign('c_section', VHref::getKey("account"));
}

$section_menus   = (intval($_SESSION["USER_ID"]) > 0) ? $smarty->assign('keep_entries_open', $_SESSION[$_SESSION["USER_KEY"] . '_list']) : null;
$display_section = ($_GET["s"] != '' and !isset($_GET["do"])) ? $smarty->display('tpl_frontend/tpl_acct/' . $tpl_page) : null;
$display_page    = (!isset($_GET["s"]) and !isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_account', $error_message, $notice_message) : null;
