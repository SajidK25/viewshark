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

/* languages array */
function langTypes()
{
    global $db;

    $_cache = false;
    $_time  = 3600 * 6; //6 hours

    $sql = sprintf("SELECT `lang_id`, `lang_name`, `lang_flag`, `lang_default` FROM `db_languages` WHERE `lang_active`='1';");
    $r   = $_cache ? $db->CacheExecute($_time, $sql) : $db->execute($sql);

    $_lang = array();

    if ($r->fields["lang_id"]) {
        while (!$r->EOF) {
            $_lang[$r->fields["lang_id"]] = array("lang_name" => $r->fields["lang_name"], "lang_flag" => $r->fields["lang_flag"], "lang_default" => $r->fields["lang_default"]);

            $r->MoveNext();
        }
    }

    return $_lang;
}
