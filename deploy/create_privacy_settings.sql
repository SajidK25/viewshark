-- EasyStream Privacy and Access Control Settings
-- This extends the branding system with comprehensive privacy controls

-- Add privacy settings to branding settings
INSERT INTO `db_branding_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`, `default_value`) VALUES
-- Site Access Control
('site_access_mode', 'public', 'text', 'privacy', 'Site access mode: public, members_only, invite_only, maintenance', 'public'),
('require_login_to_view', '0', 'boolean', 'privacy', 'Require users to be logged in to view any content', '0'),
('allow_guest_browsing', '1', 'boolean', 'privacy', 'Allow guests to browse public content without registration', '1'),
('show_content_previews', '1', 'boolean', 'privacy', 'Show content previews to non-logged-in users', '1'),

-- Content Privacy
('default_video_privacy', 'public', 'text', 'privacy', 'Default privacy for new videos: public, members_only, private', 'public'),
('default_stream_privacy', 'public', 'text', 'privacy', 'Default privacy for new streams: public, members_only, private', 'public'),
('allow_private_content', '1', 'boolean', 'privacy', 'Allow users to create private content', '1'),
('allow_unlisted_content', '1', 'boolean', 'privacy', 'Allow users to create unlisted content', '1'),

-- User Privacy
('show_user_profiles', 'members_only', 'text', 'privacy', 'Who can view user profiles: public, members_only, private', 'members_only'),
('show_user_activity', 'members_only', 'text', 'privacy', 'Who can view user activity: public, members_only, private', 'members_only'),
('show_user_statistics', 'members_only', 'text', 'privacy', 'Who can view user statistics: public, members_only, private', 'members_only'),
('allow_user_following', '1', 'boolean', 'privacy', 'Allow users to follow other users', '1'),

-- Search and Discovery
('search_requires_login', '0', 'boolean', 'privacy', 'Require login to use search functionality', '0'),
('trending_requires_login', '0', 'boolean', 'privacy', 'Require login to view trending content', '0'),
('categories_require_login', '0', 'boolean', 'privacy', 'Require login to browse categories', '0'),
('recommendations_require_login', '1', 'boolean', 'privacy', 'Require login to view personalized recommendations', '1'),

-- API Access
('api_public_access', '1', 'boolean', 'privacy', 'Allow public API access for basic content', '1'),
('api_require_key', '0', 'boolean', 'privacy', 'Require API key for all API requests', '0'),
('api_rate_limit_public', '100', 'number', 'privacy', 'Rate limit for public API requests per hour', '100'),
('api_rate_limit_members', '1000', 'number', 'privacy', 'Rate limit for member API requests per hour', '1000'),

-- Registration and Invites
('allow_public_registration', '1', 'boolean', 'privacy', 'Allow anyone to register for an account', '1'),
('require_email_verification', '1', 'boolean', 'privacy', 'Require email verification for new accounts', '1'),
('require_admin_approval', '0', 'boolean', 'privacy', 'Require admin approval for new accounts', '0'),
('enable_invite_system', '0', 'boolean', 'privacy', 'Enable invite-only registration system', '0'),
('invite_codes_required', '0', 'boolean', 'privacy', 'Require invite codes for registration', '0'),

-- Content Moderation
('auto_approve_content', '1', 'boolean', 'privacy', 'Automatically approve new content uploads', '1'),
('require_content_moderation', '0', 'boolean', 'privacy', 'Require manual approval for all content', '0'),
('enable_content_reporting', '1', 'boolean', 'privacy', 'Allow users to report inappropriate content', '1'),
('enable_comment_moderation', '0', 'boolean', 'privacy', 'Enable comment moderation', '0'),

-- Age Restrictions
('enable_age_verification', '0', 'boolean', 'privacy', 'Enable age verification system', '0'),
('minimum_age_requirement', '13', 'number', 'privacy', 'Minimum age requirement for registration', '13'),
('require_parental_consent', '0', 'boolean', 'privacy', 'Require parental consent for users under 18', '0'),
('show_age_restricted_content', 'members_only', 'text', 'privacy', 'Who can view age-restricted content: public, members_only, verified_only', 'members_only'),

-- Geographic Restrictions
('enable_geo_blocking', '0', 'boolean', 'privacy', 'Enable geographic content blocking', '0'),
('blocked_countries', '', 'text', 'privacy', 'Comma-separated list of blocked country codes', ''),
('allowed_countries', '', 'text', 'privacy', 'Comma-separated list of allowed country codes (empty = all allowed)', ''),
('geo_block_message', 'This content is not available in your region.', 'text', 'privacy', 'Message shown to geo-blocked users', 'This content is not available in your region.'),

-- GDPR and Compliance
('enable_gdpr_compliance', '1', 'boolean', 'privacy', 'Enable GDPR compliance features', '1'),
('require_cookie_consent', '1', 'boolean', 'privacy', 'Require cookie consent banner', '1'),
('enable_data_export', '1', 'boolean', 'privacy', 'Allow users to export their data', '1'),
('enable_account_deletion', '1', 'boolean', 'privacy', 'Allow users to delete their accounts', '1'),
('data_retention_days', '365', 'number', 'privacy', 'Days to retain user data after account deletion', '365'),

