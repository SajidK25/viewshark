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

class VDatabase
{
    /* db connection init */
    public function dbConnection()
    {
        require 'f_core/config.database.php';

        $db = &ADONewConnection($cfg_dbtype);
        if (!$db->Connect($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname)) {
            // Log database connection error
            $logger = VLogger::getInstance();
            $logger->logDatabaseError('Database connection failed', '', [
                'host' => $cfg_dbhost,
                'database' => $cfg_dbname,
                'user' => $cfg_dbuser
            ]);
            
            die('<b>Error: </b> A database connection could not be established.');
        } else {
            //$db->debug = true;
            return $db;
        }
    }
    /* retrieve value of one database field - SECURE VERSION */
    public function singleFieldValue($db_table, $get_value, $where_field, $where_value, $cache_time = false)
    {
        global $db;

        try {
            // Validate table and field names (whitelist approach)
            if (!$this->isValidTableName($db_table) || !$this->isValidFieldName($get_value) || !$this->isValidFieldName($where_field)) {
                throw new InvalidArgumentException('Invalid table or field name');
            }

            $sql = "SELECT `{$get_value}` FROM `{$db_table}` WHERE `{$where_field}` = ? LIMIT 1";
            $q   = $cache_time > 0 ? $db->CacheExecute($cache_time, $sql, array($where_value)) : $db->Execute($sql, array($where_value));

            if (!$q) {
                $logger = VLogger::getInstance();
                $logger->logDatabaseError($db->ErrorMsg(), $sql, [$where_value]);
                return null;
            }

            return $q && !$q->EOF ? $q->fields[$get_value] : null;
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logDatabaseError($e->getMessage(), $sql ?? '', [$where_value ?? '']);
            throw $e;
        }
    }
    
    /* validate table name against whitelist */
    private function isValidTableName($table)
    {
        // Add your actual table names here
        $allowedTables = [
            'db_settings', 'db_conversion', 'db_videofiles', 'db_livefiles', 
            'db_accountuser', 'db_trackactivity', 'db_imagefiles', 'db_audiofiles',
            'db_documentfiles', 'db_blogfiles', 'db_comments', 'db_responses',
            'db_playlists', 'db_subscriptions', 'db_categories', 'db_channels',
            'db_users', 'db_sessions', 'db_ip_tracking', 'db_banlist', 
            'db_fingerprints', 'db_fingerprint_bans', 'db_email_log', 
            'db_notifications', 'db_user_preferences', 'db_password_resets',
            'db_logs'
        ];
        return in_array($table, $allowedTables);
    }
    
