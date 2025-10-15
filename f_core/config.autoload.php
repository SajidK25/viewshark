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

spl_autoload_register(function ($class) {
    $c = explode("_", $class);

    switch (strtolower($c[0])) {
        case "smarty":
            $exclude = array(
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_href_entry.php',
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_lang_entry.php',
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_page_display.php',
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_generate_html.php',
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_sanitize.php',
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_fetch.php',
                'f_core/f_classes/class_smarty/sysplugins/smarty_internal_compile_var.php',
            );

            $path = 'f_core/f_classes/class_smarty/sysplugins/' . strtolower($class) . '.php';

            if (in_array($path, $exclude)) {
                return;
            }

            break;

        default:
            if (strpos($class, "\\") !== false or strpos($class, "Google") !== false or strpos($class, "Memcache") !== false or strpos($class, "Requests") !== false or strpos($class, "PHPMailer") !== false or strpos($class, "Embed") !== false) {
                return;
            }
            $be   = (substr($c[0], 1, 2) === 'be') ? true : false;
            $path = 'f_core/f_classes/class.' . strtolower(substr((!$be ? $class : preg_replace('/be/', 'be.', $class, 1)), 1)) . '.php';

            break;
    }

    require_once $path;
});
