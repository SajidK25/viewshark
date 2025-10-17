<?php

namespace EasyStream\Tests\Unit;

use PHPUnit\Framework\TestCase;
use VAuth;

class AuthTest extends TestCase
{
    private $auth;
    private $testUserId;
    
    protected function setUp(): void
    {
        $this->auth = VAuth::getInstance();
        
        // Clear session data
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        
        // Clear cookies
        $_COOKIE = [];
        
        // Mock server variables
        $_SERVER = [
            'REQUEST_URI' => '/test',
            'REQUEST_METHOD' => 'GET',
            'HTTP_USER_AGENT' => 'PHPUnit Auth Test',
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTPS' => '1'
        ];
        
        // Clean up any existing test data
        $this->cleanupTestData();
    }
    
    protected function tearDown(): void
    {
        // Clean up test data
        $this->cleanupTestData();
        
        // Clear session
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        
        $_COOKIE = [];
    }
    
    private function cleanupTestData()
    {
        global $class_database;
        $db = $class_database->dbConnection();
        
        // Clean up test users and related data
        $testEmails = ['test@example.com', 'testuser@example.com', 'newuser@example.com'];
        $testUsernames = ['testuser', 'newuser', 'authtest'];
        
        foreach ($testEmails as $email) {
            $db->Execute("DELETE FROM db_sessions WHERE user_id IN (SELECT user_id FROM db_users WHERE email = ?)", [$email]);
            $db->Execute("DELETE FROM db_login_history WHERE email = ?", [$email]);
            $db->Execute("DELETE FROM db_users WHERE email = ?", [$email]);
        }
        
        foreach ($testUsernames as $username) {
            $db->Execute("DELETE FROM db_sessions WHERE user_id IN (SELECT user_id FROM db_users WHERE username = ?)", [$username]);
            $db->Execute("DELETE FROM db_login_history WHERE username = ?", [$username]);
            $db->Execute("DELETE FROM db_users WHERE username = ?", [$username]);
        }
    }
    
    /**
     * Test VAuth singleton pattern
     */
    public function testSingletonPattern()
    {
        $auth1 = VAuth::getInstance();
        $auth2 = VAuth::getInstance();
        
        $this->assertSame($auth1, $auth2);
        $this->assertInstanceOf(VAuth::class, $auth1);
    }
    
    /**
     * Test user registration with valid data
     */
    public function testUserRegistrationSuccess()
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Registration successful', $result['message']);
        $this->assertArrayHasKey('user_id', $result);
        
