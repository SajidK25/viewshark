-- VMAP/VAST ad rules and caps
CREATE TABLE IF NOT EXISTS `db_ad_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `scope` ENUM('global','category','channel') NOT NULL DEFAULT 'global',
  `scope_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `ad_break` ENUM('pre','mid','post') NOT NULL DEFAULT 'pre',
  `vmap_url` TEXT NULL,
  `vast_key` VARCHAR(64) NULL,
  `weight` INT UNSIGNED NOT NULL DEFAULT 1,
  `mobile_only` TINYINT(1) NOT NULL DEFAULT 0,
  `active_from` DATETIME NULL,
  `active_to` DATETIME NULL,
  `cap_per_user` INT UNSIGNED NOT NULL DEFAULT 0,
  `cap_window_min` INT UNSIGNED NOT NULL DEFAULT 0,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `scope_idx` (`scope`,`scope_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `db_ad_caps` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usr_id` INT UNSIGNED NULL,
  `cookie_id` VARCHAR(64) NULL,
  `rule_id` INT UNSIGNED NOT NULL,
  `hit_at` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_idx` (`rule_id`,`hit_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

