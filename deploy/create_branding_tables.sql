-- EasyStream Branding and Customization Tables
-- This file creates the database structure for the comprehensive branding system

-- Branding settings table
CREATE TABLE IF NOT EXISTS `db_branding_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','color','image','number','boolean','json') DEFAULT 'text',
  `category` varchar(50) DEFAULT 'general',
  `description` text,
  `default_value` text,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default branding settings
INSERT INTO `db_branding_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`, `default_value`) VALUES
-- General Branding
('site_name', 'EasyStream', 'text', 'general', 'Main site name displayed throughout the platform', 'EasyStream'),
('site_tagline', 'Your Video Streaming Platform', 'text', 'general', 'Tagline or subtitle for the platform', 'Your Video Streaming Platform'),
('site_description', 'A powerful video streaming platform built for creators', 'text', 'general', 'Site description for SEO and about pages', 'A powerful video streaming platform built for creators'),
('footer_text', '© 2025 EasyStream. All rights reserved.', 'text', 'general', 'Footer copyright text', '© 2025 EasyStream. All rights reserved.'),

-- Logo and Images
('logo_main', '/f_scripts/fe/img/logo-header-blue.svg', 'image', 'logos', 'Main logo displayed in header', '/f_scripts/fe/img/logo-header-blue.svg'),
('logo_small', '/f_scripts/fe/img/logo-small.png', 'image', 'logos', 'Small logo for mobile and compact views', '/f_scripts/fe/img/logo-small.png'),
('favicon', '/favicon.ico', 'image', 'logos', 'Site favicon', '/favicon.ico'),
('default_avatar', '/f_scripts/fe/img/default-avatar.png', 'image', 'logos', 'Default user avatar image', '/f_scripts/fe/img/default-avatar.png'),
('default_thumbnail', '/f_scripts/fe/img/default-thumbnail.jpg', 'image', 'logos', 'Default video thumbnail', '/f_scripts/fe/img/default-thumbnail.jpg'),

-- Primary Colors
('color_primary', '#007bff', 'color', 'colors', 'Primary brand color', '#007bff'),
('color_primary_dark', '#0056b3', 'color', 'colors', 'Darker shade of primary color', '#0056b3'),
('color_primary_light', '#66b3ff', 'color', 'colors', 'Lighter shade of primary color', '#66b3ff'),
('color_secondary', '#6c757d', 'color', 'colors', 'Secondary color', '#6c757d'),
('color_success', '#28a745', 'color', 'colors', 'Success color for positive actions', '#28a745'),
('color_warning', '#ffc107', 'color', 'colors', 'Warning color for caution messages', '#ffc107'),
('color_danger', '#dc3545', 'color', 'colors', 'Danger color for errors and destructive actions', '#dc3545'),
('color_info', '#17a2b8', 'color', 'colors', 'Info color for informational messages', '#17a2b8'),

-- Background Colors
('color_bg_main', '#ffffff', 'color', 'backgrounds', 'Main background color', '#ffffff'),
('color_bg_secondary', '#f8f9fa', 'color', 'backgrounds', 'Secondary background color', '#f8f9fa'),
('color_bg_dark', '#343a40', 'color', 'backgrounds', 'Dark background color', '#343a40'),
('color_bg_card', '#ffffff', 'color', 'backgrounds', 'Card background color', '#ffffff'),
('color_bg_header', '#ffffff', 'color', 'backgrounds', 'Header background color', '#ffffff'),
('color_bg_footer', '#343a40', 'color', 'backgrounds', 'Footer background color', '#343a40'),

-- Text Colors
('color_text_primary', '#212529', 'color', 'text', 'Primary text color', '#212529'),
('color_text_secondary', '#6c757d', 'color', 'text', 'Secondary text color', '#6c757d'),
('color_text_muted', '#868e96', 'color', 'text', 'Muted text color', '#868e96'),
('color_text_light', '#ffffff', 'color', 'text', 'Light text color for dark backgrounds', '#ffffff'),
('color_text_link', '#007bff', 'color', 'text', 'Link text color', '#007bff'),
('color_text_link_hover', '#0056b3', 'color', 'text', 'Link hover color', '#0056b3'),

-- Border and Shadow Colors
('color_border', '#dee2e6', 'color', 'borders', 'Default border color', '#dee2e6'),
('color_border_light', '#e9ecef', 'color', 'borders', 'Light border color', '#e9ecef'),
('color_border_dark', '#adb5bd', 'color', 'borders', 'Dark border color', '#adb5bd'),
('color_shadow', 'rgba(0,0,0,0.1)', 'color', 'borders', 'Default shadow color', 'rgba(0,0,0,0.1)'),

-- Button Styles
('button_border_radius', '4', 'number', 'buttons', 'Button border radius in pixels', '4'),
('button_padding_x', '12', 'number', 'buttons', 'Button horizontal padding in pixels', '12'),
('button_padding_y', '8', 'number', 'buttons', 'Button vertical padding in pixels', '8'),
('button_font_weight', '500', 'number', 'buttons', 'Button font weight', '500'),

-- Typography
('font_family_primary', '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif', 'text', 'typography', 'Primary font family', '"Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif'),
('font_family_secondary', 'Georgia, "Times New Roman", serif', 'text', 'typography', 'Secondary font family for headings', 'Georgia, "Times New Roman", serif'),
('font_size_base', '16', 'number', 'typography', 'Base font size in pixels', '16'),
('font_size_small', '14', 'number', 'typography', 'Small font size in pixels', '14'),
('font_size_large', '18', 'number', 'typography', 'Large font size in pixels', '18'),
('line_height_base', '1.5', 'text', 'typography', 'Base line height', '1.5'),

-- Layout Settings
('layout_max_width', '1200', 'number', 'layout', 'Maximum content width in pixels', '1200'),
('layout_sidebar_width', '250', 'number', 'layout', 'Sidebar width in pixels', '250'),
('layout_header_height', '60', 'number', 'layout', 'Header height in pixels', '60'),
('layout_footer_height', '80', 'number', 'layout', 'Footer height in pixels', '80'),
('layout_border_radius', '8', 'number', 'layout', 'Default border radius in pixels', '8'),
('layout_spacing_small', '8', 'number', 'layout', 'Small spacing in pixels', '8'),
('layout_spacing_medium', '16', 'number', 'layout', 'Medium spacing in pixels', '16'),
('layout_spacing_large', '24', 'number', 'layout', 'Large spacing in pixels', '24'),

-- Badge Settings
('badge_border_radius', '12', 'number', 'badges', 'Badge border radius in pixels', '12'),
('badge_font_size', '12', 'number', 'badges', 'Badge font size in pixels', '12'),
('badge_padding_x', '8', 'number', 'badges', 'Badge horizontal padding in pixels', '8'),
('badge_padding_y', '4', 'number', 'badges', 'Badge vertical padding in pixels', '4'),
('badge_verified_color', '#28a745', 'color', 'badges', 'Verified badge color', '#28a745'),
('badge_premium_color', '#ffc107', 'color', 'badges', 'Premium badge color', '#ffc107'),
('badge_live_color', '#dc3545', 'color', 'badges', 'Live badge color', '#dc3545'),
('badge_new_color', '#17a2b8', 'color', 'badges', 'New content badge color', '#17a2b8'),

-- Social Media Colors
('color_youtube', '#ff0000', 'color', 'social', 'YouTube brand color', '#ff0000'),
('color_twitter', '#1da1f2', 'color', 'social', 'Twitter brand color', '#1da1f2'),
('color_facebook', '#1877f2', 'color', 'social', 'Facebook brand color', '#1877f2'),
('color_instagram', '#e4405f', 'color', 'social', 'Instagram brand color', '#e4405f'),
('color_tiktok', '#000000', 'color', 'social', 'TikTok brand color', '#000000'),
('color_discord', '#7289da', 'color', 'social', 'Discord brand color', '#7289da'),

-- Advanced Settings
('enable_dark_mode', '1', 'boolean', 'advanced', 'Enable dark mode toggle', '1'),
('enable_custom_css', '1', 'boolean', 'advanced', 'Allow custom CSS injection', '1'),
('custom_css', '', 'text', 'advanced', 'Custom CSS code', ''),
('custom_js', '', 'text', 'advanced', 'Custom JavaScript code', ''),
('enable_animations', '1', 'boolean', 'advanced', 'Enable CSS animations and transitions', '1'),
('animation_duration', '300', 'number', 'advanced', 'Default animation duration in milliseconds', '300'),

-- Player Settings
('player_primary_color', '#007bff', 'color', 'player', 'Video player primary color', '#007bff'),
('player_accent_color', '#ffffff', 'color', 'player', 'Video player accent color', '#ffffff'),
('player_background_color', '#000000', 'color', 'player', 'Video player background color', '#000000'),
('player_border_radius', '8', 'number', 'player', 'Video player border radius', '8'),

-- Email Template Colors
('email_primary_color', '#007bff', 'color', 'email', 'Primary color for email templates', '#007bff'),
('email_background_color', '#f8f9fa', 'color', 'email', 'Background color for email templates', '#f8f9fa'),
('email_text_color', '#212529', 'color', 'email', 'Text color for email templates', '#212529'),
('email_link_color', '#007bff', 'color', 'email', 'Link color for email templates', '#007bff');

-- Branding presets table for quick theme switching
CREATE TABLE IF NOT EXISTS `db_branding_presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preset_name` varchar(100) NOT NULL,
  `preset_description` text,
  `preset_data` longtext,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `preset_name` (`preset_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default presets
