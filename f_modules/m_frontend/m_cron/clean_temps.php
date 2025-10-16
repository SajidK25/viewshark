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

set_time_limit(0);

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

$sql = sprintf("DELETE FROM `db_livetemps` WHERE DATE(`date`) < DATE(NOW() - INTERVAL 2 DAY);");
$rs  = $db->execute($sql);

if ($db->Affected_Rows() > 0) {
    echo "db_livetemps records updated successfully\n";
} else {
    echo "db_livetemps not updated\n";
}
