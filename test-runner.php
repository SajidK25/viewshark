<?php
/**
 * Simple Test Runner for EasyStream
 * This script runs basic tests to verify the enhanced security infrastructure
 */

define('_ISVALID', true);
require_once 'f_core/config.core.php';

echo "🧪 EasyStream Enhanced Security Test Runner\n";
echo "==========================================\n\n";

$tests = [];
$passed = 0;
$failed = 0;

function runTest($name, $callback) {
    global $tests, $passed, $failed;
    
    echo "Testing: {$name}... ";
    
    try {
        $result = $callback();
        if ($result) {
            echo "✅ PASSED\n";
            $passed++;
        } else {
            echo "❌ FAILED\n";
            $failed++;
        }
        $tests[] = ['name' => $name, 'result' => $result];
    } catch (Exception $e) {
        echo "❌ ERROR: " . $e->getMessage() . "\n";
        $failed++;
        $tests[] = ['name' => $name, 'result' => false, 'error' => $e->getMessage()];
    }
}

// Test 1: VSecurity class exists and can be instantiated
runTest('VSecurity class instantiation', function() {
    $security = VSecurity::getInstance();
    return $security instanceof VSecurity;
});

// Test 2: Input validation works
runTest('Basic input validation', function() {
    $result1 = VSecurity::validateInput('123', 'int');
    $result2 = VSecurity::validateInput('abc', 'int');
    $result3 = VSecurity::validateInput('test@example.com', 'email');
    
    return $result1 === 123 && $result2 === null && $result3 === 'test@example.com';
});

// Test 3: CSRF token generation
runTest('CSRF token generation', function() {
    $token = VSecurity::generateCSRFToken('test_action');
    return is_string($token) && strlen($token) === 64;
});

// Test 4: CSRF token validation
runTest('CSRF token validation', function() {
    $token = VSecurity::generateCSRFToken('validation_test');
    $valid = VSecurity::validateCSRFToken($token, 'validation_test');
    $invalid = VSecurity::validateCSRFToken('invalid_token', 'validation_test');
    
    return $valid === true && $invalid === false;
});

// Test 5: Rate limiting
runTest('Rate limiting functionality', function() {
    $key = 'test_' . uniqid();
    
    // Should allow first few attempts
    $allowed = true;
    for ($i = 0; $i < 3; $i++) {
        if (!VSecurity::checkRateLimit($key, 5, 60)) {
            $allowed = false;
            break;
        }
    }
    
    // Should block after limit
    for ($i = 0; $i < 5; $i++) {
        VSecurity::checkRateLimit($key, 5, 60);
    }
    $blocked = !VSecurity::checkRateLimit($key, 5, 60);
    
    return $allowed && $blocked;
});

// Test 6: File upload validation
runTest('File upload validation', function() {
    // Create a mock file array
    $validFile = [
        'name' => 'test.txt',
        'type' => 'text/plain',
        'tmp_name' => __FILE__, // Use this file as test
        'error' => UPLOAD_ERR_OK,
        'size' => filesize(__FILE__)
    ];
    
    $result = VSecurity::validateFileUpload($validFile, ['text/plain'], 1024000);
    return $result['valid'] === true;
});

// Test 7: XSS prevention
runTest('XSS prevention', function() {
    $malicious = '<script>alert("xss")</script>';
    $sanitized = VSecurity::validateInput($malicious, 'string');
    
    return !str_contains(strtolower($sanitized), '<script>');
});

// Test 8: VLogger class functionality
runTest('VLogger functionality', function() {
    $logger = VLogger::getInstance();
    
    // Test logging doesn't throw exceptions
    $logger->info('Test log message');
    $logger->warning('Test warning');
    $logger->error('Test error');
    
    return $logger instanceof VLogger;
});

// Test 9: VErrorHandler functionality
runTest('VErrorHandler functionality', function() {
    $errorHandler = VErrorHandler::getInstance();
    
    // Test error logging doesn't throw exceptions
    $errorHandler->logApplicationError('Test application error');
    $errorHandler->logValidationError('test_field', 'test_value', 'required');
    
    return $errorHandler instanceof VErrorHandler;
});

// Test 10: VAuth class functionality
runTest('VAuth authentication system', function() {
    $auth = VAuth::getInstance();
    
    // Test registration
    $userData = [
        'username' => 'testrunner',
        'email' => 'testrunner@example.com',
        'password' => 'TestPassword123!'
    ];
    
    $result = $auth->register($userData);
    if (!$result['success']) {
        return false;
    }
    
    // Test login
    $loginResult = $auth->login('testrunner', 'TestPassword123!');
    if (!$loginResult['success']) {
        return false;
    }
    
    // Test authentication check
    $isAuth = $auth->isAuthenticated();
    
    // Test logout
    $logoutResult = $auth->logout();
    
    // Cleanup
    global $class_database;
    $db = $class_database->dbConnection();
    $db->Execute("DELETE FROM db_users WHERE email = ?", ['testrunner@example.com']);
    
    return $isAuth && $logoutResult['success'];
});

// Test 11: RBAC system functionality
runTest('RBAC permission system', function() {
    $rbac = VRBAC::getInstance();
    
    // Test guest permissions
    $hasGuestPermission = $rbac->hasPermission('content.view');
    $lacksAdminPermission = !$rbac->hasPermission('admin.dashboard');
    
    // Test role hierarchy
    $hasGuestRole = $rbac->hasRole('guest');
    $lacksAdminRole = !$rbac->hasRole('admin');
    
    return $hasGuestPermission && $lacksAdminPermission && $hasGuestRole && $lacksAdminRole;
});

// Test 12: Middleware system functionality
runTest('Middleware protection system', function() {
    $middleware = VMiddleware::getInstance();
    
    // Test guest middleware (should pass when not authenticated)
    ob_start();
    $guestResult = $middleware->requireGuest();
    ob_end_clean();
    
    // Test CSRF token generation
    $token = VSecurity::generateCSRFToken('test');
    $hasToken = !empty($token) && strlen($token) === 64;
    
    return $guestResult && $hasToken;
});

// Test 13: Enhanced security features
runTest('Enhanced security features', function() {
    // Test suspicious activity detection
    $userId = 'test_user_' . uniqid();
    $normal = VSecurity::detectSuspiciousActivity($userId, 'failed_logins');
    
    // Test advanced file upload (if method exists)
    if (method_exists('VSecurity', 'validateFileUploadAdvanced')) {
        $testFile = [
            'name' => 'test.txt',
            'type' => 'text/plain',
            'tmp_name' => __FILE__,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize(__FILE__)
        ];
        
        $result = VSecurity::validateFileUploadAdvanced($testFile, ['text/plain'], 1024000, false);
        return !$normal && $result['valid'];
    }
    
    return !$normal;
});

echo "\n" . str_repeat("=", 50) . "\n";
echo "Test Results Summary:\n";
echo "✅ Passed: {$passed}\n";
echo "❌ Failed: {$failed}\n";
echo "📊 Total: " . ($passed + $failed) . "\n";

if ($failed === 0) {
    echo "\n🎉 All tests passed! Enhanced security infrastructure is working correctly.\n";
    exit(0);
} else {
    echo "\n⚠️  Some tests failed. Please review the implementation.\n";
    exit(1);
}