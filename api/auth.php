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

define('_ISVALID', true);

// Set JSON content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../f_core/config.core.php';

// Initialize classes
$auth = VAuth::getInstance();
$security = VSecurity::getInstance();
$logger = VLogger::getInstance();

/**
 * Send JSON response
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get JSON input
 */
function getJsonInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?: [];
}

/**
 * Validate required fields
 */
function validateRequired($data, $fields) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

try {
    // Get request method and action
    $method = $_SERVER['REQUEST_METHOD'];
    $action = VSecurity::getParam('action', 'string') ?: VSecurity::postParam('action', 'string');
    
    // Route requests based on action
    switch ($action) {
        case 'register':
            if ($method !== 'POST') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            // Validate CSRF token
            if (!VSecurity::validateCSRFFromPost('register')) {
                sendResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }
            
            $data = array_merge($_POST, getJsonInput());
            $missing = validateRequired($data, ['username', 'email', 'password']);
            
            if (!empty($missing)) {
                sendResponse([
                    'success' => false, 
                    'message' => 'Missing required fields: ' . implode(', ', $missing)
                ], 400);
            }
            
            $result = $auth->register($data);
            sendResponse($result, $result['success'] ? 201 : 400);
            break;
            
        case 'verify_email':
            if ($method !== 'POST') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            $data = array_merge($_POST, getJsonInput());
            $token = $data['token'] ?? '';
            
            if (empty($token)) {
                sendResponse(['success' => false, 'message' => 'Verification token is required'], 400);
            }
            
            $result = $auth->verifyEmail($token);
            sendResponse($result, $result['success'] ? 200 : 400);
            break;
            
        case 'login':
            if ($method !== 'POST') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            // Validate CSRF token
            if (!VSecurity::validateCSRFFromPost('login')) {
                sendResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }
            
            $data = array_merge($_POST, getJsonInput());
            $missing = validateRequired($data, ['identifier', 'password']);
            
            if (!empty($missing)) {
                sendResponse([
                    'success' => false, 
                    'message' => 'Username/email and password are required'
                ], 400);
            }
            
            $rememberMe = !empty($data['remember_me']);
            $result = $auth->login($data['identifier'], $data['password'], $rememberMe);
            sendResponse($result, $result['success'] ? 200 : 401);
            break;
            
        case 'logout':
            if ($method !== 'POST') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            // Validate CSRF token
            if (!VSecurity::validateCSRFFromPost('logout')) {
                sendResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }
            
            $result = $auth->logout();
            sendResponse($result);
            break;
            
        case 'me':
            if ($method !== 'GET') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            if (!$auth->isAuthenticated()) {
                sendResponse(['success' => false, 'message' => 'Not authenticated'], 401);
            }
            
            $user = $auth->getCurrentUser();
            sendResponse(['success' => true, 'user' => $user]);
            break;
            
        case 'request_password_reset':
            if ($method !== 'POST') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            // Validate CSRF token
            if (!VSecurity::validateCSRFFromPost('password_reset')) {
                sendResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }
            
            $data = array_merge($_POST, getJsonInput());
            $email = $data['email'] ?? '';
            
            if (empty($email)) {
                sendResponse(['success' => false, 'message' => 'Email is required'], 400);
            }
            
            $result = $auth->requestPasswordReset($email);
            sendResponse($result);
            break;
            
        case 'reset_password':
            if ($method !== 'POST') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            // Validate CSRF token
            if (!VSecurity::validateCSRFFromPost('password_reset')) {
                sendResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
            }
            
            $data = array_merge($_POST, getJsonInput());
            $missing = validateRequired($data, ['token', 'password']);
            
            if (!empty($missing)) {
                sendResponse([
                    'success' => false, 
                    'message' => 'Reset token and new password are required'
                ], 400);
            }
            
            $result = $auth->resetPassword($data['token'], $data['password']);
            sendResponse($result, $result['success'] ? 200 : 400);
            break;
            
        case 'csrf_token':
            if ($method !== 'GET') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            $action = VSecurity::getParam('for', 'string', 'default');
            $token = VSecurity::generateCSRFToken($action);
            
            sendResponse([
                'success' => true, 
                'token' => $token,
                'action' => $action
            ]);
            break;
            
        case 'status':
            if ($method !== 'GET') {
                sendResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            }
            
            $isAuthenticated = $auth->isAuthenticated();
            $user = $isAuthenticated ? $auth->getCurrentUser() : null;
            
            sendResponse([
                'success' => true,
                'authenticated' => $isAuthenticated,
                'user' => $user
            ]);
            break;
            
        default:
            sendResponse(['success' => false, 'message' => 'Invalid action'], 400);
    }
    
} catch (Exception $e) {
    $logger->error('Auth API error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'action' => $action ?? 'unknown',
        'method' => $method ?? 'unknown'
    ]);
    
    sendResponse([
        'success' => false, 
        'message' => 'An internal error occurred'
    ], 500);
}