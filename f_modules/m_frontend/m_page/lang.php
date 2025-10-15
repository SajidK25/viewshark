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

include_once 'f_core/config.core.php';
include_once 'f_core/config.language.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');

$error_message  = null;
$notice_message = null;

$_lang = langTypes();
$_t    = isset($_GET["t"]) ? $class_filter->clr_str($_GET["t"]) : false;

if ($_t and is_array($_lang[$_t])) {
    $s = (isset($_GET["b"]) and (int) $_GET["b"] == 1) ? 'be_lang' : 'fe_lang';
    $f = (isset($_GET["b"]) and (int) $_GET["b"] == 1) ? 'be_flag' : 'fe_flag';

    $_SESSION[$s] = $_t;
    $_SESSION[$f] = $_lang[$_t]['lang_flag'];

    echo VGenerate::declareJS('$(document).ready(function(){window.location.reload();});');
}
