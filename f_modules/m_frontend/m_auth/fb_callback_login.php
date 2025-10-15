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

include_once 'f_core/f_classes/class_facebook/Facebook/autoload.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.recovery');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.signin');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$error_message  = null;
$notice_message = null;

$cfg = $class_database->getConfigurations('fb_auth,fb_app_id,fb_app_secret');

$_SESSION['FBRLH_' . 'state'] = $class_filter->clr_str($_GET['state']);

$fb = new Facebook\Facebook([
    'app_id'                => $cfg['fb_app_id'],
    'app_secret'            => $cfg['fb_app_secret'],
    'default_graph_version' => 'v2.7',
    'default_access_token'  => '1061711193887319|fc3a99ba0d42b98b51ac3fa124268422',
]);

$helper = $fb->getRedirectLoginHelper();

try {
    $u           = $cfg["main_url"] . '/f_modules/m_frontend/m_auth/fb_callback_login.php';
    $accessToken = $helper->getAccessToken($u);
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

if (!isset($accessToken)) {
    if ($helper->getError()) {
        header('HTTP/1.0 401 Unauthorized');
        echo "Error: " . $helper->getError() . "\n";
        echo "Error Code: " . $helper->getErrorCode() . "\n";
        echo "Error Reason: " . $helper->getErrorReason() . "\n";
        echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad request';
    }

    exit;
}

// Logged in

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();
// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
// Validation (these will throw FacebookSDKException's when they fail)
$tokenMetadata->validateAppId($cfg['fb_app_id']); // Replace {app-id} with your app id
// If you know the user ID this access token belongs to, you can validate it here
$tokenMetadata->validateExpiration();

$user_id = $tokenMetadata->getUserId();

if (!$accessToken->isLongLived()) {
    // Exchanges a short-lived access token for a long-lived one
    try {
        $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
    } catch (Facebook\Exceptions\FacebookSDKException $e) {
        echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
        exit;
    }
}

$_SESSION['fb_access_token'] = (string) $accessToken;

try {
    // Returns a `Facebook\FacebookResponse` object
    $response = $fb->get('/me?fields=id,about,cover,email,first_name,picture.width(300).height(300),last_name,name', $accessToken);
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

$user = $response->getGraphUser();

$go = true;

if ($user_id and $go) {
    $rs = $db->execute(sprintf("SELECT `usr_id` FROM `db_accountuser` WHERE `oauth_provider`='facebook' AND `oauth_uid`='%s' LIMIT 1;", $user_id));

    if ($rs->fields['usr_id']) {
        $usr_id  = $rs->fields['usr_id'];
        $reguser = VUserinfo::getUserInfo($usr_id);
    } else {
        if (isset($user['birthday']->date)) {
            $a        = $user['birthday']->date;
            $b        = explode(' ', $a);
            $birthday = $b[0];
        } else {
            $birthday = '0000-00-00';
        }
        $uu                  = explode('@', $user['email']);
        $_SESSION["fb_user"] = array(
            'id'         => $user_id,
            'email'      => $user['email'],
            'name'       => $user['name'],
            'picture'    => $user['picture']['url'],
            'username'   => $uu[0],
            'gender'     => 'M',
            'country'    => (isset($user['user_location']->name) ? $user['user_location']->name : ''),
            'city'       => (isset($user['user_location']->hometown) ? $user['user_location']->hometown : ''),
            'birth_date' => $birthday,
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
