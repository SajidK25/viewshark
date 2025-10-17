<?php

namespace EasyStream\Tests\Unit;

use PHPUnit\Framework\TestCase;
use VRBAC;
use VAuth;

class RBACTest extends TestCase
{
    private $rbac;
    private $auth;
    private $testUserId;
    private $adminUserId;
    
    protected function setUp(): void
    {
        $this->rbac = VRBAC::getInstance();
        $this->auth = VAuth::getInstance();
        
        // Clear session data
        if (isset($_SESSION)) {
            $_SESSION = [];
        }
        
        // Mock server variables
        $_SERVER = [
            'REQUEST_URI' => '/test',
            'REQUEST_METHOD' => 'GET',
            'HTTP_USER_AGENT' => 'PHPUnit RBAC Test',
            'REMOTE_ADDR' => '127.0.0.1',
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
        $testEmails = ['rbactest@example.com', 'rbacadmin@example.com', 'rbacmember@example.com'];
        $testUsernames = ['rbactest', 'rbacadmin', 'rbacmember'];
        
        foreach ($testEmails as $email) {
            $userId = $db->GetOne("SELECT user_id FROM db_users WHERE email = ?", [$email]);
            if ($userId) {
                $db->Execute("DELETE FROM db_user_permissions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_role_history WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_user_suspensions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_user_bans WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_sessions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
            }
        }
    }
    
    private function createTestUsers()
    {
        // Create regular test user
        $userData = [
            'username' => 'rbactest',
            'email' => 'rbactest@example.com',
            'password' => 'TestPassword123!'
        ];
        
        $result = $this->auth->register($userData);
        if ($result['success']) {
            $this->testUserId = $result['user_id'];
            
            // Verify email
            global $class_database;
            $db = $class_database->dbConnection();
            $db->Execute("UPDATE db_users SET email_verified = 1 WHERE user_id = ?", [$this->testUserId]);
        }
        
        // Create admin test user
        $adminData = [
            'username' => 'rbacadmin',
            'email' => 'rbacadmin@example.com',
            'password' => 'AdminPassword123!'
        ];
        
        $adminResult = $this->auth->register($adminData);
        if ($adminResult['success']) {
            $this->adminUserId = $adminResult['user_id'];
            
            // Set as admin and verify
            global $class_database;
            $db = $class_database->dbConnection();
            $db->Execute("UPDATE db_users SET role = 'admin', email_verified = 1 WHERE user_id = ?", [$this->adminUserId]);
        }
    }
    
    /**
     * Test RBAC singleton pattern
     */
    public function testSingletonPattern()
    {
        $rbac1 = VRBAC::getInstance();
        $rbac2 = VRBAC::getInstance();
        
        $this->assertSame($rbac1, $rbac2);
        $this->assertInstanceOf(VRBAC::class, $rbac1);
    }
    
    /**
     * Test role hierarchy
     */
    public function testRoleHierarchy()
    {
        // Test role levels
        $this->assertTrue($this->rbac->hasRole('guest', $this->testUserId));
        $this->assertTrue($this->rbac->hasRole('member', $this->testUserId));
        $this->assertFalse($this->rbac->hasRole('admin', $this->testUserId));
        
        $this->assertTrue($this->rbac->hasRole('guest', $this->adminUserId));
        $this->assertTrue($this->rbac->hasRole('member', $this->adminUserId));
        $this->assertTrue($this->rbac->hasRole('admin', $this->adminUserId));
    }
    
    /**
     * Test basic permission checking
     */
    public function testBasicPermissions()
    {
        // Test member permissions
        $this->assertTrue($this->rbac->hasPermission('content.view', $this->testUserId));
        $this->assertTrue($this->rbac->hasPermission('content.create', $this->testUserId));
        $this->assertFalse($this->rbac->hasPermission('admin.dashboard', $this->testUserId));
        
        // Test admin permissions
        $this->assertTrue($this->rbac->hasPermission('content.view', $this->adminUserId));
        $this->assertTrue($this->rbac->hasPermission('admin.dashboard', $this->adminUserId));
        $this->assertTrue($this->rbac->hasPermission('user.ban', $this->adminUserId));
    }
    
    /**
     * Test guest permissions
     */
    public function testGuestPermissions()
    {
        // Test guest permissions without user ID (not logged in)
        $this->assertTrue($this->rbac->hasPermission('content.view'));
        $this->assertTrue($this->rbac->hasPermission('comment.view'));
        $this->assertFalse($this->rbac->hasPermission('content.create'));
        $this->assertFalse($this->rbac->hasPermission('admin.dashboard'));
    }
    
    /**
     * Test custom user permissions
     */
    public function testCustomUserPermissions()
    {
        // Grant custom permission
        $result = $this->rbac->grantPermission($this->testUserId, 'feature.beta', $this->adminUserId);
        $this->assertTrue($result);
        
        // Test custom permission
        $this->assertTrue($this->rbac->hasPermission('feature.beta', $this->testUserId));
        
        // Revoke custom permission
        $revokeResult = $this->rbac->revokePermission($this->testUserId, 'feature.beta', $this->adminUserId);
        $this->assertTrue($revokeResult);
        
        // Test permission is revoked
        $this->assertFalse($this->rbac->hasPermission('feature.beta', $this->testUserId));
    }
    
    /**
     * Test permission expiration
     */
    public function testPermissionExpiration()
    {
        // Grant permission with expiration
        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour from now
        $result = $this->rbac->grantPermission($this->testUserId, 'upload.large_files', $this->adminUserId, $expiresAt);
        $this->assertTrue($result);
        
        // Test permission is active
        $this->assertTrue($this->rbac->hasPermission('upload.large_files', $this->testUserId));
        
        // Test with expired permission (simulate by setting past date)
        global $class_database;
        $db = $class_database->dbConnection();
        $db->Execute("UPDATE db_user_permissions SET expires_at = ? WHERE user_id = ? AND permission = ?", 
                    [date('Y-m-d H:i:s', time() - 3600), $this->testUserId, 'upload.large_files']);
        
        // Test permission is expired
        $this->assertFalse($this->rbac->hasPermission('upload.large_files', $this->testUserId));
    }
    
    /**
     * Test multiple permission checking
     */
    public function testMultiplePermissions()
    {
        $permissions = ['content.view', 'content.create', 'comment.create'];
        
        // Test hasAnyPermission
        $this->assertTrue($this->rbac->hasAnyPermission($permissions, $this->testUserId));
        $this->assertTrue($this->rbac->hasAnyPermission(['admin.dashboard', 'content.view'], $this->testUserId));
        $this->assertFalse($this->rbac->hasAnyPermission(['admin.dashboard', 'admin.system'], $this->testUserId));
        
        // Test hasAllPermissions
        $this->assertTrue($this->rbac->hasAllPermissions($permissions, $this->testUserId));
        $this->assertFalse($this->rbac->hasAllPermissions(['content.view', 'admin.dashboard'], $this->testUserId));
    }
    
    /**
     * Test role changes
     */
    public function testRoleChanges()
    {
        // Change user role
        $result = $this->rbac->changeUserRole($this->testUserId, 'verified', $this->adminUserId, 'Test role change');
        $this->assertTrue($result);
        
        // Test new role permissions
        $this->assertTrue($this->rbac->hasRole('verified', $this->testUserId));
        $this->assertTrue($this->rbac->hasPermission('content.publish', $this->testUserId));
        
        // Test invalid role change
        $invalidResult = $this->rbac->changeUserRole($this->testUserId, 'invalid_role', $this->adminUserId);
        $this->assertFalse($invalidResult);
    }
    
    /**
     * Test user suspension
     */
    public function testUserSuspension()
    {
        // Suspend user
        $result = $this->rbac->suspendUser($this->testUserId, 'Test suspension', $this->adminUserId);
        $this->assertTrue($result);
        
        // Test suspended user has no permissions
        $this->assertFalse($this->rbac->hasPermission('content.view', $this->testUserId));
        $this->assertFalse($this->rbac->hasPermission('content.create', $this->testUserId));
        
        // Reinstate user
        $reinstateResult = $this->rbac->reinstateUser($this->testUserId, 'Test reinstatement', $this->adminUserId);
        $this->assertTrue($reinstateResult);
        
        // Test reinstated user has permissions again
        $this->assertTrue($this->rbac->hasPermission('content.view', $this->testUserId));
        $this->assertTrue($this->rbac->hasPermission('content.create', $this->testUserId));
    }
    
    /**
     * Test user banning
     */
    public function testUserBanning()
    {
        // Ban user
        $result = $this->rbac->banUser($this->testUserId, 'Test ban', $this->adminUserId, false);
        $this->assertTrue($result);
        
        // Test banned user has no permissions
        $this->assertFalse($this->rbac->hasPermission('content.view', $this->testUserId));
        $this->assertFalse($this->rbac->hasRole('member', $this->testUserId));
        
        // Reinstate user
        $reinstateResult = $this->rbac->reinstateUser($this->testUserId, 'Test unban', $this->adminUserId);
        $this->assertTrue($reinstateResult);
        
        // Test unbanned user has permissions again
        $this->assertTrue($this->rbac->hasPermission('content.view', $this->testUserId));
    }
    
    /**
     * Test context-based permissions
     */
    public function testContextPermissions()
    {
        // Test content ownership context
        $context = ['content_owner_id' => $this->testUserId];
        
        // User should be able to edit their own content
        $this->assertTrue($this->rbac->hasPermission('content.edit', $this->testUserId, $context));
        $this->assertTrue($this->rbac->hasPermission('content.delete', $this->testUserId, $context));
        
        // User should not be able to edit others' content without permission
        $otherContext = ['content_owner_id' => $this->adminUserId];
        $this->assertFalse($this->rbac->hasPermission('content.moderate', $this->testUserId, $otherContext));
    }
    
    /**
     * Test permission middleware
     */
    public function testPermissionMiddleware()
    {
        // Mock current user session
        $_SESSION['USER_ID'] = $this->testUserId;
        $_SESSION['USERNAME'] = 'rbactest';
        $_SESSION['EMAIL'] = 'rbactest@example.com';
        $_SESSION['ROLE'] = 'member';
        $_SESSION['LOGIN_TIME'] = time();
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Test successful permission check
        $this->expectOutputString(''); // No output expected for successful check
        ob_start();
        $result = $this->rbac->requirePermission('content.view');
        $output = ob_get_clean();
        
        $this->assertTrue($result);
        $this->assertEmpty($output);
        
        // Clear session for next test
        $_SESSION = [];
    }
    
    /**
     * Test role middleware
     */
    public function testRoleMiddleware()
    {
        // Mock current user session
        $_SESSION['USER_ID'] = $this->adminUserId;
        $_SESSION['USERNAME'] = 'rbacadmin';
        $_SESSION['EMAIL'] = 'rbacadmin@example.com';
        $_SESSION['ROLE'] = 'admin';
        $_SESSION['LOGIN_TIME'] = time();
        $_SESSION['LAST_ACTIVITY'] = time();
        
        // Test successful role check
        $this->expectOutputString(''); // No output expected for successful check
        ob_start();
        $result = $this->rbac->requireRole('admin');
        $output = ob_get_clean();
        
        $this->assertTrue($result);
        $this->assertEmpty($output);
        
        // Clear session
        $_SESSION = [];
    }
    
    /**
     * Test getting user permissions
     */
    public function testGetUserPermissions()
    {
        $permissions = $this->rbac->getUserPermissions($this->testUserId);
        
        $this->assertIsArray($permissions);
        $this->assertContains('content.view', $permissions);
        $this->assertContains('content.create', $permissions);
        $this->assertNotContains('admin.dashboard', $permissions);
        
        // Test admin permissions
        $adminPermissions = $this->rbac->getUserPermissions($this->adminUserId);
        $this->assertContains('admin.dashboard', $adminPermissions);
        $this->assertContains('user.ban', $adminPermissions);
    }
    
    /**
     * Test getting role permissions
     */
    public function testGetRolePermissions()
    {
        $memberPermissions = $this->rbac->getRolePermissions('member');
        $this->assertIsArray($memberPermissions);
        $this->assertContains('content.view', $memberPermissions);
        $this->assertContains('content.create', $memberPermissions);
        
        $adminPermissions = $this->rbac->getRolePermissions('admin');
        $this->assertContains('admin.dashboard', $adminPermissions);
        $this->assertContains('user.ban', $adminPermissions);
        
        $guestPermissions = $this->rbac->getRolePermissions('guest');
        $this->assertContains('content.view', $guestPermissions);
        $this->assertNotContains('content.create', $guestPermissions);
    }
    
    /**
     * Test permission validation edge cases
     */
    public function testPermissionEdgeCases()
    {
        // Test with non-existent user
        $this->assertFalse($this->rbac->hasPermission('content.view', 99999));
        
        // Test with invalid permission
        $this->assertFalse($this->rbac->hasPermission('invalid.permission', $this->testUserId));
        
        // Test with null user ID and no session
        $this->assertTrue($this->rbac->hasPermission('content.view')); // Should check guest permissions
        
        // Test with empty permission
        $this->assertFalse($this->rbac->hasPermission('', $this->testUserId));
    }
    
    /**
     * Test permission caching
     */
    public function testPermissionCaching()
    {
        // First call should query database
        $permissions1 = $this->rbac->getUserPermissions($this->testUserId);
        
        // Second call should use cache
        $permissions2 = $this->rbac->getUserPermissions($this->testUserId);
        
        $this->assertEquals($permissions1, $permissions2);
        
        // Grant new permission (should clear cache)
        $this->rbac->grantPermission($this->testUserId, 'feature.beta', $this->adminUserId);
        
        // Should get updated permissions
        $permissions3 = $this->rbac->getUserPermissions($this->testUserId);
        $this->assertContains('feature.beta', $permissions3);
    }
    
    /**
     * Test error handling
     */
    public function testErrorHandling()
    {
        // Test with invalid parameters
        $result = $this->rbac->grantPermission(null, 'test.permission', $this->adminUserId);
        $this->assertFalse($result);
        
        $result = $this->rbac->changeUserRole($this->testUserId, null, $this->adminUserId);
        $this->assertFalse($result);
        
        $result = $this->rbac->suspendUser(null, 'test', $this->adminUserId);
        $this->assertFalse($result);
    }
}