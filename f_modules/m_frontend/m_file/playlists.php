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

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.home');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');

header("Location: " . $cfg["main_url"] . '/' . VHref::getKey("videos"));
exit;

$error_message    = null;
$notice_message   = null;
$cfg              = $class_database->getConfigurations('video_player,image_player,audio_player,doc_player,file_counts,file_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing');
$guest_chk        = $_SESSION["USER_ID"] == '' ? VHref::guestPermissions('guest_browse_playlist', VHref::getKey("playlists")) : null;
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

$playlist = new VPlaylist;
$files    = new VFiles;

$smarty->assign('c_section', VHref::getKey("files"));

echo $display_page = (!isset($_GET["sort"]) and !isset($_GET["v"])) ? $class_smarty->displayPage('frontend', 'tpl_playlists', $error_message, $notice_message) : VPlaylist::listPlaylists();
