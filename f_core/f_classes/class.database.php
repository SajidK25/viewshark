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

class VDatabase
{
    /* db connection init */
    public function dbConnection()
    {
        require 'f_core/config.database.php';

        $db = &ADONewConnection($cfg_dbtype);
        if (!$db->Connect($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname)) {
            die('<b>Error: </b> A database connection could not be established.');
        } else {
            //$db->debug = true;
            return $db;
        }
    }
    /* retrieve value of one database field */
    public function singleFieldValue($db_table, $get_value, $where_field, $where_value, $cache_time = false)
    {
        global $db;

        $sql = sprintf("SELECT `%s` FROM `%s` WHERE `%s`='%s' LIMIT 1;", $get_value, $db_table, $where_field, $where_value);
        $q   = $cache_time > 0 ? $db->CacheExecute($cache_time, $sql) : $db->execute($sql);

        return $q->fields[$get_value];
    }
    /* insert a single entry */
    public function singleInsert($db_table, $set_field, $set_value)
    {
        global $db;
        $q = $db->execute(sprintf("INSERT INTO `%s` (`%s`) VALUES ('%s');", $db_table, $set_field, $set_value));
    }
    /* update global settings */
    public function settingsUpdate()
    {
        global $db, $language, $cfg;

        $cfg_vars = VArrayConfig::cfgSection();
        $count    = 0;

        switch ($_GET['s']) {
            case "backend-menu-entry2-sub7": //admin, main modules
                if ($_POST['backend_menu_entry2_sub7_live'] == 0 and $_POST['backend_menu_entry2_sub7_video'] == 0 and $_POST['backend_menu_entry2_sub7_image'] == 0 and $_POST['backend_menu_entry2_sub7_audio'] == 0 and $_POST['backend_menu_entry2_sub7_doc'] == 0 and $_POST['backend_menu_entry2_sub7_blog'] == 0) {
                    echo VGenerate::noticeTpl('', $language['backend.menu.entry2.sub7.mod.err'], '');
                    return false;
                }
                $db_tbl = 'db_settings';
                break;
            case "backend-menu-entry3-sub20":
            case "backend-menu-entry3-sub21":
            case "backend-menu-entry3-sub22":
            case "backend-menu-entry3-sub23":
            case "backend-menu-entry3-sub24":
                $db_tbl = 'db_conversion';
                break;
            default:
                $db_tbl = 'db_settings';
                break;
        }

        if (is_array($cfg_vars) and count($cfg_vars) > 0) {
            foreach ($cfg_vars as $key => $post_field) {
                $query = $db->execute(sprintf("UPDATE `%s` SET `cfg_data` = '%s' WHERE `cfg_name` = '%s'  LIMIT 1; ", $db_tbl, $post_field, $key));
                $count = $db->Affected_Rows() > 0 ? $count + 1 : $count;

                if ($_GET['s'] == 'backend-menu-entry1-sub9' and $cfg['activity_logging'] == 1) {
                    $db->execute(sprintf("UPDATE `db_trackactivity` SET `%s`='%s';", $key, $post_field));
                }
            }
        }
        $opened_entry = VGenerate::keepEntryOpen();
        return $count;
    }
    /* update table field values from $_POST['hc_id'] */
    public function doUpdate($db_table, $db_field, $update_array)
    {
        global $db;

        if (!is_array($update_array)) {
            return;
        }

        foreach ($update_array as $key => $value) {
            $query_string .= "`" . $key . "` = '" . $value . "', ";
        }
        $query_string = substr($query_string, 0, -2);
        $query        = "UPDATE `" . $db_table . "` SET " . $query_string . " WHERE `" . $db_field . "` = '" . intval($_POST['hc_id']) . "';";
        $result       = $db->execute($query);

        if ($db->Affected_Rows() > 0) {
            return true;
        } else {
            return false;
        }

    }
    /* update table fields for usr_id */
    public function entryUpdate($dbt, $arr)
    {
        global $db;
        if (count($arr) > 0) {
            foreach ($arr as $dbf => $val) {$q .= "`" . $dbf . "` = '" . $val . "', ";}
            $query  = "UPDATE `" . $dbt . "` SET " . substr($q, 0, -2) . " WHERE `usr_id`='" . intval($_SESSION['USER_ID']) . "' LIMIT 1;";
            $result = $db->execute($query);

            if ($db->Affected_Rows() > 0) {
                return true;
            }

        } else {
            return false;
        }

    }
    /* insert into table from array */
    public function doInsert($db_table, $insert_array)
    {
        global $db;

        foreach ($insert_array as $key => $value) {
            $field_string .= '`' . $key . '`, ';
            $value_string .= "'" . $value . "', ";
        }
        $field_string = substr($field_string, 0, -2);
        $value_string = substr($value_string, 0, -2);

        $query  = 'INSERT INTO `' . $db_table . '` (' . $field_string . ') VALUES (' . $value_string . ');';
        $result = $db->execute($query);

        if ($db->Affected_Rows() > 0) {
            return true;
        } else {
            return false;
        }

    }
    /* get specific config values from database and assign them */
    public function getConfigurations($settings)
    {
        global $cfg, $smarty, $db;

        $db_table       = 'db_settings';
        $settings_array = explode(",", $settings);

        $q_get = '`cfg_name` IN ("' . implode('", "', $settings_array) . '")';

        $q_result = $db->Execute(sprintf("SELECT `cfg_name`, `cfg_data` FROM `%s` WHERE %s;", $db_table, $q_get));

        if ($q_result) {
            while (!$q_result->EOF) {
                $cfg_name       = $q_result->fields['cfg_name'];
                $cfg_data       = $q_result->fields['cfg_data'];
                $cfg[$cfg_name] = $cfg_data;
                $smarty->assign($cfg_name, $cfg_data);
                @$q_result->MoveNext();
            }
        }
        return $cfg;
    }

