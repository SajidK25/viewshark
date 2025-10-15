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
include_once 'f_core/f_classes/class.conversion.php';
include_once 'f_core/f_classes/class_thumb/ThumbLib.inc.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('live_uploads,image_player,video_player,audio_player,document_player,file_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing,file_comment_spam,file_thumb_change,affiliate_module');
$logged_in        = VLogin::checkFrontend(VHref::getKey("files"));
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;
$for              = isset($_GET["s"]) ? 'short' : (isset($_GET["l"]) ? 'live' : (isset($_GET["v"]) ? 'video' : (isset($_GET["i"]) ? 'image' : (isset($_GET["a"]) ? 'audio' : (isset($_GET["d"]) ? 'doc' : (isset($_GET["b"]) ? 'blog' : null))))));

$files = new VFiles;

$post_edit = (isset($_POST["files_text_file_title"]) and $_GET["do"] == '') ? VFiles::saveEdit() : null;

$smarty->assign('file_type', $for[0]);
$smarty->assign('file_key', $class_filter->clr_str($_GET[$for[0]]));

if (isset($_GET["do"])) {
    switch ($_GET["do"]) {
        case "thumb": //change thumbnail
            $smarty->assign('for', $for[0]);
            $smarty->assign('src', $class_database->singleFieldValue('db_' . $for . 'files', 'embed_src', 'file_key', $class_filter->clr_str($_GET[$for[0]])));
            $smarty->display('tpl_frontend/tpl_file/tpl_thumbnail.tpl');
            break;

        case "upload": //upload new thumbnail
            VFiles::thumbChange_upload();
            break;

        case "save": //save new thumbnail
            VFiles::thumbChange_save();
            break;

        case "cancel": //cancel changing thumbnail
            VFiles::thumbChange_cancel();
            break;

        case "insert": //insert media into blog
            VFiles::blog_insertMedia();
            break;

        case "find": //find media for inserting into blog
            VFiles::blog_findMedia();
            break;

        default:break;
    }
}
if ($_GET["fe"] == 1 and empty($_POST) and !isset($_GET["do"])) {
    $smarty->assign('c_section', VHref::getKey("files_edit"));
}

$display_page = ($_GET["fe"] == 1 and empty($_POST) and !isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_files_edit', $error_message, $notice_message) : null;
