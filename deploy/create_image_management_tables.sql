-- EasyStream Image Management Extension for Branding System
-- This extends the branding system with comprehensive image upload and management

-- Image uploads table
CREATE TABLE IF NOT EXISTS `db_branding_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_key` varchar(100) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `stored_filename` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `recommended_width` int(11) DEFAULT NULL,
  `recommended_height` int(11) DEFAULT NULL,
  `image_type` enum('logo','icon','background','avatar','thumbnail','banner','favicon') DEFAULT 'logo',
  `is_active` tinyint(1) DEFAULT 1,
  `upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `image_key` (`image_key`),
  KEY `image_type` (`image_type`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Image variants table (for different sizes/formats)
CREATE TABLE IF NOT EXISTS `db_branding_image_variants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_image_id` int(11) NOT NULL,
  `variant_type` enum('thumbnail','small','medium','large','retina','webp','avif') NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_image_id` (`parent_image_id`),
  KEY `variant_type` (`variant_type`),
  FOREIGN KEY (`parent_image_id`) REFERENCES `db_branding_images` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update branding settings to include image specifications
INSERT INTO `db_branding_settings` (`setting_key`, `setting_value`, `setting_type`, `category`, `description`, `default_value`) VALUES
-- Image specifications and requirements
('logo_main_width', '200', 'number', 'image_specs', 'Recommended width for main logo in pixels', '200'),
('logo_main_height', '60', 'number', 'image_specs', 'Recommended height for main logo in pixels', '60'),
('logo_small_width', '32', 'number', 'image_specs', 'Recommended width for small logo in pixels', '32'),
('logo_small_height', '32', 'number', 'image_specs', 'Recommended height for small logo in pixels', '32'),
('favicon_width', '32', 'number', 'image_specs', 'Recommended width for favicon in pixels', '32'),
('favicon_height', '32', 'number', 'image_specs', 'Recommended height for favicon in pixels', '32'),
('avatar_default_width', '128', 'number', 'image_specs', 'Recommended width for default avatar in pixels', '128'),
('avatar_default_height', '128', 'number', 'image_specs', 'Recommended height for default avatar in pixels', '128'),
('thumbnail_default_width', '320', 'number', 'image_specs', 'Recommended width for default thumbnail in pixels', '320'),
('thumbnail_default_height', '180', 'number', 'image_specs', 'Recommended height for default thumbnail in pixels', '180'),
('banner_width', '1200', 'number', 'image_specs', 'Recommended width for banner images in pixels', '1200'),
('banner_height', '300', 'number', 'image_specs', 'Recommended height for banner images in pixels', '300'),
('background_width', '1920', 'number', 'image_specs', 'Recommended width for background images in pixels', '1920'),
('background_height', '1080', 'number', 'image_specs', 'Recommended height for background images in pixels', '1080'),

-- Upload settings
('max_upload_size', '5242880', 'number', 'upload_settings', 'Maximum upload size in bytes (5MB default)', '5242880'),
('allowed_image_types', 'jpg,jpeg,png,gif,svg,webp', 'text', 'upload_settings', 'Allowed image file extensions', 'jpg,jpeg,png,gif,svg,webp'),
('auto_resize_images', '1', 'boolean', 'upload_settings', 'Automatically resize images to recommended dimensions', '1'),
('generate_webp', '1', 'boolean', 'upload_settings', 'Generate WebP variants for better performance', '1'),
('generate_retina', '1', 'boolean', 'upload_settings', 'Generate 2x retina variants for high-DPI displays', '1'),
('image_quality', '85', 'number', 'upload_settings', 'JPEG compression quality (1-100)', '85'),
('webp_quality', '80', 'number', 'upload_settings', 'WebP compression quality (1-100)', '80'),

-- Image storage settings
('upload_directory', 'f_data/branding_images', 'text', 'upload_settings', 'Directory for storing uploaded images', 'f_data/branding_images'),
('use_cdn', '0', 'boolean', 'upload_settings', 'Use CDN for image delivery', '0'),
('cdn_base_url', '', 'text', 'upload_settings', 'CDN base URL for images', ''),

