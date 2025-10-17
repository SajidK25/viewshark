<?php
define('_ISVALID', true);
include_once '../f_core/config.core.php';

// Set up logging
$log_file = __DIR__ . '/auto_post.log';
function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

// Load API configuration
$api_config = require_once __DIR__ . '/config.php';
$telegram_bot_token = $api_config['telegram']['bot_token'];
$telegram_channel_id = $api_config['telegram']['channel_id'];

// Verify configuration
if (empty($telegram_bot_token) || $telegram_bot_token === '123456789:ABCdefGHIjklmNOPQRstuvwxyz') {
    writeLog("ERROR: Invalid bot token. Please update config.php with your actual bot token.");
    die("Invalid bot token. Please check the logs.");
}

if (empty($telegram_channel_id) || $telegram_channel_id === 'YOUR_CHANNEL_ID') {
    writeLog("ERROR: Invalid channel ID. Please update config.php with your actual channel ID.");
    die("Invalid channel ID. Please check the logs.");
}

// Function to send data to Telegram channel
function sendToChannel($message, $parse_mode = 'HTML') {
    global $telegram_bot_token, $telegram_channel_id;
    try {
        $url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";
        $data = [
            'chat_id' => $telegram_channel_id,
            'text' => $message,
            'parse_mode' => $parse_mode
        ];
        
        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        
        if ($result === false) {
            writeLog("ERROR: Failed to send message to channel");
            return false;
        }
        
        $response = json_decode($result, true);
        if (!$response['ok']) {
            writeLog("ERROR: Telegram API error: " . ($response['description'] ?? 'Unknown error'));
            return false;
        }
        
        writeLog("SUCCESS: Message sent to channel");
        return $result;
    } catch (Exception $e) {
        writeLog("ERROR: Telegram API Exception: " . $e->getMessage());
        return false;
    }
}

// Function to format video/stream message
function formatContentMessage($content) {
    $message = "🎥 <b>{$content['title']}</b>\n\n";
    $message .= "📝 {$content['description']}\n\n";
    $message .= "👤 Posted by: {$content['username']}\n";
    $message .= "👁 Views: {$content['views']}\n";
    $message .= "🔗 <a href='{$content['url']}'>Watch on EasyStream</a>\n";
    
    // Add hashtags if available
    if (!empty($content['tags'])) {
        $message .= "\n🏷 Tags: " . implode(' ', array_map(function($tag) {
            return "#" . str_replace(' ', '', $tag);
        }, $content['tags']));
    }
    
    // Branding normalization - already using EasyStream above
    return $message;
}

// Function to check and post new content
function checkAndPostNewContent() {
    global $class_database, $cfg, $api_config;
    
    writeLog("Starting content check...");
    
    // Get latest videos (last 5 minutes)
    try {
        $videos = $class_database->getLatestVideos(
            $api_config['content']['max_items'],
            $api_config['content']['time_window']
        );
        
        writeLog("Found " . count($videos) . " new videos");
        
        foreach ($videos as $video) {
            $content = [
                'title' => htmlspecialchars($video['title']),
                'description' => htmlspecialchars($video['description']),
                'username' => htmlspecialchars($video['username']),
                'views' => $video['views'],
                'url' => $cfg['main_url'] . '/video/' . $video['file_key'],
                'tags' => explode(',', $video['tags'])
            ];
            
            $message = formatContentMessage($content);
            sendToChannel($message);
            
            // Add a small delay between posts to avoid rate limiting
            sleep(1);
        }
    } catch (Exception $e) {
        writeLog("ERROR: Failed to process videos: " . $e->getMessage());
    }
    
    // Get latest streams (last 5 minutes)
    try {
        $streams = $class_database->getLatestStreams(
            $api_config['content']['max_items'],
            $api_config['content']['time_window']
        );
        
        writeLog("Found " . count($streams) . " new streams");
        
        foreach ($streams as $stream) {
            $content = [
                'title' => htmlspecialchars($stream['title']),
                'description' => htmlspecialchars($stream['description']),
                'username' => htmlspecialchars($stream['username']),
                'views' => $stream['views'],
                'url' => $cfg['main_url'] . '/stream/' . $stream['stream_key'],
                'tags' => explode(',', $stream['tags'])
            ];
            
            $message = formatContentMessage($content);
            $message = "🔴 LIVE NOW: " . $message; // Add LIVE indicator for streams
            sendToChannel($message);
            
            // Add a small delay between posts to avoid rate limiting
            sleep(1);
        }
    } catch (Exception $e) {
        writeLog("ERROR: Failed to process streams: " . $e->getMessage());
    }
    
    writeLog("Content check completed");
}

// Run the check
writeLog("Script started");
checkAndPostNewContent();
writeLog("Script finished"); 
