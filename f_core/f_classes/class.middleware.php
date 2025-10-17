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
 * Middleware System for Route Protection and Request Processing
 */
class VMiddleware
{
    private static $instance = null;
    private $auth;
    private $rbac;
    private $security;
    private $logger;
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->auth = VAuth::getInstance();
        $this->rbac = VRBAC::getInstance();
        $this->security = VSecurity::getInstance();
        $this->logger = VLogger::getInstance();
    }
    
    /**
     * Authentication middleware - requires user to be logged in
     * @param callable|null $callback Optional callback to execute if authenticated
     * @return bool True if authenticated
     */
    public function requireAuth($callback = null)
    {
        if (!$this->auth->isAuthenticated()) {
            $this->handleUnauthenticated();
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Guest middleware - requires user to NOT be logged in
     * @param callable|null $callback Optional callback to execute if guest
     * @return bool True if guest
     */
    public function requireGuest($callback = null)
    {
        if ($this->auth->isAuthenticated()) {
            $this->handleAlreadyAuthenticated();
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Role middleware - requires specific role or higher
     * @param string $requiredRole Required role
     * @param callable|null $callback Optional callback to execute if authorized
     * @return bool True if authorized
     */
    public function requireRole($requiredRole, $callback = null)
    {
        if (!$this->rbac->requireRole($requiredRole)) {
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Permission middleware - requires specific permission
     * @param string|array $requiredPermissions Required permission(s)
     * @param array $context Additional context for permission check
     * @param callable|null $callback Optional callback to execute if authorized
     * @return bool True if authorized
     */
    public function requirePermission($requiredPermissions, $context = [], $callback = null)
    {
        if (!$this->rbac->requirePermission($requiredPermissions, $context)) {
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * CSRF middleware - validates CSRF token
     * @param string $action CSRF action name
     * @param callable|null $callback Optional callback to execute if valid
     * @return bool True if valid
     */
    public function requireCSRF($action = 'default', $callback = null)
    {
        if (!VSecurity::validateCSRFFromPost($action)) {
            $this->handleCSRFFailure();
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Rate limiting middleware
     * @param string $key Rate limit key
     * @param int $maxAttempts Maximum attempts
     * @param int $timeWindow Time window in seconds
     * @param string $action Action being rate limited
     * @param callable|null $callback Optional callback to execute if within limits
     * @return bool True if within limits
     */
    public function requireRateLimit($key, $maxAttempts = 10, $timeWindow = 300, $action = 'request', $callback = null)
    {
        if (!VSecurity::checkRateLimit($key, $maxAttempts, $timeWindow, $action)) {
            $this->handleRateLimitExceeded($action);
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Content ownership middleware - checks if user owns the content
     * @param int $contentId Content ID
     * @param string $contentType Content type (video, stream, etc.)
     * @param callable|null $callback Optional callback to execute if owner
     * @return bool True if owner or has permission
     */
    public function requireContentOwnership($contentId, $contentType = 'content', $callback = null)
    {
        $user = $this->auth->getCurrentUser();
        if (!$user) {
            $this->handleUnauthenticated();
            return false;
        }
        
        // Check if user owns the content
        $ownerId = $this->getContentOwner($contentId, $contentType);
        if ($ownerId === $user['user_id']) {
            if ($callback && is_callable($callback)) {
                return $callback();
            }
            return true;
        }
        
        // Check if user has moderation permissions
        $moderationPermission = $contentType . '.moderate';
        if ($this->rbac->hasPermission($moderationPermission)) {
            if ($callback && is_callable($callback)) {
                return $callback();
            }
            return true;
        }
        
        $this->handleAccessDenied("Content ownership or {$moderationPermission} permission required");
        return false;
    }
    
    /**
     * Email verification middleware
     * @param callable|null $callback Optional callback to execute if verified
     * @return bool True if email is verified
     */
    public function requireEmailVerification($callback = null)
    {
        $user = $this->auth->getCurrentUser();
        if (!$user) {
            $this->handleUnauthenticated();
            return false;
        }
        
        // Check if email verification is required
        global $cfg;
        $requireVerification = $cfg['require_email_verification'] ?? true;
        
        if (!$requireVerification) {
            if ($callback && is_callable($callback)) {
                return $callback();
            }
            return true;
        }
        
        // Get user data to check verification status
        global $class_database;
        $db = $class_database->dbConnection();
        $sql = "SELECT email_verified FROM db_users WHERE user_id = ?";
        $result = $db->Execute($sql, [$user['user_id']]);
        
        if (!$result || $result->EOF || !$result->fields['email_verified']) {
            $this->handleEmailNotVerified();
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Account status middleware - checks if account is active
     * @param callable|null $callback Optional callback to execute if active
     * @return bool True if account is active
     */
    public function requireActiveAccount($callback = null)
    {
        $user = $this->auth->getCurrentUser();
        if (!$user) {
            $this->handleUnauthenticated();
            return false;
        }
        
        // Get current user status
        global $class_database;
        $db = $class_database->dbConnection();
        $sql = "SELECT status FROM db_users WHERE user_id = ?";
        $result = $db->Execute($sql, [$user['user_id']]);
        
        if (!$result || $result->EOF) {
            $this->handleUnauthenticated();
            return false;
        }
        
        $status = $result->fields['status'];
        
        if ($status !== 'active') {
            $this->handleInactiveAccount($status);
            return false;
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * API middleware - validates API access
     * @param bool $requireAuth Whether authentication is required
     * @param callable|null $callback Optional callback to execute if valid
     * @return bool True if valid API request
     */
    public function requireAPI($requireAuth = true, $callback = null)
    {
        // Check if API access is enabled
        global $cfg;
        $apiEnabled = $cfg['api_enabled'] ?? true;
        
        if (!$apiEnabled) {
            $this->sendAPIResponse(['success' => false, 'message' => 'API access is disabled'], 503);
            return false;
        }
        
        // Check API rate limiting
        $clientIP = $this->getClientIP();
        $rateLimitKey = "api_requests_{$clientIP}";
        $maxRequests = $cfg['api_rate_limit'] ?? 1000;
        $timeWindow = $cfg['api_rate_window'] ?? 3600;
        
        if (!VSecurity::checkRateLimit($rateLimitKey, $maxRequests, $timeWindow, 'api_request')) {
            $this->sendAPIResponse(['success' => false, 'message' => 'API rate limit exceeded'], 429);
            return false;
        }
        
        // Check authentication if required
        if ($requireAuth) {
            if (!$this->auth->isAuthenticated()) {
                $this->sendAPIResponse(['success' => false, 'message' => 'Authentication required'], 401);
                return false;
            }
            
            // Check API permission
            if (!$this->rbac->hasPermission('api.access')) {
                $this->sendAPIResponse(['success' => false, 'message' => 'API access permission required'], 403);
                return false;
            }
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Combine multiple middleware checks
     * @param array $middlewares Array of middleware configurations
     * @param callable|null $callback Optional callback to execute if all pass
     * @return bool True if all middleware checks pass
     */
    public function requireAll($middlewares, $callback = null)
    {
        foreach ($middlewares as $middleware) {
            $method = $middleware['method'];
            $params = $middleware['params'] ?? [];
            
            if (!call_user_func_array([$this, $method], $params)) {
                return false;
            }
        }
        
        if ($callback && is_callable($callback)) {
            return $callback();
        }
        
        return true;
    }
    
    /**
     * Helper method to create middleware chain
     * @param array $middlewares Array of middleware configurations
     * @return callable Middleware chain function
     */
    public static function chain($middlewares)
    {
        return function($callback = null) use ($middlewares) {
            $middleware = VMiddleware::getInstance();
            return $middleware->requireAll($middlewares, $callback);
        };
    }
    
    /**
     * Private helper methods
     */
    
    private function handleUnauthenticated()
    {
        $this->logger->info('Unauthenticated access attempt', [
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => 'Authentication required'], 401);
        } else {
            $redirectUrl = urlencode($_SERVER['REQUEST_URI'] ?? '/');
            header("Location: /login?redirect={$redirectUrl}");
            exit;
        }
    }
    
    private function handleAlreadyAuthenticated()
    {
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => 'Already authenticated'], 400);
        } else {
            header('Location: /dashboard');
            exit;
        }
    }
    
    private function handleCSRFFailure()
    {
        $this->logger->logSecurityEvent('CSRF token validation failed', [
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
        } else {
            header('Location: /error?type=csrf');
            exit;
        }
    }
    
    private function handleRateLimitExceeded($action)
    {
        $this->logger->logSecurityEvent('Rate limit exceeded', [
            'action' => $action,
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => 'Rate limit exceeded'], 429);
        } else {
            header('Location: /error?type=rate_limit');
            exit;
        }
    }
    
    private function handleAccessDenied($message = 'Access denied')
    {
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => $message], 403);
        } else {
            header('Location: /access-denied');
            exit;
        }
    }
    
    private function handleEmailNotVerified()
    {
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => 'Email verification required'], 403);
        } else {
            header('Location: /verify-email');
            exit;
        }
    }
    
    private function handleInactiveAccount($status)
    {
        $message = "Account is {$status}";
        
        if ($this->isAPIRequest()) {
            $this->sendAPIResponse(['success' => false, 'message' => $message], 403);
        } else {
            header("Location: /account-status?status={$status}");
            exit;
        }
    }
    
    private function getContentOwner($contentId, $contentType)
    {
        global $class_database;
        $db = $class_database->dbConnection();
        
        $table = $this->getContentTable($contentType);
        $idField = $this->getContentIdField($contentType);
        
        $sql = "SELECT user_id FROM {$table} WHERE {$idField} = ?";
        $result = $db->Execute($sql, [$contentId]);
        
        return ($result && !$result->EOF) ? $result->fields['user_id'] : null;
    }
    
    private function getContentTable($contentType)
    {
        $tables = [
            'video' => 'db_videofiles',
            'stream' => 'db_livefiles',
            'image' => 'db_imagefiles',
            'audio' => 'db_audiofiles',
            'document' => 'db_documentfiles',
            'blog' => 'db_blogfiles'
        ];
        
        return $tables[$contentType] ?? 'db_videofiles';
    }
    
    private function getContentIdField($contentType)
    {
        $fields = [
            'video' => 'video_id',
            'stream' => 'stream_id',
            'image' => 'image_id',
            'audio' => 'audio_id',
            'document' => 'document_id',
            'blog' => 'blog_id'
        ];
        
        return $fields[$contentType] ?? 'video_id';
    }
    
    private function isAPIRequest()
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0 ||
               strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
    }
    
    private function sendAPIResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    private function getClientIP()
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
}