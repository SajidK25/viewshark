--
-- Table structure for table `db_shortcomments`
--

DROP TABLE IF EXISTS `db_shortcomments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortcomments` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_key` int(10) unsigned NOT NULL,
  `c_usr_id` int(10) unsigned NOT NULL,
  `c_key` int(10) unsigned NOT NULL,
  `c_replyto` int(10) unsigned NOT NULL,
  `c_body` text COLLATE utf8_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text COLLATE utf8_unicode_ci NOT NULL,
  `c_rating_value` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `c_spam` text COLLATE utf8_unicode_ci NOT NULL,
  `c_approved` tinyint(1) unsigned NOT NULL,
  `c_seen` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `c_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`c_id`),
  KEY `file_key` (`file_key`),
  KEY `c_usr_id` (`c_usr_id`),
  KEY `c_key` (`c_key`),
  KEY `c_replyto` (`c_replyto`),
  KEY `c_approved` (`c_approved`),
  KEY `c_active` (`c_active`),
  KEY `c_rating_value` (`c_rating_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortcomments`
--

LOCK TABLES `db_shortcomments` WRITE;
/*!40000 ALTER TABLE `db_shortcomments` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortcomments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortdl`
--

DROP TABLE IF EXISTS `db_shortdl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortdl` (
  `q_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_key` int(10) unsigned NOT NULL,
  `usr_key` int(10) unsigned NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `video_url` text COLLATE utf8_unicode_ci NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`q_id`),
  KEY `file_key` (`file_key`),
  KEY `usr_key` (`usr_key`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortdl`
--

LOCK TABLES `db_shortdl` WRITE;
/*!40000 ALTER TABLE `db_shortdl` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortdl` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortfavorites`
--

DROP TABLE IF EXISTS `db_shortfavorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortfavorites` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) unsigned NOT NULL,
  `fav_list` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`db_id`),
  UNIQUE KEY `uni` (`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortfavorites`
--

LOCK TABLES `db_shortfavorites` WRITE;
/*!40000 ALTER TABLE `db_shortfavorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortfavorites` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortfiles`
--

DROP TABLE IF EXISTS `db_shortfiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortfiles` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) unsigned NOT NULL,
  `file_key` int(10) unsigned NOT NULL,
  `old_file_key` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `old_key` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `file_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `file_size` int(20) unsigned NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `file_mobile` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `file_title` text CHARACTER SET utf8 NOT NULL,
  `file_description` text CHARACTER SET utf8 NOT NULL,
  `file_tags` text CHARACTER SET utf8 NOT NULL,
  `file_category` smallint(5) unsigned NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8 NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8 NOT NULL,
  `comment_votes` tinyint(1) unsigned NOT NULL,
  `comment_spam` tinyint(1) unsigned NOT NULL,
  `rating` tinyint(1) unsigned NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8 NOT NULL,
  `embedding` tinyint(1) unsigned NOT NULL,
  `social` tinyint(1) unsigned NOT NULL,
  `approved` tinyint(1) unsigned NOT NULL,
  `deleted` tinyint(1) unsigned NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thumb_server` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_promoted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_subscription` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `has_preview` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `thumb_preview` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `file_views` int(10) unsigned NOT NULL,
  `file_favorite` int(10) unsigned NOT NULL,
  `file_comments` int(10) unsigned NOT NULL,
  `file_responses` int(10) unsigned NOT NULL,
  `file_like` int(10) unsigned NOT NULL,
  `file_dislike` int(10) unsigned NOT NULL,
  `file_flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vjs_ads` text COLLATE utf8_unicode_ci NOT NULL,
  `jw_ads` text COLLATE utf8_unicode_ci NOT NULL,
  `fp_ads` text COLLATE utf8_unicode_ci NOT NULL,
  `banner_ads` text COLLATE utf8_unicode_ci NOT NULL,
  `embed_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `embed_src` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'local',
  `embed_url` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `stream_server` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `stream_key` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `stream_key_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `stream_chat` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stream_vod` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `stream_live` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `stream_start` datetime NOT NULL,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`db_id`),
  UNIQUE KEY `uni` (`embed_key`),
  KEY `usr_id` (`usr_id`),
  KEY `file_key` (`file_key`),
  KEY `file_type` (`file_type`),
  KEY `file_duration` (`file_duration`),
  KEY `file_hd` (`file_hd`),
  KEY `file_mobile` (`file_mobile`),
  KEY `upload_server` (`upload_server`),
  KEY `thumb_server` (`thumb_server`),
  KEY `is_featured` (`is_featured`),
  KEY `file_views` (`file_views`),
  KEY `file_favorite` (`file_favorite`),
  KEY `file_comments` (`file_comments`),
  KEY `file_responses` (`file_responses`),
  KEY `file_like` (`file_like`),
  KEY `file_dislike` (`file_dislike`),
  KEY `is_promoted` (`is_promoted`),
  KEY `stream_server` (`stream_server`),
  KEY `stream_key` (`stream_key`),
  KEY `stream_key_active` (`stream_key_active`),
  KEY `stream_chat` (`stream_chat`),
  KEY `stream_vod` (`stream_vod`),
  KEY `stream_live` (`stream_live`),
  KEY `stream_ended` (`stream_ended`),
  KEY `file_category` (`file_category`),
  KEY `privacy` (`privacy`,`comments`,`comment_votes`,`comment_spam`,`rating`,`responding`,`embedding`,`social`,`approved`,`deleted`),
  KEY `active` (`active`),
  KEY `old_file_key` (`old_file_key`),
  KEY `has_preview` (`has_preview`),
  KEY `old_key` (`old_key`),
  FULLTEXT KEY `file_title` (`file_title`,`file_tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortfiles`
--

LOCK TABLES `db_shortfiles` WRITE;
/*!40000 ALTER TABLE `db_shortfiles` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortfiles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shorthistory`
--

DROP TABLE IF EXISTS `db_shorthistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shorthistory` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) unsigned NOT NULL,
  `history_list` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`db_id`),
  UNIQUE KEY `uni` (`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shorthistory`
--

LOCK TABLES `db_shorthistory` WRITE;
/*!40000 ALTER TABLE `db_shorthistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shorthistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortliked`
--

DROP TABLE IF EXISTS `db_shortliked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortliked` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) unsigned NOT NULL,
  `liked_list` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`db_id`),
  UNIQUE KEY `uni` (`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortliked`
--

LOCK TABLES `db_shortliked` WRITE;
/*!40000 ALTER TABLE `db_shortliked` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortliked` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortpayouts`
--

DROP TABLE IF EXISTS `db_shortpayouts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortpayouts` (
  `p_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) unsigned NOT NULL,
  `file_key` int(10) unsigned NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p_paydate` datetime NOT NULL,
  `p_amount` float unsigned NOT NULL,
  `p_amount_shared` float unsigned NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `p_state` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `p_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  KEY `usr_key` (`usr_key`),
  KEY `file_key` (`file_key`),
  KEY `p_state` (`p_state`),
  KEY `p_active` (`p_active`),
  KEY `p_views` (`p_views`),
  KEY `usr_id` (`usr_id`),
  KEY `p_amount_shared` (`p_amount_shared`),
  KEY `p_custom` (`p_custom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortpayouts`
--

LOCK TABLES `db_shortpayouts` WRITE;
/*!40000 ALTER TABLE `db_shortpayouts` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortpayouts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortplaylists`
--

DROP TABLE IF EXISTS `db_shortplaylists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortplaylists` (
  `pl_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) unsigned NOT NULL,
  `pl_key` int(10) unsigned NOT NULL,
  `pl_name` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `pl_descr` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `pl_tags` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `pl_privacy` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) unsigned NOT NULL,
  `pl_embed` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `pl_email` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `pl_social` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `pl_thumb` int(10) unsigned NOT NULL,
  `pl_files` text COLLATE utf8_unicode_ci NOT NULL,
  `pl_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`pl_id`),
  KEY `usr_id` (`usr_id`),
  KEY `pl_key` (`pl_key`),
  KEY `pl_privacy` (`pl_privacy`),
  KEY `pl_views` (`pl_views`),
  KEY `pl_thumb` (`pl_thumb`),
  KEY `pl_active` (`pl_active`),
  FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortplaylists`
--

LOCK TABLES `db_shortplaylists` WRITE;
/*!40000 ALTER TABLE `db_shortplaylists` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortplaylists` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortque`
--

DROP TABLE IF EXISTS `db_shortque`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortque` (
  `q_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_key` int(10) unsigned NOT NULL,
  `usr_key` int(10) unsigned NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`q_id`),
  KEY `file_key` (`file_key`),
  KEY `usr_key` (`usr_key`),
  KEY `state` (`state`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortque`
--

LOCK TABLES `db_shortque` WRITE;
/*!40000 ALTER TABLE `db_shortque` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortque` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortrating`
--

DROP TABLE IF EXISTS `db_shortrating`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortrating` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_key` int(10) unsigned NOT NULL,
  `file_votes` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`db_id`),
  KEY `file_key` (`file_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortrating`
--

LOCK TABLES `db_shortrating` WRITE;
/*!40000 ALTER TABLE `db_shortrating` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortrating` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortresponses`
--

DROP TABLE IF EXISTS `db_shortresponses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortresponses` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_key` int(10) unsigned NOT NULL,
  `file_responses` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`db_id`),
  KEY `file_key` (`file_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortresponses`
--

LOCK TABLES `db_shortresponses` WRITE;
/*!40000 ALTER TABLE `db_shortresponses` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortresponses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortsubs`
--

DROP TABLE IF EXISTS `db_shortsubs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortsubs` (
  `sub_id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `file_key` int(10) unsigned NOT NULL,
  `vjs_subs` text COLLATE utf8_unicode_ci NOT NULL,
  `jw_subs` text COLLATE utf8_unicode_ci NOT NULL,
  `fp_subs` text COLLATE utf8_unicode_ci NOT NULL,
  `sub_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`sub_id`),
  UNIQUE KEY `uni` (`file_key`),
  KEY `file_key` (`file_key`),
  KEY `sub_active` (`sub_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortsubs`
--

LOCK TABLES `db_shortsubs` WRITE;
/*!40000 ALTER TABLE `db_shortsubs` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortsubs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shorttransfers`
--

DROP TABLE IF EXISTS `db_shorttransfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shorttransfers` (
  `q_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `upload_server` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `thumb_server` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `file_key` int(10) unsigned NOT NULL,
  `usr_key` int(10) unsigned NOT NULL,
  `upload_start_time` datetime NOT NULL,
  `upload_end_time` datetime NOT NULL,
  `thumb_start_time` datetime NOT NULL,
  `thumb_end_time` datetime NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`q_id`),
  UNIQUE KEY `uni` (`file_key`),
  KEY `upload_server` (`upload_server`),
  KEY `thumb_server` (`thumb_server`),
  KEY `file_key` (`file_key`),
  KEY `usr_key` (`usr_key`),
  KEY `state` (`state`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shorttransfers`
--

LOCK TABLES `db_shorttransfers` WRITE;
/*!40000 ALTER TABLE `db_shorttransfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shorttransfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `db_shortwatchlist`
--

DROP TABLE IF EXISTS `db_shortwatchlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `db_shortwatchlist` (
  `db_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usr_id` int(10) unsigned NOT NULL,
  `watch_list` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`db_id`),
  UNIQUE KEY `uni` (`usr_id`),
  KEY `usr_id` (`usr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `db_shortwatchlist`
--

LOCK TABLES `db_shortwatchlist` WRITE;
/*!40000 ALTER TABLE `db_shortwatchlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `db_shortwatchlist` ENABLE KEYS */;
UNLOCK TABLES;


ALTER TABLE `db_shortfiles` DROP INDEX `old_key`;
ALTER TABLE `db_shortfiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_shortfiles` DROP INDEX `privacy`;
ALTER TABLE `db_shortfiles` DROP INDEX `has_preview`;
ALTER TABLE `db_shortfiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_shortfiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_shortfiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_shortfiles` DROP INDEX `stream_server`;
ALTER TABLE `db_shortfiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_shortfiles` DROP INDEX `file_like`;
ALTER TABLE `db_shortfiles` DROP INDEX `file_responses`;
ALTER TABLE `db_shortfiles` DROP INDEX `file_comments`;
ALTER TABLE `db_shortfiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_shortfiles` DROP INDEX `file_type`;
ALTER TABLE `db_shortfiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_shortfiles` DROP INDEX `upload_server`;
ALTER TABLE `db_shortfiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);
ALTER TABLE `db_shortfiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_shortfiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);
ALTER TABLE `db_shortfiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);
ALTER TABLE `db_shortfiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;