        $this->testUserId = $result['user_id'];
    }
    
    /**
     * Test user registration with invalid data
     */
    public function testUserRegistrationValidation()
    {
        // Test missing username
        $result = $this->auth->register([
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('username', $result['message']);
        
        // Test missing email
        $result = $this->auth->register([
            'username' => 'testuser',
            'password' => 'TestPassword123!'
        ]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('email', $result['message']);
        
        // Test missing password
        $result = $this->auth->register([
            'username' => 'testuser',
            'email' => 'test@example.com'
        ]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('password', $result['message']);
        
        // Test invalid email
        $result = $this->auth->register([
            'username' => 'testuser',
            'email' => 'invalid-email',
            'password' => 'TestPassword123!'
        ]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid email', $result['message']);
        
        // Test weak password
        $result = $this->auth->register([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'weak'
        ]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('8 characters', $result['message']);
        
        // Test password without special characters
        $result = $this->auth->register([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'TestPassword123'
        ]);
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('special character', $result['message']);
    }
    
    /**
     * Test duplicate user registration
     */
    public function testDuplicateUserRegistration()
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ];
        
        // First registration should succeed
        $result1 = $this->auth->register($userData);
        $this->assertTrue($result1['success']);
        
        // Second registration with same username should fail
        $result2 = $this->auth->register($userData);
        $this->assertFalse($result2['success']);
        $this->assertStringContainsString('already exists', $result2['message']);
        
        // Registration with same email but different username should fail
        $userData['username'] = 'differentuser';
        $result3 = $this->auth->register($userData);
        $this->assertFalse($result3['success']);
        $this->assertStringContainsString('already exists', $result3['message']);
    }
    
    /**
     * Test email verification
     */
    public function testEmailVerification()
    {
        // Register a user first
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ];
        
        $registerResult = $this->auth->register($userData);
        $this->assertTrue($registerResult['success']);
        
        // Get verification token from database
        global $class_database;
        $db = $class_database->dbConnection();
        $sql = "SELECT verification_token FROM db_users WHERE email = ?";
        $result = $db->Execute($sql, ['test@example.com']);
        
        $this->assertFalse($result->EOF);
        $token = $result->fields['verification_token'];
        $this->assertNotEmpty($token);
        
        // Test email verification
        $verifyResult = $this->auth->verifyEmail($token);
        $this->assertTrue($verifyResult['success']);
        $this->assertStringContainsString('verified successfully', $verifyResult['message']);
        
        // Test verification with invalid token
        $invalidResult = $this->auth->verifyEmail('invalid_token');
        $this->assertFalse($invalidResult['success']);
        $this->assertStringContainsString('Invalid', $invalidResult['message']);
        
        // Test verification with already used token
        $usedResult = $this->auth->verifyEmail($token);
        $this->assertFalse($usedResult['success']);
        $this->assertStringContainsString('Invalid', $usedResult['message']);
    }
    
    /**
     * Test user login with valid credentials
     */
    public function testLoginSuccess()
    {
        // Create and verify a test user
        $this->createVerifiedTestUser();
        
        // Test login with username
        $result = $this->auth->login('testuser', 'TestPassword123!');
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('Login successful', $result['message']);
        $this->assertArrayHasKey('user', $result);
        $this->assertEquals('testuser', $result['user']['username']);
        
        // Logout for next test
        $this->auth->logout();
        
        // Test login with email
        $result = $this->auth->login('test@example.com', 'TestPassword123!');
        $this->assertTrue($result['success']);
        $this->assertEquals('test@example.com', $result['user']['email']);
    }
    
    /**
     * Test login with invalid credentials
     */
    public function testLoginFailure()
    {
        $this->createVerifiedTestUser();
        
        // Test with wrong password
        $result = $this->auth->login('testuser', 'WrongPassword123!');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid credentials', $result['message']);
        
        // Test with non-existent user
        $result = $this->auth->login('nonexistent', 'TestPassword123!');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid credentials', $result['message']);
        
        // Test with empty credentials
        $result = $this->auth->login('', '');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('required', $result['message']);
    }
    
    /**
     * Test login rate limiting
     */
    public function testLoginRateLimiting()
    {
        $this->createVerifiedTestUser();
        
        // Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $result = $this->auth->login('testuser', 'WrongPassword');
        }
        
        // Next attempt should be rate limited
        $result = $this->auth->login('testuser', 'TestPassword123!');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Too many', $result['message']);
    }
    
    /**
     * Test session management
     */
    public function testSessionManagement()
    {
        $this->createVerifiedTestUser();
        
        // Test not authenticated initially
        $this->assertFalse($this->auth->isAuthenticated());
        $this->assertNull($this->auth->getCurrentUser());
        
        // Login
        $result = $this->auth->login('testuser', 'TestPassword123!');
        $this->assertTrue($result['success']);
        
        // Test authenticated after login
        $this->assertTrue($this->auth->isAuthenticated());
        
        $currentUser = $this->auth->getCurrentUser();
        $this->assertNotNull($currentUser);
        $this->assertEquals('testuser', $currentUser['username']);
        $this->assertEquals('test@example.com', $currentUser['email']);
        
        // Test logout
        $logoutResult = $this->auth->logout();
        $this->assertTrue($logoutResult['success']);
        
        // Test not authenticated after logout
        $this->assertFalse($this->auth->isAuthenticated());
        $this->assertNull($this->auth->getCurrentUser());
    }
    
    /**
     * Test remember me functionality
     */
    public function testRememberMeFunctionality()
    {
        $this->createVerifiedTestUser();
        
        // Login with remember me
        $result = $this->auth->login('testuser', 'TestPassword123!', true);
        $this->assertTrue($result['success']);
        
        // Check if remember token cookie would be set (we can't actually test cookie setting in unit tests)
        $this->assertTrue($this->auth->isAuthenticated());
        
        // Logout
        $this->auth->logout();
        $this->assertFalse($this->auth->isAuthenticated());
    }
    
    /**
     * Test password reset request
     */
    public function testPasswordResetRequest()
    {
        $this->createVerifiedTestUser();
        
        // Test valid email
        $result = $this->auth->requestPasswordReset('test@example.com');
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('reset link', $result['message']);
        
        // Test invalid email format
        $result = $this->auth->requestPasswordReset('invalid-email');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid email', $result['message']);
        
        // Test non-existent email (should still return success for security)
        $result = $this->auth->requestPasswordReset('nonexistent@example.com');
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('reset link', $result['message']);
    }
    
    /**
     * Test password reset with token
     */
    public function testPasswordReset()
    {
        $this->createVerifiedTestUser();
        
        // Request password reset
        $resetResult = $this->auth->requestPasswordReset('test@example.com');
        $this->assertTrue($resetResult['success']);
        
        // Get reset token from database
        global $class_database;
        $db = $class_database->dbConnection();
        $sql = "SELECT reset_token FROM db_users WHERE email = ?";
        $result = $db->Execute($sql, ['test@example.com']);
        
        $this->assertFalse($result->EOF);
        $token = $result->fields['reset_token'];
        $this->assertNotEmpty($token);
        
        // Test password reset with valid token
        $newPassword = 'NewPassword123!';
        $resetResult = $this->auth->resetPassword($token, $newPassword);
        $this->assertTrue($resetResult['success']);
        $this->assertStringContainsString('reset successfully', $resetResult['message']);
        
        // Test login with new password
        $loginResult = $this->auth->login('testuser', $newPassword);
        $this->assertTrue($loginResult['success']);
        
        // Test old password no longer works
        $this->auth->logout();
        $oldLoginResult = $this->auth->login('testuser', 'TestPassword123!');
        $this->assertFalse($oldLoginResult['success']);
    }
    
    /**
     * Test password reset validation
     */
    public function testPasswordResetValidation()
    {
        // Test invalid token
        $result = $this->auth->resetPassword('invalid_token', 'NewPassword123!');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Invalid', $result['message']);
        
        // Test weak password
        $result = $this->auth->resetPassword('some_token', 'weak');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('8 characters', $result['message']);
        
        // Test password without special characters
        $result = $this->auth->resetPassword('some_token', 'NewPassword123');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('special character', $result['message']);
    }
    
    /**
     * Test password reset rate limiting
     */
    public function testPasswordResetRateLimiting()
    {
        $this->createVerifiedTestUser();
        
        // Make multiple password reset requests
        for ($i = 0; $i < 4; $i++) {
            $this->auth->requestPasswordReset('test@example.com');
        }
        
        // Next request should be rate limited
        $result = $this->auth->requestPasswordReset('test@example.com');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Too many', $result['message']);
    }
    
    /**
     * Test session security features
     */
    public function testSessionSecurity()
    {
        $this->createVerifiedTestUser();
        
        // Login
        $this->auth->login('testuser', 'TestPassword123!');
        $this->assertTrue($this->auth->isAuthenticated());
        
        $originalSessionId = session_id();
        
        // Simulate session regeneration (happens during login)
        $this->assertNotEmpty($originalSessionId);
        
        // Test session data integrity
        $this->assertEquals('testuser', $_SESSION['USERNAME']);
        $this->assertEquals('test@example.com', $_SESSION['EMAIL']);
        $this->assertArrayHasKey('SESSION_TOKEN', $_SESSION);
        $this->assertArrayHasKey('LOGIN_TIME', $_SESSION);
        $this->assertArrayHasKey('IP_ADDRESS', $_SESSION);
    }
    
    /**
     * Helper method to create a verified test user
     */
    private function createVerifiedTestUser()
    {
        $userData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ];
        
        $registerResult = $this->auth->register($userData);
        $this->assertTrue($registerResult['success']);
        
        // Get and use verification token
        global $class_database;
        $db = $class_database->dbConnection();
        $sql = "SELECT verification_token FROM db_users WHERE email = ?";
        $result = $db->Execute($sql, ['test@example.com']);
        
        if (!$result->EOF) {
            $token = $result->fields['verification_token'];
            $this->auth->verifyEmail($token);
        }
    }
    
    /**
     * Test edge cases and error handling
     */
    public function testEdgeCases()
    {
        // Test registration with null values
        $result = $this->auth->register([
            'username' => null,
            'email' => null,
            'password' => null
        ]);
        $this->assertFalse($result['success']);
        
        // Test login with null values
        $result = $this->auth->login(null, null);
        $this->assertFalse($result['success']);
        
        // Test very long username
        $longUsername = str_repeat('a', 100);
        $result = $this->auth->register([
            'username' => $longUsername,
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ]);
        $this->assertFalse($result['success']);
        
        // Test SQL injection attempts
        $maliciousUsername = "admin'; DROP TABLE db_users; --";
        $result = $this->auth->register([
            'username' => $maliciousUsername,
            'email' => 'test@example.com',
            'password' => 'TestPassword123!'
        ]);
        // Should either fail validation or be safely escaped
        $this->assertIsArray($result);
    }
}