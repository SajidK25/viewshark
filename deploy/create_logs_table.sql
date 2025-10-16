-- Create logs table for database logging (optional)
CREATE TABLE IF NOT EXISTS `db_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `context` longtext,
  `request_id` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `request_uri` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_level` (`level`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_request_id` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create index for faster queries
CREATE INDEX `idx_level_created` ON `db_logs` (`level`, `created_at`);
CREATE INDEX `idx_user_created` ON `db_logs` (`user_id`, `created_at`);