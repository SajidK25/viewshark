<?php
define('_ISVALID', true);
include_once '../f_core/config.core.php';

// Load API configuration
$api_config = require_once __DIR__ . '/config.php';

// Test results array
$tests = [];

// Test 1: Check if config file exists and is readable
$tests['config_file'] = [
    'name' => 'Configuration File',
    'status' => file_exists(__DIR__ . '/config.php') ? 'OK' : 'FAIL',
    'message' => file_exists(__DIR__ . '/config.php') ? 'Config file exists' : 'Config file not found'
];

// Test 2: Verify bot token
$tests['bot_token'] = [
    'name' => 'Bot Token',
    'status' => (!empty($api_config['telegram']['bot_token']) && $api_config['telegram']['bot_token'] !== '123456789:ABCdefGHIjklmNOPQRstuvwxyz') ? 'OK' : 'FAIL',
    'message' => (!empty($api_config['telegram']['bot_token']) && $api_config['telegram']['bot_token'] !== '123456789:ABCdefGHIjklmNOPQRstuvwxyz') ? 'Bot token is set' : 'Please set your bot token in config.php'
];

// Test 3: Verify channel ID
$tests['channel_id'] = [
    'name' => 'Channel ID',
    'status' => (!empty($api_config['telegram']['channel_id']) && $api_config['telegram']['channel_id'] !== 'YOUR_CHANNEL_ID') ? 'OK' : 'FAIL',
    'message' => (!empty($api_config['telegram']['channel_id']) && $api_config['telegram']['channel_id'] !== 'YOUR_CHANNEL_ID') ? 'Channel ID is set' : 'Please set your channel ID in config.php'
];

// Test 4: Test Telegram API connection and channel access
function testTelegramAPI($bot_token, $channel_id) {
    // First test bot token
    $url = "https://api.telegram.org/bot{$bot_token}/getMe";
    $result = file_get_contents($url);
    if ($result === false) {
        return ['status' => 'FAIL', 'message' => 'Could not connect to Telegram API'];
    }
    
    $response = json_decode($result, true);
    if (!$response['ok']) {
        return ['status' => 'FAIL', 'message' => 'Invalid bot token'];
    }
    
    // Then test channel access
    $url = "https://api.telegram.org/bot{$bot_token}/getChat?chat_id={$channel_id}";
    $result = file_get_contents($url);
    if ($result === false) {
        return ['status' => 'FAIL', 'message' => 'Could not access channel'];
    }
    
    $response = json_decode($result, true);
    return [
        'status' => $response['ok'] ? 'OK' : 'FAIL',
        'message' => $response['ok'] ? 'Successfully connected to Telegram API and channel' : 'Failed to access channel. Make sure bot is an admin.'
    ];
}

$telegram_test = testTelegramAPI($api_config['telegram']['bot_token'], $api_config['telegram']['channel_id']);
$tests['telegram_api'] = [
    'name' => 'Telegram API & Channel Access',
    'status' => $telegram_test['status'],
    'message' => $telegram_test['message']
];

// Test 5: Check database connection
$tests['database'] = [
    'name' => 'Database Connection',
    'status' => isset($class_database) ? 'OK' : 'FAIL',
    'message' => isset($class_database) ? 'Database connection is available' : 'Database connection failed'
];

// Test 6: Check file permissions
$tests['permissions'] = [
    'name' => 'File Permissions',
    'status' => is_writable(__DIR__) ? 'OK' : 'FAIL',
    'message' => is_writable(__DIR__) ? 'Directory is writable' : 'Directory is not writable'
];

// Output test results
echo "<h2>EasyStream Telegram Channel Setup Test</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Test</th><th>Status</th><th>Message</th></tr>";
foreach ($tests as $test) {
    $color = $test['status'] === 'OK' ? 'green' : 'red';
    echo "<tr>";
    echo "<td>{$test['name']}</td>";
    echo "<td style='color: {$color}'>{$test['status']}</td>";
    echo "<td>{$test['message']}</td>";
    echo "</tr>";
}
echo "</table>";

// Additional instructions
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Make sure all tests pass (show as OK)</li>";
echo "<li>If any test fails, follow the message instructions to fix it</li>";
echo "<li>Once all tests pass, set up the cron job to run auto_post.php every 5 minutes</li>";
echo "<li>Monitor the auto_post.log file for any errors</li>";
echo "</ol>"; 
