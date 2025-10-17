-- Complete EasyStream Database Schema
-- Fresh installation with all required tables

-- Core users table
CREATE TABLE IF NOT EXISTS `db_users` (
  `usr_id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_user` varchar(50) NOT NULL,
  `usr_email` varchar(100) NOT NULL,
  `usr_password` varchar(255) NOT NULL,
  `usr_fname` varchar(50) DEFAULT NULL,
  `usr_lname` varchar(50) DEFAULT NULL,
  `usr_status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `usr_signup_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usr_last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`usr_id`),
  UNIQUE KEY `idx_username` (`usr_user`),
  UNIQUE KEY `idx_email` (`usr_email`),
  KEY `idx_status` (`usr_status`),
  KEY `idx_signup_date` (`usr_signup_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Core video files table
CREATE TABLE IF NOT EXISTS `db_videofiles` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_title` varchar(255) NOT NULL,
  `file_description` text,
  `file_type` varchar(10) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `file_duration` int(11) DEFAULT NULL,
  `file_views` int(11) NOT NULL DEFAULT 0,
  `upload_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processing_status` enum('pending','processing','completed','failed') DEFAULT 'pending',
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`file_id`),
  KEY `idx_user_id` (`usr_id`),
  KEY `idx_upload_date` (`upload_date`),
  KEY `idx_processing_status` (`processing_status`),
  KEY `idx_file_views` (`file_views`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settings table
CREATE TABLE IF NOT EXISTS `db_settings` (
  `cfg_name` varchar(100) NOT NULL,
  `cfg_value` text,
  PRIMARY KEY (`cfg_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sessions table
CREATE TABLE IF NOT EXISTS `db_sessions` (
  `session_id` varchar(128) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_data` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- IP Tracking table (for VIPTracker class)
CREATE TABLE IF NOT EXISTS `db_ip_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `context` text,
  `user_agent` text,
  `referer` varchar(500) DEFAULT NULL,
  `request_uri` varchar(500) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `timestamp` datetime NOT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_ip_timestamp` (`ip_address`, `timestamp`),
  KEY `idx_user_timestamp` (`user_id`, `timestamp`),
  KEY `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enhanced ban list table
CREATE TABLE IF NOT EXISTS `db_banlist` (
  `ban_id` int(11) NOT NULL AUTO_INCREMENT,
  `ban_ip` varchar(45) NOT NULL,
  `ban_reason` varchar(500) NOT NULL,
  `ban_active` tinyint(1) NOT NULL DEFAULT 1,
  `ban_date` datetime NOT NULL,
  `ban_expires` datetime DEFAULT NULL,
  `unban_date` datetime DEFAULT NULL,
  `banned_by` varchar(100) NOT NULL,
  PRIMARY KEY (`ban_id`),
  UNIQUE KEY `idx_ip` (`ban_ip`),
  KEY `idx_active_expires` (`ban_active`, `ban_expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fingerprint tracking table
CREATE TABLE IF NOT EXISTS `db_fingerprints` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fingerprint_hash` varchar(64) NOT NULL,
  `first_seen` datetime NOT NULL,
  `last_seen` datetime NOT NULL,
  `visit_count` int(11) NOT NULL DEFAULT 1,
  `first_ip` varchar(45) DEFAULT NULL,
  `last_ip` varchar(45) DEFAULT NULL,
  `first_user_id` int(11) DEFAULT NULL,
  `last_user_id` int(11) DEFAULT NULL,
  `user_agent` text,
  `context` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_fingerprint` (`fingerprint_hash`),
  KEY `idx_last_seen` (`last_seen`),
  KEY `idx_user_id` (`last_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Fingerprint bans table
CREATE TABLE IF NOT EXISTS `db_fingerprint_bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fingerprint_hash` varchar(64) NOT NULL,
  `ban_reason` varchar(500) NOT NULL,
  `ban_active` tinyint(1) NOT NULL DEFAULT 1,
  `ban_date` datetime NOT NULL,
  `ban_expires` datetime DEFAULT NULL,
  `unban_date` datetime DEFAULT NULL,
  `banned_by` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_fingerprint` (`fingerprint_hash`),
  KEY `idx_active_expires` (`ban_active`, `ban_expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Email log table (for SendEmailJob)
CREATE TABLE IF NOT EXISTS `db_email_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipient` varchar(255) NOT NULL,
  `subject` varchar(500) NOT NULL,
  `status` enum('sent','failed') NOT NULL,
  `error_message` text,
  `sent_at` datetime NOT NULL,
  `job_class` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_recipient` (`recipient`),
  KEY `idx_status` (`status`),
  KEY `idx_sent_at` (`sent_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications table
CREATE TABLE IF NOT EXISTS `db_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `metadata` text,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_type` (`type`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password resets table
CREATE TABLE IF NOT EXISTS `db_password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) NOT NULL,
  `reset_token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_token` (`reset_token`),
  KEY `idx_user_expires` (`usr_id`, `expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add processing status to video files if not exists
ALTER TABLE `db_videofiles` 
ADD COLUMN IF NOT EXISTS `processing_status` enum('pending','processing','completed','failed') DEFAULT 'pending' AFTER `file_views`,
ADD COLUMN IF NOT EXISTS `processed_at` datetime DEFAULT NULL AFTER `processing_status`;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_processing_status` ON `db_videofiles` (`processing_status`);
CREATE INDEX IF NOT EXISTS `idx_processed_at` ON `db_videofiles` (`processed_at`);