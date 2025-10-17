-- Role-Based Access Control (RBAC) Tables for EasyStream
-- This file creates tables for the RBAC permission system

-- Role permissions table - defines what permissions each role has
CREATE TABLE IF NOT EXISTS `db_role_permissions` (
    `role_permission_id` INT PRIMARY KEY AUTO_INCREMENT,
    `role` ENUM('guest', 'member', 'verified', 'premium', 'moderator', 'admin', 'superadmin') NOT NULL,
    `permission` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `created_by` VARCHAR(50) DEFAULT 'system',
    UNIQUE KEY unique_role_permission (role, permission),
    INDEX idx_role (role),
    INDEX idx_permission (permission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User-specific permissions table - for custom permissions beyond role
CREATE TABLE IF NOT EXISTS `db_user_permissions` (
    `permission_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `permission` VARCHAR(100) NOT NULL,
    `granted_by` INT NOT NULL,
    `granted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    `revoked_at` TIMESTAMP NULL,
    `revoked_by` INT NULL,
    `notes` TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (revoked_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_permission (permission),
    INDEX idx_expires_at (expires_at),
    INDEX idx_revoked_at (revoked_at),
    INDEX idx_user_permission (user_id, permission)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Role change history
CREATE TABLE IF NOT EXISTS `db_role_history` (
    `history_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `old_role` ENUM('guest', 'member', 'verified', 'premium', 'moderator', 'admin', 'superadmin') NOT NULL,
    `new_role` ENUM('guest', 'member', 'verified', 'premium', 'moderator', 'admin', 'superadmin') NOT NULL,
    `changed_by` INT NOT NULL,
    `reason` TEXT NULL,
    `changed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_changed_by (changed_by),
    INDEX idx_changed_at (changed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User suspensions table
CREATE TABLE IF NOT EXISTS `db_user_suspensions` (
    `suspension_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `reason` TEXT NOT NULL,
    `suspended_by` INT NOT NULL,
    `suspended_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `expires_at` TIMESTAMP NULL,
    `lifted_at` TIMESTAMP NULL,
    `lifted_by` INT NULL,
    `lift_reason` TEXT NULL,
    `notes` TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (suspended_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (lifted_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_suspended_by (suspended_by),
    INDEX idx_suspended_at (suspended_at),
    INDEX idx_expires_at (expires_at),
    INDEX idx_lifted_at (lifted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User bans table
CREATE TABLE IF NOT EXISTS `db_user_bans` (
    `ban_id` INT PRIMARY KEY AUTO_INCREMENT,
    `user_id` INT NOT NULL,
    `reason` TEXT NOT NULL,
    `banned_by` INT NOT NULL,
    `banned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `permanent` BOOLEAN DEFAULT FALSE,
    `lifted_at` TIMESTAMP NULL,
    `lifted_by` INT NULL,
    `lift_reason` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `notes` TEXT NULL,
    FOREIGN KEY (user_id) REFERENCES db_users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (banned_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    FOREIGN KEY (lifted_by) REFERENCES db_users(user_id) ON DELETE RESTRICT,
    INDEX idx_user_id (user_id),
    INDEX idx_banned_by (banned_by),
    INDEX idx_banned_at (banned_at),
    INDEX idx_permanent (permanent),
    INDEX idx_lifted_at (lifted_at),
    INDEX idx_ip_address (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permission categories for organization
CREATE TABLE IF NOT EXISTS `db_permission_categories` (
    `category_id` INT PRIMARY KEY AUTO_INCREMENT,
    `category_name` VARCHAR(50) NOT NULL UNIQUE,
    `description` TEXT NULL,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- All available permissions registry
CREATE TABLE IF NOT EXISTS `db_permissions` (
    `permission_id` INT PRIMARY KEY AUTO_INCREMENT,
    `permission` VARCHAR(100) NOT NULL UNIQUE,
    `category_id` INT NULL,
    `description` TEXT NULL,
    `is_system` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES db_permission_categories(category_id) ON DELETE SET NULL,
    INDEX idx_permission (permission),
    INDEX idx_category_id (category_id),
    INDEX idx_is_system (is_system)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert permission categories
INSERT IGNORE INTO `db_permission_categories` (`category_name`, `description`, `sort_order`) VALUES
('Content', 'Content creation and management permissions', 1),
('User', 'User profile and account permissions', 2),
('Comment', 'Comment and interaction permissions', 3),
('Stream', 'Live streaming permissions', 4),
('Upload', 'File upload permissions', 5),
('Admin', 'Administrative permissions', 6),
('API', 'API access permissions', 7),
('Special', 'Special system permissions', 8);

-- Insert all available permissions
INSERT IGNORE INTO `db_permissions` (`permission`, `category_id`, `description`, `is_system`) VALUES
-- Content permissions
('content.view', 1, 'View content', FALSE),
('content.create', 1, 'Create content', FALSE),
('content.edit', 1, 'Edit own content', FALSE),
('content.delete', 1, 'Delete own content', FALSE),
('content.publish', 1, 'Publish content', FALSE),
('content.moderate', 1, 'Moderate any content', TRUE),

-- User permissions
('user.view_profile', 2, 'View user profiles', FALSE),
('user.edit_profile', 2, 'Edit own profile', FALSE),
('user.manage', 2, 'Manage other users', TRUE),
('user.ban', 2, 'Ban/suspend users', TRUE),

-- Comment permissions
('comment.view', 3, 'View comments', FALSE),
('comment.create', 3, 'Create comments', FALSE),
('comment.edit', 3, 'Edit own comments', FALSE),
('comment.delete', 3, 'Delete own comments', FALSE),
('comment.moderate', 3, 'Moderate any comments', TRUE),

-- Live streaming permissions
('stream.view', 4, 'View live streams', FALSE),
('stream.create', 4, 'Create live streams', FALSE),
('stream.manage', 4, 'Manage own streams', FALSE),
('stream.moderate', 4, 'Moderate any streams', TRUE),

-- Upload permissions
('upload.video', 5, 'Upload videos', FALSE),
('upload.image', 5, 'Upload images', FALSE),
('upload.audio', 5, 'Upload audio', FALSE),
('upload.document', 5, 'Upload documents', FALSE),
('upload.large_files', 5, 'Upload large files', FALSE),

-- Admin permissions
('admin.dashboard', 6, 'Access admin dashboard', TRUE),
('admin.settings', 6, 'Manage site settings', TRUE),
('admin.users', 6, 'Manage users', TRUE),
('admin.content', 6, 'Manage all content', TRUE),
('admin.reports', 6, 'View reports and analytics', TRUE),
('admin.logs', 6, 'View system logs', TRUE),
('admin.system', 6, 'System administration', TRUE),

-- API permissions
('api.access', 7, 'Access API', FALSE),
('api.admin', 7, 'Access admin API endpoints', TRUE),

-- Special permissions
('bypass.rate_limit', 8, 'Bypass rate limiting', TRUE),
('bypass.content_filter', 8, 'Bypass content filters', TRUE),
('feature.beta', 8, 'Access beta features', FALSE);

-- Insert default role permissions
INSERT IGNORE INTO `db_role_permissions` (`role`, `permission`) VALUES
-- Guest permissions
('guest', 'content.view'),
('guest', 'comment.view'),
('guest', 'stream.view'),
('guest', 'user.view_profile'),

-- Member permissions (includes all guest permissions)
('member', 'content.view'),
('member', 'content.create'),
('member', 'content.edit'),
('member', 'content.delete'),
('member', 'comment.view'),
('member', 'comment.create'),
('member', 'comment.edit'),
('member', 'comment.delete'),
('member', 'stream.view'),
('member', 'stream.create'),
('member', 'stream.manage'),
('member', 'user.view_profile'),
('member', 'user.edit_profile'),
('member', 'upload.video'),
('member', 'upload.image'),
('member', 'upload.audio'),
('member', 'api.access'),

-- Verified permissions (includes all member permissions)
('verified', 'content.view'),
('verified', 'content.create'),
('verified', 'content.edit'),
('verified', 'content.delete'),
('verified', 'content.publish'),
('verified', 'comment.view'),
('verified', 'comment.create'),
('verified', 'comment.edit'),
('verified', 'comment.delete'),
('verified', 'stream.view'),
('verified', 'stream.create'),
('verified', 'stream.manage'),
('verified', 'user.view_profile'),
('verified', 'user.edit_profile'),
('verified', 'upload.video'),
('verified', 'upload.image'),
('verified', 'upload.audio'),
('verified', 'upload.document'),
('verified', 'api.access'),

-- Premium permissions (includes all verified permissions)
('premium', 'content.view'),
('premium', 'content.create'),
('premium', 'content.edit'),
('premium', 'content.delete'),
('premium', 'content.publish'),
('premium', 'comment.view'),
('premium', 'comment.create'),
('premium', 'comment.edit'),
('premium', 'comment.delete'),
('premium', 'stream.view'),
('premium', 'stream.create'),
('premium', 'stream.manage'),
('premium', 'user.view_profile'),
('premium', 'user.edit_profile'),
('premium', 'upload.video'),
('premium', 'upload.image'),
('premium', 'upload.audio'),
('premium', 'upload.document'),
('premium', 'upload.large_files'),
('premium', 'api.access'),
('premium', 'feature.beta'),

-- Moderator permissions (includes premium + moderation)
('moderator', 'content.view'),
('moderator', 'content.create'),
('moderator', 'content.edit'),
('moderator', 'content.delete'),
('moderator', 'content.publish'),
('moderator', 'content.moderate'),
('moderator', 'comment.view'),
('moderator', 'comment.create'),
('moderator', 'comment.edit'),
('moderator', 'comment.delete'),
('moderator', 'comment.moderate'),
('moderator', 'stream.view'),
('moderator', 'stream.create'),
('moderator', 'stream.manage'),
('moderator', 'stream.moderate'),
('moderator', 'user.view_profile'),
('moderator', 'user.edit_profile'),
('moderator', 'user.manage'),
('moderator', 'upload.video'),
('moderator', 'upload.image'),
('moderator', 'upload.audio'),
('moderator', 'upload.document'),
('moderator', 'upload.large_files'),
('moderator', 'api.access'),
('moderator', 'feature.beta'),
('moderator', 'bypass.rate_limit'),

-- Admin permissions (includes moderator + admin features)
('admin', 'content.view'),
('admin', 'content.create'),
('admin', 'content.edit'),
('admin', 'content.delete'),
('admin', 'content.publish'),
('admin', 'content.moderate'),
('admin', 'comment.view'),
('admin', 'comment.create'),
('admin', 'comment.edit'),
('admin', 'comment.delete'),
('admin', 'comment.moderate'),
('admin', 'stream.view'),
('admin', 'stream.create'),
('admin', 'stream.manage'),
('admin', 'stream.moderate'),
('admin', 'user.view_profile'),
('admin', 'user.edit_profile'),
('admin', 'user.manage'),
('admin', 'user.ban'),
('admin', 'upload.video'),
('admin', 'upload.image'),
('admin', 'upload.audio'),
('admin', 'upload.document'),
('admin', 'upload.large_files'),
('admin', 'admin.dashboard'),
('admin', 'admin.settings'),
('admin', 'admin.users'),
('admin', 'admin.content'),
('admin', 'admin.reports'),
('admin', 'admin.logs'),
('admin', 'api.access'),
('admin', 'api.admin'),
('admin', 'feature.beta'),
('admin', 'bypass.rate_limit'),
('admin', 'bypass.content_filter'),

-- Superadmin permissions (all permissions)
('superadmin', 'content.view'),
('superadmin', 'content.create'),
('superadmin', 'content.edit'),
('superadmin', 'content.delete'),
('superadmin', 'content.publish'),
('superadmin', 'content.moderate'),
('superadmin', 'comment.view'),
('superadmin', 'comment.create'),
('superadmin', 'comment.edit'),
('superadmin', 'comment.delete'),
('superadmin', 'comment.moderate'),
('superadmin', 'stream.view'),
('superadmin', 'stream.create'),
('superadmin', 'stream.manage'),
('superadmin', 'stream.moderate'),
('superadmin', 'user.view_profile'),
('superadmin', 'user.edit_profile'),
('superadmin', 'user.manage'),
('superadmin', 'user.ban'),
('superadmin', 'upload.video'),
('superadmin', 'upload.image'),
('superadmin', 'upload.audio'),
('superadmin', 'upload.document'),
('superadmin', 'upload.large_files'),
('superadmin', 'admin.dashboard'),
('superadmin', 'admin.settings'),
('superadmin', 'admin.users'),
('superadmin', 'admin.content'),
('superadmin', 'admin.reports'),
('superadmin', 'admin.logs'),
('superadmin', 'admin.system'),
('superadmin', 'api.access'),
('superadmin', 'api.admin'),
('superadmin', 'feature.beta'),
('superadmin', 'bypass.rate_limit'),
('superadmin', 'bypass.content_filter');

-- Add RBAC-related settings
INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_value`, `cfg_description`) VALUES
('rbac_enabled', '1', 'Enable role-based access control'),
('default_user_role', 'member', 'Default role for new users'),
('role_change_log', '1', 'Log all role changes'),
('permission_cache_ttl', '3600', 'Permission cache TTL in seconds'),
('auto_verify_email', '0', 'Automatically verify email for new users'),
('moderator_approval_required', '0', 'Require moderator approval for content'),
('admin_notification_role_change', '1', 'Notify admins of role changes'),
('suspension_auto_lift', '1', 'Automatically lift expired suspensions'),
('ban_ip_on_user_ban', '0', 'Also ban IP address when banning user'),
('max_custom_permissions_per_user', '50', 'Maximum custom permissions per user');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_users_role_status ON db_users(role, status);
CREATE INDEX IF NOT EXISTS idx_role_permissions_role ON db_role_permissions(role);
CREATE INDEX IF NOT EXISTS idx_user_permissions_active ON db_user_permissions(user_id, revoked_at, expires_at);

-- Create views for easier querying
CREATE OR REPLACE VIEW v_active_user_permissions AS
SELECT 
    up.user_id,
    up.permission,
    up.granted_by,
    up.granted_at,
    up.expires_at,
    'custom' as permission_source
FROM db_user_permissions up
WHERE up.revoked_at IS NULL 
  AND (up.expires_at IS NULL OR up.expires_at > NOW())
UNION ALL
SELECT 
    u.user_id,
    rp.permission,
    NULL as granted_by,
    u.created_at as granted_at,
    NULL as expires_at,
    'role' as permission_source
FROM db_users u
JOIN db_role_permissions rp ON u.role = rp.role
WHERE u.status = 'active';

-- Create view for user role history
CREATE OR REPLACE VIEW v_user_role_summary AS
SELECT 
    u.user_id,
    u.username,
    u.email,
    u.role as current_role,
    u.status,
    COUNT(rh.history_id) as role_changes,
    MAX(rh.changed_at) as last_role_change,
    (SELECT COUNT(*) FROM db_user_suspensions us WHERE us.user_id = u.user_id) as suspension_count,
    (SELECT COUNT(*) FROM db_user_bans ub WHERE ub.user_id = u.user_id) as ban_count
FROM db_users u
LEFT JOIN db_role_history rh ON u.user_id = rh.user_id
GROUP BY u.user_id, u.username, u.email, u.role, u.status;