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
define('_ISADMIN', true);

include_once 'f_core/config.core.php';
include_once 'f_core/f_classes/class.conversion.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.upload');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('backend', 'language.files');
include_once $class_language->setLanguageFile('backend', 'language.advertising');
include_once $class_language->setLanguageFile('backend', 'language.import');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');

$error_message  = null;
$notice_message = null;
$logged_in      = VLogin::checkBackend(VHref::getKey("be_files"));
$cfg            = $class_database->getConfigurations('video_file_types,video_player,image_player,audio_player,document_player,paid_memberships,file_counts,file_comments,channel_comments,file_comment_votes,file_responses,file_rating,file_favorites,file_deleting,file_delete_method,file_privacy,file_playlists,file_views,file_history,file_watchlist,file_embedding,file_social_sharing,message_count,server_path_php,thumbs_width,thumbs_height,file_comment_spam,conversion_live_previews,conversion_video_previews,conversion_audio_previews,conversion_image_previews,conversion_doc_previews,short_file_types');

if ($cfg["video_player"] == "jw" or $cfg["audio_player"] == "jw") {
    $jw  = $db->execute("SELECT `db_config` FROM `db_fileplayers` WHERE `db_name`='jw_local' LIMIT 1;");
    $_jw = unserialize($jw->fields["db_config"]);

    $smarty->assign('jw_license_key', $_jw["jw_license_key"]);
}

$files = new VFiles;

$menu_entry = ((isset($_GET["s"]) and $_GET["s"] !== '1') and $_GET["do"] != 'ad-update' and $_GET["do"] != 'find' and $_GET["do"] != 'new-broadcast' and $_GET["do"] != 'new-blog' and $_GET["do"] != 'new-blog-autocomplete' and $_GET["do"] != 'blogload' and $_GET["do"] != 'blogsave' and $_GET["do"] != 'insert' and $_GET["do"] != 'autocomplete' and $_GET["do"] != 'server-paths' and $_GET["do"] != 'subs-update' and $_GET["do"] != 'banner-update' and $_GET["do"] != 'manage-ads' and $_GET["do"] != 'manage-subs' and $_GET["do"] != 'manage-banners' and $_GET["do"] != 'sub-mail' and $_GET["do"] != 'confirm-approve' and $_GET["do"] != 'new-upload' and $_GET["do"] != 'thumb-reset' and $_GET["do"] != 'preview-reset') ? VMenuparse::sectionDisplay('backend', $class_filter->clr_str($_GET["s"])) : null;
switch ($_GET["do"]) {
    case "sub-mail":
        echo $ht = VbeFiles::subscriptionMailer();
        break;
    case "confirm-approve":
        echo $ht = VbeFiles::confirmApprove();
        break;
    case "thumb-reset":
        echo $ht = VbeFiles::thumbReset($class_filter->clr_str($_GET["f"]));
        break;
    case "preview-reset":
        echo $ht = VbeFiles::thumbReset($class_filter->clr_str($_GET["f"]), 2);
        break;
    case "manage-ads":
        echo $ht = $cfg["video_player"] == 'vjs' ? VbeFiles::VJSadsManager($class_filter->clr_str($_GET["file_key"])) : ($cfg["video_player"] == 'jw' ? VbeFiles::JWadsManager($class_filter->clr_str($_GET["file_key"])) : VbeFiles::FPadsManager($class_filter->clr_str($_GET["file_key"])));
        break;
    case "manage-subs":
        echo $ht = VbeFiles::subsManager($class_filter->clr_str($_GET["file_key"]));
        break;
    case "manage-banners":
        echo $ht = VbeFiles::bannerManager($class_filter->clr_str($_GET["file_key"]));
        break;
    case "ad-update":
        echo $ht = VbeFiles::adUpdate($class_filter->clr_str($_GET["file_key"]));
        break;
    case "subs-update":
        echo $ht = VbeFiles::subsUpdate($class_filter->clr_str($_GET["file_key"]));
        break;
    case "banner-update":
        echo $ht = VbeFiles::adUpdate($class_filter->clr_str($_GET["file_key"]), 1);
        break;
    case "server-paths":
        echo $ht = VbeFiles::serverPaths();
        break;
    case "conversion-log":
        echo $ht = VbeFiles::conversionLog();
        break;
    case "autocomplete":
        $html = VGenerate::processAutoComplete('files');
        break;
    case "insert": //insert media into blog
        VbeFiles::blog_insertMedia();
        break;
    case "find": //find media for inserting into blog
        VbeFiles::blog_findMedia();
        break;
    case "blogload": //load blog html content
        VbeFiles::blog_loadContent();
        break;
    case "blogsave": // save blog html content
        VbeFiles::blog_saveContent();
        break;
    case "new-blog": //add new blog
    case "new-broadcast": //add new broadcast
        VbeFiles::newBlog();
        break;
    case "new-blog-autocomplete": //add new blog, autocomplete
        VGenerate::processAutoComplete('new_blog');
        break;
}

$player_entry = ($_GET["p"] != '') ? VbeFiles::playerLoader() : null;

$page = ($_GET["s"] == '' and $_GET["p"] == '' and $_GET["do"] != 'insert' and $_GET["do"] != 'find' and $_GET["do"] != 'new-broadcast' and $_GET["do"] != 'new-blog' and $_GET["do"] != 'new-blog-autocomplete' and $_GET["do"] != 'blogload' and $_GET["do"] != 'blogsave' and $_GET["do"] != 'autocomplete' and $_GET["do"] != 'server-paths' and $_GET["do"] != 'conversion-log' and $_GET["do"] != 'ad-update' and $_GET["do"] != 'subs-update' and $_GET["do"] != 'banner-update' and $_GET["do"] != 'manage-subs' and $_GET["do"] != 'manage-banners' and $_GET["do"] != 'manage-ads' and $_GET["do"] != 'sub-mail' and $_GET["do"] != 'confirm-approve' and $_GET["do"] != 'thumb-reset' and $_GET["do"] != 'preview-reset') ? $class_smarty->displayPage('backend', 'backend_tpl_files', $error_message, $notice_message) : null;
