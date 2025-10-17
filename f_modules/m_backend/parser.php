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

$main_dir = realpath(dirname(__FILE__) . '/../../');
set_include_path($main_dir);

require 'f_core/config.backend.php';
require 'f_core/config.href.php';

$section       = substr(str_replace($backend_access_url, '', strstr($_SERVER['REQUEST_URI'], $backend_access_url)), 1);
$section       = (strstr($_SERVER['REQUEST_URI'], $backend_access_url) == $backend_access_url) ? $href["be_signin"] : $section;
$section       = (strstr($section, '?') != '') ? str_replace(strstr($section, '?'), '', $section) : $section;
$section_array = explode('/', trim($section, '/'));
$section       = (count($section_array) > 1) ? $section_array[0] : $section;

$sections = array(
    $backend_access_url        => 'f_modules/m_backend/signin',
    $href["be_signin"]         => 'f_modules/m_backend/signin',
    $href["be_signout"]        => 'f_modules/m_backend/signout',
    $href["be_files"]          => 'f_modules/m_backend/files',
    $href["be_advertising"]    => 'f_modules/m_backend/advertising',
    $href["be_settings"]       => 'f_modules/m_backend/settings',
    $href["be_members"]        => 'f_modules/m_backend/members',
    $href["be_players"]        => 'f_modules/m_backend/players',
    $href["be_service"]        => 'f_modules/m_frontend/m_auth/recovery',
    $href["be_reset_password"] => 'f_modules/m_frontend/m_auth/recovery',
    $href["be_system_info"]    => 'f_modules/m_backend/m_tools/m_linfo/index',
    $href["be_upload"]         => 'f_modules/m_backend/m_tools/m_upload/upload',
    $href["be_uploader"]       => 'f_modules/m_backend/m_tools/m_upload/uploader',
    $href["be_uploader"]       => 'f_modules/m_backend/m_tools/m_upload/uploader',
    $href["be_submit"]         => 'f_modules/m_backend/m_tools/m_upload/upload_form',
    $href["be_dashboard"]      => 'f_modules/m_backend/dashboard',
    ""                         => 'f_modules/m_backend/dashboard',
    $href["be_affiliate"]      => 'f_modules/m_backend/affiliate',
    $href["be_subscribers"]    => 'f_modules/m_backend/subscribers',
    $href["be_analytics"]      => 'f_modules/m_backend/analytics',
    $href["be_import"]         => 'f_modules/m_backend/m_tools/m_upload/import',
    $href["be_tokens"]         => 'f_modules/m_backend/tokens',
    $href["be_dbtools"]        => 'f_modules/m_backend/db_tools',
);

$include = isset($sections[$section]) ? $sections[$section] : 'error';
include $include . '.php';

$get_ct = ob_get_contents();
$end_ct = ob_end_clean();
echo $get_ct;
