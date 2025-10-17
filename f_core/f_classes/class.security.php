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
 * Enhanced Security Class for Input Validation, Sanitization, and CSRF Protection
 */
class VSecurity
{
    private static $instance = null;
    private $csrfTokens = [];
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Sanitize and validate GET parameters
     * @param string $key Parameter name
     * @param string $type Expected type (int, string, email, url, alpha, alphanum)
     * @param mixed $default Default value if validation fails
     * @param array $options Additional validation options
     * @return mixed Sanitized value or default
     */
    public static function getParam($key, $type = 'string', $default = null, $options = [])
    {
        if (!isset($_GET[$key])) {
            return $default;
        }
        
        return self::validateInput($_GET[$key], $type, $default, $options);
    }
    
    /**
     * Sanitize and validate POST parameters
     * @param string $key Parameter name
     * @param string $type Expected type
     * @param mixed $default Default value if validation fails
     * @param array $options Additional validation options
     * @return mixed Sanitized value or default
     */
    public static function postParam($key, $type = 'string', $default = null, $options = [])
    {
        if (!isset($_POST[$key])) {
            return $default;
        }
        
        return self::validateInput($_POST[$key], $type, $default, $options);
    }
    
    /**
     * Validate and sanitize input based on type
     * @param mixed $input Input value
     * @param string $type Expected type
     * @param mixed $default Default value
     * @param array $options Additional options
     * @return mixed Sanitized value
     */
    public static function validateInput($input, $type, $default = null, $options = [])
    {
        // Basic sanitization first
        if (is_string($input)) {
            $input = trim($input);
        }
        
        switch ($type) {
            case 'int':
            case 'integer':
                $value = filter_var($input, FILTER_VALIDATE_INT);
                if ($value === false) return $default;
                
                // Check min/max if provided
                if (isset($options['min']) && $value < $options['min']) return $default;
                if (isset($options['max']) && $value > $options['max']) return $default;
                
                return $value;
                
            case 'float':
            case 'decimal':
                $value = filter_var($input, FILTER_VALIDATE_FLOAT);
                return $value !== false ? $value : $default;
                
            case 'email':
                $value = filter_var($input, FILTER_VALIDATE_EMAIL);
                return $value !== false ? $value : $default;
                
            case 'url':
                $value = filter_var($input, FILTER_VALIDATE_URL);
                return $value !== false ? $value : $default;
                
            case 'alpha':
                $value = preg_replace('/[^a-zA-Z]/', '', $input);
                return !empty($value) ? $value : $default;
                
            case 'alphanum':
                $value = preg_replace('/[^a-zA-Z0-9]/', '', $input);
                return !empty($value) ? $value : $default;
                
            case 'slug':
                $value = preg_replace('/[^a-zA-Z0-9\-_]/', '', $input);
                return !empty($value) ? $value : $default;
                
            case 'filename':
                $value = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $input);
                return !empty($value) ? $value : $default;
                
            case 'boolean':
            case 'bool':
                return filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
                
            case 'html':
                // Use existing VFilter for HTML content
                $filter = new VFilter();
                return $filter->sanitize($input);
                
            case 'string':
            default:
                // Basic string sanitization
                $value = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
                
                // Check length if provided
                if (isset($options['max_length']) && strlen($value) > $options['max_length']) {
                    return $default;
                }
                if (isset($options['min_length']) && strlen($value) < $options['min_length']) {
                    return $default;
                }
                
                return $value;
        }
    }
    
    /**
     * Generate CSRF token
     * @param string $action Action name for token scope
     * @return string CSRF token
     */
    public static function generateCSRFToken($action = 'default')
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_tokens'][$action] = $token;
        
        // Clean old tokens (keep only last 10)
        if (isset($_SESSION['csrf_tokens']) && count($_SESSION['csrf_tokens']) > 10) {
            $_SESSION['csrf_tokens'] = array_slice($_SESSION['csrf_tokens'], -10, 10, true);
        }
        
        return $token;
    }
    
    /**
     * Validate CSRF token
     * @param string $token Token to validate
     * @param string $action Action name for token scope
     * @return bool True if valid
     */
    public static function validateCSRFToken($token, $action = 'default')
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_tokens'][$action])) {
            return false;
        }
        
        $isValid = hash_equals($_SESSION['csrf_tokens'][$action], $token);
        
        // Remove token after use (one-time use)
        unset($_SESSION['csrf_tokens'][$action]);
        
        return $isValid;
    }
    
    /**
     * Get CSRF token HTML input field
     * @param string $action Action name
     * @return string HTML input field
     */
    public static function getCSRFField($action = 'default')
    {
        $token = self::generateCSRFToken($action);
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * Validate CSRF token from POST data
     * @param string $action Action name
     * @return bool True if valid
     */
    public static function validateCSRFFromPost($action = 'default')
    {
        $token = $_POST['csrf_token'] ?? '';
        return self::validateCSRFToken($token, $action);
    }
    
    /**
     * Escape output for safe HTML display
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeOutput($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Escape for JavaScript output
     * @param string $string String to escape
     * @return string Escaped string
     */
    public static function escapeJS($string)
    {
        return json_encode($string, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    }
    
    /**
     * Validate file upload
     * @param array $file $_FILES array element
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes
     * @return array Result with 'valid' boolean and 'error' message
     */
    public static function validateFileUpload($file, $allowedTypes = [], $maxSize = 10485760)
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['valid' => false, 'error' => 'No file uploaded or invalid upload'];
        }
        
        // Check file size
        if ($file['size'] > $maxSize) {
            return ['valid' => false, 'error' => 'File too large'];
        }
        
        // Check MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
            return ['valid' => false, 'error' => 'Invalid file type'];
        }
        
        return ['valid' => true, 'mime_type' => $mimeType];
    }
    
    /**
     * Enhanced rate limiting check with Redis support
     * @param string $key Unique identifier (IP, user ID, etc.)
     * @param int $maxAttempts Maximum attempts
     * @param int $timeWindow Time window in seconds
     * @param string $action Action being rate limited (for logging)
     * @return bool True if within limits
     */
    public static function checkRateLimit($key, $maxAttempts = 10, $timeWindow = 300, $action = 'unknown')
    {
        $now = time();
        $rateLimitKey = 'rate_limit_' . $key;
        
        // Try Redis first for better performance and persistence
        if (self::useRedisRateLimit($rateLimitKey, $maxAttempts, $timeWindow, $now)) {
            return true;
        }
        
        // Fallback to session-based rate limiting
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (!isset($_SESSION[$rateLimitKey])) {
            $_SESSION[$rateLimitKey] = [];
        }
        
        // Clean old attempts
        $_SESSION[$rateLimitKey] = array_filter($_SESSION[$rateLimitKey], function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Check if limit exceeded
        if (count($_SESSION[$rateLimitKey]) >= $maxAttempts) {
            // Log rate limit violation
            $logger = VLogger::getInstance();
            $logger->logSecurityEvent("Rate limit exceeded for key: {$key}, action: {$action}", [
                'key' => $key,
                'action' => $action,
                'max_attempts' => $maxAttempts,
                'time_window' => $timeWindow,
                'current_attempts' => count($_SESSION[$rateLimitKey])
            ]);
            
            return false;
        }
        
        // Add current attempt
        $_SESSION[$rateLimitKey][] = $now;
        
        return true;
    }
    
    /**
     * Redis-based rate limiting
     * @param string $key Rate limit key
     * @param int $maxAttempts Maximum attempts
     * @param int $timeWindow Time window in seconds
     * @param int $now Current timestamp
     * @return bool|null True if allowed, false if exceeded, null if Redis unavailable
     */
    private static function useRedisRateLimit($key, $maxAttempts, $timeWindow, $now)
    {
        try {
            $redis = self::getRedisConnection();
            if (!$redis) {
                return null;
            }
            
            // Use Redis sorted set for sliding window rate limiting
            $redisKey = 'rl:' . $key;
            
            // Remove old entries
            $redis->zRemRangeByScore($redisKey, 0, $now - $timeWindow);
            
            // Count current attempts
            $currentAttempts = $redis->zCard($redisKey);
            
            if ($currentAttempts >= $maxAttempts) {
                // Log rate limit violation
                $logger = VLogger::getInstance();
                $logger->logSecurityEvent("Redis rate limit exceeded for key: {$key}", [
                    'key' => $key,
                    'max_attempts' => $maxAttempts,
                    'time_window' => $timeWindow,
                    'current_attempts' => $currentAttempts
                ]);
                
                return false;
            }
            
            // Add current attempt
            $redis->zAdd($redisKey, $now, uniqid());
            $redis->expire($redisKey, $timeWindow);
            
            return true;
            
        } catch (Exception $e) {
            // Redis failed, fall back to session-based rate limiting
            return null;
        }
    }
    
    /**
     * Get Redis connection
     * @return Redis|null
     */
    private static function getRedisConnection()
    {
        static $redis = null;
        static $connectionAttempted = false;
        
        if ($connectionAttempted && $redis === null) {
            return null;
        }
        
        if ($redis !== null) {
            return $redis;
        }
        
        $connectionAttempted = true;
        
        try {
            if (!class_exists('Redis')) {
                return null;
            }
            
            $redis = new Redis();
            $host = getenv('REDIS_HOST') ?: 'redis';
            $port = (int)(getenv('REDIS_PORT') ?: 6379);
            $db = (int)(getenv('REDIS_DB') ?: 0);
            
            if (!$redis->connect($host, $port, 2)) {
                return null;
            }
            
            if ($db > 0) {
                $redis->select($db);
            }
            
            return $redis;
            
        } catch (Exception $e) {
            return null;
        }
    }
    
    /**
     * Advanced security monitoring
     * @param string $event Security event type
     * @param array $context Additional context
     */
    public static function logSecurityEvent($event, $context = [])
    {
        $logger = VLogger::getInstance();
        
        $securityContext = array_merge($context, [
            'ip' => self::getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'user_id' => $_SESSION['USER_ID'] ?? null,
            'session_id' => session_id() ?: null,
            'timestamp' => date('Y-m-d H:i:s'),
            'security_event' => true
        ]);
        
        $logger->logSecurityEvent($event, $securityContext);
    }
    
    /**
     * Get client IP address with proxy support
     * @return string
     */
    private static function getClientIP()
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Check for suspicious activity patterns
     * @param string $userId User ID or IP
     * @param string $action Action being performed
     * @return bool True if activity seems suspicious
     */
    public static function detectSuspiciousActivity($userId, $action)
    {
        $suspiciousPatterns = [
            'rapid_requests' => ['limit' => 100, 'window' => 60],
            'failed_logins' => ['limit' => 10, 'window' => 300],
            'password_resets' => ['limit' => 5, 'window' => 3600],
            'file_uploads' => ['limit' => 50, 'window' => 3600]
        ];
        
        if (isset($suspiciousPatterns[$action])) {
            $pattern = $suspiciousPatterns[$action];
            $key = "suspicious_{$action}_{$userId}";
            
            if (!self::checkRateLimit($key, $pattern['limit'], $pattern['window'], $action)) {
                self::logSecurityEvent("Suspicious activity detected: {$action}", [
                    'user_id' => $userId,
                    'action' => $action,
                    'pattern' => $pattern
                ]);
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Enhanced file upload validation with security scanning
     * @param array $file $_FILES array element
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size in bytes
     * @param bool $scanForMalware Enable malware scanning
     * @return array Result with 'valid' boolean and 'error' message
     */
    public static function validateFileUploadAdvanced($file, $allowedTypes = [], $maxSize = 10485760, $scanForMalware = true)
    {
        $result = self::validateFileUpload($file, $allowedTypes, $maxSize);
        
        if (!$result['valid']) {
            return $result;
        }
        
        // Additional security checks
        $filename = $file['name'] ?? '';
        $tmpName = $file['tmp_name'] ?? '';
        
        // Check for dangerous file extensions
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'pht', 'phar', 'exe', 'bat', 'cmd', 'scr', 'vbs', 'js', 'jar'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($extension, $dangerousExtensions)) {
            self::logSecurityEvent("Dangerous file upload attempt", [
                'filename' => $filename,
                'extension' => $extension,
                'mime_type' => $result['mime_type']
            ]);
            
            return ['valid' => false, 'error' => 'File type not allowed for security reasons'];
        }
        
        // Check file content for embedded scripts
        if ($scanForMalware && is_file($tmpName)) {
            $content = file_get_contents($tmpName, false, null, 0, 8192); // Read first 8KB
            
            $maliciousPatterns = [
                '/<\?php/i',
                '/<script/i',
                '/eval\s*\(/i',
                '/exec\s*\(/i',
                '/system\s*\(/i',
                '/shell_exec\s*\(/i',
                '/base64_decode\s*\(/i'
            ];
            
            foreach ($maliciousPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    self::logSecurityEvent("Malicious content detected in upload", [
                        'filename' => $filename,
                        'pattern' => $pattern,
                        'mime_type' => $result['mime_type']
                    ]);
                    
                    return ['valid' => false, 'error' => 'File contains potentially malicious content'];
                }
            }
        }
        
        return $result;
    }
}