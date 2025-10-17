SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Table structure for table `db_accountuser`
--

CREATE TABLE `db_accountuser` (
  `usr_id` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `live_key` varchar(254) NOT NULL,
  `usr_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_usr_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(15) NOT NULL,
  `oauth_provider` varchar(32) NOT NULL,
  `oauth_uid` varchar(255) NOT NULL,
  `usr_user` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_password` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_IP` varchar(48) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_last_short` int(10) UNSIGNED DEFAULT 0,
  `usr_v_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_s_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_l_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_i_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_a_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_d_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_b_count` int(10) UNSIGNED NOT NULL,
  `usr_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_partner` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `partner_date` datetime NOT NULL,
  `usr_affiliate` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `affiliate_date` datetime NOT NULL,
  `affiliate_pay_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `affiliate_custom` varchar(255) NOT NULL,
  `affiliate_email` varchar(64) NOT NULL,
  `affiliate_badge` varchar(64) NOT NULL,
  `affiliate_maps_key` varchar(48) NOT NULL,
  `usr_sub_email` varchar(64) NOT NULL,
  `usr_sub_share` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_sub_perc` tinyint(3) NOT NULL DEFAULT 50,
  `usr_sub_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `usr_free_sub` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_weekupdates` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_emailextras` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_role` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_logins` int(10) UNSIGNED NOT NULL,
  `usr_lastlogin` datetime NOT NULL,
  `usr_joindate` datetime NOT NULL,
  `usr_perm` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_menuaccess` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_verified` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `usr_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_followcount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_subcount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_tokencount` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_profileinc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_mail_filecomment` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `usr_mail_chancomment` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `usr_mail_privmessage` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `usr_mail_friendinv` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `usr_mail_chansub` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `usr_mail_chanfollow` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `oauth_password` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_theme` tinytext NOT NULL DEFAULT 'light',
  `usr_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_website` varchar(48) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_fname` varchar(24) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_lname` varchar(24) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_dname` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_photo` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_phone` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_fax` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_town` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_city` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_zip` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_country` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_birthday` date NOT NULL,
  `usr_gender` varchar(32) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `usr_relation` varchar(32) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL,
  `usr_showage` tinyint(1) UNSIGNED NOT NULL,
  `usr_occupations` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_companies` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_schools` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_interests` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_movies` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_music` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_books` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_del_reason` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `fb_id` bigint(20) UNSIGNED NOT NULL,
  `ch_user` text NOT NULL,
  `ch_dname` text NOT NULL,
  `ch_title` text NOT NULL,
  `ch_descr` text NOT NULL,
  `ch_tags` text NOT NULL,
  `ch_influences` text NOT NULL,
  `ch_style` text NOT NULL,
  `ch_type` smallint(5) NOT NULL,
  `ch_views` int(10) NOT NULL,
  `home_cfg` text NOT NULL,
  `ch_lastview` date NOT NULL,
  `ch_cfg` text NOT NULL,
  `ch_photos` text NOT NULL,
  `ch_photos_nr` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `ch_links` text NOT NULL,
  `ch_pfields` text NOT NULL,
  `ch_custom_fields` text NOT NULL,
  `ch_positions` text NOT NULL,
  `ch_channels` text NOT NULL,
  `ch_rownum` text NOT NULL,
  `chat_temp` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_advbanners`
--

CREATE TABLE `db_advbanners` (
  `adv_id` int(10) UNSIGNED NOT NULL,
  `adv_name` varchar(100) NOT NULL,
  `adv_type` varchar(12) NOT NULL DEFAULT 'shared',
  `adv_description` tinytext NOT NULL,
  `adv_group` smallint(4) UNSIGNED NOT NULL,
  `adv_code` text NOT NULL,
  `adv_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_advgroups`
--

CREATE TABLE `db_advgroups` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `adv_name` varchar(50) NOT NULL,
  `adv_description` tinytext NOT NULL,
  `adv_width` varchar(7) NOT NULL,
  `adv_height` varchar(7) NOT NULL,
  `adv_class` text NOT NULL,
  `adv_style` text NOT NULL,
  `adv_rotate` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `adv_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_advgroups`
--

INSERT INTO `db_advgroups` (`db_id`, `adv_name`, `adv_description`, `adv_width`, `adv_height`, `adv_class`, `adv_style`, `adv_rotate`, `adv_active`) VALUES
(1, 'home_promoted_top', 'Homepage: promoted top position, rectangle (728 x 90)', '100%', '90', '', 'margin: 0 auto;', 1, 1),
(2, 'home_promoted_bottom', 'Homepage: promoted bottom position, rectangle (728 x 90)', '100%', '90', '', 'margin: 0 auto;', 1, 1),
(3, 'home_middle_top', 'Homepage: top middle position (728 x 90)', '100%', '90', '', 'margin: 0 auto;', 1, 1),
(4, 'home_middle_bottom', 'Homepage: bottom middle position (728 x 90)', '100%', '90', '', 'margin: 0 auto;', 1, 1),
(7, 'browse_chan_left_top', 'Browse channels: top left position (180 x 150)', '100%', '150', '', '', 1, 1),
(8, 'browse_chan_left_bottom', 'Browse channels: bottom left position (168 x 600)', '100%', '600', '', '', 1, 1),
(9, 'browse_chan_promoted_top', 'Browse channels: top middle position (940 x 90)', '100%', '90', '', '', 1, 1),
(10, 'browse_chan_promoted_bottom', 'Browse channels: bottom middle position (940 x 90)', '100%', '90', '', '', 1, 1),
(11, 'browse_chan_main_top', 'Browse channels: top right position (940 x 90)', '100%', '90', '', '', 1, 1),
(12, 'browse_chan_main_bottom', 'Browse channels: bottom right position (940 x 90)', '100%', '90', '', '', 1, 1),
(13, 'browse_files_left_top', 'Browse files, left top (180 x 150)', '100%', '150', '', '', 1, 1),
(14, 'browse_files_left_mid', 'Browse files, left mid (168 x 600)', '100%', '600', '', '', 1, 1),
(15, 'browse_files_left_bottom', 'Browse files, left bottom (168 x 600)', '100%', '600', '', '', 1, 1),
(16, 'view_files_player_top', 'View files, above player (728 x 90)', '100%', '90', '', 'margin-bottom: 10px;', 1, 1),
(17, 'view_files_player_bottom1', 'View files, below player (728 x 90)', '100%', '90', '', '', 1, 1),
(18, 'view_files_player_bottom2', 'View files, below player (below details, 728 x 90)', '100%', '90', '', '', 1, 1),
(19, 'view_files_player_bottom3', 'View files, below player (below comments, 728 x 90)', '100%', '90', '', '', 1, 1),
(20, 'view_files_right_top', 'View files, top right position (180 x 150)', '100%', '150', '', '', 1, 1),
(21, 'view_comm_left_top', 'View comments, top left position (970 x 90)', '100%', '90', '', '', 1, 1),
(22, 'view_comm_left_bottom', 'View comments, bottom left position (970 x 90)', '100%', '90', '', '', 1, 1),
(23, 'view_comm_right_top', 'View comments, top right position (970 x 90)', '100%', '90', '', '', 1, 1),
(24, 'view_comm_right_bottom', 'View comments, bottom right position (970 x 90)', '100%', '90', '', '', 1, 1),
(25, 'view_resp_left_top', 'View responses, top left position (970 x 90)', '100%', '90', '', '', 1, 1),
(26, 'view_resp_left_bottom', 'View responses, bottom left position (970 x 90)', '100%', '90', '', '', 1, 1),
(27, 'view_resp_right_top', 'View responses, top right position (970 x 90)', '100%', '90', '', '', 1, 1),
(28, 'view_resp_right_bottom', 'View responses, bottom right position (970 x 90)', '100%', '90', '', '', 1, 1),
(29, 'view_pl_left_top', 'View playlists, top banner (970 x 90)', '100%', '90', '', '', 1, 1),
(30, 'view_pl_left_middle', 'View playlists, below thumbnail 300 x 100)', '100%', '100', '', '', 1, 1),
(31, 'view_pl_left_bottom', 'REMOVED\r\n\r\nView playlists, bottom left position, (300 x 100)\r\n\r\n', '100%', '100', '', '', 1, 0),
(32, 'view_pl_right_top', 'View playlists, above entries list (970 x 90)', '100%', '90', '', '', 1, 1),
(33, 'view_pl_right_middle', 'REMOVED\r\n\r\nView playlists, middle right position (160 x 600)', '100%', '600', '', '', 1, 0),
(34, 'view_pl_right_bottom', 'View playlists, below entries list (970 x 90)', '100%', '90', '', '', 1, 1),
(35, 'respond_top', 'New response, top position (940 x 90)', '100%', '90', '', '', 1, 1),
(36, 'respond_bottom', 'New response, bottom position (940 x 90)', '100%', '90', '', '', 1, 1),
(37, 'register_top', 'Registration page, top position (468 x 60)', '100%', '60', '', '', 1, 1),
(38, 'register_bottom', 'Registration page, bottom position (468 x 60)', '100%', '60', '', '', 1, 1),
(39, 'login_top', 'Login page, top position (468 x 60)', '100%', '60', '', '', 1, 1),
(40, 'login_bottom', 'Login page, bottom position (468 x 60)', '100%', '60', '', '', 1, 1),
(41, 'search_top', 'Search, top position (728 x 90)', '100%', '90', '', '', 1, 1),
(42, 'search_bottom', 'Search, bottom position (728 x 90)', '100%', '90', '', '', 1, 1),
(43, 'footer_top', 'Footer, above (728 x 90)', '100%', '90', '', 'padding-bottom: 10px; margin: 0 auto;', 1, 1),
(44, 'footer_bottom', 'Footer, below (728 x 90)', '100%', '90', '', 'margin: 0 auto;', 1, 1),
(45, 'browse_pl_main_top', 'Browse playlists, main left (940 x 90)', '100%', '90', '', '', 1, 1),
(46, 'browse_pl_main_bottom', 'Browse playlists, main bottom (940 x 90)', '100%', '90', '', '', 1, 1),
(49, 'view_files_right_bottom', 'View files, bottom right position (168 x 600)', '100%', '600', '', 'margin-left: 10px;', 1, 1),
(50, 'per_file_player_top', 'Per file, above player (728 x 90)', '100%', '90', '', 'margin-bottom: 10px;', 1, 1),
(51, 'per_file_player_bottom1', 'Per file, below player (728 x 90)', '100%', '90', '', '', 1, 1),
(52, 'per_file_player_bottom2', 'Per file, below player (below details, 728 x 90)', '100%', '90', '', '', 1, 1),
(53, 'per_file_player_bottom3', 'Per file, below player (below comments, 728 x 90)', '100%', '90', '', '', 1, 1),
(54, 'per_file_right_top', 'Per file, top right position (180 x 150)', '100%', '150', '', '', 1, 1),
(55, 'per_file_right_bottom', 'Per file, bottom right position (168 x 600)', '100%', '600', '', '', 1, 1),
(56, 'browse_files_promoted_top', 'Browse files, promoted list, top (940 x 90)', '100%', '90', '', '', 1, 1),
(57, 'browse_files_promoted_bottom', 'Browse files, promoted list, bottom (940 x 90)', '100%', '90', '', '', 1, 1),
(58, 'browse_files_main_top', 'Browse files, main listing, top (940 x 90)', '100%', '90', '', '', 1, 1),
(59, 'browse_files_main_bottom', 'Browse files, main listing, bottom (940 x 90)', '100%', '90', '', '', 1, 1),
(60, 'per_category_player_top', 'Per category, above player (728 x 90)', '100%', '90', '', 'margin-bottom: 10px;', 1, 1),
(61, 'per_category_player_bottom1', 'Per category, below player (728 x 90)', '100%', '90', '', '', 1, 1),
(62, 'per_category_player_bottom2', 'Per category, below player (below details, 728 x 90)', '100%', '90', '', '', 1, 1),
(63, 'per_category_player_bottom3', 'Per category, below player (below comments, 728 x 90)', '100%', '90', '', '', 1, 1),
(64, 'per_category_right_top', 'Per category, top right position (180 x 150)', '100%', '150', '', '', 1, 1),
(65, 'per_category_right_bottom', 'Per category, bottom right position (168 x 600)', '100%', '600', '', '', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_audiocomments`
--

CREATE TABLE `db_audiocomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiofavorites`
--

CREATE TABLE `db_audiofavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiofiles`
--

CREATE TABLE `db_audiofiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_short` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vjs_ads` text NOT NULL,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_started` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiohistory`
--

CREATE TABLE `db_audiohistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audioliked`
--

CREATE TABLE `db_audioliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiopayouts`
--

CREATE TABLE `db_audiopayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audioplaylists`
--

CREATE TABLE `db_audioplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audioque`
--

CREATE TABLE `db_audioque` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiorating`
--

CREATE TABLE `db_audiorating` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audioresponses`
--

CREATE TABLE `db_audioresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiosubs`
--

CREATE TABLE `db_audiosubs` (
  `sub_id` mediumint(6) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `vjs_subs` text NOT NULL,
  `jw_subs` text NOT NULL,
  `fp_subs` text NOT NULL,
  `sub_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiotransfers`
--

CREATE TABLE `db_audiotransfers` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `upload_start_time` datetime NOT NULL,
  `upload_end_time` datetime NOT NULL,
  `thumb_start_time` datetime NOT NULL,
  `thumb_end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_audiowatchlist`
--

CREATE TABLE `db_audiowatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_banlist`
--

CREATE TABLE `db_banlist` (
  `ban_id` int(10) UNSIGNED NOT NULL,
  `ban_ip` varchar(30) NOT NULL,
  `ban_descr` tinytext NOT NULL,
  `ban_start` datetime NOT NULL,
  `ban_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogcomments`
--

CREATE TABLE `db_blogcomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogfavorites`
--

CREATE TABLE `db_blogfavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogfiles`
--

CREATE TABLE `db_blogfiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_short` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_started` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_bloghistory`
--

CREATE TABLE `db_bloghistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogliked`
--

CREATE TABLE `db_blogliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogpayouts`
--

CREATE TABLE `db_blogpayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogplaylists`
--

CREATE TABLE `db_blogplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blograting`
--

CREATE TABLE `db_blograting` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogresponses`
--

CREATE TABLE `db_blogresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_blogwatchlist`
--

CREATE TABLE `db_blogwatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_categories`
--

CREATE TABLE `db_categories` (
  `ct_id` int(10) UNSIGNED NOT NULL,
  `sub_id` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `ct_index` smallint(4) UNSIGNED NOT NULL DEFAULT 0,
  `ct_name` varchar(40) NOT NULL,
  `ct_lang` varchar(255) NOT NULL,
  `ct_descr` tinytext NOT NULL,
  `ct_type` varchar(20) NOT NULL,
  `ct_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ct_slug` varchar(64) NOT NULL,
  `ct_menu` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `ct_icon` varchar(32) NOT NULL,
  `ct_ads` text NOT NULL,
  `ct_banners` text NOT NULL,
  `ct_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_categories`
--

INSERT INTO `db_categories` (`ct_id`, `sub_id`, `ct_index`, `ct_name`, `ct_lang`, `ct_descr`, `ct_type`, `ct_featured`, `ct_slug`, `ct_menu`, `ct_icon`, `ct_ads`, `ct_banners`, `ct_active`) VALUES
(23, 0, 0, 'Autos &amp; Vehicles', 'N;', 'Video category', 'live', 1, 'autos-and-vehicles', 1, 'icon-truck', '', '', 1),
(24, 0, 1, 'Comedy', 'N;', 'Video category', 'live', 1, 'comedy', 1, 'icon-happy', '', '', 1),
(25, 0, 2, 'Education', 'N;', 'Video category', 'live', 1, 'education', 1, 'icon-book', '', '', 1),
(26, 0, 0, 'Entertainment', 'N;', 'Video category', 'video', 1, 'entertainment', 1, 'icon-tv', '', '', 1),
(27, 0, 0, 'Film &amp; Animation', 'N;', 'Video category', 'video', 1, 'film-and-animation', 1, 'icon-film', '', '', 1),
(28, 0, 0, 'Gaming', 'N;', 'Video category', 'video', 1, 'gaming', 1, 'icon-play6', '', '', 1),
(29, 0, 0, 'Howto &amp; Style', 'N;', 'Video category', 'video', 1, 'howto-and-style', 1, 'icon-wand', '', '', 1),
(30, 0, 0, 'Music', 'N;', 'Video category', 'video', 1, 'music', 1, 'icon-music', '', '', 1),
(31, 0, 0, 'News &amp; Politics', 'N;', 'Video category', 'video', 1, 'news-and-politics', 1, 'icon-newspaper', '', '', 1),
(32, 0, 0, 'Nonprofits &amp; Activism', 'N;', 'Video category', 'video', 1, 'nonprofits-and-activism', 1, 'icon-globe', '', '', 1),
(33, 0, 0, 'People &amp; Blogs', 'N;', 'Video category', 'live', 1, 'people-and-blogs', 1, 'icon-user4', '', '', 1),
(34, 0, 0, 'Pets &amp; Animals', 'N;', 'Video category', 'video', 1, 'pets-and-animals', 1, 'icon-github2', '', '', 1),
(35, 0, 0, 'Science &amp; Technology', 'N;', 'Video category', 'video', 1, 'science-and-technology', 1, 'icon-lab', '', '', 1),
(36, 0, 0, 'Sports', 'N;', 'Video category', 'video', 1, 'sports', 1, 'icon-dribbble', '', '', 1),
(37, 0, 0, 'Travel &amp; Events', 'N;', 'Video category', 'video', 1, 'travel-and-events', 1, 'icon-airplane', '', '', 1),
(38, 0, 0, 'Food', 'N;', 'Image category', 'image', 0, 'food', 1, 'icon-food', '', '', 1),
(39, 0, 0, 'Landscapes', 'N;', 'Image category', 'image', 0, 'landscapes', 1, 'icon-images', '', '', 1),
(40, 0, 0, 'Travel', 'N;', 'Image category', 'image', 0, 'travel', 1, ' icon-airplane', '', '', 1),
(41, 0, 0, 'People', 'N;', 'Image category', 'image', 0, 'people', 1, 'icon-user4', '', '', 1),
(42, 0, 0, 'Animals', 'N;', 'Image category', 'image', 0, 'animals', 1, 'icon-github2', '', '', 1),
(43, 0, 0, 'Sports', 'N;', 'Image category', 'image', 0, 'sports', 1, 'icon-dribbble', '', '', 1),
(44, 0, 0, 'Technology', 'N;', 'Image category', 'image', 0, 'technology', 1, 'icon-powercord', '', '', 1),
(45, 0, 0, 'Lifestyle', 'N;', 'Image category', 'image', 0, 'lifestyle', 1, 'icon-quill', '', '', 1),
(46, 0, 0, 'Business', 'N;', 'Image category', 'image', 0, 'business', 1, 'icon-office', '', '', 1),
(47, 0, 0, 'Transport', 'N;', 'Image category', 'image', 0, 'transport', 1, 'icon-truck', '', '', 1),
(48, 0, 0, 'Science', 'N;', 'Image category', 'image', 0, 'science', 1, 'icon-lab', '', '', 1),
(49, 0, 0, 'Industry', 'N;', 'Image category', 'image', 0, 'industry', 1, 'icon-cogs4', '', '', 1),
(50, 0, 0, 'Games', 'N;', 'Image category', 'image', 0, 'games', 1, 'icon-target', '', '', 1),
(51, 0, 0, 'Art / Creative', 'N;', 'Image category', 'image', 0, 'art-creative', 1, 'icon-droplet', '', '', 1),
(52, 0, 0, 'Nature', 'N;', 'Image category', 'image', 0, 'nature', 1, 'icon-leaf', '', '', 1),
(53, 0, 0, 'Sci-Fi', 'N;', 'Image category', 'image', 0, 'sci-fi', 1, 'icon-rocket', '', '', 1),
(54, 0, 0, 'Alternative', '', 'Audio category', 'audio', 0, 'alternative', 1, 'icon-headphones', '', '', 1),
(55, 0, 0, 'Anime', 'N;', 'Audio category', 'audio', 0, 'anime', 0, 'icon-headphones', '', '', 1),
(56, 0, 0, 'Blues', '', 'Audio category', 'audio', 0, 'blues', 1, 'icon-headphones', '', '', 1),
(57, 0, 0, 'Children Music', 'N;', 'Audio category', 'audio', 0, 'children-music', 0, 'icon-headphones', '', '', 1),
(58, 0, 0, 'Classical', '', 'Audio category', 'audio', 0, 'classical', 1, 'icon-headphones', '', '', 1),
(59, 0, 0, 'Comedy', 'N;', 'Audio category', 'audio', 0, 'comedy', 0, 'icon-headphones', '', '', 1),
(60, 0, 0, 'Country', '', 'Audio category', 'audio', 0, 'country', 1, 'icon-headphones', '', '', 1),
(61, 0, 0, 'Dance / EDM', '', 'Audio category', 'audio', 0, 'dance-edm', 1, 'icon-headphones', '', '', 1),
(62, 0, 0, 'Disney', 'N;', 'Audio category', 'audio', 0, 'disney', 0, 'icon-headphones', '', '', 1),
(63, 0, 0, 'Easy Listening', 'N;', 'Audio category', 'audio', 0, 'easy-listening', 0, 'icon-headphones', '', '', 1),
(64, 0, 0, 'Electronic', '', 'Audio category', 'audio', 0, 'electronic', 1, 'icon-headphones', '', '', 1),
(65, 0, 0, 'Enka', 'N;', 'Audio category', 'audio', 0, 'enka', 0, 'icon-headphones', '', '', 1),
(66, 0, 0, 'French Pop', 'N;', 'Audio category', 'audio', 0, 'french-pop', 0, 'icon-headphones', '', '', 1),
(67, 0, 0, 'German Folk', 'N;', 'Audio category', 'audio', 0, 'german-folk', 0, 'icon-headphones', '', '', 1),
(68, 0, 0, 'German Pop', 'N;', 'Audio category', 'audio', 0, 'german-pop', 0, 'icon-headphones', '', '', 1),
(69, 0, 0, 'Fitness &amp; Workout', 'N;', 'Audio category', 'audio', 0, 'fitness-and-workout', 0, 'icon-headphones', '', '', 1),
(70, 0, 0, 'Hip-Hop/Rap', '', 'Audio category', 'audio', 0, 'hip-hop', 1, 'icon-headphones', '', '', 1),
(71, 0, 0, 'Holiday', 'N;', 'Audio category', 'audio', 0, 'holiday', 0, 'icon-headphones', '', '', 1),
(72, 0, 0, 'Indie Pop', 'N;', 'Audio category', 'audio', 0, 'indie-pop', 0, 'icon-headphones', '', '', 1),
(73, 0, 0, 'Industrial', 'N;', 'Audio category', 'audio', 0, 'industrial', 0, 'icon-headphones', '', '', 1),
(74, 0, 0, 'Christian &amp; Gospel', 'N;', 'Audio category', 'audio', 0, 'christian-and-gospel', 0, 'icon-headphones', '', '', 1),
(75, 0, 0, 'Instrumental', '', 'Audio category', 'audio', 0, 'instrumental', 1, 'icon-headphones', '', '', 1),
(76, 0, 0, 'J-Pop', 'N;', 'Audio category', 'audio', 0, 'j-pop', 0, 'icon-headphones', '', '', 1),
(77, 0, 0, 'Jazz', 'N;', 'Audio category', 'audio', 0, 'jazz', 1, 'icon-headphones', '', '', 1),
(78, 0, 0, 'K-Pop', 'N;', 'Audio category', 'audio', 0, 'k-pop', 0, 'icon-headphones', '', '', 1),
(79, 0, 0, 'Karaoke', 'N;', 'Audio category', 'audio', 0, 'karaoke', 0, 'icon-headphones', '', '', 1),
(80, 0, 0, 'Kayokyoku', 'N;', 'Audio category', 'audio', 0, 'kayokyoku', 0, 'icon-headphones', '', '', 1),
(81, 0, 0, 'Latino', '', 'Audio category', 'audio', 0, 'latino', 1, 'icon-headphones', '', '', 1),
(82, 0, 0, 'New Age', 'N;', 'Audio category', 'audio', 0, 'new-age', 0, 'icon-headphones', '', '', 1),
(83, 0, 0, 'Opera', '', 'Audio category', 'audio', 0, 'opera', 1, 'icon-headphones', '', '', 1),
(84, 0, 0, 'Pop', '', 'Audio category', 'audio', 0, 'pop', 1, 'icon-headphones', '', '', 1),
(85, 0, 0, 'R&B/Soul', '', 'Audio category', 'audio', 0, 'r-n-b', 1, 'icon-headphones', '', '', 1),
(86, 0, 0, 'Reggae', '', 'Audio category', 'audio', 0, 'reggae', 1, 'icon-headphones', '', '', 1),
(87, 0, 0, 'Rock', '', 'Audio category', 'audio', 0, 'rock', 1, 'icon-headphones', '', '', 1),
(88, 0, 0, 'Singer/Songwriter', 'N;', 'Audio category', 'audio', 0, 'singer-songwriter', 0, 'icon-headphones', '', '', 1),
(89, 0, 0, 'Soundtrack', '', 'Audio category', 'audio', 0, 'soundtrack', 1, 'icon-headphones', '', '', 1),
(90, 0, 0, 'Spoken Word', 'N;', 'Audio category', 'audio', 0, 'spoken-word', 0, 'icon-headphones', '', '', 1),
(91, 0, 0, 'Tex-Mex / Tejano', 'N;', 'Audio category', 'audio', 0, 'tex-mex-tejano', 0, 'icon-headphones', '', '', 1),
(92, 0, 0, 'Vocal', 'N;', 'Audio category', 'audio', 0, 'vocal', 0, 'icon-headphones', '', '', 1),
(93, 0, 0, 'World', 'N;', 'Audio category', 'audio', 0, 'world', 0, 'icon-headphones', '', '', 1),
(94, 0, 0, 'Books', 'N;', 'Document category', 'doc', 0, 'books', 1, 'icon-book', '', '', 1),
(95, 0, 0, 'Comic Books', 'N;', 'Document category', 'doc', 0, 'comic-books', 1, 'icon-book', '', '', 1),
(96, 0, 0, 'Documents', 'N;', 'Document category', 'doc', 0, 'documents', 1, 'icon-file-word', '', '', 1),
(97, 0, 0, 'Presentations', 'N;', 'Document category', 'doc', 0, 'presentations', 1, 'icon-file-powerpoint', '', '', 1),
(98, 0, 0, 'Music', 'N;', 'Blog category', 'blog', 0, 'music', 1, 'icon-music', '', '', 1),
(99, 0, 0, 'Fashion', 'N;', 'Blog category', 'blog', 0, 'fashion', 1, 'icon-quill', '', '', 1),
(100, 0, 0, 'Automotive', 'N;', 'Blog category', 'blog', 0, 'automotive', 1, 'icon-truck', '', '', 1),
(101, 0, 0, 'Real Estate', 'N;', 'Blog category', 'blog', 0, 'real-estate', 1, 'icon-home3', '', '', 1),
(102, 0, 0, 'Beauty', 'N;', 'Blog category', 'blog', 0, 'beauty', 1, 'icon-eye2', '', '', 1),
(103, 0, 0, 'Travel', 'N;', 'Blog category', 'blog', 0, 'travel', 1, 'icon-airplane', '', '', 1),
(104, 0, 0, 'Design', 'N;', 'Blog category', 'blog', 0, 'design', 1, 'icon-flip', '', '', 1),
(105, 0, 0, 'Food', 'N;', 'Blog category', 'blog', 0, 'food', 1, 'icon-food', '', '', 1),
(106, 0, 0, 'Health', 'N;', 'Blog category', 'blog', 0, 'health', 1, 'icon-aid', '', '', 1),
(107, 0, 0, 'Technology', 'N;', 'Blog category', 'blog', 0, 'technology', 1, 'icon-powercord', '', '', 1),
(108, 0, 0, 'Wedding', 'N;', 'Blog category', 'blog', 0, 'wedding', 1, 'icon-heart', '', '', 1),
(109, 0, 0, 'Movies', 'N;', 'Blog category', 'blog', 0, 'movies', 1, 'icon-film', '', '', 1),
(110, 0, 0, 'Photography', 'N;', 'Blog category', 'blog', 0, 'photography', 1, 'icon-camera', '', '', 1),
(111, 0, 0, 'Law', 'N;', 'Blog category', 'blog', 0, 'law', 1, 'icon-library', '', '', 1),
(112, 0, 0, 'Music', 'N;', 'Channel category', 'channel', 1, 'music', 1, 'icon-music', '', '', 1),
(113, 0, 0, 'Comedy', 'N;', 'Channel category', 'channel', 1, 'comedy', 1, 'icon-happy', '', '', 1),
(114, 0, 0, 'Film &amp; Entertainment', 'N;', 'Channel category', 'channel', 0, 'film-and-entertainment', 1, 'icon-film', '', '', 1),
(115, 0, 0, 'Gaming', 'N;', 'Channel category', 'channel', 0, 'gaming', 1, 'icon-target', '', '', 1),
(116, 0, 0, 'Beauty &amp; Fashion', 'N;', 'Channel category', 'channel', 0, 'beauty-and-fashion', 1, 'icon-eye2', '', '', 1),
(117, 0, 0, 'Automotive', 'N;', 'Channel category', 'channel', 1, 'automotive', 1, 'icon-truck', '', '', 1),
(118, 0, 0, 'Animation', 'N;', 'Channel category', 'channel', 0, 'animation', 1, 'icon-stack', '', '', 1),
(119, 0, 0, 'Sports', 'N;', 'Channel category', 'channel', 0, 'sports', 1, 'icon-dribbble', '', '', 1),
(120, 0, 0, 'Tech', 'N;', 'Channel category', 'channel', 0, 'tech', 1, 'icon-powercord', '', '', 1),
(121, 0, 0, 'Science &amp; Education', 'N;', 'Channel category', 'channel', 1, 'science-and-education', 1, 'icon-lab', '', '', 1),
(122, 0, 0, 'Cooking &amp; Health', 'N;', 'Channel category', 'channel', 1, 'cooking-and-health', 1, 'icon-food', '', '', 1),
(123, 0, 0, 'News &amp; Politics', 'N;', 'Channel category', 'channel', 1, 'news-and-politics', 1, 'icon-newspaper', '', '', 1),
(124, 0, 0, 'Autos &amp; Vehicles', 'N;', 'Broadcast category', 'live', 0, 'autos-and-vehicles', 1, 'icon-truck', '', '', 1),
(125, 0, 0, 'Comedy', 'N;', 'Broadcast category', 'live', 0, 'comedy', 1, 'icon-happy', '', '', 1),
(126, 0, 0, 'Education', 'N;', 'Broadcast category', 'live', 0, 'education', 1, 'icon-book', '', '', 1),
(127, 0, 0, 'Entertainment', 'N;', 'Broadcast category', 'live', 0, 'entertainment', 1, 'icon-tv', '', '', 1),
(128, 0, 0, 'Film &amp; Animation', 'N;', 'Broadcast category', 'live', 0, 'film-and-animation', 1, 'icon-film', '', '', 1),
(129, 0, 0, 'Gaming', 'N;', 'Broadcast category', 'live', 0, 'gaming', 1, 'icon-play6', '', '', 1),
(130, 0, 0, 'Howto &amp; Style', 'N;', 'Broadcast category', 'live', 0, 'howto-and-style', 1, 'icon-wand', '', '', 1),
(131, 0, 0, 'Music', 'N;', 'Broadcast category', 'live', 0, 'music', 1, 'icon-music', '', '', 1),
(132, 0, 0, 'News &amp; Politics', 'N;', 'Broadcast category', 'live', 0, 'news-and-politics', 1, 'icon-newspaper', '', '', 1),
(133, 0, 0, 'Nonprofits &amp; Activism', 'N;', 'Broadcast category', 'live', 0, 'nonprofits-and-activism', 1, 'icon-globe', '', '', 1),
(134, 0, 0, 'People &amp; Blogs', 'N;', 'Broadcast category', 'live', 0, 'people-and-blogs', 1, 'icon-user4', '', '', 1),
(135, 0, 0, 'Pets &amp; Animals', 'N;', 'Broadcast category', 'live', 0, 'pets-and-animals', 1, 'icon-github2', '', '', 1),
(136, 0, 0, 'Science &amp; Technology', 'N;', 'Broadcast category', 'live', 0, 'science-and-technology', 1, 'icon-lab', '', '', 1),
(137, 0, 0, 'Sports', 'N;', 'Broadcast category', 'live', 0, 'sports', 1, 'icon-dribbble', '', '', 1),
(138, 0, 0, 'Travel &amp; Events', 'N;', 'Broadcast category', 'live', 0, 'travel-and-events', 1, 'icon-airplane', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_channelcomments`
--

CREATE TABLE `db_channelcomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_channelevents`
--

CREATE TABLE `db_channelevents` (
  `e_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `e_datetime` datetime NOT NULL,
  `e_venue` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_address` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_city` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_zip` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_country` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_descr` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_ticket` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `e_suspended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `e_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_conversion`
--

CREATE TABLE `db_conversion` (
  `cfg_id` smallint(4) UNSIGNED NOT NULL,
  `cfg_name` varchar(50) NOT NULL,
  `cfg_data` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_conversion`
--

INSERT INTO `db_conversion` (`cfg_id`, `cfg_name`, `cfg_data`) VALUES
(1, 'conversion_mp4_360p_active', '1'),
(2, 'conversion_mp4_360p_bitrate_mt', 'fixed'),
(3, 'conversion_mp4_360p_bitrate_video', '300'),
(4, 'conversion_mp4_360p_resize', '1'),
(5, 'conversion_mp4_360p_resize_w', '640'),
(6, 'conversion_mp4_360p_resize_h', '360'),
(7, 'conversion_mp4_360p_bitrate_audio', '128'),
(8, 'conversion_mp4_360p_srate_audio', '44100'),
(9, 'conversion_mp4_360p_encoding', '1'),
(10, 'conversion_mp4_480p_active', '1'),
(11, 'conversion_mp4_480p_bitrate_mt', 'fixed'),
(12, 'conversion_mp4_480p_bitrate_video', '900'),
(13, 'conversion_mp4_480p_resize', '1'),
(14, 'conversion_mp4_480p_resize_w', '852'),
(15, 'conversion_mp4_480p_resize_h', '480'),
(16, 'conversion_mp4_480p_bitrate_audio', '128'),
(17, 'conversion_mp4_480p_srate_audio', '44100'),
(18, 'conversion_mp4_480p_encoding', '1'),
(19, 'conversion_mp4_720p_active', '1'),
(20, 'conversion_mp4_720p_bitrate_mt', 'auto'),
(21, 'conversion_mp4_720p_bitrate_video', '5000'),
(22, 'conversion_mp4_720p_resize', '1'),
(23, 'conversion_mp4_720p_resize_w', '1280'),
(24, 'conversion_mp4_720p_resize_h', '720'),
(25, 'conversion_mp4_720p_bitrate_audio', '128'),
(26, 'conversion_mp4_720p_srate_audio', '44100'),
(27, 'conversion_mp4_720p_encoding', '1'),
(28, 'conversion_flv_360p_active', '0'),
(29, 'conversion_flv_360p_bitrate_mt', 'fixed'),
(30, 'conversion_flv_360p_bitrate_video', '600'),
(31, 'conversion_flv_360p_fps', '25'),
(32, 'conversion_flv_360p_resize', '1'),
(33, 'conversion_flv_360p_resize_w', '630'),
(34, 'conversion_flv_360p_resize_h', '380'),
(35, 'conversion_flv_360p_srate_audio', '22050'),
(36, 'conversion_flv_360p_bitrate_audio', '56'),
(37, 'conversion_flv_480p_active', '0'),
(38, 'conversion_flv_480p_bitrate_mt', 'fixed'),
(39, 'conversion_flv_480p_bitrate_video', '1500'),
(40, 'conversion_flv_480p_fps', '25'),
(41, 'conversion_flv_480p_resize', '1'),
(42, 'conversion_flv_480p_resize_w', '852'),
(43, 'conversion_flv_480p_resize_h', '480'),
(44, 'conversion_flv_480p_srate_audio', '44100'),
(45, 'conversion_mp4_ipad_active', '1'),
(46, 'conversion_flv_480p_bitrate_audio', '128'),
(47, 'conversion_mp4_ipad_bitrate_mt', 'fixed'),
(48, 'conversion_mp4_ipad_bitrate_video', '1000'),
(49, 'conversion_mp4_ipad_resize', '1'),
(50, 'conversion_mp4_ipad_resize_w', '480'),
(51, 'conversion_mp4_ipad_resize_h', '360'),
(52, 'conversion_mp4_ipad_srate_audio', '44100'),
(53, 'conversion_mp4_ipad_bitrate_audio', '128'),
(54, 'conversion_mp4_ipad_encoding', '1'),
(55, 'conversion_flv_360p_reencode', '1'),
(56, 'conversion_flv_480p_reencode', '1'),
(57, 'conversion_vpx_360p_active', '0'),
(58, 'conversion_vpx_360p_bitrate_mt', 'fixed'),
(59, 'conversion_vpx_360p_bitrate_video', '300'),
(60, 'conversion_vpx_360p_resize', '1'),
(61, 'conversion_vpx_360p_resize_w', '640'),
(62, 'conversion_vpx_360p_resize_h', '360'),
(63, 'conversion_vpx_360p_bitrate_audio', '128'),
(64, 'conversion_vpx_360p_srate_audio', '44100'),
(65, 'conversion_vpx_360p_encoding', '1'),
(66, 'conversion_vpx_480p_active', '0'),
(67, 'conversion_vpx_480p_bitrate_mt', 'fixed'),
(68, 'conversion_vpx_480p_bitrate_video', '900'),
(69, 'conversion_vpx_480p_resize', '1'),
(70, 'conversion_vpx_480p_resize_w', '852'),
(71, 'conversion_vpx_480p_resize_h', '480'),
(72, 'conversion_vpx_480p_bitrate_audio', '128'),
(73, 'conversion_vpx_480p_srate_audio', '44100'),
(74, 'conversion_vpx_480p_encoding', '1'),
(75, 'conversion_vpx_720p_active', '0'),
(76, 'conversion_vpx_720p_bitrate_mt', 'auto'),
(77, 'conversion_vpx_720p_bitrate_video', '5000'),
(78, 'conversion_vpx_720p_resize', '1'),
(79, 'conversion_vpx_720p_resize_w', '1280'),
(80, 'conversion_vpx_720p_resize_h', '720'),
(81, 'conversion_vpx_720p_bitrate_audio', '128'),
(82, 'conversion_vpx_720p_srate_audio', '44100'),
(83, 'conversion_vpx_720p_encoding', '1'),
(84, 'conversion_ogv_360p_active', '0'),
(85, 'conversion_ogv_360p_bitrate_mt', 'fixed'),
(86, 'conversion_ogv_360p_bitrate_video', '300'),
(87, 'conversion_ogv_360p_resize', '1'),
(88, 'conversion_ogv_360p_resize_w', '640'),
(89, 'conversion_ogv_360p_resize_h', '360'),
(90, 'conversion_ogv_360p_bitrate_audio', '128'),
(91, 'conversion_ogv_360p_srate_audio', '44100'),
(92, 'conversion_ogv_360p_encoding', '1'),
(93, 'conversion_ogv_480p_active', '0'),
(94, 'conversion_ogv_480p_bitrate_mt', 'fixed'),
(95, 'conversion_ogv_480p_bitrate_video', '900'),
(96, 'conversion_ogv_480p_resize', '1'),
(97, 'conversion_ogv_480p_resize_w', '852'),
(98, 'conversion_ogv_480p_resize_h', '480'),
(99, 'conversion_ogv_480p_bitrate_audio', '128'),
(100, 'conversion_ogv_480p_srate_audio', '44100'),
(101, 'conversion_ogv_480p_encoding', '1'),
(102, 'conversion_ogv_720p_active', '0'),
(103, 'conversion_ogv_720p_bitrate_mt', 'auto'),
(104, 'conversion_ogv_720p_bitrate_video', '5000'),
(105, 'conversion_ogv_720p_resize', '1'),
(106, 'conversion_ogv_720p_resize_w', '1280'),
(107, 'conversion_ogv_720p_resize_h', '720'),
(108, 'conversion_ogv_720p_bitrate_audio', '128'),
(109, 'conversion_ogv_720p_srate_audio', '44100'),
(110, 'conversion_ogv_720p_encoding', '1'),
(111, 'conversion_mp4_1080p_active', '1'),
(112, 'conversion_mp4_1080p_bitrate_mt', 'auto'),
(113, 'conversion_mp4_1080p_bitrate_video', '7500'),
(114, 'conversion_mp4_1080p_resize', '1'),
(115, 'conversion_mp4_1080p_resize_w', '1920'),
(116, 'conversion_mp4_1080p_resize_h', '1080'),
(117, 'conversion_mp4_1080p_bitrate_audio', '128'),
(118, 'conversion_mp4_1080p_srate_audio', '44100'),
(119, 'conversion_mp4_1080p_encoding', '1'),
(120, 'conversion_vpx_1080p_active', '0'),
(121, 'conversion_vpx_1080p_bitrate_mt', 'auto'),
(122, 'conversion_vpx_1080p_bitrate_video', '7500'),
(123, 'conversion_vpx_1080p_resize', '1'),
(124, 'conversion_vpx_1080p_resize_w', '1920'),
(125, 'conversion_vpx_1080p_resize_h', '1080'),
(126, 'conversion_vpx_1080p_bitrate_audio', '128'),
(127, 'conversion_vpx_1080p_srate_audio', '44100'),
(128, 'conversion_vpx_1080p_encoding', '1'),
(129, 'conversion_ogv_1080p_active', '0'),
(130, 'conversion_ogv_1080p_bitrate_mt', 'auto'),
(131, 'conversion_ogv_1080p_bitrate_video', '7500'),
(132, 'conversion_ogv_1080p_resize', '1'),
(133, 'conversion_ogv_1080p_resize_w', '1920'),
(134, 'conversion_ogv_1080p_resize_h', '1080'),
(135, 'conversion_ogv_1080p_bitrate_audio', '128'),
(136, 'conversion_ogv_1080p_srate_audio', '44100'),
(137, 'conversion_ogv_1080p_encoding', '1');

-- --------------------------------------------------------

--
-- Table structure for table `db_dashboard`
--

CREATE TABLE `db_dashboard` (
  `id` mediumint(6) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_doccomments`
--

CREATE TABLE `db_doccomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docfavorites`
--

CREATE TABLE `db_docfavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docfiles`
--

CREATE TABLE `db_docfiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_short` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_pdf` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_swf` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_started` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_dochistory`
--

CREATE TABLE `db_dochistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docliked`
--

CREATE TABLE `db_docliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docpayouts`
--

CREATE TABLE `db_docpayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docplaylists`
--

CREATE TABLE `db_docplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docque`
--

CREATE TABLE `db_docque` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docrating`
--

CREATE TABLE `db_docrating` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docresponses`
--

CREATE TABLE `db_docresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_doctransfers`
--

CREATE TABLE `db_doctransfers` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `upload_start_time` datetime NOT NULL,
  `upload_end_time` datetime NOT NULL,
  `thumb_start_time` datetime NOT NULL,
  `thumb_end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_docwatchlist`
--

CREATE TABLE `db_docwatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_fileplayers`
--

CREATE TABLE `db_fileplayers` (
  `db_id` smallint(4) UNSIGNED NOT NULL,
  `db_name` varchar(32) NOT NULL,
  `db_config` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_fileplayers`
--

INSERT INTO `db_fileplayers` (`db_id`, `db_name`, `db_config`) VALUES
(1, 'jw_local', 'a:36:{s:14:\"jw_license_key\";s:0:\"\";s:18:\"jw_layout_controls\";s:1:\"1\";s:7:\"jw_skin\";s:5:\"seven\";s:12:\"jw_autostart\";s:1:\"0\";s:11:\"jw_fallback\";s:1:\"0\";s:7:\"jw_mute\";s:1:\"0\";s:10:\"jw_primary\";s:5:\"html5\";s:9:\"jw_repeat\";s:1:\"0\";s:13:\"jw_stretching\";s:7:\"uniform\";s:12:\"jw_logo_file\";s:80:\"https://www.viewsharkdemo.com/f_scripts/scripts_css/cosmic_dark/blue/images/logo.png\";s:12:\"jw_logo_link\";s:24:\"https://www.viewshark.com\";s:12:\"jw_logo_hide\";s:7:\"enabled\";s:14:\"jw_logo_margin\";s:0:\"\";s:16:\"jw_logo_position\";s:11:\"bottom-left\";s:10:\"jw_rc_text\";s:20:\"Powered by VIewShark\";s:10:\"jw_rc_link\";s:24:\"https://www.viewshark.com\";s:14:\"jw_adv_enabled\";s:1:\"0\";s:10:\"jw_adv_msg\";s:0:\"\";s:20:\"jw_analytics_enabled\";s:1:\"0\";s:20:\"jw_analytics_cookies\";s:1:\"0\";s:13:\"jw_ga_enabled\";s:1:\"0\";s:14:\"jw_ga_idstring\";s:4:\"file\";s:20:\"jw_ga_trackingobject\";s:0:\"\";s:16:\"jw_share_enabled\";s:1:\"1\";s:13:\"jw_share_link\";s:1:\"0\";s:13:\"jw_share_code\";s:1:\"0\";s:13:\"jw_share_head\";s:0:\"\";s:18:\"jw_related_enabled\";s:1:\"1\";s:15:\"jw_related_file\";s:18:\"/related?v=MEDIAID\";s:18:\"jw_related_onclick\";s:4:\"link\";s:21:\"jw_related_oncomplete\";s:1:\"0\";s:15:\"jw_related_head\";s:0:\"\";s:19:\"jw_captions_enabled\";s:1:\"0\";s:16:\"jw_captions_back\";s:1:\"0\";s:17:\"jw_captions_color\";s:0:\"\";s:20:\"jw_captions_fontsize\";s:0:\"\";}'),
(2, 'jw_embed', 'a:36:{s:14:\"jw_license_key\";s:0:\"\";s:18:\"jw_layout_controls\";s:1:\"1\";s:7:\"jw_skin\";s:5:\"seven\";s:12:\"jw_autostart\";s:1:\"0\";s:11:\"jw_fallback\";s:1:\"0\";s:7:\"jw_mute\";s:1:\"0\";s:10:\"jw_primary\";s:5:\"html5\";s:9:\"jw_repeat\";s:1:\"1\";s:13:\"jw_stretching\";s:7:\"uniform\";s:12:\"jw_logo_file\";s:80:\"https://www.viewsharkdemo.com/f_scripts/scripts_css/cosmic_dark/blue/images/logo.png\";s:12:\"jw_logo_link\";s:24:\"https://www.viewshark.com\";s:12:\"jw_logo_hide\";s:7:\"enabled\";s:14:\"jw_logo_margin\";s:2:\"10\";s:16:\"jw_logo_position\";s:11:\"bottom-left\";s:10:\"jw_rc_text\";s:20:\"Powered by VIewShark\";s:10:\"jw_rc_link\";s:24:\"https://www.viewshark.com\";s:14:\"jw_adv_enabled\";s:1:\"0\";s:10:\"jw_adv_msg\";s:0:\"\";s:20:\"jw_analytics_enabled\";s:1:\"0\";s:20:\"jw_analytics_cookies\";s:1:\"0\";s:13:\"jw_ga_enabled\";s:1:\"0\";s:14:\"jw_ga_idstring\";s:4:\"file\";s:20:\"jw_ga_trackingobject\";s:0:\"\";s:16:\"jw_share_enabled\";s:1:\"1\";s:13:\"jw_share_link\";s:1:\"0\";s:13:\"jw_share_code\";s:1:\"0\";s:13:\"jw_share_head\";s:0:\"\";s:18:\"jw_related_enabled\";s:1:\"0\";s:15:\"jw_related_file\";s:0:\"\";s:18:\"jw_related_onclick\";s:4:\"link\";s:21:\"jw_related_oncomplete\";s:1:\"0\";s:15:\"jw_related_head\";s:0:\"\";s:19:\"jw_captions_enabled\";s:1:\"0\";s:16:\"jw_captions_back\";s:1:\"0\";s:17:\"jw_captions_color\";s:0:\"\";s:20:\"jw_captions_fontsize\";s:0:\"\";}'),
(3, 'flow_local', 'a:16:{s:12:\"flow_license\";s:0:\"\";s:9:\"flow_logo\";s:0:\"\";s:11:\"flow_engine\";s:5:\"html5\";s:13:\"flow_disabled\";s:5:\"false\";s:13:\"flow_autoplay\";s:5:\"false\";s:15:\"flow_fullscreen\";s:4:\"true\";s:13:\"flow_keyboard\";s:4:\"true\";s:10:\"flow_muted\";s:5:\"false\";s:22:\"flow_native_fullscreen\";s:4:\"true\";s:13:\"flow_flashfit\";s:5:\"false\";s:9:\"flow_rtmp\";s:0:\"\";s:11:\"flow_splash\";s:4:\"true\";s:12:\"flow_tooltip\";s:4:\"true\";s:11:\"flow_volume\";s:3:\"0.7\";s:14:\"flow_subtitles\";s:5:\"false\";s:14:\"flow_analytics\";s:0:\"\";}'),
(4, 'flow_embed', 'a:16:{s:12:\"flow_license\";s:0:\"\";s:9:\"flow_logo\";s:0:\"\";s:11:\"flow_engine\";s:5:\"html5\";s:13:\"flow_disabled\";s:5:\"false\";s:13:\"flow_autoplay\";s:5:\"false\";s:15:\"flow_fullscreen\";s:4:\"true\";s:13:\"flow_keyboard\";s:4:\"true\";s:10:\"flow_muted\";s:5:\"false\";s:22:\"flow_native_fullscreen\";s:4:\"true\";s:13:\"flow_flashfit\";s:5:\"false\";s:9:\"flow_rtmp\";s:0:\"\";s:11:\"flow_splash\";s:4:\"true\";s:12:\"flow_tooltip\";s:4:\"true\";s:11:\"flow_volume\";s:3:\"0.6\";s:14:\"flow_subtitles\";s:5:\"false\";s:14:\"flow_analytics\";s:0:\"\";}'),
(5, 'vjs_local', 'a:21:{s:19:\"vjs_layout_controls\";s:1:\"1\";s:8:\"vjs_skin\";s:0:\"\";s:13:\"vjs_autostart\";s:1:\"0\";s:8:\"vjs_loop\";s:1:\"0\";s:9:\"vjs_muted\";s:1:\"0\";s:11:\"vjs_related\";s:1:\"1\";s:13:\"vjs_logo_file\";s:0:\"\";s:17:\"vjs_logo_position\";s:11:\"bottom-left\";s:12:\"vjs_logo_url\";s:0:\"\";s:13:\"vjs_logo_fade\";s:0:\"\";s:15:\"vjs_advertising\";s:1:\"0\";s:12:\"vjs_rc_text1\";s:0:\"\";s:12:\"vjs_rc_link1\";s:0:\"\";s:12:\"vjs_rc_text2\";s:0:\"\";s:12:\"vjs_rc_link2\";s:0:\"\";s:12:\"vjs_rc_text3\";s:0:\"\";s:12:\"vjs_rc_link3\";s:0:\"\";s:12:\"vjs_rc_text4\";s:0:\"\";s:12:\"vjs_rc_link4\";s:0:\"\";s:12:\"vjs_rc_text5\";s:0:\"\";s:12:\"vjs_rc_link5\";s:0:\"\";}'),
(6, 'vjs_embed', 'a:21:{s:19:\"vjs_layout_controls\";s:1:\"1\";s:8:\"vjs_skin\";s:0:\"\";s:13:\"vjs_autostart\";s:1:\"0\";s:8:\"vjs_loop\";s:1:\"0\";s:9:\"vjs_muted\";s:1:\"0\";s:11:\"vjs_related\";s:1:\"0\";s:13:\"vjs_logo_file\";s:0:\"\";s:17:\"vjs_logo_position\";s:11:\"bottom-left\";s:12:\"vjs_logo_url\";s:0:\"\";s:13:\"vjs_logo_fade\";s:0:\"\";s:15:\"vjs_advertising\";s:1:\"0\";s:12:\"vjs_rc_text1\";s:0:\"\";s:12:\"vjs_rc_link1\";s:0:\"\";s:12:\"vjs_rc_text2\";s:0:\"\";s:12:\"vjs_rc_link2\";s:0:\"\";s:12:\"vjs_rc_text3\";s:0:\"\";s:12:\"vjs_rc_link3\";s:0:\"\";s:12:\"vjs_rc_text4\";s:0:\"\";s:12:\"vjs_rc_link4\";s:0:\"\";s:12:\"vjs_rc_text5\";s:0:\"\";s:12:\"vjs_rc_link5\";s:0:\"\";}');

-- --------------------------------------------------------

--
-- Table structure for table `db_filetypemenu`
--

CREATE TABLE `db_filetypemenu` (
  `db_id` tinyint(3) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `value` varchar(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_followers`
--

CREATE TABLE `db_followers` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `sub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sub_time` datetime NOT NULL,
  `sub_type` tinytext NOT NULL DEFAULT 'all',
  `mail_new_uploads` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `follower_id` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_fpadentries`
--

CREATE TABLE `db_fpadentries` (
  `ad_id` int(10) UNSIGNED NOT NULL,
  `ad_key` varchar(16) NOT NULL,
  `ad_name` varchar(255) NOT NULL,
  `ad_cuepoint` float NOT NULL,
  `ad_css` text NOT NULL,
  `ad_file` varchar(16) NOT NULL,
  `ad_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagecomments`
--

CREATE TABLE `db_imagecomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagefavorites`
--

CREATE TABLE `db_imagefavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagefiles`
--

CREATE TABLE `db_imagefiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_short` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_started` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagehistory`
--

CREATE TABLE `db_imagehistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imageliked`
--

CREATE TABLE `db_imageliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagepayouts`
--

CREATE TABLE `db_imagepayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imageplaylists`
--

CREATE TABLE `db_imageplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imageque`
--

CREATE TABLE `db_imageque` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagerating`
--

CREATE TABLE `db_imagerating` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imageresponses`
--

CREATE TABLE `db_imageresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagetransfers`
--

CREATE TABLE `db_imagetransfers` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `upload_start_time` datetime NOT NULL,
  `upload_end_time` datetime NOT NULL,
  `thumb_start_time` datetime NOT NULL,
  `thumb_end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_imagewatchlist`
--

CREATE TABLE `db_imagewatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_jwadcodes`
--

CREATE TABLE `db_jwadcodes` (
  `db_id` mediumint(6) UNSIGNED NOT NULL,
  `db_key` varchar(16) NOT NULL,
  `db_type` varchar(10) NOT NULL,
  `db_name` varchar(50) NOT NULL,
  `db_code` text NOT NULL,
  `db_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_jwadentries`
--

CREATE TABLE `db_jwadentries` (
  `ad_id` int(10) UNSIGNED NOT NULL,
  `ad_key` varchar(16) NOT NULL,
  `ad_name` varchar(50) NOT NULL,
  `ad_type` varchar(12) NOT NULL DEFAULT 'shared',
  `ad_position` varchar(10) NOT NULL,
  `ad_offset` float NOT NULL,
  `ad_duration` smallint(4) UNSIGNED NOT NULL,
  `ad_client` varchar(20) NOT NULL,
  `ad_format` varchar(10) NOT NULL,
  `ad_server` varchar(20) NOT NULL,
  `ad_file` varchar(32) NOT NULL,
  `ad_width` smallint(4) UNSIGNED NOT NULL DEFAULT 480,
  `ad_height` smallint(4) UNSIGNED NOT NULL DEFAULT 360,
  `ad_bitrate` smallint(4) UNSIGNED NOT NULL DEFAULT 300,
  `ad_tag` text NOT NULL,
  `ad_comp_div` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ad_comp_id` varchar(50) NOT NULL,
  `ad_comp_w` smallint(4) UNSIGNED NOT NULL,
  `ad_comp_h` smallint(4) UNSIGNED NOT NULL,
  `ad_click_track` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `ad_click_url` text NOT NULL,
  `ad_track_events` text NOT NULL,
  `ad_impressions` int(12) UNSIGNED NOT NULL,
  `ad_clicks` int(12) UNSIGNED NOT NULL,
  `ad_primary` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ad_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_languages`
--

CREATE TABLE `db_languages` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `lang_id` varchar(30) NOT NULL,
  `lang_name` varchar(50) NOT NULL,
  `lang_flag` varchar(20) NOT NULL,
  `lang_default` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `lang_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_languages`
--

INSERT INTO `db_languages` (`db_id`, `lang_id`, `lang_name`, `lang_flag`, `lang_default`, `lang_active`) VALUES
(1, 'en_US', 'English', 'flag-icon-us', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_livechat`
--

CREATE TABLE `db_livechat` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `chat_id` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `channel_id` int(10) UNSIGNED NOT NULL,
  `channel_owner` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `stream_id` int(10) UNSIGNED NOT NULL,
  `chat_user` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `chat_display` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `is_live` tinyint(1) NOT NULL DEFAULT 0,
  `chat_ip` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `chat_fp` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `chat_time` datetime NOT NULL,
  `badge` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `usr_profileinc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `first` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `logged_in` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livecomments`
--

CREATE TABLE `db_livecomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livefavorites`
--

CREATE TABLE `db_livefavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livefiles`
--

CREATE TABLE `db_livefiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `mail_sent` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_short` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vjs_ads` text NOT NULL,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_started` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vod_server` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `viewers` mediumint(7) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livehistory`
--

CREATE TABLE `db_livehistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_liveliked`
--

CREATE TABLE `db_liveliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livepayouts`
--

CREATE TABLE `db_livepayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_liveplaylists`
--

CREATE TABLE `db_liveplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_liveque`
--

CREATE TABLE `db_liveque` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_liverating`
--

CREATE TABLE `db_liverating` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_liveresponses`
--

CREATE TABLE `db_liveresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_liveservers`
--

CREATE TABLE `db_liveservers` (
  `srv_id` smallint(5) UNSIGNED NOT NULL,
  `srv_name` varchar(48) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `srv_slug` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `srv_type` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `srv_host` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `srv_port` smallint(5) UNSIGNED NOT NULL DEFAULT 8080,
  `srv_https` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `srv_freespace` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `srv_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livetemps`
--

CREATE TABLE `db_livetemps` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` varchar(128) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livetoken`
--

CREATE TABLE `db_livetoken` (
  `tk_id` int(9) UNSIGNED NOT NULL,
  `tk_name` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tk_slug` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tk_price` float UNSIGNED NOT NULL,
  `tk_currency` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'USD',
  `tk_amount` int(9) UNSIGNED NOT NULL,
  `tk_vat` tinyint(1) NOT NULL DEFAULT 0,
  `tk_shared` int(3) NOT NULL,
  `tk_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `db_livetoken`
--

INSERT INTO `db_livetoken` (`tk_id`, `tk_name`, `tk_slug`, `tk_price`, `tk_currency`, `tk_amount`, `tk_vat`, `tk_shared`, `tk_active`) VALUES
(1, '100 Tokens', '100-tokens', 1.75, 'USD', 100, 1, 57, 1),
(2, '500 Tokens', '500-tokens', 8.75, 'USD', 500, 1, 57, 1),
(3, '1500 Tokens', '1500-tokens', 24.94, 'USD', 1500, 1, 60, 1),
(4, '5000 Tokens', '5000-tokens', 80.5, 'USD', 5000, 1, 62, 1),
(5, '10000 Tokens', '10000-tokens', 158.25, 'USD', 10000, 1, 63, 1),
(6, '25000 Tokens', '25000-tokens', 385, 'USD', 25000, 1, 65, 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_liveviewers`
--

CREATE TABLE `db_liveviewers` (
  `db_id` bigint(20) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `longip` bigint(20) UNSIGNED NOT NULL,
  `ts` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_livewatchlist`
--

CREATE TABLE `db_livewatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_mailque`
--

CREATE TABLE `db_mailque` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `mail_type` varchar(32) NOT NULL,
  `mail_key` varchar(16) NOT NULL,
  `mail_from` varchar(100) NOT NULL,
  `mail_to` text NOT NULL,
  `mail_datetime` datetime NOT NULL,
  `mail_extra` text NOT NULL,
  `mail_complete` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_messaging`
--

CREATE TABLE `db_messaging` (
  `msg_id` int(10) UNSIGNED NOT NULL,
  `msg_subj` varchar(200) NOT NULL,
  `msg_body` text NOT NULL,
  `msg_from` int(10) UNSIGNED NOT NULL,
  `msg_to` int(10) UNSIGNED NOT NULL,
  `msg_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `msg_invite` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `msg_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `msg_inbox_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `msg_outbox_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `msg_active_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `msg_reply_to` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_video_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_short_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_image_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_audio_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_doc_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_blog_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_live_attch` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `msg_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_notifications`
--

CREATE TABLE `db_notifications` (
  `id` int(9) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `date` datetime NOT NULL,
  `seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_notifications_count`
--

CREATE TABLE `db_notifications_count` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `nr` int(10) UNSIGNED DEFAULT NULL,
  `act_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_notifications_hidden`
--

CREATE TABLE `db_notifications_hidden` (
  `db_id` int(9) UNSIGNED NOT NULL,
  `act_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_packdiscounts`
--

CREATE TABLE `db_packdiscounts` (
  `dc_id` mediumint(6) UNSIGNED NOT NULL,
  `dc_code` varchar(100) NOT NULL,
  `dc_descr` tinytext NOT NULL,
  `dc_amount` float NOT NULL,
  `dc_date` datetime NOT NULL,
  `dc_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_packtypes`
--

CREATE TABLE `db_packtypes` (
  `pk_id` int(10) UNSIGNED NOT NULL,
  `pk_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_descr` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_space` bigint(30) UNSIGNED NOT NULL,
  `pk_bw` bigint(30) UNSIGNED NOT NULL,
  `pk_price` int(10) UNSIGNED NOT NULL,
  `pk_priceunit` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '$',
  `pk_priceunitname` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_alimit` int(10) UNSIGNED NOT NULL,
  `pk_ilimit` int(10) UNSIGNED NOT NULL,
  `pk_vlimit` int(10) UNSIGNED NOT NULL,
  `pk_slimit` int(10) UNSIGNED DEFAULT NULL,
  `pk_dlimit` int(10) UNSIGNED NOT NULL,
  `pk_llimit` int(10) UNSIGNED NOT NULL,
  `pk_blimit` int(10) UNSIGNED NOT NULL,
  `pk_period` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_packtypes`
--

INSERT INTO `db_packtypes` (`pk_id`, `pk_name`, `pk_descr`, `pk_space`, `pk_bw`, `pk_price`, `pk_priceunit`, `pk_priceunitname`, `pk_alimit`, `pk_ilimit`, `pk_vlimit`, `pk_slimit`, `pk_dlimit`, `pk_llimit`, `pk_blimit`, `pk_period`, `pk_active`) VALUES
(1, 'Free Trials', 'The description of the Free Trial membership.', 30, 60, 0, '&#36;', 'USD', 3, 3, 3, 0, 4, 3, 3, '31', 1),
(2, 'Bronze', 'The description of the Bronze membership.', 256, 512, 25, '$', 'USD', 50, 100, 50, NULL, 15, 10, 5, '30', 1),
(3, 'Silver', 'The description of the Silver membership.', 512, 1024, 50, '$', 'USD', 100, 250, 100, NULL, 50, 30, 15, '90', 1),
(4, 'Gold', 'The description of the Gold membership.', 5120, 10240, 150, '$', 'USD', 500, 800, 500, NULL, 200, 100, 100, '180', 1),
(5, 'Platinum', 'The description of the Platinum membership', 0, 0, 300, '$', 'USD', 0, 0, 0, NULL, 0, 0, 0, '365', 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_packusers`
--

CREATE TABLE `db_packusers` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_usedspace` bigint(40) UNSIGNED NOT NULL DEFAULT 0,
  `pk_usedbw` bigint(40) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_video` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_short` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_image` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_audio` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_doc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_live` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_blog` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_paid` float NOT NULL,
  `pk_paid_total` float NOT NULL,
  `subscribe_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expire_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_servers`
--

CREATE TABLE `db_servers` (
  `server_id` int(11) UNSIGNED NOT NULL,
  `server_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `server_type` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'ftp',
  `server_priority` mediumint(6) UNSIGNED NOT NULL DEFAULT 1,
  `server_limit` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `file_hop` int(12) UNSIGNED NOT NULL DEFAULT 10,
  `current_hop` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `total_video` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `total_short` int(12) UNSIGNED NOT NULL DEFAULT 0,
  `total_image` int(12) UNSIGNED NOT NULL,
  `total_audio` int(12) UNSIGNED NOT NULL,
  `total_doc` int(12) UNSIGNED NOT NULL,
  `upload_v_file` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_v_thumb` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_s_file` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_s_thumb` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_i_file` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_i_thumb` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_a_file` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_a_thumb` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_d_file` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `upload_d_thumb` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ftp_host` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `ftp_port` mediumint(6) UNSIGNED NOT NULL DEFAULT 21,
  `ftp_transfer` varchar(16) NOT NULL,
  `ftp_passive` tinyint(1) UNSIGNED NOT NULL,
  `ftp_username` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `ftp_password` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `ftp_root` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `streaming_method` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'progressive',
  `url` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `lighttpd_url` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `lighttpd_secdownload` enum('0','1') CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '0',
  `lighttpd_prefix` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `lighttpd_key` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `rtmp_stream` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT '',
  `s3_accesskey` varchar(100) NOT NULL,
  `s3_secretkey` varchar(100) NOT NULL,
  `s3_bucketname` varchar(100) NOT NULL,
  `s3_region` varchar(32) NOT NULL,
  `s3_fileperm` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'public-read',
  `cf_enabled` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cf_origin_id` varchar(64) NOT NULL,
  `cf_dist_type` varchar(1) NOT NULL DEFAULT 'w',
  `cf_dist_price` varchar(12) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'All',
  `cf_dist_id` varchar(32) NOT NULL,
  `cf_dist_status` varchar(32) NOT NULL,
  `cf_dist_domain` varchar(100) NOT NULL,
  `cf_dist_uri` varchar(255) NOT NULL,
  `cf_signed_url` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cf_signed_expire` int(7) UNSIGNED NOT NULL,
  `cf_key_pair` varchar(32) NOT NULL,
  `cf_key_file` varchar(255) NOT NULL,
  `last_used` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_settings`
--

CREATE TABLE `db_settings` (
  `id` int(3) UNSIGNED NOT NULL,
  `cfg_name` varchar(50) NOT NULL,
  `cfg_data` text NOT NULL,
  `cfg_info` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_settings`
--

INSERT INTO `db_settings` (`id`, `cfg_name`, `cfg_data`, `cfg_info`) VALUES
(1, 'backend_username_recovery', '1', 'backend: enable/disable username recovery'),
(2, 'backend_password_recovery', '1', 'backend: enable/disable password recovery'),
(3, 'backend_email', 'email-username@gmail.com', 'backend: admin email'),
(4, 'backend_username', 'admin', 'backend: admin username'),
(5, 'backend_password', '$2a$08$UN3Hp9N2a8rW3PAM5tCBTebLdudxotOhr42ohZreGHSR5DJm3CmxS', 'backend: admin password'),
(6, 'backend_remember', '1', 'backend: enable/disable remember me'),
(7, 'head_title', 'YourSiteName', 'frontend: head title'),
(8, 'metaname_description', 'Website Meta Description', 'frontend: head meta description'),
(9, 'metaname_keywords', 'video, video sharing, mobile video, high quality video, high definition video, digital camera, video blog, video blogging, home video, home movie, home recording, image, image sharing, audio, audio sharing, audio recording, image album, playlists, upload video, upload music, upload pictures, upload documents, youtube clone, youtube script, personal youtube', 'frontend: head meta keywords'),
(10, 'website_shortname', 'YourSiteName', 'frontend: shortname used for website'),
(11, 'username_format', 'strict', 'frontend: username format'),
(12, 'username_format_dott', '0', 'frontend: username format, allow dott characters'),
(13, 'signup_min_age', '18', 'frontend: minimum age required on signup'),
(14, 'signup_max_age', '70', 'frontend: maximum age allowed on signup'),
(15, 'signup_min_password', '5', 'frontend: minimum length of signup password'),
(16, 'signup_max_password', '15', 'frontend: maximum length of signup password'),
(17, 'signup_max_username', '15', 'frontend: maximum length of signup username'),
(18, 'signup_min_username', '5', 'frontend: minimum length of signup username'),
(19, 'signup_captcha_level', 'easy', 'frontend: signup captcha difficulty'),
(20, 'paid_memberships', '0', 'frontend: use paid memberships'),
(21, 'numeric_delimiter', '.', 'global: numeric delimit character'),
(22, 'video_module', '1', 'frontend: enable/disable video module'),
(23, 'payment_methods', 'Paypal,Moneybookers', 'frontend: membership payment method'),
(24, 'paypal_email', 'your-paypal-email@address.com', 'frontend: paypal receiver address'),
(25, 'paypal_test', '1', 'frontend: paypal test/sandbox mode'),
(26, 'login_remember', '1', 'frontend: enable/disable remember me'),
(27, 'mail_type', 'smtp', 'global: mailer type'),
(28, 'backend_email_fromname', 'Webmaster', 'global: admin email  - from name'),
(29, 'mail_pop3_host', 'pop.gmail.com', 'global: mailer pop3 hostname'),
(30, 'mail_pop3_port', '995', 'global: mailer pop3 port'),
(31, 'mail_pop3_timeout', '10', 'global: mailer pop3 timeout'),
(32, 'mail_pop3_username', '', 'global: mailer pop3 username'),
(33, 'mail_pop3_password', '', 'global: mailer pop3 password'),
(34, 'mail_smtp_host', 'smtp.gmail.com', 'global: mailer smtp hostname'),
(35, 'mail_smtp_auth', 'true', 'global: mailer smtp authentication'),
(36, 'mail_smtp_port', '587', 'global: mailer smtp port'),
(37, 'mail_smtp_username', 'email-username@gmail.com', 'global: mailer smtp username'),
(38, 'mail_smtp_password', '', 'global: mailer smtp password'),
(39, 'mail_smtp_prefix', 'tls', 'global: mailer smtp server prefix'),
(40, 'mail_debug', '1', 'global: mailer debugging messages'),
(41, 'activity_logging', '1', 'global: log all user activity'),
(42, 'signup_username_availability', '1', 'frontend: allow username availability check on signup'),
(43, 'signup_password_meter', '1', 'frontend: show password strength on signup'),
(44, 'signup_captcha', '0', 'frontend: enable/disable captcha on signup'),
(45, 'password_recovery_captcha', '1', 'frontend: enable/disable captcha on password recovery'),
(46, 'username_recovery_captcha', '1', 'frontend: enable/disable captcha on username recovery'),
(47, 'recovery_link_lifetime', '12', 'frontend: how long will a password reset link be usable (hours)'),
(48, 'allow_username_recovery', '1', 'frontend: enable/disable username recovery'),
(49, 'allow_password_recovery', '1', 'frontend: enable/disable password recovery'),
(50, 'backend_username_recovery_captcha', '1', 'backend: username recovery captcha'),
(51, 'backend_password_recovery_captcha', '1', 'backend: password recovery captcha'),
(52, 'website_offline_mode', '0', 'backend: configure offline mode'),
(53, 'global_signup', '1', 'frontend: enable/disable signup'),
(54, 'disabled_signup_message', 'Registration is currently closed! Please try later.', 'Message which shows if signup is disabled'),
(55, 'conversion_logging', '1', 'frontend: log file conversion attempts'),
(56, 'debug_mode', '0', 'global: PHP debugging mode'),
(57, 'signup_ip_access', '0', 'Allow signups only from the specified IP addresses'),
(58, 'signup_domain_restriction', '0', 'Allow signups only from email addresses coming from the specified domains'),
(59, 'list_signup_terms', 'f_data/data_lists/eula.tpl', 'frontend: the link which contains the signup terms text'),
(60, 'list_ip_signup', 'f_data/data_lists/IP-based-signup.tpl', 'frontend: the link which contains the list of IP addresses which may register'),
(61, 'list_email_domains', 'f_data/data_lists/allowed-email-domains.tpl', 'frontend: the link which contains the list of email domains which may register'),
(62, 'list_reserved_users', 'f_data/data_lists/reserved-usernames.tpl', 'frontend: the link which contains the list of reserverd usernames'),
(63, 'signup_terms', '1', 'frontend: show the ToS on signup'),
(64, 'backend_recovery_link_lifetime', '24', 'backend: password recovery link lifetime (in hours)'),
(65, 'frontend_signin_section', '1', 'frontend: enable/disable logging in'),
(66, 'backend_signin_section', '1', 'backend: enable/disable logging in'),
(67, 'website_offline_message', 'We are closed right now, please come back later. Thank you for understanding!', 'frontend: offline message'),
(68, 'website_ip_based_access', '0', 'frontend: allow access based on IP addresses'),
(69, 'list_ip_access', 'f_data/data_lists/IP-based-frontend.tpl', 'frontend: the link which contains the list of IP addresses which may access the website'),
(70, 'list_ip_backend', 'f_data/data_lists/IP-based-backend.tpl', 'backend: allow access based on IP addresses'),
(71, 'backend_ip_based_access', '0', 'backend: allow access based on IP addresses'),
(72, 'username_format_dash', '0', 'frontend: username format, allow dash characters'),
(73, 'username_format_underscore', '0', 'frontend: username format, allow underscore characters'),
(74, 'backend_username_recovery_captcha_level', 'easy', 'backend: username recovery captcha level'),
(75, 'backend_password_recovery_captcha_level', 'normal', 'backend: password recovery captcha level'),
(76, 'frontend_username_recovery_captcha_level', 'easy', 'frontend: username recovery captcha level'),
(77, 'frontend_password_recovery_captcha_level', 'easy', 'frontend: password recovery captcha level'),
(78, 'backend_signin_count', '1', 'backend: count successful logins'),
(79, 'frontend_signin_count', '1', 'frontend: count successful logins'),
(80, 'discount_codes', '0', 'frontend: enable/disable discount codes for paid memberships'),
(81, 'keep_entries_open', '1', 'global: keep div entry listings always opened'),
(82, 'paypal_test_email', '', 'frontend: paypal sandbox email account'),
(83, 'paypal_logging', '1', 'backend: enable/disable paypal transaction logging'),
(84, 'paypal_log_file', 'f_data/data_logs/log_pp/.pp_ipn.log', 'backend: paypal log file path'),
(85, 'account_approval', '0', 'global: enable/disable account approval'),
(86, 'account_email_verification', '0', 'frontend: enable/disable account email verification'),
(87, 'mail_sendmail_path', '/usr/sbin/sendmail', 'backend: path to sendmail utility'),
(88, 'website_email', 'vsdmailer@gmail.com', 'global: website email address'),
(89, 'noreply_email', 'noreply@gmail.com', 'global: noreply email'),
(90, 'notify_welcome', '1', 'notification: welcome email'),
(91, 'website_email_fromname', 'Website Service', 'global: website email address - from name'),
(92, 'noreply_email_fromname', 'NoReply', 'global: noreply email - from name'),
(93, 'paypal_payments', '1', 'frontend: enable/disable paypal payments'),
(96, 'allow_self_messaging', '1', 'frontend: allow sending messages to own user'),
(97, 'allow_multi_messaging', '1', 'frontend: allow sending messages to multiple users'),
(98, 'multi_messaging_limit', '3', 'backend: maximum number or users that can be messaged at the same time'),
(99, 'internal_messaging', '1', 'backend: enable/disable internal messaging'),
(100, 'message_attachments', '1', 'backend: enable/disable message attachments'),
(101, 'custom_labels', '1', 'backend: enable/disable labels'),
(102, 'user_friends', '1', 'backend: enable/disable friends functions'),
(103, 'user_blocking', '1', 'backend: enable/disable user blocking functions'),
(104, 'user_image_max_size', '4', 'frontend: maximum size of user avatar (in kilobytes)'),
(105, 'user_image_allowed_extensions', 'bmp,gif,jpg,jpeg,png', 'frontend: user image allowed extensions'),
(106, 'user_image_width', '220', 'frontend: user avatar width'),
(107, 'user_image_height', '220', 'frontend: user avatar height'),
(108, 'email_change_captcha_level', 'easy', 'frontend: captcha level on email change request'),
(109, 'email_change_captcha', '0', 'frontend: enable/disable captcha for email changing request'),
(110, 'message_count', '1', 'frontend: show label/message count'),
(111, 'public_channels', '1', 'backend: enable/disable user channels'),
(112, 'channel_bulletins', '1', 'backend: enable/disable channel bulletins'),
(113, 'event_map', '1', 'backend: enable/disable map of user channel events'),
(114, 'approve_friends', '1', 'backed: friend invite/friend approval'),
(115, 'channel_comments', '1', 'backend: enable/disable channel comments'),
(116, 'file_comments', '1', 'backend: enable/disable file comments'),
(117, 'ucc_limit', '3', 'backend: limit for consecutive comments by same user'),
(118, 'comment_min_length', '3', 'backend: comment limit, minumum'),
(119, 'comment_max_length', '254', 'backend: comment limit, maximum'),
(120, 'channel_backgrounds', '1', 'backend: enable/disable channel bg images'),
(121, 'channel_bg_allowed_extensions', 'jpg,jpeg,png', 'backend: channel background file types'),
(122, 'channel_bg_max_size', '2', 'backend: channel backgrounds file size limit'),
(123, 'sitemap_global_frontpage', '1', 'backend: global sitemap include options'),
(124, 'video_uploads', '1', 'backend: enable/disable video uploading'),
(125, 'video_file_types', '3gp,3gpp,asf,avi,dat,flv,mov,mpg,mpeg,mp4,mkv,m4v,rm,wmv', 'backend: allowed video formats'),
(126, 'video_limit', '1000', 'backend: maximum size of uploaded video'),
(127, 'multiple_file_uploads', '10', 'backend: maximum number of allowed uploads at once'),
(128, 'file_privacy', '1', 'backend: enable/disable privacy on files'),
(129, 'file_comment_votes', '1', 'backend: enable/disable voting on comments'),
(130, 'file_rating', '1', 'backend: enable/disable file ratings'),
(131, 'file_responses', '1', 'backend: enable/disable file responses'),
(132, 'file_embedding', '1', 'backend: enable/disable file embedding'),
(133, 'file_social_sharing', '1', 'backend: enable/disable social sharing'),
(134, 'file_favorites', '1', 'backend: enable/disable adding to favorites'),
(135, 'file_playlists', '1', 'backend: enable/disable creating playlists'),
(136, 'file_deleting', '1', 'backend: enable/disable deleting of files'),
(137, 'file_views', '1', 'backend: enable/disable view counting on files'),
(138, 'file_approval', '0', 'backend: enable/disable approving of files'),
(139, 'file_history', '1', 'backend: enable/disable history of viewed files'),
(140, 'file_watchlist', '1', 'backend: enable/disable the use of watchlists'),
(141, 'file_counts', '0', 'backend: enable/disable file count display'),
(142, 'conversion_flv', '0', 'backend: enable/disable flv conversion'),
(143, 'conversion_mp4', '1', 'backend: enable/disable mp4 conversion'),
(144, 'conversion_ipad', '1', 'backend: enable/disable ipad conversion'),
(145, 'conversion_flv_bitrate_mt', 'fixed', 'backend: flv conversion bitrate method'),
(146, 'conversion_mp4_bitrate_mt', 'auto', 'backend: hd conversion bitrate method'),
(147, 'conversion_ipad_bitrate_mt', 'fixed', 'backend: mobile conversion bitrate method'),
(148, 'conversion_flv_bitrate_video', '1600', 'backend: flv conversion video bitrate'),
(149, 'conversion_mp4_bitrate_video', '1500', 'backend: hd conversion video bitrate'),
(150, 'conversion_ipad_bitrate_video', '1000', 'backend: ipad conversion video bitrate'),
(151, 'conversion_flv_fps', '25', 'backend: flv conversion fps'),
(152, 'conversion_flv_resize', '1', 'backend: resize for flv conversion'),
(153, 'conversion_mp4_resize', '1', 'backend: resize for hd conversion'),
(154, 'conversion_ipad_resize', '1', 'backend: resize for ipad conversion'),
(155, 'conversion_flv_resize_w', '630', 'backend: resize width for flv conversion'),
(156, 'conversion_mp4_resize_w', '960', 'backend: resize width for hd conversion'),
(157, 'conversion_ipad_resize_w', '640', 'backend: resize width for ipad conversion'),
(158, 'conversion_flv_resize_h', '380', 'backend: resize height for flv conversion'),
(159, 'conversion_mp4_resize_h', '720', 'backend: resize height for hd conversion'),
(160, 'conversion_ipad_resize_h', '480', 'backend: resize height for ipad conversion'),
(161, 'conversion_flv_srate_audio', '22050', 'backend: audio sample rate for flv conversion'),
(162, 'conversion_mp4_srate_audio', '44100', 'backend: audio sample rate for hd conversion'),
(163, 'conversion_ipad_srate_audio', '44100', 'backend: audio sample rate for ipad conversion'),
(164, 'conversion_flv_bitrate_audio', '56', 'backend: flv conversion audio bitrate '),
(165, 'conversion_mp4_bitrate_audio', '128', 'backend: hd conversion audio bitrate '),
(166, 'conversion_ipad_bitrate_audio', '128', 'backend: mobile conversion audio bitrate '),
(167, 'conversion_mp4_encoding', '1', 'backend: hd conversion encoding'),
(168, 'conversion_ipad_encoding', '1', 'backend: ipad conversion encoding'),
(169, 'server_path_ffmpeg', '/usr/bin/ffmpeg', 'backend: ffmpeg server path'),
(170, 'server_path_yamdi', '/usr/bin/yamdi', 'backend: yamdi server path'),
(171, 'server_path_qt', '/usr/bin/qt-faststart', 'backend: ffmpeg qt server path'),
(172, 'server_path_lame', '/usr/bin/lame', 'backend: lame server path'),
(173, 'server_path_php', '/usr/bin/php7', 'backend: php server path'),
(174, 'conversion_source_video', '0', 'backend: store or delete original uploaded files'),
(175, 'file_downloads', '1', 'backend: allow file downloading'),
(176, 'thumbs_rotation', '0', 'backend: enable/disable thumbnail previews'),
(177, 'thumbs_nr', '1', 'backend: set nr of thumbnails'),
(178, 'thumbs_format', 'PNG', 'backend: thumbnail output format'),
(179, 'thumbs_width', '120', 'backend: thumbnail width'),
(180, 'thumbs_height', '70', 'backend: thumbnail height'),
(181, 'thumbs_method', 'rand', 'backend: thumbnail grab order'),
(182, 'backend_leftmenu', 'list', 'backend: left nav menu'),
(183, 'user_subscriptions', '1', 'backend: enable/disable user subscriptions'),
(184, 'email_logging', '1', 'backend: log email activity'),
(185, 'log_signin', '0', 'backend: global logging: sign in'),
(186, 'log_signout', '0', 'backend: global logging: sign out'),
(187, 'log_precovery', '0', 'backend: global logging: password recovery'),
(188, 'log_urecovery', '0', 'backend: global logging: username recovery'),
(189, 'log_frinvite', '1', 'backend: global logging: inviting'),
(190, 'log_pmessage', '1', 'backend: global logging: private messaging'),
(191, 'log_rating', '1', 'backend: global logging: rating'),
(192, 'log_filecomment', '1', 'backend: global logging: commenting'),
(193, 'log_subscribing', '1', 'backend: global logging: subscribing'),
(194, 'log_fav', '1', 'backend: global logging: favoriting'),
(195, 'log_upload', '1', 'backend: global logging: uploading'),
(196, 'log_video_conversion', '1', 'backend: video conversion logging'),
(197, 'backend_notification_signup', '1', 'backend: admin signup notification'),
(198, 'backend_notification_upload', '1', 'backend: admin upload notification'),
(199, 'backend_notification_payment', '1', 'backend: admin payment notification'),
(200, 'file_delete_method', '4', 'backend: how file deleting works'),
(201, 'log_delete', '0', 'backend: log delete activity'),
(202, 'channel_views', '1', 'backend: enable/disable channel view counting'),
(203, 'file_flagging', '1', 'backend: enable/disable file flagging'),
(204, 'file_email_sharing', '1', 'backend: enable/disable email file sharing'),
(205, 'file_permalink_sharing', '1', 'backend: enable/disable permalink box display'),
(206, 'backend_notification_flag', '1', 'backend: enable/disable admin notification on file flagging'),
(207, 'fcc_limit', '100', 'backend: file consecutive count limit'),
(208, 'file_comment_min_length', '3', 'backend: file comment min. length'),
(209, 'file_comment_max_length', '500', 'backend: file comment max. length'),
(210, 'file_comment_spam', '1', 'backend: enable/disable spam reporting on file comments'),
(211, 'video_player', 'vjs', 'backend: setting to control which video player is used'),
(212, 'conversion_mp3_bitrate', '128', 'backend: settings for audio conversion, bitrate'),
(213, 'conversion_mp3_srate', '44.1', 'backend: settings for audio conversion, sample rate'),
(214, 'file_download_s1', '1', 'backend: file download option, converted files'),
(215, 'file_download_s2', '1', 'backend: file download option, source files'),
(216, 'file_download_s3', '1', 'backend: file download option, mp4/hd files'),
(217, 'file_download_s4', '1', 'backend: file download option, mp4/mobile files'),
(218, 'file_download_reg', '0', 'backend: file download option, allow only members to download'),
(219, 'conversion_mp3', '1', 'backend: enable/disable audio conversion'),
(224, 'mobile_module', '1', 'backend: enable/disable mobile interface'),
(225, 'mobile_detection', '1', 'backend: mobile interface forced redirection'),
(226, 'conversion_video_que', '1', 'backend: enable/video video conversion que'),
(227, 'conversion_pdf2swf_bypass', '1', 'backend: enable/disable pdf2swf conversion'),
(228, 'server_path_pdf2swf', '/usr/bin/pdf2swf', 'backend: server path for pdf2swf'),
(229, 'server_path_convert', '/usr/bin/convert', 'backend: server path for convert'),
(230, 'server_path_unoconv', '/usr/bin/unoconv', 'backend: server path for unoconv'),
(231, 'conversion_mp3_redo', '1', 'backend: audio conversion, re-convert mp3 files'),
(232, 'session_lifetime', '60', 'backend: setting for session lifetime'),
(233, 'session_name', 'VSK', 'backend: setting for session name'),
(234, 'date_timezone', 'UTC', 'backend: setting for default date.timezone'),
(238, 'sitemap_global_content', '1', 'backend: global sitemap include options'),
(239, 'sitemap_global_categories', '1', 'backend: global sitemap include options'),
(240, 'sitemap_global_users', '1', 'backend: global sitemap include options'),
(241, 'sitemap_global_video', '1', 'backend: global sitemap include options'),
(242, 'sitemap_global_video_pl', '1', 'backend: global sitemap include options'),
(243, 'sitemap_global_max', '45000', 'backend: global sitemap include options'),
(244, 'sitemap_video_src', 'player', 'backend: video sitemap source (localtion or player)'),
(245, 'sitemap_video_max', '45000', 'backend: video sitemap limit'),
(246, 'sitemap_video_hd', '1', 'backend: video sitemap include hd'),
(247, 'guest_browse_video', '1', 'backend: guest account access, browse videos'),
(248, 'guest_view_video', '1', 'backend: guest account access, view videos'),
(249, 'guest_view_channel', '1', 'backend: guest account access, view user channels'),
(250, 'guest_search_page', '1', 'backend: guest account access, search page'),
(251, 'stream_method', '2', 'backend: stream settings, stream method'),
(252, 'stream_server', 'apache', 'backend: stream settings, stream server'),
(253, 'stream_lighttpd_url', '', 'backend: stream settings, lighttpd url'),
(254, 'stream_lighttpd_secure', '1', 'backend: stream settings, lighttpd secure'),
(255, 'stream_lighttpd_prefix', '/video/', 'backend: stream settings, lighttpd prefix'),
(256, 'stream_lighttpd_key', '', 'backend: stream settings, lighttpd key'),
(257, 'stream_rtmp_location', '', 'backend: stream settings, rtmp stream location'),
(258, 'google_analytics', '', 'backend: google analytics tracking id'),
(259, 'google_webmaster', '', 'backend: google-site-verification code'),
(260, 'yahoo_explorer', '', 'backend: yahoo site explorer verification code'),
(261, 'bing_validate', '', 'backend: bing verification code'),
(262, 'mobile_menu', '0', 'backend: mobile interface left side navigation meu'),
(263, 'benchmark_display', '0', 'backend: show memory usage information in the footer'),
(264, 'backend_menu_toggle', '0', 'backend: show left side navigation menus expanded or collapsed'),
(265, 'guest_browse_playlist', '1', 'backend: guest account access, browse playlists'),
(266, 'guest_browse_channel', '1', 'backend: guest account access, browse channels'),
(268, 'site_theme', 'blue', 'frontend: site theme name'),
(269, 'pause_video_transfer', '1', 'backend: pause or resume video transfers'),
(270, 'audio_module', '1', 'frontend: enable/disable audio module'),
(271, 'audio_uploads', '1', 'backend: enable/disable audio uploading'),
(272, 'audio_file_types', 'flac,m4a,mp3,mp4,ogg,rm,vqf,wav,wma', 'backend: allowed audio formats'),
(273, 'audio_limit', '300', 'backend: max. size of uploaded audio'),
(274, 'conversion_flv_srate_audio', '22050', 'backend: audio sample rate for flv conversion'),
(275, 'conversion_mp4_srate_audio', '44100', 'backend: audio sample rate for hd conversion'),
(276, 'conversion_ipad_srate_audio', '44100', 'backend: audio sample rate for ipad conversion'),
(277, 'conversion_source_audio', '0', 'backend: delete source audio files'),
(278, 'log_audio_conversion', '1', 'backend: audio conversion logging'),
(279, 'audio_player', 'vjs', 'backend: setting to control which audio player is used'),
(280, 'conversion_audio_que', '0', 'backend: enable/video audio conversion que'),
(281, 'sitemap_global_audio', '1', 'backend: global sitemap include options'),
(282, 'sitemap_global_audio_pl', '1', 'backend: global sitemap include options'),
(283, 'guest_browse_audio', '1', 'backend: guest account access, browse audios'),
(284, 'guest_view_audio', '1', 'backend: guest account access, view audios'),
(285, 'document_uploads', '1', 'backend: enable/disable document uploading'),
(286, 'document_file_types', 'doc,docx,pdf,ppt,pps,rtf,txt', 'backend: allowed document formats'),
(287, 'document_limit', '100', 'backend: max. size of uploaded document'),
(288, 'document_module', '1', 'backend: enable/disable documents module'),
(289, 'conversion_source_doc', '0', 'backend: delete source document files'),
(290, 'log_doc_conversion', '1', 'backend: document conversion logging'),
(291, 'conversion_document_que', '0', 'backend: enable/video document conversion que'),
(292, 'document_player', 'reader', 'backend: setting for document player'),
(293, 'sitemap_global_document', '1', 'backend: global sitemap include options'),
(294, 'sitemap_global_document_pl', '1', 'backend: global sitemap include options'),
(295, 'guest_browse_doc', '1', 'backend: guest account access, browse documents'),
(296, 'guest_view_doc', '1', 'backend: guest account access, view documents'),
(297, 'image_module', '1', 'frontend: enable/disable image module'),
(298, 'image_uploads', '1', 'backend: enable/disable image uploading'),
(299, 'image_file_types', 'gif,jpg,jpeg,png', 'backend: allowed image formats'),
(300, 'image_limit', '50', 'backend: max. size of uploaded image'),
(301, 'conversion_source_image', '0', 'backend: delete source image files'),
(302, 'log_image_conversion', '1', 'backend: image conversion logging'),
(303, 'conversion_image_type', '3', 'backend: criteria for image conversion'),
(304, 'image_player', 'jq', 'backend: setting to control how images are played, with jwplayer or jquery lightbox'),
(305, 'conversion_image_from_w', '1024', 'backend: image conversion from width, setting 3'),
(306, 'conversion_image_from_h', '768', 'backend: image conversion from height, setting 3'),
(307, 'conversion_image_to_w', '1024', 'backend: image conversion to width, setting 3'),
(308, 'conversion_image_to_h', '768', 'backend: image conversion to height, setting 3'),
(309, 'conversion_image_que', '0', 'backend: enable/disable image conversion que'),
(310, 'sitemap_global_image', '1', 'backend: global sitemap include options'),
(311, 'sitemap_global_image_pl', '1', 'backend: global sitemap include options'),
(312, 'sitemap_image_max', '1000', 'backend: image sitemap limit'),
(313, 'guest_browse_image', '1', 'backend: guest account access, browse images'),
(314, 'guest_view_image', '1', 'backend: guest account access, view images'),
(315, 'pause_audio_transfer', '1', 'backend: pause or resume audio transfers'),
(316, 'pause_image_transfer', '1', 'backend: pause or resume image transfers'),
(317, 'pause_doc_transfer', '1', 'backend: pause or resume doc transfers'),
(318, 'import_yt_channel_list', '', 'backend: video grabber, youtube channels'),
(319, 'import_dm_user_list', '', 'backend: video grabber, dailymotion users'),
(320, 'import_mc_user_list', '', 'backend: video grabber, metacafe users'),
(321, 'import_vi_user_list', '', 'backend: video grabber, vimeo users'),
(322, 'import_yt', '1', 'backend: video grabber, youtube support'),
(323, 'import_dm', '1', 'backend: video grabber, dalymotion support'),
(324, 'import_mc', '0', 'backend: video grabber, metacafe support'),
(325, 'import_vi', '1', 'backend: video grabber, vimeo support'),
(326, 'm_list_yt', '0', 'backend: video grabber, list youtube videos on mobile'),
(327, 'm_list_dm', '0', 'backend: video grabber, list dailymotion videos on mobile'),
(328, 'm_list_mc', '0', 'backend: video grabber, list metacafe videos on mobile'),
(329, 'm_list_vi', '0', 'backend: video grabber, list vimeo videos on mobile'),
(330, 'import_mode', 'embed', 'backend: video grabber file save mode'),
(331, 'youtube_api_key', 'youtube-api-key', ''),
(332, 'server_path_ffprobe', '/usr/bin/ffprobe', 'backend: server path for ffprobe utility'),
(333, 'upload_category', 'auto', 'backend: manually assign category when uploading'),
(334, 'channel_promo', '1', 'backend: setting to enable/disable promoted channels'),
(335, 'file_promo', '1', 'backend: setting to enable/disable promoted files'),
(336, 'file_thumb_change', '1', 'backend: setting to enable/disable if users are allowed to change the thumbnail'),
(337, 'recaptcha_site_key', 'recaptcha-site-key', 'backend: google recaptcha site key'),
(338, 'recaptcha_secret_key', 'recaptcha-secret-key', 'backend: google recaptcha secret key'),
(339, 'fb_auth', '0', 'backend: enable/disable facebook login/signup module'),
(340, 'fb_app_id', 'facebook-app-id', 'backend: facebook login/signup app id'),
(341, 'fb_app_secret', 'facebook-app-secret', 'backend: facebook login/signup app secret'),
(342, 'gp_auth', '0', 'backend: enable/disable google login/signup module'),
(343, 'gp_app_id', 'google-app-id', 'backend: google login/signup app id'),
(344, 'gp_app_secret', 'google-app-secret', 'backend: google login/signup app secret'),
(345, 'google_analytics_api', 'analytics-api-client-id', 'backend: google analytics api client id'),
(346, 'facebook_link', '', 'backend: facebook link'),
(347, 'twitter_link', '', 'backend: twitter link'),
(348, 'gplus_link', '', 'backend: google plus link'),
(350, 'blog_module', '1', 'backend: enable/disable blog module'),
(351, 'guest_browse_blog', '1', 'backend: guest account access, browse blogs'),
(352, 'guest_view_blog', '1', 'backend: guest account access, view blogs'),
(353, 'sitemap_global_blog', '1', 'backend: global sitemap include options'),
(354, 'sitemap_global_blog_pl', '1', 'backend: global sitemap include options'),
(355, 'custom_tagline', 'Custom Tagline', 'backend: website custom tagline'),
(356, 'last_video_sitemap', '0', 'frontend: video sitemap, last video'),
(357, 'last_image_sitemap', '0', 'frontend: image sitemap, last image'),
(358, 'live_module', '1', 'backend: enable/disable live streaming module'),
(370, 'live_uploads', '1', 'backend: enable/disable saving live broadcasts'),
(371, 'guest_browse_live', '1', 'backend: guest account access, browse broadcasts'),
(372, 'guest_view_live', '1', 'backend: guest account access, view broadcasts page'),
(373, 'live_server', 'rtmp://server-ip-address/live', 'backend: stream server address (with vods)'),
(374, 'live_chat', '1', 'backend: enable/disable live chat during live streams'),
(375, 'live_vod', '1', 'backend: enable/disable saving live streams as videos'),
(376, 'live_del', '90', 'backend: delete saved video streams after n days'),
(377, 'live_cast', '', 'backend: stream server address (no vods)'),
(378, 'user_follows', '1', 'backend: enable/disable user follows'),
(379, 'log_following', '1', 'backend: enable/disable logging of follow actions'),
(380, 'sitemap_global_live', '1', 'backend: global sitemap include options'),
(381, 'sitemap_global_live_pl', '1', 'backend: global sitemap include options'),
(382, 'blog_uploads', '1', 'backend: enable/disable blog uploading'),
(383, 'live_chat_server', 'http(s)://chat-server-ip:port', 'backend: live chat server'),
(384, 'live_chat_salt', 'CMojLHFK4TA3CJlK5uredYema5j1oGAtccEDjiK6pLdoHjjwHtF91pBAQWA37rhLxfFiHHf37bKPwdam73wv2C0KH6E', 'backend: live chat salt key'),
(385, 'sub_shared_revenue', '60', 'backend: percentage of revenue to be shared from every paid subscri\r\nption'),
(386, 'subscription_payout_currency', 'USD', 'backend: paid subscriptions payout currency'),
(387, 'conversion_video_previews', '0', 'backend: enable/disable video previews'),
(388, 'paypal_api_user', '', 'backend: paypal nvp api username'),
(389, 'paypal_api_pass', '', 'backend: paypal nvp api password'),
(390, 'paypal_api_sign', '', 'backend: paypal nvp api signature'),
(391, 'live_vod_server', 'http(s)://vod-server-ip:port', 'backend: vod stream server address'),
(392, 'live_hls_server', 'http(s)://hls-server-ip:port', 'backend: hls stream server address'),
(394, 'sub_threshold', '20', 'backend: subscription revenue sharing payout threshold'),
(395, 'partner_requirements_min', '1', 'backend: criteria for allowing partner requests'),
(396, 'partner_requirements_type', '4', 'backend: criteria for allowing partner requests'),
(397, 'conversion_audio_previews', '0', 'backend: enable/disable audio previews'),
(398, 'conversion_doc_previews', '0', 'backend: enable/disable document previews'),
(399, 'conversion_image_previews', '0', 'backend: enable/disable image previews'),
(400, 'conversion_live_previews', '0', 'backend: enable/disable broadcast previews'),
(401, 'signin_captcha', '0', 'backend: enable/disable login captcha'),
(402, 'signin_captcha_be', '0', 'backend: enable/disable login captcha'),
(403, 'google_analytics_view', 'analytics-view-id', 'backend: google analytics api view id'),
(404, 'google_analytics_maps', 'maps-api-key', 'backend: google maps api key'),
(405, 'live_cron_salt', 'IOpeCV5jyJ6SIbg2ySIB5os5RX3a1a9LIjyJmGfShcPovdx43GmBHgsJTXbo71CD4df4oFKikCZxJS4f', 'backend: crontab salt key'),
(406, 'comment_emoji', '1', 'backend: enable/disable comment emojis'),
(407, 'social_media_links', 'N;', 'backend: footer social media links'),
(408, 'offline_mode_settings', 'N;', 'backend: settings for offline mode'),
(409, 'offline_mode_until', '', 'backend: settings for offline mode'),
(410, 'file_download_s5', 'backend: file download option, mp4/hd1080p files', ''),
(808, 'affiliate_module', '0', 'backend: enable/disable affiliate module'),
(809, 'affiliate_tracking_id', 'UA-#######-#', 'backend: affiliate module, google analytics tracking id'),
(810, 'affiliate_view_id', '123456789', 'backend: affiliate module, google analytics view id'),
(811, 'affiliate_maps_api_key', 'maps-api-key', 'backend: affiliate module, google maps api key'),
(812, 'affiliate_token_script', '/path/to/service.py', 'backend: affiliate module, python token script'),
(813, 'affiliate_payout_figure', '1', 'backend: affiliate module, payout figure'),
(814, 'affiliate_payout_units', '1000', 'backend: affiliate module, payout units'),
(815, 'affiliate_payout_currency', 'USD', 'backend: affiliate module, payout currency'),
(816, 'affiliate_payout_share', '100', 'backend: affiliate module, payout share'),
(817, 'affiliate_requirements_min', '1', 'backend: affiliate module, min. views required'),
(818, 'affiliate_requirements_type', '4', 'backend: affiliate module, min. views type'),
(819, 'affiliate_geo_maps', '0', 'backend: enable/disable geo maps for affiliated users'),
(820, 'token_threshold', '1000', 'backend: token payout threshold'),
(821, 'user_tokens', '1', 'backend: enable/disable tokens module'),
(822, 'short_module', '1', 'backend: enable/disable video shorts'),
(823, 'short_uploads', '1', 'backend: enable/disable video shorts uploads'),
(824, 'new_layout', '1', 'backend: enable/disable new layout menu'),
(825, 'short_limit', '1000', 'backend: maximum size of uploaded video shorts'),
(826, 'short_file_types', '3gp,3gpp,asf,avi,dat,flv,mov,mpg,mpeg,mp4,mkv,m4v,rm,wmv', 'backend: allowed video short formats'),
(827, 'conversion_short_previews', '0', 'backend: enable/disable video shorts previews'),
(828, 'conversion_short_que', '1', 'backend: enable/video video shorts conversion que'),
(829, 'log_short_conversion', '1', 'backend: video shorts conversion logging'),
(830, 'conversion_source_short', '0', 'backend: store or delete original uploaded video shorts files'),
(831, 'sitemap_global_short', '1', 'backend: global sitemap include options'),
(832, 'sitemap_short_max', '45000', 'backend: video shorts sitemap limit'),
(833, 'sitemap_short_src', 'player', 'backend: video shorts sitemap source (location or player)'),
(835, 'last_short_sitemap', '0', 'frontend: video shorts sitemap, last video short'),
(836, 'pause_short_transfer', '1', 'backend: pause or resume video short transfers'),
(837, 'short_player', 'vjs', 'backend: setting to control which video player is used for shorts'),
(838, 'guest_view_short', '1', 'backend: guest account access, view shorts page'),
(839, 'log_responding', '1', 'backend: enable/disable logging of response actions'),
(840, 'sitemap_global_short_pl', '1', 'backend: global sitemap include options');

-- --------------------------------------------------------

--
-- Table structure for table `db_shortcomments`
--

CREATE TABLE `db_shortcomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortdl`
--

CREATE TABLE `db_shortdl` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `video_url` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortfavorites`
--

CREATE TABLE `db_shortfavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortfiles`
--

CREATE TABLE `db_shortfiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vjs_ads` text NOT NULL,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shorthistory`
--

CREATE TABLE `db_shorthistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortliked`
--

CREATE TABLE `db_shortliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortpayouts`
--

CREATE TABLE `db_shortpayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortplaylists`
--

CREATE TABLE `db_shortplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortque`
--

CREATE TABLE `db_shortque` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortrating`
--

CREATE TABLE `db_shortrating` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortresponses`
--

CREATE TABLE `db_shortresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortsubs`
--

CREATE TABLE `db_shortsubs` (
  `sub_id` int(5) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `vjs_subs` text NOT NULL,
  `jw_subs` text NOT NULL,
  `fp_subs` text NOT NULL,
  `sub_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shorttransfers`
--

CREATE TABLE `db_shorttransfers` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `upload_start_time` datetime NOT NULL,
  `upload_end_time` datetime NOT NULL,
  `thumb_start_time` datetime NOT NULL,
  `thumb_end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_shortwatchlist`
--

CREATE TABLE `db_shortwatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_subinvoices`
--

CREATE TABLE `db_subinvoices` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `sub_payout` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `sub_amount` float UNSIGNED NOT NULL,
  `sub_currency` varchar(3) NOT NULL DEFAULT 'USD',
  `create_date` datetime NOT NULL,
  `pay_date` datetime NOT NULL,
  `sub_paid` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_subpayouts`
--

CREATE TABLE `db_subpayouts` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `usr_id_to` int(10) UNSIGNED NOT NULL,
  `pk_id` int(10) NOT NULL,
  `pk_paid` float NOT NULL,
  `pk_paid_share` float UNSIGNED NOT NULL DEFAULT 0,
  `sub_id` varchar(32) NOT NULL,
  `sub_time` datetime NOT NULL,
  `txn_id` varchar(64) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `is_cancel` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `cancel_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_subscribers`
--

CREATE TABLE `db_subscribers` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `sub_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sub_time` datetime NOT NULL,
  `sub_type` tinytext NOT NULL DEFAULT 'all',
  `mail_new_uploads` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `subscriber_id` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_subscriptions`
--

CREATE TABLE `db_subscriptions` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `sub_list` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_subtemps`
--

CREATE TABLE `db_subtemps` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `usr_id_to` int(10) UNSIGNED NOT NULL,
  `pk_id` smallint(5) UNSIGNED NOT NULL,
  `expire_time` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_subtypes`
--

CREATE TABLE `db_subtypes` (
  `pk_id` int(10) UNSIGNED NOT NULL,
  `pk_name` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_descr` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_price` int(10) UNSIGNED NOT NULL,
  `pk_priceunit` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL DEFAULT '$',
  `pk_priceunitname` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_period` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `pk_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_subtypes`
--

INSERT INTO `db_subtypes` (`pk_id`, `pk_name`, `pk_descr`, `pk_price`, `pk_priceunit`, `pk_priceunitname`, `pk_period`, `pk_active`) VALUES
(1, 'Tier 1', 'Subscription benefits:\r\n- Ad-Free viewing\r\n- access to full length videos\r\n- chatting during Sub-Only Mode\r\n- subscription tenure badges\r\n\r\n$5 / Month', 5, '$', 'USD', '30', 1),
(2, 'Tier 2', 'Subscription benefits:\r\n- Ad-Free viewing\r\n- access to full length videos\r\n- chatting during Sub-Only Mode\r\n- subscription tenure badges\r\n\r\n$10 / Month', 10, '$', 'USD', '30', 1),
(3, 'Tier 3', 'Subscription benefits:\r\n- Ad-Free viewing\r\n- access to full length videos\r\n- chatting during Sub-Only Mode\r\n- subscription tenure badges\r\n\r\n$25 / Month', 25, '$', 'USD', '30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_subusers`
--

CREATE TABLE `db_subusers` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `usr_id_to` int(10) UNSIGNED NOT NULL,
  `pk_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_usedspace` bigint(40) UNSIGNED NOT NULL DEFAULT 0,
  `pk_usedbw` bigint(40) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_video` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_image` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_audio` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_doc` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_live` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_total_blog` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `pk_paid` float NOT NULL,
  `pk_paid_total` float NOT NULL,
  `subscriber_id` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `subscribe_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `expire_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_tokendonations`
--

CREATE TABLE `db_tokendonations` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `tk_from` int(10) UNSIGNED NOT NULL,
  `tk_to` int(10) UNSIGNED NOT NULL,
  `tk_from_user` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tk_to_user` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tk_amount` int(10) UNSIGNED NOT NULL,
  `tk_date` datetime NOT NULL,
  `is_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `tk_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_tokeninvoices`
--

CREATE TABLE `db_tokeninvoices` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `tk_payout` text CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `tk_amount` float UNSIGNED NOT NULL,
  `tk_currency` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL DEFAULT 'USD',
  `create_date` datetime NOT NULL,
  `pay_date` datetime NOT NULL,
  `tk_paid` tinyint(1) NOT NULL DEFAULT 0,
  `txn_id` varchar(64) NOT NULL,
  `txn_receipt` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_tokenpayments`
--

CREATE TABLE `db_tokenpayments` (
  `db_id` int(9) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `tk_id` int(9) UNSIGNED NOT NULL,
  `tk_amount` int(9) UNSIGNED NOT NULL,
  `tk_price` float UNSIGNED NOT NULL,
  `tk_date` datetime NOT NULL,
  `txn_id` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `txn_receipt` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `tk_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_tokenpayouts`
--

CREATE TABLE `db_tokenpayouts` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `usr_tokens` int(10) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `p_datetime` datetime NOT NULL,
  `txn_id` varchar(128) NOT NULL,
  `tk_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_trackactivity`
--

CREATE TABLE `db_trackactivity` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `log_delete` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_signin` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `log_signout` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `log_pmessage` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `log_precovery` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `log_urecovery` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `log_rating` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_rating` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_filecomment` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_filecomment` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_subscribing` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_subscribing` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_fav` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_fav` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_upload` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_upload` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_frinvite` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_following` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_following` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `log_responding` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `share_responding` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_trackactivity`
--

INSERT INTO `db_trackactivity` (`db_id`, `usr_id`, `log_delete`, `log_signin`, `log_signout`, `log_pmessage`, `log_precovery`, `log_urecovery`, `log_rating`, `share_rating`, `log_filecomment`, `share_filecomment`, `log_subscribing`, `share_subscribing`, `log_fav`, `share_fav`, `log_upload`, `share_upload`, `log_frinvite`, `log_following`, `share_following`, `log_responding`, `share_responding`) VALUES
(1, 1, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 2, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(3, 3, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(4, 4, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(5, 5, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(6, 6, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(7, 7, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(8, 8, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(9, 9, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(10, 10, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(11, 11, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(12, 12, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(13, 13, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(14, 14, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(15, 15, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(16, 16, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(17, 17, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(18, 18, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(19, 19, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(20, 20, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(21, 21, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(22, 22, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(23, 23, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(24, 24, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(25, 25, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(26, 26, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(27, 27, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(28, 28, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(29, 29, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(30, 30, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(32, 32, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_useractivity`
--

CREATE TABLE `db_useractivity` (
  `act_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `usr_id_to` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `act_type` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `act_time` datetime NOT NULL,
  `act_ip` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `act_visible` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `act_deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_usercodes`
--

CREATE TABLE `db_usercodes` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pwd_id` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `create_date` datetime NOT NULL,
  `use_date` datetime NOT NULL,
  `code_used` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `code_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_usercontacts`
--

CREATE TABLE `db_usercontacts` (
  `ct_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pwd_id` varchar(32) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ct_name` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ct_username` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ct_email` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ct_datetime` datetime NOT NULL,
  `ct_friend` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ct_blocked` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ct_block_cfg` varchar(254) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `ct_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_userlabels`
--

CREATE TABLE `db_userlabels` (
  `lb_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `lb_name` varchar(50) NOT NULL,
  `lb_for` varchar(255) NOT NULL,
  `lb_ids` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `lb_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Table structure for table `db_usertypes`
--

CREATE TABLE `db_usertypes` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `db_name` varchar(30) NOT NULL,
  `db_desc` varchar(100) NOT NULL,
  `db_influences` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `db_styles` text NOT NULL,
  `db_custom_fields` text NOT NULL,
  `db_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `db_usertypes`
--

INSERT INTO `db_usertypes` (`db_id`, `db_name`, `db_desc`, `db_influences`, `db_styles`, `db_custom_fields`, `db_active`) VALUES
(1, 'Member', 'Regular member account', 0, '', 'a:1:{i:0;a:3:{s:4:\"type\";s:4:\"text\";s:4:\"left\";s:11:\"Member Info\";s:5:\"right\";s:15:\"Regular Account\";}}', 1),
(2, 'Director', 'Director profile', 0, 'Acting\nArt\nBiography\nBroadcaster\nCommentary\nDance\nFashion\nModel\nNews\nReviews\nTalk Show\nVariety\nVLogging', '', 1),
(3, 'Musician', 'Musician profile', 1, 'Acappella\nAcoustic\nAlt Country\nAlternative\nAmericana\nArt Rock\nBluegrass\nBlues\nBrit Pop\nCeltic\nChristian\nChristian Rap\nChristian\nRock\nClassical\nCountry\nCrunk\nDance\nDisco\nElectronica\nElectropop\nEmo\nExperimental\nFolk\nFolk Rock\nFreestyle\nFunk\nGarage Rock\nGlam\nGospel\nGoth\nGrunge\nHard Rock\nHip-Hop\nHouse\nIndie Rock\nIndustrial\nJam Rock\nJazz\nJungle\nLatin\nLatin Pop\nMariachi\nMetal\nMiscellaneous\nMotown\nOld School Rap\nPop\nProgressive Rock\nPsychedelic\nPsychobilly\nPunk\nRap\nR&amp;B\nReggae\nRetro\nRock\nRockabilly\nRoots\nSalsa\nSinger-Songwriter\nSka\nSoul\nSouthern Rap\nSpoken Word\nString Bands\nSurf\nTango\nTechno\nTrance\nTrip Hop\nTurntablist\nWorld', 'a:5:{i:0;a:3:{s:4:\"type\";s:5:\"input\";s:4:\"left\";s:12:\"Record Label\";s:5:\"right\";s:0:\"\";}i:1;a:3:{s:4:\"type\";s:6:\"select\";s:4:\"left\";s:10:\"Label Type\";s:5:\"right\";s:32:\"Independent,Major Label,Unsigned\";}i:2;a:3:{s:4:\"type\";s:5:\"input\";s:4:\"left\";s:12:\"Band Members\";s:5:\"right\";s:0:\"\";}i:3;a:3:{s:4:\"type\";s:4:\"link\";s:4:\"left\";s:11:\"Buy Album 1\";s:5:\"right\";s:8:\"img_link\";}i:4;a:3:{s:4:\"type\";s:4:\"link\";s:4:\"left\";s:11:\"Buy Album 2\";s:5:\"right\";s:8:\"img_link\";}}', 1),
(4, 'Comedian', 'Comedian profile', 1, 'Asian\r\nBlack\r\nBlue Collar\r\nCelebrity\r\nHumor\r\nClown\r\nGay/Lesbian\r\nHypnotist\r\nImpersonations\r\nImprov\r\nLatino\r\nMagic\r\nMusical\r\nParody\r\nPolitical\r\nSketch\r\nStand-Up', '', 1),
(5, 'Guru', 'Guru profile', 0, 'Beauty\nCrafting\nEducational\nFinancial\nFitness\nFood+Drinks\nHome+Garden\nMechanics\nMusic\nRelationship\nSpiritual\nTravel\nVideo', '', 1),
(6, 'Reporter', 'Reporter profile', 0, 'Current Events\nEntertainment\nInternational Affairs\nLocal News\nNational News\nPolitics', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `db_videocomments`
--

CREATE TABLE `db_videocomments` (
  `c_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `c_usr_id` int(10) UNSIGNED NOT NULL,
  `c_key` int(10) UNSIGNED NOT NULL,
  `c_replyto` int(10) UNSIGNED NOT NULL,
  `c_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_datetime` datetime NOT NULL,
  `c_rating` text NOT NULL,
  `c_rating_value` mediumint(6) UNSIGNED NOT NULL DEFAULT 0,
  `c_spam` text DEFAULT NULL,
  `c_approved` tinyint(1) UNSIGNED NOT NULL,
  `c_edited` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_edittime` datetime NOT NULL,
  `c_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_seen` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `c_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videodl`
--

CREATE TABLE `db_videodl` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `video_url` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videofavorites`
--

CREATE TABLE `db_videofavorites` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videofiles`
--

CREATE TABLE `db_videofiles` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `thumb_cache` int(3) UNSIGNED NOT NULL DEFAULT 1,
  `is_short` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_file_key` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `old_key` varchar(16) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_hash` varchar(32) NOT NULL,
  `file_size` int(20) UNSIGNED NOT NULL,
  `file_duration` float NOT NULL,
  `file_hd` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_mobile` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_title` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_tags` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `file_category` smallint(5) UNSIGNED NOT NULL,
  `privacy` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comments` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `comment_votes` tinyint(1) UNSIGNED NOT NULL,
  `comment_spam` tinyint(1) UNSIGNED NOT NULL,
  `rating` tinyint(1) UNSIGNED NOT NULL,
  `responding` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `embedding` tinyint(1) UNSIGNED NOT NULL,
  `social` tinyint(1) UNSIGNED NOT NULL,
  `approved` tinyint(1) UNSIGNED NOT NULL,
  `deleted` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `upload_date` datetime NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `last_viewdate` date NOT NULL,
  `is_featured` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_promoted` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_subscription` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `has_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_preview` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `file_views` int(10) UNSIGNED NOT NULL,
  `file_favorite` int(10) UNSIGNED NOT NULL,
  `file_comments` int(10) UNSIGNED NOT NULL,
  `file_responses` int(10) UNSIGNED NOT NULL,
  `file_like` int(10) UNSIGNED NOT NULL,
  `file_dislike` int(10) UNSIGNED NOT NULL,
  `file_flag` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vjs_ads` text NOT NULL,
  `jw_ads` text NOT NULL,
  `fp_ads` text NOT NULL,
  `banner_ads` text NOT NULL,
  `embed_key` varchar(255) NOT NULL,
  `embed_src` varchar(32) NOT NULL DEFAULT 'local',
  `embed_url` mediumtext NOT NULL,
  `stream_server` varchar(128) NOT NULL,
  `stream_key` varchar(128) NOT NULL,
  `stream_key_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_chat` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_key_old` varchar(255) NOT NULL,
  `stream_vod` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `stream_live` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_start` datetime NOT NULL,
  `stream_started` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `stream_end` datetime NOT NULL,
  `stream_ended` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videohistory`
--

CREATE TABLE `db_videohistory` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `views` mediumint(8) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videoliked`
--

CREATE TABLE `db_videoliked` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videopayouts`
--

CREATE TABLE `db_videopayouts` (
  `p_id` int(12) UNSIGNED NOT NULL,
  `usr_id` int(12) NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `p_startdate` date NOT NULL,
  `p_enddate` date NOT NULL,
  `p_paid` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_paydate` datetime NOT NULL,
  `p_amount` float UNSIGNED NOT NULL,
  `p_amount_shared` float UNSIGNED NOT NULL,
  `p_views` int(12) NOT NULL,
  `p_custom` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `p_state` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `p_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videoplaylists`
--

CREATE TABLE `db_videoplaylists` (
  `pl_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED NOT NULL,
  `pl_key` int(10) UNSIGNED NOT NULL,
  `pl_name` tinytext NOT NULL,
  `pl_descr` tinytext NOT NULL,
  `pl_tags` tinytext NOT NULL,
  `pl_privacy` varchar(10) NOT NULL DEFAULT 'public',
  `pl_date` datetime NOT NULL,
  `pl_views` int(10) UNSIGNED NOT NULL,
  `pl_embed` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_email` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_social` tinyint(1) UNSIGNED NOT NULL DEFAULT 1,
  `pl_thumb` int(10) UNSIGNED NOT NULL,
  `pl_files` text NOT NULL,
  `pl_active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videoque`
--

CREATE TABLE `db_videoque` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videorating`
--

CREATE TABLE `db_videorating` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `file_votes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videoresponses`
--

CREATE TABLE `db_videoresponses` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `file_response` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videosubs`
--

CREATE TABLE `db_videosubs` (
  `sub_id` int(5) UNSIGNED NOT NULL,
  `file_key` int(10) UNSIGNED NOT NULL,
  `vjs_subs` text NOT NULL,
  `jw_subs` text NOT NULL,
  `fp_subs` text NOT NULL,
  `sub_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videotransfers`
--

CREATE TABLE `db_videotransfers` (
  `q_id` int(10) UNSIGNED NOT NULL,
  `upload_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `thumb_server` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `file_key` int(10) UNSIGNED NOT NULL,
  `usr_key` int(10) UNSIGNED NOT NULL,
  `upload_start_time` datetime NOT NULL,
  `upload_end_time` datetime NOT NULL,
  `thumb_start_time` datetime NOT NULL,
  `thumb_end_time` datetime NOT NULL,
  `state` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `active` tinyint(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_videowatchlist`
--

CREATE TABLE `db_videowatchlist` (
  `db_id` int(10) UNSIGNED NOT NULL,
  `usr_id` int(10) UNSIGNED DEFAULT NULL,
  `file_key` int(10) UNSIGNED DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `db_vjsadentries`
--

CREATE TABLE `db_vjsadentries` (
  `ad_id` int(10) UNSIGNED NOT NULL,
  `ad_key` varchar(16) NOT NULL,
  `ad_name` varchar(50) NOT NULL,
  `ad_type` varchar(12) NOT NULL DEFAULT 'shared',
  `ad_position` varchar(10) NOT NULL,
  `ad_offset` float NOT NULL,
  `ad_duration` smallint(4) UNSIGNED NOT NULL,
  `ad_client` varchar(20) NOT NULL,
  `ad_format` varchar(10) NOT NULL,
  `ad_server` varchar(20) NOT NULL,
  `ad_file` varchar(32) NOT NULL,
  `ad_width` smallint(4) UNSIGNED NOT NULL DEFAULT 480,
  `ad_height` smallint(4) UNSIGNED NOT NULL DEFAULT 360,
  `ad_bitrate` smallint(4) UNSIGNED NOT NULL DEFAULT 300,
  `ad_tag` text NOT NULL,
  `ad_custom` varchar(32) NOT NULL,
  `ad_custom_url` varchar(255) NOT NULL,
  `ad_skip` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ad_comp_div` tinyint(1) NOT NULL DEFAULT 0,
  `ad_comp_id` varchar(50) NOT NULL,
  `ad_comp_w` smallint(4) UNSIGNED NOT NULL,
  `ad_comp_h` smallint(4) UNSIGNED NOT NULL,
  `ad_click_track` tinyint(1) NOT NULL DEFAULT 1,
  `ad_click_url` text NOT NULL,
  `ad_track_events` text NOT NULL,
  `ad_impressions` int(12) UNSIGNED NOT NULL,
  `ad_clicks` int(12) UNSIGNED NOT NULL,
  `ad_mobile` tinyint(1) NOT NULL DEFAULT 0,
  `ad_primary` tinyint(1) NOT NULL DEFAULT 0,
  `ad_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `db_accountuser`
--
ALTER TABLE `db_accountuser`
  ADD PRIMARY KEY (`usr_id`),
  ADD UNIQUE KEY `uni` (`usr_user`) USING BTREE,
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `usr_user` (`usr_user`),
  ADD KEY `usr_email` (`usr_email`),
  ADD KEY `usr_featured` (`usr_featured`),
  ADD KEY `usr_verified` (`usr_verified`),
  ADD KEY `usr_active` (`usr_active`),
  ADD KEY `usr_deleted` (`usr_deleted`),
  ADD KEY `usr_promoted` (`usr_promoted`),
  ADD KEY `usr_affiliate` (`usr_affiliate`),
  ADD KEY `usr_status` (`usr_status`),
  ADD KEY `usr_partner` (`usr_partner`);
ALTER TABLE `db_accountuser` ADD FULLTEXT KEY `search` (`ch_title`,`ch_tags`,`ch_dname`,`ch_user`);

--
-- Indexes for table `db_advbanners`
--
ALTER TABLE `db_advbanners`
  ADD PRIMARY KEY (`adv_id`),
  ADD KEY `adv_name` (`adv_name`),
  ADD KEY `adv_group` (`adv_group`),
  ADD KEY `adv_active` (`adv_active`),
  ADD KEY `adv_type` (`adv_type`);

--
-- Indexes for table `db_advgroups`
--
ALTER TABLE `db_advgroups`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `adv_name` (`adv_name`),
  ADD KEY `adv_width` (`adv_width`),
  ADD KEY `adv_height` (`adv_height`),
  ADD KEY `adv_rotate` (`adv_rotate`),
  ADD KEY `adv_active` (`adv_active`);

--
-- Indexes for table `db_audiocomments`
--
ALTER TABLE `db_audiocomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`file_key`,`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_audiofavorites`
--
ALTER TABLE `db_audiofavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_audiofiles`
--
ALTER TABLE `db_audiofiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_type` (`file_type`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `file_favorite` (`file_favorite`),
  ADD KEY `file_comments` (`file_comments`),
  ADD KEY `file_responses` (`file_responses`),
  ADD KEY `file_like` (`file_like`),
  ADD KEY `file_dislike` (`file_dislike`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_server` (`stream_server`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_key_active` (`stream_key_active`),
  ADD KEY `stream_chat` (`stream_chat`),
  ADD KEY `stream_vod` (`stream_vod`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `privacy` (`privacy`,`comments`,`comment_votes`,`comment_spam`,`rating`,`responding`,`embedding`,`social`,`approved`,`deleted`),
  ADD KEY `active` (`active`),
  ADD KEY `old_file_key` (`old_file_key`),
  ADD KEY `has_preview` (`has_preview`),
  ADD KEY `old_key` (`old_key`),
  ADD KEY `short` (`is_short`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_audiofiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_audiohistory`
--
ALTER TABLE `db_audiohistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_audioliked`
--
ALTER TABLE `db_audioliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_audiopayouts`
--
ALTER TABLE `db_audiopayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_audioplaylists`
--
ALTER TABLE `db_audioplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_audioplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_audioque`
--
ALTER TABLE `db_audioque`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_audiorating`
--
ALTER TABLE `db_audiorating`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_audioresponses`
--
ALTER TABLE `db_audioresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_audiosubs`
--
ALTER TABLE `db_audiosubs`
  ADD PRIMARY KEY (`sub_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `sub_active` (`sub_active`);

--
-- Indexes for table `db_audiotransfers`
--
ALTER TABLE `db_audiotransfers`
  ADD PRIMARY KEY (`q_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `db_audiowatchlist`
--
ALTER TABLE `db_audiowatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_banlist`
--
ALTER TABLE `db_banlist`
  ADD PRIMARY KEY (`ban_id`),
  ADD UNIQUE KEY `uni` (`ban_ip`),
  ADD KEY `ban_ip` (`ban_ip`),
  ADD KEY `ban_active` (`ban_active`);

--
-- Indexes for table `db_blogcomments`
--
ALTER TABLE `db_blogcomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`file_key`,`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_blogfavorites`
--
ALTER TABLE `db_blogfavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_blogfiles`
--
ALTER TABLE `db_blogfiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_type` (`file_type`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `file_favorite` (`file_favorite`),
  ADD KEY `file_comments` (`file_comments`),
  ADD KEY `file_responses` (`file_responses`),
  ADD KEY `file_like` (`file_like`),
  ADD KEY `file_dislike` (`file_dislike`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_server` (`stream_server`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_key_active` (`stream_key_active`),
  ADD KEY `stream_chat` (`stream_chat`),
  ADD KEY `stream_vod` (`stream_vod`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `privacy` (`privacy`,`comments`,`comment_votes`,`comment_spam`,`rating`,`responding`,`embedding`,`social`,`approved`,`deleted`),
  ADD KEY `active` (`active`),
  ADD KEY `old_file_key` (`old_file_key`),
  ADD KEY `has_preview` (`has_preview`),
  ADD KEY `old_key` (`old_key`),
  ADD KEY `short` (`is_short`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_blogfiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_bloghistory`
--
ALTER TABLE `db_bloghistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_blogliked`
--
ALTER TABLE `db_blogliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_blogpayouts`
--
ALTER TABLE `db_blogpayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_blogplaylists`
--
ALTER TABLE `db_blogplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_blogplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_blograting`
--
ALTER TABLE `db_blograting`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_blogresponses`
--
ALTER TABLE `db_blogresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_blogwatchlist`
--
ALTER TABLE `db_blogwatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_categories`
--
ALTER TABLE `db_categories`
  ADD PRIMARY KEY (`ct_id`),
  ADD KEY `ct_id` (`ct_id`),
  ADD KEY `ct_type` (`ct_type`),
  ADD KEY `ct_active` (`ct_active`),
  ADD KEY `ct_slug` (`ct_slug`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `ct_menu` (`ct_menu`),
  ADD KEY `tfa` (`ct_type`,`ct_featured`,`ct_active`);

--
-- Indexes for table `db_channelcomments`
--
ALTER TABLE `db_channelcomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_channelevents`
--
ALTER TABLE `db_channelevents`
  ADD PRIMARY KEY (`e_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `e_suspended` (`e_suspended`),
  ADD KEY `e_active` (`e_active`);

--
-- Indexes for table `db_conversion`
--
ALTER TABLE `db_conversion`
  ADD PRIMARY KEY (`cfg_id`);

--
-- Indexes for table `db_dashboard`
--
ALTER TABLE `db_dashboard`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uni` (`date`),
  ADD KEY `date` (`date`);

--
-- Indexes for table `db_doccomments`
--
ALTER TABLE `db_doccomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`file_key`,`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_docfavorites`
--
ALTER TABLE `db_docfavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_docfiles`
--
ALTER TABLE `db_docfiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_type` (`file_type`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `file_favorite` (`file_favorite`),
  ADD KEY `file_comments` (`file_comments`),
  ADD KEY `file_responses` (`file_responses`),
  ADD KEY `file_like` (`file_like`),
  ADD KEY `file_dislike` (`file_dislike`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_server` (`stream_server`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_key_active` (`stream_key_active`),
  ADD KEY `stream_chat` (`stream_chat`),
  ADD KEY `stream_vod` (`stream_vod`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `privacy` (`privacy`,`comments`,`comment_votes`,`comment_spam`,`rating`,`responding`,`embedding`,`social`,`approved`,`deleted`),
  ADD KEY `active` (`active`),
  ADD KEY `old_file_key` (`old_file_key`),
  ADD KEY `has_preview` (`has_preview`),
  ADD KEY `old_key` (`old_key`),
  ADD KEY `short` (`is_short`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_docfiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_dochistory`
--
ALTER TABLE `db_dochistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_docliked`
--
ALTER TABLE `db_docliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_docpayouts`
--
ALTER TABLE `db_docpayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_docplaylists`
--
ALTER TABLE `db_docplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_docplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_docque`
--
ALTER TABLE `db_docque`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_docrating`
--
ALTER TABLE `db_docrating`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_docresponses`
--
ALTER TABLE `db_docresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_doctransfers`
--
ALTER TABLE `db_doctransfers`
  ADD PRIMARY KEY (`q_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `db_docwatchlist`
--
ALTER TABLE `db_docwatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_fileplayers`
--
ALTER TABLE `db_fileplayers`
  ADD PRIMARY KEY (`db_id`);

--
-- Indexes for table `db_filetypemenu`
--
ALTER TABLE `db_filetypemenu`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `value` (`value`);

--
-- Indexes for table `db_followers`
--
ALTER TABLE `db_followers`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`sub_id`) USING BTREE,
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `umail` (`usr_id`,`mail_new_uploads`),
  ADD KEY `us` (`usr_id`,`sub_id`);

--
-- Indexes for table `db_fpadentries`
--
ALTER TABLE `db_fpadentries`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `ad_key` (`ad_key`),
  ADD KEY `ad_file` (`ad_file`),
  ADD KEY `ad_active` (`ad_active`);

--
-- Indexes for table `db_imagecomments`
--
ALTER TABLE `db_imagecomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`file_key`,`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_imagefavorites`
--
ALTER TABLE `db_imagefavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_imagefiles`
--
ALTER TABLE `db_imagefiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_type` (`file_type`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `file_favorite` (`file_favorite`),
  ADD KEY `file_comments` (`file_comments`),
  ADD KEY `file_responses` (`file_responses`),
  ADD KEY `file_like` (`file_like`),
  ADD KEY `file_dislike` (`file_dislike`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_server` (`stream_server`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_key_active` (`stream_key_active`),
  ADD KEY `stream_chat` (`stream_chat`),
  ADD KEY `stream_vod` (`stream_vod`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `privacy` (`privacy`,`comments`,`comment_votes`,`comment_spam`,`rating`,`responding`,`embedding`,`social`,`approved`,`deleted`),
  ADD KEY `active` (`active`),
  ADD KEY `old_file_key` (`old_file_key`),
  ADD KEY `has_preview` (`has_preview`),
  ADD KEY `old_key` (`old_key`),
  ADD KEY `short` (`is_short`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_imagefiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_imagehistory`
--
ALTER TABLE `db_imagehistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_imageliked`
--
ALTER TABLE `db_imageliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_imagepayouts`
--
ALTER TABLE `db_imagepayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_imageplaylists`
--
ALTER TABLE `db_imageplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_imageplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_imageque`
--
ALTER TABLE `db_imageque`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_imagerating`
--
ALTER TABLE `db_imagerating`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_imageresponses`
--
ALTER TABLE `db_imageresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_imagetransfers`
--
ALTER TABLE `db_imagetransfers`
  ADD PRIMARY KEY (`q_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `db_imagewatchlist`
--
ALTER TABLE `db_imagewatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_jwadcodes`
--
ALTER TABLE `db_jwadcodes`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `db_key` (`db_key`),
  ADD KEY `db_type` (`db_type`),
  ADD KEY `db_name` (`db_name`),
  ADD KEY `db_active` (`db_active`);

--
-- Indexes for table `db_jwadentries`
--
ALTER TABLE `db_jwadentries`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `ad_key` (`ad_key`),
  ADD KEY `ad_name` (`ad_name`),
  ADD KEY `ad_duration` (`ad_duration`),
  ADD KEY `ad_server` (`ad_server`),
  ADD KEY `ad_file` (`ad_file`),
  ADD KEY `ad_active` (`ad_active`),
  ADD KEY `ad_type` (`ad_type`);

--
-- Indexes for table `db_languages`
--
ALTER TABLE `db_languages`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`lang_id`),
  ADD KEY `lang_id` (`lang_id`),
  ADD KEY `lang_active` (`lang_active`);

--
-- Indexes for table `db_livechat`
--
ALTER TABLE `db_livechat`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`stream_id`,`chat_user`,`chat_ip`,`chat_fp`),
  ADD KEY `chat_id` (`chat_id`),
  ADD KEY `chat_user` (`chat_user`),
  ADD KEY `stream_id` (`stream_id`),
  ADD KEY `channel_id` (`channel_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `chat_fp` (`chat_fp`),
  ADD KEY `logged_in` (`logged_in`),
  ADD KEY `channel_owner` (`channel_owner`),
  ADD KEY `chat_time` (`chat_time`),
  ADD KEY `ft` (`channel_owner`,`chat_user`);

--
-- Indexes for table `db_livecomments`
--
ALTER TABLE `db_livecomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`file_key`,`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_livefavorites`
--
ALTER TABLE `db_livefavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_livefiles`
--
ALTER TABLE `db_livefiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_type` (`file_type`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `file_favorite` (`file_favorite`),
  ADD KEY `file_comments` (`file_comments`),
  ADD KEY `file_responses` (`file_responses`),
  ADD KEY `file_like` (`file_like`),
  ADD KEY `file_dislike` (`file_dislike`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_server` (`stream_server`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_chat` (`stream_chat`),
  ADD KEY `stream_vod` (`stream_vod`),
  ADD KEY `stream_key_active` (`stream_key_active`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `privacy` (`privacy`,`comments`,`comment_votes`,`comment_spam`,`rating`,`responding`,`embedding`,`social`,`approved`,`deleted`),
  ADD KEY `active` (`active`),
  ADD KEY `old_file_key` (`old_file_key`),
  ADD KEY `has_preview` (`has_preview`),
  ADD KEY `old_key` (`old_key`),
  ADD KEY `uid_ff` (`usr_id`,`file_flag`),
  ADD KEY `uid_apr` (`usr_id`,`approved`),
  ADD KEY `short` (`is_short`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_livefiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_livehistory`
--
ALTER TABLE `db_livehistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_liveliked`
--
ALTER TABLE `db_liveliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_livepayouts`
--
ALTER TABLE `db_livepayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_liveplaylists`
--
ALTER TABLE `db_liveplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_liveplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_liveque`
--
ALTER TABLE `db_liveque`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_liverating`
--
ALTER TABLE `db_liverating`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_liveresponses`
--
ALTER TABLE `db_liveresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_liveservers`
--
ALTER TABLE `db_liveservers`
  ADD PRIMARY KEY (`srv_id`),
  ADD KEY `srv_active` (`srv_active`),
  ADD KEY `srv_slug` (`srv_slug`),
  ADD KEY `srv_type` (`srv_type`),
  ADD KEY `srv_freespace` (`srv_freespace`);

--
-- Indexes for table `db_livetemps`
--
ALTER TABLE `db_livetemps`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `file_key` (`file_key`,`date`);

--
-- Indexes for table `db_livetoken`
--
ALTER TABLE `db_livetoken`
  ADD PRIMARY KEY (`tk_id`),
  ADD KEY `tk_active` (`tk_active`),
  ADD KEY `tk_slug` (`tk_slug`);

--
-- Indexes for table `db_liveviewers`
--
ALTER TABLE `db_liveviewers`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `fl` (`file_key`,`longip`),
  ADD KEY `ft` (`file_key`,`ts`);

--
-- Indexes for table `db_livewatchlist`
--
ALTER TABLE `db_livewatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_mailque`
--
ALTER TABLE `db_mailque`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `mail_type` (`mail_type`),
  ADD KEY `mail_key` (`mail_key`),
  ADD KEY `mail_from` (`mail_from`);

--
-- Indexes for table `db_messaging`
--
ALTER TABLE `db_messaging`
  ADD PRIMARY KEY (`msg_id`),
  ADD KEY `msg_from` (`msg_from`),
  ADD KEY `msg_to` (`msg_to`),
  ADD KEY `msg_invite` (`msg_invite`),
  ADD KEY `msg_active` (`msg_active`);

--
-- Indexes for table `db_notifications`
--
ALTER TABLE `db_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `date` (`date`,`seen`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `db_notifications_count`
--
ALTER TABLE `db_notifications_count`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`),
  ADD KEY `ua` (`usr_id`,`act_id`),
  ADD KEY `u` (`usr_id`);

--
-- Indexes for table `db_notifications_hidden`
--
ALTER TABLE `db_notifications_hidden`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`act_id`,`usr_id`),
  ADD KEY `act_id` (`act_id`,`usr_id`);

--
-- Indexes for table `db_packdiscounts`
--
ALTER TABLE `db_packdiscounts`
  ADD PRIMARY KEY (`dc_id`),
  ADD KEY `dc_code` (`dc_code`),
  ADD KEY `dc_amount` (`dc_amount`),
  ADD KEY `dc_active` (`dc_active`);

--
-- Indexes for table `db_packtypes`
--
ALTER TABLE `db_packtypes`
  ADD PRIMARY KEY (`pk_id`),
  ADD UNIQUE KEY `uni` (`pk_name`),
  ADD KEY `pk_name` (`pk_name`),
  ADD KEY `pk_price` (`pk_price`),
  ADD KEY `pk_active` (`pk_active`);

--
-- Indexes for table `db_packusers`
--
ALTER TABLE `db_packusers`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`pk_id`),
  ADD KEY `pk_id` (`pk_id`);

--
-- Indexes for table `db_servers`
--
ALTER TABLE `db_servers`
  ADD PRIMARY KEY (`server_id`),
  ADD KEY `last_used` (`last_used`),
  ADD KEY `server_type` (`server_type`),
  ADD KEY `url` (`url`),
  ADD KEY `lighttpd_url` (`lighttpd_url`),
  ADD KEY `s3_accesskey` (`s3_accesskey`),
  ADD KEY `s3_secretkey` (`s3_secretkey`),
  ADD KEY `s3_bucketname` (`s3_bucketname`),
  ADD KEY `s3_fileperm` (`s3_fileperm`),
  ADD KEY `cf_enabled` (`cf_enabled`),
  ADD KEY `cf_dist_type` (`cf_dist_type`),
  ADD KEY `cf_dist_domain` (`cf_dist_domain`),
  ADD KEY `cf_signed_url` (`cf_signed_url`),
  ADD KEY `cf_signed_expire` (`cf_signed_expire`),
  ADD KEY `cf_key_pair` (`cf_key_pair`),
  ADD KEY `cf_key_file` (`cf_key_file`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `db_settings`
--
ALTER TABLE `db_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cfg_name` (`cfg_name`),
  ADD KEY `cfg_data` (`cfg_data`(255));

--
-- Indexes for table `db_shortcomments`
--
ALTER TABLE `db_shortcomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_shortdl`
--
ALTER TABLE `db_shortdl`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_shortfavorites`
--
ALTER TABLE `db_shortfavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_shortfiles`
--
ALTER TABLE `db_shortfiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `active` (`active`),
  ADD KEY `usrv_tsrv` (`upload_server`,`thumb_server`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_shortfiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_shorthistory`
--
ALTER TABLE `db_shorthistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_shortliked`
--
ALTER TABLE `db_shortliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_shortpayouts`
--
ALTER TABLE `db_shortpayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_shortplaylists`
--
ALTER TABLE `db_shortplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_shortplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_shortque`
--
ALTER TABLE `db_shortque`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_shortrating`
--
ALTER TABLE `db_shortrating`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_shortresponses`
--
ALTER TABLE `db_shortresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_shortsubs`
--
ALTER TABLE `db_shortsubs`
  ADD PRIMARY KEY (`sub_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `sub_active` (`sub_active`);

--
-- Indexes for table `db_shorttransfers`
--
ALTER TABLE `db_shorttransfers`
  ADD PRIMARY KEY (`q_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `db_shortwatchlist`
--
ALTER TABLE `db_shortwatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_subinvoices`
--
ALTER TABLE `db_subinvoices`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`,`pay_date`,`sub_paid`),
  ADD KEY `create_date` (`create_date`);

--
-- Indexes for table `db_subpayouts`
--
ALTER TABLE `db_subpayouts`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`,`usr_id_to`,`is_paid`,`is_cancel`);

--
-- Indexes for table `db_subscribers`
--
ALTER TABLE `db_subscribers`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`sub_id`) USING BTREE,
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `sub_id` (`sub_id`),
  ADD KEY `umail` (`usr_id`,`mail_new_uploads`),
  ADD KEY `us` (`usr_id`,`sub_id`);

--
-- Indexes for table `db_subscriptions`
--
ALTER TABLE `db_subscriptions`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`);

--
-- Indexes for table `db_subtemps`
--
ALTER TABLE `db_subtemps`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`usr_id_to`,`pk_id`),
  ADD KEY `usr_id` (`usr_id`,`usr_id_to`,`pk_id`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `db_subtypes`
--
ALTER TABLE `db_subtypes`
  ADD PRIMARY KEY (`pk_id`),
  ADD UNIQUE KEY `uni` (`pk_name`),
  ADD KEY `pk_name` (`pk_name`),
  ADD KEY `pk_price` (`pk_price`),
  ADD KEY `pk_active` (`pk_active`);

--
-- Indexes for table `db_subusers`
--
ALTER TABLE `db_subusers`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`pk_id`,`usr_id_to`),
  ADD KEY `pk_id` (`pk_id`),
  ADD KEY `pk_total_live` (`pk_total_live`,`pk_total_blog`),
  ADD KEY `usr_id_to` (`usr_id_to`);

--
-- Indexes for table `db_tokendonations`
--
ALTER TABLE `db_tokendonations`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `tk_from` (`tk_from`,`tk_to`),
  ADD KEY `tk_active` (`tk_active`),
  ADD KEY `tk_paid` (`is_paid`);

--
-- Indexes for table `db_tokeninvoices`
--
ALTER TABLE `db_tokeninvoices`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`,`pay_date`,`tk_paid`),
  ADD KEY `create_date` (`create_date`);

--
-- Indexes for table `db_tokenpayments`
--
ALTER TABLE `db_tokenpayments`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`),
  ADD KEY `usr_id` (`usr_id`,`tk_id`),
  ADD KEY `tk_active` (`tk_active`);

--
-- Indexes for table `db_tokenpayouts`
--
ALTER TABLE `db_tokenpayouts`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`,`is_paid`,`tk_active`),
  ADD KEY `usr_key` (`usr_key`);

--
-- Indexes for table `db_trackactivity`
--
ALTER TABLE `db_trackactivity`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`);

--
-- Indexes for table `db_useractivity`
--
ALTER TABLE `db_useractivity`
  ADD PRIMARY KEY (`act_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `usr_id_to` (`usr_id_to`),
  ADD KEY `uuaa` (`usr_id`,`usr_id_to`,`act_visible`,`act_deleted`);

--
-- Indexes for table `db_usercodes`
--
ALTER TABLE `db_usercodes`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pwd_id` (`pwd_id`),
  ADD KEY `code_active` (`code_active`);

--
-- Indexes for table `db_usercontacts`
--
ALTER TABLE `db_usercontacts`
  ADD PRIMARY KEY (`ct_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`ct_username`,`ct_email`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `ct_name` (`ct_name`),
  ADD KEY `ct_friend` (`ct_friend`),
  ADD KEY `ct_blocked` (`ct_blocked`),
  ADD KEY `ct_active` (`ct_active`);

--
-- Indexes for table `db_userlabels`
--
ALTER TABLE `db_userlabels`
  ADD PRIMARY KEY (`lb_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`lb_name`,`lb_for`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `lb_name` (`lb_name`),
  ADD KEY `lb_active` (`lb_active`);

--
-- Indexes for table `db_usertypes`
--
ALTER TABLE `db_usertypes`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `db_name` (`db_name`),
  ADD KEY `db_active` (`db_active`);

--
-- Indexes for table `db_videocomments`
--
ALTER TABLE `db_videocomments`
  ADD PRIMARY KEY (`c_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `c_usr_id` (`c_usr_id`),
  ADD KEY `c_key` (`c_key`),
  ADD KEY `c_replyto` (`c_replyto`),
  ADD KEY `c_approved` (`c_approved`),
  ADD KEY `c_active` (`c_active`),
  ADD KEY `c_rating_value` (`c_rating_value`),
  ADD KEY `ms` (`file_key`,`c_usr_id`,`c_replyto`,`c_active`);

--
-- Indexes for table `db_videodl`
--
ALTER TABLE `db_videodl`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_videofavorites`
--
ALTER TABLE `db_videofavorites`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_videofiles`
--
ALTER TABLE `db_videofiles`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`embed_key`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `file_duration` (`file_duration`),
  ADD KEY `file_hd` (`file_hd`),
  ADD KEY `file_mobile` (`file_mobile`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `file_views` (`file_views`),
  ADD KEY `is_promoted` (`is_promoted`),
  ADD KEY `stream_key` (`stream_key`),
  ADD KEY `stream_live` (`stream_live`),
  ADD KEY `stream_ended` (`stream_ended`),
  ADD KEY `file_category` (`file_category`),
  ADD KEY `active` (`active`),
  ADD KEY `short` (`is_short`),
  ADD KEY `upada` (`usr_id`,`privacy`,`approved`,`deleted`,`active`),
  ADD KEY `ufpa` (`usr_id`,`file_views`,`privacy`,`active`);
ALTER TABLE `db_videofiles` ADD FULLTEXT KEY `file_title` (`file_title`);

--
-- Indexes for table `db_videohistory`
--
ALTER TABLE `db_videohistory`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_videoliked`
--
ALTER TABLE `db_videoliked`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_videopayouts`
--
ALTER TABLE `db_videopayouts`
  ADD PRIMARY KEY (`p_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`p_startdate`,`p_enddate`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `p_state` (`p_state`),
  ADD KEY `p_active` (`p_active`),
  ADD KEY `p_views` (`p_views`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `p_amount_shared` (`p_amount_shared`),
  ADD KEY `p_custom` (`p_custom`);

--
-- Indexes for table `db_videoplaylists`
--
ALTER TABLE `db_videoplaylists`
  ADD PRIMARY KEY (`pl_id`),
  ADD KEY `usr_id` (`usr_id`),
  ADD KEY `pl_key` (`pl_key`),
  ADD KEY `pl_privacy` (`pl_privacy`),
  ADD KEY `pl_views` (`pl_views`),
  ADD KEY `pl_thumb` (`pl_thumb`),
  ADD KEY `pl_active` (`pl_active`);
ALTER TABLE `db_videoplaylists` ADD FULLTEXT KEY `full_title` (`pl_name`,`pl_tags`);

--
-- Indexes for table `db_videoque`
--
ALTER TABLE `db_videoque`
  ADD PRIMARY KEY (`q_id`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`);

--
-- Indexes for table `db_videorating`
--
ALTER TABLE `db_videorating`
  ADD PRIMARY KEY (`db_id`),
  ADD KEY `file_key` (`file_key`);

--
-- Indexes for table `db_videoresponses`
--
ALTER TABLE `db_videoresponses`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`file_key`,`file_response`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`),
  ADD KEY `fa` (`file_key`,`active`);

--
-- Indexes for table `db_videosubs`
--
ALTER TABLE `db_videosubs`
  ADD PRIMARY KEY (`sub_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `sub_active` (`sub_active`);

--
-- Indexes for table `db_videotransfers`
--
ALTER TABLE `db_videotransfers`
  ADD PRIMARY KEY (`q_id`),
  ADD UNIQUE KEY `uni` (`file_key`),
  ADD KEY `upload_server` (`upload_server`),
  ADD KEY `thumb_server` (`thumb_server`),
  ADD KEY `file_key` (`file_key`),
  ADD KEY `usr_key` (`usr_key`),
  ADD KEY `state` (`state`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `db_videowatchlist`
--
ALTER TABLE `db_videowatchlist`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `uni` (`usr_id`,`file_key`),
  ADD KEY `u` (`usr_id`),
  ADD KEY `uf` (`usr_id`,`file_key`);

--
-- Indexes for table `db_vjsadentries`
--
ALTER TABLE `db_vjsadentries`
  ADD PRIMARY KEY (`ad_id`),
  ADD KEY `ad_key` (`ad_key`),
  ADD KEY `ad_name` (`ad_name`),
  ADD KEY `ad_duration` (`ad_duration`),
  ADD KEY `ad_server` (`ad_server`),
  ADD KEY `ad_file` (`ad_file`),
  ADD KEY `ad_mobile` (`ad_mobile`),
  ADD KEY `ad_active` (`ad_active`),
  ADD KEY `ad_custom` (`ad_custom`),
  ADD KEY `ad_custom_url` (`ad_custom_url`),
  ADD KEY `ad_skip` (`ad_skip`),
  ADD KEY `ad_type` (`ad_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `db_accountuser`
--
ALTER TABLE `db_accountuser`
  MODIFY `usr_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_advbanners`
--
ALTER TABLE `db_advbanners`
  MODIFY `adv_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_advgroups`
--
ALTER TABLE `db_advgroups`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `db_audiocomments`
--
ALTER TABLE `db_audiocomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiofavorites`
--
ALTER TABLE `db_audiofavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiofiles`
--
ALTER TABLE `db_audiofiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiohistory`
--
ALTER TABLE `db_audiohistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audioliked`
--
ALTER TABLE `db_audioliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiopayouts`
--
ALTER TABLE `db_audiopayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audioplaylists`
--
ALTER TABLE `db_audioplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audioque`
--
ALTER TABLE `db_audioque`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiorating`
--
ALTER TABLE `db_audiorating`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audioresponses`
--
ALTER TABLE `db_audioresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiosubs`
--
ALTER TABLE `db_audiosubs`
  MODIFY `sub_id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiotransfers`
--
ALTER TABLE `db_audiotransfers`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_audiowatchlist`
--
ALTER TABLE `db_audiowatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_banlist`
--
ALTER TABLE `db_banlist`
  MODIFY `ban_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogcomments`
--
ALTER TABLE `db_blogcomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogfavorites`
--
ALTER TABLE `db_blogfavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogfiles`
--
ALTER TABLE `db_blogfiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_bloghistory`
--
ALTER TABLE `db_bloghistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogliked`
--
ALTER TABLE `db_blogliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogpayouts`
--
ALTER TABLE `db_blogpayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogplaylists`
--
ALTER TABLE `db_blogplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blograting`
--
ALTER TABLE `db_blograting`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogresponses`
--
ALTER TABLE `db_blogresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_blogwatchlist`
--
ALTER TABLE `db_blogwatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_categories`
--
ALTER TABLE `db_categories`
  MODIFY `ct_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `db_channelcomments`
--
ALTER TABLE `db_channelcomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_channelevents`
--
ALTER TABLE `db_channelevents`
  MODIFY `e_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_conversion`
--
ALTER TABLE `db_conversion`
  MODIFY `cfg_id` smallint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT for table `db_dashboard`
--
ALTER TABLE `db_dashboard`
  MODIFY `id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_doccomments`
--
ALTER TABLE `db_doccomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docfavorites`
--
ALTER TABLE `db_docfavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docfiles`
--
ALTER TABLE `db_docfiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_dochistory`
--
ALTER TABLE `db_dochistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docliked`
--
ALTER TABLE `db_docliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docpayouts`
--
ALTER TABLE `db_docpayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docplaylists`
--
ALTER TABLE `db_docplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docque`
--
ALTER TABLE `db_docque`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docrating`
--
ALTER TABLE `db_docrating`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docresponses`
--
ALTER TABLE `db_docresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_doctransfers`
--
ALTER TABLE `db_doctransfers`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_docwatchlist`
--
ALTER TABLE `db_docwatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_fileplayers`
--
ALTER TABLE `db_fileplayers`
  MODIFY `db_id` smallint(4) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `db_filetypemenu`
--
ALTER TABLE `db_filetypemenu`
  MODIFY `db_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_followers`
--
ALTER TABLE `db_followers`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_fpadentries`
--
ALTER TABLE `db_fpadentries`
  MODIFY `ad_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagecomments`
--
ALTER TABLE `db_imagecomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagefavorites`
--
ALTER TABLE `db_imagefavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagefiles`
--
ALTER TABLE `db_imagefiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagehistory`
--
ALTER TABLE `db_imagehistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imageliked`
--
ALTER TABLE `db_imageliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagepayouts`
--
ALTER TABLE `db_imagepayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imageplaylists`
--
ALTER TABLE `db_imageplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imageque`
--
ALTER TABLE `db_imageque`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagerating`
--
ALTER TABLE `db_imagerating`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imageresponses`
--
ALTER TABLE `db_imageresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagetransfers`
--
ALTER TABLE `db_imagetransfers`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_imagewatchlist`
--
ALTER TABLE `db_imagewatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_jwadcodes`
--
ALTER TABLE `db_jwadcodes`
  MODIFY `db_id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `db_jwadentries`
--
ALTER TABLE `db_jwadentries`
  MODIFY `ad_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_languages`
--
ALTER TABLE `db_languages`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `db_livechat`
--
ALTER TABLE `db_livechat`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livecomments`
--
ALTER TABLE `db_livecomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livefavorites`
--
ALTER TABLE `db_livefavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livefiles`
--
ALTER TABLE `db_livefiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livehistory`
--
ALTER TABLE `db_livehistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_liveliked`
--
ALTER TABLE `db_liveliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livepayouts`
--
ALTER TABLE `db_livepayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_liveplaylists`
--
ALTER TABLE `db_liveplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_liveque`
--
ALTER TABLE `db_liveque`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_liverating`
--
ALTER TABLE `db_liverating`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_liveresponses`
--
ALTER TABLE `db_liveresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_liveservers`
--
ALTER TABLE `db_liveservers`
  MODIFY `srv_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livetemps`
--
ALTER TABLE `db_livetemps`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livetoken`
--
ALTER TABLE `db_livetoken`
  MODIFY `tk_id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `db_liveviewers`
--
ALTER TABLE `db_liveviewers`
  MODIFY `db_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_livewatchlist`
--
ALTER TABLE `db_livewatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_mailque`
--
ALTER TABLE `db_mailque`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_messaging`
--
ALTER TABLE `db_messaging`
  MODIFY `msg_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_notifications`
--
ALTER TABLE `db_notifications`
  MODIFY `id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_notifications_count`
--
ALTER TABLE `db_notifications_count`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_notifications_hidden`
--
ALTER TABLE `db_notifications_hidden`
  MODIFY `db_id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `db_packdiscounts`
--
ALTER TABLE `db_packdiscounts`
  MODIFY `dc_id` mediumint(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_packtypes`
--
ALTER TABLE `db_packtypes`
  MODIFY `pk_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `db_packusers`
--
ALTER TABLE `db_packusers`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_servers`
--
ALTER TABLE `db_servers`
  MODIFY `server_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_settings`
--
ALTER TABLE `db_settings`
  MODIFY `id` int(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=841;

--
-- AUTO_INCREMENT for table `db_shortcomments`
--
ALTER TABLE `db_shortcomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortdl`
--
ALTER TABLE `db_shortdl`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortfavorites`
--
ALTER TABLE `db_shortfavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortfiles`
--
ALTER TABLE `db_shortfiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shorthistory`
--
ALTER TABLE `db_shorthistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortliked`
--
ALTER TABLE `db_shortliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortpayouts`
--
ALTER TABLE `db_shortpayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortplaylists`
--
ALTER TABLE `db_shortplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortque`
--
ALTER TABLE `db_shortque`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortrating`
--
ALTER TABLE `db_shortrating`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortresponses`
--
ALTER TABLE `db_shortresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortsubs`
--
ALTER TABLE `db_shortsubs`
  MODIFY `sub_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shorttransfers`
--
ALTER TABLE `db_shorttransfers`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_shortwatchlist`
--
ALTER TABLE `db_shortwatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subinvoices`
--
ALTER TABLE `db_subinvoices`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subpayouts`
--
ALTER TABLE `db_subpayouts`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subscribers`
--
ALTER TABLE `db_subscribers`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subscriptions`
--
ALTER TABLE `db_subscriptions`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subtemps`
--
ALTER TABLE `db_subtemps`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_subtypes`
--
ALTER TABLE `db_subtypes`
  MODIFY `pk_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `db_subusers`
--
ALTER TABLE `db_subusers`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_tokendonations`
--
ALTER TABLE `db_tokendonations`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_tokeninvoices`
--
ALTER TABLE `db_tokeninvoices`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_tokenpayments`
--
ALTER TABLE `db_tokenpayments`
  MODIFY `db_id` int(9) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_tokenpayouts`
--
ALTER TABLE `db_tokenpayouts`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_trackactivity`
--
ALTER TABLE `db_trackactivity`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `db_useractivity`
--
ALTER TABLE `db_useractivity`
  MODIFY `act_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_usercodes`
--
ALTER TABLE `db_usercodes`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_usercontacts`
--
ALTER TABLE `db_usercontacts`
  MODIFY `ct_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_userlabels`
--
ALTER TABLE `db_userlabels`
  MODIFY `lb_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_usertypes`
--
ALTER TABLE `db_usertypes`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `db_videocomments`
--
ALTER TABLE `db_videocomments`
  MODIFY `c_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videodl`
--
ALTER TABLE `db_videodl`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videofavorites`
--
ALTER TABLE `db_videofavorites`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videofiles`
--
ALTER TABLE `db_videofiles`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videohistory`
--
ALTER TABLE `db_videohistory`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videoliked`
--
ALTER TABLE `db_videoliked`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videopayouts`
--
ALTER TABLE `db_videopayouts`
  MODIFY `p_id` int(12) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videoplaylists`
--
ALTER TABLE `db_videoplaylists`
  MODIFY `pl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videoque`
--
ALTER TABLE `db_videoque`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videorating`
--
ALTER TABLE `db_videorating`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videoresponses`
--
ALTER TABLE `db_videoresponses`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videosubs`
--
ALTER TABLE `db_videosubs`
  MODIFY `sub_id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videotransfers`
--
ALTER TABLE `db_videotransfers`
  MODIFY `q_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_videowatchlist`
--
ALTER TABLE `db_videowatchlist`
  MODIFY `db_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `db_vjsadentries`
--
ALTER TABLE `db_vjsadentries`
  MODIFY `ad_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
