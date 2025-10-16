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
include_once 'f_core/f_classes/class_facebook/Facebook/autoload.php';

include_once $class_language->setLanguageFile('backend', 'language.members.entries');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.signin');
include_once $class_language->setLanguageFile('frontend', 'language.welcome');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$error_message  = null;
$notice_message = null;

$cfg           = $class_database->getConfigurations('global_signup,signup_ip_access,list_ip_signup,disabled_signup_message,reserved_usernames,frontend_signin_section,frontend_signin_count,signup_min_age,signup_max_age,signup_min_password,signup_max_password,signup_min_username,signup_max_username,paid_memberships,numeric_delimiter,paypal_email,paypal_test,paypal_test_email,signup_username_availability,signup_password_meter,signup_captcha,signup_domain_restriction,list_email_domains,list_reserved_users,signup_terms,username_format,username_format_dott,username_format_dash,username_format_underscore,discount_codes,account_email_verification,account_approval,notify_welcome,paypal_payments,approve_friends,backend_notification_payment,backend_notification_signup,backend_email,backend_username,recaptcha_site_key,recaptcha_secret_key,fb_app_id,fb_app_secret,fb_auth,gp_app_id,gp_app_secret,gp_auth');
$pack_class    = ($cfg["paid_memberships"] == 1 and $cfg["global_signup"] == 1 and $error_message == '') ? include_once 'f_core/f_classes/class.payment.php' : null;
$logged_in     = (($_GET["do"] == 'continue' or $_GET["do"] == 'process') and intval($_SESSION["USER_ID"]) > 0 and intval($_SESSION["renew_id"]) == intval($_SESSION["USER_ID"])) ? null : VLogin::isLoggedIn();
$error_message = ($cfg["signup_ip_access"] == 1 and !VIPaccess::checkIPlist($cfg["list_ip_signup"])) ? $smarty->assign('do_disable', 'yes') : null;
$memberships   = ($cfg["paid_memberships"] == 1 and $cfg["global_signup"] == 1 and $error_message == '') ? VPayment::getPackTypes() : null;
//form sessions will be reset, unless we're dealing with the second form
$formSessionReset = (intval($_POST["signup_submit_right"] != 1)) ? VSignup::formSessionReset() : null;

if (intval($_POST["frontend_global_submit"] == 1) and $cfg["global_signup"] == 1) {
    //left form has been submitted
    $form_fields    = VArraySection::getArray('signup');
    $allowedFields  = $form_fields[1];
    $requiredFields = $allowedFields;

    $error_message  = VSignup::processForm($allowedFields, $requiredFields);
    $notice_message = ($error_message == '' and VSignup::formSessionInit()) ? $language["notif.notice.signup.step1"] : null;
    $notice_message = ($error_message == '' and VSignup::processAccount()) ? $language["notif.notice.signup.success"] . $cfg["website_shortname"] . ($cfg["account_approval"] == 1 ? '. ' . $language["notif.notice.signup.approve"] : null) : null;
    $user_info      = ($cfg["paid_memberships"] == 1 and $notice_message != '') ? VPayment::getUserPack() : VUserinfo::getUserEmail();
}
if (($cfg['fb_auth'] == 1 or $cfg['gp_auth'] == 1) and $_POST and isset($_GET["do"]) and $_GET["do"] == 'auth_register') {
//    $auth        = VSignup::auth_register_ajax();
}

//free account update
$notice_message = ($cfg["global_signup"] == 1 and $cfg["paid_memberships"] == 1 and $_GET["p"] != '' and $_GET["u"] != '' and $_POST["signup_finalize"] == 1) ? VPayment::updateFreeEntry() : $notice_message;
//payment processing
$payment_prepare = ($cfg["global_signup"] == 1 and $cfg["paid_memberships"] == 1 and $_GET["p"] != '' and $_GET["u"] != '') ? VPayment::preparePayment() : null;
$payment_confirm = ($cfg["global_signup"] == 1 and $cfg["paid_memberships"] == 1 and $_GET["do"] == 'continue') ? VPayment::continuePayment() : null;
$payment_process = ($cfg["global_signup"] == 1 and $cfg["paid_memberships"] == 1 and ($_GET["do"] == 'process' or $_GET["do"] == 'ipn' or $_GET["do"] == 'success' or $_GET["do"] == 'cancel')) ? VPayment::doPayment($_GET["do"]) : null;
//username avail.
$username_check = (($cfg["signup_username_availability"] == 1 and $cfg["global_signup"] == 1) and ($_GET["do"] == 'ucheck' and strlen($_POST["frontend_signin_username"]) > 0)) ? VUserinfo::usernameAvailability($_POST["frontend_signin_username"]) : null;
//fb register
$fb_register = ($cfg['fb_auth'] == 1 and !isset($_GET["do"])) ? $smarty->assign('fb_register', VSignup::fb_register_button()) : null;
//gp register
$gp_register = ($cfg['gp_auth'] == 1 and !isset($_GET["do"])) ? $smarty->assign('gp_register', VSignup::gp_register_button()) : null;
//page
$display_page = !isset($_GET["do"]) ? $class_smarty->displayPage('frontend', 'tpl_signup', $error_message, $notice_message) : null;
