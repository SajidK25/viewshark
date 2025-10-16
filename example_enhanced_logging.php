<?php
/*******************************************************************************************************************
| Example: Enhanced Logging Implementation
| This file demonstrates how to use the new logging features
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once 'f_core/config.core.php';

// Example 1: Basic logging
$logger = VLogger::getInstance();

// Different log levels
$logger->info('User logged in successfully', ['user_id' => 123]);
$logger->warning('Failed login attempt', ['username' => 'testuser', 'ip' => '192.168.1.1']);
$logger->error('Database connection failed', ['host' => 'localhost', 'database' => 'easystream']);

// Example 2: Security event logging
log_security_event('Suspicious file upload attempt', [
    'filename' => 'malicious.php',
    'mime_type' => 'application/x-php',
    'user_id' => $_SESSION['USER_ID'] ?? null
]);

// Example 3: Performance monitoring
$startTime = microtime(true);

// Simulate some processing
sleep(1);

$executionTime = microtime(true) - $startTime;
if ($executionTime > 0.5) { // Log if takes more than 500ms
    log_performance_issue('Slow database query', $executionTime, [
        'query_type' => 'user_search',
        'parameters' => ['search_term' => 'example']
    ]);
}

// Example 4: Validation error logging
function validateUserInput($data) {
    $errors = [];
    
    if (empty($data['username'])) {
        log_validation_error('username', $data['username'] ?? '', 'required');
        $errors[] = 'Username is required';
    }
    
    if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
        log_validation_error('email', $data['email'] ?? '', 'valid_email');
        $errors[] = 'Valid email is required';
    }
    
    return $errors;
}

// Example 5: Database operation with error logging
function updateUserProfile($userId, $data) {
    global $class_database;
    
    try {
        $result = $class_database->doUpdate('db_accountuser', 'usr_id', $data, $userId);
        
        if ($result) {
            $logger = VLogger::getInstance();
            $logger->info('User profile updated', [
                'user_id' => $userId,
                'updated_fields' => array_keys($data)
            ]);
            return true;
        } else {
            log_app_error('Failed to update user profile', [
                'user_id' => $userId,
                'data' => $data
            ]);
            return false;
        }
    } catch (Exception $e) {
        log_app_error('Exception during profile update: ' . $e->getMessage(), [
            'user_id' => $userId,
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

// Example 6: Authentication logging
function authenticateUser($username, $password) {
    global $class_database;
    
    // Rate limiting check
    $clientIP = $_SERVER['REMOTE_ADDR'];
    if (!check_rate_limit('login_' . $clientIP, 5, 300)) {
        log_auth_error('Rate limit exceeded for login attempts', $username, [
            'ip' => $clientIP,
            'rate_limit_exceeded' => true
        ]);
        return false;
    }
    
    // Validate credentials (simplified example)
    $user = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $username);
    
    if (!$user) {
        log_auth_error('Login attempt with non-existent username', $username);
        return false;
    }
    
    // Password verification would go here
    $passwordValid = true; // Simplified
    
    if (!$passwordValid) {
        log_auth_error('Login attempt with invalid password', $username, [
            'user_id' => $user
        ]);
        return false;
    }
    
    // Successful login
    $logger = VLogger::getInstance();
    $logger->info('User logged in successfully', [
        'user_id' => $user,
        'username' => $username
    ]);
    
    return $user;
}

// Example 7: File upload logging
function handleFileUpload($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    $validation = validate_file_upload($file, $allowedTypes, $maxSize);
    
    if (!$validation['valid']) {
        log_security_event('Invalid file upload attempt', [
            'filename' => $file['name'],
            'size' => $file['size'],
            'type' => $file['type'],
            'error' => $validation['error']
        ]);
        return false;
    }
    
    // Process upload
    $uploadDir = 'f_data/data_userfiles/uploads/';
    $filename = uniqid() . '_' . basename($file['name']);
    $uploadPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $logger = VLogger::getInstance();
        $logger->info('File uploaded successfully', [
            'original_filename' => $file['name'],
            'stored_filename' => $filename,
            'size' => $file['size'],
            'mime_type' => $validation['mime_type']
        ]);
        return $filename;
    } else {
        log_app_error('Failed to move uploaded file', [
            'filename' => $file['name'],
            'destination' => $uploadPath
        ]);
        return false;
    }
}

// Example 8: API request logging
function logAPIRequest($endpoint, $method, $params, $response, $executionTime) {
    $logger = VLogger::getInstance();
    
    $logLevel = VLogger::INFO;
    $context = [
        'api_endpoint' => $endpoint,
        'method' => $method,
        'parameters' => $params,
        'response_size' => strlen(json_encode($response)),
        'execution_time' => $executionTime
    ];
    
    // Log as warning if slow
    if ($executionTime > 2.0) {
        $logLevel = VLogger::WARNING;
        $context['slow_request'] = true;
    }
    
    $logger->log($logLevel, "API request to {$endpoint}", $context);
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $startTime = microtime(true);
    
    // Simulate API processing
    $response = ['status' => 'success', 'data' => []];
    
    $executionTime = microtime(true) - $startTime;
    logAPIRequest('/api/users', 'POST', $_POST, $response, $executionTime);
}

echo "Enhanced logging examples completed. Check the logs in f_data/logs/";
?>