-- Maintenance Mode
('maintenance_mode', '0', 'boolean', 'privacy', 'Enable maintenance mode', '0'),
('maintenance_message', 'Site is currently under maintenance. Please check back later.', 'text', 'privacy', 'Message shown during maintenance', 'Site is currently under maintenance. Please check back later.'),
('maintenance_allowed_ips', '', 'text', 'privacy', 'Comma-separated list of IPs allowed during maintenance', ''),
('maintenance_bypass_key', '', 'text', 'privacy', 'URL parameter to bypass maintenance mode', ''),

-- Social Features
('enable_social_login', '1', 'boolean', 'privacy', 'Enable social media login options', '1'),
('enable_social_sharing', '1', 'boolean', 'privacy', 'Enable social media sharing buttons', '1'),
('social_sharing_requires_login', '0', 'boolean', 'privacy', 'Require login to share content', '0'),
('enable_social_comments', '0', 'boolean', 'privacy', 'Enable social media comment integration', '0'),

-- Analytics and Tracking
('enable_analytics_tracking', '1', 'boolean', 'privacy', 'Enable analytics tracking', '1'),
('analytics_respect_dnt', '1', 'boolean', 'privacy', 'Respect Do Not Track browser settings', '1'),
('enable_user_tracking', '1', 'boolean', 'privacy', 'Enable user behavior tracking', '1'),
('tracking_requires_consent', '1', 'boolean', 'privacy', 'Require user consent for tracking', '1'),

-- Custom Messages
('login_required_message', 'Please log in to view this content.', 'text', 'privacy', 'Message shown when login is required', 'Please log in to view this content.'),
('members_only_message', 'This content is available to members only.', 'text', 'privacy', 'Message shown for members-only content', 'This content is available to members only.'),
('private_content_message', 'This content is private.', 'text', 'privacy', 'Message shown for private content', 'This content is private.'),
('age_restricted_message', 'This content is age-restricted.', 'text', 'privacy', 'Message shown for age-restricted content', 'This content is age-restricted.');

-- Create privacy rules table for advanced access control
CREATE TABLE IF NOT EXISTS `db_privacy_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rule_name` varchar(100) NOT NULL,
  `rule_type` enum('content','user','feature','api') DEFAULT 'content',
  `target_type` varchar(50) NOT NULL,
  `target_id` varchar(100) DEFAULT NULL,
  `access_level` enum('public','members_only','verified_only','premium_only','admin_only','private') DEFAULT 'public',
  `conditions` text,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rule_type` (`rule_type`),
  KEY `target_type` (`target_type`),
  KEY `access_level` (`access_level`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default privacy rules
INSERT INTO `db_privacy_rules` (`rule_name`, `rule_type`, `target_type`, `target_id`, `access_level`, `conditions`) VALUES
('Default Video Access', 'content', 'video', '*', 'public', '{"allow_preview": true, "show_metadata": true}'),
('Default Stream Access', 'content', 'stream', '*', 'public', '{"allow_preview": true, "show_metadata": true}'),
('User Profile Access', 'user', 'profile', '*', 'members_only', '{"show_activity": false, "show_statistics": false}'),
('Admin Panel Access', 'feature', 'admin', '*', 'admin_only', '{"require_2fa": false}'),
('API Access', 'api', 'public', '*', 'public', '{"rate_limit": 100, "require_key": false}'),
('Search Feature', 'feature', 'search', '*', 'public', '{"show_suggestions": true, "log_queries": true}'),
('Upload Feature', 'feature', 'upload', '*', 'members_only', '{"max_file_size": "100MB", "allowed_formats": "mp4,mov,avi"}'),
('Comment Feature', 'feature', 'comments', '*', 'members_only', '{"require_moderation": false, "allow_replies": true}');

-- Create user privacy preferences table
CREATE TABLE IF NOT EXISTS `db_user_privacy_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) NOT NULL,
  `preference_key` varchar(100) NOT NULL,
  `preference_value` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_preference` (`usr_id`, `preference_key`),
  KEY `usr_id` (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create content privacy settings table
CREATE TABLE IF NOT EXISTS `db_content_privacy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` enum('video','stream','image','audio','document','blog') NOT NULL,
  `content_id` int(11) NOT NULL,
  `privacy_level` enum('public','members_only','followers_only','private','unlisted') DEFAULT 'public',
  `password_protected` tinyint(1) DEFAULT 0,
  `content_password` varchar(255) DEFAULT NULL,
  `age_restricted` tinyint(1) DEFAULT 0,
  `geo_restricted` tinyint(1) DEFAULT 0,
  `allowed_countries` text,
  `blocked_countries` text,
  `scheduled_public` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `custom_message` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `content_privacy` (`content_type`, `content_id`),
  KEY `privacy_level` (`privacy_level`),
  KEY `age_restricted` (`age_restricted`),
  KEY `geo_restricted` (`geo_restricted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create access logs table for privacy compliance
CREATE TABLE IF NOT EXISTS `db_privacy_access_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `access_type` enum('view','download','share','report') NOT NULL,
  `content_type` varchar(50) DEFAULT NULL,
  `content_id` int(11) DEFAULT NULL,
  `access_granted` tinyint(1) DEFAULT 1,
  `denial_reason` varchar(255) DEFAULT NULL,
  `privacy_level_required` varchar(50) DEFAULT NULL,
  `user_privacy_level` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usr_id` (`usr_id`),
  KEY `ip_address` (`ip_address`),
  KEY `access_type` (`access_type`),
  KEY `content_type` (`content_type`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;