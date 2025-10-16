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

defined('_ISVALID') or header('Location: /error');

class VSignup
{
    /* google, facebook ajax username registration/confirmation */
    public static function auth_register_ajax()
    {
        global $db, $cfg, $class_filter, $language;

        $oauth_type = isset($_SESSION['fb_user']['id']) ? 'facebook' : (isset($_SESSION['gp_user']['id']) ? 'google' : null);
        $user       = $oauth_type == 'facebook' ? $_SESSION['fb_user'] : ($oauth_type == 'google' ? $_SESSION['gp_user'] : null);

        $username = $class_filter->clr_str($user['username']);
        $username = preg_replace('/[^a-zA-Z0-9@_.\-]/', '', $username);
        $user_id  = $class_filter->clr_str($user['id']);
        $confirm  = (int) $user['status'];

        $error_message = !VUserinfo::usernameVerification($username) ? 'error:' . $language['notif.error.invalid.user'] : false;
        if ($error_message) {
            echo 'error:' . $error_message;
            return;
        }

        if (!$error_message) {
            $password = time(); //a password is needed
            if (isset($user['name']) != '') {
                $fn    = explode(" ", $user['name']);
                $first = $fn[0];
                $last  = $fn[1];
            }

            if ($confirm > 0) {
                $_SESSION['signup_username'] = $username;

                $uid     = self::processAccount(array('oauth_provider' => $oauth_type, 'oauth_uid' => $user_id, 'username' => $username, 'email' => $user['email'], 'password' => $password, 'first' => $first, 'last' => $last, 'gender' => $user['gender'], 'birth_date' => $user['birth_date']));
                $reguser = VUserinfo::getUserInfo($uid);

                if (($oauth_type == 'facebook' and isset($user['picture']['url'])) or ($oauth_type == 'google' and isset($user['picture']))) {
                    $fr = $oauth_type == 'facebook' ? $user['picture']['url'] : $user['picture'];
                    $to = $cfg['profile_images_dir'] . '/' . $reguser['key'] . '/' . $reguser['key'] . '.jpg';

                    file_put_contents($to, file_get_contents($fr));

                    $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_photo`='file' WHERE `usr_id`='%s' LIMIT 1;", $uid));
                }
                if ($oauth_type == 'google') {

                    $loc = $cfg['main_url'] . '/' . VHref::getKey('account');
                    echo '<script type="text/javascript">opener.location="' . $loc . '"; window.close();</script>';

                    exit;
                }

            }

            if ($uid > 0) {
                if ($reguser['key'] != '') {
                    $_SESSION['USER_ID']    = $uid;
                    $_SESSION['USER_NAME']  = $reguser['uname'];
                    $_SESSION['USER_KEY']   = $reguser['key'];
                    $_SESSION['USER_DNAME'] = $reguser['dname'];

                    unset($_SESSION['fb_user']);
                    unset($_SESSION['gp_user']);
                    unset($_SESSION['fb_access_token']);
                    unset($_SESSION['token']);
                    unset($_SESSION['FBRLH_state']);

                    $loc = $cfg['main_url'] . '/' . VHref::getKey('account');
                    echo '<script type="text/javascript">opener.location="' . $loc . '"; window.close();</script>';
                } else {
                    die('Account suspended!');
                }
            }

        }
    }
    /* google, facebook ajax username registration/confirmation */
    public static function auth_register_ajax_src()
    {
        global $db, $cfg, $class_filter, $language;

        $username = $class_filter->clr_str($_POST['auth_username']);
        $user_id  = $class_filter->clr_str($_POST['auth_userid']);
        $confirm  = (int) $_POST['auth_confirm'];

        $error_message = !VUserinfo::usernameVerification($username) ? $language['notif.error.invalid.user'] : false;

        if ($error_message) {
            echo 'error:' . $error_message;
            return;
        }

        if (!$error_message) {
            $oauth_type = isset($_SESSION['fb_user']['id']) ? 'facebook' : (isset($_SESSION['gp_user']['id']) ? 'google' : null);
            $user       = $oauth_type == 'facebook' ? $_SESSION['fb_user'] : ($oauth_type == 'google' ? $_SESSION['gp_user'] : null);

            $password = time(); //a password is needed

            if (isset($user['name']) != '') {
                $fn    = explode(" ", $user['name']);
                $first = $fn[0];
                $last  = $fn[1];
            }

            if ($confirm > 0) {
                $_SESSION['signup_username'] = $username;

                $uid     = self::processAccount(array('oauth_provider' => $oauth_type, 'oauth_uid' => $user_id, 'username' => $username, 'email' => $user['email'], 'password' => $password, 'first' => $first, 'last' => $last, 'gender' => $user['gender'], 'birth_date' => $user['birth_date']));
                $reguser = VUserinfo::getUserInfo($uid);

                if (($oauth_type == 'facebook' and isset($user['picture']['url'])) or ($oauth_type == 'google' and isset($user['picture']))) {
                    $fr = $oauth_type == 'facebook' ? $user['picture']['url'] : $user['picture'];
                    $to = $cfg['profile_images_dir'] . '/' . $reguser['key'] . '/' . $reguser['key'] . '.jpg';

                    file_put_contents($to, file_get_contents($fr));

                    $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_photo`='file' WHERE `usr_id`='%s' LIMIT 1;", $uid));
                }
            }

            if ($uid > 0) {
                if ($reguser['key'] != '') {
                    $_SESSION['USER_ID']    = $uid;
                    $_SESSION['USER_NAME']  = $reguser['uname'];
                    $_SESSION['USER_KEY']   = $reguser['key'];
                    $_SESSION['USER_DNAME'] = $reguser['dname'];

                    unset($_SESSION['fb_user']);
                    unset($_SESSION['gp_user']);
                    unset($_SESSION['fb_access_token']);
                    unset($_SESSION['token']);
                    unset($_SESSION['FBRLH_state']);

                    $loc = $cfg['main_url'] . '/' . VHref::getKey('account');
                    echo '<script type="text/javascript">window.location("' . $loc . '");</script>';
                } else {
                    die('Account suspended!');
                }
            }
        }
    }
    /* facebook register button */
    public static function fb_register_button($signup = false)
    {
        global $cfg, $language;

        $text = !$signup ? $language['frontend.signup.fb'] : $language['frontend.signin.fb'];
        $cb   = $cfg['main_url'] . '/f_modules/m_frontend/m_auth/fb_callback_login.php';

        $fb = new Facebook\Facebook([
            'app_id'                => $cfg['fb_app_id'],
            'app_secret'            => $cfg['fb_app_secret'],
            'default_graph_version' => 'v2.7',
            'display'               => 'popup',
            'default_access_token'  => '1061711193887319|fc3a99ba0d42b98b51ac3fa124268422',
        ]);

        $helper      = $fb->getRedirectLoginHelper();
        $permissions = ['email'];
        $loginUrl    = $helper->getLoginUrl($cb, $permissions);

        return '<script type="text/javascript"> function popupwindow(url, title, win, w, h) { var y = window.top.outerHeight / 2 + window.top.screenY - ( h / 2); var x = window.top.outerWidth / 2 + window.top.screenX - ( w / 2); winpop = window.open(url, title, \'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=\'+w+\', height=\'+h+\', top=\'+y+\', left=\'+x); } </script>
        <button onclick="popupwindow(&quot;' . htmlspecialchars($loginUrl) . '&display=popup&quot;, &quot;winpop&quot;, &quot;winpop&quot;, &quot;560&quot;, &quot;400&quot;); return false;" class="search-button form-button fb-login-button no-display" type="button" value="1" name="frontend_global_fb"><span>' . $text . '</span></button>
        <a href="javascript:;" onclick="popupwindow(&quot;' . htmlspecialchars($loginUrl) . '&display=popup&quot;, &quot;winpop&quot;, &quot;winpop&quot;, &quot;560&quot;, &quot;400&quot;); return false;" rel="nofollow" style="display:inline-block;padding:10px 20px;"><img src="' . $cfg['global_images_url'] . '/f_logo_RGB-Blue_58.png" height="32" style="display:block;margin-bottom:10px"> <span>' . $language['frontend.signin.fb'] . '</span></a>';

    }
    /* google register button */
    public static function gp_register_button($signup = false)
    {
        include_once 'f_core/f_classes/class_google/Google_Client.php';
        include_once 'f_core/f_classes/class_google/contrib/Google_Oauth2Service.php';

        global $cfg, $language;

        $text = !$signup ? $language['frontend.signup.gp'] : $language['frontend.signin.gp'];
        $cb   = $cfg['main_url'] . '/f_modules/m_frontend/m_auth/gp_callback_login.php';

        $clientId     = $cfg['gp_app_id'];
        $clientSecret = $cfg['gp_app_secret'];
        $redirectUrl  = $cb;
        $homeUrl      = $cfg['main_url'];

        $gClient = new Google_Client();
        $gClient->setAccessType('online');
        $gClient->setApprovalPrompt('auto');
        $gClient->setClientId($clientId);
        $gClient->setClientSecret($clientSecret);
        $gClient->setRedirectUri($redirectUrl);

        $google_oauthV2 = new Google_Oauth2Service($gClient);

        $authUrl = $gClient->createAuthUrl();

        return '<script type="text/javascript">function popupwindow(url, title, win, w, h) { var y = window.top.outerHeight / 2 + window.top.screenY - ( h / 2); var x = window.top.outerWidth / 2 + window.top.screenX - ( w / 2); winpop = window.open(url, title, \'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=\'+w+\', height=\'+h+\', top=\'+y+\', left=\'+x); }</script>
        <button onclick="popupwindow(&quot;' . htmlspecialchars($authUrl) . '&display=popup&quot;, &quot;winpop&quot;, &quot;winpop&quot;, &quot;560&quot;, &quot;400&quot;); return false;" class="search-button form-button gp-login-button no-display" type="button" value="1" name="frontend_global_fb"><span>' . $text . '</span></button>
        <a href="javascript:;" onclick="popupwindow(&quot;' . htmlspecialchars($authUrl) . '&display=popup&quot;, &quot;winpop&quot;, &quot;winpop&quot;, &quot;560&quot;, &quot;400&quot;); return false;" rel="nofollow" style="display:inline-block;padding:10px 20px;"><img src="' . $cfg['global_images_url'] . '/google-logo.png" height="32" style="display:block;margin-bottom:10px"> <span>' . $language['frontend.signin.gp'] . '</span></a>';
    }

