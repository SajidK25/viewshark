<?php
namespace Donations;

class ApiKeyManager {
    private $db;
    private $rate_limit = 100; // requests per minute
    private $rate_window = 60; // seconds

    public function __construct() {
        $this->db = db();
    }

    /**
     * Generate a new API key
     */
    public function generateKey($user_id, $name, $description = '') {
        $api_key = bin2hex(random_bytes(32));
        
        $sql = "INSERT INTO api_keys (user_id, api_key, name, description) 
                VALUES (?, ?, ?, ?)";
        
        $this->db->query($sql, [$user_id, $api_key, $name, $description]);
        
        return $api_key;
    }

    /**
     * Validate an API key
     */
    public function validateKey($api_key) {
        $sql = "SELECT user_id FROM api_keys 
                WHERE api_key = ? AND is_active = 1";
        
        $result = $this->db->getRow($sql, [$api_key]);
        
        if ($result) {
            $this->updateLastUsed($api_key);
            return $result['user_id'];
        }
        
        return false;
    }

    /**
     * Update last used timestamp
     */
    private function updateLastUsed($api_key) {
        $sql = "UPDATE api_keys 
                SET last_used = CURRENT_TIMESTAMP 
                WHERE api_key = ?";
        
        $this->db->query($sql, [$api_key]);
    }

    /**
     * Check rate limit
     */
    public function checkRateLimit($api_key) {
        $sql = "SELECT request_count, window_start 
                FROM api_rate_limits 
                WHERE api_key = ? 
                ORDER BY window_start DESC 
                LIMIT 1";
        
        $result = $this->db->getRow($sql, [$api_key]);
        
        if (!$result) {
            $this->createRateLimitWindow($api_key);
            return true;
        }
        
        $window_start = strtotime($result['window_start']);
        $current_time = time();
        
        // If window has expired, create new window
        if ($current_time - $window_start >= $this->rate_window) {
            $this->createRateLimitWindow($api_key);
            return true;
        }
        
        // Check if rate limit exceeded
        if ($result['request_count'] >= $this->rate_limit) {
            return false;
        }
        
        // Increment request count
        $sql = "UPDATE api_rate_limits 
                SET request_count = request_count + 1 
                WHERE api_key = ? AND window_start = ?";
        
        $this->db->query($sql, [$api_key, $result['window_start']]);
        
        return true;
    }

    /**
     * Create new rate limit window
     */
    private function createRateLimitWindow($api_key) {
        $sql = "INSERT INTO api_rate_limits (api_key, request_count, window_start) 
                VALUES (?, 1, CURRENT_TIMESTAMP)";
        
        $this->db->query($sql, [$api_key]);
    }

    /**
     * Get user's API keys
     */
    public function getUserKeys($user_id) {
        $sql = "SELECT key_id, api_key, name, description, is_active, 
                       last_used, created_at 
                FROM api_keys 
                WHERE user_id = ? 
                ORDER BY created_at DESC";
        
        return $this->db->getRows($sql, [$user_id]);
    }

    /**
     * Deactivate API key
     */
    public function deactivateKey($key_id, $user_id) {
        $sql = "UPDATE api_keys 
                SET is_active = 0 
                WHERE key_id = ? AND user_id = ?";
        
        return $this->db->query($sql, [$key_id, $user_id]);
    }

    /**
     * Reactivate API key
     */
    public function reactivateKey($key_id, $user_id) {
        $sql = "UPDATE api_keys 
                SET is_active = 1 
                WHERE key_id = ? AND user_id = ?";
        
        return $this->db->query($sql, [$key_id, $user_id]);
    }

    /**
     * Delete API key
     */
    public function deleteKey($key_id, $user_id) {
        $sql = "DELETE FROM api_keys 
                WHERE key_id = ? AND user_id = ?";
        
        return $this->db->query($sql, [$key_id, $user_id]);
    }

    /**
     * Clean up old rate limit records
     */
    public function cleanupRateLimits() {
        $sql = "DELETE FROM api_rate_limits 
                WHERE window_start < DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        return $this->db->query($sql);
    }
} 