-- Watermark settings
('enable_watermark', '0', 'boolean', 'watermark', 'Enable watermark on uploaded images', '0'),
('watermark_image', '', 'text', 'watermark', 'Path to watermark image', ''),
('watermark_position', 'bottom-right', 'text', 'watermark', 'Watermark position (top-left, top-right, bottom-left, bottom-right, center)', 'bottom-right'),
('watermark_opacity', '50', 'number', 'watermark', 'Watermark opacity percentage', '50'),
('watermark_margin', '10', 'number', 'watermark', 'Watermark margin from edges in pixels', '10');

-- Image dimension presets for different use cases
CREATE TABLE IF NOT EXISTS `db_image_presets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `preset_name` varchar(100) NOT NULL,
  `preset_key` varchar(100) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `aspect_ratio` varchar(20) DEFAULT NULL,
  `description` text,
  `use_case` varchar(100) DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `preset_key` (`preset_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert image dimension presets
INSERT INTO `db_image_presets` (`preset_name`, `preset_key`, `width`, `height`, `aspect_ratio`, `description`, `use_case`, `is_required`) VALUES
-- Logos
('Main Logo', 'logo_main', 200, 60, '10:3', 'Primary logo displayed in header and main navigation', 'Header, Navigation', 1),
('Small Logo', 'logo_small', 32, 32, '1:1', 'Compact logo for mobile views and small spaces', 'Mobile, Compact views', 1),
('Logo Large', 'logo_large', 400, 120, '10:3', 'Large logo for landing pages and promotional materials', 'Landing pages, Marketing', 0),

-- Icons and Favicons
('Favicon', 'favicon', 32, 32, '1:1', 'Browser tab icon (will generate multiple sizes)', 'Browser tab, Bookmarks', 1),
('Apple Touch Icon', 'apple_touch_icon', 180, 180, '1:1', 'iOS home screen icon', 'iOS devices', 0),
('Android Icon', 'android_icon', 192, 192, '1:1', 'Android home screen icon', 'Android devices', 0),

-- User Interface
('Default Avatar', 'avatar_default', 128, 128, '1:1', 'Default user profile picture', 'User profiles', 1),
('Default Thumbnail', 'thumbnail_default', 320, 180, '16:9', 'Default video thumbnail image', 'Video placeholders', 1),
('Loading Placeholder', 'loading_placeholder', 320, 180, '16:9', 'Image shown while content loads', 'Loading states', 0),

-- Banners and Headers
('Header Banner', 'banner_header', 1200, 300, '4:1', 'Main header banner for homepage', 'Homepage header', 0),
('Section Banner', 'banner_section', 800, 200, '4:1', 'Section header banners', 'Category pages', 0),
('Promotional Banner', 'banner_promo', 728, 90, '8:1', 'Promotional banner for announcements', 'Promotions', 0),

-- Backgrounds
('Hero Background', 'bg_hero', 1920, 1080, '16:9', 'Full-screen hero section background', 'Hero sections', 0),
('Page Background', 'bg_page', 1920, 1080, '16:9', 'General page background image', 'Page backgrounds', 0),
('Pattern Background', 'bg_pattern', 400, 400, '1:1', 'Repeating pattern background', 'Texture overlays', 0),

-- Social Media
('Open Graph Image', 'og_image', 1200, 630, '1.91:1', 'Image for social media sharing', 'Social sharing', 0),
('Twitter Card', 'twitter_card', 1200, 600, '2:1', 'Twitter card image', 'Twitter sharing', 0),
('YouTube Thumbnail', 'youtube_thumb', 1280, 720, '16:9', 'YouTube video thumbnail', 'Video sharing', 0),

-- Email Templates
('Email Header', 'email_header', 600, 150, '4:1', 'Email template header image', 'Email marketing', 0),
('Email Banner', 'email_banner', 600, 200, '3:1', 'Email promotional banner', 'Email campaigns', 0),

-- Mobile Specific
('Mobile Logo', 'logo_mobile', 120, 40, '3:1', 'Mobile-optimized logo', 'Mobile navigation', 0),
('Mobile Banner', 'banner_mobile', 375, 200, '1.875:1', 'Mobile banner image', 'Mobile headers', 0),
('App Icon', 'app_icon', 512, 512, '1:1', 'Mobile app icon', 'Mobile app', 0);