RENAME TABLE `db_shorthistory` TO `db_shorthistory_old`;
CREATE TABLE `db_shorthistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_shorthistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_shorthistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_shorthistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_shorthistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_shortfavorites` TO `db_shortfavorites_old`;
CREATE TABLE `db_shortfavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_shortfavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_shortfavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_shortfavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_shortliked` TO `db_shortliked_old`;
CREATE TABLE `db_shortliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_shortliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_shortliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_shortliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_shortwatchlist` TO `db_shortwatchlist_old`;
CREATE TABLE `db_shortwatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_shortwatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_shortwatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_shortwatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

ALTER TABLE `db_servers` ADD `total_short` INT(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `total_video`;
ALTER TABLE `db_servers` ADD `upload_s_file` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `upload_v_thumb`, ADD `upload_s_thumb` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `upload_s_file`;
ALTER TABLE `db_packtypes` ADD `pk_slimit` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `pk_vlimit`;

ALTER TABLE `db_accountuser` ADD `usr_s_count` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `usr_v_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_fname`;
ALTER TABLE `db_accountuser` DROP INDEX `oauth_provider`;
ALTER TABLE `db_accountuser` DROP INDEX `oauth_uid`;
ALTER TABLE `db_accountuser` DROP INDEX `oauth_password`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_mail_chanfollow`;
ALTER TABLE `db_accountuser` DROP INDEX `ch_lastview`;
ALTER TABLE `db_accountuser` DROP INDEX `affiliate_email`;
ALTER TABLE `db_accountuser` DROP INDEX `affiliate_badge`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_v_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_i_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_a_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_d_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_b_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_l_count`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_followcount`;
ALTER TABLE `db_accountuser` DROP INDEX `usr_live`;
ALTER TABLE `db_accountuser` ADD `usr_last_short` INT(10) UNSIGNED NULL DEFAULT '0' AFTER `usr_IP`;
ALTER TABLE `db_accountuser` CHANGE `usr_IP` `usr_IP` VARCHAR(48) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;

