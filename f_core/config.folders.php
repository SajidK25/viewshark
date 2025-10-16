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

defined('_ISVALID') or header('Location: /error');

$cfg['main_dir']           = dirname(dirname(__FILE__));
$cfg['scripts_dir']        = $cfg['main_dir'] . '/f_scripts';
$cfg['scripts_url']        = $cfg['main_url'] . '/f_scripts';
$cfg['styles_dir']         = $cfg['main_dir'] . '/f_scripts/fe/css'; // styles dir
$cfg['styles_url']         = $cfg['main_url'] . '/f_scripts/fe/css'; // js dir
$cfg['javascript_dir']     = $cfg['main_dir'] . '/f_scripts/fe/js';
$cfg['javascript_url']     = $cfg['main_url'] . '/f_scripts/fe/js';
$cfg['styles_dir_be']      = $cfg['main_dir'] . '/f_scripts/be/css'; // styles dir, backend
$cfg['styles_url_be']      = $cfg['main_url'] . '/f_scripts/be/css'; // js dir, backend
$cfg['javascript_dir_be']  = $cfg['main_dir'] . '/f_scripts/be/js';
$cfg['javascript_url_be']  = $cfg['main_url'] . '/f_scripts/be/js';
$cfg['class_files_dir']    = $cfg['main_dir'] . '/f_core/f_classes'; // PHP classes dir
$cfg['db_cache_dir']       = $cfg['main_dir'] . '/f_data/data_cache/_c_db'; // db cache dir
$cfg['smarty_cache_dir']   = $cfg['main_dir'] . '/f_data/data_cache/_c_tpl'; // smarty cache dir
$cfg['global_images_dir']  = $cfg['main_dir'] . '/f_data/data_images'; // images dir
$cfg['global_images_url']  = $cfg['main_url'] . '/f_data/data_images'; // images url
$cfg['logging_dir']        = $cfg['main_dir'] . '/f_data/data_logs'; // logs dir
$cfg['logging_url']        = $cfg['main_url'] . '/f_data/data_logs'; // logs url
$cfg['templates_dir']      = $cfg['main_dir'] . '/f_templates'; // templates dir
$cfg['templates_url']      = $cfg['main_url'] . '/f_templates'; // templates url
$cfg['profile_images_dir'] = $cfg['main_dir'] . '/f_data/data_userfiles/user_profile'; // user Images dir
$cfg['profile_images_url'] = $cfg['main_url'] . '/f_data/data_userfiles/user_profile'; // user Images url
$cfg['media_files_dir']    = $cfg['main_dir'] . '/f_data/data_userfiles/user_media'; // user Media dir
$cfg['media_files_url']    = $cfg['main_url'] . '/f_data/data_userfiles/user_media'; // user Media url
$cfg['upload_files_dir']   = $cfg['main_dir'] . '/f_data/data_userfiles/user_uploads'; // user Uploads dir
$cfg['upload_files_url']   = $cfg['main_url'] . '/f_data/data_userfiles/user_uploads'; // user Uploads url
$cfg['channel_views_dir']  = $cfg['main_dir'] . '/f_data/data_userfiles/user_views'; // channel Views dir
$cfg['channel_views_url']  = $cfg['main_url'] . '/f_data/data_userfiles/user_views'; // channel Views url
$cfg['modules_url']        = $cfg['main_url'] . '/f_modules/m_frontend'; // modules url
$cfg['modules_url_be']     = $cfg['main_url'] . '/f_modules/m_backend'; // modules url, backend
$cfg['modules_dir_be']     = $cfg['main_dir'] . '/f_modules/m_backend'; // modules dir, backend
$cfg['ww_templates_dir']   = $cfg['main_dir'] . '/f_templates/tpl_frontend/tpl_ww'; // World writable templates dir
$cfg['language_dir']       = $cfg['main_dir'] . '/f_data/data_languages'; // languages dir
$cfg['sitemap_dir']        = $cfg['main_dir'] . '/f_data/data_sitemaps'; // sitemaps dir
$cfg['sitemap_url']        = $cfg['main_url'] . '/f_data/data_sitemaps'; // sitemaps url
$cfg['player_dir']         = $cfg['main_dir'] . '/f_data/data_player'; // player dir
$cfg['player_url']         = $cfg['main_url'] . '/f_data/data_player'; // player url
$cfg['previews_url']       = $cfg['main_url'] . '/previews'; // player url
