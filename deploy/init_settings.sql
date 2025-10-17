-- Initial settings for EasyStream
-- This file is run during database initialization

-- Create settings table if it doesn't exist (backup)
CREATE TABLE IF NOT EXISTS `db_settings` (
  `cfg_name` varchar(100) NOT NULL,
  `cfg_value` text,
  PRIMARY KEY (`cfg_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default configuration values
INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_value`) VALUES
('site_name', 'EasyStream'),
('site_description', 'Professional Video Streaming Platform'),
('site_keywords', 'video, streaming, live, broadcast'),
('admin_email', 'admin@easystream.local'),
('timezone', 'UTC'),
('date_format', 'Y-m-d H:i:s'),
('max_upload_size', '256'),
('allowed_extensions', 'mp4,avi,mov,wmv,flv,webm,mkv'),
('video_quality', 'auto'),
('enable_registration', '1'),
('enable_comments', '1'),
('enable_ratings', '1'),
('enable_notifications', '1'),
('redis_enabled', '1'),
('redis_host', 'redis'),
('redis_port', '6379'),
('redis_db', '0'),
('queue_enabled', '1'),
('logging_enabled', '1'),
('security_csrf_enabled', '1'),
('security_rate_limit', '100'),
('security_ban_duration', '3600'),
('backend_username', 'admin'),
('backend_password', 'admin123'),
('backend_email', 'admin@easystream.local'),
('backend_signin_section', '1'),
('backend_remember', '1'),
('main_url', 'http://localhost:8083');

-- Create default admin user in users table
INSERT IGNORE INTO `db_users` (`usr_id`, `usr_user`, `usr_email`, `usr_password`, `usr_fname`, `usr_lname`, `usr_status`, `usr_signup_date`) VALUES
(1, 'admin', 'admin@easystream.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'active', NOW());