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
include_once $class_language->setLanguageFile('frontend', 'language.home');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.userpage');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.view');

$error_message  = null;
$notice_message = null;

$cfg              = $class_database->getConfigurations('paid_memberships,backend_email,backend_username,channel_comments,file_counts,numeric_delimiter,channel_views,user_subscriptions,user_friends,user_blocking,internal_messaging,approve_friends,channel_promo');
$guest_chk        = $_SESSION["USER_ID"] == '' ? VHref::guestPermissions('guest_browse_channel', VHref::getKey("channels")) : null;
$membership_check = ($cfg["paid_memberships"] == 1 and $_SESSION["USER_ID"] > 0) ? VLogin::checkSubscription() : null;
$section          = VHref::getKey('channels');
$channels         = new VChannels;

if (isset($_GET["p"]) and (int) $_GET["p"] >= 0) {
//viewmode changer/loader
    $view_mode = (int) $_GET["m"];

    switch ($view_mode) {
        case "1":
        case "2":
            echo $html = VChannels::viewMode_loader($view_mode);

            break;
    }
}

if (isset($_GET["a"])) {
    switch ($_GET["a"]) {
        case "sub":
            $act = VChannels::chSubscribe();
            break;
        case "unsub":
            $act = VChannels::chSubscribe(1);
            break;
        case "cb-addfr":
        case "cb-remfr":
        case "cb-block":
        case "cb-unblock":
            $act = VChannels::contactActions($_GET["a"]);
            break;
        case "cb-msg":
            $act = VChannels::sessionMessageName();
            break;
        case "vm":
            echo $ct = VChannels::viewMode();
            break;

    }
}
if (isset($_GET["do"])) {
    if ($_GET["do"] == 'sub-option' or $_GET["do"] == 'unsub-option' or $_GET["do"] == 'sub-continue' or $_GET["do"] == 'user-sub' or $_GET["do"] == 'user-unsub') {
        $vview = new VView;
    }

    switch ($_GET["do"]) {
        case "subscribe":break;
        case "user-unsubscribe":
            echo $do_load = VSubscriber::unsub_request((int) $_POST["uf_vuid"]);
            break;
        case "sub-option":
            echo $do_load = VView::subHtml(0, 'home');
            break;
        case "unsub-option":
            echo $do_load = VView::subHtml(1, 'home');
            break;
        case "sub-continue":
            echo $do_load = VView::subContinue('channels');
            break;
    }
}

if (!isset($_GET["sort"]) and !isset($_GET["s"]) and !isset($_GET["a"]) and !isset($_GET["p"]) and !isset($_GET["do"])) {
    $smarty->assign('c_section', VHref::getKey("channels"));
    $_SESSION["q"] = null;
}

echo $display_page = (!isset($_GET["sort"]) and !isset($_GET["s"]) and !isset($_GET["a"]) and !isset($_GET["p"]) and !isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_channels', $error_message, $notice_message) : null;
