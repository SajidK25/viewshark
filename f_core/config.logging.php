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

defined('_ISVALID') or header('Location: /error');

// Logging Configuration
$logging_config = [
    // Enable/disable different types of logging
    'file_logging' => true,
    'database_logging' => false, // Set to true if you want to log to database
    'error_alerts' => true,      // Send email alerts for critical errors
    
    // Log levels to record (set to false to disable specific levels)
    'log_levels' => [
        'emergency' => true,
        'alert' => true,
        'critical' => true,
        'error' => true,
        'warning' => true,
        'notice' => true,
        'info' => true,
        'debug' => false  // Disable debug logging in production
    ],
    
    // File rotation settings
    'max_file_size' => 10 * 1024 * 1024, // 10MB
    'max_files' => 5,
    
    // Alert settings
    'admin_email' => 'admin@yourdomain.com', // Change this to your admin email
    'alert_rate_limit' => [
        'max_alerts' => 5,
        'time_window' => 3600 // 1 hour
    ],
    
    // Performance monitoring
    'performance_thresholds' => [
        'slow_query' => 1.0,      // Log queries taking more than 1 second
        'slow_request' => 2.0,    // Log requests taking more than 2 seconds
        'memory_limit' => 128 * 1024 * 1024, // Log if memory usage exceeds 128MB
    ],
    
    // Security logging
    'security_events' => [
        'failed_logins' => true,
        'suspicious_uploads' => true,
        'rate_limit_exceeded' => true,
        'csrf_failures' => true,
        'sql_injection_attempts' => true,
        'xss_attempts' => true
    ],
    
    // Log retention (days)
    'retention_days' => 30,
    
    // Sensitive data filtering (fields to exclude from logs)
    'sensitive_fields' => [
        'password',
        'passwd',
        'pwd',
        'secret',
        'token',
        'key',
        'credit_card',
        'ssn',
        'social_security'
    ]
];

// Apply configuration to global config
foreach ($logging_config as $key => $value) {
    $cfg['logging_' . $key] = $value;
}

// Set up log cleanup cron job (if needed)
if (isset($cfg['logging_retention_days']) && $cfg['logging_retention_days'] > 0) {
    // This would typically be handled by a cron job
    // For now, we'll do a simple cleanup check occasionally
    if (rand(1, 100) === 1) { // 1% chance on each request
        $logDir = 'f_data/logs/';
        $cutoffTime = time() - ($cfg['logging_retention_days'] * 24 * 60 * 60);
        
        $files = glob($logDir . '*.log*');
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
            }
        }
    }
}