    /* validate field name */
    private function isValidFieldName($field)
    {
        // Only allow alphanumeric characters and underscores
        return preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $field);
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
    /* update table field values - SECURE VERSION */
    public function doUpdate($db_table, $db_field, $update_array, $id_value = null)
    {
        global $db;

        try {
            if (!is_array($update_array) || empty($update_array)) {
                return false;
            }

            // Validate table and field names
            if (!$this->isValidTableName($db_table) || !$this->isValidFieldName($db_field)) {
                throw new InvalidArgumentException('Invalid table or field name');
            }

            // Use provided ID or get from POST (with validation)
            $id = $id_value !== null ? (int)$id_value : VSecurity::postParam('hc_id', 'int', 0);
            if ($id <= 0) {
                return false;
            }

            $setParts = [];
            $values = [];
            
            foreach ($update_array as $key => $value) {
                if (!$this->isValidFieldName($key)) {
                    continue; // Skip invalid field names
                }
                $setParts[] = "`{$key}` = ?";
                $values[] = $value;
            }
            
            if (empty($setParts)) {
                return false;
            }
            
            $values[] = $id; // Add ID for WHERE clause
            
            $query = "UPDATE `{$db_table}` SET " . implode(', ', $setParts) . " WHERE `{$db_field}` = ?";
            $result = $db->Execute($query, $values);

            if (!$result) {
                $logger = VLogger::getInstance();
                $logger->logDatabaseError($db->ErrorMsg(), $query, $values);
                return false;
            }

            return $db->Affected_Rows() > 0;
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logDatabaseError($e->getMessage(), $query ?? '', $values ?? []);
            throw $e;
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
    /* insert into table from array - SECURE VERSION */
    public function doInsert($db_table, $insert_array)
    {
        global $db;

        try {
            if (!is_array($insert_array) || empty($insert_array)) {
                return false;
            }

            // Validate table name
            if (!$this->isValidTableName($db_table)) {
                throw new InvalidArgumentException('Invalid table name');
            }

            $fields = [];
            $placeholders = [];
            $values = [];

            foreach ($insert_array as $key => $value) {
                if (!$this->isValidFieldName($key)) {
                    continue; // Skip invalid field names
                }
                $fields[] = "`{$key}`";
                $placeholders[] = '?';
                $values[] = $value;
            }

            if (empty($fields)) {
                return false;
            }

            $query = "INSERT INTO `{$db_table}` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $result = $db->Execute($query, $values);

            if (!$result) {
                $logger = VLogger::getInstance();
                $logger->logDatabaseError($db->ErrorMsg(), $query, $values);
                return false;
            }

            return $db->Affected_Rows() > 0;
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logDatabaseError($e->getMessage(), $query ?? '', $values ?? []);
            throw $e;
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

        $limit = max(1, (int) $limit);

        $params = [];
        $whereTime = '';
        if ($timeWindowMinutes !== null) {
            $timeWindowMinutes = max(1, (int) $timeWindowMinutes);
            $since = date('Y-m-d H:i:s', time() - ($timeWindowMinutes * 60));
            $whereTime = " AND A.`upload_date` >= ? ";
            $params[] = $since;
        }

        $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
                FROM `db_videofiles` A
                JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
                WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy` IN (0,'0','public') " . $whereTime . "
                ORDER BY A.`upload_date` DESC
                LIMIT " . $limit;

        $res = empty($params) ? $db->Execute($sql) : $db->Execute($sql, $params);
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

        $limit = max(1, (int) $limit);

        $params = [];
        $whereTime = '';
        if ($timeWindowMinutes !== null) {
            $timeWindowMinutes = max(1, (int) $timeWindowMinutes);
            $since = date('Y-m-d H:i:s', time() - ($timeWindowMinutes * 60));
            $whereTime = " AND A.`upload_date` >= ? ";
            $params[] = $since;
        }

        $sql = "SELECT A.`file_key`, A.`stream_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
                FROM `db_livefiles` A
                JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
                WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy` IN (0,'0','public') " . $whereTime . "
                ORDER BY A.`upload_date` DESC
                LIMIT " . $limit;

        $res = empty($params) ? $db->Execute($sql) : $db->Execute($sql, $params);
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
        $limit = max(1, (int) $limit);
        $query = trim((string) $query);
        $rows = [];

        // Try FULLTEXT first; fallback to LIKE
        try {
            $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`,
                           MATCH(A.`file_title`, A.`file_description`, A.`file_tags`) AGAINST (? IN BOOLEAN MODE) AS score
                    FROM `db_videofiles` A
                    JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
                    WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy` IN (0,'0','public')
                      AND MATCH(A.`file_title`, A.`file_description`, A.`file_tags`) AGAINST (? IN BOOLEAN MODE)
                    ORDER BY score DESC, A.`upload_date` DESC
                    LIMIT " . $limit;
            $res = $db->Execute($sql, array($query . '*', $query . '*'));
        } catch (Exception $e) {
            $like = '%' . $query . '%';
            $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
                    FROM `db_videofiles` A
                    JOIN `db_accountuser` B ON A.`usr_id`=B.`usr_id`
                    WHERE A.`approved`='1' AND A.`deleted`='0' AND A.`active`='1' AND A.`privacy` IN (0,'0','public')
                      AND (A.`file_title` LIKE ? OR A.`file_description` LIKE ? OR A.`file_tags` LIKE ?)
                    ORDER BY A.`upload_date` DESC
                    LIMIT " . $limit;
            $res = $db->Execute($sql, array($like, $like, $like));
        }
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

    /**
     * Get latest videos within a time window
     * @param int $limit Maximum number of videos to return
     * @param int $time_window Time window in minutes (default: 5)
     * @return array Array of video data
     */
    public function getLatestVideos($limit = 5, $time_window = 5)
    {
        global $db;
        $rows = [];
        
        try {
            // Validate inputs
            $limit = max(1, min(100, (int)$limit)); // Between 1 and 100
            $time_window = max(1, min(1440, (int)$time_window)); // Between 1 minute and 24 hours
            
            $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, 
                           A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`,
                           A.`upload_date`
                    FROM `db_videofiles` A
                    JOIN `db_accountuser` B ON A.`usr_id` = B.`usr_id`
                    WHERE A.`approved` = '1' AND A.`deleted` = '0' AND A.`active` = '1' 
                      AND A.`privacy` IN (0, '0', 'public')
                      AND A.`upload_date` >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
                    ORDER BY A.`upload_date` DESC
                    LIMIT ?";
                    
            $res = $db->Execute($sql, array($time_window, $limit));
            
            if ($res) {
                while (!$res->EOF) {
                    $rows[] = [
                        'file_key'    => $res->fields['file_key'],
                        'title'       => $res->fields['title'],
                        'description' => $res->fields['description'],
                        'views'       => (int) $res->fields['views'],
                        'tags'        => (string) $res->fields['tags'],
                        'username'    => $res->fields['username'],
                        'upload_date' => $res->fields['upload_date']
                    ];
                    $res->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logDatabaseError($e->getMessage(), $sql ?? '', [$time_window, $limit]);
        }
        
        return $rows;
    }

    /**
     * Search videos by query
     * @param string $query Search query
     * @param int $limit Maximum number of results to return
     * @return array Array of video data
     */
    public function searchVideos($query, $limit = 10)
    {
        global $db;
        $rows = [];
        
        try {
            // Validate inputs
            $limit = max(1, min(100, (int)$limit)); // Between 1 and 100
            $query = trim($query);
            
            if (empty($query)) {
                return $rows;
            }
            
            // Try fulltext search first (if available)
            try {
                $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, 
                               A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`,
                               MATCH(A.`file_title`, A.`file_description`, A.`file_tags`) AGAINST (? IN BOOLEAN MODE) AS score
                        FROM `db_videofiles` A
                        JOIN `db_accountuser` B ON A.`usr_id` = B.`usr_id`
                        WHERE A.`approved` = '1' AND A.`deleted` = '0' AND A.`active` = '1' 
                          AND A.`privacy` IN (0, '0', 'public')
                          AND MATCH(A.`file_title`, A.`file_description`, A.`file_tags`) AGAINST (? IN BOOLEAN MODE)
                        ORDER BY score DESC, A.`upload_date` DESC
                        LIMIT ?";
                $res = $db->Execute($sql, array($query . '*', $query . '*', $limit));
            } catch (Exception $e) {
                // Fallback to LIKE search
                $like = '%' . $query . '%';
                $sql = "SELECT A.`file_key`, A.`file_title` AS `title`, A.`file_description` AS `description`, 
                               A.`file_views` AS `views`, A.`file_tags` AS `tags`, B.`usr_user` AS `username`
                        FROM `db_videofiles` A
                        JOIN `db_accountuser` B ON A.`usr_id` = B.`usr_id`
                        WHERE A.`approved` = '1' AND A.`deleted` = '0' AND A.`active` = '1' 
                          AND A.`privacy` IN (0, '0', 'public')
                          AND (A.`file_title` LIKE ? OR A.`file_description` LIKE ? OR A.`file_tags` LIKE ?)
                        ORDER BY A.`upload_date` DESC
                        LIMIT ?";
                $res = $db->Execute($sql, array($like, $like, $like, $limit));
            }
            
            if ($res) {
                while (!$res->EOF) {
                    $rows[] = [
                        'file_key'    => $res->fields['file_key'],
                        'title'       => $res->fields['title'],
                        'description' => $res->fields['description'],
                        'views'       => (int) $res->fields['views'],
                        'tags'        => (string) $res->fields['tags'],
                        'username'    => $res->fields['username']
                    ];
                    $res->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logDatabaseError($e->getMessage(), $sql ?? '', [$query, $limit]);
        }
        
        return $rows;
    }

    /**
     * Get latest live streams within a time window
     * @param int $limit Maximum number of streams to return
     * @param int $time_window Time window in minutes (default: 5)
     * @return array Array of stream data
     */
    public function getLatestStreams($limit = 5, $time_window = 5)
    {
        global $db;
        $rows = [];
        
        try {
            // Validate inputs
            $limit = max(1, min(100, (int)$limit)); // Between 1 and 100
            $time_window = max(1, min(1440, (int)$time_window)); // Between 1 minute and 24 hours
            
            $sql = "SELECT A.`live_key` AS `stream_key`, A.`live_title` AS `title`, A.`live_description` AS `description`, 
                           A.`live_viewers` AS `views`, A.`live_tags` AS `tags`, B.`usr_user` AS `username`,
                           A.`date_created`
                    FROM `db_livefiles` A
                    JOIN `db_accountuser` B ON A.`usr_id` = B.`usr_id`
                    WHERE A.`approved` = '1' AND A.`deleted` = '0' AND A.`active` = '1' 
                      AND A.`privacy` IN (0, '0', 'public')
                      AND A.`date_created` >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
                    ORDER BY A.`date_created` DESC
                    LIMIT ?";
                    
            $res = $db->Execute($sql, array($time_window, $limit));
            
            if ($res) {
                while (!$res->EOF) {
                    $rows[] = [
                        'stream_key'  => $res->fields['stream_key'],
                        'title'       => $res->fields['title'],
                        'description' => $res->fields['description'],
                        'views'       => (int) $res->fields['views'],
                        'tags'        => (string) $res->fields['tags'],
                        'username'    => $res->fields['username'],
                        'date_created' => $res->fields['date_created']
                    ];
                    $res->MoveNext();
                }
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logDatabaseError($e->getMessage(), $sql ?? '', [$time_window, $limit]);
        }
        
        return $rows;
    }
}
