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

include_once 'f_core/config.core.php';

$cfg = $class_database->getConfigurations('email_change_captcha_level,signup_captcha_level,frontend_username_recovery_captcha_level,frontend_password_recovery_captcha_level,backend_username_recovery_captcha_level,backend_password_recovery_captcha_level');

switch ($_GET["extra"]) {
    case '':$_c = new VCaptcha('', $cfg["signup_captcha_level"]);
        break;
    case '1':$_c = new VCaptcha('recover_left', $cfg["frontend_password_recovery_captcha_level"]);
        break;
    case '2':$_c = new VCaptcha('recover_right', $cfg["frontend_username_recovery_captcha_level"]);
        break;
    case '3':$_c = new VCaptcha('recover_left', $cfg["backend_password_recovery_captcha_level"]);
        break;
    case '4':$_c = new VCaptcha('recover_right', $cfg["backend_username_recovery_captcha_level"]);
        break;
    case '5':$_c = new VCaptcha('change_email', $cfg["email_change_captcha_level"]);
        break;
}
