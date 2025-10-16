<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : Database Configuration Fix Script
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

// Simple script to fix missing database configurations
echo "EasyStream Database Configuration Fix\n";
echo "=====================================\n\n";

// Include database configuration
$main_dir = realpath(dirname(__FILE__).'/../');
require_once $main_dir . '/f_core/config.database.php';

// Connect to database
try {
    $pdo = new PDO("mysql:host=$cfg_dbhost;dbname=$cfg_dbname", $cfg_dbuser, $cfg_dbpass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n";
} catch(PDOException $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Array of configurations to add
$configs = [
    ['new_layout', '1', 'backend: enable/disable new layout menu'],
    ['short_module', '1', 'backend: enable/disable video shorts'],
    ['short_uploads', '1', 'backend: enable/disable video shorts uploads'],
    ['channel_memberships', '0', 'backend: enable/disable channel memberships'],
    ['member_chat_only', '0', 'backend: enable/disable member-only chat'],
    ['member_badges', '0', 'backend: enable/disable member badges']
];

echo "\nAdding missing configurations...\n";

foreach ($configs as $config) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_data`, `cfg_info`) VALUES (?, ?, ?)");
        $result = $stmt->execute($config);
        
        // Check if the row was actually inserted
        $check = $pdo->prepare("SELECT COUNT(*) FROM `db_settings` WHERE `cfg_name` = ?");
        $check->execute([$config[0]]);
        $exists = $check->fetchColumn();
        
        if ($exists > 0) {
            echo "✓ Configuration '{$config[0]}' is now available\n";
        } else {
            echo "✗ Failed to add configuration '{$config[0]}'\n";
        }
    } catch(PDOException $e) {
        echo "✗ Error adding '{$config[0]}': " . $e->getMessage() . "\n";
    }
}

echo "\n✓ Database configuration fix completed!\n";
echo "\nYou can now access the control panel.\n";
?>