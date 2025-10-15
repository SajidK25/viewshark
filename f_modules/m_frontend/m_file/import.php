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

require_once 'Google/autoload.php';
require_once 'Google/Client.php';
require_once 'Google/Service/YouTube.php';

require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_YouTube');

include_once 'f_core/config.backend.php';
include_once 'f_core/config.core.php';
include_once 'f_core/f_classes/class.conversion.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('backend', 'language.import');

$error_message  = null;
$notice_message = null;
$logged_in      = VLogin::checkFrontend(VHref::getKey("import") . '?t=video');

$cfg              = $class_database->getConfigurations('file_approval,import_yt,import_dm,import_mc,import_vi,import_yt_channel_list,import_dm_user_list,import_mc_user_list,import_vi_user_list,import_yt_playback,youtube_api_key,import_mode,server_path_ffmpeg,server_path_php');
$membership_check = VLogin::checkSubscription();
$side             = (strstr($_SERVER['REQUEST_URI'], $backend_access_url) == true) ? 'backend' : 'frontend';

switch ($_GET["do"]) {
    case "list-feed": //list youtube video and channel feeds
    case "import-yt-video": //import youtube video feeds
    case "import-yt-channel": //import youtube channel feeds
        $ht = VImport::processVideoFeed();
        break;
    case "vimeo-feed": //list vimeo user feeds
    case "import-vimeo-feed": //import vimeo user feeds
        $ht = VImport::processVimeoFeed();
        break;
    case "dm-feed": //list dailymotion user feeds
    case "dm-video": //list dailymotion video feeds
    case "import-dm-video": //import dailymotion video feeds
    case "import-dm-user": //import dailymotion user feeds
        $ht = VImport::processDailymotionFeed();
        break;
    case "mc-feed": //list metacafe user feeds
    case "mc-video": //list metacafe video feeds
    case "import-mc-user": //import metacafe user feeds
    case "import-mc-video": //import metacafe video feeds
        $ht = VImport::processMetacafeFeed();
        break;
    case "video-embed": //embed single file
    case "video-find": //find single file
        $ht = VImport::processVideoEmbed();
        break;
    default:break;
}

$display_page = !isset($_GET["do"]) ? $class_smarty->displayPage('frontend', 'tpl_import', $error_message, $notice_message) : null;