INSERT INTO `db_settings` (`cfg_name`, `cfg_data`, `cfg_info`) VALUES
('short_limit', '1000', 'backend: maximum size of uploaded video shorts'),
('short_file_types', '3gp,3gpp,asf,avi,dat,flv,mov,mpg,mpeg,mp4,mkv,m4v,rm,wmv', 'backend: allowed video short formats'),
('conversion_short_previews', '0', 'backend: enable/disable video shorts previews'),
('conversion_short_que', '1', 'backend: enable/video video shorts conversion que'),
('log_short_conversion', '1', 'backend: video shorts conversion logging'),
('conversion_source_short', '0', 'backend: store or delete original uploaded video shorts files'),
('sitemap_global_short', '1', 'backend: global sitemap include options'),
('sitemap_global_short_pl', '1', 'backend: global sitemap include options'),
('sitemap_short_max', '45000', 'backend: video shorts sitemap limit'),
('sitemap_short_src', 'player', 'backend: video shorts sitemap source (location or player)'),
('last_short_sitemap', '0', 'frontend: video shorts sitemap, last video short'),
('pause_short_transfer', '1', 'backend: pause or resume video short transfers'),
('short_player', 'vjs', 'backend: setting to control which video player is used for shorts'),
('guest_view_short', '1', 'backend: guest account access, view shorts page');



