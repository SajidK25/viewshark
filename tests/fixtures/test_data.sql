-- Test data for EasyStream testing environment

-- Insert test users
INSERT INTO `db_users` (`username`, `email`, `password_hash`, `role`, `status`, `email_verified`, `created_at`) VALUES
('testuser', 'test@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'active', 1, NOW()),
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', 1, NOW()),
('premium', 'premium@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'premium', 'active', 1, NOW()),
('suspended', 'suspended@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'member', 'suspended', 1, NOW());

-- Insert test video files
INSERT INTO `db_videofiles` (`user_id`, `title`, `description`, `filename`, `file_size`, `duration`, `resolution`, `status`, `privacy`, `view_count`, `like_count`, `created_at`) VALUES
(1, 'Test Video 1', 'This is a test video for unit testing', 'test_video_1.mp4', 1048576, 120, '1920x1080', 'ready', 'public', 100, 10, NOW()),
(1, 'Test Video 2', 'Another test video', 'test_video_2.mp4', 2097152, 180, '1280x720', 'ready', 'unlisted', 50, 5, NOW()),
(2, 'Admin Video', 'Video uploaded by admin', 'admin_video.mp4', 3145728, 240, '1920x1080', 'ready', 'public', 200, 20, NOW()),
(3, 'Premium Video', 'Premium content', 'premium_video.mp4', 4194304, 300, '3840x2160', 'ready', 'members_only', 75, 15, NOW());

-- Insert test live streams
INSERT INTO `db_livefiles` (`user_id`, `title`, `description`, `stream_key`, `status`, `privacy`, `viewer_count`, `created_at`) VALUES
(1, 'Test Live Stream', 'Testing live streaming functionality', 'test_stream_key_123', 'scheduled', 'public', 0, NOW()),
(2, 'Admin Stream', 'Admin live stream', 'admin_stream_key_456', 'live', 'public', 25, NOW());

-- Insert test comments
INSERT INTO `db_comments` (`content_type`, `content_id`, `user_id`, `comment_text`, `like_count`, `status`, `created_at`) VALUES
('video', 1, 2, 'Great video! Thanks for sharing.', 3, 'active', NOW()),
('video', 1, 3, 'Very informative content.', 1, 'active', NOW()),
('video', 2, 1, 'Nice work on this one.', 2, 'active', NOW());

-- Insert test interactions
INSERT INTO `db_interactions` (`user_id`, `content_type`, `content_id`, `interaction_type`, `created_at`) VALUES
(1, 'video', 3, 'like', NOW()),
(1, 'video', 3, 'view', NOW()),
(2, 'video', 1, 'like', NOW()),
(2, 'video', 1, 'view', NOW()),
(3, 'video', 1, 'like', NOW()),
(3, 'video', 2, 'view', NOW());

-- Insert test settings
INSERT INTO `db_settings` (`cfg_name`, `cfg_value`) VALUES
('site_name', 'EasyStream Test'),
('admin_email', 'admin@test.com'),
('debug_mode', '1'),
('logging_database_logging', '1'),
('error_alerts', '0'),
('max_upload_size', '104857600'),
('allowed_video_formats', 'mp4,avi,mov,wmv'),
('video_processing_enabled', '1'),
('live_streaming_enabled', '1'),
('user_registration_enabled', '1');

-- Insert test categories
INSERT INTO `db_categories` (`category_name`, `category_description`, `created_at`) VALUES
('Technology', 'Technology related content', NOW()),
('Entertainment', 'Entertainment and fun content', NOW()),
('Education', 'Educational content', NOW()),
('Gaming', 'Gaming related videos', NOW());

-- Insert test logs (for testing log viewer)
INSERT INTO `db_logs` (`level`, `message`, `context`, `request_id`, `user_id`, `ip`, `user_agent`, `request_uri`, `created_at`) VALUES
('info', 'Test info log message', '{"test": true}', 'req_test_001', 1, '127.0.0.1', 'PHPUnit Test', '/test', NOW()),
('warning', 'Test warning log message', '{"warning": "test"}', 'req_test_002', 2, '127.0.0.1', 'PHPUnit Test', '/test', NOW()),
('error', 'Test error log message', '{"error": "test"}', 'req_test_003', NULL, '127.0.0.1', 'PHPUnit Test', '/test', NOW());