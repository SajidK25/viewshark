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
include_once $class_language->setLanguageFile('frontend', 'language.respond');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.view');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('paid_memberships,file_counts,file_comments,channel_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_delete_method,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing,message_count,server_path_php,thumbs_width,thumbs_height,user_blocking,file_flagging,file_email_sharing,file_permalink_sharing,fcc_limit,file_comment_min_length,file_comment_max_length,file_comment_spam');
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

switch ($_GET["do"]) {
    case "resp-clear": //delete response
        echo $do_load = VResponses::delResponse(1);
        break;
    case "cb-favadd": //file menu - add to favorites
    case "cb-watchadd": //file menu - add to watchlist
        $do_load = VFiles::doActions($_GET["do"]);
        break;
    default: //file menu - add to playlist
        if (substr($_GET["do"], 0, 8) == 'cb-label') {
            $do_load = VFiles::doActions($_GET["do"]);
        }
        break;
}

$display_page = ($_GET["s"] == '' and $_GET["do"] == '') ? $class_smarty->displayPage('frontend', 'tpl_respond_extra', $error_message, $notice_message) : null;
