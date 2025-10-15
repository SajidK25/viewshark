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

defined('_ISVALID') or header("Location: /error");
/*
---- edit
 */
$COOKIE_VALIDATION = false; //BETA feature in testing phase, keep disabled for now
$COOKIE_DOMAIN     = '.viewsharkdemo.com';
$COOKIE_WHITELIST  = array('127.0.0.1');
/*
---- end edit
 *
 */

/* set error reporting */
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

/* set include path */
$main_dir = realpath(dirname(__FILE__) . '/../');
set_include_path($main_dir);

/* start defines */
define('_INCLUDE', true);
define('REM_ADDR', (isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ? 'HTTP_X_FORWARDED_FOR' : 'REMOTE_ADDR'));
define('ENC_FIRSTKEY', '4xR5Zlcwo8uUxyrdA5ykgFUXXQFV32o7abJiv+SBzBqXLCAmPq+ciq2ik1M32aGx8f/PZuNxHZ3uckPF/8BL2w==');
define('ENC_SECONDKEY', 'sH7ZuZ0jsiq9DKvjHHzQWAJaB1Ypav17v1rXVxyXpJSCI0untO8B1BUaUT7jxN2YlnyLy2e/JPJO3hMPSneJhhfQbV+ifrWIgD9JmubK+8PDTzB4gM9C0lV1g5R00KQmHWJ0iScv/oXldB0y6nMnLjiVhnTGNwf6gq1JEvukfac=');
// define('CA_CERT', '/etc/ssl/certs/cacert.pm');

define('COOKIE_VALIDATION', $COOKIE_VALIDATION);
define('COOKIE_DOMAIN', $COOKIE_DOMAIN);
define('COOKIE_VALIDATION_WHITELIST', $COOKIE_WHITELIST);

define('COOKIE_LOG', $main_dir . '/f_data/data_logs/log_error/log_cookie/' . date("Ymd") . "-cookie.log");
define('REQUEST_LOG', $main_dir . '/f_data/data_logs/log_error/log_request/' . date("Ymd") . "-request.log");
define('LIVE_AUTH_LOG', $main_dir . '/f_data/data_logs/log_error/log_live/' . date("Ymd") . "-auth.log");
define('LIVE_DONE_LOG', $main_dir . '/f_data/data_logs/log_error/log_live/' . date("Ymd") . "-done.log");
define('LIVE_REC_LOG', $main_dir . '/f_data/data_logs/log_error/log_live/' . date("Ymd") . "-rec.log");

define('SET_COOKIE_OPTIONS', array(
    'expires'  => time() + 60 * 60 * 24 * 10, //10 days
    'path'     => '/',
    'domain'   => COOKIE_DOMAIN, // leading dot for compatibility or use subdomain
    'secure'   => true, // or false
    'httponly' => true, // or false
    'samesite' => 'Strict', // None || Lax  || Strict
));

define('DEL_COOKIE_OPTIONS', array(
    'expires'  => time() - 60 * 60 * 24 * 10, //10 days
    'path'     => '/',
    'domain'   => COOKIE_DOMAIN, // leading dot for compatibility or use subdomain
    'secure'   => true, // or false
    'httponly' => true, // or false
    'samesite' => 'Strict', // None || Lax  || Strict
));

define('SK_INC', (int) 0);
