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
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('backend', 'language.dashboard');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.subscriber');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message  = null;
$notice_message = null;
$ipn_check      = false;
$cfg[]          = $class_database->getConfigurations('paypal_test,paypal_test_email,affiliate_module,affiliate_tracking_id,affiliate_view_id,affiliate_maps_api_key,affiliate_token_script,affiliate_payout_figure,affiliate_payout_units,affiliate_payout_currency,affiliate_payout_share,sub_shared_revenue,subscription_payout_currency,channel_views,sub_threshold,partner_requirements_min,partner_requirements_type');
$logged_in      = !$ipn_check ? VLogin::checkFrontend(VHref::getKey("subscribers")) : null;
$analytics      = false;

if (isset($_GET["do"])) {
    switch ($_GET["do"]) {
        case "save-subscriber":
            echo $ht = VAffiliate::affiliateProfile(1);
            break;
        case "showstats":
            echo $ht = VSubscriber::userStats();
            break;
        case "make-partner":
        case "clear-partner":
            echo $ht = VAffiliate::affiliateRequest();
            break;
        case "make-partner-email":
        case "clear-partner-email":
            $ht = $_POST ? VAffiliate::affiliateRequestEmail() : null;
            break;
    }
}

if (!isset($_GET["s"]) and !isset($_GET["do"])) {
    VAffiliate::allowRequest('partner');
    $smarty->assign('c_section', VHref::getKey("subscribers"));
}

if (!isset($_GET["do"])) {
    $smarty->assign('file_type', 'sub');

    if (isset($_GET["rg"]) and isset($_SESSION["USER_PARTNER"]) and (int) $_SESSION["USER_PARTNER"] == 1) {
        $smarty->assign('html_payouts', VSubscriber::html_payouts(1));
    } else if (isset($_GET["rp"]) and isset($_SESSION["USER_PARTNER"]) and (int) $_SESSION["USER_PARTNER"] == 1) {
        $smarty->assign('html_payouts', VSubscriber::html_payouts());
    } else if ((isset($_GET["rg"]) and (!isset($_SESSION["USER_PARTNER"]) or (int) $_SESSION["USER_PARTNER"] == 0)) or (isset($_GET["rp"]) and (!isset($_SESSION["USER_PARTNER"]) or (int) $_SESSION["USER_PARTNER"] == 0))) {
        header("Location: " . $cfg["main_url"] . '/' . VHref::getKey("subscribers"));
        exit;
    }

    $class_smarty->displayPage('frontend', 'tpl_subscribers', $error_message, $notice_message);
}
