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
            
            // Handle security and logging classes specifically
            if ($class === 'VSecurity') {
                $path = 'f_core/f_classes/class.security.php';
                break;
            }
            if ($class === 'VAuth') {
                $path = 'f_core/f_classes/class.auth.php';
                break;
            }
            if ($class === 'VRBAC') {
                $path = 'f_core/f_classes/class.rbac.php';
                break;
            }
            if ($class === 'VMiddleware') {
                $path = 'f_core/f_classes/class.middleware.php';
                break;
            }
            if ($class === 'VLogger') {
                $path = 'f_core/f_classes/class.logger.php';
                break;
            }
            if ($class === 'VErrorHandler') {
                $path = 'f_core/f_classes/class.errorhandler.php';
                break;
            }
            if ($class === 'VFingerprint') {
                $path = 'f_core/f_classes/class.fingerprint.php';
                break;
            }
            if ($class === 'VIPTracker') {
                $path = 'f_core/f_classes/class.iptracker.php';
                break;
            }
            if ($class === 'VRedis') {
                $path = 'f_core/f_classes/class.redis.php';
                break;
            }
            if ($class === 'VQueue') {
                $path = 'f_core/f_classes/class.queue.php';
                break;
            }
            
            $be   = (substr($c[0], 1, 2) === 'be') ? true : false;
            $path = 'f_core/f_classes/class.' . strtolower(substr((!$be ? $class : preg_replace('/be/', 'be.', $class, 1)), 1)) . '.php';

            break;
    }

    require_once $path;
});
