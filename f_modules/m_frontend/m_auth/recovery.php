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
include_once $class_language->setLanguageFile('frontend', 'language.recovery');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.signin');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$error_message  = null;
$notice_message = null;
$notifier       = new VNotify;

$cfg           = $class_database->getConfigurations('activity_logging,password_recovery_captcha,username_recovery_captcha,recovery_link_lifetime,allow_username_recovery,allow_password_recovery,backend_username_recovery,backend_password_recovery,backend_username_recovery_captcha,backend_password_recovery_captcha,backend_username,backend_email,noreply_email,recaptcha_site_key,recaptcha_secret_key');
$section_check = ($class_smarty->backendSectionCheck() == 1) ? 'backend' : 'frontend';
$logged_in     = $section_check == 'frontend' ? VLogin::isLoggedIn('fe') : ($section_check == 'backend' ? VLogin::isLoggedIn('be') : null);

$rec_username  = $_POST["rec_username"] != '' ? $class_filter->clr_str($_POST["rec_username"]) : null;
$rec_email     = $_POST["rec_email"] != '' ? $class_filter->clr_str($_POST["rec_email"]) : null;
$left_captcha  = (($cfg["password_recovery_captcha"] == 1 or $cfg["backend_password_recovery_captcha"] == 1) and $_POST["g-recaptcha-response"] != '') ? $class_filter->clr_str($_POST["g-recaptcha-response"]) : null;
$right_captcha = (($cfg["username_recovery_captcha"] == 1 or $cfg["backend_username_recovery_captcha"] == 1) and $_POST["g-recaptcha-response"] != '') ? $class_filter->clr_str($_POST["g-recaptcha-response"]) : null;

$pass_rec_cond    = ($section_check == 'backend') ? $cfg["backend_password_recovery"] : $cfg["allow_password_recovery"];
$pass_rec_captcha = ($section_check == 'backend') ? $cfg["backend_password_recovery_captcha"] : $cfg["password_recovery_captcha"];
$user_rec_cond    = ($section_check == 'backend') ? $cfg["backend_username_recovery"] : $cfg["allow_username_recovery"];
$user_rec_captcha = ($section_check == 'backend') ? $cfg["backend_username_recovery_captcha"] : $cfg["username_recovery_captcha"];

$siteKey = $cfg['recaptcha_site_key'];
$secret  = $cfg['recaptcha_secret_key'];

switch (intval($_GET["t"])) {
    case '':break;
    case '0':break;
    case '1':
        if ($pass_rec_cond == 1) {
            switch ($pass_rec_captcha) {
                case '0':$password_recovery = (!VUserinfo::existingUsername($rec_username, $section_check)) ? $notifier->showNotice('error', $language["notif.error.invalid.request"]) : (VUserinfo::existingUsername($rec_username, $section_check)) ? VNotify::queInit('password_recovery', array(VUserinfo::getUserEmail(VUserinfo::getUserID($rec_username))), $section_check) . VNotify::showNotice('confirmation', $language["notif.success.request"], 'x_err') : null;
                    break;
                case '1':
                    if (!VUserinfo::existingUsername($rec_username, $section_check) or $left_captcha == '') {
                        $notifier->showNotice('error', $language["notif.error.invalid.request"]);
                    } elseif (VUserinfo::existingUsername($rec_username, $section_check)) {
                        $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\CurlPost());
                        $resp      = $recaptcha->verify($left_captcha, $_SERVER[REM_ADDR]);

                        if ($resp->isSuccess()) {
                            VNotify::queInit('password_recovery', array(VUserinfo::getUserEmail(VUserinfo::getUserID($rec_username))), $section_check) . VNotify::showNotice('confirmation', $language["notif.success.request"], 'x_err');
                        } else {
                            foreach ($resp->getErrorCodes() as $code) {
                                $notifier->showNotice('error', $code);
                            }
                        }
                    }
                    break;
            }
            $log = ($cfg["activity_logging"] == 1 and $action = new VActivity($user_id, 0)) ? $action->addTo('log_urecovery') : null;
        }
        break;
    case '2':
        if ($user_rec_cond == 1) {
            switch ($user_rec_captcha) {
                case '0':$username_recovery = (!VUserinfo::existingEmail($rec_email, $section_check)) ? $notifier->showNotice('error', $language["notif.error.invalid.request"], 'r_err') : (VUserinfo::existingEmail($rec_email, $section_check)) ? VNotify::queInit('username_recovery', array($rec_email), $section_check) . VNotify::showNotice('confirmation', $language["notif.success.request"], 'r_err') : null;
                    break;
                case '1':
                    if (!VUserinfo::existingEmail($rec_email, $section_check) or $right_captcha == '') {
                        $notifier->showNotice('error', $language["notif.error.invalid.request"], 'r_err');
                    } elseif (VUserinfo::existingEmail($rec_email, $section_check)) {
                        $recaptcha = new \ReCaptcha\ReCaptcha($secret, new \ReCaptcha\RequestMethod\CurlPost());
                        $resp      = $recaptcha->verify($right_captcha, $_SERVER[REM_ADDR]);

                        if ($resp->isSuccess()) {
                            VNotify::queInit('username_recovery', array($rec_email), $section_check) . VNotify::showNotice('confirmation', $language["notif.success.request"], 'r_err');
                        } else {
                            foreach ($resp->getErrorCodes() as $code) {
                                $notifier->showNotice('error', $code);
                            }
                        }
                    }
                    break;
            }
            $log = ($cfg["activity_logging"] == 1 and $action = new VActivity($user_id, 0)) ? $action->addTo('log_precovery') : null;
        }
        break;
    default:break;
}
if (intval($_POST["reset_password"] == 1) or ($_GET["s"] != '' and $_GET["id"] != '')) {
    $error_message  = ($_GET["s"] != '' and $_GET["id"] != '') ? VRecovery::validCheck($section_check) : null;
    $error_message  = (intval($_POST["reset_password"] == 1) and $error_message == '') ? VRecovery::processForm($section_check) : $error_message;
    $notice_message = ((intval($_POST["reset_password"] == 1)) and ($error_message == '' and VRecovery::doPasswordReset($section_check))) ? $language["recovery.forgot.password.confirm"] : null;
}

$u                 = VUserinfo::getUserInfo(VRecovery::getRecoveryID($_GET["s"]));
$recovery_username = ($_GET["s"] != '' and $_GET["id"] != '' and $section_check == 'frontend') ? $smarty->assign('fe_recovery_username', $u["uname"]) : ($_GET["s"] != '' and $_GET["id"] != '' and $section_check == 'backend') ? $smarty->assign('recovery_username', $cfg["backend_username"]) : null;
$page_display      = ($_GET["t"] == '') ? $class_smarty->displayPage($section_check, ($section_check == 'frontend' ? 'tpl_recovery' : 'backend_tpl_recovery'), $error_message, $notice_message) : null;
