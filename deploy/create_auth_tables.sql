-- Enhanced Authentication Tables for EasyStream
-- This file creates/updates tables for the VAuth authentication system

-- Enhanced users table with authentication fields
CREATE TABLE IF NOT EXISTS `db_users` (
    `user_id` INT PRIMARY KEY AUTO_INCREMENT,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('guest', 'member', 'verified', 'premium', 'admin') DEFAULT 'member',
    `status` ENUM('active', 'suspended', 'banned', 'deleted') DEFAULT 'active',
    `email_verified` BOOLEAN DEFAULT FALSE,
    `verification_token` VARCHAR(64) NULL,
    `reset_token` VARCHAR(64) NULL,
    `reset_expires` TIMESTAMP NULL,
    `remember_token` VARCHAR(255) NULL,
    `remember_expires` TIMESTAMP NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_login` TIMESTAMP NULL,
    `profile_image` VARCHAR(255) NULL,
    `bio` TEXT NULL,
    `website` VARCHAR(255) NULL,
    `social_links` JSON NULL,
    `login_attempts` INT DEFAULT 0,
    `locked_until` TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_role (role),
    INDEX idx_verification_token (verification_token),
    INDEX idx_reset_token (reset_token),
    INDEX idx_remember_token (remember_token),
    INDEX idx_created_at (created_at),
    INDEX idx_last_login (last_login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enhanced sessions table with security features
CREATE TABLE IF NOT EXISTS `db_sessions` (
    `session_id` VARCHAR(128) PRIMARY KEY,
    `user_id` INT NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NOT NULL,
    `last_activity` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `remember_me` BOOLEAN DEFAULT FALSE,
    `is_mobile` BOOLEAN DEFAULT FALSE,
    `location` VARCHAR(255) NULL,
    `device_fingerprint` VARCHAR(64) NULL,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_ip_address (ip_address),
    INDEX idx_last_activity (last_activity),
    INDEX idx_remember_me (remember_me)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User login history for security monitoring
CREATE TABLE IF NOT EXISTS `db_login_history` (
    `history_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NULL,
    `username` VARCHAR(50) NULL,
    `email` VARCHAR(255) NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `user_agent` TEXT NULL,
    `login_result` ENUM('success', 'failed', 'blocked') NOT NULL,
    `failure_reason` VARCHAR(255) NULL,
    `location` VARCHAR(255) NULL,
    `device_info` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_login_result (login_result),
    INDEX idx_created_at (created_at),
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password history to prevent reuse
CREATE TABLE IF NOT EXISTS `db_password_history` (
    `history_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User preferences and settings
CREATE TABLE IF NOT EXISTS `db_user_preferences` (
    `preference_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `preference_key` VARCHAR(100) NOT NULL,
    `preference_value` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_preference (user_id, preference_key),
    INDEX idx_user_id (user_id),
    INDEX idx_preference_key (preference_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email verification attempts tracking
CREATE TABLE IF NOT EXISTS `db_email_verifications` (
    `verification_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `attempts` INT DEFAULT 0,
    `verified_at` TIMESTAMP NULL,
    `expires_at` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_token (token),
    INDEX idx_email (email),
    INDEX idx_expires_at (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Two-factor authentication (for future implementation)
CREATE TABLE IF NOT EXISTS `db_user_2fa` (
    `2fa_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `method` ENUM('totp', 'sms', 'email') NOT NULL,
    `secret` VARCHAR(255) NULL,
    `phone` VARCHAR(20) NULL,
    `backup_codes` JSON NULL,
    `enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_method (user_id, method),
    INDEX idx_user_id (user_id),
    INDEX idx_enabled (enabled)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add authentication-related settings
INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_value`, `cfg_description`) VALUES
('require_email_verification', '1', 'Require email verification for new accounts'),
('password_min_length', '8', 'Minimum password length'),
('password_require_special', '1', 'Require special characters in passwords'),
('session_timeout', '3600', 'Session timeout in seconds (1 hour)'),
('remember_me_timeout', '2592000', 'Remember me timeout in seconds (30 days)'),
('max_login_attempts', '5', 'Maximum login attempts before lockout'),
('lockout_duration', '900', 'Account lockout duration in seconds (15 minutes)'),
('password_reset_timeout', '3600', 'Password reset token timeout in seconds (1 hour)'),
('allow_registration', '1', 'Allow new user registration'),
('admin_approval_required', '0', 'Require admin approval for new accounts'),
('enable_remember_me', '1', 'Enable remember me functionality'),
('force_password_change', '0', 'Force password change on first login'),
('password_history_count', '5', 'Number of previous passwords to remember'),
('enable_2fa', '0', 'Enable two-factor authentication'),
('login_rate_limit', '10', 'Login attempts per IP per hour'),
('registration_rate_limit', '3', 'Registration attempts per IP per hour');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_email_verified ON db_users(email_verified);
CREATE INDEX IF NOT EXISTS idx_users_role_status ON db_users(role, status);
CREATE INDEX IF NOT EXISTS idx_sessions_user_expires ON db_sessions(user_id, expires_at);
CREATE INDEX IF NOT EXISTS idx_login_history_ip_result ON db_login_history(ip_address, login_result);

-- Clean up expired sessions (can be run periodically)
-- DELETE FROM db_sessions WHERE expires_at < NOW();

-- Clean up old login history (keep last 90 days)
-- DELETE FROM db_login_history WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Clean up old password history (keep last 10 per user)
-- DELETE ph1 FROM db_password_history ph1
-- INNER JOIN (
--     SELECT user_id, MIN(history_id) as min_id
--     FROM db_password_history
--     GROUP BY user_id
--     HAVING COUNT(*) > 10
-- ) ph2 ON ph1.user_id = ph2.user_id AND ph1.history_id = ph2.min_id;