UPDATE `db_languages` SET `lang_flag` = 'flag-icon-us' WHERE `lang_id` = 'en_US';
ALTER TABLE `db_languages` DROP INDEX `lang_flag`;
ALTER TABLE `db_languages` DROP INDEX `lang_name`;

INSERT INTO `db_settings` (`id`, `cfg_name`, `cfg_data`, `cfg_info`) VALUES (NULL, 'new_layout', '1', 'backend: enable/disable new layout menu');
INSERT INTO `db_settings` (`id`, `cfg_name`, `cfg_data`, `cfg_info`) VALUES (NULL, 'short_module', '1', 'backend: enable/disable video shorts');
INSERT INTO `db_settings` (`id`, `cfg_name`, `cfg_data`, `cfg_info`) VALUES (NULL, 'short_uploads', '1', 'backend: enable/disable video shorts uploads');

UPDATE `db_settings` SET `cfg_data` = '0' WHERE `db_settings`.`cfg_name` = 'affiliate_module';
UPDATE `db_settings` SET `cfg_data` = '1' WHERE `db_settings`.`cfg_name` = 'thumbs_nr';
UPDATE `db_settings` SET `cfg_data` = 'rand' WHERE `db_settings`.`cfg_name` = 'thumbs_method';
UPDATE `db_settings` SET `cfg_data` = 'auto' WHERE `db_settings`.`cfg_name` = 'upload_category';

ALTER TABLE `db_accountuser` ADD `usr_profileinc` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `usr_tokencount`;
ALTER TABLE `db_accountuser` DROP INDEX `uni`, ADD UNIQUE `uni` (`usr_user`) USING BTREE;
ALTER TABLE `db_accountuser` ADD `ch_photos_nr` SMALLINT(5) UNSIGNED NOT NULL DEFAULT '0' AFTER `ch_photos`;
ALTER TABLE `db_accountuser` CHANGE `usr_theme` `usr_theme` TINYTEXT NOT NULL DEFAULT 'light';
UPDATE `db_accountuser` SET `usr_theme`='light', `usr_affiliate`='0';

ALTER TABLE `db_categories` DROP INDEX `ct_featured`;
ALTER TABLE `db_categories` DROP INDEX `ct_name`;
ALTER TABLE `db_categories` ADD INDEX `tfa` (`ct_type`, `ct_featured`, `ct_active`);
ALTER TABLE `db_categories` ADD `ct_index` SMALLINT(4) UNSIGNED NOT NULL DEFAULT '0' AFTER `sub_id`;

