-- Membership tiers per channel
CREATE TABLE IF NOT EXISTS `db_membership_tiers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usr_id` INT UNSIGNED NOT NULL,
  `tier_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(120) NOT NULL,
  `price_cents` INT UNSIGNED NOT NULL DEFAULT 0,
  `currency` CHAR(3) NOT NULL DEFAULT 'USD',
  `perks_json` TEXT NULL,
  `badge_key` VARCHAR(64) NULL,
  `chat_access` TINYINT(1) NOT NULL DEFAULT 1,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `usr_tier` (`usr_id`,`tier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- User <> Channel membership relationships
CREATE TABLE IF NOT EXISTS `db_user_memberships` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscriber_usr_id` INT UNSIGNED NOT NULL,
  `channel_usr_id` INT UNSIGNED NOT NULL,
  `tier_id` INT UNSIGNED NOT NULL,
  `status` ENUM('active','canceled','expired','past_due') NOT NULL DEFAULT 'active',
  `provider` VARCHAR(24) NULL,
  `provider_ref` VARCHAR(128) NULL,
  `started_at` DATETIME NOT NULL,
  `expires_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `sub_chan` (`subscriber_usr_id`,`channel_usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional file access rules
CREATE TABLE IF NOT EXISTS `db_file_access_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_key` VARCHAR(32) NOT NULL,
  `type` ENUM('video','live','short','image','audio','doc','blog') NOT NULL DEFAULT 'video',
  `channel_usr_id` INT UNSIGNED NOT NULL,
  `required_tier_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `u_file` (`file_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

