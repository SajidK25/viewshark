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
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.view');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.browse');
include_once $class_language->setLanguageFile('frontend', 'language.channel');
include_once $class_language->setLanguageFile('frontend', 'language.manage.channel');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.userpage');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('image_player,video_player,audio_player,document_player,paid_memberships,file_favorites,file_playlists,file_watchlist,file_responses,user_subscriptions,public_channels,channel_bulletins,event_map,user_friends,user_blocking,approve_friends,activity_logging,channel_comments,ucc_limit,comment_min_length,comment_max_length,channel_backgrounds,channel_bg_allowed_extensions,channel_bg_max_size,file_favorites,file_rating,file_comments,file_views,channel_views,guest_view_channel,file_promo,comment_emoji');
$guest_chk        = $_SESSION["USER_ID"] == '' ? VHref::guestPermissions('guest_view_channel', VHref::getKey("channels")) : null;
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;

$channel = new VChannel;
if (isset($_GET["a"])) {
    switch ($_GET["a"]) {
        case "postbulletin":
            echo $ht = VChannel::postBulletin();
            break;

        case "hideuseractivity":
            echo $ht = VChannel::hideActivity();
            break;

        case "cb-addfr": //add friends
        case "cb-remfr": //remove friend
        case "cb-block": //block friend
        case "cb-unblock": //unblock friend
            $notifier = new VNotify;
            echo $do  = $_POST ? VChannel::userActions($_GET["a"]) : null;
            break;
    }
}

if (isset($_GET["do"])) {
    $vview = new VView;

    switch ($_GET["do"]) {
        case "sub-option":
            echo $do_load = VView::subHtml('', 'channel');
            break;
        case "unsub-option":
            echo $do_load = VView::subHtml(1, 'channel');
            break;
        case "sub-continue":
            echo $do_load = VView::subContinue('channel');
            break;
        case "user-unsubscribe":
            echo $do_load = VSubscriber::unsub_request((int) $_POST["uf_vuid"]);
            break;

    }
}

if (!isset($_GET["s"]) and !isset($_GET["do"]) and !isset($_GET["a"]) and $error_message == '' and isset($_SESSION["q"])) {$_SESSION["q"] = null;}

$update_views = (!isset($_GET["s"]) and !isset($_GET["do"]) and !isset($_GET["a"]) and $error_message == '') ? VChannel::updateViews() : null;
$display_page = (!isset($_GET["a"]) and !isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_channel', $error_message, $notice_message) : null;
