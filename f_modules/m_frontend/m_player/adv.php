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

include_once 'f_core/config.core.php';

$ad_impr        = $class_filter->clr_str($_GET["impression"]);
$ad_click       = $class_filter->clr_str($_GET["click"]);
$ad_type        = $class_filter->clr_str($_GET["t"]);
$ad_key         = ($ad_impr != '' ? $ad_impr : $ad_click);
$ad_click_track = $class_database->singleFieldValue('db_' . $ad_type . 'adentries', 'ad_click_track', 'ad_key', $ad_key);

$db_field = $ad_impr != '' ? 'ad_impressions' : ($ad_click != '' ? 'ad_clicks' : null);
$u        = ($db_field != '' and $ad_click_track == 1) ? $db->execute(sprintf("UPDATE `db_%sadentries` SET `%s`=`%s`+1 WHERE `ad_key`='%s' LIMIT 1;", $ad_type, $db_field, $db_field, $ad_key)) : null;
