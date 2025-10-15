<?php
defined('_ISVALID') or header('Location: /error');

$cfg['theme_name']	= 'blue';

$user_theme			= false;
if (isset($_SESSION["USER_THEME"])) {
	$user_theme = $_SESSION["USER_THEME"] == 'light' ? $cfg['theme_name'] : 'dark' . $cfg['theme_name'];
}

$cfg['theme_name']    = isset($_SESSION['theme_name']) ? $_SESSION['theme_name'] : ($user_theme ?: $cfg['theme_name']);
$cfg['theme_name_be'] = isset($_SESSION['theme_name_be']) ? $_SESSION['theme_name_be'] : $cfg['theme_name'];

$smarty->assign('theme_name', $cfg['theme_name']);
$smarty->assign('theme_name_be', $cfg['theme_name_be']);
$smarty->assign('theme_style', file_get_contents($cfg['scripts_dir'].'/shared/themes.css'));