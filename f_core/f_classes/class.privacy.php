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
 * VPrivacy - Comprehensive Privacy and Access Control System
 * 
 * This class handles all aspects of privacy and access control:
 * - Site-wide access modes (public, members-only, invite-only)
 * - Content privacy levels
 * - User privacy preferences
 * - Geographic restrictions
 * - Age verification
 * - GDPR compliance
 * - Access logging
 */
class VPrivacy
{
    private static $instance = null;
    private $branding;
    private $currentUser = null;
    private $userPrivacyLevel = 'guest';
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->branding = VBranding::getInstance();
        $this->initializeUser();
    }
    
    /**
     * Initialize current user and privacy level
     */
    private function initializeUser()
    {
        if (isset($_SESSION['USER_ID']) && $_SESSION['USER_ID'] > 0) {
            $this->currentUser = $_SESSION['USER_ID'];
            $this->userPrivacyLevel = $this->getUserPrivacyLevel($_SESSION['USER_ID']);
        } else {
            $this->userPrivacyLevel = 'guest';
        }
    }
    
    /**
     * Get user's privacy level
     */
    private function getUserPrivacyLevel($userId)
    {
        global $db;
        
        try {
            // Check if user is admin
            $sql = "SELECT usr_status FROM db_accountuser WHERE usr_id = ?";
            $result = $db->Execute($sql, [$userId]);
            
            if ($result && !$result->EOF) {
                $status = $result->fields['usr_status'];
                if ($status == 'admin' || $status == 'administrator') {
                    return 'admin';
                }
                if ($status == 'premium') {
                    return 'premium';
                }
                if ($status == 'verified') {
                    return 'verified';
                }
                return 'member';
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get user privacy level: ' . $e->getMessage());
        }
        
        return 'guest';
    }
    
    /**
     * Check if site access is allowed
     */
    public function checkSiteAccess()
    {
        $accessMode = $this->branding->get('site_access_mode', 'public');
        $maintenanceMode = $this->branding->get('maintenance_mode', false);
        
        // Check maintenance mode first
        if ($maintenanceMode) {
            return $this->checkMaintenanceAccess();
        }
        
        switch ($accessMode) {
            case 'public':
                return ['allowed' => true];
                
            case 'members_only':
                if ($this->userPrivacyLevel === 'guest') {
                    return [
                        'allowed' => false,
                        'reason' => 'members_only',
                        'message' => $this->branding->get('members_only_message', 'This site is available to members only.')
                    ];
                }
                return ['allowed' => true];
                
            case 'invite_only':
                if ($this->userPrivacyLevel === 'guest' || !$this->hasValidInvite()) {
                    return [
                        'allowed' => false,
                        'reason' => 'invite_only',
                        'message' => 'This site is invite-only. Please contact an administrator for access.'
                    ];
                }
                return ['allowed' => true];
                
            default:
                return ['allowed' => true];
        }
    }
    
    /**
     * Check maintenance mode access
     */
    private function checkMaintenanceAccess()
    {
        // Allow admin access
        if ($this->userPrivacyLevel === 'admin') {
            return ['allowed' => true];
        }
        
        // Check allowed IPs
        $allowedIPs = $this->branding->get('maintenance_allowed_ips', '');
        if (!empty($allowedIPs)) {
            $userIP = $_SERVER['REMOTE_ADDR'];
            $allowedList = array_map('trim', explode(',', $allowedIPs));
            if (in_array($userIP, $allowedList)) {
                return ['allowed' => true];
            }
        }
        
        // Check bypass key
        $bypassKey = $this->branding->get('maintenance_bypass_key', '');
        if (!empty($bypassKey) && isset($_GET['bypass']) && $_GET['bypass'] === $bypassKey) {
            return ['allowed' => true];
        }
        
        return [
            'allowed' => false,
            'reason' => 'maintenance',
            'message' => $this->branding->get('maintenance_message', 'Site is currently under maintenance.')
        ];
    }
    
    /**
     * Check content access permissions
     */
    public function checkContentAccess($contentType, $contentId, $contentData = null)
    {
        // Get content privacy settings
        $privacySettings = $this->getContentPrivacySettings($contentType, $contentId);
        
        if (!$privacySettings) {
            // Use default privacy level
            $privacyLevel = $this->branding->get("default_{$contentType}_privacy", 'public');
        } else {
            $privacyLevel = $privacySettings['privacy_level'];
        }
        
        // Check basic privacy level
        $accessCheck = $this->checkPrivacyLevel($privacyLevel);
        if (!$accessCheck['allowed']) {
            $this->logAccess($contentType, $contentId, false, $accessCheck['reason']);
            return $accessCheck;
        }
        
        // Check additional restrictions
        if ($privacySettings) {
            // Password protection
            if ($privacySettings['password_protected']) {
                if (!$this->checkContentPassword($contentType, $contentId, $privacySettings['content_password'])) {
                    return [
                        'allowed' => false,
                        'reason' => 'password_required',
                        'message' => 'This content is password protected.'
                    ];
                }
            }
            
            // Age restrictions
            if ($privacySettings['age_restricted']) {
                $ageCheck = $this->checkAgeRestriction();
                if (!$ageCheck['allowed']) {
                    return $ageCheck;
                }
            }
            
            // Geographic restrictions
            if ($privacySettings['geo_restricted']) {
                $geoCheck = $this->checkGeographicRestriction($privacySettings);
                if (!$geoCheck['allowed']) {
                    return $geoCheck;
                }
            }
            
            // Scheduled content
            if ($privacySettings['scheduled_public'] && strtotime($privacySettings['scheduled_public']) > time()) {
                if ($this->userPrivacyLevel !== 'admin') {
                    return [
                        'allowed' => false,
                        'reason' => 'scheduled',
                        'message' => 'This content is not yet available.'
                    ];
                }
            }
            
            // Expired content
            if ($privacySettings['expires_at'] && strtotime($privacySettings['expires_at']) < time()) {
                return [
                    'allowed' => false,
                    'reason' => 'expired',
                    'message' => 'This content is no longer available.'
                ];
            }
        }
        
        $this->logAccess($contentType, $contentId, true);
        return ['allowed' => true];
    }
    
    /**
     * Check privacy level access
     */
    private function checkPrivacyLevel($privacyLevel)
    {
        switch ($privacyLevel) {
            case 'public':
                return ['allowed' => true];
                
            case 'members_only':
                if ($this->userPrivacyLevel === 'guest') {
                    return [
                        'allowed' => false,
                        'reason' => 'login_required',
                        'message' => $this->branding->get('login_required_message', 'Please log in to view this content.')
                    ];
                }
                return ['allowed' => true];
                
            case 'verified_only':
                if (!in_array($this->userPrivacyLevel, ['verified', 'premium', 'admin'])) {
                    return [
                        'allowed' => false,
                        'reason' => 'verification_required',
                        'message' => 'This content is available to verified users only.'
                    ];
                }
                return ['allowed' => true];
                
            case 'premium_only':
                if (!in_array($this->userPrivacyLevel, ['premium', 'admin'])) {
                    return [
                        'allowed' => false,
                        'reason' => 'premium_required',
                        'message' => 'This content is available to premium members only.'
                    ];
                }
                return ['allowed' => true];
                
            case 'admin_only':
                if ($this->userPrivacyLevel !== 'admin') {
                    return [
                        'allowed' => false,
                        'reason' => 'admin_required',
                        'message' => 'This content is restricted to administrators.'
                    ];
                }
                return ['allowed' => true];
                
            case 'private':
                return [
                    'allowed' => false,
                    'reason' => 'private',
                    'message' => $this->branding->get('private_content_message', 'This content is private.')
                ];
                
            case 'unlisted':
                // Unlisted content can be viewed if you have the direct link
                return ['allowed' => true];
                
            default:
                return ['allowed' => true];
        }
    }
    
    /**
     * Get content privacy settings
     */
    private function getContentPrivacySettings($contentType, $contentId)
    {
        global $db;
        
        try {
            $sql = "SELECT * FROM db_content_privacy WHERE content_type = ? AND content_id = ?";
            $result = $db->Execute($sql, [$contentType, $contentId]);
            
            if ($result && !$result->EOF) {
                return $result->fields;
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get content privacy settings: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Check content password
     */
    private function checkContentPassword($contentType, $contentId, $hashedPassword)
    {
        $sessionKey = "content_password_{$contentType}_{$contentId}";
        
        // Check if password already verified in session
        if (isset($_SESSION[$sessionKey]) && $_SESSION[$sessionKey] === true) {
            return true;
        }
        
        // Check submitted password
        if (isset($_POST['content_password'])) {
            $submittedPassword = $_POST['content_password'];
            if (password_verify($submittedPassword, $hashedPassword)) {
                $_SESSION[$sessionKey] = true;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check age restriction
     */
    private function checkAgeRestriction()
    {
        if (!$this->branding->get('enable_age_verification', false)) {
            return ['allowed' => true];
        }
        
        // Check if user has verified age
        if ($this->currentUser) {
            $ageVerified = $this->getUserPreference($this->currentUser, 'age_verified');
            if ($ageVerified) {
                return ['allowed' => true];
            }
        }
        
        // Check session for age verification
        if (isset($_SESSION['age_verified']) && $_SESSION['age_verified'] === true) {
            return ['allowed' => true];
        }
        
        return [
            'allowed' => false,
            'reason' => 'age_verification_required',
            'message' => $this->branding->get('age_restricted_message', 'This content is age-restricted.')
        ];
    }
    
    /**
     * Check geographic restriction
     */
    private function checkGeographicRestriction($privacySettings)
    {
        if (!$this->branding->get('enable_geo_blocking', false)) {
            return ['allowed' => true];
        }
        
        $userCountry = $this->getUserCountry();
        
        // Check blocked countries
        if (!empty($privacySettings['blocked_countries'])) {
            $blockedCountries = array_map('trim', explode(',', $privacySettings['blocked_countries']));
            if (in_array($userCountry, $blockedCountries)) {
                return [
                    'allowed' => false,
                    'reason' => 'geo_blocked',
                    'message' => $this->branding->get('geo_block_message', 'This content is not available in your region.')
                ];
            }
        }
        
        // Check allowed countries
        if (!empty($privacySettings['allowed_countries'])) {
            $allowedCountries = array_map('trim', explode(',', $privacySettings['allowed_countries']));
            if (!in_array($userCountry, $allowedCountries)) {
                return [
                    'allowed' => false,
                    'reason' => 'geo_blocked',
                    'message' => $this->branding->get('geo_block_message', 'This content is not available in your region.')
                ];
            }
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Get user's country from IP
     */
    private function getUserCountry()
    {
        // This is a simplified version - in production, you'd use a GeoIP service
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // For localhost/development
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return 'US'; // Default for development
        }
        
        // You would integrate with a GeoIP service here
        // For now, return a default
        return 'US';
    }
    
    /**
     * Set content privacy settings
     */
    public function setContentPrivacy($contentType, $contentId, $settings)
    {
        global $db;
        
        try {
            $sql = "INSERT INTO db_content_privacy 
                    (content_type, content_id, privacy_level, password_protected, content_password, 
                     age_restricted, geo_restricted, allowed_countries, blocked_countries, 
                     scheduled_public, expires_at, custom_message) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    privacy_level = VALUES(privacy_level),
                    password_protected = VALUES(password_protected),
                    content_password = VALUES(content_password),
                    age_restricted = VALUES(age_restricted),
                    geo_restricted = VALUES(geo_restricted),
                    allowed_countries = VALUES(allowed_countries),
                    blocked_countries = VALUES(blocked_countries),
                    scheduled_public = VALUES(scheduled_public),
                    expires_at = VALUES(expires_at),
                    custom_message = VALUES(custom_message),
                    updated_at = NOW()";
            
            $password = null;
            if (!empty($settings['password'])) {
                $password = password_hash($settings['password'], PASSWORD_DEFAULT);
            }
            
            return $db->Execute($sql, [
                $contentType,
                $contentId,
                $settings['privacy_level'] ?? 'public',
                $settings['password_protected'] ?? false,
                $password,
                $settings['age_restricted'] ?? false,
                $settings['geo_restricted'] ?? false,
                $settings['allowed_countries'] ?? '',
                $settings['blocked_countries'] ?? '',
                $settings['scheduled_public'] ?? null,
                $settings['expires_at'] ?? null,
                $settings['custom_message'] ?? ''
            ]);
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to set content privacy: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user privacy preference
     */
    public function getUserPreference($userId, $key)
    {
        global $db;
        
        try {
            $sql = "SELECT preference_value FROM db_user_privacy_preferences WHERE usr_id = ? AND preference_key = ?";
            $result = $db->Execute($sql, [$userId, $key]);
            
            if ($result && !$result->EOF) {
                return $result->fields['preference_value'];
            }
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get user preference: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Set user privacy preference
     */
    public function setUserPreference($userId, $key, $value)
    {
        global $db;
        
        try {
            $sql = "INSERT INTO db_user_privacy_preferences (usr_id, preference_key, preference_value) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE preference_value = VALUES(preference_value), updated_at = NOW()";
            
            return $db->Execute($sql, [$userId, $key, $value]);
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to set user preference: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Log access attempt
     */
    private function logAccess($contentType, $contentId, $granted, $reason = null)
    {
        global $db;
        
        try {
            $sql = "INSERT INTO db_privacy_access_logs 
                    (usr_id, ip_address, user_agent, access_type, content_type, content_id, 
                     access_granted, denial_reason, privacy_level_required, user_privacy_level) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $db->Execute($sql, [
                $this->currentUser,
                $_SERVER['REMOTE_ADDR'],
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                'view',
                $contentType,
                $contentId,
                $granted ? 1 : 0,
                $reason,
                '', // Would be filled based on content settings
                $this->userPrivacyLevel
            ]);
        } catch (Exception $e) {
            // Fail silently for logging
        }
    }
    
    /**
     * Check if user has valid invite
     */
    private function hasValidInvite()
    {
        // This would check for valid invite codes
        // Implementation depends on your invite system
        return false;
    }
    
    /**
     * Generate privacy-aware content list
     */
    public function filterContentList($contentList, $contentType = 'video')
    {
        $filteredList = [];
        
        foreach ($contentList as $content) {
            $accessCheck = $this->checkContentAccess($contentType, $content['id'], $content);
            
            if ($accessCheck['allowed']) {
                $filteredList[] = $content;
            } else {
                // Optionally include placeholder for restricted content
                if ($this->branding->get('show_content_previews', true)) {
                    $content['restricted'] = true;
                    $content['restriction_reason'] = $accessCheck['reason'];
                    $content['restriction_message'] = $accessCheck['message'];
                    $filteredList[] = $content;
                }
            }
        }
        
        return $filteredList;
    }
    
    /**
     * Get privacy statistics for admin
     */
    public function getPrivacyStatistics()
    {
        global $db;
        $stats = [];
        
        try {
            // Access attempts in last 24 hours
            $sql = "SELECT COUNT(*) as total, 
                           SUM(access_granted) as granted,
                           SUM(CASE WHEN access_granted = 0 THEN 1 ELSE 0 END) as denied
                    FROM db_privacy_access_logs 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $result = $db->Execute($sql);
            
            if ($result && !$result->EOF) {
                $stats['access_attempts_24h'] = [
                    'total' => (int) $result->fields['total'],
                    'granted' => (int) $result->fields['granted'],
                    'denied' => (int) $result->fields['denied']
                ];
            }
            
            // Content privacy distribution
            $sql = "SELECT privacy_level, COUNT(*) as count 
                    FROM db_content_privacy 
                    GROUP BY privacy_level";
            $result = $db->Execute($sql);
            
            $privacyDistribution = [];
            if ($result) {
                while (!$result->EOF) {
                    $privacyDistribution[$result->fields['privacy_level']] = (int) $result->fields['count'];
                    $result->MoveNext();
                }
            }
            $stats['privacy_distribution'] = $privacyDistribution;
            
        } catch (Exception $e) {
            $logger = VLogger::getInstance();
            $logger->logError('Failed to get privacy statistics: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get current user's privacy level
     */
    public function getCurrentUserPrivacyLevel()
    {
        return $this->userPrivacyLevel;
    }
    
    /**
     * Check if feature is accessible
     */
    public function checkFeatureAccess($featureName)
    {
        $featureSettings = [
            'search' => $this->branding->get('search_requires_login', false),
            'trending' => $this->branding->get('trending_requires_login', false),
            'categories' => $this->branding->get('categories_require_login', false),
            'recommendations' => $this->branding->get('recommendations_require_login', true),
            'upload' => true, // Always requires login
            'comments' => true, // Always requires login
        ];
        
        $requiresLogin = $featureSettings[$featureName] ?? false;
        
        if ($requiresLogin && $this->userPrivacyLevel === 'guest') {
            return [
                'allowed' => false,
                'reason' => 'login_required',
                'message' => $this->branding->get('login_required_message', 'Please log in to use this feature.')
            ];
        }
        
        return ['allowed' => true];
    }
}