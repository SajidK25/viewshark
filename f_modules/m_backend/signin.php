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
include_once 'f_core/f_classes/class_recaptcha/autoload.php';

include_once $class_language->setLanguageFile('backend', 'language.signin');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.signin');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.recovery');

$error_message  = null;
$notice_message = null;
$cfg            = $class_database->getConfigurations('backend_remember,backend_signin_section,backend_username,backend_password,activity_logging,password_recovery_captcha,username_recovery_captcha,recovery_link_lifetime,allow_username_recovery,allow_password_recovery,backend_username_recovery,backend_password_recovery,backend_username_recovery_captcha,backend_password_recovery_captcha,backend_username,backend_email,noreply_email,global_signup,signup_ip_access,list_ip_signup,disabled_signup_message,reserved_usernames,signup_min_age,signup_max_age,signup_min_password,signup_max_password,signup_min_username,signup_max_username,paid_memberships,numeric_delimiter,paypal_email,paypal_test,paypal_test_email,signup_username_availability,signup_password_meter,signin_captcha_be,signup_domain_restriction,list_email_domains,list_reserved_users,signup_terms,username_format,username_format_dott,username_format_dash,username_format_underscore,discount_codes,account_email_verification,account_approval,notify_welcome,paypal_payments,approve_friends,backend_notification_payment,backend_notification_signup,backend_email,backend_username,recaptcha_site_key,recaptcha_secret_key');

if (intval($_POST["frontend_global_submit"] == 1) and $cfg["backend_signin_section"] == 1) {
    //login has been submitted
    $remember      = ($error_message == '' and intval($_POST["signin_remember"]) == 1) ? 1 : null;
    $error_message = !VLogin::loginAttempt('backend', $class_filter->clr_str($_POST["frontend_signin_username"]), $_POST["frontend_signin_password"], $remember) ? $language["frontend.signin.error.auth"] : null;
}
$remember = ($cfg["backend_remember"] == 1 and $cfg["backend_signin_section"] == 1) ? VLoginRemember::checkLogin('backend') : null;

if ($_SESSION["ADMIN_NAME"] == $cfg["backend_username"] and $_SESSION["ADMIN_PASS"] == $cfg["backend_password"]) {
    $next = $class_filter->clr_str($_GET["next"]);
    $class_redirect->to('', $cfg["main_url"] . '/' . $backend_access_url . '/' . ($next == '' ? VHref::getKey('be_dashboard') : $next));
    exit;
}

$class_smarty->displayPage('backend', 'backend_tpl_signin', $error_message);
