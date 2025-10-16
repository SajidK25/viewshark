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

/**
 * Enhanced IP Tracking and Banning System
 */
class VIPTracker
{
    /**
     * Log IP activity with detailed information
     * @param string $action Action performed
     * @param array $context Additional context
     * @param string $ip IP address (auto-detected if not provided)
     */
    public static function logActivity($action, $context = [], $ip = null)
    {
        global $db;
        
        if ($ip === null) {
            $ip = VIPaccess::getUserIP();
        }
        
        $user_id = $_SESSION['USER_ID'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Get geolocation info (if available)
        $geo_info = self::getGeoLocation($ip);
        
        $insert_data = [
            'ip_address' => $ip,
            'user_id' => $user_id,
            'action' => $action,
            'context' => json_encode($context),
            'user_agent' => $user_agent,
            'referer' => $referer,
            'request_uri' => $request_uri,
            'country' => $geo_info['country'] ?? null,
            'city' => $geo_info['city'] ?? null,
            'timestamp' => date('Y-m-d H:i:s'),
            'session_id' => session_id()
        ];
        
        // Create table if it doesn't exist
        self::createTrackingTable();
        
        try {
            global $class_database;
            $class_database->doInsert('db_ip_tracking', $insert_data);
        } catch (Exception $e) {
            error_log("IP Tracking Error: " . $e->getMessage());
        }
    }
    
    /**
     * Ban an IP address with reason and duration
     * @param string $ip IP address to ban
     * @param string $reason Reason for ban
     * @param int $duration Duration in hours (0 = permanent)
     * @param string $banned_by Admin who issued the ban
     * @return bool Success status
     */
    public static function banIP($ip, $reason = 'Violation of terms', $duration = 0, $banned_by = null)
    {
        global $db, $class_database;
        
        // Validate IP
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return false;
        }
        
        $expires_at = $duration > 0 ? date('Y-m-d H:i:s', time() + ($duration * 3600)) : null;
        $banned_by = $banned_by ?? ($_SESSION['ADMIN_NAME'] ?? 'System');
        
        $ban_data = [
            'ban_ip' => $ip,
            'ban_reason' => $reason,
            'ban_active' => 1,
            'ban_date' => date('Y-m-d H:i:s'),
            'ban_expires' => $expires_at,
            'banned_by' => $banned_by
        ];
        
        // Create ban table if it doesn't exist
        self::createBanTable();
        
        try {
            // Check if IP is already banned
            $existing = $class_database->singleFieldValue('db_banlist', 'ban_id', 'ban_ip', $ip);
            
            if ($existing) {
                // Update existing ban
                return $class_database->doUpdate('db_banlist', 'ban_ip', $ban_data, null);
            } else {
                // Insert new ban
                return $class_database->doInsert('db_banlist', $ban_data);
            }
        } catch (Exception $e) {
            error_log("IP Ban Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Unban an IP address
     * @param string $ip IP address to unban
     * @return bool Success status
     */
    public static function unbanIP($ip)
    {
        global $db;
        
        try {
            $sql = "UPDATE `db_banlist` SET `ban_active` = 0, `unban_date` = NOW() WHERE `ban_ip` = ?";
            $result = $db->Execute($sql, [$ip]);
            return $db->Affected_Rows() > 0;
        } catch (Exception $e) {
            error_log("IP Unban Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if IP is currently banned
     * @param string $ip IP address to check
     * @return array|false Ban info or false if not banned
     */
    public static function isBanned($ip)
    {
        global $db;
        
        try {
            $sql = "SELECT * FROM `db_banlist` 
                    WHERE `ban_ip` = ? 
                    AND `ban_active` = 1 
                    AND (`ban_expires` IS NULL OR `ban_expires` > NOW())";
            
            $result = $db->Execute($sql, [$ip]);
            
            if ($result && !$result->EOF) {
                return [
                    'banned' => true,
                    'reason' => $result->fields['ban_reason'],
                    'ban_date' => $result->fields['ban_date'],
                    'expires' => $result->fields['ban_expires'],
                    'banned_by' => $result->fields['banned_by']
                ];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("IP Ban Check Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get IP activity statistics
     * @param string $ip IP address
     * @param int $hours Hours to look back (default 24)
     * @return array Activity statistics
     */
    public static function getIPStats($ip, $hours = 24)
    {
        global $db;
        
        try {
            $sql = "SELECT 
                        COUNT(*) as total_requests,
                        COUNT(DISTINCT action) as unique_actions,
                        COUNT(DISTINCT user_id) as unique_users,
                        MIN(timestamp) as first_seen,
                        MAX(timestamp) as last_seen,
                        country,
                        city
                    FROM `db_ip_tracking` 
                    WHERE `ip_address` = ? 
                    AND `timestamp` >= DATE_SUB(NOW(), INTERVAL ? HOUR)
                    GROUP BY ip_address";
            
            $result = $db->Execute($sql, [$ip, $hours]);
            
            if ($result && !$result->EOF) {
                return [
                    'total_requests' => (int)$result->fields['total_requests'],
                    'unique_actions' => (int)$result->fields['unique_actions'],
                    'unique_users' => (int)$result->fields['unique_users'],
                    'first_seen' => $result->fields['first_seen'],
                    'last_seen' => $result->fields['last_seen'],
                    'country' => $result->fields['country'],
                    'city' => $result->fields['city']
                ];
            }
            
            return ['total_requests' => 0];
        } catch (Exception $e) {
            error_log("IP Stats Error: " . $e->getMessage());
            return ['total_requests' => 0];
        }
    }
    
    /**
     * Detect suspicious activity patterns
     * @param string $ip IP address
     * @return array Threat assessment
     */
    public static function detectThreats($ip)
    {
        global $db;
        
        $threats = [];
        $threat_level = 0;
        
        try {
            // Check request rate (last hour)
            $sql = "SELECT COUNT(*) as requests 
                    FROM `db_ip_tracking` 
                    WHERE `ip_address` = ? 
                    AND `timestamp` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            
            $result = $db->Execute($sql, [$ip]);
            $hourly_requests = $result->fields['requests'] ?? 0;
            
            if ($hourly_requests > 1000) {
                $threats[] = 'High request rate: ' . $hourly_requests . ' requests/hour';
                $threat_level += 3;
            } elseif ($hourly_requests > 500) {
                $threats[] = 'Elevated request rate: ' . $hourly_requests . ' requests/hour';
                $threat_level += 2;
            }
            
            // Check for failed login attempts
            $sql = "SELECT COUNT(*) as failed_logins 
                    FROM `db_ip_tracking` 
                    WHERE `ip_address` = ? 
                    AND `action` = 'login_failed'
                    AND `timestamp` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            
            $result = $db->Execute($sql, [$ip]);
            $failed_logins = $result->fields['failed_logins'] ?? 0;
            
            if ($failed_logins > 10) {
                $threats[] = 'Brute force attempt: ' . $failed_logins . ' failed logins';
                $threat_level += 4;
            } elseif ($failed_logins > 5) {
                $threats[] = 'Multiple failed logins: ' . $failed_logins;
                $threat_level += 2;
            }
            
            // Check for suspicious user agents
            $sql = "SELECT DISTINCT user_agent 
                    FROM `db_ip_tracking` 
                    WHERE `ip_address` = ? 
                    AND `timestamp` >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            
            $result = $db->Execute($sql, [$ip]);
            $suspicious_agents = [];
            
            while ($result && !$result->EOF) {
                $ua = strtolower($result->fields['user_agent']);
                if (strpos($ua, 'bot') !== false || 
                    strpos($ua, 'crawler') !== false || 
                    strpos($ua, 'spider') !== false ||
                    strpos($ua, 'scraper') !== false) {
                    $suspicious_agents[] = $result->fields['user_agent'];
                }
                $result->MoveNext();
            }
            
            if (!empty($suspicious_agents)) {
                $threats[] = 'Bot/crawler activity detected';
                $threat_level += 1;
            }
            
        } catch (Exception $e) {
            error_log("Threat Detection Error: " . $e->getMessage());
        }
        
        return [
            'threat_level' => $threat_level,
            'threats' => $threats,
            'risk_assessment' => self::getRiskLevel($threat_level)
        ];
    }
    
    /**
     * Auto-ban based on threat detection
     * @param string $ip IP address
     * @return bool True if banned
     */
    public static function autoBan($ip)
    {
        $threats = self::detectThreats($ip);
        
        if ($threats['threat_level'] >= 5) {
            $reason = 'Auto-ban: ' . implode(', ', $threats['threats']);
            return self::banIP($ip, $reason, 24, 'Auto-System'); // 24 hour ban
        }
        
        return false;
    }
    
    /**
     * Get geolocation information for IP
     * @param string $ip IP address
     * @return array Geo information
     */
    private static function getGeoLocation($ip)
    {
        // Basic implementation - you can integrate with services like:
        // - MaxMind GeoIP2
        // - IP-API
        // - ipinfo.io
        
        // For now, return empty array
        // You can implement actual geolocation service here
        return [];
    }
    
    /**
     * Get risk level description
     * @param int $threat_level Numeric threat level
     * @return string Risk description
     */
    private static function getRiskLevel($threat_level)
    {
        if ($threat_level >= 5) return 'HIGH';
        if ($threat_level >= 3) return 'MEDIUM';
        if ($threat_level >= 1) return 'LOW';
        return 'NONE';
    }
    
    /**
     * Create IP tracking table if it doesn't exist
     */
    private static function createTrackingTable()
    {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS `db_ip_tracking` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip_address` varchar(45) NOT NULL,
            `user_id` int(11) DEFAULT NULL,
            `action` varchar(100) NOT NULL,
            `context` text,
            `user_agent` text,
            `referer` varchar(500) DEFAULT NULL,
            `request_uri` varchar(500) DEFAULT NULL,
            `country` varchar(2) DEFAULT NULL,
            `city` varchar(100) DEFAULT NULL,
            `timestamp` datetime NOT NULL,
            `session_id` varchar(128) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_ip_timestamp` (`ip_address`, `timestamp`),
            KEY `idx_user_timestamp` (`user_id`, `timestamp`),
            KEY `idx_action` (`action`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->Execute($sql);
        } catch (Exception $e) {
            error_log("Create Tracking Table Error: " . $e->getMessage());
        }
    }
    
    /**
     * Create ban table if it doesn't exist
     */
    private static function createBanTable()
    {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS `db_banlist` (
            `ban_id` int(11) NOT NULL AUTO_INCREMENT,
            `ban_ip` varchar(45) NOT NULL,
            `ban_reason` varchar(500) NOT NULL,
            `ban_active` tinyint(1) NOT NULL DEFAULT 1,
            `ban_date` datetime NOT NULL,
            `ban_expires` datetime DEFAULT NULL,
            `unban_date` datetime DEFAULT NULL,
            `banned_by` varchar(100) NOT NULL,
            PRIMARY KEY (`ban_id`),
            UNIQUE KEY `idx_ip` (`ban_ip`),
            KEY `idx_active_expires` (`ban_active`, `ban_expires`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->Execute($sql);
        } catch (Exception $e) {
            error_log("Create Ban Table Error: " . $e->getMessage());
        }
    }
}