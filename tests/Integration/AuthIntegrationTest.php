<?php

namespace EasyStream\Tests\Integration;

use PHPUnit\Framework\TestCase;
use VAuth;
use VRBAC;
use VMiddleware;

class AuthIntegrationTest extends TestCase
{
    private $auth;
    private $rbac;
    private $middleware;
    private $testUserId;
    private $adminUserId;
    
    protected function setUp(): void
    {
        $this->auth = VAuth::getInstance();
        $this->rbac = VRBAC::getInstance();
        $this->middleware = VMiddleware::getInstance();
        
        // Clear session data
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        
        // Mock server variables
        $_SERVER = [
            'REQUEST_URI' => '/test',
            'REQUEST_METHOD' => 'POST',
            'HTTP_USER_AGENT' => 'PHPUnit Integration Test',
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTPS' => '1',
            'HTTP_ACCEPT' => 'text/html'
        ];
        
        // Clean up any existing test data
        $this->cleanupTestData();
        
        // Create test users
        $this->createTestUsers();
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
        
        // Clean up test users and related data
        $testEmails = ['authint@example.com', 'authintadmin@example.com'];
        
        foreach ($testEmails as $email) {
            $userId = $db->GetOne("SELECT user_id FROM db_users WHERE email = ?", [$email]);
            if ($userId) {
                $db->Execute("DELETE FROM db_user_permissions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_role_history WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_user_suspensions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_user_bans WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_sessions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_login_history WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
            }
        }
    }
    
    private function createTestUsers()
    {
        // Create regular test user
        $userData = [
            'username' => 'authinttest',
            'email' => 'authint@example.com',
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
        
        // Create admin test user
        $adminData = [
            'username' => 'authintadmin',
            'email' => 'authintadmin@example.com',
            'password' => 'AdminPassword123!'
        ];
        
        $adminResult = $this->auth->register($adminData);
        if ($adminResult['success']) {
            $this->adminUserId = $adminResult['user_id'];
            
            // Set as admin and verify
            global $class_database;
            $db = $class_database->dbConnection();
            $token = $db->GetOne("SELECT verification_token FROM db_users WHERE user_id = ?", [$this->adminUserId]);
            if ($token) {
                $this->auth->verifyEmail($token);
            }
            $db->Execute("UPDATE db_users SET role = 'admin' WHERE user_id = ?", [$this->adminUserId]);
        }
    }
    
    /**
     * Test complete user registration and verification workflow
     */
    public function testCompleteRegistrationWorkflow()
    {
        // Test registration
        $userData = [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'NewPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user_id', $result);
        
        $userId = $result['user_id'];
        
        // Test user exists in database
        global $class_database;
        $db = $class_database->dbConnection();
        $userExists = $db->GetOne("SELECT COUNT(*) FROM db_users WHERE user_id = ?", [$userId]);
        $this->assertEquals(1, $userExists);
        
        // Test email is not verified initially
        $emailVerified = $db->GetOne("SELECT email_verified FROM db_users WHERE user_id = ?", [$userId]);
        $this->assertEquals(0, $emailVerified);
        
        // Get verification token
        $token = $db->GetOne("SELECT verification_token FROM db_users WHERE user_id = ?", [$userId]);
        $this->assertNotEmpty($token);
        
        // Test email verification
        $verifyResult = $this->auth->verifyEmail($token);
        $this->assertTrue($verifyResult['success']);
        
        // Test email is now verified
        $emailVerified = $db->GetOne("SELECT email_verified FROM db_users WHERE user_id = ?", [$userId]);
        $this->assertEquals(1, $emailVerified);
        
        // Test token is cleared
        $tokenAfter = $db->GetOne("SELECT verification_token FROM db_users WHERE user_id = ?", [$userId]);
        $this->assertNull($tokenAfter);
        
        // Clean up
        $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
    }
    
    /**
     * Test complete login and session workflow
     */
    public function testCompleteLoginWorkflow()
    {
        // Test login
        $result = $this->auth->login('authinttest', 'TestPassword123!');
        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        
        // Test session is created
        $this->assertTrue($this->auth->isAuthenticated());
        
        $currentUser = $this->auth->getCurrentUser();
        $this->assertNotNull($currentUser);
        $this->assertEquals('authinttest', $currentUser['username']);
        
        // Test session exists in database
        global $class_database;
        $db = $class_database->dbConnection();
        $sessionExists = $db->GetOne("SELECT COUNT(*) FROM db_sessions WHERE user_id = ?", [$this->testUserId]);
        $this->assertGreaterThan(0, $sessionExists);
        
        // Test logout
        $logoutResult = $this->auth->logout();
        $this->assertTrue($logoutResult['success']);
        
        // Test session is destroyed
        $this->assertFalse($this->auth->isAuthenticated());
        $this->assertNull($this->auth->getCurrentUser());
        
        // Test session is removed from database
        $sessionExists = $db->GetOne("SELECT COUNT(*) FROM db_sessions WHERE user_id = ?", [$this->testUserId]);
        $this->assertEquals(0, $sessionExists);
    }
    
    /**
     * Test authentication with RBAC integration
     */
    public function testAuthRBACIntegration()
    {
        // Login as regular user
        $this->auth->login('authinttest', 'TestPassword123!');
        
        // Test basic permissions
        $this->assertTrue($this->rbac->hasPermission('content.view'));
        $this->assertTrue($this->rbac->hasPermission('content.create'));
        $this->assertFalse($this->rbac->hasPermission('admin.dashboard'));
        
        // Logout and login as admin
        $this->auth->logout();
        $this->auth->login('authintadmin', 'AdminPassword123!');
        
        // Test admin permissions
        $this->assertTrue($this->rbac->hasPermission('admin.dashboard'));
        $this->assertTrue($this->rbac->hasPermission('user.ban'));
        $this->assertTrue($this->rbac->hasRole('admin'));
        
        $this->auth->logout();
    }
    
    /**
     * Test middleware integration with authentication
     */
    public function testMiddlewareAuthIntegration()
    {
        // Test unauthenticated access
        $this->expectOutputRegex('/Location:/');
        ob_start();
        $result = $this->middleware->requireAuth();
        ob_end_clean();
        $this->assertFalse($result);
        
        // Login user
        $this->auth->login('authinttest', 'TestPassword123!');
        
        // Test authenticated access
        ob_start();
        $result = $this->middleware->requireAuth();
        $output = ob_get_clean();
        $this->assertTrue($result);
        $this->assertEmpty($output);
        
        // Test role requirement
        ob_start();
        $result = $this->middleware->requireRole('member');
        $output = ob_get_clean();
        $this->assertTrue($result);
        $this->assertEmpty($output);
        
        // Test permission requirement
        ob_start();
        $result = $this->middleware->requirePermission('content.create');
        $output = ob_get_clean();
        $this->assertTrue($result);
        $this->assertEmpty($output);
        
        $this->auth->logout();
    }
    
    /**
     * Test password reset workflow
     */
    public function testPasswordResetWorkflow()
    {
        // Request password reset
        $result = $this->auth->requestPasswordReset('authint@example.com');
        $this->assertTrue($result['success']);
        
        // Get reset token from database
        global $class_database;
        $db = $class_database->dbConnection();
        $token = $db->GetOne("SELECT reset_token FROM db_users WHERE user_id = ?", [$this->testUserId]);
        $this->assertNotEmpty($token);
        
        // Test password reset
        $newPassword = 'NewPassword456!';
        $resetResult = $this->auth->resetPassword($token, $newPassword);
        $this->assertTrue($resetResult['success']);
        
        // Test old password no longer works
        $oldLoginResult = $this->auth->login('authinttest', 'TestPassword123!');
        $this->assertFalse($oldLoginResult['success']);
        
        // Test new password works
        $newLoginResult = $this->auth->login('authinttest', $newPassword);
        $this->assertTrue($newLoginResult['success']);
        
        // Test token is cleared
        $tokenAfter = $db->GetOne("SELECT reset_token FROM db_users WHERE user_id = ?", [$this->testUserId]);
        $this->assertNull($tokenAfter);
        
        $this->auth->logout();
    }
    
    /**
     * Test user suspension integration
     */
    public function testUserSuspensionIntegration()
    {
        // Login as admin
        $this->auth->login('authintadmin', 'AdminPassword123!');
        
        // Suspend the test user
        $result = $this->rbac->suspendUser($this->testUserId, 'Test suspension', $this->adminUserId);
        $this->assertTrue($result);
        
        $this->auth->logout();
        
        // Try to login as suspended user
        $loginResult = $this->auth->login('authinttest', 'TestPassword123!');
        $this->assertFalse($loginResult['success']);
        $this->assertStringContainsString('not active', $loginResult['message']);
        
        // Reinstate user
        $this->auth->login('authintadmin', 'AdminPassword123!');
        $reinstateResult = $this->rbac->reinstateUser($this->testUserId, 'Test reinstatement', $this->adminUserId);
        $this->assertTrue($reinstateResult);
        $this->auth->logout();
        
        // Test user can login again
        $loginResult = $this->auth->login('authinttest', 'TestPassword123!');
        $this->assertTrue($loginResult['success']);
        
        $this->auth->logout();
    }
    
    /**
     * Test session timeout and cleanup
     */
    public function testSessionTimeoutAndCleanup()
    {
        // Login user
        $this->auth->login('authinttest', 'TestPassword123!');
        $this->assertTrue($this->auth->isAuthenticated());
        
        // Simulate session timeout by modifying last activity
        $_SESSION['LAST_ACTIVITY'] = time() - 7200; // 2 hours ago
        
        // Test session is expired
        $this->assertFalse($this->auth->isAuthenticated());
        
        // Test session data is cleared
        $this->assertNull($this->auth->getCurrentUser());
    }
    
    /**
     * Test concurrent sessions
     */
    public function testConcurrentSessions()
    {
        // Login user
        $this->auth->login('authinttest', 'TestPassword123!');
        $sessionId1 = session_id();
        
        // Simulate second session
        session_write_close();
        session_id('test_session_2');
        session_start();
        
        // Login same user in second session
        $this->auth->login('authinttest', 'TestPassword123!');
        $sessionId2 = session_id();
        
        $this->assertNotEquals($sessionId1, $sessionId2);
        
        // Test both sessions exist in database
        global $class_database;
        $db = $class_database->dbConnection();
        $sessionCount = $db->GetOne("SELECT COUNT(*) FROM db_sessions WHERE user_id = ?", [$this->testUserId]);
        $this->assertGreaterThanOrEqual(1, $sessionCount);
        
        // Logout from current session
        $this->auth->logout();
    }
    
    /**
     * Test remember me functionality
     */
    public function testRememberMeFunctionality()
    {
        // Login with remember me
        $result = $this->auth->login('authinttest', 'TestPassword123!', true);
        $this->assertTrue($result['success']);
        
        // Test remember token is set in database
        global $class_database;
        $db = $class_database->dbConnection();
        $rememberToken = $db->GetOne("SELECT remember_token FROM db_users WHERE user_id = ?", [$this->testUserId]);
        $this->assertNotNull($rememberToken);
        
        // Test session is marked as remember me
        $rememberMe = $db->GetOne("SELECT remember_me FROM db_sessions WHERE user_id = ?", [$this->testUserId]);
        $this->assertEquals(1, $rememberMe);
        
        // Logout
        $this->auth->logout();
        
        // Test remember token is cleared
        $rememberToken = $db->GetOne("SELECT remember_token FROM db_users WHERE user_id = ?", [$this->testUserId]);
        $this->assertNull($rememberToken);
    }
    
    /**
     * Test email verification requirement
     */
    public function testEmailVerificationRequirement()
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
        
        // Try to login without verification (should fail if verification required)
        global $cfg;
        $cfg['require_email_verification'] = true;
        
        $loginResult = $this->auth->login('unverified', 'TestPassword123!');
        $this->assertFalse($loginResult['success']);
        $this->assertStringContainsString('verify', $loginResult['message']);
        
        // Clean up
        global $class_database;
        $db = $class_database->dbConnection();
        $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
    }
    
    /**
     * Test rate limiting integration
     */
    public function testRateLimitingIntegration()
    {
        // Make multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $result = $this->auth->login('authinttest', 'WrongPassword');
            if ($i < 5) {
                $this->assertFalse($result['success']);
                $this->assertStringContainsString('Invalid credentials', $result['message']);
            } else {
                // 6th attempt should be rate limited
                $this->assertFalse($result['success']);
                $this->assertStringContainsString('Too many', $result['message']);
            }
        }
        
        // Even correct password should be rate limited now
        $result = $this->auth->login('authinttest', 'TestPassword123!');
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Too many', $result['message']);
    }
    
