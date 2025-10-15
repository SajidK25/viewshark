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
| Copyright (c) 2013-2018 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

include_once 'f_core/f_classes/class_google/Google_Client.php';
include_once 'f_core/f_classes/class_google/contrib/Google_Oauth2Service.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.recovery');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.signin');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$error_message  = null;
$notice_message = null;

$cfg = $class_database->getConfigurations('gp_auth,gp_app_id,gp_app_secret,list_reserved_users');

$clientId     = $cfg['gp_app_id'];
$clientSecret = $cfg['gp_app_secret'];
$redirectUrl  = $cfg['main_url'] . '/f_modules/m_frontend/m_auth/gp_callback_login.php';
$homeUrl      = $cfg['main_url'];

$gClient = new Google_Client();
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectUrl);
$google_oauthV2 = new Google_Oauth2Service($gClient);

if (isset($_GET['code'])) {
    $gClient->authenticate();
    $_SESSION['token'] = $gClient->getAccessToken();
    header('Location: ' . filter_var($redirectUrl, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
    $gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
//logged in
    $userProfile = $google_oauthV2->userinfo->get();
    $user_id     = $userProfile['id'];

    if ($user_id) {
        $rs = $db->execute(sprintf("SELECT `usr_id` FROM `db_accountuser` WHERE `oauth_provider`='google' AND `oauth_uid`='%s' LIMIT 1;", $user_id));

        if ($rs->fields['usr_id']) {
            $usr_id  = $rs->fields['usr_id'];
            $reguser = VUserinfo::getUserInfo($usr_id);
            $loc     = $cfg["main_url"] . '/' . VHref::getKey('account');
            echo '<script type="text/javascript">opener.location="' . $loc . '"; window.close();</script>';
        } else {
            $uu                  = explode('@', $userProfile['email']);
            $_SESSION["gp_user"] = array(
                'id'         => $user_id,
                'email'      => $userProfile['email'],
                'name'       => $userProfile['name'],
                'picture'    => $userProfile['picture'],
                'username'   => $uu[0],
                'gender'     => 'M',
                'country'    => '',
                'city'       => '',
                'birth_date' => '0000-00-00',
                'status'     => 1,
            );

            echo VSignup::auth_register_ajax();
            exit;

        }

        if ($reguser['key'] != '') {
            $_SESSION["USER_ID"]    = $usr_id;
            $_SESSION["USER_NAME"]  = $reguser['uname'];
            $_SESSION["USER_KEY"]   = $reguser['key'];
            $_SESSION["USER_DNAME"] = $reguser['dname'];

            $login_update = VLogin::updateOnLogin($usr_id);
            $log_activity = ($cfg["activity_logging"] == 1 and $action = new VActivity($usr_id, 0)) ? $action->addTo('log_signin') : null;

            $loc = $cfg["main_url"] . '/' . VHref::getKey('account');

            echo '<script type="text/javascript">opener.location="' . $loc . '"; window.close();</script>';
        } else {
            die('Account suspended!');
        }
    }
}
