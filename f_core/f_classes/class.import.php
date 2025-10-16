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
use Embed\Embed;

class VImport
{
    /* import feeds layout */
    public function videoFeeds_layout()
    {
        global $cfg, $language, $side, $db, $smarty;

        $err = false;
        if ($cfg['paid_memberships'] == 1) {
            $err = VUpload::subscriptionCheck('video');
        }

        $categ_list         = $language['backend.import.feed.categ.list'];
        $region_list        = explode(',', $language['backend.import.feed.region.list']);
        $type_list          = explode(',', $language['backend.import.feed.type.list']);
        $time_list          = explode(',', $language['backend.import.feed.time.list']);
        $results_list       = explode(',', $language['backend.import.feed.results.list']);
        $results_ch_list    = explode(',', $language['backend.import.feed.results.channel.list']);
        $feed_ch_list       = explode(',', $language['backend.import.feed.results.feed.list']);
        $feed_vimeo_list    = explode(',', $language['backend.import.feed.results.vimeo.list']);
        $results_vimeo_list = explode(',', $language['backend.import.feed.results.vimeo.feed']);
        $feed_dm_list       = explode(',', $language['backend.import.feed.results.dm.list']);
        $results_dm_list    = explode(',', $language['backend.import.feed.results.dm.feed']);
        $type_list_dm       = explode(',', $language['backend.import.feed.dm.filters.list']);
        $categ_dm_list      = explode(',', $language['backend.import.feed.categ.dm.list']);
        $sort_list_dm       = explode(',', $language['backend.import.feed.dm.sort.list']);
        $region_list_dm     = explode(',', $language['backend.import.feed.region.dm.list']);
        $search_on_list     = explode(',', $language['backend.import.feed.search.list']);
        $feed_def_list      = explode(',', $language['backend.import.feed.type.def.list']);
        $feed_dim_list      = explode(',', $language['backend.import.feed.type.dim.list']);
        $feed_dur_list      = explode(',', $language['backend.import.feed.type.dur.list']);
        $feed_emb_list      = explode(',', $language['backend.import.feed.type.emb.list']);
        $feed_lic_list      = explode(',', $language['backend.import.feed.type.lic.list']);
        $feed_syn_list      = explode(',', $language['backend.import.feed.type.syn.list']);
        $feed_type_list     = explode(',', $language['backend.import.feed.type.type.list']);

        $yt_channel_list = explode("\n", $cfg['import_yt_channel_list']);
        $dm_channel_list = explode("\n", $cfg['import_dm_user_list']);
        $vi_channel_list = explode("\n", $cfg['import_vi_user_list']);
        /* many select lists */
        $category_list    = self::getCategoryList();
        $def_ht           = self::selectList('feed_def', $feed_def_list, 1);
        $dim_ht           = self::selectList('feed_dim', $feed_dim_list, 1);
        $dur_ht           = self::selectList('feed_dur', $feed_dur_list, 1);
        $emb_ht           = self::selectList('feed_emb', $feed_emb_list, 1);
        $lic_ht           = self::selectList('feed_lic', $feed_lic_list, 1);
        $syn_ht           = self::selectList('feed_syn', $feed_syn_list, 1);
        $type_ht          = self::selectList('feed_type', $feed_type_list, 1);
        $categ_ht         = self::selectList('feed_categ', $categ_list, 1);
        $region_ht        = self::selectList('feed_region', $region_list, 1);
        $time_ht          = self::selectList('feed_time', $time_list, 0);
        $results_ht       = self::selectList('feed_results', $results_list, 0);
        $channel_ht       = self::selectList('feed_channel_yt', $yt_channel_list, 0);
        $channel_dm_ht    = self::selectList('feed_channel_dm', $dm_channel_list, 0);
        $channel_vi_ht    = self::selectList('feed_channel_vimeo', $vi_channel_list, 0);
        $results_ch_ht    = self::selectList('feed_results_ch', $results_ch_list, 0);
        $feed_ch_ht       = self::selectList('feed_type_ch', $feed_ch_list, 0);
        $category_feed    = self::selectList('assign_category_feed', $category_list, 1);
        $category_ch      = self::selectList('assign_category_channel', $category_list, 1);
        $category_file    = self::selectList('assign_category_file', $category_list, 1);
        $category_dm      = self::selectList('assign_category_dm', $category_list, 1);
        $category_vimeo   = self::selectList('assign_category_vimeo', $category_list, 1);
        $category_dm_type = self::selectList('assign_category_dm_type', $category_list, 1);
        $category_dm_ht   = self::selectList('assign_category_dm_video', $categ_dm_list, 1);
        $type_dm_ht       = self::selectList('feed_type_dm_video', $type_list_dm, 1);
        $sort_dm_ht       = self::selectList('feed_sort_dm_video', $sort_list_dm, 1);

        $feed_vimeo_ht      = self::selectList('feed_type_vimeo', $feed_vimeo_list, 0);
        $results_vimeo_ht   = self::selectList('feed_results_vimeo', $results_vimeo_list, 0);
        $feed_dm_ht         = self::selectList('feed_type_dm', $feed_dm_list, 0);
        $results_dm_ht      = self::selectList('feed_results_dm', $results_dm_list, 0);
        $region_dm_ht       = self::selectList('feed_region_dm', $region_list_dm, 1);
        $results_dm_type_ht = self::selectList('feed_results_dm_type', $results_dm_list, 0);
        $search_ht          = self::selectList('search_on', $search_on_list, 0);

        $all = ($cfg['import_yt'] == 0 and $cfg['import_dm'] == 0 and $cfg['import_vi'] == 0) ? 0 : 1;
        if ($side == 'frontend') {
            $uid  = intval($_SESSION['USER_ID']);
            $p    = $db->execute(sprintf("SELECT `usr_perm` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $uid));
            $perm = unserialize($p->fields['usr_perm']);
        }
        /* layout start */
        $html = $side == 'frontend' ? '
            <div class="tabs tabs-style-line no-display">
            <nav>
                <ul>
                    ' . (($cfg['video_module'] == 1 and $cfg['video_uploads'] == 1) ? '<li><a class="icon icon-video" href="' . $cfg['main_url'] . '/' . VHref::getKey('upload') . '?t=video"><span>' . $language['upload.menu.video'] . '</span></a></li>' : null) . '
                    ' . (($cfg['video_module'] == 1 and $cfg['video_uploads'] == 1) ? '<li><a class="icon icon-video" href="' . $cfg['main_url'] . '/' . VHref::getKey('upload') . '?t=short"><span>' . $language['upload.menu.video'] . '</span></a></li>' : null) . '
                    ' . (($cfg['import_yt'] == 1 or $cfg['import_dm'] == 1 or $cfg['import_vi'] == 1) ? '<li class="tab-current"><a class="icon icon-video" href="' . $cfg['main_url'] . '/' . VHref::getKey('import') . '?t=video"><span>' . $language['upload.menu.grab'] . '</span></a></li>' : null) . '
                    ' . (($cfg['image_module'] == 1 and $cfg['image_uploads'] == 1) ? '<li><a class="icon icon-image" href="' . $cfg['main_url'] . '/' . VHref::getKey('upload') . '?t=image"><span>' . $language['upload.menu.image'] . '</span></a></li>' : null) . '
                    ' . (($cfg['audio_module'] == 1 and $cfg['audio_uploads'] == 1) ? '<li><a class="icon icon-headphones" href="' . $cfg['main_url'] . '/' . VHref::getKey('upload') . '?t=audio"><span>' . $language['upload.menu.audio'] . '</span></a></li>' : null) . '
                    ' . (($cfg['document_module'] == 1 and $cfg['document_uploads'] == 1) ? '<li><a class="icon icon-file" href="' . $cfg['main_url'] . '/' . VHref::getKey('upload') . '?t=document"><span>' . $language['upload.menu.document'] . '</span></a></li>' : null) . '
                </ul>
            </nav>
            </div>
            ' . ($err ? '<br>' . VGenerate::noticeTpl('', $err, '') : null) . '
            <div class="clearfix"></div>
           ' : '';

        $html .= '<div id="import-wrap" class="container' . ($side == 'frontend' ? '-off' : null) . ' cbp-spmenu-push vs-column">';
        $html .= '<div class="vs-column half">';
        $html .= '<div class="left-float wd380 all-paddings10 entry-list" id="left-side"' . ($side == 'frontend' ? ' style="padding: 15px 0;"' : null) . '>';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        /* embed single file */
        if ($side == 'backend' or ($side == 'frontend' and intval($perm['perm_embed_single']) == 1 and !$err)) {
            if ($all == 1) {
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head jc-sb active">';
                $html .= VGenerate::simpleDivWrap('top-padding5 wd90p all-paddings5 bold pointer', '', '<i class="icon-link"></i> ' . $language['backend.import.embed.h1'], '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel active">';
                $html .= '<form id="embed-file-form" method="post" action="" class="entry-form-class">';
                $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.embed.youtube'] . '</label>', 'left-float', 'be_video_url', 'text-input wd190', '');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.assign.c'] . '</label><div class="left-float selector">' . $category_file . '</div>');
                $html .= $side == 'backend' ? VGenerate::simpleDivWrap('row top-padding15', '', '<label>' . $language['backend.import.feed.assign'] . '</label><div class="left-float selector">' . self::username_selectList('import_single') . '</div>') : null;
                $html .= VGenerate::simpleDivWrap('row', '', '<div class="clearfix"></div><br>' . VGenerate::basicInput('button', 'find_video_file', 'button-grey search-button form-button save-entry-button find-video-file', '', 'embed', '<span>' . $language['backend.embed.video.find'] . '</span>'));
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</li>';
            } else {
                $html .= VGenerate::simpleDivWrap('row', '', $language['backend.import.menu.disabled.functions']);
            }
        }
        if ($cfg['import_yt'] == 1) {
            if ($side == 'backend' or ($side == 'frontend' and intval($perm['perm_embed_yt_video']) == 1 and !$err)) {
                $resnr = ((int) $_POST['yt_video_results'] > 0) ? (int) $_POST['yt_video_results'] : 10;
                /* import youtube video feeds */
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head jc-sb">';
                $html .= VGenerate::simpleDivWrap('top-padding5 wd90p all-paddings5 bold pointer', '', '<i class="icon-youtube"></i> ' . $language['backend.import.feed.h1'], '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<form id="video-feed-form" method="post" action="" class="entry-form-class">';
                $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.import.search.for'] . '</label>', 'left-float', 'be_search_term', 'text-input wd190', '');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.categ'] . '</label><div class="left-float selector">' . $categ_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.def'] . '</label><div class="left-float selector">' . $def_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.dim'] . '</label><div class="left-float selector">' . $dim_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.dur'] . '</label><div class="left-float selector">' . $dur_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.emb'] . '</label><div class="left-float selector">' . $emb_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.lic'] . '</label><div class="left-float selector">' . $lic_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.syn'] . '</label><div class="left-float selector">' . $syn_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type.type'] . '</label><div class="left-float selector">' . $type_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row no-display', '', VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', $language['backend.import.feed.type.res'], 'left-float', 'yt_video_results', 'text-input wd190', $resnr));
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.prevpage'] . '</label><span class="left-float lh20 prev-page">##prevPageToken##</span>', 'float: left;');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.nextpage'] . '</label><span class="left-float lh20 next-page">##nextPageToken##</span>', 'float: right;');
                $html .= '<div class="clearfix"></div><br>';
                $html .= VGenerate::simpleDivWrap('row no-display', '', '<div class="left-float lh20 wd140">' . $language['backend.import.currpage'] . '</div><div class="left-float lh20 current-page">##currentPageToken##</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.assign.c'] . '</label><div class="left-float selector">' . $category_feed . '</div>');
                $html .= $side == 'backend' ? VGenerate::simpleDivWrap('row top-padding15', '', '<label>' . $language['backend.import.feed.assign'] . '</label><div class="left-float selector">' . self::username_selectList('import_yt_video') . '</div>') : null;
                $html .= self::selectEntries('list_video_feed', 'import_video_feed', true);
                $html .= VGenerate::simpleDivWrap('row no-display', '', '<br>' . VGenerate::basicInput('button', $btn1, 'search-button form-button ' . str_replace('_', '-', 'list_video_feed_yt'), '', 'import', $language['backend.import.feed.list']));
                $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140 no-display', 'form', 'left-float no-display', 'list_video_feed_hid', 'text-input wd190 list-video-feed-hid', 'video-feed-form');
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</li>';
            }
            if ($side == 'backend' or ($side == 'frontend' and intval($perm['perm_embed_yt_channel']) == 1 and !$err)) {
                $resnr = ((int) $_POST['yt_channel_results'] > 0) ? (int) $_POST['yt_channel_results'] : 10;
                /* import youtube channel feeds */
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head jc-sb">';
                $html .= VGenerate::simpleDivWrap('top-padding5 wd90p all-paddings5 bold pointer', '', '<i class="icon-youtube"></i> ' . $language['backend.import.channel.h1'], '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<form id="channel-feed-form" method="post" action="" class="entry-form-class">';
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.channel'] . '</label><div class="left-float selector">' . $channel_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row no-display', '', VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', $language['backend.import.feed.type.res'], 'left-float', 'yt_channel_results', 'text-input wd190', $resnr));
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.prevpage'] . '</label><span class="left-float lh20 prev-page">##prevPageToken##</span>', 'float: left;');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.nextpage'] . '</label><span class="left-float lh20 next-page">##nextPageToken##</span>', 'float: right;');
                $html .= '<div class="clearfix"></div><br>';
                $html .= VGenerate::simpleDivWrap('row no-display', '', '<div class="left-float lh20 wd140">' . $language['backend.import.currpage'] . '</div><div class="left-float lh20 current-page">##currentPageToken##</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.assign.c'] . '</label><div class="left-float selector">' . $category_ch . '</div>');
                $html .= $side == 'backend' ? VGenerate::simpleDivWrap('row top-padding15', '', '<label>' . $language['backend.import.feed.assign'] . '</label><div class="left-float selector">' . self::username_selectList('import_yt_channel') . '</div>') : null;
                $html .= self::selectEntries('list_channel_feed', 'import_channel_feed', true);
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
        if ($cfg['import_vi'] == 1) {
            if ($side == 'backend' or ($side == 'frontend' and intval($perm['perm_embed_vi_user']) == 1 and !$err)) {
                /* import vimeo user feeds */
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head jc-sb">';
                $html .= VGenerate::simpleDivWrap('top-padding5 wd90p all-paddings5 bold pointer', '', '<i class="icon-vimeo"></i> ' . $language['backend.import.vimeo.h1'], '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<form id="vimeo-user-form" method="post" action="" class="entry-form-class">';
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.user'] . '</label><div class="left-float selector">' . $channel_vi_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type'] . '</label><div class="left-float selector">' . $feed_vimeo_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.results'] . '</label><div class="left-float selector">' . $results_vimeo_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.assign.c'] . '</label><div class="left-float selector">' . $category_vimeo . '</div>');
                $html .= $side == 'backend' ? VGenerate::simpleDivWrap('row top-padding15', '', '<label>' . $language['backend.import.feed.assign'] . '</label><div class="left-float selector">' . self::username_selectList('import_vimeo_channel') . '</div>') : null;
                $html .= self::selectEntries('list_vimeo_feed', 'import_vimeo_feed', true);
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
        if ($cfg['import_dm'] == 1) {
            if ($side == 'backend' or ($side == 'frontend' and intval($perm['perm_embed_dm_video']) == 1 and !$err)) {
                /* import dailymotion video feeds */
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head jc-sb">';
                $html .= VGenerate::simpleDivWrap('top-padding5 wd90p all-paddings5 bold pointer', '', '<i class="icon-libreoffice"></i> ' . $language['backend.import.dm.video.h1'], '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<form id="dailymotion-video-form" method="post" action="" class="entry-form-class">';
                $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.import.search.for'] . '</label>', 'left-float', 'be_search_term', 'text-input wd190', '');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.categ'] . '</label><div class="left-float selector">' . $category_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.region'] . '</label><div class="left-float selector">' . $region_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type'] . '</label><div class="left-float selector">' . $type_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.sort'] . '</label><div class="left-float selector">' . $sort_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.results'] . '</label><div class="left-float selector">' . $results_dm_type_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.assign.c'] . '</label><div class="left-float selector">' . $category_dm_type . '</div>');
                $html .= $side == 'backend' ? VGenerate::simpleDivWrap('row top-padding15', '', '<label>' . $language['backend.import.feed.assign'] . '</label><div class="left-float selector">' . self::username_selectList('import_dm_video') . '</div>') : null;
                $html .= self::selectEntries('list_dailymotion_feed_video', 'import_dailymotion_feed_video', true);
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</li>';
            }
            if ($side == 'backend' or ($side == 'frontend' and intval($perm['perm_embed_dm_user']) == 1 and !$err)) {
                /* import dailymotion user feeds */
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head jc-sb">';
                $html .= VGenerate::simpleDivWrap('top-padding5 wd90p all-paddings5 bold pointer', '', '<i class="icon-libreoffice"></i> ' . $language['backend.import.dm.h1'], '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<form id="dailymotion-user-form" method="post" action="" class="entry-form-class">';
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.user'] . '</label><div class="left-float selector">' . $channel_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.type'] . '</label><div class="left-float selector">' . $feed_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.results'] . '</label><div class="left-float selector">' . $results_dm_ht . '</div>');
                $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language['backend.import.feed.assign.c'] . '</label><div class="left-float selector">' . $category_dm . '</div>');
                $html .= $side == 'backend' ? VGenerate::simpleDivWrap('row top-padding15', '', '<label>' . $language['backend.import.feed.assign'] . '</label><div class="left-float selector">' . self::username_selectList('import_dm_channel') . '</div>') : null;
                $html .= self::selectEntries('list_dailymotion_feed', 'import_dailymotion_feed', true);
                $html .= '</form>';
                $html .= '</div>';
                $html .= '</li>';
            }
        }
        $html .= '</ul>';
        $html .= '</div>'; //end left-side
        $html .= '</div>';
        $html .= '<div class="vs-column half fit">';
        $html .= '<form id="right-side-form" method="post" action=""' . ($side == 'frontend' ? ' style="padding: 15px 0px 15px 0px;"' : null) . '>';
        $html .= '<div class="left-float wd560" id="right-side">';
        $html .= '</div>'; //end right-side
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>'; //end import-wrap
        $html .= '<div class="clearfix"></div>';

        $html .= '
            <script type="text/javascript">
                var error_html = \'<div class="pointer left-float wdmax" id="cb-response"><div class="error-message" id="error-message" onclick="$(this).replaceWith(&quot;&quot;); $(&quot;#cb-response&quot;).replaceWith(&quot;&quot;); $(&quot;#cb-response-wrap&quot;).replaceWith(&quot;&quot;);"><p class="error-message-text">No entries selected! Please select one or more entries.</p></div>\';
                ' . ($side == 'frontend' ? '$(window).resize(function() {dinamicSizeSetFunction_menu();});$(function() {dinamicSizeSetFunction_menu();});' : null) . '
            </script>
    ';

        return $html;
    }
    /* check for existing accounts */
    public function chkAccount()
    {
        global $db, $language;

        $chk = $db->execute("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_status`='1' ORDER BY `usr_user` LIMIT 1;");
        $err = $chk->fields['usr_key'] == '' ? $language['upload.err.msg.13'] : null;
        return $err;
    }
    /* get "other" category id */
    public function getOther()
    {
        global $db;

        $c     = $db->execute("SELECT `ct_id` FROM `db_categories` WHERE (`ct_name`='Other' OR `ct_descr` LIKE '%other%') AND `ct_type`='video' LIMIT 1;");
        $ct_id = $c->fields['ct_id'];

        return ($ct_id > 0 ? $ct_id : 1);
    }
    /* select all/none entries */
    public function selectEntries($btn1, $btn2, $list_button = false)
    {
        global $language, $cfg;

        $sel = '<div class="right-float lh25 wd140" style="float: right;">
            <div class="row icheck-box all">
                <input type="radio" name="select_entries_on" value="all"><label>' . $language['backend.import.select.all'] . '</label>
                <input type="radio" name="select_entries_off" value="none"><label>' . $language['backend.import.unselect.all'] . '</label>
            </div>
            </div>';

        $sel = null;

        if ($cfg['import_mode'] == 'ask') {
            $save_action = '
                <div class="place-left">
                    <div class="icheck-box ask">
                        <input type="radio" name="save_action" value="embed" checked="checked">
                        <label>' . $language['backend.import.embed'] . '</label>
                        <input type="radio" name="save_action" value="download"  style="margin-left: 20px;">
                        <label>' . $language['backend.import.download'] . '</label>
                    </div>
                </div>
            ';
        } else {
            $save_action = '<div class="row no-display"><input type="hidden" name="save_action" value="' . $cfg['import_mode'] . '" readonly="readonly" /></div>';
        }

        $button = VGenerate::simpleDivWrap('row', '', '<div class="clearfix"></div><br><div class="left-float">' . VGenerate::basicInput('button', $btn1, 'button-grey save-entry-button search-list-entry search-button form-button ' . str_replace('_', '-', $btn1), '', 'import', '<span>' . $language['backend.import.feed.list'] . '</span>') . '</div>');
        $html   = $list_button ? $button : $save_action;

        return $html;
    }
    /* submit YOUTUBE video feeds */
    public function processVideoFeed()
    {
        global $cfg;

        if (isset($_POST['feed_categ'])) {
//standard feeds
            $out = self::getAndPrintStandardFeeds('m1');
        } else {
//channel feeds
            $out = self::getAndPrintStandardFeeds('m2');
        }

        echo $out;
    }
    /* submit VIMEO user video feeds */
    public function processVimeoFeed()
    {
        global $class_filter, $language, $cfg, $smarty, $side;

        $user    = $class_filter->clr_str(trim($_POST['feed_channel_vimeo']));
        $type    = $class_filter->clr_str($_POST['feed_type_vimeo']);
        $results = $class_filter->clr_str($_POST['feed_results_vimeo']);

        $smarty->assign('page_display', 'tpl_import');

        switch ($results) {
            case '1_-_20':$page = 1;
                break;
            case '21_-_40':$page = 2;
                break;
            case '41_-_60':$page = 3;
                break;
            default:$page = 1;
                break;
        }

        $vimeo_api = 'http://vimeo.com/api/v2/' . $user . '/' . $type . '.php?page=' . $page;

        $r       = VServer::curl_tt($vimeo_api);
        $res     = unserialize($r);
        $count   = 1;
        $checked = count($_POST['import_id']);
        $error   = false;

        $btn1 = 'list_vimeo_feed';
        $btn2 = 'import_vimeo_feed';
        $usr  = 'assign_username_import_vimeo_channel';

        $btn = VGenerate::basicInput('button', $btn2, 'no-display save-videos-button search-button form-button ' . str_replace('_', '-', $btn2), '', 'import', $language['backend.import.feed.import']);

        if ($_GET['do'] == 'import-vimeo-feed' and $checked == 0) {
            $error = VGenerate::noticeTpl('', $language['backend.embed.nosel'], '');
        } elseif ($_POST[$usr] == '' and $side == 'backend' and $_GET['do'] == 'import-vimeo-feed') {
            $error = VGenerate::noticeTpl('', $language['backend.embed.username'], '');
        }

        if (is_array($res)) {
            $html = '<div class="entry-list wdmax">';
            $html .= '<div class="section-top-bar" id="ct-wrapper">';
            $html .= '<div class="left-float left-padding5">';
            $html .= '<div class="sortings">';
            $html .= '<div class="icheck-box fetched place-left" id="checkselect-all-entries"><input type="checkbox" id="check-all" /></div>';
            $html .= self::selectEntries($btn1, $btn2);
            $html .= $btn;
            $html .= '</div>';
            $html .= '<div class="page-actions">' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '</div>';
            $html .= '<div class="clearfix"></div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="clearfix"></div>';
            $html .= '<div class="entry-response">' . $error . '</div>';
            $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
            foreach ($res as $v) {
                $db_categ_id = 0;
                $tmb         = array(
                    0 => $v['thumbnail_large'],
                    1 => $v['thumbnail_medium'],
                    2 => $v['thumbnail_medium'],
                    3 => $v['thumbnail_medium'],
                );

                $html .= '<li>';
                $html .= '<div>';
                $html .= '<div class="responsive-accordion-head jc-sb active">';
                $html .= '<span class="icheck-box fetched"><input type="checkbox" name="import_id[]"' . ((count($_POST['import_id']) > 0 and in_array($v['id'], $_POST['import_id'])) ? ' checked="checked"' : null) . ' class="import-selected" value="' . $v['id'] . '" /></span>';
                $html .= VGenerate::simpleDivWrap('entry-title-off ct-bullet-label left-float right-padding10 link', '', '', '', 'span');
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: none;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: block;"></i>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel active" style="display: block;">';
                $html .= VGenerate::simpleDivWrap('left-float vs-column thirds', '', '<a href="http://vimeo.com/' . $v['id'] . '" target="_blank"><img width="168" src="' . $v['thumbnail_medium'] . '" /></a>');
                $html .= '<div class="left-float wd300 left-padding10 vs-column two_thirds fit">';

                $html .= !$error ? self::saveCheck($v['id'], array(
                    "title"       => $v['title'],
                    "description" => $v['description'],
                    "tags"        => $v['tags'],
                    "category"    => ((isset($_POST['assign_category_vimeo']) and $_POST['assign_category_vimeo'] != '') ? intval($_POST['assign_category_vimeo']) : ($db_categ_id > 0 ? $db_categ_id : self::getOther())),
                    "duration"    => $v['duration'],
                    "key"         => $v['id'],
                    "src"         => 'vimeo',
                    "url"         => $v['url'],
                    "tmb"         => $tmb)) : null;

                $html .= VGenerate::simpleDivWrap('row bold embed-title', '', $v['title']);
                $d = str_replace('<br />', "\n", $v['description']);
                $html .= VGenerate::simpleDivWrap('row embed-description', '', VUserinfo::truncateString($d, 200));
                $html .= '</div><div class="clearfix"></div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</li>';

                $count++;
            }
            $html .= '</ul>';
            $html .= '</div>';

            $html .= VGenerate::declareJS(self::grabberjs());
            $html .= '<script type="text/javascript">' . $smarty->fetch($cfg['javascript_dir_be'] . '/settings-accordion.js') . '</script>';

        } else {
            $html = $r;
        }

        $ht = '<div style="overflow-y: scroll; height: 640px;">' . $html . '</div>';

        echo $ht;
    }
    /* submit DAILYMOTION user and video feeds */
    public function processDailymotionFeed()
    {
        global $class_filter, $db, $language, $smarty, $cfg, $side;

        $_do          = $class_filter->clr_str($_GET['do']);
        $user         = $class_filter->clr_str(trim($_POST['feed_channel_dm']));
        $type         = $class_filter->clr_str($_POST['feed_type_dm']);
        $type_video   = $class_filter->clr_str($_POST['feed_type_dm_video']);
        $categ_video  = $class_filter->clr_str($_POST['assign_category_dm_video']);
        $sort_video   = $class_filter->clr_str($_POST['feed_sort_dm_video']);
        $region_video = $class_filter->clr_str($_POST['feed_region_dm']);
        $search_video = $class_filter->clr_str($_POST['be_search_term']);

        $results    = ($_do == 'dm-feed' or $_do == 'import-dm-user') ? $class_filter->clr_str($_POST['feed_results_dm']) : $class_filter->clr_str($_POST['feed_results_dm_type']);
        $fr         = explode("-", $results);
        $feed_start = substr($fr[0], 0, -1);
        $feed_max   = substr($fr[1], 1);
        $feed_stop  = 50;
        $feed_page  = $feed_max / $feed_stop;

        $dm_api   = 'https://api.dailymotion.com';
        $dm_get   = ($_do == 'dm-feed' or $_do == 'import-dm-user') ? '/user/' . $user . '/' . $type : '/videos';
        $dm_param = '?page=' . $feed_page . '&limit=' . $feed_stop . '&fields=description,duration,tags,title,channel,thumbnail_url,thumbnail_small_url,thumbnail_medium_url,thumbnail_large_url,url,id';
        if ($_do == 'dm-video' or $_do == 'import-dm-video') {
            $dm_param .= ($type_video != '' ? '&filters=' . $type_video : null);
            $dm_param .= ($categ_video != '' ? '&channel=' . $categ_video : null);
            $dm_param .= ($sort_video != '' ? '&sort=' . $sort_video : null);
            $dm_param .= ($region_video != '' ? '&language=' . $region_video : null);
            $dm_param .= ($search_video != '' ? '&search=' . urlencode($search_video) : null);
        }
        $dm_api = $dm_api . $dm_get . $dm_param;

        $rs      = VServer::curl_tt($dm_api);
        $res     = json_decode($rs, true);
        $err     = $res['error']['message'] != '' ? $res['error']['message'] : false;
        $error   = false;
        $total   = count($res['list']);
        $checked = is_array($_POST['import_id']) ? count($_POST['import_id']) : 0;

        if ($_do == 'dm-feed' or $_do == 'import-dm-user') {
            $btn1 = 'list_dailymotion_feed';
            $btn2 = 'import_dailymotion_feed';
            $usr  = 'assign_username_import_dm_channel';
        } else {
            $btn1 = 'list_dailymotion_feed_video';
            $btn2 = 'import_dailymotion_feed_video';
            $usr  = 'assign_username_import_dm_video';
        }

        $smarty->assign('page_display', 'tpl_import');

        $btn = VGenerate::basicInput('button', $btn2, 'no-display save-videos-button search-button form-button ' . str_replace('_', '-', $btn2), '', 'import', $language['backend.import.feed.import']);

        if (($_GET['do'] == 'import-dm-user' or $_GET['do'] == 'import-dm-video') and $checked == 0) {
            $error = VGenerate::noticeTpl('', $language['backend.embed.nosel'], '');
        } elseif ($_POST[$usr] == '' and $side == 'backend' and ($_GET['do'] == 'import-dm-user' or $_GET['do'] == 'import-dm-video')) {
            $error = VGenerate::noticeTpl('', $language['backend.embed.username'], '');
        }

        $i     = 0;
        $count = 1;

        $html = '<div class="entry-list wdmax">';
        $html .= '<div class="section-top-bar" id="ct-wrapper">';
        $html .= '<div class="left-float left-padding5">';
        $html .= '<div class="sortings">';
        $html .= '<div class="icheck-box fetched place-left" id="checkselect-all-entries"><input type="checkbox" id="check-all" /></div>';
        $html .= self::selectEntries($btn1, $btn2);
        $html .= $btn;
        $html .= '</div>';
        $html .= '<div class="page-actions">' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>'; //end left-float
        $html .= '</div>'; //end section-top-bar
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="entry-response">' . ($err != '' ? $err : $error) . '</div>';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        foreach ($res['list'] as $k => $v) {
            $dbc         = $db->execute(sprintf("SELECT `ct_id`, `ct_name` FROM `db_categories` WHERE `ct_type`='video' AND `ct_active`='1' AND `ct_name` LIKE '%s' OR `ct_descr` LIKE '%s';", '%' . $v['channel'] . '%', '%' . $v['channel'] . '%'));
            $db_categ_id = $dbc->fields['ct_id'];
            $ct_name     = $dbc->fields['ct_name'];
            $tmb         = array(
                0 => $v['thumbnail_large_url'],
                1 => $v['thumbnail_medium_url'],
                2 => $v['thumbnail_medium_url'],
                3 => $v['thumbnail_medium_url'],
            );

            $html .= '<li>';
            $html .= '<div>';
            $html .= '<div class="responsive-accordion-head jc-sb active">';
            $html .= '<span class="icheck-box fetched"><input type="checkbox" name="import_id[]"' . ((is_array($_POST['import_id']) and count($_POST['import_id']) > 0 and in_array($v['id'], $_POST['import_id'])) ? ' checked="checked"' : null) . ' class="import-selected" value="' . $v['id'] . '" /></span>';
            $html .= VGenerate::simpleDivWrap('entry-title-off ct-bullet-label left-float right-padding10 link', '', '', '', 'span');
            $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: none;"></i>';
            $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: block;"></i>';
            $html .= '</div>';
            $html .= '<div class="responsive-accordion-panel active" style="display: block;">';
            $html .= VGenerate::simpleDivWrap('left-float vs-column thirds', '', '<a href="' . $v['url'] . '" target="_blank"><img width="168" src="' . $v['thumbnail_medium_url'] . '" /></a>');
            $html .= '<div class="left-float wd300 left-padding10 vs-column two_thirds fit">';
            $html .= (!$err and !$error) ? self::saveCheck($v['id'], array(
                "title"       => $v['title'],
                "description" => $v['description'],
                "tags"        => implode(" ", $v['tags']),
                "category"    => ((isset($_POST['assign_category_dm_type']) and $_POST['assign_category_dm_type'] != '') ? intval($_POST['assign_category_dm_type']) : ((isset($_POST['assign_category_dm']) and $_POST['assign_category_dm'] != '') ? intval($_POST['assign_category_dm']) : ($db_categ_id > 0 ? $db_categ_id : self::getOther()))),
                "duration"    => $v['duration'],
                "key"         => $v['id'],
                "src"         => 'dailymotion',
                "url"         => $v['url'],
                "tmb"         => $tmb)) : null;

            $html .= VGenerate::simpleDivWrap('row bold embed-title', '', $v['title']);
            $d = str_replace('<br />', "\n", $v['description']);
            $html .= VGenerate::simpleDivWrap('row embed-description', '', VUserinfo::truncateString($d, 200));
            $html .= VGenerate::simpleDivWrap('row greyed-out embed-category', '', $language['backend.import.category'] . ($ct_name != '' ? $ct_name : $v['channel']));
            $html .= '</div><div class="clearfix"></div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</li>';

            $count++;
        }
        $html .= '</ul>';
        $html .= '</div>';

        $html .= VGenerate::declareJS(self::grabberjs());
        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg['javascript_dir_be'] . '/settings-accordion.js') . '</script>';

        $ht = '<div style="overflow-y: scroll; height: 640px;">' . $html . '</div>';

        echo $ht;
    }
    private static function durationFromString($string)
    {
        if (is_array($string) and isset($string[0])) {
            $string = $string[0];
        }

        if (substr($string, 0, 2) === "PT") {
            $period       = new DateInterval($string);
            $totalSeconds = $period->s + $period->i * 60;
        } else {
            if (strpos($string, ":") !== false) {
                $exp = explode(":", $string);
                $ext = count($exp);

                if ($ext == 2) {
                    $hours = 0;

                    list($minutes, $seconds) = $exp;
                } elseif ($ext == 3) {
                    list($hours, $minutes, $seconds) = $exp;
                }

                $totalSeconds = $hours * 3600 + $minutes * 60 + $seconds;
            } else {
                $totalSeconds = $string;
            }
        }

        return (float) $totalSeconds;
    }
    private static function extractUniqueWords($inputArray, $limit = 10)
    {
        $uniqueWords = array();
        $count       = 0;

        foreach ($inputArray as $text) {
            $words = explode(" ", $text);

            foreach ($words as $word) {
                // Remove non-alphanumeric characters and convert to lowercase
                $word = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $word));

                // Check if the word is longer than 4 characters and not in the $uniqueWords array
                if (strlen($word) > 4 && !in_array($word, $uniqueWords)) {
                    $uniqueWords[] = $word;
                    $count++;
                }

                // Stop after reaching the specified limit
                if ($count >= $limit) {
                    break 2; // Break both the inner and outer loops
                }
            }
        }

        return $uniqueWords;
    }
    /* submit single video embed */
    public function processVideoEmbed()
    {
        global $class_filter, $language, $cfg;

        require_once 'f_core/f_classes/class_oembed/vendor/autoload.php';

        // $video_url = $class_filter->clr_str($_POST['be_video_url']);
        $video_url   = htmlspecialchars($_POST['be_video_url']);
        $video_categ = $class_filter->clr_str($_POST['assign_category_file']);
        $u           = parse_url($video_url);
        $h           = explode('.', $u['host']);
        $src         = $h[(count($h) - 2)];
        $src         = ($src == 'youtu' ? 'youtube' : $src);
        $data        = array();
        $duration    = null;
        $short       = 0;

        try {
            $embed       = new Embed();
            $info        = $embed->get($video_url);
            $title       = $info->title;
            $thumb       = $info->image;
            $code        = $info->code->html;
            $description = $info->description ?: $title;
            $tags        = $info->keywords ?: $title;

            if ($src == 'facebook') {
                $metas       = $info->getMetas()->all();
                $title       = $metas['og:title'][0];
                $description = $metas['og:description'][0];
                $thumb       = $metas['og:image'][0];
                $tags        = implode(' ', self::extractUniqueWords(array_slice($metas['og:title'], 0, 10)));
                $code        = '<style type="text/css">.fb-video{width:640px;height:360px}.fb-video > span, .fb-video iframe{width:100% !important;height:100% !important}</style><div id="fb-root"></div><script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script><div class="fb-video" data-href="' . $video_url . '" data-allowfullscreen="true" data-width="640px"></div>';
            }
            if (is_array($tags)) {
                $tags = implode(' ', self::extractUniqueWords(array_slice($tags, 0, 10)));
            }
            if (strpos($u['path'], "/shorts/") !== false) {
                $short = 1;
            }
            if ($title and $thumb and $code) {
                $metas = $info->getMetas()->all();

                if (isset($metas['og:video:duration'])) {
                    $duration = $metas['og:video:duration'];
                } elseif (isset($metas['video:duration'])) {
                    $duration = $metas['video:duration'];
                } elseif (isset($metas['duration'][0])) {
                    $duration = $metas['duration'][0];
                } elseif (isset($metas['duration'])) {
                    $duration = $metas['duration'];
                }
                if ($duration) {
                    $duration = self::durationFromString($duration);
                }
                if (!$duration) {
                    $ld = $info->getLinkedData()->all();

                    if (isset($ld[0]['duration'])) {
                        $duration = $ld[0]['duration'];
                    } elseif (isset($ld['duration'])) {
                        $duration = $ld['duration'];
                    }

                    if ($duration) {
                        $duration = self::durationFromString($duration);
                    }
                }
                if (!$duration) {
                    $duration = 1;
                }

                $key = md5($video_url);

                if ($src == 'youtube') {
                    $u  = parse_url($video_url);
                    $v  = explode('&', $u['query']);
                    $id = $class_filter->clr_str(substr($v[0], 2));

                    if ($id == '') {
                        $id = str_replace("/", "", $u['path']);
                    }

                    $key = str_replace('shorts', '', $id);

                    if ($short) {
                        $thumb = 'https://i.ytimg.com/vi/' . $key . '/hq720.jpg';
                    }
                }

                $data['src']         = $src;
                $data['title']       = $title;
                $data['description'] = $description;
                $data['duration']    = $duration;
                $data['tags']        = $tags;
                $data['url']         = $info->url;
                $data['code']        = $code;
                $data['key']         = $key;
                $data['category']    = null;
                $data['short']       = $short;
                $data['tmb']         = array(
                    0 => $thumb,
                    1 => $thumb,
                );

                $html = self::embedFile_details($src, $data);

                echo $html;
            } else {
                echo VGenerate::noticeTpl('', 'Unable to extract video data.', '');
                return false;
            }
        } catch (Exception $e) {
            echo VGenerate::noticeTpl('', $e->getMessage(), '');
            return false;
        }
    }

    /* print details about embedded video */
    public function embedFile_details($src, $info)
    {
        global $cfg, $db, $class_filter, $language, $smarty, $side;

        $ec          = preg_replace('/(width|height)="[^"]*"/', '$1="100%"', $info['code']);
        $video_categ = $class_filter->clr_str($_POST['assign_category_file']);
        $username    = $class_filter->clr_str($_POST['assign_username_import_single']);
        $db_uid      = $class_filter->clr_str($_POST['assign_username']);

        if ($cfg['import_mode'] == 'ask') {
            $save_action = '<div class="row icheck-box ask"><input type="radio" name="save_action" value="embed" checked="checked" /><label>' . $language['backend.import.embed'] . '</label> <input type="radio" name="save_action" value="download" /><label>' . $language['backend.import.download'] . '</label></div>';
        } else {
            $save_action = '<div class="row no-display"><input type="hidden" name="save_action" value="' . $cfg['import_mode'] . '" readonly="readonly" /></div>';
        }

        $error = false;

        if ($video_categ == '' and $_GET['do'] == 'video-embed') {
            $error = VGenerate::noticeTpl('', $language['backend.embed.category'], '');
        } elseif ($username == '' and $_GET['do'] == 'video-embed' and $side == 'backend') {
            $error = VGenerate::noticeTpl('', $language['backend.embed.username'], '');
        }

        $smarty->assign('page_display', 'tpl_import');

        $html = '<div class="entry-list wdmax">';
        $html .= '<div class="section-top-bar" id="ct-wrapper">';
        $html .= '<div class="left-float left-padding5">';
        $html .= '<div class="sortings">';
        $html .= VGenerate::simpleDivWrap('row', '', $save_action);
        $html .= VGenerate::basicInput('button', 'embed_video_file', 'no-display save-videos-button search-button form-button embed-video-file', '', 'embed', $language['backend.embed.video.save']);
        $html .= '</div>';
        $html .= '<div class="page-actions">' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="entry-response">' . $error . '</div>';

        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head jc-sb active">';
        $html .= VGenerate::simpleDivWrap('entry-title-off ct-bullet-label left-float right-padding10 link', '', $info['title'], '', 'span');
        $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: none;"></i>';
        $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: block;"></i>';
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active d-column">';

        if ($_GET['do'] == 'video-embed' and !$error) {
//save embedded video
            $info['url'] = $class_filter->clr_str($_POST['be_video_url']);
            $html .= VGenerate::simpleDivWrap('row', '', self::saveCheck($info['key'], $info));
        }

        $html .= '<div class="d-flex d-column">';
        $html .= '<div class="vs-column full embed-player">' . $ec . '</div>';
        $html .= '<div class="vs-column full fit embed-info">';

        $html .= VGenerate::simpleDivWrap('row embed-description', '', str_replace("\n", '<br>', $info['description']));
        $html .= VGenerate::simpleDivWrap('row greyed-out embed-tags', '', 'tags: ' . $info['tags']);
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';

        $html .= VGenerate::declareJS(self::grabberjs());
        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg['javascript_dir_be'] . '/settings-accordion.js') . '</script>';

        return $html;
    }

    /* check and save selected files, save single embedded file */
    public function saveCheck($db_rkey, $info)
    {
        global $language, $class_filter, $db;

        $ok          = null;
        $_do         = $class_filter->clr_str($_GET['do']);
        $checked     = $_do == 'video-embed' ? 1 : (is_array($_POST['import_id']) ? count($_POST['import_id']) : 0);
        $save_action = $class_filter->clr_str($_POST['save_action']);
        if ($_do == 'video-embed' or (substr($_GET['do'], 0, 6) == 'import' and $checked > 0)) {
            if ($_do == 'video-embed' or (in_array($db_rkey, $_POST['import_id']))) {
                $act = self::embedFile_save(array(
                    "title"       => $info['title'],
                    "description" => $info['description'],
                    "tags"        => $info['tags'],
                    "category"    => $info['category'],
                    "duration"    => $info['duration'],
                    "key"         => $info['key'],
                    "src"         => $info['src'],
                    "url"         => $info['url'],
                    "tmb"         => $info['tmb'],
                    "short"       => (int) $info['short'],
                ));

                if ($db->Affected_Rows() > 0) {
                    $ok = '<span class="conf-green lh20 left-margin5">' . ($save_action == 'download' ? $language['backend.embed.confirmed.dl'] : $language['backend.embed.confirmed']) . '</span>';
                } else {
                    $ok = '<span class="err-red lh20 left-margin5">' . $language['backend.embed.failed'] . '</span>';
                }
            }
        }

        return $ok;
    }
    /* save file to database */
    public function embedFile_save($info)
    {
        global $class_database, $cfg, $db, $class_filter, $side, $smarty;

        $err     = false;
        $short   = (isset($info['short']) and $info['short'] == 1);
        $type    = $short ? 'short' : 'video';
        $filekey = VUserinfo::generateRandomString(10);
        $chk     = $class_database->singleFieldValue('db_' . $type . 'files', 'db_id', 'file_key', $filekey);

        for ($i = 1; $i <= 10; $i++) {
            $chk = $class_database->singleFieldValue('db_' . $type . 'files', 'db_id', 'file_key', $filekey);

            if ($chk) {
                $filekey = VUserinfo::generateRandomString(10);
            } else {
                break;
            }
        }

        $ukey = $side == 'backend' ? $class_filter->clr_str($_POST['assign_username']) : $_SESSION['USER_KEY'];
        if ($ukey == '' and $side != 'backend') {
            header("Location:" . $cfg['main_url'] . '/' . VHref::getKey('signout'));
            exit;
        }
        $info['title']       = $class_filter->clr_str($info['title']);
        $info['description'] = $class_filter->clr_str(str_replace(array('<br />', '<br>'), array("\n", "\n"), $info['description']));
        $info['tags']        = str_replace('quot', '', $info['tags']);
        $info['tags']        = str_replace('amp', '', $info['tags']);

        $categ = isset($_POST['assign_category_file']) ? $class_filter->clr_str($_POST['assign_category_file']) :
        (isset($_POST['assign_category_feed']) ? $class_filter->clr_str($_POST['assign_category_feed']) :
            (isset($_POST['assign_category_channel']) ? $class_filter->clr_str($_POST['assign_category_channel']) :
                (isset($_POST['assign_category_dm_type']) ? $class_filter->clr_str($_POST['assign_category_dm_type']) :
                    (isset($_POST['assign_category_dm']) ? $class_filter->clr_str($_POST['assign_category_dm']) :
                        (isset($_POST['assign_category_vimeo']) ? $class_filter->clr_str($_POST['assign_category_vimeo']) : self::getOther())))));

        if ($side == 'backend') {
            $ukey = isset($_POST['assign_category_file']) ? $class_filter->clr_str($_POST['assign_username_import_single']) :
            (isset($_POST['assign_category_feed']) ? $class_filter->clr_str($_POST['assign_username_import_yt_video']) :
                (isset($_POST['assign_category_channel']) ? $class_filter->clr_str($_POST['assign_username_import_yt_channel']) :
                    (isset($_POST['assign_category_dm_type']) ? $class_filter->clr_str($_POST['assign_username_import_dm_video']) :
                        (isset($_POST['assign_category_dm']) ? $class_filter->clr_str($_POST['assign_username_import_dm_channel']) :
                            (isset($_POST['assign_category_vimeo']) ? $class_filter->clr_str($_POST['assign_username_import_vimeo_channel']) : self::getOther())))));
        }

        $usr_id      = $side == 'backend' ? $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $ukey) : intval($_SESSION['USER_ID']);
        $save_action = $class_filter->clr_str($_POST['save_action']);
        $db_approved = (($cfg['file_approval'] == 1 or $save_action == 'download') ? 0 : 1);

        $v_info = array(
            "usr_id"           => $usr_id,
            "file_key"         => $filekey,
            "old_file_key"     => 0,
            "file_type"        => 'embed',
            "file_name"        => 'none',
            "file_size"        => 0,
            "file_duration"    => $class_filter->clr_str(($info['duration'] == '' or (int) $info['duration'] == 0) ? 1 : $info['duration']),
            "upload_date"      => date("Y-m-d H:i:s"),
            "is_subscription"  => ($side == 'frontend' ? intval($cfg['paid_memberships']) : 0),
            "file_views"       => 0,
            "file_comments"    => 0,
            "file_responses"   => 0,
            "file_like"        => 0,
            "file_dislike"     => 0,
            "file_mobile"      => 1,
            "embed_key"        => $class_filter->clr_str(str_replace('shorts', '', $info['key'])),
            "embed_src"        => $class_filter->clr_str($info['src']),
            "embed_url"        => $class_filter->clr_str($info['url']),
            "file_title"       => $info['title'],
            "file_description" => $info['description'],
            "file_tags"        => VForm::clearTag($info['tags']),
            "file_category"    => intval($categ != '' ? $categ : $info['category']),
            "approved"         => $db_approved,
            "privacy"          => "public",
            "comments"         => "all",
            "comment_votes"    => 1,
            "rating"           => 1,
            "responding"       => "all",
            "embedding"        => 1,
            "social"           => 1,
        );

        if ($cfg['paid_memberships'] == 1 and $side == 'frontend' and $save_action == 'embed') {
            $err   = VUpload::subscriptionCheck($type);
            $db_id = 0;
        }
        if (!$err) {
            $do_db = $class_database->doInsert('db_' . $type . 'files', $v_info);
            $db_id = $db->Insert_ID();
        }

        if ($db_id > 0) {
            if ($save_action == 'download') {
                $v_que = array(
                    "file_key" => $filekey,
                    "usr_key"  => $ukey,
                );
                $do_db = $class_database->doInsert('db_videodl', $v_que);
            }
            $activity = ($cfg['activity_logging'] == 1 and $action = new VActivity($usr_id, 0)) ? $action->addTo('log_upload', $type . ':' . $filekey) : null;

            $src_folder = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/';
            foreach ($info['tmb'] as $k => $tmb) {
//download thumbnails
                $tmb      = $info['tmb'][$k];
                $file_loc = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/' . $k . '.tmp.jpg';
                $file_src = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/0.tmp.jpg';
                $d1       = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey;
                if (!is_dir($d1)) {
                    mkdir($d1);
                }

                if ($info['src'] == 'youtube' and $k == 0) {
                    $cmd = 'curl -r 0-25 --silent ' . $tmb . ' | identify -format "%wx%h" -';
                    // error_log($cmd);
                    exec($cmd . ' 2>&1', $output);

                    $dim = $output[0];

                    if ($dim == '120x90') {
                        $tmb = str_replace('maxresdefault.jpg', 'mqdefault.jpg', $tmb);
                    }
                }

                $ch = curl_init($tmb);
                $fp = fopen($file_loc, 'wb');
                curl_setopt($ch, CURLOPT_FILE, $fp);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_exec($ch);
                curl_close($ch);
                fclose($fp);

                if (file_exists($file_loc) and $k > 0) {
                    $conv = new VDocument();
                    $conv->log_setup($filekey, true);
                    $dst_folder_tmb = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/';

                    if ($conv->createThumbs_ffmpeg($src_folder, $k, 640, 360, $filekey, $ukey, $file_src, $short)) {
                        $final = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/' . $k . '.jpg';

                        if (file_exists($final)) {
                            unlink($file_loc);
                        }
                    }
                }
            }
            if (file_exists($file_src)) {
                $conv = new VDocument();
                $conv->log_setup($filekey, true);
                $dst_folder_tmb = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/';

                if ($conv->createThumbs_ffmpeg($src_folder, 0, 640, 360, $filekey, $ukey, $file_src, $short)) {
                    $final = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/0.jpg';

                    if (file_exists($final)) {
                        unlink($file_src);
                    }
                }
            }
            if (!VGenerate::isImage($cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/0.jpg')) {
                if (file_exists($cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/0.jpg')) {
                    unlink($cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/0.jpg');
                }

                if (file_exists($cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/1.jpg')) {
                    unlink($cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/1.jpg');
                }

                $tmp_file = str_replace($cfg['main_url'], $cfg['main_dir'], VUseraccount::getProfileImage($usr_id, false));
                if ($tmp_file && is_file($tmp_file)) {
                    $src_folder = $cfg['media_files_dir'] . '/' . $ukey . '/t/' . $filekey . '/';
                    $conv       = new VDocument();
                    $conv->log_setup($filekey, false);

                    if ($conv->createThumbs_ffmpeg($src_folder, '1', 320, 180, $filekey, $ukey, $tmp_file, $short)) {}
                    if ($conv->createThumbs_ffmpeg($src_folder, '0', 640, 360, $filekey, $ukey, $tmp_file, $short)) {}
                }
            }
            $ct_update = $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_%s_count`=`usr_%s_count`+1 WHERE `usr_id`='%s' LIMIT 1;", $type[0], $type[0], $usr_id));
            if ($cfg['paid_memberships'] == 1 and $side == 'frontend' and $save_action == 'embed') {
                $ct_update = $db->execute(sprintf("UPDATE `db_packusers` SET `pk_total_%s`=`pk_total_%s`+1 WHERE `usr_id`='%s' LIMIT 1;", $type, $type, $usr_id));
            }
        }
    }

    /* get youtube tags from title */
    public function yt_tags($string)
    {
        $tags = array();
        $arr  = explode(' ', $string);

        foreach ($arr as $word) {
            if (strlen($word) > 4) {
                $tags[] = VUserinfo::clearString($word);
            }
        }

        if ($tags[0][0]) {
            return str_replace(array('amp', 'quot'), array('', ''), implode(" ", $tags));
        }
    }
    /* single file embed, get YOUTUBE details */
    public function embedFile_youtube($url)
    {
        return;

        global $class_filter, $language, $cfg;

        $DEVELOPER_KEY = $cfg['youtube_api_key'];

        $client = new Google_Client();
        $client->setDeveloperKey($DEVELOPER_KEY);
        // Define an object that will be used to make all API requests.
        $youtube = new Google_Service_YouTube($client);

        $info = array();
        $v    = explode('&', $url['query']);
        $id   = $url['path'] == "/watch" ? $class_filter->clr_str(substr($v[0], 2)) : null;

        if ($id == '') {
            $id = str_replace(array("/live", "/"), array("", ""), $url['path']);
        }

        try {
            $videosResponse = $youtube->videos->listVideos('snippet,contentDetails,statistics,status', array(
                'id' => $id,
            ));

            foreach ($videosResponse['items'] as $videoResult) {
                $info['src']         = 'youtube';
                $info['key']         = $id;
                $info['title']       = $videoResult['snippet']['title'];
                $info['description'] = $videoResult['snippet']['description'];
                $info['tags']        = self::yt_tags($class_filter->clr_str($info['title']));
                $info['category']    = $language['backend.import.feed.categ.list'][$videoResult['snippet']['categoryId']];
                $date                = new DateTime('1970-01-01');
                $date->add(new DateInterval($videoResult['contentDetails']['duration']));
                $duration         = $date->format('H:i:s');
                $d                = explode(':', $duration);
                $info['duration'] = ($d[0] * 60 * 60) + ($d[1] * 60) + ($d[2]);

                $tmb = array(
                    0 => "http://i.ytimg.com/vi/" . $id . "/maxresdefault.jpg",
                    1 => "http://i.ytimg.com/vi/" . $id . "/mqdefault.jpg",
                    2 => "http://i.ytimg.com/vi/" . $id . "/mqdefault.jpg",
                    3 => "http://i.ytimg.com/vi/" . $id . "/mqdefault.jpg",
                );
                $info['tmb'] = $tmb;
            }
        } catch (Google_Service_Exception $e) {
            $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>', htmlspecialchars($e->getMessage()));
        } catch (Google_Exception $e) {
            $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
                htmlspecialchars($e->getMessage()));
        }

        $html = self::embedFile_details('youtube', $info);

        echo $html;
    }
    /* single file embed, get VIMEO details */
    public function embedFile_vimeo($url)
    {
        global $class_filter;
        $info      = array();
        $id        = $class_filter->clr_str(substr($url['path'], 1));
        $vimeo_api = 'http://vimeo.com/api/v2/video/' . $id . '.php';

        $r = unserialize(VServer::curl_tt($vimeo_api));

        $info['src']         = 'vimeo';
        $info['key']         = $id;
        $info['title']       = $r[0]['title'];
        $info['description'] = $r[0]['description'];
        $info['tags']        = str_replace(',', ' ', $r[0]['tags']);
        $info['category']    = '';
        $info['duration']    = $r[0]['duration'];

        $tmb = array(
            0 => $r[0]['thumbnail_large'],
            1 => $r[0]['thumbnail_medium'],
            2 => $r[0]['thumbnail_medium'],
            3 => $r[0]['thumbnail_medium'],
        );
        $info['tmb'] = $tmb;

        $html = self::embedFile_details('vimeo', $info);

        echo $html;
    }
    /* single file embed, get DAILYMOTION details */
    public function embedFile_dailymotion($url)
    {
        $info   = array();
        $p      = explode('_', $url['path']);
        $id     = str_replace('/video/', '', $p[0]);
        $dm_api = 'https://api.dailymotion.com/video/' . $id . '?fields=description,duration,tags,title,channel,thumbnail_url,thumbnail_small_url,thumbnail_medium_url,thumbnail_large_url';

        $rs = VServer::curl_tt($dm_api);
        $r  = json_decode($rs);

        $info['src']         = 'dailymotion';
        $info['key']         = $id;
        $info['title']       = $r->{'title'};
        $info['description'] = $r->{'description'};
        $info['tags']        = implode(" ", $r->{'tags'});
        $info['category']    = $r->{'channel'};
        $info['duration']    = $r->{'duration'};

        $tmb = array(
            0 => $r->{'thumbnail_url'},
            1 => $r->{'thumbnail_medium_url'},
            2 => $r->{'thumbnail_medium_url'},
            3 => $r->{'thumbnail_medium_url'},
        );
        $info['tmb'] = $tmb;

        $html = self::embedFile_details('dailymotion', $info);

        echo $html;
    }

    /* youtube video feeds functions */
    public function getAndPrintVideoFeed($api_url, $feed_type)
    {
        global $language, $class_filter;

        $response = VServer::curl_tt($api_url);
        $results  = json_decode($response);

        $res  = self::printVideoFeed($results, $feed_type);
        $html = $res == '' ? $language['backend.import.feed.stop'] : $res;

        return '<div style="overflow-y: scroll; height: 640px;">' . $html . '</div>';
    }

    public function printVideoFeed($videoFeed, $feed_type)
    {
        global $class_filter, $language, $cfg, $smarty, $side;

        $error = false;
        $count = 1;

        if (is_array($videoFeed) and count($videoFeed) == 0) {
            return false;
        }

        $smarty->assign('page_display', 'tpl_import');

        $checked = is_array($_POST['import_id']) ? count($_POST['import_id']) : 0;

        switch ($feed_type) {
            case "m1":
                $form = 'video-feed-form';
                $btn1 = 'list_video_feed';
                $btn2 = 'import_video_feed';
                $usr  = 'assign_username_import_yt_video';
                break;
            case "m2":
                $form = 'channel-feed-form';
                $btn1 = 'list_channel_feed';
                $btn2 = 'import_channel_feed';
                $usr  = 'assign_username_import_yt_channel';
                break;
        }

        $btn = VGenerate::basicInput('button', $btn2, 'no-display save-videos-button search-button form-button ' . str_replace('_', '-', $btn2), '', 'import', $language['backend.import.feed.import']);

        if (($_GET['do'] == 'import-yt-channel' or $_GET['do'] == 'import-yt-video') and $checked == 0) {
            $error = VGenerate::noticeTpl('', $language['backend.embed.nosel'], '');
        } elseif ($_POST[$usr] == '' and $side == 'backend' and ($_GET['do'] == 'import-yt-channel' or $_GET['do'] == 'import-yt-video')) {
            $error = VGenerate::noticeTpl('', $language['backend.embed.username'], '');
        }
        $html = null;

        $html .= '<div class="entry-list wdmax">';
        $html .= '<div class="section-top-bar" id="ct-wrapper">';
        $html .= '<div class="left-float left-padding5">';
        $html .= '<div class="sortings">';
        $html .= '<div class="icheck-box fetched place-left" id="checkselect-all-entries"><input type="checkbox" id="check-all" /></div>';
        $html .= self::selectEntries($btn1, $btn2);
        $html .= $btn;
        $html .= '</div>';
        $html .= '<div class="page-actions">' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>'; //end left-float
        $html .= '</div>'; //end section-top-bar
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="entry-response">' . $error . '</div>';

        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        foreach ($videoFeed as $videoEntry) {
            if (is_array($videoEntry)) {
                foreach ($videoEntry as $k => $videoEntryDetails) {
                    if ($k >= 0) {
                        $_id    = $videoEntryDetails->id->videoId;
                        $_title = $videoEntryDetails->id->videoTitle;

                        $html .= '<li>';
                        $html .= '<div>';
                        $html .= '<div class="responsive-accordion-head jc-sb active">';
                        $html .= '<span class="icheck-box fetched"><input type="checkbox" name="import_id[]"' . (($checked > 0 and in_array($_id, $_POST['import_id'])) ? ' checked="checked"' : null) . ' class="import-selected" value="' . $_id . '" /></span>';
                        $html .= VGenerate::simpleDivWrap('entry-title-off ct-bullet-label left-float right-padding10 link', '', $_title, '', 'span');
                        $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: none;"></i>';
                        $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: block;"></i>';
                        $html .= '</div>';
                        $html .= '<div class="responsive-accordion-panel active" style="display: block;">';
                        $html .= VGenerate::simpleDivWrap('left-float vs-column thirds', '', '<a href="http://www.youtube.com/watch?v=' . $_id . '" target="_blank"><img width="168" src="http://i.ytimg.com/vi/' . $_id . '/0.jpg" /></a>');
                        $html .= '<div class="left-float wd400 left-padding10 vs-column two_thirds fit">';
                        $html .= self::printVideoEntry($videoFeed, $_id, $error);
                        $html .= '</div><div class="clearfix"></div>';
                        $html .= '</div>';
                        $html .= '</div>';
                        $html .= '</li>';

                        $count++;
                    }
                }
            }
        }
        $html .= '</ul>';
        $html .= '</div>';

        $nextPageToken    = $videoFeed->nextPageToken;
        $prevPageToken    = $videoFeed->prevPageToken;
        $currentPageToken = isset($_GET['token']) ? $class_filter->clr_str($_GET['token']) : null;

        $pjsct = $prevPageToken != '' ? '<a href="javascript:;" class="class-token prev-token" rel-form="' . $form . '" onclick="$(&quot;#' . $form . ' .class-token&quot).removeClass(&quot;active-token&quot); $(this).addClass(&quot;active-token&quot); $(&quot;#' . $form . ' .current-page&quot;).html(&quot;' . $prevPageToken . '&quot;); $(&quot;.list-video-feed-yt&quot;).click();">' . $prevPageToken . '</a>' : '##prevPageToken##';
        $njsct = '<a href="javascript:;" class="class-token next-token" rel-form="' . $form . '" onclick="$(&quot;#' . $form . ' .class-token&quot).removeClass(&quot;active-token&quot); $(this).addClass(&quot;active-token&quot); $(&quot;#' . $form . ' .current-page&quot;).html(&quot;' . $nextPageToken . '&quot;); $(&quot;.list-video-feed-hid&quot;).val(&quot;' . $form . '&quot;); $(&quot;.list-video-feed-yt&quot;).click();">' . $nextPageToken . '</a>';
        $cjsct = $currentPageToken != '' ? '<a href="javascript:;" class="class-token current-token" rel-form="' . $form . '" onclick="$(&quot;#' . $form . ' .class-token&quot).removeClass(&quot;active-token&quot); $(this).addClass(&quot;active-token&quot); $(&quot;#' . $form . ' .current-page&quot;).html(&quot;' . $currentPageToken . '&quot;); $(&quot;.list-video-feed-yt&quot;).click();">' . $currentPageToken . '</a>' : '##currentPageToken##';

        $js = '$(document).ready(function(){$("#' . $form . ' .next-page").html(\'' . $njsct . '\');$("#' . $form . ' .prev-page").html(\'' . $pjsct . '\');$("#' . $form . ' .current-page").html(\'' . $cjsct . '\');});';
        $js .= self::grabberjs();

        $html .= VGenerate::declareJS($js);
        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg['javascript_dir_be'] . '/settings-accordion.js') . '</script>';

        return $html;
    }

    public function grabberjs()
    {
        $js .= '$(".icheck-box.fetched input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        });
               });
               $(".embed-title").each(function () {
                        var self = $(this).text();

                        $(this).parent().parent().parent().find(".entry-title-off").first().append(self);
                        $(this).detach();
               });
               $(document).on({click: function () {
                $("#right-side .responsive-accordion div.responsive-accordion-head").addClass("active");
                $("#right-side .responsive-accordion div.responsive-accordion-panel").addClass("active").show();
                $("#right-side .responsive-accordion i.responsive-accordion-plus").hide();
                $("#right-side .responsive-accordion i.responsive-accordion-minus").show();
                resizeDelimiter();
            }}, "#all-open");

            $(document).on({click: function () {
                $("#right-side .responsive-accordion div.responsive-accordion-head").removeClass("active");
                $("#right-side .responsive-accordion div.responsive-accordion-panel").removeClass("active").hide();
                $("#right-side .responsive-accordion i.responsive-accordion-plus").show();
                $("#right-side .responsive-accordion i.responsive-accordion-minus").hide();
                $("#ct_entry").val("");
                resizeDelimiter();
            }}, "#all-close");

            $("#checkselect-all-entries input").on("ifChecked", function () {
                $("#import-wrap .responsive-accordion input").iCheck("check");
            });
            $("#checkselect-all-entries input").on("ifUnchecked", function () {
                $("#import-wrap .responsive-accordion input").iCheck("uncheck");
            });

               $(".icheck-box.ask input").each(function () {
                        var self = $(this);
                        label = self.parent().parent().find("label").first();
                        label_text = label.text();
                        label.detach();
                        self.iCheck({
                                checkboxClass: "icheckbox_line-blue place-left",
                                radioClass: "iradio_line-blue place-left",
                                insert: "<div class=\"icheck_line-icon\"></div>" + label_text
                        });
               });
    ';

        return $js;
    }

    public function printVideoEntry($videoFeed, $_id, $error = false)
    {
        global $class_filter, $db, $cfg, $language;

        $DEVELOPER_KEY = $cfg['youtube_api_key'];
        $client        = new Google_Client();
        $client->setDeveloperKey($DEVELOPER_KEY);
        $youtube = new Google_Service_YouTube($client);

        $videosResponse = $youtube->videos->listVideos('snippet,contentDetails,statistics,status', array('id' => $_id));

        foreach ($videosResponse['items'] as $videoResult) {

            $category = str_replace(' ', '%', VForm::clearTag($language['backend.import.feed.categ.list'][$videoResult['snippet']['categoryId']]));
            $dbc      = $db->execute(sprintf("SELECT `ct_id`, `ct_name` FROM `db_categories` WHERE `ct_type`='video' AND `ct_active`='1' AND `ct_name` LIKE '%s' OR `ct_descr` LIKE '%s';", '%' . $category . '%', '%' . $category . '%'));

            $db_uid = $class_filter->clr_str($_POST['assign_username']);
            $date   = new DateTime('1970-01-01');
            $date->add(new DateInterval($videoResult['contentDetails']['duration']));
            $duration      = $date->format('H:i:s');
            $d             = explode(':', $duration);
            $db_duration   = ($d[0] * 60 * 60) + ($d[1] * 60) + ($d[2]);
            $db_categ_id   = intval($dbc->fields['ct_id']);
            $db_categ_name = $dbc->fields['ct_name'];
            $db_title      = $videoResult['snippet']['title'];
            $db_tags       = self::yt_tags($class_filter->clr_str($db_title));
            $db_desc       = $videoResult['snippet']['description'];
            $db_rkey       = $_id;
            $tmb           = array(
                0 => "http://i.ytimg.com/vi/" . $db_rkey . "/maxresdefault.jpg",
                1 => "http://i.ytimg.com/vi/" . $db_rkey . "/mqdefault.jpg",
                2 => "http://i.ytimg.com/vi/" . $db_rkey . "/mqdefault.jpg",
                3 => "http://i.ytimg.com/vi/" . $db_rkey . "/mqdefault.jpg",
            );

            $html = !$error ? self::saveCheck($db_rkey, array(
                "title"       => $db_title,
                "description" => $db_desc,
                "tags"        => $db_tags,
                "category"    => ((isset($_POST['assign_category_feed']) and $_POST['assign_category_feed'] != '') ? intval($_POST['assign_category_feed']) : ((isset($_POST['assign_category_channel']) and $_POST['assign_category_channel'] != '') ? intval($_POST['assign_category_channel']) : ($db_categ_id > 0 ? $db_categ_id : self::getOther()))),
                "duration"    => $db_duration,
                "key"         => $db_rkey,
                "src"         => 'youtube',
                "url"         => 'http://www.youtube.com/watch?v=' . $db_rkey,
                "tmb"         => $tmb)) : null;

            $html .= VGenerate::simpleDivWrap('row embed-title', '', $db_title);
            $html .= VGenerate::simpleDivWrap('row embed-description', '', VUserinfo::truncateString($db_desc, 200));
            $html .= VGenerate::simpleDivWrap('row embed-category greyed-out', '', $language['backend.import.category'] . $db_categ_name);
        }

        return $html;
    }

    public function getAndPrintStandardFeeds($m)
    {
        global $class_filter, $cfg;

        $DEVELOPER_KEY = $cfg['youtube_api_key'];
        $token         = (isset($_GET['token']) and $_GET['token'] != "undefined") ? $class_filter->clr_str($_GET['token']) : false;

        switch ($m) {
            case "m1": //video feeds
                $feed_region   = $class_filter->clr_str($_POST['feed_region']);
                $feed_category = $class_filter->clr_str($_POST['feed_categ']);
                $feed_def      = $class_filter->clr_str($_POST['feed_def']);
                $feed_dim      = $class_filter->clr_str($_POST['feed_dim']);
                $feed_dur      = $class_filter->clr_str($_POST['feed_dur']);
                $feed_emb      = $class_filter->clr_str($_POST['feed_emb']);
                $feed_lic      = $class_filter->clr_str($_POST['feed_lic']);
                $feed_syn      = $class_filter->clr_str($_POST['feed_syn']);
                $feed_type     = $class_filter->clr_str($_POST['feed_type']);
                $feed_search   = str_replace(' ', '+', $class_filter->clr_str($_POST['be_search_term']));

                $region   = $feed_region != '' ? '&amp;regionCode=' . $feed_region : null;
                $category = $feed_category != '' ? '&videoCategoryId=' . $feed_category : null;
                $def      = $feed_def != '' ? '&videoDefinition=' . $feed_def : null;
                $dim      = $feed_dim != '' ? '&videoDimension=' . $feed_dim : null;
                $dur      = $feed_dur != '' ? '&videoDuration=' . $feed_dur : null;
                $emb      = $feed_emb != '' ? '&videoEmbeddable=' . $feed_emb : null;
                $lic      = $feed_lic != '' ? '&videoLicense=' . $feed_lic : null;
                $syn      = $feed_syn != '' ? '&videoSyndicated=' . $feed_syn : null;
                $type     = $feed_type != '' ? '&videoType=' . $feed_type : null;
                $search   = $feed_search != '' ? '&q=' . $feed_search : null;
                $page     = $token ? '&pageToken=' . $token : null;
                $max      = (int) $_POST['yt_video_results'];

                $YOUTUBE_FEED = 'https://www.googleapis.com/youtube/v3/search?part=snippet' . $search . '&maxResults=' . $max . $region . $def . $dim . $dur . $emb . $lic . $syn . $type . '&type=video' . $category . '&key=' . $DEVELOPER_KEY . $page;
                break;
            case "m2": //channel feeds
                $username_get = $class_filter->clr_str(trim($_POST['feed_channel_yt']));
                $ch_id        = self::getYTchannelID($username_get);
                $search       = '&channelId=' . ($ch_id != '' ? $ch_id : $username_get);
                $page         = $token ? '&pageToken=' . $token : null;
                $max          = (int) $_POST['yt_channel_results'];

                $YOUTUBE_FEED = 'https://www.googleapis.com/youtube/v3/search?part=snippet' . $search . '&maxResults=' . $max . '&type=video&order=date&key=' . $DEVELOPER_KEY . $page;
                break;
        }

        return self::getAndPrintVideoFeed($YOUTUBE_FEED, $m);
    }
    /* end youtube video feeds functions */

    public function getYTchannelID($username)
    {
        global $cfg;

        $api_url = 'https://www.googleapis.com/youtube/v3/channels?part=snippet&forUsername=' . $username . '&key=' . $cfg['youtube_api_key'];

        $result = json_decode(VServer::curl_tt($api_url));

        return $result->items[0]->id;
    }

    /* generate various select lists */
    public function selectList($name, $for, $show_blank)
    {
        global $db, $language;

        $_ht = '<select name="' . $name . '" class="select-input wd200">';
        $_ht .= $show_blank == 1 ? '<option value=""> -- </option>' : null;

        $feed_type_ch  = array('uploads', 'favorites');
        $feed_time     = array('today', 'this_week', 'this_month', 'all_time');
        $feed_vimeo    = array('videos', 'likes', 'appears_in', 'subscriptions');
        $feed_dm       = array('videos', 'favorites', 'features', 'subscriptions');
        $feed_dm_type  = array('featured', 'hd', 'official', 'creative', 'creative-official', 'buzz', 'buzz-premium', '3d', 'live');
        $c             = 0;
        $yt_categ      = $language['backend.import.feed.categ.list'];
        $feed_yt_categ = array_keys($yt_categ);
        $feed_dm_categ = array('animals', 'creation', 'auto', 'school', 'shortfilms', 'fun', 'videogames', 'gaylesbian', 'kids', 'lifestyle', 'music', 'news', 'people', 'sexy', 'sport', 'tv', 'tech', 'travel', 'webcam');
        $feed_dm_sort  = array('recent', 'visited', 'visited-hour', 'visited-today', 'visited-week', 'visited-month', 'commented', 'commented-hour', 'commented-today', 'commented-week', 'commented-month', 'rated', 'rated-hour', 'rated-today', 'rated-week', 'rated-month', 'ranking');
        $search        = array('youtube', 'dailymotion', 'metacafe', 'vimeo');
        $feed_categ    = array();
        $rs            = $db->execute("SELECT `ct_id` FROM `db_categories` WHERE `ct_type`='video' AND `ct_active`='1';");
        while (!$rs->EOF) {
            $feed_categ[] = $rs->fields['ct_id'];
            $rs->MoveNext();
        }

        foreach ($for as $k => $val) {
            switch ($name) {
                case "feed_type_ch":
                    $ov = $feed_type_ch[$k];
                    break;
                case "feed_type_dm":
                    $ov = $feed_dm[$k];
                    break;
                case "feed_type_dm_video":
                    $ov = $feed_dm_type[$k];
                    break;
                case "feed_time":
                    $ov = $feed_time[$k];
                    break;
                case "feed_type_vimeo":
                    $ov = $feed_vimeo[$k];
                    break;
                case "assign_category_dm_video":
                    $ov = $feed_dm_categ[$k];
                    break;
                case "search_on":
                    $ov = $search[$k];
                    break;
                case "feed_categ":
                    $ov = $feed_yt_categ[$c];
                    $c  = $c + 1;
                    break;
                case "assign_category_feed":
                case "assign_category_channel":
                case "assign_category_dm_type":
                case "assign_category_dm":
                case "assign_category_vimeo":
                case "assign_category_file":
                    $ov = $feed_categ[$k];
                    break;
                case "feed_sort_dm_video":
                    $ov = $feed_dm_sort[$k];
                    break;
                case "feed_results":
                case "feed_results_ch":
                case "feed_results_vimeo":
                case "feed_results_dm":
                case "feed_results_dm_type":
                    $ov = str_replace(' ', '_', strtolower($val));
                    break;
                default:
                    $ov = $val;
                    break;
            }
            $_ht .= '<option value="' . trim($ov) . '">' . trim($val) . '</option>';
        }
        $_ht .= '</select>';

        return $_ht;
    }
    /* get category list */
    public function getCategoryList()
    {
        require_once 'f_core/config.backend.php';
        global $db, $cfg;

        $c        = array();
        $_section = (strstr($_SERVER['REQUEST_URI'], $backend_access_url) == true) ? 'backend' : 'frontend';

        $res = $db->execute("SELECT `ct_name`, `ct_lang` FROM `db_categories` WHERE `ct_type`='video' AND `ct_active`='1';");
        if ($res->fields['ct_name']) {
            while (!$res->EOF) {
                if ($res->fields['ct_lang'] != '') {
                    $cl  = unserialize($res->fields['ct_lang']);
                    $ln  = ((isset($_SESSION['be_lang']) and $_section == 'backend') ? $_SESSION['be_lang'] : ((isset($_SESSION['fe_lang']) and $_section == 'frontend') ? $_SESSION['fe_lang'] : 'en_US'));
                    $c[] = $ln != 'en_US' ? $cl[$ln] : $res->fields['ct_name'];
                } else {
                    $c[] = $res->fields['ct_name'];
                }

                $res->MoveNext();
            }
        }
        return $c;
    }
    /* get username list */
    public function getUsernameList()
    {
        global $db;

        $u = array();

        $res = $db->execute("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_status`='1' ORDER BY `usr_user`;");

        if ($res->fields['usr_key']) {
            return $res;
        }
    }
    /* generate html username select list */
    public function username_selectList($name)
    {
        $input_name = $name ? 'assign_username_' . $name : 'assign_username';
        $input_id   = str_replace('_', '-', $input_name);

        $html = '<input type="text" name="' . $input_name . '_key" id="' . $input_id . '-key" class="assign-username">';
        $html .= '<input type="text" name="' . $input_name . '" id="' . $input_id . '" class="assign-username-value no-display">';

        return $html;
    }
}