    /* API: latest public videos */
    public function getLatestVideos($limit = 5, $timeWindowMinutes = null)
    {
        global $db;

        $limit = (int) $limit;
        $whereTime = '';
        if ($timeWindowMinutes !== null) {
            $timeWindowMinutes = (int) $timeWindowMinutes;
            $whereTime = sprintf(" AND A.`upload_date` >= NOW() - INTERVAL %d MINUTE ", $timeWindowMinutes);
        }

        $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
             FROM `db_videofiles` A
             JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
             WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy`='public" . $whereTime . "
             ORDER BY A.`upload_date` DESC
             LIMIT " . $limit . ";";

        $res = $db->execute($sql); // only ints interpolated; strings are static
        $rows = [];
        if ($res) {
            while (!$res->EOF) {
                $rows[] = [
                    'file_key'    => $res->fields['file_key'],
                    'title'       => $res->fields['title'],
                    'description' => $res->fields['description'],
                    'views'       => (int) $res->fields['views'],
                    'tags'        => (string) $res->fields['tags'],
                    'username'    => $res->fields['username'],
                ];
                $res->MoveNext();
            }
        }
        return $rows;
    }

    /* API: latest live streams */
    public function getLatestStreams($limit = 5, $timeWindowMinutes = null)
    {
        global $db;

        $limit = (int) $limit;
        $whereTime = '';
        if ($timeWindowMinutes !== null) {
            $timeWindowMinutes = (int) $timeWindowMinutes;
            $whereTime = sprintf(" AND A.`upload_date` >= NOW() - INTERVAL %d MINUTE ", $timeWindowMinutes);
        }

        $sql = "SELECT A.`file_key`, A.`stream_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
             FROM `db_livefiles` A
             JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
             WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy`='public" . $whereTime . "
             ORDER BY A.`upload_date` DESC
             LIMIT " . $limit . ";";

        $res = $db->execute($sql); // only ints interpolated; strings are static
        $rows = [];
        if ($res) {
            while (!$res->EOF) {
                $rows[] = [
                    'file_key'    => $res->fields['file_key'],
                    'stream_key'  => $res->fields['stream_key'],
                    'title'       => $res->fields['title'],
                    'description' => $res->fields['description'],
                    'views'       => (int) $res->fields['views'],
                    'tags'        => (string) $res->fields['tags'],
                    'username'    => $res->fields['username'],
                ];
                $res->MoveNext();
            }
        }
        return $rows;
    }

    /* API: search public videos by query */
    public function searchVideos($query, $limit = 5)
    {
        global $db;
        $limit = (int) $limit;
        $like = '%' . $query . '%';
        $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
             FROM `db_videofiles` A
             JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
             WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy`='public'
               AND (A.`file_title` LIKE ? OR A.`file_description` LIKE ? OR A.`file_tags` LIKE ?)
             ORDER BY A.`upload_date` DESC
             LIMIT " . $limit . ";";

        $res = $db->Execute($sql, array($like, $like, $like));
        $rows = [];
        if ($res) {
            while (!$res->EOF) {
                $rows[] = [
                    'file_key'    => $res->fields['file_key'],
                    'title'       => $res->fields['title'],
                    'description' => $res->fields['description'],
                    'views'       => (int) $res->fields['views'],
                    'tags'        => (string) $res->fields['tags'],
                    'username'    => $res->fields['username'],
                ];
                $res->MoveNext();
            }
        }
        return $rows;
    }
}
