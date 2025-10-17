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
defined('_INCLUDE') or header('Location: /error');

$cfg["file_seo_url"] = 1;
$request_uri         = $_SERVER["REQUEST_URI"];

$href = array();
/* frontend */
$href["index"]          = '';
$href["error"]          = 'error';
$href["renew"]          = 'renew';
$href["signup"]         = 'register';
$href["signin"]         = 'signin';
$href["signout"]        = 'signout';
$href["service"]        = 'service';
$href["reset_password"] = 'reset_password';
$href["confirm_email"]  = 'confirm_email';
$href["captcha"]        = 'captcha';
$href["account"]        = 'account';
$href["channels"]       = 'channels';
$href["channel"]        = 'channel';
$href["@"]              = 'channel';
$href["manage_channel"] = 'my_channel';
$href["featured"]       = 'featured';
$href["activity"]       = 'activity';
$href["discussion"]     = 'comments';
$href["about"]          = 'about';
$href["messages"]       = 'messages';
$href["contacts"]       = 'contacts';
$href["comments"]       = 'comments';
$href["confirm_friend"] = 'friend_action';
$href["upload"]         = 'upload';
$href["uploads"]        = 'uploads';
$href["uploader"]       = 'uploader';
$href["submit"]         = 'submit';
$href["files"]          = 'library';
$href["blogs"]          = 'blogs';
$href["my_blogs"]       = 'my_blogs';
$href["videos"]         = 'videos';
$href["images"]         = 'pictures';
$href["audios"]         = 'music';
$href["documents"]      = 'documents';
$href["broadcasts"]     = 'broadcasts';
$href["browse"]         = (strpos($request_uri, $href["broadcasts"]) ? $href["broadcasts"] : ((strpos($request_uri, $href["videos"])) ? $href["videos"] : ((strpos($request_uri, $href["images"])) ? $href["images"] : ((strpos($request_uri, $href["audios"])) ? $href["audios"] : ((strpos($request_uri, $href["documents"])) ? $href["documents"] : ((strpos($request_uri, $href["blogs"])) ? $href["blogs"] : 'browse'))))));
$href["files_edit"]     = 'file_edit';
$href["user"]           = 'users';
$href["playlist"]       = 'playlist';
$href["playlists"]      = 'playlists';
$href["watch"]          = 'view';
$href["watchlist"]      = 'watchlist';
$href["history"]        = 'history';
$href["liked"]          = 'liked';
$href["download"]       = 'get_file';
$href["see_comments"]   = 'all_comments';
$href["see_responses"]  = 'all_responses';
$href["responses"]      = 'responses';
$href["respond"]        = 'respond';
$href["subscriptions"]  = 'subscriptions';
$href["subscribers"]    = 'subscribers';
$href["followers"]      = 'followers';
$href["following"]      = 'following';
$href["mobile"]         = 'mobile';
$href["search"]         = 'search';
$href["video_playlist"] = 'video_playlist';
$href["image_playlist"] = 'image_playlist';
$href["audio_playlist"] = 'audio_playlist';
$href["freepaper"]      = 'freepaper';
$href["jwplayer"]       = 'jw';
$href["flowplayer"]     = 'flow';
$href["embed"]          = 'embed';
$href["embed_blog"]     = 'embed_blog';
$href["embed_doc"]      = 'embed_doc';
$href["related"]        = 'related';
$href["vast"]           = 'vast';
$href["vmap"]           = 'vmap';
$href["adv"]            = 'adv';
$href["page"]           = 'page';
$href["language"]       = 'lang';
$href["unsupported"]    = 'unsupported';
$href["import"]         = 'import';
$href["favorites"]      = 'favorites';
$href["affiliate"]      = 'affiliate';
$href["publish"]        = 'publish';
$href["publish_done"]   = 'publish_done';
$href["record_done"]    = 'record_done';
$href["viewers"]        = 'vcount';
$href["soon"]           = 'coming_soon';
$href["tokens"]         = 'tokens';
$href["tokenlist"]      = 'tokenlist';
$href["tokenpayment"]   = 'tokenpayment';
$href["tokendonate"]    = 'tokendonate';
$href["chat"]           = 'chat';
$href["chat_url_1"]     = 'userchatrequest';
$href["chat_url_2"]     = 'logoutrequest';
$href["chat_url_3"]     = 'fsrequest';
$href["chat_url_4"]     = 'ufsrequest';
$href["chat_sync"]      = 'syncsubs';
$href["vods_sync"]      = 'syncvods';
$href["df_sync"]        = 'syncdf';
$href["shorts"]         = 'shorts';
$href["thumb"]          = 'thumb';
$href["lstatus"]        = 'ls';
/* backend */
$href["be_signin"]         = 'signin';
$href["be_signout"]        = 'signout';
$href["be_settings"]       = 'settings';
$href["be_analytics"]      = 'analytics';
$href["be_dashboard"]      = 'dashboard';
$href["be_affiliate"]      = 'affiliate';
$href["be_members"]        = 'members';
$href["be_subscribers"]    = 'subscribers';
$href["be_files"]          = 'files';
$href["be_advertising"]    = 'advertising';
$href["be_service"]        = 'service';
$href["be_reset_password"] = 'reset_password';
$href["be_system_info"]    = 'system_info';
$href["be_upload"]         = 'upload';
$href["be_uploader"]       = 'uploader';
$href["be_submit"]         = 'submit';
$href["be_players"]        = 'players';
$href["be_import"]         = 'import';
$href["be_tokens"]         = 'tokens';
$href["be_dbtools"]        = 'db_tools';
/* other/mixed/extra */
$href["x_recovery"] = 'recovery';
$href["x_payment"]  = 'payment';
