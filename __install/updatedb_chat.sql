CREATE TABLE `db_livevips` (
  `db_id` int(9) UNSIGNED NOT NULL,
  `channel_id` int(10) UNSIGNED NOT NULL,
  `vip_list` mediumtext NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

ALTER TABLE `db_livevips`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`channel_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `active` (`active`);

ALTER TABLE `db_livevips` MODIFY `db_id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_livechat` ADD `first` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `badge`;
ALTER TABLE `db_livechat` ADD `usr_profileinc` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `badge`;
ALTER TABLE `db_livechat` ADD INDEX `ft` (`channel_owner`, `chat_user`);
ALTER TABLE `db_livechat` CHANGE `chat_id` `chat_id` VARCHAR(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `channel_owner` `channel_owner` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_user` `chat_user` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_display` `chat_display` VARCHAR(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_ip` `chat_ip` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_fp` `chat_fp` VARCHAR(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL;
ALTER TABLE `db_livecolors` ADD `avatars` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `timestamps`;
ALTER TABLE `db_livecolors` ADD `highlight_own` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `timestamps`;
ALTER TABLE `db_livecolors` ADD `highlight_mod` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `timestamps`;
ALTER TABLE `db_livecolors` ADD `highlight_vip` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `timestamps`;
ALTER TABLE `db_livecolors` ADD `highlight_atme` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `timestamps`;
ALTER TABLE `db_livecolors` ADD `highlight_byme` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `timestamps`;
