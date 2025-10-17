<?php

namespace EasyStream\Tests\Performance;

use PHPUnit\Framework\TestCase;
use VAuth;
use VRBAC;
use VSecurity;

class AuthPerformanceTest extends TestCase
{
    private $auth;
    private $rbac;
    private $testUsers = [];
    
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
            'HTTP_USER_AGENT' => 'PHPUnit Performance Test',
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTPS' => '1'
        ];
        
        // Clean up any existing test data
        $this->cleanupTestData();
        
        // Create test users for performance testing
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
        
        // Clean up test users
        for ($i = 1; $i <= 10; $i++) {
            $email = "perftest{$i}@example.com";
            $userId = $db->GetOne("SELECT user_id FROM db_users WHERE email = ?", [$email]);
            if ($userId) {
                $db->Execute("DELETE FROM db_user_permissions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_sessions WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_login_history WHERE user_id = ?", [$userId]);
                $db->Execute("DELETE FROM db_users WHERE user_id = ?", [$userId]);
            }
        }
    }
    
    private function createTestUsers()
    {
        for ($i = 1; $i <= 10; $i++) {
            $userData = [
                'username' => "perftest{$i}",
                'email' => "perftest{$i}@example.com",
                'password' => 'TestPassword123!'
            ];
            
            $result = $this->auth->register($userData);
            if ($result['success']) {
                $this->testUsers[] = [
                    'user_id' => $result['user_id'],
                    'username' => $userData['username'],
                    'email' => $userData['email']
                ];
                
                // Verify email
                global $class_database;
                $db = $class_database->dbConnection();
                $token = $db->GetOne("SELECT verification_token FROM db_users WHERE user_id = ?", [$result['user_id']]);
                if ($token) {
                    $this->auth->verifyEmail($token);
                }
            }
        }
    }
    
    /**
     * Test login performance
     */
    public function testLoginPerformance()
    {
        $iterations = 50;
        $maxTimePerLogin = 0.1; // 100ms max per login
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $userIndex = $i % count($this->testUsers);
            $user = $this->testUsers[$userIndex];
            
            $loginStart = microtime(true);
            $result = $this->auth->login($user['username'], 'TestPassword123!');
            $loginTime = microtime(true) - $loginStart;
            
            $this->assertTrue($result['success'], "Login should succeed for user {$user['username']}");
            $this->assertLessThan($maxTimePerLogin, $loginTime, "Login should complete within {$maxTimePerLogin}s");
            
            $this->auth->logout();
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        $this->assertLessThan($maxTimePerLogin, $averageTime, "Average login time should be under {$maxTimePerLogin}s");
        
        echo "\nLogin Performance: {$iterations} logins in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 2) . "ms per login)\n";
    }
    
    /**
     * Test permission checking performance
     */
    public function testPermissionCheckingPerformance()
    {
        // Login a user
        $user = $this->testUsers[0];
        $this->auth->login($user['username'], 'TestPassword123!');
        
        $permissions = [
            'content.view', 'content.create', 'content.edit', 'content.delete',
            'comment.view', 'comment.create', 'upload.video', 'upload.image',
            'admin.dashboard', 'user.ban', 'api.access', 'feature.beta'
        ];
        
        $iterations = 1000;
        $maxTimePerCheck = 0.001; // 1ms max per permission check
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $permission = $permissions[$i % count($permissions)];
            
            $checkStart = microtime(true);
            $hasPermission = $this->rbac->hasPermission($permission);
            $checkTime = microtime(true) - $checkStart;
            
            $this->assertIsBool($hasPermission);
            $this->assertLessThan($maxTimePerCheck, $checkTime, "Permission check should complete within {$maxTimePerCheck}s");
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        $this->assertLessThan($maxTimePerCheck, $averageTime, "Average permission check time should be under {$maxTimePerCheck}s");
        
        echo "\nPermission Check Performance: {$iterations} checks in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 3) . "ms per check)\n";
        
        $this->auth->logout();
    }
    
    /**
     * Test session validation performance
     */
    public function testSessionValidationPerformance()
    {
        // Login a user
        $user = $this->testUsers[0];
        $this->auth->login($user['username'], 'TestPassword123!');
        
        $iterations = 500;
        $maxTimePerValidation = 0.005; // 5ms max per validation
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $validationStart = microtime(true);
            $isAuthenticated = $this->auth->isAuthenticated();
            $validationTime = microtime(true) - $validationStart;
            
            $this->assertTrue($isAuthenticated);
            $this->assertLessThan($maxTimePerValidation, $validationTime, "Session validation should complete within {$maxTimePerValidation}s");
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        $this->assertLessThan($maxTimePerValidation, $averageTime, "Average session validation time should be under {$maxTimePerValidation}s");
        
        echo "\nSession Validation Performance: {$iterations} validations in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 3) . "ms per validation)\n";
        
        $this->auth->logout();
    }
    
    /**
     * Test concurrent login performance
     */
    public function testConcurrentLoginPerformance()
    {
        $concurrentLogins = 10;
        $maxTotalTime = 2.0; // 2 seconds max for all concurrent logins
        
        $startTime = microtime(true);
        
        // Simulate concurrent logins by rapidly switching sessions
        for ($i = 0; $i < $concurrentLogins; $i++) {
            $user = $this->testUsers[$i];
            
            // Create new session for each "concurrent" user
            session_write_close();
            session_id('perf_test_session_' . $i);
            session_start();
            
            $result = $this->auth->login($user['username'], 'TestPassword123!');
            $this->assertTrue($result['success'], "Concurrent login should succeed for user {$user['username']}");
        }
        
        $totalTime = microtime(true) - $startTime;
        
        $this->assertLessThan($maxTotalTime, $totalTime, "Concurrent logins should complete within {$maxTotalTime}s");
        
        echo "\nConcurrent Login Performance: {$concurrentLogins} concurrent logins in " . number_format($totalTime, 3) . "s\n";
        
        // Cleanup sessions
        for ($i = 0; $i < $concurrentLogins; $i++) {
            session_write_close();
            session_id('perf_test_session_' . $i);
            session_start();
            $this->auth->logout();
        }
    }
    
    /**
     * Test password hashing performance
     */
    public function testPasswordHashingPerformance()
    {
        $iterations = 10;
        $maxTimePerHash = 0.5; // 500ms max per hash (password hashing should be slow)
        $minTimePerHash = 0.01; // 10ms min per hash (ensure it's not too fast)
        
        $passwords = [
            'TestPassword123!',
            'AnotherPassword456@',
            'ComplexPassword789#',
            'SecurePassword012$'
        ];
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $password = $passwords[$i % count($passwords)];
            
            $hashStart = microtime(true);
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $hashTime = microtime(true) - $hashStart;
            
            $this->assertNotEmpty($hash);
            $this->assertLessThan($maxTimePerHash, $hashTime, "Password hashing should complete within {$maxTimePerHash}s");
            $this->assertGreaterThan($minTimePerHash, $hashTime, "Password hashing should take at least {$minTimePerHash}s for security");
            
            // Verify the hash works
            $this->assertTrue(password_verify($password, $hash));
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        echo "\nPassword Hashing Performance: {$iterations} hashes in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 2) . "ms per hash)\n";
    }
    
    /**
     * Test CSRF token generation performance
     */
    public function testCSRFTokenPerformance()
    {
        $iterations = 1000;
        $maxTimePerToken = 0.001; // 1ms max per token generation
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $tokenStart = microtime(true);
            $token = VSecurity::generateCSRFToken('test_action_' . $i);
            $tokenTime = microtime(true) - $tokenStart;
            
            $this->assertNotEmpty($token);
            $this->assertEquals(64, strlen($token));
            $this->assertLessThan($maxTimePerToken, $tokenTime, "CSRF token generation should complete within {$maxTimePerToken}s");
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        $this->assertLessThan($maxTimePerToken, $averageTime, "Average CSRF token generation time should be under {$maxTimePerToken}s");
        
        echo "\nCSRF Token Performance: {$iterations} tokens in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 3) . "ms per token)\n";
    }
    
    /**
     * Test database query performance for authentication
     */
    public function testDatabaseQueryPerformance()
    {
        $iterations = 100;
        $maxTimePerQuery = 0.01; // 10ms max per query
        
        global $class_database;
        $db = $class_database->dbConnection();
        
        // Test user lookup queries
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $user = $this->testUsers[$i % count($this->testUsers)];
            
            $queryStart = microtime(true);
            $result = $db->Execute("SELECT * FROM db_users WHERE username = ? OR email = ?", 
                                  [$user['username'], $user['email']]);
            $queryTime = microtime(true) - $queryStart;
            
            $this->assertNotFalse($result);
            $this->assertFalse($result->EOF);
            $this->assertLessThan($maxTimePerQuery, $queryTime, "User lookup query should complete within {$maxTimePerQuery}s");
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        $this->assertLessThan($maxTimePerQuery, $averageTime, "Average query time should be under {$maxTimePerQuery}s");
        
        echo "\nDatabase Query Performance: {$iterations} user lookups in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 3) . "ms per query)\n";
    }
    
    /**
     * Test memory usage during authentication operations
     */
    public function testMemoryUsage()
    {
        $initialMemory = memory_get_usage();
        $maxMemoryIncrease = 5 * 1024 * 1024; // 5MB max increase
        
        // Perform various authentication operations
        $user = $this->testUsers[0];
        
        // Login/logout cycle
        for ($i = 0; $i < 10; $i++) {
            $this->auth->login($user['username'], 'TestPassword123!');
            $this->auth->logout();
        }
        
        // Permission checks
        $this->auth->login($user['username'], 'TestPassword123!');
        for ($i = 0; $i < 100; $i++) {
            $this->rbac->hasPermission('content.view');
            $this->rbac->hasPermission('admin.dashboard');
        }
        $this->auth->logout();
        
        // CSRF token generation
        for ($i = 0; $i < 100; $i++) {
            VSecurity::generateCSRFToken('test_' . $i);
        }
        
        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;
        
        $this->assertLessThan($maxMemoryIncrease, $memoryIncrease, 
                             "Memory usage should not increase by more than " . ($maxMemoryIncrease / 1024 / 1024) . "MB");
        
        echo "\nMemory Usage: " . number_format($memoryIncrease / 1024, 2) . "KB increase";
        echo " (Peak: " . number_format(memory_get_peak_usage() / 1024 / 1024, 2) . "MB)\n";
    }
    
    /**
     * Test rate limiting performance impact
     */
    public function testRateLimitingPerformance()
    {
        $iterations = 100;
        $maxTimePerCheck = 0.002; // 2ms max per rate limit check
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $key = 'perf_test_' . ($i % 10); // Use 10 different keys
            
            $checkStart = microtime(true);
            $allowed = VSecurity::checkRateLimit($key, 100, 3600, 'test_action');
            $checkTime = microtime(true) - $checkStart;
            
            $this->assertIsBool($allowed);
            $this->assertLessThan($maxTimePerCheck, $checkTime, "Rate limit check should complete within {$maxTimePerCheck}s");
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $iterations;
        
        $this->assertLessThan($maxTimePerCheck, $averageTime, "Average rate limit check time should be under {$maxTimePerCheck}s");
        
        echo "\nRate Limiting Performance: {$iterations} checks in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 3) . "ms per check)\n";
    }
    
    /**
     * Test overall authentication system performance under load
     */
    public function testOverallSystemPerformance()
    {
        $operations = 200;
        $maxTotalTime = 5.0; // 5 seconds max for all operations
        
        $startTime = microtime(true);
        
        for ($i = 0; $i < $operations; $i++) {
            $user = $this->testUsers[$i % count($this->testUsers)];
            
            // Mix of operations
            switch ($i % 4) {
                case 0:
                    // Login/logout
                    $this->auth->login($user['username'], 'TestPassword123!');
                    $this->auth->logout();
                    break;
                    
                case 1:
                    // Permission check
                    $this->auth->login($user['username'], 'TestPassword123!');
                    $this->rbac->hasPermission('content.create');
                    $this->auth->logout();
                    break;
                    
                case 2:
                    // CSRF token generation
                    VSecurity::generateCSRFToken('mixed_test_' . $i);
                    break;
                    
                case 3:
                    // Rate limit check
                    VSecurity::checkRateLimit('mixed_' . ($i % 5), 50, 3600, 'mixed_test');
                    break;
            }
        }
        
        $totalTime = microtime(true) - $startTime;
        $averageTime = $totalTime / $operations;
        
        $this->assertLessThan($maxTotalTime, $totalTime, "Overall system should handle {$operations} operations within {$maxTotalTime}s");
        
        echo "\nOverall System Performance: {$operations} mixed operations in " . number_format($totalTime, 3) . "s";
        echo " (avg: " . number_format($averageTime * 1000, 3) . "ms per operation)\n";
    }
}