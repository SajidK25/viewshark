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
defined('_ISVALID') or header('Location: /error');

require 'config.define.php';
// PHP 8+ polyfills used by app (strftime)
require_once __DIR__ . '/polyfill.php';

//var init
$cfg      = array();
$language = array();

//require init
require_once 'config.cache.php';
require_once 'config.set.php';
require_once 'config.href.php';
require_once 'config.folders.php';
require_once 'config.paging.php';
require_once 'config.footer.php';
require_once 'config.smarty.php';
require_once 'config.autoload.php';
require_once 'config.keys.php';
//var init
VServer::var_check();
//cache dir
$ADODB_CACHE_DIR = $cfg['db_cache_dir'];
//include init
require_once 'f_classes/class_adodb/adodb.inc.php';
require_once 'f_classes/class_mobile/MobileDetect.php';
require_once 'f_functions/functions.general.php';
//class init
$class_filter   = new VFilter;
$class_language = new VLanguage;
$class_redirect = new VRedirect;
$class_smarty   = new VTemplate;
$class_database = new VDatabase;
//database, config and lang init
$db                     = $class_database->dbConnection();
$cfg                    = $class_database->getConfigurations('affiliate_module,live_module,live_server,live_uploads,live_cast,live_chat,live_chat_server,live_chat_salt,live_vod,live_vod_server,live_hls_server,live_del,thumbs_nr,mobile_module,mobile_detection,default_language,head_title,metaname_description,metaname_keywords,website_shortname,video_module,video_uploads,image_module,image_uploads,audio_module,audio_uploads,document_module,document_uploads,activity_logging,debug_mode,website_offline_mode,website_offline_message,internal_messaging,user_friends,user_blocking,channel_comments,file_comments,paid_memberships,user_subscriptions,file_playlists,public_channels,session_name,session_lifetime,date_timezone,google_analytics,google_webmaster,yahoo_explorer,bing_validate,backend_menu_toggle,benchmark_display,facebook_link,twitter_link,gplus_link,twitter_feed,blog_module,file_favorites,file_rating,file_history,file_watchlist,file_playlists,file_comments,file_responses,custom_tagline,user_follows,file_approval,video_player,audio_player,comment_emoji,social_media_links,user_tokens,import_yt,import_dm,import_vi,short_module,short_uploads,new_layout,channel_memberships,member_chat_only,member_badges');
$cfg["global_salt_key"] = 'nad0af09j30fm93049f30m94f3mf90f04m94094m03999999999999';
//session init
VSession::init();
//theme init
require_once 'config.theme.php';
//cookie stuff
VServer::cookie_validation_set();
//check access based on IP address
VIPaccess::sectionAccess($backend_access_url);
//load some language files
include_once $class_language->setLanguageFile('frontend', 'language.footer');
if (VSession::isLoggedIn()) {
    include_once $class_language->setLanguageFile('frontend', 'language.files');
    include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
}
//cookie stuff
VServer::cookie_validation_check();
//smarty swiper styles
VGenerate::smarty_swiper();
