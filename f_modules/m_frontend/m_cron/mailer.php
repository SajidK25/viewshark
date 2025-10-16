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

define('_ISVALID', true);

$main_dir = realpath(dirname(__FILE__) . '/../../../');

set_time_limit(0);
set_include_path($main_dir);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$mail_type = $class_filter->clr_str($_SERVER["argv"][1]);
$mail_key  = $class_filter->clr_str($_SERVER["argv"][2]);

$do_notify = VNotify::Mailer($mail_type, $mail_key);