INSERT INTO `db_branding_presets` (`preset_name`, `preset_description`, `preset_data`, `is_default`) VALUES
('Default Blue', 'Clean blue theme with modern styling', '{"color_primary":"#007bff","color_primary_dark":"#0056b3","color_primary_light":"#66b3ff","color_bg_main":"#ffffff","color_text_primary":"#212529"}', 1),
('Dark Mode', 'Dark theme for better viewing in low light', '{"color_primary":"#0d6efd","color_bg_main":"#121212","color_bg_secondary":"#1e1e1e","color_bg_card":"#2d2d2d","color_text_primary":"#ffffff","color_text_secondary":"#b3b3b3"}', 0),
('YouTube Red', 'YouTube-inspired red theme', '{"color_primary":"#ff0000","color_primary_dark":"#cc0000","color_primary_light":"#ff6666","color_bg_main":"#ffffff","color_text_primary":"#212529"}', 0),
('Twitch Purple', 'Twitch-inspired purple theme', '{"color_primary":"#9146ff","color_primary_dark":"#7c3aed","color_primary_light":"#a855f7","color_bg_main":"#0f0f23","color_text_primary":"#ffffff"}', 0),
('Green Nature', 'Nature-inspired green theme', '{"color_primary":"#28a745","color_primary_dark":"#1e7e34","color_primary_light":"#5cb85c","color_bg_main":"#f8fff8","color_text_primary":"#155724"}', 0);

-- Custom CSS cache table for performance
CREATE TABLE IF NOT EXISTS `db_branding_cache` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cache_key` varchar(100) NOT NULL,
  `cache_data` longtext,
  `cache_type` enum('css','js','json') DEFAULT 'css',
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cache_key` (`cache_key`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;