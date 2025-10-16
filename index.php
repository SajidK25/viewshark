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

// Initialize security class
$security = VSecurity::getInstance();

include_once $class_language->setLanguageFile('frontend', 'language.home');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.userpage');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message = null;
$cfg           = $class_database->getConfigurations('video_uploads,video_player,image_player,audio_player,document_player,public_channels,channel_bulletins,user_subscriptions,paid_memberships,file_counts,file_comments,channel_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_delete_method,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing,message_count,server_path_php,thumbs_width,thumbs_height,file_comment_spam');

// Secure parameter handling
$ap = VSecurity::getParam('ap', 'int');
if ($ap !== null) {
    $_SESSION['ap'] = $ap;
    exit;
}

// Secure parameter handling for m/n
$m = VSecurity::getParam('m', 'string');
$n = VSecurity::getParam('n', 'string');
if ($m !== null || $n !== null) {
    $_SESSION['sbm'] = ($m !== null) ? 1 : 0;
    exit;
}

$home   = new VHome;
$browse = new VBrowse;

// Secure parameter handling with validation
$cfg_param = VSecurity::getParam('cfg', 'alphanum');
$load_param = VSecurity::getParam('load', 'alphanum');
$loadall_param = VSecurity::getParam('loadall', 'alphanum');
$hide_param = VSecurity::getParam('hide', 'alphanum');
$unhide_param = VSecurity::getParam('unhide', 'alphanum');
$sub_param = VSecurity::getParam('sub', 'alphanum');
$categ_param = VSecurity::getParam('categ', 'alphanum');
$rc_param = VSecurity::getParam('rc', 'alphanum');
$a_param = VSecurity::getParam('a', 'alphanum');
$do_param = VSecurity::getParam('do', 'slug');

if ($cfg_param !== null) {
    $html = VHome::homeConfig();
    exit;
} elseif ($load_param !== null) {
    $html = VHome::userNotifications();
    exit;
} elseif ($loadall_param !== null) {
    $html = VHome::userNotifications(true);
    exit;
} elseif ($hide_param !== null) {
    $html = VHome::hideNotifications();
    exit;
} elseif ($unhide_param !== null) {
    $html = VHome::hideNotifications(true);
    exit;
} elseif ($sub_param !== null) {
    exit;
} elseif ($categ_param !== null) {
    exit;
} elseif ($rc_param !== null) {
    exit;
} elseif ($a_param !== null) {
    // Whitelist allowed actions
    $allowedActions = ['color', 'sub', 'unsub', 'cb-favadd', 'cb-watchadd'];
    
    if (in_array($a_param, $allowedActions)) {
        switch ($a_param) {
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
                $files = new VFiles;
                $ct = VFiles::doActions($a_param);
                break;
        }
    }
} elseif ($do_param !== null) {
    // Whitelist allowed do actions
    $allowedDoActions = [
        'sub-option', 'unsub-option', 'sub-continue', 'user-sub', 'user-unsub',
        'act-load', 'vm', 'vm-ch', 'featured-list', 'channel-sort', 
        'channel-browse', 'channel-list', 'subscribe', 'user-unsubscribe'
    ];
    
    if (in_array($do_param, $allowedDoActions)) {
        if (in_array($do_param, ['sub-option', 'unsub-option', 'sub-continue', 'user-sub', 'user-unsub'])) {
            $vview = new VView;
        }

        switch ($do_param) {
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
                $a_check = VSecurity::getParam('a', 'string', '');
                echo $ct = ($a_check === '') ? VHome::featuredFiles() : null;
                break;
            case "channel-browse":
            case "channel-list":
                $a_check = VSecurity::getParam('a', 'string', '');
                echo $ct = ($a_check === '') ? VHome::listFeaturedChannels() : null;
                break;
            case "subscribe":
                break;
            case "user-unsubscribe":
                // Validate CSRF token for POST actions
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && VSecurity::validateCSRFFromPost('unsubscribe')) {
                    $uf_vuid = VSecurity::postParam('uf_vuid', 'int', 0);
                    if ($uf_vuid > 0) {
                        echo $do_load = VSubscriber::unsub_request($uf_vuid);
                    }
                }
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
}

$smarty->assign('c_section', VHref::getKey("index"));
// Secure feature parameter check
$feature_param = VSecurity::getParam('feature', 'alpha');
$check_rd = ($feature_param === 'channels' && $cfg["public_channels"] == 0) ? $class_redirect->to('', VHref::getKey('index')) : null;
$page = ($do_param === null && $a_param === null) ? $class_smarty->displayPage('frontend', 'tpl_index', $error_message) : null;
