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
 * Enhanced Authentication Class with Secure Session Management
 */
class VAuth
{
    private static $instance = null;
    private $db;
    private $logger;
    private $security;
    private $redis;
    
    // Session configuration
    private $sessionTimeout = 3600; // 1 hour default
    private $rememberMeTimeout = 2592000; // 30 days
    private $maxLoginAttempts = 5;
    private $lockoutDuration = 900; // 15 minutes
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        global $class_database;
        
        $this->db = $class_database ?: new VDatabase();
        $this->logger = VLogger::getInstance();
        $this->security = VSecurity::getInstance();
        $this->redis = $this->getRedisConnection();
        
        // Configure secure session settings
        $this->configureSession();
    }
    
    /**
     * Configure secure session settings
     */
    private function configureSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure session configuration
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_lifetime', 0); // Session cookies only
            
            // Use Redis for session storage if available
            if ($this->redis) {
                ini_set('session.save_handler', 'redis');
                $redisHost = getenv('REDIS_HOST') ?: 'redis';
                $redisPort = getenv('REDIS_PORT') ?: 6379;
                $redisDb = getenv('REDIS_DB') ?: 0;
                ini_set('session.save_path', "tcp://{$redisHost}:{$redisPort}?database={$redisDb}");
            }
            
            session_start();
        }
    }
    
    /**
     * Get Redis connection
     * @return Redis|null
     */
    private function getRedisConnection()
    {
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
            $this->logger->warning('Redis connection failed for auth', ['error' => $e->getMessage()]);
            return null;
        }
    }
    
    /**
     * Register a new user with email verification
     * @param array $userData User registration data
     * @return array Result with success status and message
     */
    public function register($userData)
    {
        try {
            // Validate required fields
            $requiredFields = ['username', 'email', 'password'];
            foreach ($requiredFields as $field) {
                if (empty($userData[$field])) {
                    return ['success' => false, 'message' => "Field '{$field}' is required"];
                }
            }
            
            // Sanitize and validate input
            $username = VSecurity::validateInput($userData['username'], 'alphanum', null, ['min_length' => 3, 'max_length' => 50]);
            $email = VSecurity::validateInput($userData['email'], 'email');
            $password = $userData['password']; // Don't sanitize password
            
            if (!$username) {
                return ['success' => false, 'message' => 'Invalid username. Use 3-50 alphanumeric characters only.'];
            }
            
            if (!$email) {
                return ['success' => false, 'message' => 'Invalid email address'];
            }
            
            if (strlen($password) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
            }
            
            // Check password strength
            if (!$this->isPasswordStrong($password)) {
                return ['success' => false, 'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'];
            }
            
            // Check if username or email already exists
            if ($this->userExists($username, $email)) {
                $this->logger->logSecurityEvent('Registration attempt with existing credentials', [
                    'username' => $username,
                    'email' => $email
                ]);
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user into database
            $sql = "INSERT INTO `db_users` (`username`, `email`, `password_hash`, `role`, `status`, `email_verified`, `verification_token`, `created_at`) 
                    VALUES (?, ?, ?, 'member', 'active', 0, ?, NOW())";
            
            $result = $this->db->dbConnection()->Execute($sql, [$username, $email, $passwordHash, $verificationToken]);
            
            if (!$result) {
                $this->logger->error('User registration failed', [
                    'username' => $username,
                    'email' => $email,
                    'error' => $this->db->dbConnection()->ErrorMsg()
                ]);
                return ['success' => false, 'message' => 'Registration failed. Please try again.'];
            }
            
            $userId = $this->db->dbConnection()->Insert_ID();
            
            // Send verification email
            $this->sendVerificationEmail($email, $username, $verificationToken);
            
            $this->logger->info('User registered successfully', [
                'user_id' => $userId,
                'username' => $username,
                'email' => $email
            ]);
            
            return [
                'success' => true,
                'message' => 'Registration successful! Please check your email to verify your account.',
                'user_id' => $userId
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Registration error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'An error occurred during registration'];
        }
    }
    
    /**
     * Verify email address
     * @param string $token Verification token
     * @return array Result with success status and message
     */
    public function verifyEmail($token)
    {
        try {
            $token = VSecurity::validateInput($token, 'alphanum');
            if (!$token) {
                return ['success' => false, 'message' => 'Invalid verification token'];
            }
            
            $sql = "SELECT `user_id`, `username`, `email` FROM `db_users` WHERE `verification_token` = ? AND `email_verified` = 0";
            $result = $this->db->dbConnection()->Execute($sql, [$token]);
            
            if (!$result || $result->EOF) {
                $this->logger->logSecurityEvent('Invalid email verification attempt', ['token' => $token]);
                return ['success' => false, 'message' => 'Invalid or expired verification token'];
            }
            
            $userData = $result->fields;
            
            // Update user as verified
            $updateSql = "UPDATE `db_users` SET `email_verified` = 1, `verification_token` = NULL, `updated_at` = NOW() WHERE `user_id` = ?";
            $updateResult = $this->db->dbConnection()->Execute($updateSql, [$userData['user_id']]);
            
            if (!$updateResult) {
                return ['success' => false, 'message' => 'Verification failed. Please try again.'];
            }
            
            $this->logger->info('Email verified successfully', [
                'user_id' => $userData['user_id'],
                'username' => $userData['username'],
                'email' => $userData['email']
            ]);
            
            return [
                'success' => true,
                'message' => 'Email verified successfully! You can now log in.',
                'user_id' => $userData['user_id']
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Email verification error', [
                'error' => $e->getMessage(),
                'token' => $token ?? 'unknown'
            ]);
            return ['success' => false, 'message' => 'An error occurred during verification'];
        }
    }
    
    /**
     * Authenticate user login
     * @param string $identifier Username or email
     * @param string $password Password
     * @param bool $rememberMe Remember me option
     * @return array Result with success status and user data
     */
    public function login($identifier, $password, $rememberMe = false)
    {
        try {
            // Rate limiting for login attempts
            $clientIP = $this->getClientIP();
            $rateLimitKey = "login_attempts_{$clientIP}";
            
            if (!VSecurity::checkRateLimit($rateLimitKey, $this->maxLoginAttempts, $this->lockoutDuration, 'login')) {
                $this->logger->logSecurityEvent('Login rate limit exceeded', [
                    'ip' => $clientIP,
                    'identifier' => $identifier
                ]);
                return ['success' => false, 'message' => 'Too many login attempts. Please try again later.'];
            }
            
            // Validate input
            $identifier = VSecurity::validateInput($identifier, 'string', null, ['max_length' => 255]);
            if (!$identifier || !$password) {
                return ['success' => false, 'message' => 'Username/email and password are required'];
            }
            
            // Find user by username or email
            $user = $this->findUserByIdentifier($identifier);
            if (!$user) {
                $this->logger->logSecurityEvent('Login attempt with non-existent user', [
                    'identifier' => $identifier,
                    'ip' => $clientIP
                ]);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                $this->logger->logSecurityEvent('Login attempt with inactive account', [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'status' => $user['status']
                ]);
                return ['success' => false, 'message' => 'Account is not active'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                $this->logger->logSecurityEvent('Failed login attempt', [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'ip' => $clientIP
                ]);
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Check if email is verified (optional based on configuration)
            global $cfg;
            $requireEmailVerification = $cfg['require_email_verification'] ?? true;
            if ($requireEmailVerification && !$user['email_verified']) {
                return ['success' => false, 'message' => 'Please verify your email address before logging in'];
            }
            
            // Create session
            $sessionResult = $this->createSession($user, $rememberMe);
            if (!$sessionResult['success']) {
                return $sessionResult;
            }
            
            // Update last login
            $this->updateLastLogin($user['user_id']);
            
            $this->logger->info('User logged in successfully', [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'ip' => $clientIP,
                'remember_me' => $rememberMe
            ]);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'email_verified' => $user['email_verified']
                ]
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Login error', [
                'error' => $e->getMessage(),
                'identifier' => $identifier ?? 'unknown'
            ]);
            return ['success' => false, 'message' => 'An error occurred during login'];
        }
    }
    
    /**
     * Create secure session
     * @param array $user User data
     * @param bool $rememberMe Remember me option
     * @return array Result with success status
     */
    private function createSession($user, $rememberMe = false)
    {
        try {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Set session data
            $_SESSION['USER_ID'] = $user['user_id'];
            $_SESSION['USERNAME'] = $user['username'];
            $_SESSION['EMAIL'] = $user['email'];
            $_SESSION['ROLE'] = $user['role'];
            $_SESSION['LOGIN_TIME'] = time();
            $_SESSION['LAST_ACTIVITY'] = time();
            $_SESSION['IP_ADDRESS'] = $this->getClientIP();
            $_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $_SESSION['SESSION_TOKEN'] = bin2hex(random_bytes(32));
            
            // Store session in database
            $sessionId = session_id();
            $expiresAt = date('Y-m-d H:i:s', time() + ($rememberMe ? $this->rememberMeTimeout : $this->sessionTimeout));
            
            $sql = "INSERT INTO `db_sessions` (`session_id`, `user_id`, `ip_address`, `user_agent`, `created_at`, `expires_at`, `remember_me`) 
                    VALUES (?, ?, ?, ?, NOW(), ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    `ip_address` = VALUES(`ip_address`), 
                    `user_agent` = VALUES(`user_agent`), 
                    `expires_at` = VALUES(`expires_at`),
                    `remember_me` = VALUES(`remember_me`)";
            
            $result = $this->db->dbConnection()->Execute($sql, [
                $sessionId,
                $user['user_id'],
                $_SESSION['IP_ADDRESS'],
                $_SESSION['USER_AGENT'],
                $expiresAt,
                $rememberMe ? 1 : 0
            ]);
            
            if (!$result) {
                $this->logger->error('Session creation failed', [
                    'user_id' => $user['user_id'],
                    'error' => $this->db->dbConnection()->ErrorMsg()
                ]);
                return ['success' => false, 'message' => 'Session creation failed'];
            }
            
            // Set remember me cookie if requested
            if ($rememberMe) {
                $rememberToken = bin2hex(random_bytes(32));
                $cookieExpiry = time() + $this->rememberMeTimeout;
                
                setcookie('remember_token', $rememberToken, $cookieExpiry, '/', '', isset($_SERVER['HTTPS']), true);
                
                // Store remember token in database
                $tokenSql = "UPDATE `db_users` SET `remember_token` = ?, `remember_expires` = ? WHERE `user_id` = ?";
                $this->db->dbConnection()->Execute($tokenSql, [
                    password_hash($rememberToken, PASSWORD_DEFAULT),
                    date('Y-m-d H:i:s', $cookieExpiry),
                    $user['user_id']
                ]);
            }
            
            return ['success' => true];
            
        } catch (Exception $e) {
            $this->logger->error('Session creation error', [
                'error' => $e->getMessage(),
                'user_id' => $user['user_id'] ?? 'unknown'
            ]);
            return ['success' => false, 'message' => 'Session creation failed'];
        }
    }
    
    /**
     * Logout user and destroy session
     * @return array Result with success status
     */
    public function logout()
    {
        try {
            $userId = $_SESSION['USER_ID'] ?? null;
            $sessionId = session_id();
            
            // Remove session from database
            if ($sessionId) {
                $sql = "DELETE FROM `db_sessions` WHERE `session_id` = ?";
                $this->db->dbConnection()->Execute($sql, [$sessionId]);
            }
            
            // Clear remember me cookie and token
            if (isset($_COOKIE['remember_token'])) {
                setcookie('remember_token', '', time() - 3600, '/', '', isset($_SERVER['HTTPS']), true);
                
                if ($userId) {
                    $tokenSql = "UPDATE `db_users` SET `remember_token` = NULL, `remember_expires` = NULL WHERE `user_id` = ?";
                    $this->db->dbConnection()->Execute($tokenSql, [$userId]);
                }
            }
            
            // Destroy session
            session_unset();
            session_destroy();
            
            $this->logger->info('User logged out successfully', [
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
            
            return ['success' => true, 'message' => 'Logged out successfully'];
            
        } catch (Exception $e) {
            $this->logger->error('Logout error', [
                'error' => $e->getMessage(),
                'user_id' => $_SESSION['USER_ID'] ?? 'unknown'
            ]);
            return ['success' => false, 'message' => 'An error occurred during logout'];
        }
    }
    
    /**
     * Check if user is authenticated
     * @return bool True if authenticated
     */
    public function isAuthenticated()
    {
        if (!isset($_SESSION['USER_ID']) || !isset($_SESSION['SESSION_TOKEN'])) {
            return false;
        }
        
        // Check session timeout
        $lastActivity = $_SESSION['LAST_ACTIVITY'] ?? 0;
        if (time() - $lastActivity > $this->sessionTimeout) {
            $this->logout();
            return false;
        }
        
        // Update last activity
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Verify session in database
        $sessionId = session_id();
        $sql = "SELECT `user_id` FROM `db_sessions` WHERE `session_id` = ? AND `expires_at` > NOW()";
        $result = $this->db->dbConnection()->Execute($sql, [$sessionId]);
        
        if (!$result || $result->EOF) {
            $this->logout();
            return false;
        }
        
        return true;
    }
    
    /**
     * Get current authenticated user
     * @return array|null User data or null if not authenticated
     */
    public function getCurrentUser()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['USER_ID'],
            'username' => $_SESSION['USERNAME'],
            'email' => $_SESSION['EMAIL'],
            'role' => $_SESSION['ROLE']
        ];
    }
    
    /**
     * Request password reset
     * @param string $email Email address
     * @return array Result with success status and message
     */
    public function requestPasswordReset($email)
    {
        try {
            $email = VSecurity::validateInput($email, 'email');
            if (!$email) {
                return ['success' => false, 'message' => 'Invalid email address'];
            }
            
            // Rate limiting for password reset requests
            $rateLimitKey = "password_reset_{$email}";
            if (!VSecurity::checkRateLimit($rateLimitKey, 3, 3600, 'password_reset')) {
                return ['success' => false, 'message' => 'Too many password reset requests. Please try again later.'];
            }
            
            // Find user by email
            $sql = "SELECT `user_id`, `username`, `email` FROM `db_users` WHERE `email` = ? AND `status` = 'active'";
            $result = $this->db->dbConnection()->Execute($sql, [$email]);
            
            if (!$result || $result->EOF) {
                // Don't reveal if email exists or not
                return ['success' => true, 'message' => 'If the email exists, a password reset link has been sent.'];
            }
            
            $user = $result->fields;
            
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
            
            // Store reset token
            $tokenSql = "UPDATE `db_users` SET `reset_token` = ?, `reset_expires` = ? WHERE `user_id` = ?";
            $tokenResult = $this->db->dbConnection()->Execute($tokenSql, [$resetToken, $expiresAt, $user['user_id']]);
            
            if (!$tokenResult) {
                return ['success' => false, 'message' => 'Failed to generate reset token'];
            }
            
            // Send reset email
            $this->sendPasswordResetEmail($user['email'], $user['username'], $resetToken);
            
            $this->logger->info('Password reset requested', [
                'user_id' => $user['user_id'],
                'email' => $email
            ]);
            
            return ['success' => true, 'message' => 'If the email exists, a password reset link has been sent.'];
            
        } catch (Exception $e) {
            $this->logger->error('Password reset request error', [
                'error' => $e->getMessage(),
                'email' => $email ?? 'unknown'
            ]);
            return ['success' => false, 'message' => 'An error occurred while processing your request'];
        }
    }
    
    /**
     * Reset password with token
     * @param string $token Reset token
     * @param string $newPassword New password
     * @return array Result with success status and message
     */
    public function resetPassword($token, $newPassword)
    {
        try {
            $token = VSecurity::validateInput($token, 'alphanum');
            if (!$token) {
                return ['success' => false, 'message' => 'Invalid reset token'];
            }
            
            if (strlen($newPassword) < 8) {
                return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
            }
            
            if (!$this->isPasswordStrong($newPassword)) {
                return ['success' => false, 'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character'];
            }
            
            // Find user by reset token
            $sql = "SELECT `user_id`, `username`, `email` FROM `db_users` 
                    WHERE `reset_token` = ? AND `reset_expires` > NOW() AND `status` = 'active'";
            $result = $this->db->dbConnection()->Execute($sql, [$token]);
            
            if (!$result || $result->EOF) {
                $this->logger->logSecurityEvent('Invalid password reset attempt', ['token' => $token]);
                return ['success' => false, 'message' => 'Invalid or expired reset token'];
            }
            
            $user = $result->fields;
            
            // Hash new password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            
            // Update password and clear reset token
            $updateSql = "UPDATE `db_users` SET 
                         `password_hash` = ?, 
                         `reset_token` = NULL, 
                         `reset_expires` = NULL, 
                         `updated_at` = NOW() 
                         WHERE `user_id` = ?";
            
            $updateResult = $this->db->dbConnection()->Execute($updateSql, [$passwordHash, $user['user_id']]);
            
            if (!$updateResult) {
                return ['success' => false, 'message' => 'Failed to update password'];
            }
            
            // Invalidate all existing sessions for this user
            $this->invalidateUserSessions($user['user_id']);
            
            $this->logger->info('Password reset successfully', [
                'user_id' => $user['user_id'],
                'username' => $user['username']
            ]);
            
            return ['success' => true, 'message' => 'Password reset successfully. Please log in with your new password.'];
            
        } catch (Exception $e) {
            $this->logger->error('Password reset error', [
                'error' => $e->getMessage(),
                'token' => $token ?? 'unknown'
            ]);
            return ['success' => false, 'message' => 'An error occurred while resetting your password'];
        }
    }
    
    /**
     * Helper methods
     */
    
    private function userExists($username, $email)
    {
        $sql = "SELECT COUNT(*) as count FROM `db_users` WHERE `username` = ? OR `email` = ?";
        $result = $this->db->dbConnection()->Execute($sql, [$username, $email]);
        return $result && !$result->EOF && $result->fields['count'] > 0;
    }
    
    private function isPasswordStrong($password)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password);
    }
    
    private function findUserByIdentifier($identifier)
    {
        $sql = "SELECT * FROM `db_users` WHERE (`username` = ? OR `email` = ?) AND `status` != 'deleted'";
        $result = $this->db->dbConnection()->Execute($sql, [$identifier, $identifier]);
        return ($result && !$result->EOF) ? $result->fields : null;
    }
    
    private function updateLastLogin($userId)
    {
        $sql = "UPDATE `db_users` SET `last_login` = NOW() WHERE `user_id` = ?";
        $this->db->dbConnection()->Execute($sql, [$userId]);
    }
    
    private function invalidateUserSessions($userId)
    {
        $sql = "DELETE FROM `db_sessions` WHERE `user_id` = ?";
        $this->db->dbConnection()->Execute($sql, [$userId]);
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
    
    private function sendVerificationEmail($email, $username, $token)
    {
        // Implementation depends on your email system
        // This is a placeholder for the email sending logic
        $this->logger->info('Verification email sent', [
            'email' => $email,
            'username' => $username,
            'token' => substr($token, 0, 8) . '...' // Log partial token for debugging
        ]);
    }
    
    private function sendPasswordResetEmail($email, $username, $token)
    {
        // Implementation depends on your email system
        // This is a placeholder for the email sending logic
        $this->logger->info('Password reset email sent', [
            'email' => $email,
            'username' => $username,
            'token' => substr($token, 0, 8) . '...' // Log partial token for debugging
        ]);
    }
}