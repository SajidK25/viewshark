<?php
/**
 * PHPUnit Bootstrap for EasyStream Testing
 */

// Define testing environment
define('_ISVALID', true);
define('TESTING', true);

// Set error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set timezone
date_default_timezone_set('UTC');

// Start session for testing
if (!session_id()) {
    session_start();
}

// Include core configuration
require_once __DIR__ . '/../f_core/config.core.php';

// Override database configuration for testing
global $cfg;
$cfg['db_host'] = getenv('DB_HOST') ?: 'test-db';
$cfg['db_name'] = getenv('DB_NAME') ?: 'easystream_test';
$cfg['db_user'] = getenv('DB_USER') ?: 'test';
$cfg['db_pass'] = getenv('DB_PASS') ?: 'test';

// Enable debug mode for testing
$cfg['debug_mode'] = true;
$cfg['logging_database_logging'] = true;
$cfg['error_alerts'] = false; // Disable email alerts during testing

// Create test directories
$testDirs = [
    __DIR__ . '/temp',
    __DIR__ . '/fixtures',
    __DIR__ . '/coverage',
    __DIR__ . '/results',
    'f_data/logs/test',
    'f_data/uploads/test',
    'f_data/cache/test'
];

foreach ($testDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Test helper functions
function createTestUser($username = 'testuser', $email = 'test@example.com', $role = 'member')
{
    global $db;
    
    $hashedPassword = password_hash('testpassword', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO `db_users` (`username`, `email`, `password_hash`, `role`, `status`, `email_verified`, `created_at`) 
            VALUES (?, ?, ?, ?, 'active', 1, NOW())";
    
    $result = $db->Execute($sql, [$username, $email, $hashedPassword, $role]);
    
    if ($result) {
        return $db->Insert_ID();
    }
    
    return false;
}

function cleanupTestData()
{
    global $db;
    
    // Clean up test data
    $tables = ['db_users', 'db_videofiles', 'db_livefiles', 'db_comments', 'db_interactions', 'db_logs'];
    
    foreach ($tables as $table) {
        $db->Execute("DELETE FROM `{$table}` WHERE `created_at` >= DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    }
    
    // Clean up test files
    $testFiles = glob(__DIR__ . '/temp/*');
    foreach ($testFiles as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}

function createTestVideo($userId = null, $title = 'Test Video', $status = 'ready')
{
    global $db;
    
    if (!$userId) {
        $userId = createTestUser();
    }
    
    $sql = "INSERT INTO `db_videofiles` (`user_id`, `title`, `filename`, `status`, `privacy`, `created_at`) 
            VALUES (?, ?, 'test_video.mp4', ?, 'public', NOW())";
    
    $result = $db->Execute($sql, [$userId, $title, $status]);
    
    if ($result) {
        return $db->Insert_ID();
    }
    
    return false;
}

function createTestStream($userId = null, $title = 'Test Stream', $status = 'scheduled')
{
    global $db;
    
    if (!$userId) {
        $userId = createTestUser();
    }
    
    $streamKey = 'test_' . uniqid();
    
    $sql = "INSERT INTO `db_livefiles` (`user_id`, `title`, `stream_key`, `status`, `privacy`, `created_at`) 
            VALUES (?, ?, ?, ?, 'public', NOW())";
    
    $result = $db->Execute($sql, [$userId, $title, $streamKey, $status]);
    
    if ($result) {
        return [
            'stream_id' => $db->Insert_ID(),
            'stream_key' => $streamKey
        ];
    }
    
    return false;
}

// Mock file upload for testing
function createMockUploadedFile($filename, $content, $mimeType = 'text/plain')
{
    $tempFile = __DIR__ . '/temp/' . $filename;
    file_put_contents($tempFile, $content);
    
    return [
        'name' => $filename,
        'type' => $mimeType,
        'tmp_name' => $tempFile,
        'error' => UPLOAD_ERR_OK,
        'size' => strlen($content)
    ];
}

// Initialize error handler for testing
$errorHandler = VErrorHandler::getInstance();

// Clean up any existing test data
register_shutdown_function('cleanupTestData');

echo "EasyStream Test Environment Initialized\n";