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

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.upload');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.view');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');

$error_message    = null;
$notice_message   = null;
$cfg              = $class_database->getConfigurations('video_player,image_player,audio_player,doc_player,file_counts,file_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing');
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

if ($cfg["file_playlists"] == 0) {
    header("Location: " . $cfg["main_url"]);
    exit;
}

$files = new VFiles;

switch ($_GET["a"]) {
    case "save":
        $save = VFiles::savePlaylist();
        break;
    case "pl-mail":
        $mail = VFiles::plMail();
        break;
    case "cb-favadd": //add to fav
    case "cb-watchadd": //add to watchlist
        $cb_action = VFiles::doActions($_GET["a"]);
        break;
    default: //add/clear from pl
        $cb_action = (substr($_GET["a"], 0, 8) == 'cb-label') ? VFiles::doActions($_GET["a"]) : null;
        break;
}

$smarty->assign('c_section', VHref::getKey("playlist"));
$display_page = ((strlen($_GET["v"]) >= 10 or strlen($_GET["s"]) >= 10 or strlen($_GET["l"]) >= 10 or strlen($_GET["i"]) >= 10 or strlen($_GET["a"]) >= 10 or strlen($_GET["d"]) >= 10 or strlen($_GET["b"]) >= 10) and !$_POST) ? $class_smarty->displayPage('frontend', 'tpl_playlist', $error_message, $notice_message) : null;
