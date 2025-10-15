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

$language['recovery.h1.text']         = 'Forgot Login';
$language['backend.recovery.h1.text'] = 'Forgot Your Admin Access?';

$language['recovery.password.text']             = 'Password Recovery';
$language['recovery.password.account']          = 'Account Password Recovery';
$language['recovery.forgot.password']           = 'Forgot ' . $cfg['website_shortname'] . ' Password';
$language['backend.recovery.forgot.password']   = 'Forgot Admin Password';
$language['backend.recovery.recovery.password'] = 'Admin Password Recovery';
$language['recovery.forgot.username']           = 'Forgot ' . $cfg['website_shortname'] . ' Username';
$language['backend.recovery.forgot.username']   = 'Forgot Admin Username';

$language['recovery.forgot.pass.txt']         = 'Enter your username ';
$language['backend.recovery.forgot.pass.txt'] = 'Enter your admin username ';
$language['recovery.forgot.pass.txt1']        = 'and we will email instructions to you on how to reset your password.';

$language['recovery.forgot.user.txt']         = 'Enter the email address you used to sign up ';
$language['backend.recovery.forgot.user.txt'] = 'Enter your admin email address ';
$language['recovery.forgot.user.txt1']        = 'and we will email your username to you.';
$language['recovery.verif.code.txt']          = ' and the verification code ';
$language['recovery.verif.code.txt']          = '';

$language['recovery.forgot.new.password']     = 'New Password';
$language['recovery.forgot.retype.password']  = 'Retype Password';
$language['recovery.forgot.password.confirm'] = 'Your password was successfully reset!';
$language['recovery.general.code']            = 'Code';
$language['recovery.disabled.username']       = 'Username recovery is currently disabled.';
$language['recovery.disabled.password']       = 'Password recovery is currently disabled.';
