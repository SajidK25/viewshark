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
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.messages');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');

$error_message  = null;
$notice_message = null;

$error_message = (isset($_GET["sid"]) and isset($_GET["uid"])) ? VContacts::inviteCheck() : null;
$display_page  = (isset($_GET["sid"]) and isset($_GET["uid"]) and !isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_friend_action', $error_message, $notice_message) : null;
