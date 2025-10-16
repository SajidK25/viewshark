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

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.signin');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');

$error_message  = null;
$notice_message = null;
$cfg            = $class_database->getConfigurations('account_email_verification');

if ($error_message == '' and isset($_GET["sid"]) and isset($_GET["uid"])) {
    $error_message  = VRecovery::validCheck('frontend', 'verification');
    $notice_message = ($error_message == '' and VSignup::verifyAccount()) ? $language["notif.success.verified"] : null;
} elseif ($cfg["account_email_verification"] == 0) {
    $error_message = 'tpl_error_max';
}

$class_smarty->displayPage('frontend', 'tpl_verify', $error_message, $notice_message);
