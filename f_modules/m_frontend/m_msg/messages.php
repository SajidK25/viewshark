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

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.messages');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.userpage');

$error_message  = null;
$notice_message = null;
$notifier       = new VNotify;

$cfg              = $class_database->getConfigurations('backend_username,list_reserved_users,message_count,internal_messaging,allow_self_messaging,allow_multi_messaging,multi_messaging_limit,message_attachments,custom_labels,user_friends,user_blocking,approve_friends,channel_comments,file_comments,channel_comments,ucc_limit,comment_min_length,comment_max_length,file_responses,file_favorites');
$_s               = isset($_GET["s"]) ? $class_filter->clr_str($_GET["s"]) : null;
$_ss              = substr($_s, 0, 19);
$section_access   = ($cfg["internal_messaging"] == 0 and $cfg["user_friends"] == 0 and $cfg["user_blocking"] == 0 and $cfg["channel_comments"] == 0) ? $class_redirect->to('', $cfg["main_url"] . '/' . VHref::getKey('index')) : null;
$section_check    = $cfg["internal_messaging"] == 1 ? VHref::getKey('messages') : (($cfg["internal_messaging"] == 0 and ($cfg["user_friends"] == 1 or $cfg["user_blocking"] == 1)) ? VHref::getKey('contacts') : VHref::getKey('comments'));
$logged_in        = VLogin::checkFrontend($section_check);
$membership_check = VLogin::checkSubscription();

$url           = explode("/", $_SERVER['REQUEST_URI']);
$show_contacts = in_array(VHref::getKey("contacts"), $url) ? $smarty->assign('show_contacts', 1) : null;

$files = new VFiles;

switch ($_ss) {
    case "message-menu-entry2":
        $smarty->assign('section_title', $language["msg.entry.inbox"]);
        $smarty->assign('section_icon', 'icon-envelope');
        break;
    case "message-menu-entry4":
        $smarty->assign('section_title', $language["msg.entry.fr.invite"]);
        $smarty->assign('section_icon', 'icon-notebook');
        break;
    case "message-menu-entry5":
        $smarty->assign('section_title', $language["msg.entry.sent"]);
        $smarty->assign('section_icon', 'icon-envelope');
        break;
    case "message-menu-entry6":
        $smarty->assign('section_title', $language["msg.entry.spam"]);
        $smarty->assign('section_icon', 'icon-spam');
        break;
    case "message-menu-entry7":
        switch ($_s) {
            case "message-menu-entry7-sub1":
                $smarty->assign('section_title', $language["msg.entry.friends"]);
                $smarty->assign('section_icon', 'icon-users5');
                break;
            case "message-menu-entry7-sub2":
                $smarty->assign('section_title', $language["msg.entry.blocked.users"]);
                $smarty->assign('section_icon', 'icon-blocked');
                break;
            default:
                $smarty->assign('section_title', $language["msg.entry.adr.book"]);
                $smarty->assign('section_icon', 'icon-address-book');
                break;
        }
        break;
}
switch (VMessages::currentMenuEntry($_s)) {
    case "message-menu-entry2":
    case "message-menu-entry5":
    case "message-menu-entry6":
    default:
        if ($_s != '') {
            $smarty->assign('page_display', 'tpl_messages');
            if ($_GET["do"] != 'autocomplete' and $_GET["do"] != 'set-bl' and $_GET["do"] != 'read' and $_GET["do"] != 'label' and $_GET["do"] != 'delete_label' and $_GET["do"] != 'menu' and $_GET["do"] != 'rename' and $_GET["do"] != 'contact' and $_GET["do"] != 'ctedit') {$smarty->display('tpl_frontend/tpl_msg/tpl_messages.tpl');}
        }
        break;
}
switch ($_GET["do"]) {
    case "autocomplete": //username autocomplete
        $autocomplete = VGenerate::processAutoComplete('private_message');
        break;
    case "cb-label-add": //add to label
    case "cb-label-clear": //remove from label
    case "cb-addfr": //add friends (multiple)
    case "cb-remfr": //remove friend (single)
    case "cb-sendmsg": //send message
    case "cb-block": //(cb) block
    case "block": //(single) block
    case "cb-unblock": //unblock
    case "cb-delete": //remove entry
        $cb_action = $_POST ? VContacts::cbActions($_GET["do"]) : null;
        break;
    case "set-bl": //set block options
        $set = $_POST ? VContacts::setBlockCfg() : null;
        break;
    case "read": //mark as read
        $read = VMessages::readMessage();
        break;
    case "contact": //add new contact
        $error_message = VContacts::addNew();
        break;
    case "ctedit": //edit contact
        $error_message = VContacts::ctEdit();
        break;
    case "compose": //compose
        $compose = VMessages::composeMessage();
        break;
    case "label": //new label
        $new_label = VMessages::addNewLabel();
        break;
    case "rename": //rename label
        $rename = VMessages::labelRename();
        break;
    case "delete_label": //delete label
        $delete = VMessages::deleteLabel();
        break;
}

if (!isset($_GET["s"]) and !isset($_GET["do"])) {
    $smarty->assign('c_section', VHref::getKey("messages"));
}

$display_page = ($_s == '' and !isset($_GET["do"])) ? $class_smarty->displayPage('frontend', 'tpl_messages', $error_message, $notice_message) : null;
