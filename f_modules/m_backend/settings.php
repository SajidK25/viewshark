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
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('backend', 'language.files');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.conversion');
include_once $class_language->setLanguageFile('backend', 'language.import');
include_once $class_language->setLanguageFile('backend', 'language.servers');
include_once $class_language->setLanguageFile('backend', 'language.advertising');

$tpl            = false;
$error_message  = null;
$notice_message = null;
$notice_message = ($_POST and $_GET["s"] != 'backend-menu-entry2-sub5' and $_GET["s"] != 'backend-menu-entry1-sub10') ? VbeSettings::doSettings() : null;
$cfg[]          = $class_database->getConfigurations('video_player,audio_player,keep_entries_open,thumbs_nr,pause_video_transfer,pause_image_transfer,pause_audio_transfer,pause_doc_transfer,pause_short_transfer');

$q_result = $db->Execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");

if ($q_result) {
    while (!$q_result->EOF) {
        $cfg_name       = $q_result->fields["cfg_name"];
        $cfg_data       = $q_result->fields["cfg_data"];
        $cfg[$cfg_name] = $cfg_data;
        @$q_result->MoveNext();
    }
}

$logged_in = VLogin::checkBackend(VHref::getKey("be_settings"));
switch ($_GET["do"]) {
    case "global-sitemap":
        $sm = VbeSitemaps::doGlobalSitemap();
        break;
    case "video-sitemap":
        $sm = VbeSitemaps::doFileSitemap('video');
        break;
    case "short-sitemap":
        $sm = VbeSitemaps::doFileSitemap('short');
        break;
    case "image-sitemap":
        $sm = VbeSitemaps::doFileSitemap('image');
        break;
    case "backup-db":
        break;
    case "backup-db-remove":
        break;
    case "backup-file":
        break;
    case "backup-file-remove":
        break;
    case "tpl-edit-mail":
    case "tpl-edit-page":
    case "lang-fe":
    case "lang-be":
        echo $ht = VbeSettings::tplEdit($class_filter->clr_str($_GET["do"]));
        break;
    case "tpl-save":
        echo $tp = VbeSettings::tplSave();
        break;
    case "ftp-probe":
        $tpl = $_GET["do"];
        $ht  = VbeServers::ftpConn();
        break;
    case "s3-probe":
        $tpl = $_GET["do"];
        $ht  = VbeServers::S3Conn();
        break;
    case "cf-test":
        $tpl = $_GET["do"];
        $ht  = VbeServers::CFConn();
        break;
    case "cf-update":
        $tpl = $_GET["do"];
        $ht  = VbeServers::CFConn(1);
        break;
    case "cf-status":
    case "cf-del-origin":
    case "cf-del-dist":
    case "s3-delete":
        $tpl = $_GET["do"];
        $ht  = VbeServers::CFInfo();
        break;
    case "ftp-list":
        $tpl = $_GET["do"];
        $ht  = VbeServers::ftpList();
        break;
    case "xfer-log":
        $tpl = $_GET["do"];
        $ht  = VbeServers::xferLog();
        break;
    case "ftp-reset":
        $tpl = $_GET["do"];
        $ht  = VbeServers::ftpReset();
        break;
    case "reset-count":
        echo $ht = VbeServers::ftpResetCount();
        break;
    case "pause":
    case "resume":
        echo $ht = VbeServers::xferPause($_GET["do"]);
        break;
    case "categ-ads":
        echo $ht = VbeCategories::manageAds();
        break;
    case "categ-banners":
        echo $ht = VbeCategories::manageAds(1);
        break;
    case "ad-update":
        echo $ht = VbeCategories::updateAds();
        break;
    case "banner-update":
        echo $ht = VbeCategories::updateAds(1);
        break;
    case "categ-lang":
        $ht = VbeCategories::saveCategLang();
        break;
}

if (isset($_GET["s"])) {
    $type = false;

    switch ($_GET["s"]) {
        case "backend-menu-entry6-sub1":
        case "backend-menu-entry6-sub2":
        case "backend-menu-entry6-sub3":
        case "backend-menu-entry6-sub4":
        case "backend-menu-entry6-sub5":
        case "backend-menu-entry6-sub6":
        case "backend-menu-entry6-sub7":$files = new VFiles;
            break;
        case "backend-menu-entry3-sub14":$type = 'video';
            break;
        case "backend-menu-entry3-sub15":$type = 'image';
            break;
        case "backend-menu-entry3-sub16":$type = 'audio';
            break;
        case "backend-menu-entry3-sub17":$type = 'doc';
            break;
        case "backend-menu-entry3-sub19":$type = 'short';
            break;
    }

    if ($type) {
        if ($_POST and isset($_GET["do"]) and $_GET["do"] == 'autocomplete') {
            $html = VGenerate::processAutoComplete('xfer_list', $type);

            return;
        } elseif ($_POST and isset($_GET["autocomplete"]) and $_GET["do"] == 'add') {
            $html = VGenerate::processAutoComplete('xfer_new', $type);

            return;
        }
    }
}

if ($tpl) {
    $popup     = VbeServers::popupTpl($tpl);
    echo $html = str_replace('##CONTENT##', $ht, $popup);
}

$menu_entry = ($_GET["s"] != '' and $_GET["do"] != 'categ-ads' and $_GET["do"] != 'ad-update' and $_GET["do"] != 'banner-update' and $_GET["do"] != 'categ-lang' and $_GET["do"] != 'categ-banners' and $_GET["do"] != 'cf-update' and $_GET["do"] != 's3-delete' and $_GET["do"] != 'cf-del-dist' and $_GET["do"] != 'cf-del-origin' and $_GET["do"] != 'cf-status' and $_GET["do"] != 'cf-test' and $_GET["do"] != 's3-probe' and $_GET["do"] != 'reset-count' and $_GET["do"] != 'ftp-reset' and $_GET["do"] != 'xfer-log' and $_GET["do"] != 'ftp-probe' and $_GET["do"] != 'ftp-list' and $_GET["do"] != 'tpl-edit-mail' and $_GET["do"] != 'tpl-edit-page' and $_GET["do"] != 'tpl-save' and $_GET["do"] != 'lang-fe' and $_GET["do"] != 'lang-be') ? VMenuparse::sectionDisplay('backend', $class_filter->clr_str($_GET["s"])) : null;
$page       = ($_GET["s"] == '') ? $class_smarty->displayPage('backend', 'backend_tpl_settings', $error_message, $notice_message) : null;