ALTER TABLE `db_useractivity` ADD `usr_id_to` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `usr_id`;
ALTER TABLE `db_useractivity` ADD INDEX `usr_id_to` (`usr_id_to`);
ALTER TABLE `db_useractivity` DROP INDEX `act_visible`;
ALTER TABLE `db_useractivity` DROP INDEX `act_deleted`;
ALTER TABLE `db_useractivity` ADD INDEX `uuaa` (`usr_id`, `usr_id_to`, `act_visible`, `act_deleted`);
ALTER TABLE `db_useractivity` CHANGE `act_ip` `act_ip` VARCHAR(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;

ALTER TABLE `db_usercontacts` CHANGE `ct_block_cfg` `ct_block_cfg` VARCHAR(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;
ALTER TABLE `db_usercodes` CHANGE `type` `type` VARCHAR(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL, CHANGE `pwd_id` `pwd_id` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;

CREATE TABLE `db_notifications_count` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `act_id` INT(10) UNSIGNED NULL DEFAULT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_notifications_count` ADD `nr` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `usr_id`;
ALTER TABLE `db_notifications_count` ADD INDEX `ua` (`usr_id`, `act_id`);
ALTER TABLE `db_notifications_count` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_notifications_count` ADD UNIQUE `uni` (`usr_id`);

UPDATE `db_settings` SET `cfg_data`='1' WHERE `cfg_name`='log_pmessage' LIMIT 1;
UPDATE `db_settings` SET `cfg_data`='1' WHERE `cfg_name`='log_frinvite' LIMIT 1;
UPDATE `db_trackactivity` SET `log_pmessage`='1', `log_frinvite`='1';

RENAME TABLE `db_followers` TO `db_followers_old`;
RENAME TABLE `db_subscribers` TO `db_subscribers_old`;

CREATE TABLE `db_followers` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `sub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sub_time` datetime NOT NULL,
  `sub_type` tinytext NOT NULL DEFAULT 'all',
  `mail_new_uploads` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

ALTER TABLE `db_followers`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`sub_id`) USING BTREE,
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `umail` (`usr_id`,`mail_new_uploads`),
  ADD KEY `us` (`usr_id`,`sub_id`);

ALTER TABLE `db_followers` MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `db_subscribers` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `sub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sub_time` datetime NOT NULL,
  `sub_type` tinytext NOT NULL DEFAULT 'all',
  `mail_new_uploads` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

ALTER TABLE `db_subscribers`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`sub_id`) USING BTREE,
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `umail` (`usr_id`,`mail_new_uploads`),
  ADD KEY `us` (`usr_id`,`sub_id`);

ALTER TABLE `db_subscribers` MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `db_videofiles` DROP INDEX `old_key`;
ALTER TABLE `db_videofiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_videofiles` DROP INDEX `privacy`;
ALTER TABLE `db_videofiles` DROP INDEX `has_preview`;
ALTER TABLE `db_videofiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_videofiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_videofiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_videofiles` DROP INDEX `stream_server`;
ALTER TABLE `db_videofiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_videofiles` DROP INDEX `file_like`;
ALTER TABLE `db_videofiles` DROP INDEX `file_responses`;
ALTER TABLE `db_videofiles` DROP INDEX `file_comments`;
ALTER TABLE `db_videofiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_videofiles` DROP INDEX `file_type`;
ALTER TABLE `db_videofiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_videofiles` DROP INDEX `upload_server`;

ALTER TABLE `db_livefiles` DROP INDEX `old_key`;
ALTER TABLE `db_livefiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_livefiles` DROP INDEX `privacy`;
ALTER TABLE `db_livefiles` DROP INDEX `has_preview`;
ALTER TABLE `db_livefiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_livefiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_livefiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_livefiles` DROP INDEX `stream_server`;
ALTER TABLE `db_livefiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_livefiles` DROP INDEX `file_like`;
ALTER TABLE `db_livefiles` DROP INDEX `file_responses`;
ALTER TABLE `db_livefiles` DROP INDEX `file_comments`;
ALTER TABLE `db_livefiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_livefiles` DROP INDEX `file_type`;
ALTER TABLE `db_livefiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_livefiles` DROP INDEX `upload_server`;
ALTER TABLE `db_livefiles` ADD `mail_sent` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `file_key`;

ALTER TABLE `db_imagefiles` DROP INDEX `old_key`;
ALTER TABLE `db_imagefiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_imagefiles` DROP INDEX `privacy`;
ALTER TABLE `db_imagefiles` DROP INDEX `has_preview`;
ALTER TABLE `db_imagefiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_imagefiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_imagefiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_imagefiles` DROP INDEX `stream_server`;
ALTER TABLE `db_imagefiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_imagefiles` DROP INDEX `file_like`;
ALTER TABLE `db_imagefiles` DROP INDEX `file_responses`;
ALTER TABLE `db_imagefiles` DROP INDEX `file_comments`;
ALTER TABLE `db_imagefiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_imagefiles` DROP INDEX `file_type`;
ALTER TABLE `db_imagefiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_imagefiles` DROP INDEX `upload_server`;

ALTER TABLE `db_audiofiles` DROP INDEX `old_key`;
ALTER TABLE `db_audiofiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_audiofiles` DROP INDEX `privacy`;
ALTER TABLE `db_audiofiles` DROP INDEX `has_preview`;
ALTER TABLE `db_audiofiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_audiofiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_audiofiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_audiofiles` DROP INDEX `stream_server`;
ALTER TABLE `db_audiofiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_audiofiles` DROP INDEX `file_like`;
ALTER TABLE `db_audiofiles` DROP INDEX `file_responses`;
ALTER TABLE `db_audiofiles` DROP INDEX `file_comments`;
ALTER TABLE `db_audiofiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_audiofiles` DROP INDEX `file_type`;
ALTER TABLE `db_audiofiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_audiofiles` DROP INDEX `upload_server`;

ALTER TABLE `db_docfiles` DROP INDEX `old_key`;
ALTER TABLE `db_docfiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_docfiles` DROP INDEX `privacy`;
ALTER TABLE `db_docfiles` DROP INDEX `has_preview`;
ALTER TABLE `db_docfiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_docfiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_docfiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_docfiles` DROP INDEX `stream_server`;
ALTER TABLE `db_docfiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_docfiles` DROP INDEX `file_like`;
ALTER TABLE `db_docfiles` DROP INDEX `file_responses`;
ALTER TABLE `db_docfiles` DROP INDEX `file_comments`;
ALTER TABLE `db_docfiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_docfiles` DROP INDEX `file_type`;
ALTER TABLE `db_docfiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_docfiles` DROP INDEX `upload_server`;

ALTER TABLE `db_blogfiles` DROP INDEX `old_key`;
ALTER TABLE `db_blogfiles` DROP INDEX `old_file_key`;
ALTER TABLE `db_blogfiles` DROP INDEX `privacy`;
ALTER TABLE `db_blogfiles` DROP INDEX `has_preview`;
ALTER TABLE `db_blogfiles` DROP INDEX `stream_vod`;
ALTER TABLE `db_blogfiles` DROP INDEX `stream_chat`;
ALTER TABLE `db_blogfiles` DROP INDEX `stream_key_active`;
ALTER TABLE `db_blogfiles` DROP INDEX `stream_server`;
ALTER TABLE `db_blogfiles` DROP INDEX `file_dislike`;
ALTER TABLE `db_blogfiles` DROP INDEX `file_like`;
ALTER TABLE `db_blogfiles` DROP INDEX `file_responses`;
ALTER TABLE `db_blogfiles` DROP INDEX `file_comments`;
ALTER TABLE `db_blogfiles` DROP INDEX `file_favorite`;
ALTER TABLE `db_blogfiles` DROP INDEX `file_type`;
ALTER TABLE `db_blogfiles` DROP INDEX `thumb_server`;
ALTER TABLE `db_blogfiles` DROP INDEX `upload_server`;

ALTER TABLE `db_videofiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);
ALTER TABLE `db_livefiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);
ALTER TABLE `db_imagefiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);
ALTER TABLE `db_audiofiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);
ALTER TABLE `db_docfiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);
ALTER TABLE `db_blogfiles` ADD INDEX `usrv_tsrv` (`upload_server`, `thumb_server`);

ALTER TABLE `db_videofiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_videofiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);
ALTER TABLE `db_livefiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_livefiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);
ALTER TABLE `db_imagefiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_imagefiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);
ALTER TABLE `db_audiofiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_audiofiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);
ALTER TABLE `db_docfiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_docfiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);
ALTER TABLE `db_blogfiles` ADD INDEX `upada` (`usr_id`, `privacy`, `approved`, `deleted`, `active`);
ALTER TABLE `db_blogfiles` ADD INDEX `ufpa` (`usr_id`, `file_views`, `privacy`, `active`);

