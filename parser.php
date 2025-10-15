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
define('_INCLUDE', true);

require 'f_core/config.backend.php';
require 'f_core/config.href.php';

$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
$request_uri  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$request_uri  = $query_string != null ? substr($request_uri, 0, strpos($request_uri, '?')) : $request_uri;

$section_array = explode('/', trim($request_uri, '/'));
if (isset($section_array[0]) and $section_array[0][0] == '@') {
    $section_array[0] = '@';
}

$section = (strpos($request_uri, $backend_access_url) and isset($section_array[0]) and $section_array[0][0] != '@') ? $backend_access_url : keyCheck($section_array, $href);

/* uncomment to have links with more than one forward slash */
//$href["user"]            = hrefCheck($href["user"]);

/* uncomment for admin access url with more than one forward slash */
//$backend_access_url        = hrefCheck($backend_access_url);

$sections = array(
    $backend_access_url     => 'f_modules/m_backend/parser',
    $href["error"]          => 'error',
    $href["renew"]          => 'f_modules/m_frontend/m_auth/renew',
    $href["signup"]         => 'f_modules/m_frontend/m_auth/signup',
    $href["signin"]         => 'f_modules/m_frontend/m_auth/signin',
    $href["signout"]        => 'f_modules/m_frontend/m_auth/signout',
    $href["service"]        => 'f_modules/m_frontend/m_auth/recovery',
    $href["reset_password"] => 'f_modules/m_frontend/m_auth/recovery',
    $href["confirm_email"]  => 'f_modules/m_frontend/m_auth/verify',
    $href["captcha"]        => 'f_modules/m_frontend/m_auth/captcha',
    $href["account"]        => 'f_modules/m_frontend/m_acct/account',
    $href["channels"]       => 'f_modules/m_frontend/m_acct/channels',
    $href["messages"]       => 'f_modules/m_frontend/m_msg/messages',
    $href["contacts"]       => 'f_modules/m_frontend/m_msg/messages',
    $href["comments"]       => 'f_modules/m_frontend/m_msg/messages',
    $href["confirm_friend"] => 'f_modules/m_frontend/m_msg/friend_action',
    $href["upload"]         => 'f_modules/m_frontend/m_file/upload',
    $href["uploader"]       => 'f_modules/m_frontend/m_file/uploader',
    $href["submit"]         => 'f_modules/m_frontend/m_file/upload_form',
    $href["files"]          => 'f_modules/m_frontend/m_file/files',
    $href["subscriptions"]  => 'f_modules/m_frontend/m_file/subscriptions',
    $href["following"]      => 'f_modules/m_frontend/m_file/subscriptions',
    $href["files_edit"]     => 'f_modules/m_frontend/m_file/files_edit',
    $href["playlist"]       => 'f_modules/m_frontend/m_file/playlist',
    $href["playlists"]      => 'f_modules/m_frontend/m_file/playlists',
    $href["browse"]         => 'f_modules/m_frontend/m_file/browse',
    $href["blogs"]          => 'f_modules/m_frontend/m_file/browse',
    $href["broadcasts"]     => 'f_modules/m_frontend/m_file/browse',
    $href["videos"]         => 'f_modules/m_frontend/m_file/browse',
    $href["images"]         => 'f_modules/m_frontend/m_file/browse',
    $href["audios"]         => 'f_modules/m_frontend/m_file/browse',
    $href["documents"]      => 'f_modules/m_frontend/m_file/browse',
    $href["watch"]          => 'f_modules/m_frontend/m_file/view',
    $href["see_comments"]   => 'f_modules/m_frontend/m_file/view_extra',
    $href["download"]       => 'f_modules/m_frontend/m_file/download',
    $href["respond"]        => 'f_modules/m_frontend/m_file/respond',
    $href["see_responses"]  => 'f_modules/m_frontend/m_file/respond_extra',
    $href["search"]         => 'f_modules/m_frontend/m_file/search',
    $href["video_playlist"] => 'f_modules/m_frontend/m_player/video_playlist',
    $href["image_playlist"] => 'f_modules/m_frontend/m_player/image_playlist',
    $href["audio_playlist"] => 'f_modules/m_frontend/m_player/audio_playlist',
    $href["freepaper"]      => 'f_modules/m_frontend/m_player/freepaper',
    $href["page"]           => 'f_modules/m_frontend/m_page/page',
    $href["language"]       => 'f_modules/m_frontend/m_page/lang',
    $href["unsupported"]    => 'f_modules/m_frontend/m_page/browser',
    $href["mobile"]         => 'f_modules/m_frontend/m_mobile/main',
    $href["jwplayer"]       => 'f_modules/m_frontend/m_player/jwplayer',
    $href["flowplayer"]     => 'f_modules/m_frontend/m_player/flowplayer',
    $href["embed"]          => 'f_modules/m_frontend/m_player/embed',
    $href["embed_blog"]     => 'f_modules/m_frontend/m_player/embed_blog',
    $href["embed_doc"]      => 'f_modules/m_frontend/m_player/embed_doc',
    $href["related"]        => 'f_modules/m_frontend/m_player/related',
    $href["vast"]           => 'f_modules/m_frontend/m_player/vast',
    $href["vmap"]           => 'f_modules/m_frontend/m_player/vmap',
    $href["adv"]            => 'f_modules/m_frontend/m_player/adv',
    $href["import"]         => 'f_modules/m_frontend/m_file/import',
    $href["manage_channel"] => 'f_modules/m_frontend/m_acct/manage_channel',
    $href["channel"]        => 'f_modules/m_frontend/m_acct/channel',
    $href["@"]              => 'f_modules/m_frontend/m_acct/channel',
    $href["affiliate"]      => 'f_modules/m_frontend/m_acct/affiliate',
    $href["subscribers"]    => 'f_modules/m_frontend/m_acct/subscribers',
    $href["publish"]        => 'f_modules/m_frontend/m_live/auth',
    $href["publish_done"]   => 'f_modules/m_frontend/m_live/done',
    $href["record_done"]    => 'f_modules/m_frontend/m_live/record',
    $href["lstatus"]        => 'f_modules/m_frontend/m_live/status',
    $href["chat_sync"]      => 'f_modules/m_frontend/m_acct/sync_subs',
    $href["vods_sync"]      => 'f_modules/m_frontend/m_acct/sync_vods',
    $href["df_sync"]        => 'f_modules/m_frontend/m_acct/sync_df',
    $href["viewers"]        => 'f_modules/m_frontend/m_acct/live_viewers',
    $href["tokenlist"]      => 'f_modules/m_frontend/m_acct/token_list',
    $href["tokenpayment"]   => 'f_modules/m_frontend/m_acct/token_payment',
    $href["tokendonate"]    => 'f_modules/m_frontend/m_acct/token_donate',
    $href["tokens"]         => 'f_modules/m_frontend/m_acct/tokens',
    $href["soon"]           => 'f_offline/index',
    $href["shorts"]         => 'f_modules/m_frontend/m_file/shorts',
    $href["thumb"]          => 'f_modules/m_frontend/m_file/thumb',
);

if (!ob_start("ob_gzhandler")) {
    ob_start();
}

$include = isset($sections[$section]) ? $sections[$section] : 'error';
include $include . '.php';

$get_ct = ob_get_contents();
$end_ct = ob_end_clean();
echo $get_ct;

function hrefCheck($c)
{
    $section = explode('/', $c);return $section[0];
}
function keyCheck($k, $a)
{
    foreach ($k as $v) {
        if ($v == '@') {
            $v = 'channel';
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
}

function compress_page($buffer)
{
    $search = array(
        "/ +/"                                                                                                                                  => " ",
        "/<!--\{(.*?)\}-->|<!--(.*?)-->|\/\/(.*?)|[\t\r\n]|<!--|-->|\/\/ <!--|\/\/ -->|<!\[CDATA\[|\/\/ \]\]>|\]\]>|\/\/\]\]>|\/\/<!\[CDATA\[/" => "",
    );
    $buffer = preg_replace(array_keys($search), array_values($search), $buffer);
    return $buffer;
}
