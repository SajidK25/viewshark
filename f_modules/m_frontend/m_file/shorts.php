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
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.userpage');
include_once $class_language->setLanguageFile('frontend', 'language.respond');
include_once $class_language->setLanguageFile('frontend', 'language.view');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('image_player,video_player,audio_player,document_player,paid_memberships,file_downloads,file_counts,file_comments,channel_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_delete_method,file_privacy,file_playlists,file_views,channel_views,file_history,file_watchlist,file_embedding,file_social_sharing,message_count,server_path_php,thumbs_width,thumbs_height,user_blocking,file_flagging,file_email_sharing,file_permalink_sharing,fcc_limit,file_comment_min_length,file_comment_max_length,file_comment_spam,file_download_s1,file_download_s2,file_download_s3,file_download_s4,file_download_reg,user_subscriptions,backend_email,guest_view_video,guest_view_image,guest_view_audio,guest_view_doc,guest_view_blog,affiliate_tracking_id,affiliate_module,conversion_video_previews,conversion_audio_previews,conversion_doc_previews,conversion_image_previews,conversion_live_previews,guest_view_live,guest_view_short');
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;
$is_shorts        = 1;

if ($_GET["g"] == 1) {
//for seo mod_rewrite rules
    include 'f_core/config.href.php';
    $section = $href["watch"];
}

$view   = new VView();
$vfiles = new VFiles();

if ($cfg["file_responses"] == 1) {
    $vresponses = new VResponses();
}

switch ($_GET["do"]) {
    case "side-column":
    case "recommend-more":
        echo $do_load = VView::sideColumn(true);
    case "resp-clear": //delete response
        echo $do_load = VResponses::delResponse();
        break;
    case "comm-suspend": //suspend comments
    case "comm-approve": //approve comments
    case "comm-spam": //report spam
    case "comm-delete": //delete comments
    case "comm-like": //like
    case "comm-dislike": //dislike
    case "comm-edit": //edit
    case "comm-pin": //pin
    case "comm-unpin": //unpin
        echo $do_load = VComments::commentActions($_GET["do"]);
        break;
    case "comm-sort": //load/reload after sorting
    case "comm-load": //load/reload after posting, etc
        echo $do_load = VComments::browseComment();
        break;
    case "comm-post": //post comments
    case "comm-reply": //reply comments
        echo $do_load = VComments::postComment();
        break;
    case "response-load": //load responses
        echo $do_load = '';
        break;
    case "user-files": //load user files
        echo $do_load = VView::loadUserFiles();
        break;
    case "load-more": //load more files
        echo $do_load = VView::loadMoreFiles();
        break;
    case "featured-files": //load featured files
        echo $do_load = VView::loadFeaturedFiles();
        break;
    case "user-sub": //subscribe to file owner
        echo $do_load = VView::chSubscribe('', (int) $_POST["uf_vuid"]);
        break;
    case "user-unsub": //unsubscribe
        echo $do_load = VView::chSubscribe(1, (int) $_POST["uf_vuid"]);
        break;
    case "user-follow": //follow file owner
        echo $do_load = VView::chSubscribe('', (int) $_POST["uf_vuid"], true);
        break;
    case "user-unfollow": //unfollow
        echo $do_load = VView::chSubscribe(1, (int) $_POST["uf_vuid"], true);
        break;
    case "user-unsubscribe":
        echo $do_load = VSubscriber::unsub_request((int) $_POST["uf_vuid"]);
        break;
    case "unsub-option": //unsub html
        echo $do_load = VView::subHtml(1);
        break;
    case "sub-option": //sub html
        echo $do_load = VView::subHtml();
        break;
    case "sub-continue": //sub paypal
        echo $do_load = VView::subContinue();
        break;
    case "ipn": //verify paypal
        echo $do_load = VView::verifyPP();
        break;
    case "cb-favadd": //file menu - add to favorites
    case "cb-favclear": //file menu - add to favorites
    case "cb-labeladd": //file menu - add to playlists
    case "cb-labelclear": //file menu - clear playlists
    case "cb-watchadd": //file menu - add to watchlist
    case "cb-watchclear": //file menu - add to watchlist
        $do_load = VFiles::doActions($_GET["do"]);
        break;
    case "file-share": //share file via email
        echo $do_load = VFiles::plMail();
        break;
    case "file-like": //like file
    case "file-dislike": //like file
        echo $do_load = VView::likeAction($_GET["do"]);
        break;
    default: //file flagging
        $do_load = (substr($_GET["do"], 0, 9) == 'file-flag') ? VView::fileFlagging(intval(substr($_GET["do"], 9))) : null;
        break;
}

if (!isset($_GET["do"])) {
    $mob       = $smarty->assign('is_mobile', VHref::isAnyMobile());
    $file_info = VView::getFileInfo();
    $smarty->assign('c_section', VHref::getKey("watch"));
    $smarty->assign('tpl_section', "tpl_shorts");
    $display_page = $class_smarty->displayPage('frontend', 'tpl_view', $error_message, $notice_message);
}
