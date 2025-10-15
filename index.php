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

include_once $class_language->setLanguageFile('frontend', 'language.home');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.userpage');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message = null;
$cfg           = $class_database->getConfigurations('video_uploads,video_player,image_player,audio_player,document_player,public_channels,channel_bulletins,user_subscriptions,paid_memberships,file_counts,file_comments,channel_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_delete_method,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing,message_count,server_path_php,thumbs_width,thumbs_height,file_comment_spam');

if (isset($_GET['ap'])) {
    $_SESSION['ap'] = (int) $_GET['ap'];

    exit;
}

if (isset($_GET['m']) or isset($_GET['n'])) {
    if (isset($_GET['m'])) {
        $_SESSION['sbm'] = 1;
    } else {
        $_SESSION['sbm'] = 0;
    }

    exit;
}

$home   = new VHome;
$browse = new VBrowse;

if (isset($_GET['cfg'])) {
    $html = VHome::homeConfig();
    exit;
} elseif (isset($_GET['load'])) {
    $html = VHome::userNotifications();
    exit;
} elseif (isset($_GET['loadall'])) {
    $html = VHome::userNotifications(true);
    exit;
} elseif (isset($_GET['hide'])) {
    $html = VHome::hideNotifications();
    exit;
} elseif (isset($_GET['unhide'])) {
    $html = VHome::hideNotifications(true);
    exit;
} elseif (isset($_GET['sub'])) {
    exit;
} elseif (isset($_GET['categ'])) {
    exit;
} elseif (isset($_GET['rc'])) {
    exit;
} elseif (isset($_GET['a'])) {
    switch ($_GET["a"]) {
        case "color":
            $act = VGenerate::doThemeSwitch();
            break;
        case "sub":
            $act = VChannels::chSubscribe();
            break;
        case "unsub":
            $act = VChannels::chSubscribe(1);
            break;
        case "cb-favadd":
        case "cb-watchadd":
        default:
            $files = new VFiles;
            $ct    = isset($_GET["a"]) ? VFiles::doActions($_GET["a"]) : null;
            break;
    }
} elseif (isset($_GET['do'])) {
    if ($_GET["do"] == 'sub-option' or $_GET["do"] == 'unsub-option' or $_GET["do"] == 'sub-continue' or $_GET["do"] == 'user-sub' or $_GET["do"] == 'user-unsub') {
        $vview = new VView;
    }

    switch ($_GET["do"]) {
        case "act-load":
            echo $ct = VHome::getActivity();
            break;
        case "vm":
            echo $ct = VHome::viewMode();
            break;
        case "vm-ch":
            echo $ct = VChannels::viewMode(1);
            break;
        case "featured-list":
        case "channel-sort":
            echo $ct = $_GET["a"] == '' ? VHome::featuredFiles() : null;
            break;
        case "channel-browse":
        case "channel-list":
            echo $ct = $_GET["a"] == '' ? VHome::listFeaturedChannels() : null;
            break;
        case "subscribe":break;
        case "user-unsubscribe":
            echo $do_load = VSubscriber::unsub_request((int) $_POST["uf_vuid"]);
            break;
        case "sub-option":
            echo $do_load = VView::subHtml('', 'home');
            break;
        case "unsub-option":
            echo $do_load = VView::subHtml(1, 'home');
            break;
        case "sub-continue":
            echo $do_load = VView::subContinue('home');
            break;
        case "user-sub":
        case "user-unsub":
            break;

    }
}

$smarty->assign('c_section', VHref::getKey("index"));
$check_rd = (isset($_GET["feature"]) and $_GET["feature"] == 'channels' and $cfg["public_channels"] == 0) ? $class_redirect->to('', VHref::getKey('index')) : null;
$page     = (!isset($_GET["do"]) and !isset($_GET["a"])) ? $class_smarty->displayPage('frontend', 'tpl_index', $error_message) : null;
