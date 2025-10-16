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

$cfg_dbtype = 'mysqli';
$cfg_dbhost = getenv('DB_HOST') ?: 'localhost';
$cfg_dbname = getenv('DB_NAME') ?: '';
$cfg_dbuser = getenv('DB_USER') ?: '';
$cfg_dbpass = getenv('DB_PASS') ?: '';
