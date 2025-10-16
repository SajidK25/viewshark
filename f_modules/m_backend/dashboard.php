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
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('backend', 'language.dashboard');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.advertising');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.account');

$error_message  = null;
$notice_message = null;
$cfg[]          = $class_database->getConfigurations('video_player,audio_player');
$logged_in      = VLogin::checkBackend(VHref::getKey("be_dashboard"));
$dash           = new VbeDashboard();

if (isset($_GET["s"])) {
    switch ($_GET["s"]) {
        case "notif":
            echo $notif = $dash->getNotifications();
            break;
        case "new":
            echo $new = $dash->getNewNotifications();
            break;
    }

    return;
}

$stats = $dash->assignStats();
$page  = !isset($_GET["s"]) ? $class_smarty->displayPage('backend', 'backend_tpl_dashboard', $error_message, $notice_message) : null;
