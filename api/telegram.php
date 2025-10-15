<?php
define('_ISVALID', true);
include_once '../f_core/config.core.php';

// Load API configuration
$api_config = require_once __DIR__ . '/config.php';
$telegram_bot_token = $api_config['telegram']['bot_token'];

// Function to send data to Telegram
function sendToTelegram($chat_id, $message) {
    global $telegram_bot_token;
    try {
        $url = "https://api.telegram.org/bot{$telegram_bot_token}/sendMessage";
        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'HTML'
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
            error_log("Failed to send Telegram message to chat_id: {$chat_id}");
            return false;
        }
        
        return $result;
    } catch (Exception $e) {
        error_log("Telegram API Error: " . $e->getMessage());
        return false;
    }
}

// Handle incoming webhook
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $update = json_decode(file_get_contents('php://input'), true);
    
    // Process the update
    if (isset($update['message'])) {
        $message = $update['message'];
        $chat_id = $message['chat']['id'];
        $text = $message['text'] ?? '';
        
        // Handle commands
        if (strpos($text, '/') === 0) {
            switch ($text) {
                case '/start':
                    sendToTelegram($chat_id, "Welcome to ViewShark Bot! Use /videos to get latest videos.");
                    break;
                    
                case '/videos':
                    // Get latest videos from ViewShark
                    $videos = $class_database->getLatestVideos(5); // Adjust limit as needed
                    $response = "Latest Videos:\n\n";
                    foreach ($videos as $video) {
                        $response .= "📹 {$video['title']}\n";
                        $response .= "👤 {$video['username']}\n";
                        $response .= "👁 {$video['views']} views\n";
                        $response .= "🔗 {$cfg['main_url']}/video/{$video['file_key']}\n\n";
                    }
                    sendToTelegram($chat_id, $response);
                    break;
                    
                case '/search':
                    $query = trim(substr($text, 7));
                    if (empty($query)) {
                        sendToTelegram($chat_id, "Please provide a search query: /search <query>");
                        break;
                    }
                    
                    $results = $class_database->searchVideos($query, 5);
                    if (empty($results)) {
                        sendToTelegram($chat_id, "No videos found for: {$query}");
                        break;
                    }
                    
                    $response = "Search Results for: {$query}\n\n";
                    foreach ($results as $video) {
                        $response .= "📹 {$video['title']}\n";
                        $response .= "👤 {$video['username']}\n";
                        $response .= "👁 {$video['views']} views\n";
                        $response .= "🔗 {$cfg['main_url']}/video/{$video['file_key']}\n\n";
                    }
                    sendToTelegram($chat_id, $response);
                    break;
                    
                default:
                    sendToTelegram($chat_id, "Unknown command. Available commands:\n/start - Start the bot\n/videos - Get latest videos\n/search <query> - Search for videos");
            }
        }
    }
} 