RENAME TABLE `db_videohistory` TO `db_videohistory_old`;
CREATE TABLE `db_videohistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_videohistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_videohistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_videohistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_videohistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_livehistory` TO `db_livehistory_old`;
CREATE TABLE `db_livehistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_livehistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_livehistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_livehistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_livehistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_audiohistory` TO `db_audiohistory_old`;
CREATE TABLE `db_audiohistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_audiohistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_audiohistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_audiohistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_audiohistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_imagehistory` TO `db_imagehistory_old`;
CREATE TABLE `db_imagehistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_imagehistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_imagehistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_imagehistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_imagehistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_dochistory` TO `db_dochistory_old`;
CREATE TABLE `db_dochistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_dochistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_dochistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_dochistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_dochistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_bloghistory` TO `db_bloghistory_old`;
CREATE TABLE `db_bloghistory` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_bloghistory` ADD `views` MEDIUMINT UNSIGNED NULL DEFAULT NULL AFTER `file_key`;
ALTER TABLE `db_bloghistory` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_bloghistory` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_bloghistory` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_videofavorites` TO `db_videofavorites_old`;
CREATE TABLE `db_videofavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_videofavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_videofavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_videofavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_livefavorites` TO `db_livefavorites_old`;
CREATE TABLE `db_livefavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_livefavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_livefavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_livefavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_audiofavorites` TO `db_audiofavorites_old`;
CREATE TABLE `db_audiofavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_audiofavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_audiofavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_audiofavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_imagefavorites` TO `db_imagefavorites_old`;
CREATE TABLE `db_imagefavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_imagefavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_imagefavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_imagefavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_docfavorites` TO `db_docfavorites_old`;
CREATE TABLE `db_docfavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_docfavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_docfavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_docfavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_blogfavorites` TO `db_blogfavorites_old`;
CREATE TABLE `db_blogfavorites` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_blogfavorites` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_blogfavorites` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_blogfavorites` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_videoliked` TO `db_videoliked_old`;
CREATE TABLE `db_videoliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_videoliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_videoliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_videoliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_liveliked` TO `db_liveliked_old`;
CREATE TABLE `db_liveliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_liveliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_liveliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_liveliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_audioliked` TO `db_audioliked_old`;
CREATE TABLE `db_audioliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_audioliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_audioliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_audioliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_imageliked` TO `db_imageliked_old`;
CREATE TABLE `db_imageliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_imageliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_imageliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_imageliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_docliked` TO `db_docliked_old`;
CREATE TABLE `db_docliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_docliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_docliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_docliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_blogliked` TO `db_blogliked_old`;
CREATE TABLE `db_blogliked` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_blogliked` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_blogliked` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_blogliked` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_videowatchlist` TO `db_videowatchlist_old`;
CREATE TABLE `db_videowatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_videowatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_videowatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_videowatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_livewatchlist` TO `db_livewatchlist_old`;
CREATE TABLE `db_livewatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_livewatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_livewatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_livewatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_audiowatchlist` TO `db_audiowatchlist_old`;
CREATE TABLE `db_audiowatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_audiowatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_audiowatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_audiowatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_imagewatchlist` TO `db_imagewatchlist_old`;
CREATE TABLE `db_imagewatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_imagewatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_imagewatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_imagewatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_docwatchlist` TO `db_docwatchlist_old`;
CREATE TABLE `db_docwatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_docwatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_docwatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_docwatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_blogwatchlist` TO `db_blogwatchlist_old`;
CREATE TABLE `db_blogwatchlist` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_blogwatchlist` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_blogwatchlist` ADD UNIQUE `uni` (`usr_id`, `file_key`);
ALTER TABLE `db_blogwatchlist` ADD INDEX `uf` (`usr_id`, `file_key`);

