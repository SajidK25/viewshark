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

$main_dir = realpath(dirname(__FILE__) . '/../../../');

set_include_path($main_dir);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.browse');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('file_counts,file_comments,channel_comments,file_comment_votes,file_promo,file_responses,file_rating,file_favorites,file_deleting,file_delete_method,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing,message_count,server_path_php,thumbs_width,thumbs_height');
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

$browse = new VBrowse;
$vfiles = new VFiles;

if ((int) $_GET["p"] >= 0 or (int) $_GET["vm"] >= 0) {
//viewmode changer/loader
    $view_mode = isset($_GET["m"]) ? (int) $_GET["m"] : (int) $_GET["vm"];

    switch ($view_mode) {
        case "1":
        case "2":
        case "3":
            $t                       = VBrowse::browseType();
            $t                       = $t == 'document' ? 'doc' : $t;
            $v                       = (int) $_GET["p"] == 1 ? 'pvm' : 'vm';
            $_SESSION[$t . "_" . $v] = $view_mode;

            echo $html = isset($_GET["m"]) ? VBrowse::viewMode_loader($view_mode) : null;

            break;
    }
}
if (isset($_GET["a"])) {
    switch ($_GET["a"]) {
//user menu actions
        case "cb-watchadd": //add to watchlist
            $cb_action = $cfg["file_watchlist"] == 1 ? VFiles::doActions($_GET["a"]) : null;

            break;
    }
}

if (!isset($_GET["s"]) and !isset($_GET["do"]) and !isset($_GET["p"]) and !isset($_GET["a"])) {
    $section_check = VBrowse::browseInit();
    $section       = VHref::getKey("browse");
    $smarty->assign('c_section', $section);
    $display_page = $class_smarty->displayPage('frontend', 'tpl_browse', $error_message, $notice_message);
}
