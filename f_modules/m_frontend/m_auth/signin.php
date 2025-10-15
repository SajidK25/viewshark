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
include_once 'f_core/f_classes/class_recaptcha/autoload.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.signin');

$error_message        = isset($_SESSION["USER_ERROR"]) ? $_SESSION["USER_ERROR"] : null;
$notice_message       = null;
$cfg                  = $class_database->getConfigurations('login_remember,paid_memberships,frontend_signin_section,frontend_signin_count,fb_app_id,fb_app_secret,fb_auth,gp_app_id,gp_app_secret,gp_auth,recaptcha_site_key,recaptcha_secret_key,signin_captcha,list_reserved_users');
$_SESSION["renew_id"] = $cfg["paid_memberships"] == 1 ? '' : null;

if ($cfg['gp_auth'] == 1 and $cfg["frontend_signin_section"] == 1) {
//google authentication
    include_once 'f_core/f_classes/class_google/Google_Client.php';
    include_once 'f_core/f_classes/class_google/contrib/Google_Oauth2Service.php';

    $clientId     = $cfg['gp_app_id'];
    $clientSecret = $cfg['gp_app_secret'];
    $redirectUrl  = $cfg['main_url'] . '/f_modules/m_frontend/m_auth/gp_callback_login.php';
    $homeUrl      = $cfg['main_url'];

    $gClient = new Google_Client();
    $gClient->setAccessType('online');
    $gClient->setApprovalPrompt('auto');
    $gClient->setClientId($clientId);
    $gClient->setClientSecret($clientSecret);
    $gClient->setRedirectUri($redirectUrl);

    $google_oauthV2 = new Google_Oauth2Service($gClient);

    $authUrl = $gClient->createAuthUrl();

    $smarty->assign('gp_loginUrl', htmlspecialchars($authUrl));
}

if ($cfg['fb_auth'] == 1 and $cfg["frontend_signin_section"] == 1) {
//facebook authentication
    include_once 'f_core/f_classes/class_facebook/Facebook/autoload.php';

    $fb = new Facebook\Facebook([
        'app_id'                => $cfg['fb_app_id'],
        'app_secret'            => $cfg['fb_app_secret'],
        'default_graph_version' => 'v2.7',
        'default_access_token'  => '1061711193887319|fc3a99ba0d42b98b51ac3fa124268422',
    ]);

    $fb_helper      = $fb->getRedirectLoginHelper();
    $fb_permissions = ['email']; // Optional permissions
    $fb_loginUrl    = $fb_helper->getLoginUrl($cfg['main_url'] . '/f_modules/m_frontend/m_auth/fb_callback_login.php', $fb_permissions);

    $smarty->assign('fb_loginUrl', htmlspecialchars($fb_loginUrl));
}

if (intval($_POST["frontend_global_submit"] == 1) and $cfg["frontend_signin_section"] == 1) {
    //regular login
    $remember      = ($error_message == '' and intval($_POST["signin_remember"]) == 1) ? 1 : null;
    $error_message = !VLogin::loginAttempt('frontend', $class_filter->clr_str($_POST["frontend_signin_username"]), $_POST["frontend_signin_password"], $remember) ? $language["frontend.signin.error.auth"] : null;
}

$remember  = ($cfg["login_remember"] == 1 and $cfg["frontend_signin_section"] == 1) ? VLoginRemember::checkLogin('frontend') : null;
$logged_in = VLogin::isLoggedIn();

$class_smarty->displayPage('frontend', 'tpl_signin', $error_message, $notice_message);

$_SESSION["USER_ERROR"] = null;
