EasyStream Database Configuration Fix
====================================

If you're getting the error:
"Warning: Undefined array key \"new_layout\" in /srv/easystream/f_core/config.smarty.php on line 44"

This means some database configurations are missing. To fix this:

OPTION 1: Run the PHP fix script
--------------------------------
1. Navigate to your EasyStream installation directory
2. Run: php __install/fix_database.php

OPTION 2: Run the SQL script manually
------------------------------------
1. Import the SQL file into your database:
   mysql -u your_username -p your_database < __install/fix_missing_config.sql

OPTION 3: Add manually via phpMyAdmin or similar
-----------------------------------------------
Execute this SQL query in your database:

INSERT IGNORE INTO `db_settings` (`cfg_name`, `cfg_data`, `cfg_info`) VALUES 
('new_layout', '1', 'backend: enable/disable new layout menu'),
('short_module', '1', 'backend: enable/disable video shorts'),
('short_uploads', '1', 'backend: enable/disable video shorts uploads'),
('channel_memberships', '0', 'backend: enable/disable channel memberships'),
('member_chat_only', '0', 'backend: enable/disable member-only chat'),
('member_badges', '0', 'backend: enable/disable member badges');

After applying the fix, the control panel should be accessible without errors.
