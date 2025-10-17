-- Create additional indexes for better API performance
-- Add indexes to existing tables for better query performance

-- Video files indexes
ALTER TABLE `db_videofiles` 
ADD INDEX IF NOT EXISTS `idx_file_type` (`file_type`),
ADD INDEX IF NOT EXISTS `idx_upload_date` (`upload_date`),
ADD INDEX IF NOT EXISTS `idx_file_views` (`file_views`),
ADD INDEX IF NOT EXISTS `idx_usr_id_upload_date` (`usr_id`, `upload_date`);

-- Users indexes  
ALTER TABLE `db_users`
ADD INDEX IF NOT EXISTS `idx_usr_email` (`usr_email`),
ADD INDEX IF NOT EXISTS `idx_usr_status` (`usr_status`),
ADD INDEX IF NOT EXISTS `idx_usr_signup_date` (`usr_signup_date`);

-- Comments indexes (if table exists)
CREATE INDEX IF NOT EXISTS `idx_comment_video_id` ON `db_comments` (`video_id`);
CREATE INDEX IF NOT EXISTS `idx_comment_user_id` ON `db_comments` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_comment_created_at` ON `db_comments` (`created_at`);

-- Sessions indexes (if table exists)
CREATE INDEX IF NOT EXISTS `idx_session_user_id` ON `db_sessions` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_session_expires` ON `db_sessions` (`expires_at`);