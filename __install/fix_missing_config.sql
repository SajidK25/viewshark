-- Fix for missing new_layout configuration
-- This script adds the missing new_layout configuration to the database

INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_data`, `cfg_info`) VALUES 
('new_layout', '1', 'backend: enable/disable new layout menu');

-- Also add other potentially missing configurations that might be needed
INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_data`, `cfg_info`) VALUES 
('short_module', '1', 'backend: enable/disable video shorts'),
('short_uploads', '1', 'backend: enable/disable video shorts uploads'),
('channel_memberships', '0', 'backend: enable/disable channel memberships'),
('member_chat_only', '0', 'backend: enable/disable member-only chat'),
('member_badges', '0', 'backend: enable/disable member badges');