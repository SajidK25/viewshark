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

class VUserpage
{
    public static function getSubCount($uid = '')
    {
        global $db, $class_database, $upage_id;

        $rs = $db->execute(sprintf("SELECT `usr_subcount` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", intval($uid == '' ? $upage_id : $uid)));

        return $rs->fields['usr_subcount'];
    }
    public static function getFollowCount($uid = '')
    {
        global $db, $class_database, $upage_id;

        $rs = $db->execute(sprintf("SELECT `usr_followcount` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", intval($uid == '' ? $upage_id : $uid)));

        return $rs->fields['usr_followcount'];
    }
}
