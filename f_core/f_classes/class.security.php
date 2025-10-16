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
     * Rate limiting check
     * @param string $key Unique identifier (IP, user ID, etc.)
     * @param int $maxAttempts Maximum attempts
     * @param int $timeWindow Time window in seconds
     * @return bool True if within limits
     */
    public static function checkRateLimit($key, $maxAttempts = 10, $timeWindow = 300)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $now = time();
        $rateLimitKey = 'rate_limit_' . $key;
        
        if (!isset($_SESSION[$rateLimitKey])) {
            $_SESSION[$rateLimitKey] = [];
        }
        
        // Clean old attempts
        $_SESSION[$rateLimitKey] = array_filter($_SESSION[$rateLimitKey], function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Check if limit exceeded
        if (count($_SESSION[$rateLimitKey]) >= $maxAttempts) {
            return false;
        }
        
        // Add current attempt
        $_SESSION[$rateLimitKey][] = $now;
        
        return true;
    }
}