    /* signup form */
    public function processForm($allowedFields, $requiredFields)
    {
        global $cfg, $language, $class_filter;

        $siteKey = $cfg['recaptcha_site_key'];
        $secret  = $cfg['recaptcha_secret_key'];

        $email_check = new VValidation;
        //check for empty fields
        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields, array("frontend_signin_username", "frontend_signup_location", "frontend_signup_bdayM", "frontend_signup_bdayD", "frontend_signup_bdayY", "frontend_signup_gender", "frontend_membership_type"));
        //check for valid username format
        $error_message = (!VUserinfo::usernameVerification($class_filter->clr_str(trim($_POST['frontend_signin_username']))) and $error_message == '') ? $language['notif.error.invalid.user'] : $error_message;
        //check for valid email format
        $error_message = (!$email_check->checkEmailAddress($class_filter->clr_str(trim($_POST['frontend_signup_emailadd']))) and $error_message == '') ? $language['frontend.signup.email.invalid'] : $error_message;
        //check for email domain restriction
        $error_message = ($cfg['signup_domain_restriction'] == 1 and $error_message == '' and !VIPaccess::emailDomainCheck()) ? $language['notif.error.nosignup'] : $error_message;
        //check for existing registered email
        $error_message = (VUserinfo::existingEmail($class_filter->clr_str(trim($_POST['frontend_signup_emailadd']))) and $error_message == '') ? $language['notif.error.existing.email'] : $error_message;
        //check for password format
        $error_message = ((strlen($_POST['frontend_signup_setpass']) < $cfg['signup_min_password'] or strlen($_POST['frontend_signup_setpass']) > $cfg['signup_max_password'] or strlen($_POST['frontend_signup_setpassagain']) < $cfg['signup_min_password'] or strlen($_POST['frontend_signup_setpassagain']) > $cfg['signup_max_password']) and $error_message == '') ? $language['notif.error.invalid.pass'] : $error_message;
        //check for password match
        $error_message = (md5($_POST['frontend_signup_setpass']) !== md5($_POST['frontend_signup_setpassagain']) and $error_message == '') ? $language['notif.error.pass.nomatch'] : $error_message;
        //check for correct captcha
        if ($error_message == '' and $cfg['signup_captcha'] == 1) {
            $captcha = $class_filter->clr_str($_POST['g-recaptcha-response']);

            if ($captcha == '') {
                $error_message = $language['notif.error.incorect.captcha'];
            } else {
                $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\CurlPost());
                $resp      = $recaptcha->verify($captcha, $_SERVER[REM_ADDR]);
                if ($resp->isSuccess()) {
                } else {
                    foreach ($resp->getErrorCodes() as $code) {
                        $error_message = $code;
                    }
                }
            }
        }

        return $error_message;
    }
    /* define user folders */
    public function getUserFolders($usr_key)
    {
        global $cfg;

        $dir              = array();
        $dir_user_media   = array();
        $dir_user_uploads = array();
        $dir_fch_views    = array();

        $dir_user_media_main = $cfg['media_files_dir'] . '/' . $usr_key;
        $dir_user_media[]    = $dir_user_media_main . '/a';
        $dir_user_media[]    = $dir_user_media_main . '/d';
        $dir_user_media[]    = $dir_user_media_main . '/i';
        $dir_user_media[]    = $dir_user_media_main . '/t';
        $dir_user_media[]    = $dir_user_media_main . '/v';
        $dir_user_media[]    = $dir_user_media_main . '/s';
        $dir_user_media[]    = $dir_user_media_main . '/b';
        $dir_user_media[]    = $dir_user_media_main . '/l';

        $dir_user_uploads_main = $cfg['upload_files_dir'] . '/' . $usr_key;
        $dir_user_uploads[]    = $dir_user_uploads_main . '/a';
        $dir_user_uploads[]    = $dir_user_uploads_main . '/d';
        $dir_user_uploads[]    = $dir_user_uploads_main . '/i';
        $dir_user_uploads[]    = $dir_user_uploads_main . '/v';
        $dir_user_uploads[]    = $dir_user_uploads_main . '/s';
        $dir_user_uploads[]    = $dir_user_uploads_main . '/b';
        $dir_user_uploads[]    = $dir_user_uploads_main . '/l';

        $dir_fch_views_main = $cfg['channel_views_dir'] . '/' . $usr_key;
        $dir_fch_views[]    = $dir_fch_views_main . '/a';
        $dir_fch_views[]    = $dir_fch_views_main . '/c';
        $dir_fch_views[]    = $dir_fch_views_main . '/d';
        $dir_fch_views[]    = $dir_fch_views_main . '/i';
        $dir_fch_views[]    = $dir_fch_views_main . '/v';
        $dir_fch_views[]    = $dir_fch_views_main . '/s';
        $dir_fch_views[]    = $dir_fch_views_main . '/b';
        $dir_fch_views[]    = $dir_fch_views_main . '/l';

        $dir_user_profile_main = $cfg['profile_images_dir'] . '/' . $usr_key;

        $dir[0] = array($dir_user_media_main, $dir_user_uploads_main, $dir_fch_views_main, $dir_user_profile_main);
        $dir[1] = array($dir_user_media, $dir_user_uploads, $dir_fch_views);

        return array($dir[0], $dir[1]);
    }
    /* create user folders */
    public function createUserFolders($usr_key)
    {
        global $cfg;

        $dirs    = self::getUserFolders($usr_key);
        $dir[0]  = $dirs[0];
        $dir[1]  = $dirs[1];
        $php_cgi = (strpos(php_sapi_name(), 'cgi')) ? 1 : 0;

        foreach ($dir[0] as $fk => $fv) {
            if (!is_dir($fv)) {
                mkdir($fv);

                if ($php_cgi == 0) {
                    chmod($fv, 0777);
                }
            }
        }
        foreach ($dir[1] as $dk => $dv) {
            foreach ($dv as $mk => $mv) {
                if (!is_dir($mv)) {
                    mkdir($mv);

                    if ($php_cgi == 0) {
                        chmod($mv, 0777);
                    }
                }
            }
        }
        copy($cfg['profile_images_dir'] . '/default.jpg', $cfg['profile_images_dir'] . '/' . $usr_key . '/' . $usr_key . '.jpg');
    }
    /* validating registration account */
    public function processAccount($fields = false)
    {
        global $db, $cfg, $class_filter, $class_login, $class_redirect, $class_database;

        $email_check  = new VValidation;
        $hasher       = new VPasswordHash(8, false);
        $emailadd     = !$fields ? $class_filter->clr_str(trim($_POST['frontend_signup_emailadd'])) : trim($fields['email']);
        $emailadd     = preg_replace('/[^a-zA-Z0-9_.\-@]/', '', $emailadd);
        $signup_email = !$fields ? ($email_check->checkEmailAddress($emailadd) ? $emailadd : null) : $fields['email'];
        $signup_email = preg_replace('/[^a-zA-Z0-9_.\-@]/', '', $signup_email);
        $enc_pass     = $class_filter->clr_str($hasher->HashPassword((!$fields ? $_POST['frontend_signup_setpass'] : $fields['password'])));
        $extra_emails = intval($_POST['frontend_signup_extraemail']);
        $signup_bday  = date("Y-m-d");
        $usr_active   = ($cfg['account_approval'] == 1 and $cfg['paid_memberships'] == 0) ? 0 : 1;
        $u_key        = VUserinfo::generateRandomString(10);
        $chk          = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $u_key);

        for ($i = 1; $i <= 10; $i++) {
            $chk = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $u_key);

            if ($chk) {
                $u_key = VUserinfo::generateRandomString(10);
            } else {
                break;
            }
        }

        $perm_arr = array(
            "perm_upload_l"         => 1,
            "perm_upload_v"         => 1,
            "perm_upload_s"         => 1,
            "perm_upload_i"         => 1,
            "perm_upload_a"         => 1,
            "perm_upload_d"         => 1,
            "perm_upload_b"         => 1,
            "perm_view_l"           => 1,
            "perm_view_v"           => 1,
            "perm_view_s"           => 1,
            "perm_view_i"           => 1,
            "perm_view_a"           => 1,
            "perm_view_d"           => 1,
            "perm_view_b"           => 1,
            "perm_live_chat"        => 1,
            "perm_live_vod"         => 1,
            "perm_embed_single"     => 1,
            "perm_embed_yt_video"   => 0,
            "perm_embed_yt_short"   => 0,
            "perm_embed_yt_channel" => 0,
            "perm_embed_dm_video"   => 0,
            "perm_embed_dm_user"    => 0,
            "perm_embed_mc_video"   => 0,
            "perm_embed_mc_user"    => 0,
            "perm_embed_vi_user"    => 0,
        );
        $ch_cfg = serialize(array(
            "ch_visible"         => 1,
            "ch_m_comments"      => 1,
            "ch_m_friends"       => 1,
            "ch_m_channels"      => 1,
            "ch_m_events"        => 1,
            "ch_m_activity"      => 1,
            "ch_m_subscribers"   => 1,
            "ch_m_subscriptions" => 1,
            "ch_m_followers"     => 1,
            "ch_m_following"     => 1,
            "ch_v_upfiles"       => 1,
            "ch_v_favorites"     => 1,
            "ch_v_playlists"     => 1,
            "ch_v_all"           => 1,
            "ch_m_home"          => 1,
            "ch_m_live"          => 1,
            "ch_m_videos"        => 1,
            "ch_m_shorts"        => 1,
            "ch_m_images"        => 1,
            "ch_m_audios"        => 1,
            "ch_m_documents"     => 1,
            "ch_m_blogs"         => 1,
            "ch_m_playlists"     => 1,
            "ch_m_discussion"    => 1,
            "ch_m_about"         => 1,
            "ch_v_layout"        => "player",
            "ch_v_content"       => "all",
            "ch_v_default"       => ($cfg['video_module'] == 1 ? 'video' : ($cfg['live_module'] == 1 ? 'live' : ($cfg['image_module'] == 1 ? 'image' : ($cfg['audio_module'] == 1 ? 'audio' : ($cfg['document_module'] == 1 ? 'doc' : null))))),
            "ch_v_featured"      => "",
            "ch_v_autoplay"      => 0,
            "ch_v_pl_ids"        => "",
            "ch_ev_expired"      => 1,
            "ch_ev_map"          => 0,
            "ch_comm_perms"      => "free",
            "ch_comm_spam"       => "yes"));

        $ch_pfields = serialize(array(
            "profile_edit_name"      => 0,
            "profile_edit_total"     => 0,
            "profile_edit_age"       => 0,
            "profile_edit_last"      => 0,
            "profile_edit_subs"      => 0,
            "profile_edit_infl"      => 0,
            "profile_edit_style"     => 0,
            "profile_edit_descr"     => 0,
            "profile_edit_about"     => 0,
            "profile_edit_site"      => 0,
            "profile_edit_town"      => 0,
            "profile_edit_country"   => 0,
            "profile_edit_occup"     => 0,
            "profile_edit_companies" => 0,
            "profile_edit_school"    => 0,
            "profile_edit_interes"   => 0,
            "profile_edit_movies"    => 0,
            "profile_edit_music"     => 0,
            "profile_edit_books"     => 0,
        ));

        $ch_rownum  = serialize(array("r_friends" => 2, "r_subscribers" => 2, "r_subscriptions" => 2, "r_activity" => 10));
        $_usr       = trim($_SESSION['signup_username']);
        $_usr       = preg_replace('/[^a-zA-Z0-9@_.\-]/', '', $_usr);
        $ins_array1 = array(
            "usr_key"         => $u_key,
            "usr_user"        => $_usr,
            "usr_password"    => $enc_pass,
            "usr_email"       => $signup_email,
            "usr_emailextras" => $extra_emails,
            "usr_joindate"    => date("Y-m-d H:i:s"),
            "usr_IP"          => $class_filter->clr_str($_SERVER[REM_ADDR]),
            "usr_theme"       => "light",
            "usr_perm"        => serialize($perm_arr),
            "usr_verified"    => ($cfg['account_email_verification'] == 0 ? 1 : 0),
            "usr_active"      => $usr_active,
            "usr_status"      => $usr_active,
            "usr_birthday"    => (!$fields ? $signup_bday : $fields['birth_date']),
            "usr_gender"      => (!$fields ? 'M' : $fields['gender']),
            "usr_country"     => 'United States',
            "usr_photo"       => 'file',
            "usr_dname"       => $_usr,
            "ch_user"         => $_usr,
            "ch_dname"        => $_usr,
            "ch_cfg"          => $ch_cfg,
            "ch_pfields"      => $ch_pfields,
            "ch_rownum"       => $ch_rownum,
        );
        if ($fields) {
            $ins_array1['oauth_provider'] = $fields['oauth_provider'];
            $ins_array1['oauth_uid']      = $fields['oauth_uid'];

            if ($fields['first'] != '') {
                $ins_array1['usr_fname'] = $fields['first'];
            }
            if ($fields['last'] != '') {
                $ins_array1['usr_lname'] = $fields['last'];
            }
        }
        $account_q = $class_database->doInsert('db_accountuser', $ins_array1);
        $user_id   = $db->Insert_ID();

        if ($user_id > 0) {
            /* create user folders */
            self::createUserFolders($u_key);

            if ($cfg['activity_logging'] == 1) {
//activity tracking
                $db->execute(sprintf("INSERT INTO `db_trackactivity` SET `usr_id`='%s';", $user_id));
            }
            if ($_GET['next'] != '') {
//friend invited
                $next     = str_replace(array("-", "%", "="), array("?", "?", "?"), $class_filter->clr_str($_GET['next']));
                $next_arr = explode("?", $next);
                $pwd_id   = $next_arr[2];
                $db_q     = sprintf("SELECT A.`ct_id`, A.`usr_id`, B.`usr_user` FROM `db_usercontacts` A, `db_accountuser` B WHERE A.`pwd_id`='%s' AND A.`ct_active`='1' AND A.`usr_id`=B.`usr_id`;", $pwd_id);
                $q        = $db->execute($db_q);
                if ($q->fields['ct_id'] > 0) {
                    $u = $q->fields['usr_user'];
                    $q = $db->execute(sprintf("UPDATE `db_usercodes` SET `use_date`='%s', code_used='1', code_active='0' WHERE `pwd_id`='%s' AND `code_active`='1' LIMIT 1;", date("Y-m-d H:i:s"), $pwd_id));
                    $q = $db->execute(sprintf("UPDATE `db_usercontacts` SET `ct_username`='%s', `ct_friend`='1', `ct_datetime`='%s' WHERE `pwd_id`='%s' LIMIT 1;", $_SESSION['signup_username'], date("Y-m-d H:i:s"), $pwd_id));
                    $q = $class_database->doInsert('db_usercontacts', array("usr_id" => $user_id, "pwd_id" => VUserinfo::generateRandomString(10), "ct_username" => $u, "ct_friend" => 1, "ct_datetime" => date("Y-m-d H:i:s")));
                }
            }
            if ($cfg['account_email_verification'] == 1 or $cfg['notify_welcome'] == 1) {
//email verification and welcome notification
                $notifier = new VNotify;
            }

            $welcome_email      = ($cfg['notify_welcome'] == 1 and $notifier->dst_mail = $signup_email) ? VNotify::queInit('welcome', array($signup_email), $user_id) : null;
            $verification_email = ($cfg['account_email_verification'] == 1) ? VNotify::queInit('account_email_verification', array($signup_email), $user_id) : null;
            $admin_email        = ($cfg['backend_notification_signup'] == 1) ? VNotify::queInit('backend_notification_signup', array($cfg['backend_email']), $signup_email) : null;

            if ($cfg['paid_memberships'] == 1) {
                //paid memberships
                $q = $db->execute(sprintf("INSERT INTO `db_packusers` SET `usr_id`='%s';", $user_id));
                $q = $db->execute(sprintf("SELECT `pk_price`, `pk_period` FROM `db_packtypes` WHERE `pk_id`='%s' LIMIT 2;", intval($_SESSION['signup_pack'])));

                switch ($q->fields['pk_price']) {
                    case '0':
                        $expire_time = date("Y-m-d H:i:s", strtotime("+" . $q->fields['pk_period'] . " day"));
                        $sub_usage   = VPayment::updateFreeUsage($user_id);
                        $sub_update  = VPayment::updateFreeAccount(intval($_SESSION['signup_pack']), $expire_time, $user_id);
                        if (!$fields) {
                            $login = ($db->Affected_Rows() > 0) ? VLogin::loginAttempt('frontend', $class_filter->clr_str($_SESSION['signup_username']), $class_filter->clr_str($_POST['frontend_signup_setpass'])) : null;
                            if ($login) {return true;} else {return false;}
                        }
                        break;
                    default:
                        $q = $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_active`='0', `usr_status='0' WHERE `usr_id`='%s' LIMIT 1;", intval($user_id)));
                        // a logout and session clearing might go here
                        if (!$fields) {
                            $_SESSION['renew_id'] = '';
                            header('Location: ' . $cfg['main_url'] . '/' . VHref::getKey('signup') . '/' . VHref::getKey('x_payment') . '?p=' . base64_encode(intval($_SESSION['signup_pack'])) . '&u=' . base64_encode(intval($user_id)));
                            die;
                        }
                }
            }

            return $user_id;
        } else {
            return false;
        }

    }
    /* set account verified */
    public function verifyAccount()
    {
        global $db;

        $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_verified`='1' WHERE `usr_id`='%s' AND `usr_verified`='0' LIMIT 1;", VRecovery::getRecoveryID($_GET['sid'], 'verification')));
        if ($db->Affected_Rows() > 0) {
            $update = VRecovery::updateRecoveryUsage('verification');
            return true;
        } else {
            return false;
        }

    }
    /* signup form sessions start */
    public function formSessionInit()
    {
        global $cfg, $class_filter, $language;

        $signup_username = ($cfg['username_format'] == 'strict' and VUserinfo::isValidUsername($_POST['frontend_signin_username'])) ? $class_filter->clr_str($_POST['frontend_signin_username']) : ($cfg['username_format'] == 'loose' and VUserinfo::isValidUsername($_POST['frontend_signin_username'])) ? VUserinfo::clearString($_POST['frontend_signin_username']) : null;
        $signup_pack     = $cfg['paid_memberships'] == 1 ? $class_filter->clr_str($_POST['frontend_membership_type_sel']) : null;

        $_SESSION['signup_username'] = $signup_username;
        $_SESSION['signup_pack']     = $signup_pack != '' ? $signup_pack : null;
        return true;
    }
    /* signup form sessions reset */
    public function formSessionReset()
    {
        $_SESSION['signup_username'] = null;
        $_SESSION['signup_pack']     = null;
    }
}