RENAME TABLE `db_videoresponses` TO `db_videoresponses_old`;
CREATE TABLE `db_videoresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_videoresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_videoresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_videoresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_videoresponses` ADD INDEX `fa` (`file_key`, `active`);

RENAME TABLE `db_liveresponses` TO `db_liveresponses_old`;
CREATE TABLE `db_liveresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_liveresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_liveresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_liveresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_liveresponses` ADD INDEX `fa` (`file_key`, `active`);

RENAME TABLE `db_imageresponses` TO `db_imageresponses_old`;
CREATE TABLE `db_imageresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_imageresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_imageresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_imageresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_imageresponses` ADD INDEX `fa` (`file_key`, `active`);

RENAME TABLE `db_audioresponses` TO `db_audioresponses_old`;
CREATE TABLE `db_audioresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_audioresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_audioresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_audioresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_audioresponses` ADD INDEX `fa` (`file_key`, `active`);

RENAME TABLE `db_docresponses` TO `db_docresponses_old`;
CREATE TABLE `db_docresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_docresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_docresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_docresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_docresponses` ADD INDEX `fa` (`file_key`, `active`);

RENAME TABLE `db_blogresponses` TO `db_blogresponses_old`;
CREATE TABLE `db_blogresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_blogresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_blogresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_blogresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_blogresponses` ADD INDEX `fa` (`file_key`, `active`);

RENAME TABLE `db_shortresponses` TO `db_shortresponses_old`;
CREATE TABLE `db_shortresponses` ( `db_id` INT(10) UNSIGNED NULL DEFAULT NULL AUTO_INCREMENT , `usr_id` INT(10) UNSIGNED NULL DEFAULT NULL , `file_key` INT(10) UNSIGNED NULL DEFAULT NULL , `file_response` INT(10) UNSIGNED NULL DEFAULT NULL , `datetime` DATETIME NOT NULL , `active` TINYINT(1) UNSIGNED NULL DEFAULT '0' , PRIMARY KEY (`db_id`)) ENGINE = InnoDB;
ALTER TABLE `db_shortresponses` ADD UNIQUE `uni` (`file_key`, `file_response`);
ALTER TABLE `db_shortresponses` ADD INDEX `u` (`usr_id`);
ALTER TABLE `db_shortresponses` ADD INDEX `uf` (`usr_id`, `file_key`);
ALTER TABLE `db_shortresponses` ADD INDEX `fa` (`file_key`, `active`);


ALTER TABLE `db_videofiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);
ALTER TABLE `db_livefiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);
ALTER TABLE `db_imagefiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);
ALTER TABLE `db_audiofiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);
ALTER TABLE `db_docfiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);
ALTER TABLE `db_blogfiles` DROP INDEX `file_title`, ADD FULLTEXT `file_title` (`file_title`);

ALTER TABLE `db_videofiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;
ALTER TABLE `db_livefiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;
ALTER TABLE `db_imagefiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;
ALTER TABLE `db_audiofiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;
ALTER TABLE `db_docfiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;
ALTER TABLE `db_blogfiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;
ALTER TABLE `db_shortfiles` ADD `file_hash` VARCHAR(32) NOT NULL AFTER `file_name`;

ALTER TABLE `db_videofiles` ADD INDEX `fh` (`file_hash`);
ALTER TABLE `db_livefiles` ADD INDEX `fh` (`file_hash`);
ALTER TABLE `db_imagefiles` ADD INDEX `fh` (`file_hash`);
ALTER TABLE `db_audiofiles` ADD INDEX `fh` (`file_hash`);
ALTER TABLE `db_docfiles` ADD INDEX `fh` (`file_hash`);
ALTER TABLE `db_blogfiles` ADD INDEX `fh` (`file_hash`);
ALTER TABLE `db_shortfiles` ADD INDEX `fh` (`file_hash`);


ALTER TABLE `db_messaging` ADD `msg_short_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `msg_video_attch`;
ALTER TABLE `db_messaging` CHANGE `msg_reply_to` `msg_reply_to` INT(10) UNSIGNED NOT NULL DEFAULT '0', CHANGE `msg_video_attch` `msg_video_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0', CHANGE `msg_image_attch` `msg_image_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0', CHANGE `msg_audio_attch` `msg_audio_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0', CHANGE `msg_doc_attch` `msg_doc_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0', CHANGE `msg_blog_attch` `msg_blog_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0', CHANGE `msg_live_attch` `msg_live_attch` INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `db_messaging` DROP INDEX `msg_video_attch`;
ALTER TABLE `db_messaging` DROP INDEX `msg_image_attch`;
ALTER TABLE `db_messaging` DROP INDEX `msg_audio_attch`;
ALTER TABLE `db_messaging` DROP INDEX `msg_doc_attch`;
ALTER TABLE `db_messaging` DROP INDEX `msg_blog_attch`;
ALTER TABLE `db_messaging` DROP INDEX `msg_live_attch`;
ALTER TABLE `db_messaging` DROP INDEX `msg_subj`;