    /**
     * Test database transaction integrity
     */
    public function testDatabaseTransactionIntegrity()
    {
        global $class_database;
        $db = $class_database->dbConnection();
        
        // Count initial users
        $initialCount = $db->GetOne("SELECT COUNT(*) FROM db_users");
        
        // Try to register with invalid data (should not create partial records)
        $userData = [
            'username' => 'testuser',
            'email' => 'invalid-email', // Invalid email
            'password' => 'TestPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        $this->assertFalse($result['success']);
        
        // Count should be unchanged
        $finalCount = $db->GetOne("SELECT COUNT(*) FROM db_users");
        $this->assertEquals($initialCount, $finalCount);
    }
    
    /**
     * Test authentication state consistency
     */
    public function testAuthenticationStateConsistency()
    {
        // Login user
        $this->auth->login('authinttest', 'TestPassword123!');
        
        // Test all authentication state methods are consistent
        $this->assertTrue($this->auth->isAuthenticated());
        $this->assertNotNull($this->auth->getCurrentUser());
        
        $currentUser = $this->auth->getCurrentUser();
        $this->assertEquals($this->testUserId, $currentUser['user_id']);
        $this->assertEquals('authinttest', $currentUser['username']);
        
        // Test session variables are set correctly
        $this->assertEquals($this->testUserId, $_SESSION['USER_ID']);
        $this->assertEquals('authinttest', $_SESSION['USERNAME']);
        $this->assertArrayHasKey('LOGIN_TIME', $_SESSION);
        $this->assertArrayHasKey('SESSION_TOKEN', $_SESSION);
        
        $this->auth->logout();
        
        // Test all state is cleared after logout
        $this->assertFalse($this->auth->isAuthenticated());
        $this->assertNull($this->auth->getCurrentUser());
        $this->assertArrayNotHasKey('USER_ID', $_SESSION);
    }
}