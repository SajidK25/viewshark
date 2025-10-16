<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

// entries are in seconds

$one_hour = 3600;

$cfg['cache_browse_promoted']          = $one_hour; //browse page, promoted
$cfg['cache_browse_main']              = $one_hour; //browse page, main listing
$cfg['cache_browse_categories_menu']   = $one_hour; //browse page, categories menu
$cfg['cache_dashboard_weekstats']      = $one_hour * 6; //admin dashboard
$cfg['cache_dashboard_filecounts']     = $one_hour * 6; //admin dashboard
$cfg['cache_signed_thumbnails']        = $one_hour * 6; //used in VGenerate::thumbSigned()
$cfg['cache_file_url']                 = $one_hour * 6; //used in VGenerate::fileURL()
$cfg['cache_key_check']                = $one_hour * 3; //used in files management
$cfg['cache_user_files_favorites']     = $one_hour; //file manager, favorites
$cfg['cache_user_files_liked']         = $one_hour; //file manager, liked
$cfg['cache_user_files_history']       = $one_hour; //file manager, history
$cfg['cache_user_files_watchlist']     = $one_hour; //file manager, watchlist
$cfg['cache_user_files_subs_follows']  = $one_hour; //VFiles::userSubs
$cfg['cache_view_current']             = $one_hour * 6; //current video details, used in VView::viewLayout() and VHref::getPageMeta()
$cfg['cache_view_related']             = $one_hour * 6; //current video related, VView::sideColumn()
$cfg['cache_view_responses']           = $one_hour * 6; //current video responses, used in VView::viewLayout()
$cfg['cache_view_pl_privacy']          = $one_hour * 6; //playlist privacy
$cfg['cache_view_sub_id']              = $one_hour; //user subscriber id
$cfg['cache_view_user_id']             = $one_hour * 24; //updateViewLogs user id
$cfg['cache_view_user_key']            = $one_hour * 24; //updateViewLogs user key
$cfg['cache_view_check_perm']          = $one_hour * 3; //VUseraccount::checkPerm
$cfg['cache_view_friend_status']       = $one_hour * 3; //VContacts::getFriendStatus
$cfg['cache_view_block_status']        = $one_hour * 3; //VContacts::getBlockCfg
$cfg['cache_view_block_cfg']           = $one_hour * 3; //VContacts::getBlockCfg
$cfg['cache_view_playlist_entries']    = $one_hour * 3; //VView::runningPlaylist
$cfg['cache_view_response_entries']    = $one_hour * 3; //VResponses::viewFileResponses
$cfg['cache_view_comments']            = $one_hour; //VComments::listFileComments
$cfg['cache_view_comments_c_usr_id']   = $one_hour * 3; //VComments::listFileComments
$cfg['cache_view_template_file_info']  = $one_hour * 3; //VView::getFileInfo
$cfg['cache_respond_file_list']        = $one_hour; //VResponses::responseSelect
$cfg['cache_files_playlist_key']       = $one_hour * 3; //VFiles::viewModes
$cfg['cache_home_promoted']            = $one_hour * 3; //VHome::htmlSlides()
$cfg['cache_home_featured_media']      = $one_hour * 3; //VHome::featuredMedia()
$cfg['cache_home_featured_channels']   = $one_hour * 3; //VHome::featuredChannels()
$cfg['cache_home_subs_follows']        = $one_hour; //VHome::featuredMedia()
$cfg['cache_playlist_details_deleted'] = $one_hour * 6; //VFiles::listPlaylistDetails()
$cfg['cache_playlist_details_tmbsrv']  = $one_hour * 6; //VFiles::listPlaylistDetails()
$cfg['cache_playlist_details_meta']    = $one_hour * 3; //VHref::getPageMeta()
$cfg['cache_channel_userinfo']         = $one_hour * 3; //VChannel::channelLayout()
$cfg['cache_channel_channeltypes']     = $one_hour * 3; //VChannel::getChannelTypes()
$cfg['cache_channel_ch_cfg']           = $one_hour; //VChannel::__construct()
$cfg['cache_channel_activity']         = $one_hour; //VChannel::activityTimeline() and //VChannel::listUserActivities()
$cfg['cache_channels_main']            = $one_hour; //VChannels::getChannels()
$cfg['cache_channels_promoted']        = $one_hour; //VChannels::getPromoted()
$cfg['cache_channels_about']           = $one_hour; //VChannels::aboutPage()