ALTER TABLE `db_packusers` ADD `pk_total_short` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `pk_total_video`;
ALTER TABLE `db_packusers` DROP INDEX `pk_total_live`;
ALTER TABLE `db_packtypes` DROP INDEX `pk_llimit`;
ALTER TABLE `db_mailque` CHANGE `mail_to` `mail_to` MEDIUMTEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL;
ALTER TABLE `db_trackactivity` ADD `log_responding` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `share_following`, ADD `share_responding` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' AFTER `log_responding`;
ALTER TABLE `db_trackactivity` DROP INDEX `log_following`;
INSERT INTO `db_settings` (`id`, `cfg_name`, `cfg_data`, `cfg_info`) VALUES (NULL, 'log_responding', '1', 'backend: enable/disable logging of response actions');



ALTER TABLE `db_videofiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `db_livefiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `db_imagefiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `db_audiofiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `db_docfiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `db_blogfiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `db_shortfiles` CHANGE `embed_key` `embed_key` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `db_videofiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;
ALTER TABLE `db_livefiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;
ALTER TABLE `db_imagefiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;
ALTER TABLE `db_audiofiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;
ALTER TABLE `db_docfiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;
ALTER TABLE `db_blogfiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;
ALTER TABLE `db_shortfiles` ADD `thumb_cache` INT(3) UNSIGNED NOT NULL DEFAULT '1' AFTER `file_key`;

ALTER TABLE `db_videocomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_livecomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_imagecomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_audiocomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_doccomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_blogcomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_channelcomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;
ALTER TABLE `db_shortcomments` CHANGE `c_body` `c_body` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;

ALTER TABLE `db_videocomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_videocomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_livecomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_livecomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_imagecomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_imagecomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_audiocomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_audiocomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_doccomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_doccomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_blogcomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_blogcomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_channelcomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_channelcomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;
ALTER TABLE `db_shortcomments` ADD `c_edited` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_approved`;
ALTER TABLE `db_shortcomments` ADD `c_edittime` DATETIME NOT NULL AFTER `c_edited`;

ALTER TABLE `db_videocomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_livecomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_imagecomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_audiocomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_doccomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_blogcomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_channelcomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;
ALTER TABLE `db_shortcomments` ADD `c_pinned` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `c_edittime`;

ALTER TABLE `db_videocomments` ADD INDEX `ms` (`file_key`, `c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_livecomments` ADD INDEX `ms` (`file_key`, `c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_imagecomments` ADD INDEX `ms` (`file_key`, `c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_audiocomments` ADD INDEX `ms` (`file_key`, `c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_doccomments` ADD INDEX `ms` (`file_key`, `c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_blogcomments` ADD INDEX `ms` (`file_key`, `c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_channelcomments` ADD INDEX `ms` (`c_usr_id`, `c_replyto`, `c_active`);
ALTER TABLE `db_shortcomments` ADD INDEX `ms` (`c_usr_id`, `c_replyto`, `c_active`);

ALTER TABLE `db_videocomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_livecomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_imagecomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_audiocomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_doccomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_blogcomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_channelcomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `db_shortcomments` CHANGE `c_spam` `c_spam` TEXT CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `db_usercontacts` CHANGE `pwd_id` `pwd_id` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL, CHANGE `ct_name` `ct_name` VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL, CHANGE `ct_username` `ct_username` VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL, CHANGE `ct_email` `ct_email` VARCHAR(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL;

ALTER TABLE `db_livechat` ADD `first` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `badge`;
ALTER TABLE `db_livechat` ADD `usr_profileinc` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `badge`;
ALTER TABLE `db_livechat` ADD INDEX `ft` (`channel_owner`, `chat_user`);
ALTER TABLE `db_livechat` CHANGE `chat_id` `chat_id` VARCHAR(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `channel_owner` `channel_owner` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_user` `chat_user` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_display` `chat_display` VARCHAR(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_ip` `chat_ip` VARCHAR(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL, CHANGE `chat_fp` `chat_fp` VARCHAR(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL;

ALTER TABLE `db_liveviewers` DROP INDEX `int`;
ALTER TABLE `db_liveviewers` DROP INDEX `ts`;
ALTER TABLE `db_liveviewers` DROP INDEX `file_key`;
ALTER TABLE `db_liveviewers` ADD INDEX `fl` (`file_key`, `longip`);
ALTER TABLE `db_liveviewers` ADD INDEX `ft` (`file_key`, `ts`);

ALTER TABLE `db_accountuser` ADD `usr_live` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `usr_key`, ADD INDEX (`usr_live`);
ALTER TABLE `db_accountuser` ADD `live_key` VARCHAR(254) NOT NULL AFTER `usr_key`;
ALTER TABLE `db_livefiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;
ALTER TABLE `db_videofiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;
ALTER TABLE `db_imagefiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;
ALTER TABLE `db_audiofiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;
ALTER TABLE `db_docfiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;
ALTER TABLE `db_blogfiles` ADD `stream_key_old` VARCHAR(255) NOT NULL AFTER `stream_chat`;
