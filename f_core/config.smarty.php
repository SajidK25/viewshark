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

include_once $cfg["class_files_dir"] . '/class_smarty/Smarty.class.php';

$smarty                = new Smarty;
$smarty->compile_check = true;
$smarty->debugging     = false;
$smarty->template_dir  = $cfg["templates_dir"];
$smarty->cache_dir     = $cfg["smarty_cache_dir"];
$smarty->compile_dir   = $cfg["smarty_cache_dir"];
//$smarty->allow_php_tag  = false;

$smarty->assign('main_dir', $cfg["main_dir"]);
$smarty->assign('main_url', $cfg["main_url"]);
$smarty->assign('scripts_url', $cfg["scripts_url"]);
$smarty->assign('modules_url', $cfg["modules_url"]);
$smarty->assign('modules_url_be', $cfg["modules_url_be"]);
$smarty->assign('styles_url', $cfg["styles_url"]);
$smarty->assign('javascript_dir', $cfg["javascript_dir"]);
$smarty->assign('javascript_url', $cfg["javascript_url"]);
$smarty->assign('styles_url_be', $cfg["styles_url_be"]);
$smarty->assign('javascript_url_be', $cfg["javascript_url_be"]);
$smarty->assign('global_images_url', $cfg["global_images_url"]);
$smarty->assign('media_files_url', $cfg["media_files_url"]);
$smarty->assign('profile_images_url', $cfg["profile_images_url"]);
$smarty->assign('logging_dir', $cfg["logging_dir"]);
// Fixed: Use null coalescing to prevent undefined key warning
$smarty->assign('new_layout', $cfg["new_layout"] ?? '1');

$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$smarty->assign('is_mobile', (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') ? true : (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') ? true : (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') ? true : false))));
