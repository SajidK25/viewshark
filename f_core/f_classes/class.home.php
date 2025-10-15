<?php
/*******************************************************************************************************************
| Software Name        : ViewShark
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) ViewShark
| Website              : https://www.viewshark.com
| E-mail               : support@viewshark.com || viewshark@gmail.com
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the ViewShark End-User License Agreement, available online at:
| https://www.viewshark.com/support/license/
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2013-2024 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

class VHome
{
    private static $cfg;
    private static $db;
    private static $db_cache;
    private static $dbc;
    private static $filter;
    private static $class_language;
    private static $language;
    private static $href;
    private static $home_cfg = false;
    private static $mod      = array('live', 'video', 'short', 'image', 'audio', 'document', 'blog');

    public function __construct($_type = false)
    {
        require 'f_core/config.href.php';

        global $cfg, $class_filter, $class_database, $db, $language, $class_language;

        self::$cfg            = $cfg;
        self::$db             = $db;
        self::$dbc            = $class_database;
        self::$filter         = $class_filter;
        self::$language       = $language;
        self::$class_language = $class_language;
        self::$href           = $href;

        if ((int) $_SESSION['USER_ID'] > 0) {
            self::$home_cfg = unserialize($class_database->singleFieldValue('db_accountuser', 'home_cfg', 'usr_id', (int) $_SESSION['USER_ID']));
        }

        self::$db_cache = false; //change here to enable caching
    }

    /* content layout */
    public static function doLayout()
    {
        $cfg      = self::$cfg;
        $language = self::$language;

        $html = '
        <div id="home-content">
            ' . VGenerate::advHTML(1) . '

            ' . (isset($_GET['fsn']) ? VGenerate::noticeTpl('', '', $language['notif.success.subscribe']) : null) . '

            ' . ($cfg['live_module'] == 1 ? self::featuredMedia('live', 0) : null) . '

            ' . ($cfg['video_module'] == 1 ? self::featuredMedia('video', 1) : null) . '

            ' . ($cfg['short_module'] == 1 ? self::shortsMedia('latest') : null) . '

            ' . ($cfg['video_module'] == 1 ? self::featuredMedia('video', 0, false, ' r-section') : null) . '

            ' . ($cfg['short_module'] == 1 ? self::shortsMedia('featured') : null) . '

            ' . VGenerate::advHTML(2);

        $html .= self::getFeaturedChannels();
        $html .= self::getFeaturedCategories();
        $html .= '</div>';

        /* subscribe/unsubscribe action */
        if ($cfg['user_subscriptions'] == 1) {
            $ht_js = 'c_url = "' . $cfg['main_url'] . '/' . VHref::getKey('watch') . '?a";';
            $ht_js .= '$(document).on("click", ".unsubscribe-action", function(e){';
            $ht_js .= 'rel = $(this).attr("rel-usr"); if (rel === "' . $_SESSION['USER_KEY'] . '") return;';
            $ht_js .= 'if($("#sub-wrap .sub-txt:first").text()=="' . $language['frontend.global.unsubscribed'] . '")return;';
            $ht_js .= '$("#sub-wrap .sub-txt:first").text("' . $language['frontend.global.loading'] . '");return;';
            $ht_js .= '$.post(c_url+"?do=user-unsubscribe", $("#user-files-form").serialize(), function(data){';
            $ht_js .= '$("#sub-wrap .sub-txt:first").text("' . $language['frontend.global.unsubscribed'] . '");';
            $ht_js .= '});';
            $ht_js .= '});';
        }
        /* follow/unfollow action */
        if ($cfg['user_follows'] == 1) {
            $ht_js .= 'c_url = "' . $cfg['main_url'] . '/' . VHref::getKey('watch') . '?a";';
            $ht_js .= '$(document).on("click", ".follow-action", function(e){';
            $ht_js .= 'rel = $(this).attr("rel-usr"); if (rel === "' . $_SESSION['USER_KEY'] . '") return;';
            $ht_js .= '$(".follow-txt-"+rel).text("' . $language['frontend.global.loading'] . '");';
            $ht_js .= '$.post(c_url+"&do=user-follow", $("#user-files-form-"+rel).serialize(), function(data){';
            $ht_js .= '$(".follow-txt-"+rel).text("' . $language['frontend.global.followed'] . '");';
            $ht_js .= '});';
            $ht_js .= '});';
            $ht_js .= '$(document).on("click", ".unfollow-action", function(e){';
            $ht_js .= 'rel = $(this).attr("rel-usr"); if (rel === "' . $_SESSION['USER_KEY'] . '") return;';
            $ht_js .= '$(".follow-txt-"+rel).text("' . $language['frontend.global.loading'] . '");';
            $ht_js .= '$.post(c_url+"&do=user-unfollow", $("#user-files-form-"+rel).serialize(), function(data){';
            $ht_js .= '$(".follow-txt-"+rel).text("' . $language['frontend.global.unfollowed'] . '");';
            $ht_js .= '});';
            $ht_js .= '});';
        }

        $html .= $ht_js != '' ? '
                                <script type="text/javascript">
                                        $(function(){' . $ht_js . '});
                                </script>
                        ' : null;

        return $html;
    }

    private static function getFeaturedCategories($type = 'video')
    {
        $db       = self::$db;
        $language = self::$language;
        $cfg      = self::$cfg;
        $uid      = (int) $_SESSION['USER_ID'];
        $html     = null;

        switch ($type[0]) {
            case "l":$href_key = 'broadcasts';
                break;
            case "v":$href_key = 'videos';
                break;
            case "i":$href_key = 'images';
                break;
            case "a":$href_key = 'audios';
                break;
            case "d":$href_key = 'documents';
                break;
            case "b":$href_key = 'blogs';
                break;
        }
        $sql = sprintf("SELECT `ct_id`, `ct_name`, `ct_slug`, `ct_icon` FROM `db_categories` WHERE `ct_type`='%s' AND `ct_featured`='1' AND `ct_active`='1' ORDER BY RAND() LIMIT 5;", $type);
        $rs  = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_channels'], $sql) : self::$db->execute($sql);

        $ids   = [];
        $names = [];
        $icons = [];
        $slugs = [];

        if ($rs->fields['ct_id']) {
            while (!$rs->EOF) {
                $ct_id         = $rs->fields['ct_id'];
                $ids[]         = $ct_id;
                $names[$ct_id] = $rs->fields['ct_name'];
                $icons[$ct_id] = $rs->fields['ct_icon'];
                $slugs[$ct_id] = $rs->fields['ct_slug'];

                $rs->MoveNext();
            }
        }

        $sql = sprintf("WITH RankedVideos AS (
                        SELECT
                            A.`file_key`,
                            A.`file_views`,
                            A.`file_duration`,
                            A.`file_like`,
                            A.`file_comments`,
                            A.`thumb_server`,
                            A.`thumb_cache`,
                            A.`upload_date`,
                            A.`stream_live`,
                            A.`thumb_preview`,
                            A.`file_title`,
                            A.`file_category`,
                            D.`usr_dname`,
                            D.`ch_title`,
                            D.`usr_photo`,
                            D.`usr_profileinc`,
                            D.`usr_partner`,
                            D.`usr_affiliate`,
                            D.`affiliate_badge`,
                            D.`usr_id`,
                            D.`usr_key`,
                            D.`usr_user`,
                            ROW_NUMBER() OVER (PARTITION BY A.`file_category` ORDER BY A.`db_id` DESC) AS rn
                        FROM
                            (SELECT DISTINCT `ct_id` FROM `db_categories` WHERE `ct_type` = '%s' AND `ct_id` IN (%s) LIMIT 5) AS sub
                            JOIN `db_%sfiles` AS A ON sub.`ct_id` = A.`file_category`
                            JOIN `db_accountuser` AS D ON A.`usr_id` = D.`usr_id`
                        WHERE
                            A.`privacy` = 'public'
                            AND A.`approved` = '1'
                            AND A.`deleted` = '0'
                            AND A.`active` = '1'
                        )
                        SELECT *
                        FROM RankedVideos
                        WHERE rn <= 5
                        ORDER BY `file_category`, `rn`", $type, implode(',', $ids), $type);

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_channels'], $sql) : self::$db->execute($sql);

        $html            = null;
        $ares            = 'categ';
        $default_section = 'featured';
        $main_section    = 'featured_section recommended_section';

        $user_watchlist = VBrowse::watchlistEntries($type);

        $results = [];

        if ($res->fields['usr_id']) {
            while (!$res->EOF) {
                $index[$ct_id] = 0;

                foreach ($ids as $ct_id) {
                    if ($res->fields['file_category'] == $ct_id) {
                        if ($index[$ct_id] <= 5) {
                            $results[$ct_id][] = [
                                "file_title"      => $res->fields['file_title'],
                                "usr_user"        => $res->fields['usr_user'],
                                "usr_dname"       => $res->fields['usr_dname'],
                                "ch_title"        => $res->fields['ch_title'],
                                "file_key"        => $res->fields['file_key'],
                                "usr_key"         => $res->fields['usr_key'],
                                "usr_id"          => $res->fields['usr_id'],
                                "usr_affiliate"   => $res->fields['usr_affiliate'],
                                "usr_partner"     => $res->fields['usr_partner'],
                                "affiliate_badge" => $res->fields['affiliate_badge'],
                                "thumb_server"    => $res->fields['thumb_server'],
                                "thumb_cache"     => $res->fields['thumb_cache'],
                                "usr_photo"       => $res->fields['usr_photo'],
                                "usr_profileinc"  => $res->fields['usr_profileinc'],
                                "stream_live"     => $res->fields['stream_live'],
                                "upload_date"     => $res->fields['upload_date'],
                                "file_duration"   => $res->fields['file_duration'],
                                "file_views"      => $res->fields['file_views'],
                                "file_like"       => $res->fields['file_like'],
                                "file_comments"   => $res->fields['file_comments'],
                                "thumb_preview"   => $res->fields['thumb_preview'],
                            ];
                        }

                        $index[$ct_id] += 1;
                    }
                }

                $res->MoveNext();
            }
        }

        foreach ($results as $ct_id => $result) {
            $_heading = '<i class="' . $icons[$ct_id] . '"></i><a href="' . $cfg['main_url'] . '/' . $href_key . '/' . $slugs[$ct_id] . '">' . $names[$ct_id] . '</a>';

            $html .= '      <div class="clearfix"></div>
                <section class="' . $main_section . ' ' . $default_section . '_' . $href_key . '">
                    <div class="container">
                    <article>
                        <h2 class="content-title">
                            <span class="heading">' . $_heading . '</span>
                        </h2>
                    </article>
                    <article>
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '" class="homeContent ' . $default_section . ' ' . $type . '">
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '-list">
            ';

            if ($results[$ct_id]) {
                $html .= VBrowse::viewMode1($results[$ct_id], $user_watchlist, $type, true);
            }

            $html .= '                  </div>
                        </div>
                    </article>
                    </div>
                </section>';

            $html .= '<div class="clearfix"></div>';
            $html .= '<div class="line"></div>';
        }

        return $html;
    }
    private function getFeaturedChannels($page = 1, $type = 'video')
    {
        $db       = self::$db;
        $language = self::$language;
        $cfg      = self::$cfg;
        $uid      = (int) $_SESSION['USER_ID'];

        $_q = "AND A.`usr_featured`='1' ORDER BY RAND()";

        if ($page) {
            switch ($page) {
                case 3:
                    $lim = "10, 5";
                    break;

                case 2:
                    $lim = "5, 5";
                    break;

                default:
                    $lim = "0, 5";
                    break;
            }
        }

        switch ($type[0]) {
            case "l":$href_key = 'broadcasts';
                break;
            case "v":$href_key = 'videos';
                break;
            case "i":$href_key = 'images';
                break;
            case "a":$href_key = 'audios';
                break;
            case "d":$href_key = 'documents';
                break;
            case "b":$href_key = 'blogs';
                break;
        }

        $sql = sprintf("SELECT
                    A.`usr_id`, A.`usr_key`, A.`usr_user`, A.`usr_affiliate`, A.`usr_partner`,
                    A.`usr_dname`, A.`ch_views`, A.`ch_title`, A.`affiliate_badge`, A.`usr_photo`, A.`usr_profileinc`
                    FROM
                    `db_accountuser` A
                    WHERE
                    A.`usr_status`='1'
                    %s
                    %s
                    LIMIT %s;", $_q, ($uid > 0 ? (is_array($ids) ? "ORDER BY FIND_IN_SET(A.`usr_id`, '" . implode(',', $ids) . "')" : null) : null), $lim);

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_channels'], $sql) : self::$db->execute($sql);

        $ids   = [];
        $keys  = [];
        $uuser = [];

        if ($res->fields['usr_id']) {
            while (!$res->EOF) {
                $usr_id         = $res->fields['usr_id'];
                $ids[]          = (int) $usr_id;
                $keys[$usr_id]  = $res->fields['usr_key'];
                $uuser[$usr_id] = $res->fields['usr_user'];

                $res->MoveNext();
            }

            shuffle($ids);
        }

        $sql = sprintf("WITH RankedVideos AS (
                        SELECT
                            A.`file_key`,
                            A.`file_views`,
                            A.`file_duration`,
                            A.`file_like`,
                            A.`file_comments`,
                            A.`thumb_server`,
                            A.`thumb_cache`,
                            A.`upload_date`,
                            A.`stream_live`,
                            A.`thumb_preview`,
                            A.`file_title`,
                            A.`file_category`,
                            D.`usr_dname`,
                            D.`ch_title`,
                            D.`usr_photo`,
                            D.`usr_profileinc`,
                            D.`usr_partner`,
                            D.`usr_affiliate`,
                            D.`affiliate_badge`,
                            D.`usr_id`,
                            D.`usr_key`,
                            D.`usr_user`,
                            D.`usr_followcount`,
                            D.`usr_subcount`,
                            ROW_NUMBER() OVER (PARTITION BY A.`usr_id` ORDER BY A.`db_id` DESC) AS rn
                        FROM
                            (SELECT DISTINCT `usr_id` FROM `db_accountuser` WHERE `usr_id` IN (%s)) AS sub
                            JOIN `db_%sfiles` AS A ON sub.`usr_id` = A.`usr_id`
                            JOIN `db_accountuser` AS D ON A.`usr_id` = D.`usr_id`
                        WHERE
                            A.`privacy` = 'public'
                            AND A.`approved` = '1'
                            AND A.`deleted` = '0'
                            AND A.`active` = '1'
                        )
                        SELECT *
                        FROM RankedVideos
                        WHERE rn <= 5
                        ORDER BY `usr_id`, `rn`;", implode(',', $ids), $type);

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_channels'], $sql) : self::$db->execute($sql);

        $html            = null;
        $ares            = 'categ';
        $default_section = 'featured';
        $main_section    = 'featured_section recommended_section';

        $user_watchlist = VBrowse::watchlistEntries($type);

        $results = [];

        if ($res->fields['usr_id']) {
            while (!$res->EOF) {
                $index[$usr_id] = 0;

                foreach ($ids as $usr_id) {
                    if ($res->fields['usr_id'] == $usr_id) {
                        if ($index[$usr_id] <= 5) {
                            $results[$usr_id][] = [
                                "file_title"      => $res->fields['file_title'],
                                "usr_user"        => $res->fields['usr_user'],
                                "usr_dname"       => $res->fields['usr_dname'],
                                "ch_title"        => $res->fields['ch_title'],
                                "file_key"        => $res->fields['file_key'],
                                "usr_key"         => $res->fields['usr_key'],
                                "usr_id"          => $res->fields['usr_id'],
                                "usr_affiliate"   => $res->fields['usr_affiliate'],
                                "usr_partner"     => $res->fields['usr_partner'],
                                "affiliate_badge" => $res->fields['affiliate_badge'],
                                "thumb_server"    => $res->fields['thumb_server'],
                                "thumb_cache"     => $res->fields['thumb_cache'],
                                "usr_photo"       => $res->fields['usr_photo'],
                                "usr_profileinc"  => $res->fields['usr_profileinc'],
                                "stream_live"     => $res->fields['stream_live'],
                                "upload_date"     => $res->fields['upload_date'],
                                "file_duration"   => $res->fields['file_duration'],
                                "file_views"      => $res->fields['file_views'],
                                "file_like"       => $res->fields['file_like'],
                                "file_comments"   => $res->fields['file_comments'],
                                "thumb_preview"   => $res->fields['thumb_preview'],
                            ];
                        }

                        $index[$usr_id] += 1;
                    }
                }

                $res->MoveNext();
            }
        }

        foreach ($results as $usr_id => $result) {
            $usr_id           = $result[0]['usr_id'];
            $usr_key          = $result[0]['usr_key'];
            $usr_user         = $result[0]['usr_user'];
            $user_followtotal = $result[0]['usr_followcount'];
            $user_subtotal    = $result[0]['usr_subcount'];
            $_user            = ($result[0]['usr_dname'] != '' ? $result[0]['usr_dname'] : ($result[0]['ch_title'] != '' ? $result[0]['ch_title'] : $result[0]['usr_user']));
            $_heading         = '<a href="' . VHref::channelURL(["username" => $result[0]['usr_user']]) . '">' . $_user . VAffiliate::affiliateBadge($result[0]['usr_affiliate'], $result[0]['affiliate_badge']) . '</a>';
            $bg_url           = VUseraccount::getProfileImage_inc($usr_key, $result[0]['usr_photo'], $result[0]['usr_profileinc']);
            $_icon            = '<img alt="' . $_user . '" title="' . $_user . '" height="32" src="' . $bg_url . '" />';

            $html .= '      <div class="clearfix"></div>
                <section class="' . $main_section . ' ' . $default_section . '_' . $href_key . '">
                    <div class="container">
                    <article>
                        <h2 class="content-title">
                            ' . $_icon . '
                            <span class="heading">' . $_heading . '</span>
                        </h2>
                    </article>
                    <article>
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '" class="homeContent ' . $default_section . ' ' . $type . '">
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '-list">
            ';

            if ($results[$usr_id]) {
                $html .= VBrowse::viewMode1($results[$usr_id], $user_watchlist, $type, true);
            }

            $html .= '                  </div>
                        </div>
                    </article>
                    </div>
                </section>';

            $html .= '<div class="clearfix"></div>';
            $html .= '<div class="line"></div>';
        }

        return $html;
    }

    /* list media content
     * $ares = true trending
     * $ares = false recommended/featured
     * $ares = array subscriptions/featured channels
     */
    public function featuredMedia($type = 'video', $ares = false, $ct_id = false, $extra_class = null)
    {
        $cfg      = self::$cfg;
        $db       = self::$db;
        $language = self::$language;

        switch ($type[0]) {
            case "l":$href_key = 'broadcasts';
                break;
            case "v":$href_key = 'videos';
                break;
            case "i":$href_key = 'images';
                break;
            case "a":$href_key = 'audios';
                break;
            case "d":$href_key = 'documents';
                break;
            case "b":$href_key = 'blogs';
                break;
        }

        $_o = null;
        if (is_array($ares)) {
            $usr_key   = $ares['usr_key'];
            $usr_photo = $ares['usr_photo'];
            $usr_inc   = $ares['usr_profileinc'];
            $_q        = sprintf("AND %sB.`usr_id`='%s'%s", ((int) $_SESSION['USER_ID'] == 0 ? "(" : null), $ares['usr_id'], ((int) $_SESSION['USER_ID'] == 0 ? ")" : null));
            $_o        = "ORDER BY B.`upload_date` DESC";

            $usr_affiliate = $ares['usr_affiliate'];
            $usr_partner   = $ares['usr_partner'];
            $usr_affiliate = ($usr_affiliate == 1 or $usr_partner == 1) ? 1 : 0;
            $af_badge      = $ares['affiliate_badge'];

            $_user    = ($ares['usr_dname'] != '' ? $ares['usr_dname'] : ($ares['ch_title'] != '' ? $ares['ch_title'] : $ares['usr_user']));
            $_heading = '<a href="' . VHref::channelURL(["username" => $ares['usr_user']]) . '">' . $_user . VAffiliate::affiliateBadge($usr_affiliate, $af_badge) . '</a>';
            $bg_url   = VUseraccount::getProfileImage_inc($usr_key, $usr_photo, $usr_inc);
            $_icon    = '<img alt="' . $_user . '" title="' . $_user . '" height="32" src="' . $bg_url . '" />';

            $default_section = 'channel-' . $usr_key;
            $main_section    = 'channel_section';
        } else {
            if ((int) $_SESSION['USER_ID'] > 0 and !$ares) {
                $_q = ($type != 'live' and !$ct_id) ? "AND B.`is_featured`='1' ORDER BY RAND() " : null;

            } elseif ((int) $_SESSION['USER_ID'] > 0 and $ares == 'categ') {
                $_q = "AND B.`is_featured`='1' ";

            } else {
                $_q = (($ct_id > 0 and !$ares) ? null : ($type != 'live' ? "AND B.`is_featured`='1' ORDER BY RAND() " : null));
            }

            if (!$ares) {
                $_heading = ($type == 'live' ? '<i class="icon-live"></i>' . $language['frontend.global.live.now'] : $language['frontend.global.recommended']);
                if ($ct_id > 0) {
                    $csql = $db->execute(sprintf("SELECT `ct_name`, `ct_icon`, `ct_type`, `ct_slug` FROM `db_categories` WHERE `ct_id`='%s' LIMIT 1;", $ct_id));
                    switch ($csql->fields['ct_type']) {
                        case "video":$u = self::$href['videos'];
                            break;
                        case "live":$u = self::$href['broadcasts'];
                            break;
                        case "image":$u = self::$href['images'];
                            break;
                        case "audio":$u = self::$href['audios'];
                            break;
                        case "document":$u = self::$href['documents'];
                            break;
                        case "blog":$u = self::$href['blogs'];
                            break;
                    }
                    $_heading = '<i class="' . $csql->fields['ct_icon'] . '"></i><a href="' . $cfg['main_url'] . '/' . $u . '/' . $csql->fields['ct_slug'] . '">' . $csql->fields['ct_name'] . '</a>';
                }
                $default_section = 'featured';
                $main_section    = 'featured_section recommended_section';
            } elseif ($ares and $ares !== 'categ') {
                $_q              = "AND (B.`upload_date` BETWEEN DATE_SUB(NOW(), INTERVAL 365 DAY) AND NOW()) AND B.`file_views` > 0 ORDER BY RAND() ";
                $_heading        = $language['frontend.global.trending'];
                $default_section = 'recommended';
                $main_section    = 'featured_section';
                $_icon           = 'icon-fire';

            }

            $_icon    = '<i class="' . (($ares and $ares !== "categ") ? 'icon-fire' : ($ares == 'categ' ? $_cticon : ($type == 'doc' ? 'icon-file' : ($type == 'blog' ? 'icon-pencil2' : 'icon-' . $type)))) . '"></i>';
            $sub_html = null;
        }

        if (isset($_GET['rc']) and isset($_GET['rn'])) {
            $nr  = (int) $_GET['rn'];
            $lim = sprintf("%s, %s", $nr, $nr);
        } else {
            $lim = ($ct_id > 0) ? 5 : 10;
        }

        $_q .= ($ct_id > 0 and !$ares) ? "AND B.`file_category`='" . $ct_id . "' ORDER BY B.`db_id` DESC " : null;

        $sql = sprintf("SELECT
                B.`file_key`, B.`old_file_key`, B.`old_key` AS `fkey`, B.`file_title`, B.`thumb_preview`,
                B.`file_hd`, B.`file_views`, B.`file_duration`, B.`file_comments`, B.`file_like`, B.`upload_date`, B.`thumb_server`, B.`thumb_cache`, B.`stream_live`,
                C.`usr_affiliate`, C.`usr_partner`, C.`affiliate_badge`, C.`usr_id`, C.`usr_key`, C.`old_usr_key`, C.`old_key` AS `ukey`, C.`usr_user`, C.`usr_dname`, C.`ch_title`, C.`usr_photo`, C.`usr_profileinc`
                FROM
                `db_%sfiles` B, `db_accountuser` C
                WHERE
                B.`usr_id`=C.`usr_id` AND
                " . ($type == 'live' ? "B.`stream_live`='1' AND" : null) . "
                B.`privacy`='public' AND
                B.`approved`='1' AND
                B.`deleted`='0' AND
                B.`active`='1'
                %s %s LIMIT %s;", $type, $_q, $_o, (!$ares ? $lim : 5));

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_media'], $sql) : self::$db->execute($sql);

        if (!$res->fields['file_key']) {
            return;
        }

        if (isset($_GET['rc']) and isset($_GET['rn'])) {
            $user_watchlist = VBrowse::watchlistEntries($type);

            $html = VBrowse::viewMode1($res, $user_watchlist, $type, (!$ares ? true : false));

            return $html;
        }

        $html = '   <div class="clearfix"></div>
                <section class="' . $main_section . ' ' . $default_section . '_' . $href_key . $extra_class .'">
                    <div class="container">
                    <article>
                        <h2 class="content-title">
                            ' . $_icon . '<span class="heading">' . $_heading . '</span>
                        </h2>
                        ' . $sub_html . '
                    </article>
                    <article>
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '" class="homeContent ' . $default_section . ' ' . $type . '">
                            <div id="main-view-mode-1-' . $default_section . '-' . $type . '-list">
            ';

        if ($res->fields['file_key']) {
            $user_watchlist = VBrowse::watchlistEntries($type);

            $html .= VBrowse::viewMode1($res, $user_watchlist, $type, (!$ares ? true : false));
        }
        $html .= '          </div>
                        </div>
                    </article>
                    </div>
                </section>';

        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="line"></div>';

        return $html;
    }

/* get my subs/follows count */
    public function getSubCount($follow_count = false)
    {
        global $class_database, $cfg;

        $user_id   = (int) $_SESSION['USER_ID'];
        $sub_cache = self::$db_cache ? $cfg['cache_view_sub_id'] : false;

        if (!$follow_count and $cfg['user_subscriptions'] == 1) {
            return $class_database->singleFieldValue('db_accountuser', 'usr_subcount', 'usr_id', $user_id, $sub_cache);
        } elseif ($follow_count and $cfg['user_follows'] == 1) {
            return $class_database->singleFieldValue('db_accountuser', 'usr_followcount', 'usr_id', $user_id, $sub_cache);
        }
    }
    public function shortsMedia($show = 'latest')
    {
        $cfg      = self::$cfg;
        $language = self::$language;

        $href_key        = 'home';
        $main_section    = 'shorts';
        $default_section = $type . '-' . $main_section;
        $_icon           = '<i class="icon-clock"></i>';
        $_heading        = $language['frontend.global.shorts'];
        $user_theme      = isset($_SESSION['USER_THEME']) ? $_SESSION['USER_THEME'] : $_SESSION['theme_name'];
        $def_thumb       = $cfg['global_images_url'] . '/loading-' . (strpos($user_theme, 'dark') !== false ? 'dark' : 'light') . '-shorts.gif';
        $vpv             = true;
        $shorts          = VBrowse::getShorts($show);

        $items = [];

        if ($shorts->fields['file_key']) {
            while (!$shorts->EOF) {
                $file_key      = $shorts->fields['file_key'];
                $usr_key       = $shorts->fields['usr_key'];
                $thumb_server  = $shorts->fields['thumb_server'];
                $thumb_cache   = $shorts->fields['thumb_cache'];
                $thumb_cache   = $thumb_cache > 1 ? $thumb_cache : null;
                $title         = $shorts->fields['file_title'];
                $url           = $cfg['main_url'] . '/' . VHref::getKey("shorts") . '/' . $file_key;
                $duration      = VFiles::fileDuration($shorts->fields['file_duration']);
                $duration_show = 1;
                $views         = VFiles::numFormat($shorts->fields['file_views']);
                $_rel_v        = $shorts->fields['embed_src'] == 'local' ? md5($file_key . '_preview') : null;

                if ($duration_show == 1 and $duration == '00:00') {
                    $conv       = VFileinfo::get_progress($file_key);
                    $conv_class = ' converting';
                    $thumbnail  = '<img class="mediaThumb" src="' . $def_thumb . '" alt="' . $title . '">';
                } else {
                    $conv       = null;
                    $conv_class = null;
                    $img_tmb    = is_file($cfg['media_files_dir'] . '/' . $usr_key . '/t/' . $file_key . '/0' . $thumb_cache . '.jpg');
                    $img_src    = ($type == 'blog' and !$img_tmb) ? $cfg['global_images_url'] . '/default-blog.png' : VBrowse::thumbnail(array($usr_key, $thumb_cache), $file_key, $thumb_server);
                    $thumbnail  = '<img class="mediaThumb" src="' . $def_thumb . '" ' . ($img_tmb ? 'data-src="' . $img_src . '"' : null) . ' alt="' . $title . '" onclick="window.location=\'' . $url . '\'">';
                }

                $items[] = '
                                <li class="vs-column sixths small-thumbs">
                                    <div class="thumbs-wrapper">
                                        <figure class="effect-smallT">
                                            ' . $thumbnail . '
                                            <div style="display:none;position:absolute;top:0px;width:100%;height:100%" class="vpv">
                                            ' . ($vpv ? '<video loop playsinline="true" muted="true" style="width:100%;height:100%" id="pv' . $file_key . rand(9999, 999999999) . '" rel-u="' . $usr_key . '" rel-s="' . $_rel_v . '" oncontextmenu="return false;" onclick="window.location=\'' . $url . '\'">
                                                <source src="' . self::$cfg['previews_url'] . '/default.mp4" type="video/mp4"></source>
                                                </video>
                                            ' : null) . '
                                            </div>
                                        </figure>
                                        <h3><a href="' . $url . '">' . $title . '</a></h3>
                                        <div class="caption">
                                            <div class="vs-column">
                                                <span class="views-number">' . $views . ' ' . ($views == 1 ? $language['frontend.global.view'] : $language['frontend.global.views']) . '</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                ';

                $shorts->MoveNext();
            }
        }

        $html = null;

        if (!empty($items)) {
            $html .= '   <div class="clearfix"></div>
                <section class="' . $main_section . ' ' . $default_section . '-' . $href_key . '">
                    <div class="container">
                    <article>
                        <h2 class="content-title">
                            ' . $_icon . '<span class="heading">' . $_heading . '</span>
                        </h2>
                        ' . $sub_html . '
                    </article>
                    <article>
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '" class="shortsContent ' . $default_section . ' ' . $type . '">
                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '-list">
                            <ul class="fileThumbs big clearfix view-list">';
            // for ($i == 1; $i <= 12; $i++) {
            $html .= implode('', $items);
            // }
            $html .= '              </ul>';
            $html .= '              </div>
                        </div>
                    </article>
                    </div>
                </section>';
            $html .= '<div class="clearfix"></div>';
            $html .= '<div class="line"></div>';
        }

        return $html;
    }
    /* get sub paging content */
    public static function subContent_OFFFFF()
    {
        $cfg      = self::$cfg;
        $language = self::$language;
        $page     = (int) $_GET['p'];

        $channels = self::getFeaturedChannels($page);

        $html = null;

        if ($channels->fields['usr_id']) {
            foreach ($channels as $channel) {
                $html .= self::featuredMedia('video', $channel);
            }
        }
        $html .= '<div class="load-more-sub" data-loader="pageLoader" rel-p="' . ($page + 1) . '"></div>';
        echo $html;
    }
    /* user notifications list */
    public static function userNotifications($show_hidden = false)
    {
        $html = null;
        $uid  = (int) $_SESSION['USER_ID'];

        $db           = self::$db;
        $cfg          = self::$cfg;
        $class_filter = self::$filter;
        $page         = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $ids          = array();
        $_q           = array();

        if ($uid > 0) {
            if ($cfg['user_subscriptions'] == 1) {
                $rs = $db->execute(sprintf("SELECT `db_id`, `usr_id`, `sub_type` FROM `db_subscribers` WHERE `sub_id`='%s';", $uid));

                if ($rs->fields['db_id']) {
                    while (!$rs->EOF) {
                        $id = $rs->fields['usr_id'];

                        if (!in_array($id, $ids)) {
                            $ids[] = (int) $id;
                            $_q[]  = sprintf("(A.`usr_id`='%s'%s)", $id, ($rs->fields['sub_type'] == 'files' ? " AND A.`act_type` LIKE 'upload%'" : null));
                        }

                        $rs->MoveNext();
                    }
                }
            }

            if ($cfg['user_follows'] == 1) {
                $rs = $db->execute(sprintf("SELECT `db_id`, `usr_id`, `sub_type` FROM `db_followers` WHERE `sub_id`='%s';", $uid));

                if ($rs->fields['db_id']) {
                    while (!$rs->EOF) {
                        $id = $rs->fields['usr_id'];

                        if (!in_array($id, $ids)) {
                            $ids[] = (int) $id;
                            $_q[]  = sprintf("(A.`usr_id`='%s'%s)", $id, ($rs->fields['sub_type'] == 'files' ? " AND A.`act_type` LIKE 'upload%'" : null));
                        }

                        $rs->MoveNext();
                    }
                }
            }

            if ($page == 1) {
                $html = '
                    <p class="notification-entries-heading">
                        ' . self::$language['frontend.global.notifications'] . '
                        <i class="icon-eye-blocked hidden-notifications' . ($show_hidden ? ' active' : null) . '" rel="tooltip" title="' . self::$language['frontend.global.notification.toggle'] . '"></i>
                    </p>
                ';
            }
            $html .= self::userActivity($_q, $show_hidden);

            if ($page == 1) {
                $html .= '
                        <div class="line toggle-off"></div>
                        <div id="more-results">
                            <center>
                                <a href="javascript:;" rel="nofollow">
                                    <span class="info-toggle notifications-more" rel-page="2">' . self::$language['frontend.global.load.more'] . '</span>
                                </a>
                            </center>
                        </div>
                    ';
            }
        }

        echo $html;
    }
    /* generate subscriber activity */
    private static function userActivity($_q, $show_hidden)
    {
        $html           = null;
        $cfg            = self::$cfg;
        $db             = self::$db;
        $class_filter   = self::$filter;
        $class_database = self::$dbc;
        $class_language = self::$class_language;
        $language       = self::$language;
        $usr_id         = (int) $_SESSION['USER_ID'];
        $page           = isset($_GET['p']) ? (int) $_GET['p'] : 1;
        $lim            = 10;
        $page_lim       = $page > 1 ? sprintf("%s, %s", ($lim * ($page - 1)), $lim) : $lim;
        $display        = 0;

        include_once $class_language->setLanguageFile('frontend', 'language.channel');

        $ex   = array();
        $fids = array();

        $q   = isset($_q[0]) ? implode(' OR ', $_q) . ' OR ' : null;
        $sql = sprintf("SELECT `act_id` FROM `db_notifications_hidden` WHERE `usr_id`='%s';", $usr_id);
        $res = $db->execute($sql);

        if ($res->fields['act_id']) {
            while (!$res->EOF) {
                $ex[] = $res->fields['act_id'];
                $res->MoveNext();
            }
        }

        $sql = sprintf("SELECT
                        A.`act_id` AS `db_id`, A.`usr_id`, A.`usr_id_to`, A.`act_type`, A.`act_time`,
                        B.`usr_key`, B.`usr_user`,
                        B.`usr_dname`, B.`ch_title`
                        FROM
                        `db_useractivity` A, `db_accountuser` B
                        WHERE
                        DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= A.`act_time` AND
                        %s
                        (%sA.`usr_id_to`='%s') AND
                        A.`usr_id`=B.`usr_id` AND
                        A.`act_visible`='1' AND
                        A.`act_deleted`='0'
                        GROUP BY A.`act_id`
                        ORDER BY A.`act_id` DESC LIMIT %s;", (($ex[0] != '' and !$show_hidden) ? "A.`act_id` NOT IN (" . implode(",", $ex) . ") AND" : null), $q, $usr_id, $page_lim);

        $rs = $db->execute($sql);

        if ($rs->fields['db_id']) {
            $db->execute(sprintf("UPDATE `db_notifications_count` SET `nr`=0 WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
            if ($db->Affected_Rows()) {
                $_SESSION['new_notifications'] = 0;
            }

            $html .= $page == 1 ? '<div id="notifications-box-scroll" class="scroll scroll-' . (strpos($cfg['theme_name'], 'dark') === 0 ? 'dark' : 'light') . '">' : null;
            $html .= $page == 1 ? '<div id="notifications-box-list">' : null;

            while (!$rs->EOF) {
                $act_id       = $rs->fields['db_id'];
                $user_id      = $rs->fields['usr_id'];
                $user_id_to   = $rs->fields['usr_id_to'];
                $user_key     = $rs->fields['usr_key'];
                $user_uname   = $rs->fields['usr_user'];
                $user_dname   = $rs->fields['usr_dname'];
                $user_chtitle = $rs->fields['ch_title'];
                $user_name    = $user_dname != '' ? $user_dname : ($user_chtitle != '' ? $user_chtitle : $user_uname);

                $act_type = $rs->fields['act_type'];
                $act_time = VUserinfo::timeRange($rs->fields['act_time']);
                $action   = explode(":", $act_type);
                $show     = true;
                $class    = null;

                switch ($action[0]) {
                    case "bulletin":
                        $class       = $action[0];
                        $action_text = '<p>';
                        $action_text .= $user_name . ' ' . $language["upage.act." . $action[0]];
                        $action_text .= '</p>';

                        $action_text .= '<p style="margin-top: 7px;">';
                        $action_text .= '<span class="black">' . $action[1] . '</span>';
                        $action_text .= '</p>';

                        $action_text .= '<p>';
                        $action_text .= '<form class="entry-form-class"><label>' . $act_time . '</label></form>';
                        $action_text .= '</p>';
                        break;

                    case "upload":
                    case "like":
                    case "dislike":
                    case "favorite":
                    case "response":
                        $action_text = null;
                        $title       = null;

                        if ($user_uname != $_SESSION['USER_NAME']) {
                            $class       = $action[0];
                            $i           = $db->execute(sprintf("SELECT A.`file_title`, A.`thumb_cache`, C.`usr_key` FROM `db_%sfiles` A, `db_accountuser` C WHERE A.`usr_id`=C.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $action[1], $action[2]));
                            $title       = $i->fields['file_title'];
                            $user_key    = $i->fields['usr_key'];
                            $thumb_cache = $i->fields['thumb_cache'];
                            $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
                            $url         = $cfg['main_url'] . '/' . VGenerate::fileHref($action[1][0], $action[2], $title);

                            if ($action[0] !== 'response') {
                                $action_thumb = VGenerate::thumbSigned($action[1], $action[2], array($user_key, $thumb_cache), (3600 * 24), 0, 1);
                            } else {
                                $u            = $db->execute(sprintf("SELECT A.`usr_key`, B.`usr_id`, C.`file_title`, C.`thumb_cache` FROM `db_accountuser` A, `db_%sresponses` B, `db_%sfiles` C WHERE B.`file_key`='%s' AND B.`file_response`='%s' AND A.`usr_id`=B.`usr_id` AND B.`file_response`=C.`file_key` LIMIT 1;", $action[1], $action[1], $action[2], $action[3]));
                                $title2       = $u->fields['file_title'];
                                $user_key2    = $u->fields['usr_key'];
                                $thumb_cache  = $u->fields['thumb_cache'];
                                $action_thumb = VGenerate::thumbSigned($action[1], $action[3], array($user_key2, $thumb_cache), (3600 * 24), 0, 1);
                                $url          = $cfg['main_url'] . '/' . VGenerate::fileHref($action[1][0], $action[3], $title2);
                                $url .= '&rs=' . md5(date("Y-m-d"));
                            }

                            if ($title) {
                                $action_text .= '<p>';
                                $action_text .= '<a href="' . VHref::channelURL(["username" => $user_uname]) . '">' . $user_name . '</a> ' . str_replace('##TYPE##', $language["frontend.global." . $action[1][0]], $language["upage.act." . $action[0]]) . ' ' . ($action[0] !== 'response' ? $language["frontend.global." . $action[1][0] . ($user_key == $_SESSION['USER_KEY'] ? ".y" : ".a")] : null);
                                $action_text .= '<br><a href="' . $url . '">' . $title . '</a>';
                                $action_text .= '</p>';

                                $action_text .= '<p>';
                                $action_text .= '<form class="entry-form-class"><label>' . $act_time . '</label></form>';
                                $action_text .= '</p>';
                            } else {
                                $show = false;
                            }
                        } else {
                            $show = false;
                        }

                        break;

                    default:
                        $_x = explode(" ", $action[0]);

                        switch ($_x[0]) {
                            case "comments":
                                $action_text = null;
                                $title       = null;
                                $class       = $_x[0];
                                if ($_x[2] == 'channel') {
                                    $i = $db->execute(sprintf("SELECT B.`c_usr_id`, B.`c_body`, B.`c_replyto`, C.`usr_id`, C.`usr_user`, C.`usr_key`, C.`usr_dname`, C.`ch_title` FROM `db_%scomments` B, `db_accountuser` C WHERE B.`c_key`='%s' AND B.`file_key`=C.`usr_id` LIMIT 1;", $_x[2], $action[2]));
                                    if ($i->fields['c_usr_id'] != $_SESSION['USER_ID']) {
                                        $title        = $i->fields['ch_title'] != '' ? $i->fields['ch_title'] : ($i->fields['usr_dname'] != '' ? $i->fields['usr_dname'] : $i->fields['usr_user']);
                                        $title        = $i->fields['usr_user'] == $_SESSION['USER_NAME'] ? $language['upage.act.your.ch'] : $title;
                                        $user_key     = $i->fields['usr_key'];
                                        $url          = VHref::channelURL(["username" => $i->fields['usr_user']]) . '/' . VHref::getKey("discussion");
                                        $action_thumb = VUserAccount::getProfileImage($i->fields['usr_id']);
                                    }
                                } else {
                                    $i = $db->execute(sprintf("SELECT A.`file_title`, A.`thumb_cache`, B.`c_usr_id`, B.`c_body`, B.`c_replyto`, C.`usr_key` FROM `db_%sfiles` A, `db_%scomments` B, `db_accountuser` C WHERE A.`file_key`=B.`file_key` AND A.`usr_id`=C.`usr_id` AND B.`c_key`='%s' LIMIT 1;", $_x[2], $_x[2], $action[2]));
                                    if ($i->fields['c_usr_id'] != $_SESSION['USER_ID']) {
                                        $title        = $i->fields['file_title'];
                                        $user_key     = $i->fields['usr_key'];
                                        $thumb_cache  = $i->fields['thumb_cache'];
                                        $thumb_cache  = $thumb_cache > 1 ? $thumb_cache : null;
                                        $url          = $cfg['main_url'] . '/' . VGenerate::fileHref($_x[2][0], $action[1], $title);
                                        $action_thumb = VGenerate::thumbSigned($_x[2], $action[1], array($user_key, $thumb_cache), (3600 * 24), 0, 1);
                                    }
                                }
                                $comment = $i->fields['c_body'];

                                if ($title) {
                                    $action_text .= '<p>';
                                    $action_text .= '<a href="' . VHref::channelURL(["username" => $user_uname]) . '">' . $user_name . '</a> ' . ($i->fields['c_replyto'] == '0' ? $language['upage.act.comment'] : $language['upage.act.reply']);
                                    $action_text .= ' <a href="' . $url . '">' . $title . '</a>';
                                    $action_text .= '<br><br><pre><span class="black">' . $comment . '</span></pre>';
                                    $action_text .= '</p>';

                                    $action_text .= '<p>';
                                    $action_text .= '<form class="entry-form-class"><label>' . $act_time . '</label></form>';
                                    $action_text .= '</p>';
                                } else {
                                    $show = false;
                                }

                                break;

                            case "subscribes":
                            case "follows":
                                $usr_id       = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', ($_x[0] == 'subscribes' ? $_x[2] : $_x[1]));
                                $action_text  = null;
                                $action_thumb = null;
                                $class        = $_x[0];

                                if ($usr_id > 0) {
                                    $_uinfo        = VUserinfo::getUserInfo($usr_id);
                                    $_user_uname   = $_uinfo['uname'];
                                    $_user_dname   = $_uinfo['dname'];
                                    $_user_chtitle = $_uinfo['ch_title'];
                                    $_user_key     = $_uinfo['key'];
                                    $_user_name    = $_user_dname != '' ? $_user_dname : ($_user_chtitle != '' ? $_user_chtitle : $_user_uname);

                                    $title = $_user_name;
                                    $url   = VHref::channelURL(["username" => $_user_uname]);

                                    $action_text .= '<p>';
                                    $action_text .= '<a href="' . VHref::channelURL(["username" => $user_uname]) . '">' . $user_name . '</a> ' . ($_x[0] == 'subscribes' ? $language['upage.act.subscribe'] : $language['upage.act.follow']);
                                    $action_text .= ($user_id_to == $_SESSION['USER_ID'] ? ' ' . $language['upage.act.your.ch'] : ' <a href="' . $url . '">' . $title . '</a>');
                                    $action_text .= '</p>';

                                    $action_text .= '<p>';
                                    $action_text .= '<form class="entry-form-class"><label>' . $act_time . '</label></form>';
                                    $action_text .= '</p>';
                                } else {
                                    $show = false;
                                }
                                break;

                            case "private":
                                $action_text  = null;
                                $action_thumb = null;
                                $title        = null;
                                $class        = $_x[0];
                                if ($user_id_to == $_SESSION['USER_ID']) {
                                    $pm      = $db->execute(sprintf("SELECT `msg_subj`, `msg_body` FROM `db_messaging` WHERE `msg_id`='%s' AND `msg_to`='%s' LIMIT 1;", (int) $action[1], $user_id_to));
                                    $title   = $pm->fields['msg_subj'];
                                    $comment = VUserinfo::truncateString($pm->fields['msg_body'], 30);
                                    $action_text .= '<p>';
                                    $action_text .= '<a href="' . VHref::channelURL(["username" => $user_uname]) . '">' . $user_name . '</a> ' . $language['upage.act.pmessage'];
                                    $action_text .= ': <a href="' . $cfg['main_url'] . '/' . VHref::getKey("messages") . '">' . $title . '</a>';
                                    $action_text .= '<br><br><pre><span class="black">' . $comment . '</span></pre>';
                                    $action_text .= '</p>';

                                    $action_text .= '<p>';
                                    $action_text .= '<form class="entry-form-class"><label>' . $act_time . '</label></form>';
                                    $action_text .= '</p>';
                                } else {
                                    $show = false;
                                }
                                break;

                            case "friend":
                                $action_text  = null;
                                $action_thumb = null;
                                $title        = null;
                                $class        = $_x[0];
                                if ($user_id_to == $_SESSION['USER_ID']) {
                                    $comment = str_replace('##PMSG##', '<a href="' . $cfg['main_url'] . '/' . VHref::getKey("messages") . '">' . $language['subnav.entry.contacts.messages'] . '</a>', $language['upage.act.check.pmg']);
                                    $action_text .= '<p>';
                                    $action_text .= '<a href="' . VHref::channelURL(["username" => $user_uname]) . '">' . $user_name . '</a> ' . $language['upage.act.frinvite'];
                                    $action_text .= '<br><br><pre><span class="black">' . $comment . '</span></pre>';
                                    $action_text .= '</p>';

                                    $action_text .= '<p>';
                                    $action_text .= '<form class="entry-form-class"><label>' . $act_time . '</label></form>';
                                    $action_text .= '</p>';
                                } else {
                                    $show = false;
                                }
                                break;
                        }
                        break;
                }
                if ($show) {
                    $html .= '
                    <div class="user-sub-activity' . (in_array($act_id, $ex) ? ' is-hidden' : null) . '" id="a' . $act_id . '">
                        <div class="place-left user-activity-side-left">
                            <div class="user-activity-entry">
                                <div class="user-thumb-xlarge top">
                                    <a href="' . VHref::channelURL(["username" => $user_uname]) . '">
                                        <img height="48" class="own-profile-image" title="' . $user_name . '" alt="' . $user_name . '" src="' . VUseraccount::getProfileImage($user_id) . '">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="user-activity-entry user-activity-text ' . $class . '">' . $action_text . '</div>
                        <i class="icon-' . (in_array($act_id, $ex) ? 'undo2' : 'times') . ' ' . (in_array($act_id, $ex) ? 'unhide' : 'hide') . '-entry" rel-nr="' . $act_id . '" rel="tooltip" title="' . (in_array($act_id, $ex) ? $language['frontend.global.notification.restore'] : $language['frontend.global.notification.hide']) . '"></i>
                        ' . ($action_thumb != '' ? '
                        <div class="user-activity-side-right">
                            <div class="user-activity-entry">
                                <a href="' . $url . '"><img src="' . $action_thumb . '" height="' . (((isset($_x[2]) and $_x[2] == 'short') or (isset($action[1]) and $action[1] == 'short')) ? 96 : 64) . '" /></a>
                            </div>
                        </div>
                        ' : null) . '
                        <div class="clearfix"></div>
                    </div>
                    ';
                    $display += 1;
                }

                $rs->MoveNext();
            }
            if ($display == 0) {
                $html .= '<div id="notifications-default">';
                $html .= '<i class="icon-bell"></i>';
                $html .= '<p class="n-1">' . $language['frontend.global.notif.text1'] . '</p>';
                $html .= '<p class="n-2">' . $language['frontend.global.notif.text2'] . '</p>';
                $html .= '</div>';
            }
            $html .= $page == 1 ? '</div>' : null;
            $html .= $page == 1 ? '</div>' : null;
        }

        return $html;
    }
    /* get new notifications number */
    public static function countNewNotifications()
    {
        global $cfg, $db, $class_database, $class_filter;

        $_q     = array();
        $ids    = array();
        $usr_id = (int) $_SESSION['USER_ID'];

        if ($usr_id == 0) {
            return;
        }

        if ($cfg['user_subscriptions'] == 1) {
            $rs = $db->execute(sprintf("SELECT `db_id`, `usr_id`, `sub_type` FROM `db_subscribers` WHERE `sub_id`='%s';", $usr_id));

            if ($rs->fields['db_id']) {
                while (!$rs->EOF) {
                    $id = $rs->fields['usr_id'];

                    if (!in_array($id, $ids)) {
                        $ids[] = (int) $id;
                        $_q[]  = sprintf("(A.`usr_id`='%s'%s)", $id, ($rs->fields['sub_type'] == 'files' ? " AND A.`act_type` LIKE 'upload%'" : null));
                    }

                    $rs->MoveNext();
                }
            }
        }

        if ($cfg['user_follows'] == 1) {
            $rs = $db->execute(sprintf("SELECT `db_id`, `usr_id`, `sub_type` FROM `db_followers` WHERE `sub_id`='%s';", $usr_id));

            if ($rs->fields['db_id']) {
                while (!$rs->EOF) {
                    $id = $rs->fields['usr_id'];

                    if (!in_array($id, $ids)) {
                        $ids[] = (int) $id;
                        $_q[]  = sprintf("(A.`usr_id`='%s'%s)", $id, ($rs->fields['sub_type'] == 'files' ? " AND A.`act_type` LIKE 'upload%'" : null));
                    }

                    $rs->MoveNext();
                }
            }
        }
        $last = 0;
        $tnr  = 0;
        $rs   = $db->execute(sprintf("SELECT `db_id`, `act_id`, `nr` FROM `db_notifications_count` WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
        if ($rs->fields['db_id']) {
            $lid  = $rs->fields['db_id'];
            $last = $rs->fields['act_id'];
            $tnr  = $rs->fields['nr'];
        }

        if ($tnr > 0) {
            return $tnr;
        }

        $sql = sprintf("SELECT
                        A.`act_id`, A.`usr_id`, A.`usr_id_to`,
                        B.`usr_key`
                        FROM
                        `db_useractivity` A, `db_accountuser` B
                        WHERE
                        A.`usr_id`!=A.`usr_id_to` AND
                        A.`act_id`>'%s' AND
                        (%sA.`usr_id_to`='%s') AND
                        A.`usr_id`=B.`usr_id` AND
                        A.`act_visible`='1' AND
                        A.`act_deleted`='0' AND DATE_SUB(CURDATE(),INTERVAL 30 DAY) <= A.`act_time`
                        ORDER BY A.`act_id` DESC;", $last, (isset($_q[0]) ? implode(' OR ', $_q) . ' OR ' : null), $usr_id);

        $rs = $db->execute($sql);
        $tt = $rs->fields['act_id'] ? $rs->RecordCount() : 0;
        $ai = $rs->fields['act_id'];

        if ($last == 0 and $tt == 0) {
            $ins = array("usr_id" => $usr_id, "nr" => $tt, "act_id" => $ai);
            $class_database->doInsert('db_notifications_count', $ins);

            return $tt;
        } elseif ($tt > 0) {
            $db->execute(sprintf("UPDATE `db_notifications_count` SET `nr`='%s', `act_id`='%s' WHERE `usr_id`='%s' LIMIT 1;", $tt, $ai, $usr_id));

            return $tt;
        }
    }
    /* hide notifications from list */
    public static function hideNotifications($unhide = false)
    {
        $usr_id = (int) $_SESSION['USER_ID'];

        if (!$unhide and $_POST and $usr_id > 0) {
            $act_id = (int) $_POST['i'];

            $ins = array("act_id" => $act_id, "usr_id" => $usr_id);

            $res = self::$dbc->doInsert("db_notifications_hidden", $ins);

            if ($res) {
                echo 1;
            } else {
                echo 0;
            }
        } elseif ($unhide and $_POST and $usr_id > 0) {
            $act_id = (int) $_POST['i'];

            $sql = sprintf("DELETE FROM `db_notifications_hidden` WHERE `act_id`='%s' AND `usr_id`='%s' LIMIT 1;", $act_id, $usr_id);

            $res = self::$db->execute($sql);

            if (self::$db->Affected_Rows() > 0) {
                echo 1;
            } else {
                echo 0;
            }
        }
    }
}
