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

$cfg = $class_database->getConfigurations('gp_auth,gp_app_id,gp_app_secret');

$clientId     = $cfg['gp_app_id'];
$clientSecret = $cfg['gp_app_secret'];
$redirectUrl  = $cfg['main_url'] . '/f_modules/m_frontend/m_auth/gp_callback_register.php';
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
    $user        = $google_oauthV2->userinfo->get();
    $user_id     = $user['id'];
    $user_email  = $user['email'];
    $user_name   = $user['name'];
    $user_gender = $user['gender'][0];
    $user_gender = $user_gender == '' ? 'M' : $user_gender;
    $user_bday   = '0000-00-00';

    $rs = $db->execute(sprintf("SELECT `usr_id` FROM `db_accountuser` WHERE `oauth_provider`='google' AND `oauth_uid`='%s' LIMIT 1;", $user_id));

    if ($rs->fields['usr_id']) {
        $usr_id                 = $rs->fields['usr_id'];
        $_SESSION["USER_ERROR"] = $language["notif.error.accout.linked"];
        $loc                    = $cfg["main_url"] . '/' . VHref::getKey('signin') . '?f=';

        echo '<script type="text/javascript">opener.location="' . $loc . '"; window.close();</script>';

        exit;
    } else {
        $_SESSION["gp_user"] = array(
            'id'         => $user_id,
            'email'      => $user_email,
            'name'       => $user_name,
            'gender'     => $user_gender,
            'birth_date' => $user_bday,
            'status'     => 1,
        );

        $loc = $cfg["main_url"] . '/' . VHref::getKey('signup') . '?u=' . $user_id;

        echo '<script type="text/javascript">opener.location="' . $loc . '"; window.close();</script>';

        exit;
    }
}
