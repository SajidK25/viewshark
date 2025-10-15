<?php
// API Configuration
return [
    'telegram' => [
        'bot_token' => '123456789:ABCdefGHIjklmNOPQRstuvwxyz', // Replace with your actual bot token from BotFather
        'channel_id' => 'YOUR_CHANNEL_ID', // Replace with your channel ID (e.g., -100xxxxxxxxxx)
        'allowed_ips' => [
            // Add Telegram's IP ranges here for security
            '149.154.160.0/20',
            '91.108.4.0/22'
        ]
    ],
    'security' => [
        'rate_limit' => 30, // requests per minute
        'max_requests' => 1000 // per hour
    ],
    'content' => [
        'check_interval' => 5, // minutes between checks
        'max_items' => 10,     // maximum items to post per check
        'time_window' => 5     // minutes to look back for new content
    ]
]; 