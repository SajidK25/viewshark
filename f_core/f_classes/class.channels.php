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

class VChannels
{
    private static $type = 'channel';
    private static $cfg;
    private static $db;
    private static $db_cache;
    private static $dbc;
    private static $filter;
    private static $language;
    private static $href;
    private static $section;
    private static $page;
    private static $page_end;
    private static $viewMode1_limit                 = 30;
    private static $viewMode2_limit                 = 12;
    private static $search_viewMode1_limit          = 10;
    private static $search_viewMode2_limit          = 10;
    private static $promoted_viewMode1_limit        = 10;
    private static $promoted_viewMode2_limit        = 10;
    private static $search_promoted_viewMode1_limit = 10;
    private static $search_promoted_viewMode2_limit = 10;

    public function __construct($_type = false)
    {
        require 'f_core/config.href.php';

        global $cfg, $class_filter, $class_database, $db, $language, $section;

        self::$cfg      = $cfg;
        self::$db       = $db;
        self::$dbc      = $class_database;
        self::$filter   = $class_filter;
        self::$language = $language;
        self::$href     = $href;
        self::$section  = $section;
        self::$page     = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
        self::$page_end = false;

        self::$db_cache = false; //change here to enable caching
    }

    /* page layout */
    public static function doLayout()
    {
        $res_promoted  = self::$cfg["channel_promo"] == 1 ? self::getPromoted() : null;
        $res_channels  = self::getChannels();
        $res_watchlist = null;

        $html = (self::$cfg["channel_promo"] == 1 and $res_promoted->fields["usr_key"]) ? self::listPromoted($res_promoted, $res_watchlist) : null;
        $html .= (!isset($_GET["c"]) and !isset($_GET["tf"])) ? self::getFeaturedCategories() : null;
        $html .= self::listChannels($res_channels, $res_watchlist);

        return $html;
    }
    private static function getFeaturedCategories($type = 'channel')
    {
        $db       = self::$db;
        $language = self::$language;
        $cfg      = self::$cfg;
        $uid      = (int) $_SESSION["USER_ID"];
        $html     = null;

        switch ($type[0]) {
            case "l":$href_key = 'broadcasts';
                break;
            case "v":$href_key = 'videos';
            case "s":$href_key = 'shorts';
                break;
            case "i":$href_key = 'images';
                break;
            case "a":$href_key = 'audios';
                break;
            case "d":$href_key = 'documents';
                break;
            case "b":$href_key = 'blogs';
                break;
            case "c":$href_key = 'channels';
                break;
        }
        $sql = sprintf("SELECT `ct_id`, `ct_name`, `ct_slug`, `ct_icon` FROM `db_categories` WHERE `ct_type`='%s' AND `ct_featured`='1' AND `ct_active`='1';", $type);
        $rs  = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_channels'], $sql) : self::$db->execute($sql);

        $ids   = [];
        $names = [];
        $icons = [];
        $slugs = [];

        if ($rs->fields["ct_id"]) {
            while (!$rs->EOF) {
                $ct_id         = $rs->fields["ct_id"];
                $ids[]         = $ct_id;
                $names[$ct_id] = $rs->fields["ct_name"];
                $icons[$ct_id] = $rs->fields["ct_icon"];
                $slugs[$ct_id] = $rs->fields["ct_slug"];

                $rs->MoveNext();
            }
        }

        $sql = sprintf("SELECT
            D.`usr_id`, D.`usr_dname`, D.`ch_title`, D.`usr_photo`, D.`usr_profileinc`, D.`usr_partner`, D.`usr_affiliate`, D.`affiliate_badge`,
            D.`usr_id`, D.`usr_key`, D.`usr_user`, D.`usr_subcount`, D.`usr_followcount`, D.`ch_type`
            FROM
            ( SELECT DISTINCT `ct_id` FROM `db_categories` WHERE `ct_type`='%s' AND `ct_id` IN (%s) LIMIT 10 ) AS sub
            JOIN `db_accountuser` AS D on sub.`ct_id`=D.`ch_type`
            WHERE D.`usr_active`='1' AND
            D.`usr_status`='1' AND
            D.`usr_deleted`='0' AND
            D.`usr_verified`='1'
            ORDER BY D.`usr_id` DESC LIMIT 100", $type, implode(',', $ids));

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_featured_channels'], $sql) : self::$db->execute($sql);

        $html            = null;
        $ares            = 'categ';
        $default_section = 'featured';
        $main_section    = 'featured_section recommended_section';
        $results         = [];

        if ($res->fields["usr_id"]) {
            while (!$res->EOF) {
                foreach ($ids as $ct_id) {
                    if ($res->fields["ch_type"] == $ct_id) {
                        if (!is_int($index)) {
                            $results[$ct_id][] = [
                                "usr_user"        => $res->fields["usr_user"],
                                "usr_dname"       => $res->fields["usr_dname"],
                                "ch_title"        => $res->fields["ch_title"],
                                "usr_key"         => $res->fields["usr_key"],
                                "usr_id"          => $res->fields["usr_id"],
                                "usr_affiliate"   => $res->fields["usr_affiliate"],
                                "usr_partner"     => $res->fields["usr_partner"],
                                "affiliate_badge" => $res->fields["affiliate_badge"],
                                "usr_photo"       => $res->fields["usr_photo"],
                                "usr_profileinc"  => $res->fields["usr_profileinc"],
                                "usr_subcount"    => $res->fields["usr_subcount"],
                                "usr_followcount" => $res->fields["usr_followcount"],
                            ];
                        }

                        $results[$ct_id] = array_slice($results[$ct_id], 0, 10);
                    }
                }

                $res->MoveNext();
            }
        }

        $default_section = (isset($_SESSION["q"]) and $_SESSION["q"] != '') ? 'relevance' : 'featured';

        foreach ($results as $ct_id => $result) {
            $_heading = '<i class="' . $icons[$ct_id] . '"></i><a href="' . $cfg["main_url"] . '/' . $href_key . '/' . $slugs[$ct_id] . '">' . $names[$ct_id] . '</a>';

            $html .= '      <div class="clearfix"></div>
                            <section class="' . $main_section . ' ' . $default_section . '_' . $href_key . '">
                                <div class="container">
                                    <article>
                                        <h2 class="content-title"><span class="heading">' . $_heading . '</span></h2>
                                    </article>
                                    <article>
                                        <div id="main-view-mode-1-' . $default_section . '-' . $type . '" class="' . $default_section . ' channels">
                                                <div class="row mview" id="main-view-mode-1-' . $default_section . '-list">';

            if ($results[$ct_id]) {
                $html .= self::viewMode1($results[$ct_id], $user_watchlist, $type, true, 'featured-' . $slugs[$ct_id]);
            }

            $html .= '                          </div>
                                        </div>
                                    </article>
                                </div>
                            </section>';

            $html .= '<div class="clearfix"></div>';
        }

        return $html;
    }
    /* get database entries for promoted channels */
    private static function getPromoted($viewMode_id = null)
    {
        $type         = self::$type;
        $sort         = self::$filter->clr_str($_GET["sort"]);
        $categ_query1 = null;
        $categ_query2 = null;
        $categ_query3 = null;
        $ct_slug      = null;
        $q            = null;

        $sql_1        = null;
        $sql_2        = null;
        $search_order = false;

        if (isset($_SESSION["q"]) and $_SESSION["q"] != '') {
            $rel   = VGenerate::prepare($_SESSION["q"]);
            $sql_1 = ", MATCH(`ch_user`, `ch_dname`, `ch_title`, `ch_tags`) AGAINST ('" . $rel . "') AS `Relevance`";
            $sql_2 = "MATCH(`ch_user`, `ch_dname`, `ch_title`, `ch_tags`) AGAINST('" . $rel . "' IN BOOLEAN MODE) AND ";

            $search_order = true;

            $search_uf = (int) $_SESSION["uf"];

            switch ($search_uf) {
                case "1": //last hour
                    $q .= sprintf(" AND A.`usr_joindate` >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ");
                    break;
                case "2": //today
                    $q .= sprintf(" AND DATE(A.`usr_joindate`) = DATE(NOW()) ");
                    break;
                case "3": //this week
                    $q .= sprintf(" AND YEARWEEK(A.`usr_joindate`) = YEARWEEK(NOW()) ");
                    break;
                case "4": //this month
                    $q .= sprintf(" AND A.`usr_joindate` >= DATE_SUB(NOW(), INTERVAL 1 MONTH) ");
                    break;
                case "5": //this year
                    $q .= sprintf(" AND YEAR(A.`usr_joindate`) = YEAR(NOW()) ");
                    break;
            }
        }

        if (isset($_GET["c"])) {
            $ct_slug = self::$filter->clr_str($_GET["c"]);

            if ($ct_slug != 'all') {
                $cq           = self::$db->execute(sprintf("SELECT `ct_id` FROM `db_categories` WHERE `ct_type`='channel' AND `ct_slug`='%s' LIMIT 1;", $ct_slug));
                $ch_id        = $cq->fields["ct_id"];
                $categ_query1 = ', E.`ct_name`';
                $categ_query2 = ', `db_categories` E';
                $categ_query3 = "AND (A.`ch_type`='" . $ch_id . "' AND A.`ch_type`=E.`ct_id`)";
            }

        }

        switch ($viewMode_id) {
            default:
            case "1":
                $lim = (isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::$search_promoted_viewMode1_limit : self::$promoted_viewMode1_limit;
                $des = null;
                break;
            case "2":
                $lim = (isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::$search_promoted_viewMode2_limit : self::$promoted_viewMode2_limit;
                $des = ', A.`ch_descr`';
                break;
        }

        $sql = sprintf("SELECT
                    A.`usr_id`, A.`usr_key`, A.`usr_user`, A.`usr_partner`, A.`usr_affiliate`, A.`affiliate_badge`,
                    A.`usr_dname`, A.`usr_photo`, A.`usr_profileinc`, A.`usr_followcount`, A.`usr_subcount`,
                    A.`ch_views`, A.`ch_title` %s
                    %s
                    %s
                    FROM
                    `db_accountuser` A %s
                    WHERE
                    %s
                    A.`usr_promoted`='1' AND
                    A.`usr_status`='1'
                    %s %s ORDER BY RAND() LIMIT %s", $des, $categ_query1, $sql_1, $categ_query2, $sql_2, $categ_query3, $q, $lim);

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_channels_promoted'], $sql) : self::$db->execute($sql);

        return $res;
    }
    /* get database entries for channel entries */
    private static function getChannels($viewMode_id = null)
    {
        $type         = self::$type;
        $sort         = self::$filter->clr_str($_GET["sort"]);
        $categ_query1 = null;
        $categ_query2 = null;
        $categ_query3 = null;
        $ct_slug      = null;
        $q            = null;

        $sql_1        = null;
        $sql_2        = null;
        $search_order = false;

        if (isset($_SESSION["q"]) and $_SESSION["q"] != '') {
            $rel   = VGenerate::prepare($_SESSION["q"]);
            $sql_1 = ", MATCH(`ch_dname`, `ch_user`, `ch_title`, `ch_tags`) AGAINST ('" . $rel . "') AS `Relevance`";
            $sql_2 = "MATCH(`ch_dname`, `ch_user`, `ch_title`, `ch_tags`) AGAINST('" . $rel . "' IN BOOLEAN MODE) AND ";

            $search_order = true;
            $search_uf    = (int) $_SESSION["uf"];

            switch ($search_uf) {
                case "1": //last hour
                    $q .= sprintf(" AND A.`usr_joindate` >= DATE_SUB(NOW(), INTERVAL 1 HOUR) ");
                    break;
                case "2": //today
                    $q .= sprintf(" AND DATE(A.`usr_joindate`) = DATE(NOW()) ");
                    break;
                case "3": //this week
                    $q .= sprintf(" AND YEARWEEK(A.`usr_joindate`) = YEARWEEK(NOW()) ");
                    break;
                case "4": //this month
                    $q .= sprintf(" AND A.`usr_joindate` >= DATE_SUB(NOW(), INTERVAL 1 MONTH) ");
                    break;
                case "5": //this year
                    $q .= sprintf(" AND YEAR(A.`usr_joindate`) = YEAR(NOW()) ");
                    break;
            }
        }

        if (isset($_GET["c"])) {
            $ct_slug = self::$filter->clr_str($_GET["c"]);

            if ($ct_slug != 'today') {
                $cq           = self::$db->execute(sprintf("SELECT `ct_id` FROM `db_categories` WHERE `ct_type`='channel' AND `ct_slug`='%s' LIMIT 1;", $ct_slug));
                $ch_id        = $cq->fields["ct_id"];
                $categ_query1 = ', E.`ct_name`';
                $categ_query2 = ', `db_categories` E';
                $categ_query3 = "AND (A.`ch_type`='" . $ch_id . "' AND A.`ch_type`=E.`ct_id`)";
            }
        }

        $ct_all = $ct_slug === 'today' ? 1 : 0;

        if ($ct_all == 1) {
            $q .= sprintf(" AND A.`ch_lastview`='%s' ", date('Y-m-d'));
        }

        switch ($viewMode_id) {
            default:
            case "1":
                $lim = (isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::$search_viewMode1_limit : self::$viewMode1_limit;
                $des = null;
                break;
            case "2":
                $lim = (isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::$search_viewMode2_limit : self::$viewMode2_limit;
                $des = ', SUBSTRING(A.`ch_descr`, 1, 200) as `ch_descr`';
                break;
        }

        switch ($sort) {
            case "recent":$q .= "ORDER BY A.`usr_id` DESC";
                break;
            case "relevance":$q .= "ORDER BY `Relevance` DESC";
                break;
            case "featured":$q .= "AND A.`usr_featured`='1' ORDER BY A.`ch_views` DESC";
                break;
            case "promoted":$q .= "AND A.`usr_promoted`='1' ORDER BY A.`ch_views` DESC";
                break;
            case "active":$q .= "AND A.`usr_logins`>'0' ORDER BY A.`ch_views` DESC";
                break;
            case "views":$q .= "AND A.`ch_views` > 0 ORDER BY A.`ch_views` DESC";
                break;
            case "live":
            case "broadcasts":
            case "videos":
            case "shorts":
            case "images":
            case "audios":
            case "docs":
            case "blogs":
                $q .= "AND A.`usr_" . $sort[0] . "_count`>'0' ORDER BY A.`usr_" . $sort[0] . "_count` DESC";
                break;
            default:
                $q .= $search_order ? "ORDER BY `Relevance` DESC" : "ORDER BY A.`usr_id` DESC";
                break;
        }

        $page      = self::$page;
        $lim_start = $page > 1 ? (($page * $lim) - $lim) : 0;
        $lim_end   = $lim;

        $lim_sql = sprintf("%s, %s", $lim_start, $lim_end);

        $total_sql = sprintf("SELECT COUNT(*) AS `total`,
                    A.`usr_id`
                    %s
                    %s
                    FROM
                    `db_accountuser` A %s
                    WHERE
                    %s
                    A.`usr_status`='1' %s %s;", $categ_query1, $sql_1, $categ_query2, $sql_2, $categ_query3, $q);

        $total_res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_channels_main'], $total_sql) : self::$db->execute($total_sql);
        $total     = $total_res->fields["total"];

        $sql = sprintf("SELECT
                    A.`usr_id`, A.`usr_key`, A.`usr_user`, A.`usr_partner`, A.`usr_affiliate`, A.`affiliate_badge`,
                    A.`usr_dname`, A.`usr_photo`, A.`usr_profileinc`, A.`usr_followcount`, A.`usr_subcount`,
                    A.`ch_views`, A.`ch_title` %s
                    %s
                    %s
                    FROM
                    `db_accountuser` A %s
                    WHERE
                    %s
                    A.`usr_status`='1' %s %s LIMIT %s", $des, $categ_query1, $sql_1, $categ_query2, $sql_2, $categ_query3, $q, $lim_sql);

        $res = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_channels_main'], $sql) : self::$db->execute($sql);

        if ($lim_start + $lim >= $total) {
            self::$page_end = true;
        }

        return $res;
    }
    /* list promoted entries */
    private static function listPromoted($entries, $user_watchlist)
    {
        $category       = isset($entries->fields["ct_name"]) ? $entries->fields["ct_name"] : false;
        $title_category = $category ? ' <i class="iconBe-chevron-right"></i> ' . $category : null;
        $content        = $entries->fields["usr_id"] ? self::viewMode1($entries, $user_watchlist, false, true, 'promoted') : VGenerate::simpleDivWrap('no-content', '', self::$language["frontend.global.results.none"]);

        $html = '
                                    <div id="promo-content">
                                        <article>
                                            <h2 class="content-title"><i class="icon-bullhorn"></i>' . self::typeLangReplace(self::$language["frontend.global.promoted"]) . $title_category . '</h2>
                                            <section class="filter no-display">
                                                <div class="promo loadmask-img pull-left"></div>
                                                <div class="btn-group viewType vmtop pull-right">
                                                    <button type="button" id="promo-view-mode-1" class="viewType_btn viewType_btn-default promo-view-mode ' . self::$type . ' active" rel="tooltip" title="' . self::$language["files.menu.view1"] . '"><span class="icon-thumbs-with-details"></span></button>
                                                    <button type="button" id="promo-view-mode-2" class="viewType_btn viewType_btn-default promo-view-mode ' . self::$type . '" rel="tooltip" title="' . self::$language["files.menu.view3"] . '"><span class="icon-full-details"></span></button>
                                                </div>
                                            </section>
                                            <div class="line no-display"></div>
                                        </article>
                                        ' . VGenerate::advHTML(9) . '
                                        <div class="row pview" id="promo-view-mode-1-list">
                                            ' . $content . '
                                        </div>

                                        <div class="row pview" id="promo-view-mode-2-list" style="display: none">
                                        </div>

                                        ' . VGenerate::advHTML(10) . '
                                    </div>
                ';

        return $html;
    }
    /* list available channels */
    public static function listChannels($entries, $user_watchlist = null)
    {
        $category        = isset($entries->fields["ct_name"]) ? $entries->fields["ct_name"] : false;
        $title_category  = $category ? ' <i class="iconBe-chevron-right"></i> ' . $category : null;
        $content         = $entries->fields["usr_id"] ? self::viewMode_loader(1, $entries, $user_watchlist) : VGenerate::simpleDivWrap('no-content', '', self::$language["frontend.global.results.none"]);
        $default_section = (isset($_SESSION["q"]) and $_SESSION["q"] != '') ? 'relevance' : 'recent';

        $html = '           <div id="main-content" class="tabs tabs-style-topline">
                                        ' . self::tabs() . '

                                        <div class="content-wrap">
                                            <section id="section-' . $default_section . '">
                                                <article>
                                                    <h2 class="content-title"><i class="' . ((isset($_SESSION["q"]) and $_SESSION["q"] != '') ? 'icon-search' : 'icon-clock-o') . '"></i>' . self::typeLangReplace(((isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::$language["files.menu.relevant.type"] : self::$language["files.menu.recent.type"])) . $title_category . '</h2>
                                                    <section class="filter no-display">
                                                        <div class="main loadmask-img pull-left"></div>
                                                        <div class="btn-group viewType vmtop pull-right">
                                                            <button type="button" id="main-view-mode-1-' . $default_section . '" value="' . $default_section . '" class="viewType_btn viewType_btn-default main-view-mode active" rel="tooltip" title="' . self::$language["files.menu.view1"] . '"><span class="icon-thumbs-with-details"></span></button>
                                                            <button type="button" id="main-view-mode-2-' . $default_section . '" value="' . $default_section . '" class="viewType_btn viewType_btn-default main-view-mode" rel="tooltip" title="' . self::$language["files.menu.view3"] . '"><span class="icon-full-details"></span></button>
                                                        </div>
                                                    </section>
                                                    <div class="line no-display"></div>
                                                </article>
                                                ' . VGenerate::advHTML(11) . '
                                                <div class="row mview" id="main-view-mode-1-' . $default_section . '-list">
                                                    ' . $content . '
                                                </div>

                                                <div class="row mview" id="main-view-mode-2-' . $default_section . '-list" style="display: none">
                                                </div>

                                                ' . VGenerate::advHTML(12) . '
                                            </section>
                                            ' . ((isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::tabSection_loader('recent', $category) : null) . '
                                            ' . ((isset($_SESSION["q"]) and $_SESSION["q"] != '') ? self::tabSection_loader('promoted', $category) : null) . '
                                            ' . self::tabSection_loader('featured', $category) . '
                                            ' . self::tabSection_loader('active', $category) . '
                                            ' . self::tabSection_loader('views', $category) . '
                                            ' . (self::$cfg["video_module"] == 1 ? self::tabSection_loader('videos', $category) : null) . '
                                            ' . (self::$cfg["short_module"] == 1 ? self::tabSection_loader('shorts', $category) : null) . '
                                            ' . (self::$cfg["live_module"] == 1 ? self::tabSection_loader('live', $category) : null) . '
                                            ' . (self::$cfg["image_module"] == 1 ? self::tabSection_loader('images', $category) : null) . '
                                            ' . (self::$cfg["audio_module"] == 1 ? self::tabSection_loader('audios', $category) : null) . '
                                            ' . (self::$cfg["document_module"] == 1 ? self::tabSection_loader('docs', $category) : null) . '
                                            ' . (self::$cfg["blog_module"] == 1 ? self::tabSection_loader('blogs', $category) : null) . '

                                        </div><!-- /content-wrap -->
                                    </div><!-- /tabs -->';

        /* follow/unfollow action */
        if (self::$cfg["user_follows"] == 1) {
            $ht_js .= 'c_url = "' . self::$cfg["main_url"] . '/' . VHref::getKey('watch') . '?a";';
            $ht_js .= '$(document).on("click", ".follow-action", function(e){';
            if (isset($_SESSION["USER_ID"])) {
                $ht_js .= 'rel = $(this).attr("rel-usr");if (rel === "' . $_SESSION["USER_KEY"] . '")return;';
                $ht_js .= '$(".follow-txt-"+rel).text("' . self::$language["frontend.global.loading"] . '");';
                $ht_js .= '$.post(c_url+"&do=user-follow", $("#user-files-form-"+rel).serialize(), function(data){';
                $ht_js .= '$(".follow-txt-"+rel).text("' . self::$language["frontend.global.followed"] . '");';
                $ht_js .= '});';
            } else {
                $ht_js .= 'rel = $(this).attr("rel-name");$.fancybox.open({href:"#div-login",type:"inline",afterLoad:function(){$(".follow-username").text(rel);$(".tooltip").hide()},opts:{onComplete:function(){}},margin:0,minWidth:"50%",maxWidth:"95%",maxHeight:"90%"});';
            }
            $ht_js .= '});';
            if (isset($_SESSION["USER_ID"])) {
                $ht_js .= '$(document).on("click", ".unfollow-action", function(e){';
                $ht_js .= 'rel = $(this).attr("rel-usr");if (rel === "' . $_SESSION["USER_KEY"] . '")return;';
                $ht_js .= '$(".follow-txt-"+rel).text("' . self::$language["frontend.global.loading"] . '");';
                $ht_js .= '$.post(c_url+"&do=user-unfollow", $("#user-files-form-"+rel).serialize(), function(data){';
                $ht_js .= '$(".follow-txt-"+rel).text("' . self::$language["frontend.global.unfollowed"] . '");';
                $ht_js .= '});';
                $ht_js .= '});';
            }
        }

        $visitor_html = '<div class="tabs_signin">' . str_replace(array('##SIGNIN##', '##SIGNUP##'), array('<a href="' . self::$cfg["main_url"] . '/' . VHref::getKey("signin") . '?next=' . (VHref::getKey("channels")) . '" rel="nofollow">' . self::$language["frontend.global.signin"] . '</a>', '<a href="' . self::$cfg["main_url"] . '/' . VHref::getKey("signup") . '" rel="nofollow">' . self::$language["frontend.global.createaccount"] . '</a>'), self::$language["view.files.use.please"]) . '</div>';

        $html .= (!isset($_SESSION["USER_ID"]) ? '
                <div id="div-login" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-users"></i>' . self::$language["frontend.global.follow"] . ' <span class="follow-username">##USER##</span></h3>
                        <div class="line"></div>
                        <div>
                            ' . $visitor_html . '
                        </div>
                    </div>
                </div>
                ' : null);

        $html .= '
                                <script type="text/javascript">
                                        $(function(){' . $ht_js . '});
                                </script>
                        ';

        return $html;
    }
    /* viewmode loader */
    public static function viewMode_loader($viewMode_id, $entries = false, $user_watchlist = false)
    {
        $entries = (isset($_GET["p"]) and (int) $_GET["p"] == 1) ? self::getPromoted($viewMode_id) : self::getChannels($viewMode_id);
        $section = $entries ? (isset($_GET["sort"]) ? self::$filter->clr_str($_GET["sort"]) : (self::$section == self::$href['search'] ? 'relevance' : 'recent')) : false;

        if (!$section) {
            return;
        }

        if (!$user_watchlist) {
            $user_watchlist = null;
        }

        $method  = "viewMode" . $viewMode_id;
        $content = $entries->fields["usr_id"] ? self::$method($entries, $user_watchlist) : VGenerate::simpleDivWrap('no-content', '', self::$language["frontend.global.results.none"]);

        if (self::$page_end) {
            if (isset($_GET["m"])) {
                $js = 'if (typeof($) != "undefined") { setTimeout(function () { $("#main-view-mode-' . $viewMode_id . '-' . $section . '-more").detach(); }, 200); }';
                $content .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');
            }
        } else {
            $content .= ((isset($_GET["p"]) and (int) $_GET["p"] == 0 and !isset($_GET["page"])) or !isset($_GET["p"])) ? self::loadMore($viewMode_id, $section) : null;
        }

        return $content;
    }
    /* viewmode 1 */
    public static function viewMode1($entries, $user_watchlist, $type = false, $swiper = false, $for = 'promoted')
    {
        $db             = self::$db;
        $class_database = self::$dbc;
        $cfg            = self::$cfg;
        $language       = self::$language;

        $dbentries  = isset($entries->fields) ? $entries->fields : $entries;
        $session_id = self::getUserID();

        if (is_array($dbentries)) {
            $count   = 1;
            $html_li = $swiper ? '
                                <div class="swiper-ph swiper-ph-' . $for . '"></div>
                                <div class="swiper-top swiper-top-' . $for . '" style="display:' . VGenerate::ssd() . '">
                                    <div class="swiper-button-prev swiper-button-prev-' . $for . '"' . VGenerate::sso() . '></div>
                                    <div class="swiper-button-next swiper-button-next-' . $for . '"' . VGenerate::sso() . '></div>
                                    <div class="swiper swiper-' . $for . '" id="' . $for . '">
                                        <div class="swiper-wrapper">
                                ' : null;

            foreach ($entries as $k => $entry) {
                $dbentries = isset($entries->fields) ? $entries->fields : $entries[$k];
                $uid       = $dbentries["usr_id"];
                $usr_key   = $dbentries["usr_key"];
                $usr_photo = $dbentries["usr_photo"];
                $usr_inc   = $dbentries["usr_profileinc"];
                $bg_url    = VUseraccount::getProfileImage_inc($usr_key, $usr_photo, $usr_inc);
                $h3        = ($dbentries["usr_dname"] != '' ? $dbentries["usr_dname"] : ($dbentries["ch_title"] != '' ? $dbentries["ch_title"] : $dbentries["usr_user"]));

                if ($cfg["user_follows"] == 1) {
                    $sql = sprintf("SELECT `db_id` FROM `db_followers` WHERE `usr_id`='%s' AND `sub_id`='%s' LIMIT 1;", $uid, $session_id);
                    $rs  = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_sub_id'], $sql) : self::$db->execute($sql);

                    $is_follow = isset($rs->fields["db_id"]);
                    $a_cls     = $session_id > 0 ? ($is_follow ? 'unfollow-action' : 'follow-action') : 'follow-action';
                    $a_txt     = $is_follow ? $language["frontend.global.unfollow"] : ($uid == $session_id ? $language["frontend.global.followers"] : $language["frontend.global.follow"]);
                }

                if (isset($_SESSION["q"]) and $_SESSION["q"] != '' and (int) $_SESSION["tf"] == 6) {
                    $col_cls = ($count % 5 == 0) ? 'fifths fit' : 'fifths';
                } else {
                    $col_cls = ($count % 4 == 0) ? 'fourths fit' : 'fourths';
                    $col_cls = 'fifths';
                }

                if ($swiper) {
                    $col_cls .= ' swiper-slide';
                }

                $html_li .= '<div class="vs-column ' . $col_cls . '">';
                $html_li .= '<li>
                                    <a href="' . VHref::channelURL(["username" => $dbentries["usr_user"]]) . '"" class="channel-url">
                                        <div class="ch-item" style="background-image: url(' . $bg_url . ');">
                                            <div class="ch-info">
                                                <h3>' . $h3 . VAffiliate::affiliateBadge((($dbentries["usr_affiliate"] == 1 or $dbentries["usr_partner"] == 1) ? 1 : 0), $dbentries["affiliate_badge"]) . '
                                                </h3>
                                            </div>
                                        </div>
                                    </a>
                                    <div class="ch-user">
                                        <a href="' . VHref::channelURL(["username" => $dbentries["usr_user"]]) . '">' . VUserinfo::truncateString($h3, 20) . VAffiliate::affiliateBadge((($dbentries["usr_affiliate"] == 1 or $dbentries["usr_partner"] == 1) ? 1 : 0), $dbentries["affiliate_badge"]) . '</a>
                                        <span class="ch-user-stats">' . $dbentries["usr_followcount"] . ' ' . ($dbentries["usr_followcount"] == 1 ? $language["frontend.global.follower"] : $language["frontend.global.followers"]) . '</span>
                                        ' . ((self::getUserID() != $dbentries["usr_id"]) ? '<a class="user-follow-link ' . $a_cls . '" href="javascript:;" rel-usr="' . $usr_key . '" rel-name="' . $h3 . '" rel="nofollow"><span class="follow-txt follow-txt-' . $usr_key . '">' . $a_txt . '</span></a>' : null) . '
                                    </div>
                                    <form id="user-files-form-' . $usr_key . '" method="post" action="">
                                        <input type="hidden" name="uf_vuid" value="' . $dbentries["usr_id"] . '">
                                    </form>
                                </li>
                                ';
                $html_li .= '</div>';

                $count += 1;
            }
            $html_li .= $swiper ? '
                                        </div>
                                    </div>
                                </div>
                ' : null;
        }

        $html = !isset($_GET["page"]) ? '
                    <div class="channels_section">
                        <div class="container_off">
                            <ul class="ch-grid">' : null;

        $html .= $html_li;

        $html .= !isset($_GET["page"]) ? '  </ul>
                        </div>
                    </div>
                ' : null;

        return $html;
    }
    /* viewmode 2 */
    public static function viewMode2($res, $user_watchlist, $type = false)
    {
        return;
    }
    /* tab section loader */
    public static function tabSection_loader($tabSection, $category = false)
    {
        $title_category = $category ? ' <i class="iconBe-chevron-right"></i> ' . $category : null;

        switch ($tabSection) {
            case "recent":
                $title = self::typeLangReplace(self::$language["files.menu.recent.type"]);
                $icon  = 'clock-o';
                break;

            case "featured":
                $title = self::typeLangReplace(self::$language["files.menu.featured.type"]);
                $icon  = 'star';
                break;

            case "promoted":
                $title = self::typeLangReplace(self::$language["files.menu.promoted.type"]);
                $icon  = 'bullhorn';
                break;

            case "views":
                $title = self::typeLangReplace(self::$language["files.menu.viewed.type"]);
                $icon  = 'eye';
                break;

            case "relevance":
                $title = self::typeLangReplace(self::$language["files.menu.relavant.type"]);
                $icon  = 'search';
                break;

            case "active":
                $title = self::typeLangReplace(self::$language["files.menu.active.type"]);
                $icon  = 'busy';
                break;

            case "videos":
                $title = self::typeLangReplace(self::$language["files.menu.most.videos"]);
                $icon  = 'video';
                break;

            case "shorts":
                $title = self::typeLangReplace(self::$language["files.menu.most.shorts"]);
                $icon  = 'mobile';
                break;

            case "live":
            case "broadcasts":
                $title = self::typeLangReplace(self::$language["files.menu.most.live"]);
                $icon  = 'live';
                break;

            case "images":
                $title = self::typeLangReplace(self::$language["files.menu.most.images"]);
                $icon  = 'image';
                break;

            case "audios":
                $title = self::typeLangReplace(self::$language["files.menu.most.audios"]);
                $icon  = 'audio';
                break;

            case "docs":
                $title = self::typeLangReplace(self::$language["files.menu.most.doc"]);
                $icon  = 'file';
                break;

            case "blogs":
                $title = self::typeLangReplace(self::$language["files.menu.most.blogs"]);
                $icon  = 'pencil2';
                break;
        }

        $html = '                   <section id="section-' . $tabSection . '">
                                                <article>
                                                    <h2 class="content-title"><i class="icon-' . $icon . '"></i>' . $title . $title_category . '</h2>
                                                    <section class="filter no-display">
                                                        <div class="main loadmask-img pull-left"></div>
                                                        <div class="btn-group viewType vmtop pull-right">
                                                            <button type="button" id="main-view-mode-1-' . $tabSection . '" value="' . $tabSection . '" class="viewType_btn viewType_btn-default main-view-mode ' . self::$type . ' active" rel="tooltip" title="' . self::$language["files.menu.view1"] . '"><span class="icon-thumbs-with-details"></span></button>
                                                            <button type="button" id="main-view-mode-2-' . $tabSection . '" value="' . $tabSection . '" class="viewType_btn viewType_btn-default main-view-mode ' . self::$type . '" rel="tooltip" title="' . self::$language["files.menu.view3"] . '"><span class="icon-full-details"></span></button>
                                                        </div>
                                                    </section>
                                                    <div class="line no-display"></div>
                                                </article>

                                                <div class="row mview" id="main-view-mode-1-' . $tabSection . '-list">
                                                </div>

                                                <div class="row mview" id="main-view-mode-2-' . $tabSection . '-list" style="display: none">
                                                </div>

                                            </section>';

        return $html;
    }
    /* sorting tabs */
    private static function tabs()
    {
        $html = '   <nav>
                        <ul>
                            ' . ((isset($_SESSION["q"]) and $_SESSION["q"] != '') ? '<li><a href="#section-relevance" class="icon icon-search" rel="nofollow"><span>' . self::$language["files.menu.relevance"] . '</span></a></li>' : null) . '
                            ' . ('<li><a href="#section-recent" class="icon icon-clock-o" rel="nofollow"><span>' . self::$language["files.menu.recent"] . '</span></a></li>') . '
                        </ul>
                    </nav>';
        return $html;
    }
    /* category left side menu */
    public static function categoryMenu()
    {
        $type = self::$type;
        $get  = isset($_GET["c"]) ? self::$filter->clr_str($_GET["c"]) : 'all';
        $sql  = sprintf("SELECT `ct_id`, `ct_name`, `ct_slug`, `ct_icon` FROM `db_categories` WHERE `ct_type`='%s' AND `sub_id`='0' AND `ct_active`='1' AND `ct_menu`='1' ORDER BY `ct_name` ASC;", $type);
        $ct   = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_browse_categories_menu'], $sql) : self::$db->execute($sql);

        if ($ct) {
            $html = '
                    ' . VGenerate::advHTML(7) . '
                    <div class="blue categories-container">
                        <h4 class="nav-title categories-menu-title left-menu-h4"><i class="icon-users"></i>' . self::$language["frontend.global.bestof"] . '</h4>
                        <aside>
                            <nav>
                                <ul class="accordion" id="categories-accordion">
                                    <li><a href="' . self::baseUrl($type) . '/today" class="' . ($get === 'today' ? 'dcjq-parent selected active' : null) . '" rel-name="all"><i class="icon-clock-o"></i>' . self::$language["browse.file.picks"] . '</a></li>
                                    #LI_LOOP#
                                </ul>
                            </nav>
                        </aside>
                    </div>
                    ' . VGenerate::advHTML(8) . '
                ';

            while (!$ct->EOF) {
                $db_id   = $ct->fields["ct_id"];
                $ct_slug = $ct->fields["ct_slug"];
                $ct_icon = $ct->fields["ct_icon"];
                $ct_name = VUserinfo::truncateString($ct->fields["ct_name"], 30);
                $count   = (self::$cfg["file_counts"] == 1 ? ' (' . self::categoryCount($type, $db_id) . ')' : null);
                $class   = $get === $ct_slug ? 'dcjq-parent selected active' : null;

                $sub_menu           = null;
                $sub_active         = array();
                $sub_active[$db_id] = ($get === $ct_slug) ? 1 : 0;

                $sq = self::$db->execute(sprintf("SELECT `ct_name`, `ct_slug`, `ct_icon` FROM `db_categories` WHERE `ct_type`='%s' AND `sub_id`='%s' AND `sub_id`>'0';", $type, $db_id));

                if ($sq->fields["ct_slug"]) {
                    $sub_active = array();
                    $class .= ' sub-categ';
                    $sub_menu = '<ul class="inner-menu">';
                    while (!$sq->EOF) {
                        $sub_name           = $sq->fields["ct_name"];
                        $sub_slug           = $sq->fields["ct_slug"];
                        $sub_icon           = $sq->fields["ct_icon"];
                        $sub_active[$db_id] = ($get === $sub_slug) ? 2 : 0;
                        $sub_class          = $get === $sub_slug ? 'dcjq-parent-off selected active' : null;

                        $sub_menu .= '  <li><a href="' . self::baseUrl($type) . '/' . $sub_slug . '" class="' . $sub_class . '" rel-name="' . $sub_slug . '"><i class="' . $sub_icon . '"></i>' . $sub_name . '</a></li>';

                        $sq->MoveNext();
                    }
                    $sub_menu .= '</ul>';
                }
                $li_loop .= '    <li class="' . ($sub_active[$db_id] == 1 ? 'dcjq-parent-li' : ($sub_active[$db_id] == 2 ? 'dcjq-current-parent' : null)) . '"><a href="' . self::baseUrl($type) . '/' . $ct_slug . '" class="' . $class . '" rel-name="' . $ct_slug . '"><i class="' . $ct_icon . '"></i>' . $ct_name . $count . '</a>
                            ' . $sub_menu . '
                        </li>
                    ';

                @$ct->MoveNext();
            }
        }

        return str_replace('#LI_LOOP#', $li_loop, $html);
    }
    private static function loadMore($viewMode, $section)
    {
        return;
        $html = '                       <div class="btn-group load-more-group">
                            <button class="more-button" id="main-view-mode-' . $viewMode . '-' . $section . '-more" rel-page="2">
                                <span class="load-more loadmask-img">' . self::$language["frontend.global.loading"] . '</span>
                                <span class="load-more-text"><i class="iconBe-plus"></i></span>
                            </button>
                             <a href="" class="nextSelector" rel="nofollow"></a>
                                                </div>
                                                ';

        return $html;
    }
    /* build url from type */
    private static function baseUrl()
    {
        switch (self::$type[0]) {
            case "l":
                $key = 'broadcasts';
                break;
            case "c":
                $key = 'channels';
                break;
            case "v":
                $key = 'videos';
                break;
            case "s":
                $key = 'shorts';
                break;
            case "i":
                $key = 'images';
                break;
            case "a":
                $key = 'audios';
                break;
            case "d":
                $key = 'documents';
                break;
            case "b":
                $key = 'blogs';
                break;
        }

        return self::$cfg["main_url"] . '/' . self::$href[$key];
    }
    /* my user id */
    private static function getUserID()
    {
        return (int) $_SESSION["USER_ID"];
    }
    /* lang replace stuff */
    private static function typeLangReplace($src)
    {
        return str_replace('##TYPE##', self::$language["frontend.global.channels"], $src);
    }
    /* get promoted menu entries */
    private static function getSwiperSlides()
    {
        $cfg = self::$cfg;
        $db  = self::$db;

        $html_array = array();

        $sql = sprintf("SELECT
                    A.`usr_id`, A.`usr_key`, A.`usr_user`,
                    A.`usr_dname`,
                    A.`ch_title`, A.`ch_photos`
                    FROM
                    `db_accountuser` A
                    WHERE
                    A.`usr_status`='1' AND
                    A.`usr_promoted`='1'
                    ORDER BY RAND()
                    LIMIT 10;
                    ");

        $rs = self::$db_cache ? $db->CacheExecute($cfg['cache_browse_promoted'], $sql) : $db->execute($sql);

        if ($rs->fields["usr_id"]) {
            $t        = 1;
            $html_on  = null;
            $html_off = null;

            while (!$rs->EOF) {
                $usr_key   = $rs->fields["usr_key"];
                $usr_photo = $rs->fields["ch_photos"];
                $ch_photo  = $usr_photo != '' ? unserialize($usr_photo) : false;
                $ch_name   = $rs->fields["ch_title"] != '' ? $rs->fields["ch_title"] : ($rs->fields["usr_dname"] != '' ? $rs->fields["usr_dname"] : $rs->fields["usr_user"]);
                $tmb_file  = $cfg["profile_images_dir"] . '/' . $usr_key . '/' . $usr_key . '-' . $ch_photo["default"] . '-ch.jpg';

                if (file_exists($tmb_file)) {
                    if ($t < 6) {

                        $html_on .= '
                                    <div class="swiper-slide">
                                        <section class="channel_row">
                                            <a href="' . VHref::channelURL(["username" => $rs->fields["usr_user"]]) . '" rel="nofollow">
                                                <img class="channel_thumb" src="' . $cfg["profile_images_url"] . '/' . $usr_key . '/' . $usr_key . '-' . $ch_photo["default"] . '-ch.jpg">
                                                <div class="channel_title">' . $ch_name . '</div>
                                            </a>
                                        </section>
                                    </div>
                        ';
                    } else {
                        $html_off .= '

                                        <section class="channel_row">
                                            <a href="' . VHref::channelURL(["username" => $rs->fields["usr_user"]]) . '" rel="nofollow">
                                                <img class="channel_thumb" src="' . $cfg["profile_images_url"] . '/' . $usr_key . '/' . $usr_key . '-' . $ch_photo["default"] . '-ch.jpg">
                                                <div class="channel_title">' . $ch_name . '</div>
                                            </a>
                                        </section>
                        ';
                    }
                }

                $t += 1;
                $rs->MoveNext();
            }

            $html_array["on"]  = $html_on;
            $html_array["off"] = $html_off;
        }
        return $html_array;
    }
    /* left side promoted menu */
    public static function promotedMenu()
    {
        $cfg      = self::$cfg;
        $language = self::$language;

        $html_array = self::getSwiperSlides();

        $html = '
                <article id="channelLinks">
                    <h2 class="nav-title channels-title"><i class="icon-users"></i>' . $language["frontend.global.channels"] . '</h2>

                    <div class="swiper-holder">
                        <div class="preloader">' . $language["frontend.global.loading"] . '</div>
                        <div class="swiper-container">
                            <div class="swiper-wrapper">
                                ' . $html_array["on"] . '
                            </div>
                        </div>
                    </div><!-- .swiper-holder -->

                    <div class="sidebar-footer"></div>

                    <div class="no-display" id="other">
                        ' . $html_array["off"] . '
                    </div>
                </article>

            ';

        return $html;
    }
}
