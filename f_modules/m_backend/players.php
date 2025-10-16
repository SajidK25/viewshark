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

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('backend', 'language.players');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.import');

$error_message  = null;
$notice_message = ($_POST) ? VbeSettings::doSettings() : null;

$logged_in = VLogin::checkBackend(VHref::getKey("be_players"));
$cfg       = $class_database->getConfigurations('keep_entries_open');

$menu_entry = ($_GET["s"] != '') ? VMenuparse::sectionDisplay('backend', $class_filter->clr_str($_GET["s"])) : null;
$page       = ($_GET["s"] == '') ? $class_smarty->displayPage('backend', 'backend_tpl_players', $error_message, $notice_message) : null;
