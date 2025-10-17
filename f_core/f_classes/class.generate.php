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

class VGenerate
{
    private static $db_cache = false;

    /* prepare match query string */
    public static function prepare($str)
    {
        $rel = html_entity_decode($str, ENT_QUOTES, "UTF-8");
        $rel = html_entity_decode($rel, ENT_QUOTES, "UTF-8");
        $rel = preg_replace('/[^a-zA-Z0-9\s]+/', ' ', $rel);
        $rel = preg_replace('/\s+/', ' ', $rel);

        return trim(filter_var($rel, FILTER_SANITIZE_STRING));
    }

    /* get video, thumb url */
    public function fileURL($type, $key, $field)
    {
        global $db, $cfg;

        $is_mobile = strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') ? true : false;

        $sql = sprintf("SELECT
                                     A.`thumb_server`, A.`upload_server`,
                                     B.`server_type`, B.`url`, B.`lighttpd_url`, B.`s3_bucketname`, B.`cf_dist_type`, B.`cf_enabled`, B.`cf_dist_domain`,
                                     B.`cf_signed_url`, B.`cf_signed_expire`, B.`cf_key_pair`, B.`cf_key_file`
                                     FROM
                                     `db_%sfiles` A, `db_servers` B
                                     WHERE
                                     A.`file_key`='%s' AND
                                     A.`%s_server`=B.`server_id`
                                     LIMIT 1;", $type, $key, $field);

        $rs = self::$db_cache ? $db->CacheExecute($cfg['cache_file_url'], $sql) : $db->execute($sql);

        $server_type      = $rs->fields['server_type'];
        $url              = $rs->fields['url'];
        $lighttpd_url     = $rs->fields['lighttpd_url'];
        $s3_bucketname    = $rs->fields['s3_bucketname'];
        $cf_enabled       = $rs->fields['cf_enabled'];
        $cf_dist_domain   = $rs->fields['cf_dist_domain'];
        $cf_dist_type     = $rs->fields['cf_dist_type'];
        $cf_signed_url    = $rs->fields['cf_signed_url'];
        $cf_signed_expire = $rs->fields['cf_signed_expire'];
        $cf_key_pair      = $rs->fields['cf_key_pair'];
        $cf_key_file      = $rs->fields['cf_key_file'];

        switch ($server_type) {
            case "ftp":$base = ($url == '' ? $cfg['media_files_url'] : $url);
                break;
            case "s3":
            case "ws":
                if ($cf_enabled == 0 or ($cf_enabled == 1 and $cf_dist_type == 'r' and $is_mobile)) {
                    $pv   = $server_type == 'ws' ? '.s3.wasabisys.com' : '.s3.amazonaws.com';
                    $base = 'https://' . $s3_bucketname . $pv;
                } elseif ($cf_enabled == 1) {
                    $base = 'https://' . $cf_dist_domain;

                    if ($cf_dist_type == 'r') {
                        $base = 'rtmp://' . $cf_dist_domain;
                    }
                }
                break;
            default:$base = $cfg['media_files_url'];
                break;
        }

        return $base;
    }
    /* flowplayer signed url */
    public function fpSigned($type, $vid, $ukey)
    {
        global $db, $cfg;

        $sql = sprintf("SELECT
                              A.`server_type`, A.`cf_enabled`, A.`cf_dist_type`, A.`cf_dist_domain`, A.`cf_signed_url`, A.`cf_signed_expire`, A.`cf_key_pair`, A.`cf_key_file`,
                              B.`upload_server`
                              FROM
                              `db_servers` A, `db_%sfiles` B
                              WHERE
                              B.`file_key`='%s' AND
                              B.`upload_server` > '0' AND
                              A.`server_id`=B.`upload_server` LIMIT 1;", $type, $vid);

        $rs  = self::$db_cache ? $db->CacheExecute($cfg['cache_signed_thumbnails'], $sql) : $db->execute($sql);
        $srv = $db->execute($sql);

        $html = '';

        if ($srv->fields['server_type'] == 's3' and $srv->fields['cf_enabled'] == 1 and $srv->fields['cf_signed_url'] == 1) {
            $cf_signed_expire = $srv->fields['cf_signed_expire'];
            $cf_key_pair      = $srv->fields['cf_key_pair'];
            $cf_key_file      = $srv->fields['cf_key_file'];

            $sources = VPlayers::fileSources($type, $ukey, $vid);
            foreach ($sources as $b => $f) {
                foreach ($f as $loc) {
                    $path = $srv->fields['cf_dist_type'] == 'r' ? strstr($loc, $ukey) : $loc;
                    $html .= '<div class="row no-display fp-signed ' . $srv->fields['cf_dist_type'] . '" id="' . $type . '-' . $vid . '-' . $b . '">' . VbeServers::getSignedURL($path, $cf_signed_expire, $cf_key_pair, $cf_key_file) . '</div>';
                }
            }
        }
        return $html;
    }
    /* thumbnail url, mostly for email notifications */
    public static function thumbURL($type, $user_key, $file_key)
    {
        global $cfg;

        $enc = rawurlencode(secured_encrypt(json_encode(array($type, $user_key, $file_key))));
        $url = sprintf("%s/%s?_=%s", $cfg['main_url'], VHref::getKey("thumb"), $enc);

        return $url;
    }
    /* file and thumb signed url */
    public function thumbSigned($for, $file_key, $usr_key, $expires = 0, $custom_policy = 0, $nr = 0)
    {
        global $db, $cfg;

        $thumb_cache = null;
        $new_key     = $usr_key;
        if (is_array($usr_key)) {
            $thumb_cache = $usr_key[1];
            $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
            $usr_key     = $usr_key[0];
        }

        if (is_array($for)) {
            $type = $for['type'];
            $srv  = $for['server'];
            $file = $for['key'];
        } else {
            $type = $for;
            $srv  = 'thumb';
        }
        if (is_array($file_key)) {
            $file_key = $file_key[0];
        }

        $sql = sprintf("SELECT
                                                 A.`%s_server`,
                                                 B.`server_type`, B.`cf_enabled`, B.`cf_signed_url`, B.`cf_signed_expire`, B.`cf_key_pair`, B.`cf_key_file`
                                                 FROM
                                                 `db_%sfiles` A, `db_servers` B
                                                 WHERE
                                                 A.`file_key`='%s' AND
                                                 A.`%s_server`>'0' AND
                                                 A.`%s_server`=B.`server_id`
                                                 LIMIT 1", $srv, $type, $file_key, $srv, $srv);

        $cf = self::$db_cache ? $db->CacheExecute($cfg['cache_signed_thumbnails'], $sql) : $db->execute($sql);

        $server_type      = $cf->fields['server_type'];
        $cf_enabled       = $cf->fields['cf_enabled'];
        $cf_signed_url    = $cf->fields['cf_signed_url'];
        $cf_signed_expire = ($expires == 0 ? $cf->fields['cf_signed_expire'] : $expires);
        $cf_key_pair      = $cf->fields['cf_key_pair'];
        $cf_key_file      = $cf->fields['cf_key_file'];

        $file_url = $srv == 'thumb' ? VGenerate::fileURL($type, $file_key, 'thumb') . '/' . $usr_key . '/t/' . $file_key . '/' . $nr . $thumb_cache . '.jpg' : VGenerate::fileURL($type, $file_key, 'upload') . $file;

        if (($server_type == 's3' or $server_type == 'ws') and $cf_enabled == 1 and $cf_signed_url == 1) {
            $file_url = VbeServers::getSignedURL($file_url, $cf_signed_expire, $cf_key_pair, $cf_key_file, 0, $custom_policy);
        }
        return $file_url;
    }
    /* flowplayer bitrate select */
    public function fpBitrate($a = '')
    {
        global $cfg;

        $type = 'video';

        if ($a == '') {
            return '<span class="info info0"></span>';
        } else {
            $s0       = 0;
            $s1       = 0;
            $s2       = 0;
            $s3       = 0;
            $file_key = $a[0];
            $usr_key  = $a[1];

            $f = VPlayers::fileSources($type, $usr_key, $file_key);

            $url = VGenerate::fileURL($type, $file_key, 'upload');

            foreach ($f as $k => $v) {
                $loc0 = str_replace($url, $cfg['media_files_dir'], $v[0]);
                $loc1 = str_replace($url, $cfg['media_files_dir'], $v[1]);
                $loc2 = str_replace($url, $cfg['media_files_dir'], $v[2]);
                $loc3 = str_replace($url, $cfg['media_files_dir'], $v[3]);
                $loc4 = str_replace($url, $cfg['media_files_dir'], $v[4]);

                if ($k == '360p') {
                    if ($s0 == 0 and (file_exists($loc0) or file_exists($loc1) or file_exists($loc2) or file_exists($loc3) or file_exists($loc4))) {
                        $s0 = 1;
                    }
                } elseif ($k == '480p') {
                    if ($s1 == 0 and (file_exists($loc0) or file_exists($loc1) or file_exists($loc2) or file_exists($loc4))) {
                        $s1 = 1;
                    }
                } elseif ($k == '720p') {
                    if ($s2 == 0 and (file_exists($loc0) or file_exists($loc1) or file_exists($loc2) or file_exists($loc4))) {
                        $s2 = 1;
                    }
                } elseif ($k == '1080p') {
                    if ($s3 == 0 and (file_exists($loc0) or file_exists($loc1) or file_exists($loc2) or file_exists($loc4))) {
                        $s3 = 1;
                    }
                }
            }
            $html = '<div class="info info0">';
            $html .= ($s0 == 1 and ($s1 == 1 or $s2 == 1)) ? '<a href="javascript:;" class="fsrc-360p factive">360p</a>' : null;
            $html .= ($s1 == 1 and ($s0 == 1 or $s2 == 1)) ? '<a href="javascript:;" class="fsrc-480p">480p</a>' : null;
            $html .= ($s2 == 1 and ($s0 == 1 or $s1 == 1)) ? '<a href="javascript:;" class="fsrc-720p">720p</a>' : null;
            $html .= ($s3 == 1 and ($s0 == 1 or $s1 == 1)) ? '<a href="javascript:;" class="fsrc-1080p">1080p</a>' : null;
            $html .= '</div>';

            return $html;
        }
    }
    /* h2 span words */
    public function H2span($w, $footer = '')
    {

        $thx    = explode(' ', $w);
        $footer = $footer == 1 ? 'f' : null;

        return '<span class="h2-left' . $footer . '">' . $thx[0] . '</span><span class="h2-right' . $footer . '">' . $thx[1] . '</span>';
    }
    /* generate footer copyright text */
    public function footerText($ct = 1)
    {
        global $cfg, $language;
        include_once 'f_core/config.version.php';

        $html .= $language['frontend.copyright.text'] . ' ' . date("Y") . ' &copy; ' . $cfg['head_title'] . ' ' . $language['frontend.rights.text'] . '<br />';
        $html .= $ct == 1 ? $language['frontend.powered.text'] . ' <a href="' . $cfg['main_url'] . '">' . $version['name'] . $version['major'] . '.' . $version['minor'] . '</a><br />' : null;

        return $html;
    }
    /* generate footer links and pages, including language menu */
    public function footerInit()
    {
        global $class_filter, $cfg, $language;

        $_fp = footerPages();
        $_ct = count($_fp);
        $_t  = $class_filter->clr_str($_GET['t']);
        $_s  = 1;

        $ht .= '<div class="footer_menu">';
        foreach ($_fp as $k => $v) {
            $hr = $v['page_url'] == '' ? $cfg['main_url'] . '/' . VHref::getKey("page") . '?t=' . $k : $v['page_url'];
            $tg = $v['page_open'] == 1 ? ' target="_blank"' : null;

            $ht .= '<a href="' . $hr . '" rel="nofollow"' . $tg . ' class="' . ($_t == $k ? 'active' : null) . '">' . $v['link_name'] . '</a>' . ($_s < $_ct ? ' ' : null);

            $_s += 1;
        }
        $ht .= '</div>';

        echo $ht;
    }
    /* frontend language menu */
    public static function langInit()
    {
        global $cfg, $language;

        $area  = (VTemplate::backendSectionCheck()) ? 'be' : 'fe';
        $_lang = langTypes();
        $_ct   = count($_lang);

        $_ln_list = null;
        $_di      = null;

        foreach ($_lang as $lk => $lv) {
            if (!isset($_SESSION[$area . '_lang']) and $lv['lang_default'] == 1) {
                $_ln = $lv['lang_name'];
            } elseif (isset($_SESSION[$area . '_lang'])) {
                $_ln = $_lang[$_SESSION[$area . '_lang']]['lang_name'];
            }
            $_di = $_SESSION[$area . '_lang'] == $lk ? '<i class="icon-check"></i>' : null;

            $_ln_list .= '<a href="javascript:;" class="lang-entry" rel-lang="' . $lk . '" rel="no-follow"><span class="flag-icon ' . $lv['lang_flag'] . '"></span> ' . $lv['lang_name'] . $_di . '</a>';
        }

        $html = '<a class="dcjq-parent a-ln" href="javascript:;" rel="nofollow"><i class="icon-earth"></i> ' . $language['frontend.global.language'] . ': ' . $_ln . '<i class="iconBe-chevron-right place-right"></i></a>';
        $html .= '</li>';
        $html .= '<li id="l-ln" style="display:none">';
        $html .= '<div class="dm-wrap">';
        $html .= '<div class="dm-head dm-head-ln"><i class="icon-arrow-left2"></i> ' . $language['frontend.global.lang.select'] . '</div>';
        $html .= '<div class="ln-list">' . $_ln_list . '</div>';
        $html .= '</div>';
        $html .= '<div id="lang-update" style="display:none"></div>';
        $html .= '</li>';

        $js = '$(".lang-entry").click(function(){';
        $js .= '$("#siteContent").mask(" ");';
        $js .= '$.post("' . $cfg['main_url'] . '/' . VHref::getKey("language") . '?t="+$(this).attr("rel-lang")+"' . ($area == 'be' ? '&b=1' : null) . '", function(data){';
        $js .= '$("#lang-update").html(data);';
        $js .= '$("#siteContent").unmask();';
        $js .= '});';
        $js .= '});';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* backend language menu */
    public static function langInit_be()
    {
        global $cfg, $language;

        $_lang = langTypes();
        $_ct   = count($_lang);

        if ($_ct == 0 or $_ct == 1) {
            return false;
        }

        $html = '<li class="main likes_holder">
            <div class="head_but head_lang likes">
                <div class="items_count item_inactive">
                <i class="flag-icon ' . $_SESSION['be_flag'] . '"></i>
                </div>
            ';
        $html .= '<div class="">
            <div class="menu_drop">
                <div class="dl-menuwrapper" id="lang-menu-be">
                    <span class="dl-trigger actions-trigger"></span>
                    <ul class="dl-menu" style="display: none;">
        ';

        foreach ($_lang as $lk => $lv) {
            $html .= '
                        <li>
                            <a href="javascript:;" class="lang-entry" rel-lang="' . $lk . '" rel="no-follow"><span class="flag-icon ' . $lv['lang_flag'] . '"></span> <span class="lang-name">' . $lv['lang_name'] . '</span></a>
                        </li>
            ';
        }

        $html .= '
                    </ul>
                </div>
            </div>
        </div>
        </div>
        </li>
            <div id="lang-update"></div>

        ';

        $js = '$(".lang-entry").click(function(){';
        $js .= '$("#siteContent").mask(" ");';
        $js .= '$.post("' . $cfg['main_url'] . '/' . VHref::getKey("language") . '?b=1&t="+$(this).attr("rel-lang"), function(data){';
        $js .= '$("#lang-update").html(data);';
        $js .= '$("#siteContent").unmask();';
        $js .= '});';
        $js .= '});';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* generate page HTML, for footer links */
    public function pageHTML()
    {
        global $class_filter, $smarty, $cfg, $class_database;

        $cfg[]     = $class_database->getConfigurations('server_path_php');
        $_fp       = footerPages();
        $_t        = $class_filter->clr_str($_GET['t']);
        $file_path = $cfg['ww_templates_dir'] . '/tpl_page/' . $_fp[$_t]['page_name'];

        if (!is_file($file_path)) {
            return;
        }

        if (substr($_fp[$_t]['page_name'], -3) == 'php') {
        } else {
            $_body = $smarty->fetch($file_path);
        }

        return $_body;
    }
    /* generate advertising banners */
    public function advHTML($a)
    {
        global $db, $language, $class_filter;

        $type = isset($_GET['a']) ? 'audio' : (isset($_GET['s']) ? 'short' : 'video');
        $q    = null;

        if (is_array($a)) {
            $f  = 0;
            $id = $a[1];
            $c  = $db->execute(sprintf("SELECT `banner_ads` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $a[2]));

            if ($c->fields['banner_ads'] != '') {
//video banner ads
                $ca  = unserialize($c->fields['banner_ads']);
                $sql = sprintf("SELECT `adv_id` FROM `db_advbanners` WHERE `adv_id` IN (%s) AND `adv_active`='1' AND `adv_group`='%s' ORDER BY RAND() LIMIT 1;", implode(',', $ca), $id);
                $r   = $db->execute($sql);
                if ($f == 0 and $r->fields['adv_id']) {
                    $f = 1;
                    $q = sprintf("B.`adv_id`='%s' AND ", $r->fields['adv_id']);
                }
                if ($f == 0) {
                    $id = $a[0];
                }
            } else {
//category banner ads
                $c = $db->execute(sprintf("SELECT
                                A.`ct_banners`, B.`file_key`
                                FROM
                                `db_categories` A, `db_%sfiles` B
                                WHERE
                                A.`ct_id`=B.`file_category` AND
                                B.`file_key`='%s'
                                LIMIT 1;", $type, $a[2]));

                if ($c->fields['ct_banners'] != '') {
                    $ca  = unserialize($c->fields['ct_banners']);
                    $sql = sprintf("SELECT `adv_id`, `adv_group` FROM `db_advbanners` WHERE `adv_id` IN (%s) AND `adv_active`='1' AND `adv_group`='%s' ORDER BY RAND() LIMIT 1;", implode(',', $ca), ($id + 10));
                    $r   = $db->execute($sql);
                    if ($f == 0 and $r->fields['adv_id']) {
                        $f  = 1;
                        $id = $r->fields['adv_group'];
                        $q  = sprintf("B.`adv_id`='%s' AND ", $r->fields['adv_id']);
                    }
                }

                if ($f == 0) {
                    $id = $a[0];
                }
            }
        } else {
            $id = $a;
        }

        $html = null;
        $sql  = sprintf("SELECT
                            A.`adv_name`,
                            A.`adv_description`,
                            A.`adv_width`,
                            A.`adv_height`,
                            A.`adv_class`,
                            A.`adv_style`,
                            A.`adv_rotate`,
                            A.`adv_active`,
                            B.`adv_code`,
                            B.`adv_active`
                            FROM
                            `db_advgroups` A, `db_advbanners` B
                            WHERE
                            %s
                            A.`db_id`='%s' AND
                            B.`adv_group`='%s' AND
                            A.`adv_active`='1' AND
                            B.`adv_active`='1';", $q, $id, $id);
        $ad       = $db->execute($sql);
        $ad_res   = $ad->getrows();
        $ad_total = $ad->recordcount();

        if ($ad_res[0]['adv_active'] == 0) {
            return false;
        }

        $ad_rotate = $ad_res[0]['adv_rotate'];
        $key       = $ad_rotate == 1 ? rand(0, ($ad_total - 1)) : 0;

        $ad_name   = $ad_res[$key]['adv_name'];
        $ad_descr  = $ad_res[$key]['adv_description'];
        $ad_width  = $ad_res[$key]['adv_width'];
        $ad_height = $ad_res[$key]['adv_height'];
        $ad_class  = $ad_res[$key]['adv_class'];
        $ad_style  = $ad_res[$key]['adv_style'];
        $ad_code   = $ad_res[$key]['adv_code'];
        $ad_html   = $ad_code != '' ? $ad_code : $ad_descr . ' <br />' . $ad_total . ' ' . $language['frontend.global.banners.here'];

        $style = ($ad_width == 0 or $ad_height == 0) ? null : 'width: ' . ($ad_width == '100%' ? '100%' : $ad_width . 'px') . '; height: ' . ($ad_height == '100%' ? '100%' : $ad_height . 'px') . '; overflow: hidden;';
        $style .= $ad_style;

        $html .= $id == 43 ? '<div id="footer-top-ad">' : null;
        $html .= '<div class="row no-top-padding">';
        $html .= '<div class="' . $ad_class . '" style="border: 0px solid black;' . $style . '">' . $ad_html . '</div>';
        $html .= '</div>';

        return ($html != '' ? $html : $ad_name);
    }

    /* generate ad groups select list */
    public function adGroupsList($db_id, $selected = '')
    {
        global $db;

        switch ($_GET['s']) {
            case "backend-menu-entry7":
            case "backend-menu-entry9":$db_add_query = "`db_id` > '0'";
                break;

            case "backend-menu-entry7-sub1":
            case "backend-menu-entry9-sub1":$db_add_query = "`adv_name` LIKE 'home_promoted_%'";
                break;

            case "backend-menu-entry7-sub2":
            case "backend-menu-entry9-sub2":$db_add_query = "`adv_name` LIKE 'browse_chan_%'";
                break;

            case "backend-menu-entry7-sub3":
            case "backend-menu-entry9-sub3":$db_add_query = "`adv_name` LIKE 'browse_files_%'";
                break;

            case "backend-menu-entry7-sub4":
            case "backend-menu-entry9-sub4":$db_add_query = "`adv_name` LIKE 'view_files_%'";
                break;

            case "backend-menu-entry7-sub5":
            case "backend-menu-entry9-sub5":$db_add_query = "`adv_name` LIKE 'view_comm_%'";
                break;

            case "backend-menu-entry7-sub6":
            case "backend-menu-entry9-sub6":$db_add_query = "`adv_name` LIKE 'view_resp_%'";
                break;

            case "backend-menu-entry7-sub7":
            case "backend-menu-entry9-sub7":$db_add_query = "`adv_name` LIKE 'view_pl_%'";
                break;

            case "backend-menu-entry7-sub8":
            case "backend-menu-entry9-sub8":$db_add_query = "`adv_name` LIKE 'respond_%'";
                break;

            case "backend-menu-entry7-sub9":
            case "backend-menu-entry9-sub9":$db_add_query = "`adv_name` LIKE 'register_%'";
                break;

            case "backend-menu-entry7-sub10":
            case "backend-menu-entry9-sub10":$db_add_query = "`adv_name` LIKE 'login_%'";
                break;

            case "backend-menu-entry7-sub11":
            case "backend-menu-entry9-sub11":$db_add_query = "`adv_name` LIKE 'search_%'";
                break;

            case "backend-menu-entry7-sub12":
            case "backend-menu-entry9-sub12":$db_add_query = "`adv_name` LIKE 'footer_%'";
                break;

            case "backend-menu-entry7-sub13":
            case "backend-menu-entry9-sub13":$db_add_query = "`adv_name` LIKE 'browse_pl_%'";
                break;

            case "backend-menu-entry7-sub14":
            case "backend-menu-entry9-sub14":$db_add_query = "`adv_name` LIKE 'per_file_%'";
                break;

            case "backend-menu-entry7-sub15":
            case "backend-menu-entry9-sub15":$db_add_query = "`adv_name` LIKE 'per_category_%'";
                break;

            default:break;
        }
        $sql = sprintf("SELECT `db_id`, `adv_name` FROM `db_advgroups` WHERE %s AND `adv_active`='1';", $db_add_query);
        $res = $db->execute($sql);

        $html = '<i class="iconBe-chevron-down"></i><select name="adv_group_ids_' . $db_id . '" class="select-input wd350">';
        while (!$res->EOF) {
            $html .= '<option value="' . $res->fields['db_id'] . '"' . ($res->fields['db_id'] == $selected ? ' selected="selected"' : null) . '>' . $res->fields['adv_name'] . '</option>';

            @$res->MoveNext();
        }
        $html .= '</select>';

        return $html;
    }
    /* generate video/image/audio/doc select list */
    public function fileTypesList($section = 'fe', $selected = '', $entry_id = 0)
    {
        global $cfg, $language;

        $html = '<select name="this_file_type_' . $entry_id . '" class="select-input wd100">';
        $html .= (($section == 'fe' and $cfg['live_module'] == 1) or $section == 'be') ? '<option value="live"' . (($selected == 'live') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'video') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.l'] . '</option>' : null;
        $html .= (($section == 'fe' and $cfg['video_module'] == 1) or $section == 'be') ? '<option value="video"' . (($selected == 'video') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'video') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.v'] . '</option>' : null;
        $html .= (($section == 'fe' and $cfg['short_module'] == 1) or $section == 'be') ? '<option value="short"' . (($selected == 'short') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'short') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.s'] . '</option>' : null;
        $html .= (($section == 'fe' and $cfg['image_module'] == 1) or $section == 'be') ? '<option value="image"' . (($selected == 'image') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'image') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.i'] . '</option>' : null;
        $html .= (($section == 'fe' and $cfg['audio_module'] == 1) or $section == 'be') ? '<option value="audio"' . (($selected == 'audio') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'audio') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.a'] . '</option>' : null;
        $html .= (($section == 'fe' and $cfg['blog_module'] == 1) or $section == 'be') ? '<option value="blog"' . (($selected == 'blog') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'audio') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.b'] . '</option>' : null;
        $html .= (($section == 'fe' and $cfg['document_module'] == 1) or $section == 'be') ? '<option value="doc"' . (($selected == 'doc') ? ' selected="selected"' : (($_GET['do'] == 'add' and $_POST['this_file_type'] == 'doc') ? ' selected="selected"' : null)) . '>' . $language['frontend.global.d'] . '</option>' : null;
        $html .= '</select>';

        return $html;
    }
    /* generate social bookmarks */
    public function socialBookmarks($s = '')
    {
        global $section, $href, $cfg;

        $section = $s != '' ? $s : $section;

        switch ($section) {
            case "m":
            case $href['watch']:
                $html = '<div id="share"></div>';
                break;
            default:
                $html = '<div id="share"></div>';
                break;
        }

        return $html;
    }
    /* generate file href html */
    public function fileHref($type, $key, $title = '')
    {
        require 'f_core/config.href.php';
        global $class_database;

        switch ($type) {
            case "l":$tbl = 'live';
                break;
            case "v":$tbl = 'video';
                break;
            case "s":$tbl = 'short';
                break;
            case "i":$tbl = 'image';
                break;
            case "a":$tbl = 'audio';
                break;
            case "d":$tbl = 'doc';
                break;
            case "b":$tbl = 'blog';
                break;
        }
        if ($type == 's') {
            return sprintf("%s/%s", VHref::getKey('shorts'), $key);
        }

        $_title = $title == '' ? $class_database->singleFieldValue('db_' . $tbl . 'files', 'file_title', 'file_key', $key) : $title;

        return ($cfg['file_seo_url'] == 1 ? sprintf("%s/%s/%s", $type, $key, VForm::clearTag($_title, 1)) : sprintf("%s?%s=%s", VHref::getKey("watch"), $type, $key));
    }
    /* generate/various inputs */
    public function basicInput($type = '', $input_name = '', $input_class = '', $input_value = '', $input_id = '', $btn_label = '', $btn_rel = '', $placeholder = '')
    {
        global $language;
        $_id = $input_id != '' ? 'id="' . $input_id . '"' : null;

        switch ($type) {
            case "text":
            case "text-perm":
                $read_only = ($type == 'text-perm') ? ' readonly="readonly"' : null;
                $input     = '<input' . $read_only . ' type="' . ($type == 'text-perm' ? 'hidden' : 'text') . '" name="' . $input_name . '" class="' . $input_class . '" value="' . $input_value . '" ' . $_id . ' /> <p style="float:right;font-size:11px;margin-bottom:0px;margin-top:5px;">' . $perm_text . '</p>';
                $input .= '<div id="slider-' . $input_name . '"></div>';
                break;
            case "file":
                $input = '<input type="file" name="' . $input_name . '" class="' . $input_class . '" ' . $_id . ' />';
                break;
            case "password":
                $input = '<input type="password" name="' . $input_name . '" class="' . $input_class . '" value="' . $input_value . '" ' . $_id . ' placeholder="' . $placeholder . '" />';
                break;
            case "textarea-on":
            case "textarea-off":
            case "textarea":
                $disabled = ($type == 'textarea-off') ? 'disabled="disabled"' : null;
                $input    = '<textarea ' . $disabled . ' name="' . $input_name . '" class="' . $input_class . '" ' . $_id . '>' . $input_value . '</textarea>' . $extra_tip;
                break;
            case "button":
                $input = '<button' . ($btn_rel != '' ? ' rel="' . $btn_rel . '"' : null) . ' name="' . $input_name . '" id="btn-' . $input_id . '-' . $input_name . '" class="' . $input_class . $btn_class . '" type="button" value="' . ($input_value != '' ? $input_value : 1) . '" onfocus="blur();">' . $btn_label . '</button>';
                break;
        }
        return $input;
    }
    /* generate on/off switches */
    public function entrySwitch($entry_id, $entry_title, $sel_on, $sel_off, $sw_on, $sw_off, $input_name, $check_on, $check_off, $col_type = 'eights')
    {
        global $language, $cfg;

        $input_code = '';

        switch ($input_name) {
            case "backend_menu_entry3_sub1_smtp_auth":
                $c_on  = 'true';
                $c_off = 'false';
                break;
            case "backend_menu_entry1_sub7_file_opt_del":
                $radio_check1 = $cfg['file_delete_method'] == 1 ? ' checked="checked"' : null;
                $radio_check2 = $cfg['file_delete_method'] == 2 ? ' checked="checked"' : null;
                $radio_check3 = $cfg['file_delete_method'] == 3 ? ' checked="checked"' : null;
                $radio_check4 = $cfg['file_delete_method'] == 4 ? ' checked="checked"' : null;

                $input_code .= '<div class="icheck-box"><input type="radio" ' . $radio_check1 . ' name="' . $input_name . '_method" value="1"><label>' . $language['backend.menu.entry1.sub7.file.opt.del.t1'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="radio" ' . $radio_check2 . ' name="' . $input_name . '_method" value="2"><label>' . $language['backend.menu.entry1.sub7.file.opt.del.t2'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="radio" ' . $radio_check3 . ' name="' . $input_name . '_method" value="3"><label>' . $language['backend.menu.entry1.sub7.file.opt.del.t3'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="radio" ' . $radio_check4 . ' name="' . $input_name . '_method" value="4"><label>' . $language['backend.menu.entry1.sub7.file.opt.del.t4'] . '</label></div>';

                $c_on  = 1;
                $c_off = 0;
                break;
            case "backend_menu_entry1_sub7_file_opt_down":
                $dl_1   = $cfg['file_download_s1'] == 1 ? 'checked="checked"' : null;
                $dl_2   = $cfg['file_download_s2'] == 1 ? 'checked="checked"' : null;
                $dl_3   = $cfg['file_download_s3'] == 1 ? 'checked="checked"' : null;
                $dl_4   = $cfg['file_download_s4'] == 1 ? 'checked="checked"' : null;
                $dl_reg = $cfg['file_download_reg'] == 1 ? 'checked="checked"' : null;

                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $dl_reg . ' name="dl_reg" value="1"><label>' . $language['backend.menu.entry1.sub7.file.opt.down.reg'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $dl_1 . ' name="dl_1" value="1"><label>' . $language['backend.menu.entry1.sub7.file.opt.down.s1'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $dl_2 . ' name="dl_2" value="1"><label>' . $language['backend.menu.entry1.sub7.file.opt.down.s2'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $dl_3 . ' name="dl_3" value="1"><label>' . $language['backend.menu.entry1.sub7.file.opt.down.s3'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $dl_4 . ' name="dl_4" value="1"><label>' . $language['backend.menu.entry1.sub7.file.opt.down.s4'] . '</label></div>';

                $c_on  = 1;
                $c_off = 0;
                break;
            case "backend_menu_entry1_sub7_file_video":
                $c_on       = 1;
                $c_off      = 0;
                $input_size = '<label>' . $language['backend.menu.members.entry2.sub1.max'] . $language['frontend.sizeformat.mb'] . '</label>' . VGenerate::basicInput('text', $input_name . '_size', 'backend-text-input wd70', $cfg['video_limit'], $entry_id . '-input2');
                $input_code .= '<label>' . $language['backend.menu.members.entry2.sub1.allowed'] . '</label>' . VGenerate::basicInput('text', $input_name . '_types', 'backend-text-input wd350', $cfg['video_file_types']) . $input_size;
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VbeSettings::settings_delOriginalFiles('conversion_source_video', 'video');
                break;
            case "backend_menu_entry1_sub7_file_short":
                $c_on       = 1;
                $c_off      = 0;
                $input_size = '<label>' . $language['backend.menu.members.entry2.sub1.max'] . $language['frontend.sizeformat.mb'] . '</label>' . VGenerate::basicInput('text', $input_name . '_size', 'backend-text-input wd70', $cfg['short_limit'], $entry_id . '-input2');
                $input_code .= '<label>' . $language['backend.menu.members.entry2.sub1.allowed'] . '</label>' . VGenerate::basicInput('text', $input_name . '_types', 'backend-text-input wd350', $cfg['short_file_types']) . $input_size;
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VbeSettings::settings_delOriginalFiles('conversion_source_short', 'short');
                break;
            case "backend_menu_entry1_sub7_file_image":
                $c_on       = 1;
                $c_off      = 0;
                $input_size = '<label>' . $language['backend.menu.members.entry2.sub1.max'] . $language['frontend.sizeformat.mb'] . '</label>' . VGenerate::basicInput('text', $input_name . '_size', 'backend-text-input wd70', $cfg['image_limit'], $entry_id . '-input2');
                $input_code .= '<label>' . $language['backend.menu.members.entry2.sub1.allowed'] . '</label>' . VGenerate::basicInput('text', $input_name . '_types', 'backend-text-input wd350', $cfg['image_file_types']) . $input_size;
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VbeSettings::settings_delOriginalFiles('conversion_source_image', 'image');
                break;
            case "backend_menu_entry1_sub7_file_audio":
                $c_on       = 1;
                $c_off      = 0;
                $input_size = '<label>' . $language['backend.menu.members.entry2.sub1.max'] . $language['frontend.sizeformat.mb'] . '</label>' . VGenerate::basicInput('text', $input_name . '_size', 'backend-text-input wd70', $cfg['audio_limit'], $entry_id . '-input2');
                $input_code .= '<label>' . $language['backend.menu.members.entry2.sub1.allowed'] . '</label>' . VGenerate::basicInput('text', $input_name . '_types', 'backend-text-input wd350', $cfg['audio_file_types']) . $input_size;
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VbeSettings::settings_delOriginalFiles('conversion_source_audio', 'audio');
                break;
            case "backend_menu_entry1_sub7_file_doc":
                $c_on       = 1;
                $c_off      = 0;
                $input_size = '<label>' . $language['backend.menu.members.entry2.sub1.max'] . $language['frontend.sizeformat.mb'] . '</label>' . VGenerate::basicInput('text', $input_name . '_size', 'backend-text-input wd70', $cfg['document_limit'], $entry_id . '-input2');
                $input_code .= '<label>' . $language['backend.menu.members.entry2.sub1.allowed'] . '</label>' . VGenerate::basicInput('text', $input_name . '_types', 'backend-text-input wd350', $cfg['document_file_types']) . $input_size;
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VbeSettings::settings_delOriginalFiles('conversion_source_doc', 'doc');
                break;
            case "backend_menu_entry2_sub4_activity":
                $c_on      = 1;
                $c_off     = 0;
                $opt_array = array(
                    "log_upload"      => $language['backend.menu.entry6.sub6.log.upload'],
                    "log_filecomment" => $language['backend.menu.entry6.sub6.log.comment'],
                    "log_rating"      => $language['backend.menu.entry6.sub6.log.rate'],
                    "log_fav"         => $language['backend.menu.entry6.sub6.log.favorite'],
                    "log_responding"  => $language['backend.menu.entry6.sub6.log.respond'],
                    "log_subscribing" => $language['backend.menu.entry6.sub6.log.subscribe'],
                    "log_following"   => $language['backend.menu.entry6.sub6.log.follow'],
                    "log_pmessage"    => $language['backend.menu.entry6.sub6.log.pm'],
                    "log_frinvite"    => $language['backend.menu.entry6.sub6.log.invite'],
                    "log_delete"      => $language['backend.menu.entry6.sub6.log.delete'],
                    "log_signin"      => $language['backend.menu.entry6.sub6.log.signin'],
                    "log_signout"     => $language['backend.menu.entry6.sub6.log.signout'],
                    "log_precovery"   => $language['backend.menu.entry6.sub6.log.pr'],
                    "log_urecovery"   => $language['backend.menu.entry6.sub6.log.ur'],
                );

                $input_code .= VGenerate::simpleDivWrap('row left-float', '', '');
                foreach ($opt_array as $key => $val) {
                    $cfg_check = $cfg[$key] == 1 ? ' checked="checked"' : null;
                    $input_code .= '<div class="icheck-box"><input class="activity_logging_cb" type="checkbox"' . $cfg_check . ' name="' . $key . '" value="1" /><label>' . $language['backend.menu.entry6.sub6.log.comp'] . $val . '</label></div>';
                }
                break;

            default:$c_on = 1;
                $c_off        = 0;
        }
        $input_code = '
                    <div class="">
                            <div class="switch_holder">
                                <label class="switch switch-light" onclick="if($(this).find(\'.switch-input\').is(\':checked\')){$(\'#' . $entry_id . '-input1\').click();}else{$(\'#' . $entry_id . '-input2\').click();}">
                                    <input type="checkbox" class="switch-input" name="' . $input_name . '_check"' . ($check_on != '' ? ' checked="checked"' : null) . '>
                                        <span class="switch-label" data-on="' . $sw_on . '" data-off="' . $sw_off . '"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                <div style="display: none;">
                                    <input type="radio" id="' . $entry_id . '-input1" name="' . $input_name . '" value="' . $c_on . '" ' . $check_on . '>
                                    <input type="radio" id="' . $entry_id . '-input2" name="' . $input_name . '" value="' . $c_off . '" ' . $check_off . '>
                                </div>
                            </div>
                            ' . ($input_code != '' ? '<div class="settings_content">' . $input_code . '</div>' : null) . '
                    </div>
        ';

        return $input_code;
    }
    /* backend menu entries */
    public function menuEntries()
    {
        global $cfg;

        return ($cfg['backend_menu_toggle'] == 1 ? 'block' : 'none');
    }
    /* backend menu toggle */
    public function menuToggle()
    {
        global $cfg;

        $c1 = $cfg['backend_menu_toggle'] == 1 ? 'tree tree_expand no-display' : 'tree tree_expand';
        $c2 = $cfg['backend_menu_toggle'] == 1 ? 'tree tree_collapse' : 'tree tree_collapse no-display';

        return VGenerate::simpleDivWrap($c1) . VGenerate::simpleDivWrap($c2);
    }
    /* build select list options */
    public function selectListOptions($arr, $for)
    {
        global $language;
        if ($for == 'usr_showage') {
            $showage = VUseraccount::getProfileDetail($for);
            $ck      = $showage == 1 ? $language['account.profile.age.array'][0] : $language['account.profile.age.array'][1];
        }
        foreach ($arr as $val) {
            $sel_opts .= '<option' . (($for == 'usr_showage' and $val == $ck) ? ' selected="selected"' : null) . ' value="' . ($for == 'usr_gender' ? $val[0] : (($for == 'usr_showage' and $val == $arr[0]) ? 1 : (($for == 'usr_showage' and $val == $arr[1]) ? 0 : $val))) . '" ' . self::selOptionCheck(VUseraccount::getProfileDetail($for), ($for == 'usr_gender' ? $val[0] : $val)) . '>' . (($for == 'usr_showage' and $val == $arr[0]) ? $val : (($for == 'usr_showage' and $val == $arr[0]) ? $val : $val)) . '</option>';
        }
        return $sel_opts;
    }
    /* select options checked verification */
    public function selOptionCheck($check, $val)
    {
        return $sel = $check == $val ? ' selected="selected"' : null;
    }
    /* simple label and input display */
    public function sigleInputEntry($type, $label_class, $label_text, $div_class, $input_name, $input_class, $input_value)
    {
        switch ($input_name) {
            case "server_path_lame":
            case "server_path_ffmpeg":
            case "server_path_ffprobe":
            case "server_path_yamdi":
            case "server_path_qt":
            case "server_path_unoconv":
            case "server_path_convert":
            case "server_path_pdf2swf":
            case "server_path_php":
            case "server_path_mysqldump":
            case "server_path_tar":
            case "server_path_gzip":
            case "server_path_zip":
                $ht = '<p style="float: right; font-size: 11px;">' . VbeSettings::checkPath($input_name) . '<p>';
                break;
            case "account_manage_pass_new":
            case "account_manage_pass_retype":
            case "account_manage_curr_pass":
            case "account_email_address_pass":
                $ht = '<a href="" rel="nofollow" class="showp"><i class="icon-eye"></i></a><a href="" rel="nofollow" class="hidep no-display"><i class="icon-eye-blocked"></i></a>';
                break;
            default:
                $ht = null;
                break;
        }
        return self::simpleDivWrap('row', '', self::simpleDivWrap($label_class, '', $label_text) . self::simpleDivWrap($div_class, '', self::basicInput($type, $input_name, $input_class, $input_value) . $ht));
    }
    /* wrapping in a div */
    public function simpleDivWrap($class, $id = '', $val = '', $style = '', $span_instead = '', $rel_attr = '')
    {
        $htm      = $span_instead == '' ? 'div' : 'span';
        $rel_attr = $rel_attr != '' ? ' ' . $rel_attr : null;
        $div_id   = $id != '' ? ' id="' . $id . '"' : null;
        $div_st   = $style != '' ? ' style="' . $style . '"' : null;

        return '<' . $htm . '' . $div_st . ' class="' . $class . '"' . $div_id . $rel_attr . '>' . $val . '</' . $htm . '>';
    }
    /* hidden input */
    public function entryHiddenInput($db_id)
    {
        return '<input type="hidden" name="hc_id" value="' . $db_id . '" /><input type="checkbox" class="no-display" id="hcs-id' . $db_id . '" name="current_entry_id[]" value="' . $db_id . '">';
    }
    //notices/errors
    public function noticeTpl($extra_class, $error_message = '', $notice_message = '')
    {
        global $smarty, $language;

        $smarty->assign('notice_message', $notice_message);
        $smarty->assign('error_message', $error_message);

        $n_tpl = VGenerate::simpleDivWrap('pointer left-float wdmax' . $extra_class, 'cb-response', $smarty->fetch("tpl_frontend/tpl_header/tpl_notify.tpl") . '<div class="right-float auto-close-response no-display"><span class="auto-close-time no-display">5</span><span class="auto-close-text">' . $language['frontend.global.close.auto'] . '</span></div>');

        return $n_tpl;
    }
    /* wrapping notices */
    public function noticeWrap($html)
    {
        echo self::simpleDivWrap('pointer left-float no-top-padding wdmax section-bottom-border', 'cb-response-wrap', self::simpleDivWrap('centered', '', $html[2]));
    }

    public function jqHtml($div_id, $div_ct)
    {
        echo self::declareJS('$("' . $div_id . '").html("' . $div_ct . '");');
    }
    public function keepEntryOpen()
    {
        global $class_filter;
        if ($_POST['ct_entry'] != '') {
            $db_id    = substr($class_filter->clr_str($_POST['ct_entry']), 9);
            $entry_id = 'ct-entry-details' . $db_id;
            $extra_js = 'var p_id = $("#' . $entry_id . '").parent().attr("id"); $("#"+p_id+" > div.ct-bullet-out").addClass("ct-bullet-in"); $("#"+p_id+" > div.ct-bullet-label").addClass("bold"); $("#' . $entry_id . '").removeClass("no-display"); $("#ct_entry").val("ct-bullet' . $db_id . '");';

            echo self::declareJS($extra_js);
        }
    }
    public function declareJS($code, $id = '')
    {
        return '<script type="text/javascript"' . ($id != '' ? ' id="' . $id . '"' : null) . '> ' . $code . ' </script>';
    }
    public function actionTooltipJS($on_class, $var_id, $off_class, $_id)
    {
        $extra_js = '$(".' . $on_class . '").mouseover(function() { var ' . $var_id . ' = $(this).attr("id"); showDiv(' . $var_id . '+"' . $_id . '"); $("#"+' . $var_id . ').removeClass("' . $on_class . '").addClass("' . $off_class . '"); $("#"+' . $var_id . ').mouseout(function() { hideDiv(' . $var_id . '+"' . $_id . '"); $("#"+' . $var_id . ').removeClass("' . $off_class . '").addClass("' . $on_class . '"); }); });';

        return $extra_js;
    }
    public function settingsTooltipJS($entry_id)
    {
        $extra_js = '$("#' . $entry_id . '-tip").mouseover(function() { showDiv("' . $entry_id . '-thetip"); $("#' . $entry_id . '-tip").removeClass("different-sub-gray").addClass("different-sub"); $("#' . $entry_id . '-tip").mouseout(function() { hideDiv("' . $entry_id . '-thetip"); $("#' . $entry_id . '-tip").removeClass("different-sub").addClass("different-sub-gray"); }); });';

        return $extra_js;
    }
    public function entryTooltip($class, $pos, $id, $text)
    {
        return '<span title=\'' . $text . '\' rel="tooltip" class="' . $class . '"></span>';
    }
    /* generate heading text and line */
    public static function headingArticle($text, $icon, $nlb = false)
    {
        $html = '
        <article>
            <h3 class="content-title"><i class="' . $icon . '"></i> ' . $text . '</h3>
            <div class="line' . ($nlb ? ' mb-0' : null) . '"></div>
        </article>
        ';

        return $html;
    }
    /* generate lightbox cancel button */
    public static function lb_Cancel($text = false)
    {
        global $language;

        return '<a onclick="$.fancybox.close();" href="javascript:;" class="link cancel-trigger"><span>' . (!$text ? $language['frontend.global.cancel'] : $text) . '</span></a>';
    }
    /* extract blog short tags */
    public static function extract_text($string)
    {
        $text_outside = array();
        $text_inside  = array();
        $t            = "";

        for ($i = 0; $i < strlen($string); $i++) {
            if ($string[$i] == '[' and $string[$i + 1] == 'm' and $string[$i + 2] == 'e' and $string[$i + 3] == 'd' and $string[$i + 4] == 'i' and $string[$i + 5] == 'a') {
                $text_outside[] = $t;
                $t              = "";
                $t1             = "";
                $i++;

                while ($string[$i] != ']') {
                    $t1 .= $string[$i];
                    $i++;
                }
                $text_inside[] = $t1;
            } else {
                if ($string[$i] != ']') {
                    $t .= $string[$i];
                } else {
                    continue;
                }
            }
        }

        if ($t != "") {
            $text_outside[] = $t;
        }

        return $text_inside;
    }
    /* process autocomplete requests */
    public static function processAutoComplete($section, $type = false)
    {
        global $class_filter, $db;

        $sql    = false;
        $query  = $_POST ? $class_filter->clr_str($_POST['query']) : false;
        $output = array('query' => $query, 'suggestions' => array());
        $fields = array('usr_user', 'usr_key');

        switch ($section) {
            case "search": //frontend main search
                $files = new VFiles;
                $type  = $class_filter->clr_str($_POST['t']);

                $fields = $type == 'channel' ? array('usr_user', 'usr_key') : array('file_title', 'file_key');
                $sql    = $type == 'channel' ? sprintf("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_user` LIKE '%s';", $query . '%') : sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`file_title` LIKE '%s';", $type, $query . '%');

                break;
            case "account_media":
                $fields = array('file_title', 'file_key');
                $type   = $class_filter->clr_str($_POST['t']);
                $sql    = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`usr_id`='%s' AND A.`file_title` LIKE '%s';", $type, (int) $_SESSION['USER_ID'], $query . '%');

                break;
            case "account_media_be":
                $fields = array('file_title', 'file_key');
                $type   = $class_filter->clr_str($_POST['t']);
                $sql    = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`usr_id` > 0 AND A.`file_title` LIKE '%s';", $type, $query . '%');

                break;
            case "account_user":
                $sql = sprintf("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_user` LIKE '%s';", $query . '%');

                break;
            case "media_library": //my files / media library
                $fields = array('file_title', 'file_key');
                $type   = $class_filter->clr_str($_POST['t']);
                $_s     = $class_filter->clr_str($_GET['s']);

                switch ($_s) {
                    case "file-menu-entry1":
                        $sql = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`file_title` LIKE '%s';", $type, $query . '%');
                        break;

                    case "file-menu-entry2": //favorites
                        $db_field  = 'file_key';
                        $db_tbl    = 'favorites';
                        $pg_cfg    = 'page_user_files_favorites';
                        $cache_cfg = 'cache_user_files_favorites';
                        break;

                    case "file-menu-entry3": //liked
                        $db_field  = 'file_key';
                        $db_tbl    = 'liked';
                        $pg_cfg    = 'page_user_files_liked';
                        $cache_cfg = 'cache_user_files_liked';
                        break;

                    case "file-menu-entry4": //history
                        $db_field  = 'file_key';
                        $db_tbl    = 'history';
                        $pg_cfg    = 'page_user_files_history';
                        $cache_cfg = 'cache_user_files_history';
                        break;

                    case "file-menu-entry5": //watchlist
                        $db_field  = 'file_key';
                        $db_tbl    = 'watchlist';
                        $pg_cfg    = 'page_user_files_watchlist';
                        $cache_cfg = 'cache_user_files_watchlist';
                        break;

                    default:
                        if (substr($_s, 0, 4) == 'subs' or substr($_s, 0, 4) == 'osub') {
//subscribers and subscriptions
                            $uid = (int) substr($_s, 15);

                            $sql = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`usr_id`='%s' AND A.`file_title` LIKE '%s';", $type, $uid, $query . '%');
                        }
                        if (substr($_s, 0, 16) == 'file-menu-entry6') {
//playlists
                            $t    = str_replace('file-menu-entry6-sub', '', $_s);
                            $type = $t[0];

                            switch ($t[0]) {
                                case "l":$type = 'live';
                                    break;
                                case "v":$type = 'video';
                                    break;
                                case "s":$type = 'short';
                                    break;
                                case "i":$type = 'image';
                                    break;
                                case "a":$type = 'audio';
                                    break;
                                case "d":$type = 'doc';
                                    break;
                                case "b":$type = 'blog';
                                    break;
                            }

                            $t  = str_replace($t[0], '', $t);
                            $id = (int) $t;

                            if ($id > 0) {
                                $sql = sprintf("SELECT `pl_files` FROM `db_%splaylists` WHERE `pl_id`='%s' AND `usr_id`='%s' LIMIT 1;", $type, $id, (int) $_SESSION['USER_ID']);
                                $rs  = $db->execute($sql);

                                if ($rs->fields['pl_files'] != '') {
                                    $keys = unserialize($rs->fields['pl_files']);
                                    $qstr = implode("','", $keys);
                                    $sql  = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`file_key` IN ('%s') AND A.`file_title` LIKE '%s';", $type, $qstr, $query . '%');
                                } else {
                                    $sql = false;
                                }
                            }
                        }

                        break;
                }

                switch ($_s) {
                    case "file-menu-entry2":
                    case "file-menu-entry3":
                    case "file-menu-entry4":
                    case "file-menu-entry5":
                        $keys = array();
                        $sql  = sprintf("SELECT `%s` FROM `db_%s%s` WHERE `usr_id`='%s';", $db_field, $type, $db_tbl, (int) $_SESSION['USER_ID']);
                        $rs   = $db->execute($sql);

                        if ($rs->fields[$db_field] != '') {
                            while (!$rs->EOF) {
                                $keys[] = $rs->fields['file_key'];
                                $rs->MoveNext();
                            }
                            $qstr = implode("','", $keys);
                            $sql  = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`file_key` IN ('%s') AND A.`file_title` LIKE '%s';", $type, $qstr, $query . '%');
                        } else {
                            $sql = false;
                        }
                        break;
                }
                break;
            case "files":
            case "xfer_new":
                $fields = array('file_title', 'file_key');

                switch ($_GET['s']) {
                    case "backend-menu-entry6-sub1":$type = 'video';
                        break;
                    case "backend-menu-entry6-sub7":$type = 'short';
                        break;
                    case "backend-menu-entry6-sub2":$type = 'image';
                        break;
                    case "backend-menu-entry6-sub3":$type = 'audio';
                        break;
                    case "backend-menu-entry6-sub4":$type = 'doc';
                        break;
                    case "backend-menu-entry6-sub5":$type = 'blog';
                        break;
                    case "backend-menu-entry6-sub6":$type = 'live';
                        break;
                }

                $sql = sprintf("SELECT A.`file_key`, A.`file_title` FROM `db_%sfiles` A WHERE A.`file_title` LIKE '%s';", $type, $query . '%');
                break;
            case "xfer_list":
                $fields = array('file_title', 'file_key');

                $sql = sprintf("SELECT A.`file_key`, A.`file_title`, C.`file_key` FROM `db_%sfiles` A, `db_%stransfers` C WHERE A.`file_key`=C.`file_key` AND A.`file_title` LIKE '%s';", $type, $type, $query . '%');
                break;
            case "accounts":
            case "private_message":
                $sql = sprintf("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_user` LIKE '%s';", $query . '%');
                break;
            case "upload":
            case "import":
            case "new_blog":
                $sql = sprintf("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_user` LIKE '%s' AND `usr_status`='1';", $query . '%');
                break;
        }

        if ($query and $sql) {
            $res = $db->execute($sql);

            if ($res->fields[$fields[0]]) {
                $obj = array();
                while (!$res->EOF) {
                    $obj[] = array("value" => html_entity_decode($res->fields[$fields[0]]), "data" => $res->fields[$fields[1]]);

                    $res->MoveNext();
                }

                $output['suggestions'] = $obj;
            }

            echo json_encode($output);
        }
    }
    /* load backend css plugins (minified) */
    public static function becssplugins()
    {
        global $cfg, $href, $section, $smarty, $class_filter;

        $html = null;
        $uid  = $_SESSION['ADMIN_NAME'];

        switch ($section) {
            case VHref::getKey("be_dashboard"):
            case VHref::getKey("be_analytics"):
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['styles_url_be'] . '/dash.css">';
                break;
            case VHref::getKey("be_affiliate"):
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/datepicker/tiny-date-picker.min.css">';
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/datepicker/date-range-picker.min.css">';
                break;
            case VHref::getKey("be_subscribers"):
            case VHref::getKey("be_tokens"):
                $html .= (isset($_GET['rg']) and (int) $_GET['rg'] == 1) ? '<link rel="stylesheet" type="text/css" href="' . $cfg['styles_url_be'] . '/dash.css">' : null;
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/datepicker/tiny-date-picker.min.css">';
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/datepicker/date-range-picker.min.css">';
                break;
            case VHref::getKey("be_upload"):
                $html .= '<link type="text/css" rel="stylesheet" href="' . $cfg['javascript_url'] . '/uploader/jquery.plupload.queue/css/jquery.plupload.queue.min.css" media="screen">';
                break;
            case VHref::getKey("be_import"):
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/grabber/grabber.min.css">';
                break;
        }

        if ($uid == $cfg['backend_username'] and ($cfg['video_player'] == 'vjs' or $cfg['audio_player'] == 'vjs')) {
            $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
            $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
            $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['styles_url_be'] . '/init1.min.css">';
            $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/lightbox/jquery.fancybox.min.css">';
            $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/multilevelmenu/css/component.min.css">';
            $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/autocomplete/jquery.autocomplete.min.css">';
            $html .= '<link href="https://vjs.zencdn.net/5.19/video-js.min.css" rel="stylesheet">';
            $html .= '<link href="' . $cfg['scripts_url'] . '/shared/videojs/videojs-styles.min.css" rel="stylesheet">';
        } elseif ($uid == '' or $uid != $cfg['backend_username']) {
            $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['styles_url_be'] . '/login.min.css">';
        }

        return $html;
    }
    /* load backend javascript plugins (minified) */
    public static function bejsplugins()
    {
        global $cfg, $href, $section, $smarty, $class_filter;

        $html = null;
        $uid  = $_SESSION['ADMIN_NAME'];

        if (!isset($_GET['rg'])) {
            $html .= '<script type="text/javascript" src="' . $cfg['javascript_url_be'] . '/fw.init.min.js"></script>';
        }

        switch ($section) {
            case VHref::getKey("be_affiliate"):
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/datepicker/tiny-date-picker.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/datepicker/date-range-picker.min.js"></script>';
                $html .= '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $cfg['affiliate_maps_api_key'] . '" type="text/javascript"></script>';
                break;
            case VHref::getKey("be_analytics"):
                $html .= "<script type='text/javascript'>(function(w,d,s,g,js,fs){g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};js=d.createElement(s);fs=d.getElementsByTagName(s)[0];js.src='https://apis.google.com/js/platform.js';fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};}(window,document,'script'));</script>";
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/Chart.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/moment.min.js"></script>';
                $html .= '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $cfg['google_analytics_maps'] . '" type="text/javascript"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/view-selector2.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/date-range-selector.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/active-users.js"></script>';
                $html .= $smarty->fetch("tpl_backend/tpl_affiliatejs_min.tpl");
                break;
            case VHref::getKey("be_dashboard"):
                $html .= '<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>';
                $html .= '<script type="text/javascript">google.charts.load("current", {packages: ["corechart"]});</script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/Chart.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/moment.min.js"></script>';
                $html .= $smarty->fetch("tpl_backend/tpl_affiliatejs_min.tpl");
                $html .= '<script type="text/javascript">$(document).ready(function () {$(".icheck-box input").each(function () {var self = $(this);self.iCheck({checkboxClass: "icheckbox_square-blue",radioClass: "iradio_square-blue",increaseArea: "20%"});});$(".icheck-box").toggleClass("no-display");$(".filters-loading").addClass("no-display");});</script>';
                break;
            case VHref::getKey("be_subscribers"):
            case VHref::getKey("be_tokens"):
                $html .= (isset($_GET['rg']) and (int) $_GET['rg'] == 1) ? '<script type="text/javascript" src="' . $cfg['javascript_url_be'] . '/jsapi.js"></script>' : null;
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/datepicker/tiny-date-picker.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/datepicker/date-range-picker.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/Chart.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['modules_url_be'] . '/m_tools/m_gasp/dash/moment.min.js"></script>';
                $html .= $smarty->fetch("tpl_backend/tpl_affiliatejs_min.tpl");
                $html .= (isset($_GET['rg']) and (int) $_GET['rg'] == 1) ? '<script type="text/javascript">$(document).ready(function () {$(".icheck-box input").each(function () {var self = $(this);self.iCheck({checkboxClass: "icheckbox_square-blue",radioClass: "iradio_square-blue",increaseArea: "20%"});});$(".icheck-box").toggleClass("no-display");$(".filters-loading").addClass("no-display");});</script>' : null;
                break;
            case VHref::getKey("be_upload"):
                $html .= $smarty->fetch("tpl_backend/tpl_uploadjs_min.tpl");
                break;
            case VHref::getKey("be_import"):
                $html .= '<script src="' . $cfg['scripts_url'] . '/shared/grabber/grabber.js"></script>';
                $html .= '<script type="text/javascript">' . $smarty->fetch("f_scripts/be/js/settings-accordion.js") . '</script>';
                break;
            default:
                if ($uid == '' or $uid != $cfg['backend_username']) {
                    $html .= $smarty->fetch("tpl_backend/tpl_loginjs_min.tpl");
                    $html .= '<script type="text/javascript">function getPasswordStrength(pw){var pwlength=(pw.length);if(pwlength>5)pwlength=5;var numnumeric=pw.replace(/[0-9]/g,"");var numeric=(pw.length-numnumeric.length);if(numeric>3)numeric=3;var symbols=pw.replace(/\W/g,"");var numsymbols=(pw.length-symbols.length);if(numsymbols>3)numsymbols=3;var numupper=pw.replace(/[A-Z]/g,"");var upper=(pw.length-numupper.length);if(upper>3)upper=3;var pwstrength=((pwlength*10)-20)+(numeric*10)+(numsymbols*15)+(upper*10);if(pwstrength<0){pwstrength=0}if(pwstrength>100){pwstrength=100}return pwstrength}function updatePasswordStrength_new(pwbox,pwdiv,divorderlist){var bpb=""+pwbox.value;var pwstrength=getPasswordStrength(bpb);var bars=(parseInt(pwstrength/10)*10);var pwdivEl=document.getElementById(pwdiv);if(!pwdivEl){alert(\'Password Strength Display Element Missing\')}var divlist=pwdivEl.getElementsByTagName(\'div\');var maindiv=divlist[0].getElementsByTagName(\'div\');maindiv[0].className=\'pass_meter_base pass_meter_\'+bars;var txtdivnum=1;if(divorderlist&&divorderlist.text>-1){txtdivnum=divorderlist.text}var txtdiv=divlist[txtdivnum];if(txtdiv&&self.pass_strength_phrases){txtdiv.innerHTML=pass_strength_phrases[bars]}}function updatePasswordStrength(pwbox,pwdiv,divorderlist){var bpb=""+pwbox.value;var pwstrength=getPasswordStrength(bpb);var bars=(parseInt(pwstrength/10)*10);var pwdivEl=document.getElementById(pwdiv);if(!pwdivEl){alert(\'Password Strength Display Element Missing\')}var divlist=pwdivEl.getElementsByTagName(\'div\');var imgdivnum=0;var txtdivnum=1;if(divorderlist&&divorderlist.text>-1){txtdivnum=divorderlist.text}if(divorderlist&&divorderlist.image>-1){imgdivnum=divorderlist.image}var imgdiv=divlist[imgdivnum];imgdiv.id=\'ui-passbar-\'+bars;var txtdiv=divlist[txtdivnum];if(txtdiv&&self.pass_strength_phrases){txtdiv.innerHTML=pass_strength_phrases[bars]}}var pass_strength_phrases={0:\'\',10:\'\',20:\'\',30:\'\',40:\'\',50:\'\',60:\'\',70:\'\',80:\'\',90:\'\',100:\'\'};</script>';
                    $html .= '<script type="text/javascript">(function(){[].slice.call(document.querySelectorAll(\'.tabs\')).forEach(function(el){new CBPFWTabs(el);});})();function oldSafariCSSfix(){if(isOldSafari()){var tabnr = $(".login-page .tabs nav ul li").length;var width = jQuery(\'.login-page\').width() - 32;jQuery(".login-page .tabs nav ul li").width((width / tabnr) - 1).css("float", "left");jQuery(".login-page .tabs nav").css("width", (width + 1));}}$(document).ready(function(){$(document).on("click", ".tabs ul:not(.fileThumbs) li",function(){});});jQuery(window).load(function(){oldSafariCSSfix();});jQuery(window).resize(function(){oldSafariCSSfix();});function isOldSafari(){return !!navigator.userAgent.match(\' Safari/\') && !navigator.userAgent.match(\' Chrome\') && (!!navigator.userAgent.match(\' Version/6.0\') || !!navigator.userAgent.match(\' Version/5.\'));}</script>';
                    $html .= '<script type="text/javascript">$(document).ready(function(){$(".icheck-box input").each(function(){var self=$(this);self.iCheck({checkboxClass:"icheckbox_square-blue",radioClass:"iradio_square-blue",increaseArea:"20%"})});});</script>';
                }
                break;
        }

        if ($uid == $cfg['backend_username'] and ($cfg['video_player'] == 'vjs' or $cfg['audio_player'] == 'vjs')) {
            $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
            $html .= '<script src="https://vjs.zencdn.net/5.19/video.min.js"></script>';
            $html .= '<script src="' . $cfg['scripts_url'] . '/shared/videojs/videojs-scripts.min.js"></script>';
            $html .= '<script defer async src="' . $cfg['scripts_url'] . '/shared/videojs/videojs-hlsjs-plugin.js"></script>';
        }
        if ($uid == $cfg['backend_username'] and ($cfg['video_player'] == 'jw' or $cfg['audio_player'] == 'jw')) {
            $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/jwplayer/jwplayer.js"></script>';
        }

        return $html;
    }

    /* load frontend css plugins (minified) */
    public static function cssplugins()
    {
        global $cfg, $href, $section, $smarty, $class_filter, $db;

        $html   = null;
        $uid    = (int) $_SESSION['USER_ID'];
        $mobile = VHref::isMobile();

        $html .= $cfg['google_webmaster'] != '' ? '<meta name="google-site-verification" content="' . $cfg['google_webmaster'] . '">' : null;
        $html .= $cfg['yahoo_explorer'] != '' ? '<meta name="y_key" content="' . $cfg['yahoo_explorer'] . '">' : null;
        $html .= $cfg['bing_validate'] != '' ? '<meta name="msvalidate.01" content="' . $cfg['bing_validate'] . '">' : null;

        switch ($section) {
            case VHref::getKey("index"):
                $html .= $uid > 0 ? self::icheckbluecss() : null;
                break;
            case VHref::getKey("watch"):
                $blocked = $smarty->getTemplateVars('blocked');
                if (!$blocked) {
                    $html .= '<link rel="preload" href="' . $cfg['styles_url'] . '/view.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                    $html .= '<noscript><link rel="stylesheet" href="' . $cfg['styles_url'] . '/view.min.css"></noscript>';
                    $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                    $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                    $html .= '<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                    $html .= '<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>';
                    $html .= $smarty->fetch("tpl_frontend/tpl_viewcss_min.tpl");
                    $html .= ($mobile and isset($_GET['d'])) ? self::pdfjs_css() : null;
                }
                break;
            case VHref::getKey("playlist"):
                $html .= '<link rel="preload" href="' . $cfg['styles_url'] . '/view.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['styles_url'] . '/view.min.css"></noscript>';
                $html .= '<link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>';

                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/jssocials/jssocials.css">';
                break;
            case VHref::getKey("upload"):
            case VHref::getKey("import"):
                $html .= '<style>.top-wrapper{background:#06a2cb none repeat scroll 0 0;color:#fff;padding:10px;margin-bottom:10px;cursor:pointer}#left-side form.entry-form-class{padding:0 10px}#right-side-form .conf-green,#right-side-form .err-red,#right-side-form .prev-page,#right-side-form .next-page{color:white;margin:0;padding:3px;font-weight:bold;margin-bottom:3px;display:inline-block}#right-side-form .conf-green{background-color:green}#right-side-form .err-red{background-color:red}#left-side .prev-page,#left-side .next-page{background-color:lightblue;color:#555;margin-left:5px;margin-top:10px;font-size:12px}.embed-title{font-weight:bold;font-size:16px;color:#000;margin-bottom:5px}.embed-description{font-size:14px;font-weight:normal;color:#666;margin-bottom:10px}.embed-category{font-size:14px;font-weight:normal;color:#333}.embed-tags{font-size:14px;font-weight:normal;color:#555;margin-top:10px}#right-side .bm-larger .vs-column a img{width:100%}</style>';
                $html .= self::icheckcss();
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['javascript_url'] . '/uploader/jquery.plupload.queue/css/jquery.plupload.queue.min.css">';
                break;
            case VHref::getKey("subscribers"):
            case VHref::getKey("affiliate"):
            case VHref::getKey("tokens"):
                $html .= $smarty->fetch("tpl_frontend/tpl_affiliatecss_min.tpl");
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                break;
            case VHref::getKey("search"):
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                break;
            case VHref::getKey("channels"):
                $html .= $uid > 0 ? self::icheckbluecss() : null;
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                break;
            case VHref::getKey("channel"):
                $cs = $smarty->getTemplateVars('channel_module');
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                $html .= self::icheckcss();
                $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg['styles_url'] . '/channel.init.min.css">';
                if ($cs == VHref::getKey('discussion')) {
                    $html .= '<link rel="preload" href="' . $cfg['styles_url'] . '/view.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                    $html .= '<noscript><link rel="stylesheet" href="' . $cfg['styles_url'] . '/view.min.css"></noscript>';
                }
                break;
            case VHref::getKey("manage_channel"):
                $html .= self::icheckbluecss();
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/cropper/cropper.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/cropper/cropper.min.css"></noscript>';
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                break;
            case VHref::getKey("account"):
            case VHref::getKey("files"):
            case VHref::getKey("messages"):
            case VHref::getKey("subscriptions"):
            case VHref::getKey("following"):
            case VHref::getKey("browse"):
                $html .= self::icheckbluecss();
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                break;
            case VHref::getKey("respond"):
            case VHref::getKey("comments"):
            case VHref::getKey("playlists"):
                $html .= '<link rel="preload" href="' . $cfg['styles_url'] . '/view.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['styles_url'] . '/view.min.css"></noscript>';
                $html .= self::icheckbluecss();
                break;
            case VHref::getKey("files_edit"):
                $html .= '<link rel="preload" href="' . $cfg['styles_url'] . '/view.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['styles_url'] . '/view.min.css"></noscript>';
                $html .= self::icheckbluecss();
                $html .= '<link rel="preload" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css" as="style" onload="this.rel=\'stylesheet\'">';
                $html .= '<noscript><link rel="stylesheet" href="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.css"></noscript>';
                break;
            case VHref::getKey("signin"):
            case VHref::getKey("signup"):
            case VHref::getKey("x_recovery"):
            case VHref::getKey("service"):
            case VHref::getKey("renew"):
            case VHref::getKey("x_payment"):
                if (isset($_GET['next'])) {
                    $n = explode('/', $class_filter->clr_str($_GET['next']));
                    $t = $class_filter->clr_str($n[0]);
                    if ($t === 'v' or $t === 'i' or $t === 'a' or $t === 'd' or $t === 'b' or $t === 'l') {
                        $k  = $class_filter->clr_str($n[1]);
                        $tp = ($t === 'v' ? 'video' : ($t === 'i' ? 'image' : ($t === 'a' ? 'audio' : ($t === 'd' ? 'doc' : ($t === 'b' ? 'blog' : ($t === 'l' ? 'live' : ($t === 's' ? 'short' : 'video')))))));
                        $uu = $db->execute(sprintf("SELECT A.`usr_key` FROM `db_accountuser` A, `db_%sfiles` B WHERE A.`usr_id`=B.`usr_id` AND B.`file_key`='%s' LIMIT 1", $tp, $k));
                        $u  = $uu->fields['usr_key'];
                        $smarty->assign('file_key', $k);
                        $smarty->assign('usr_key', $u);
                        $smarty->assign('media_files_url', VGenerate::fileURL($tp, $k, 'thumb'));
                        $html .= $smarty->fetch("tpl_frontend/tpl_headview_min.tpl");
                    }
                }

                $html .= '<style>.pass_meter_0{width:1%;background-color:#c00}.pass_meter_10{width:10%;background-color:#c00}.pass_meter_20{width:20%;background-color:#f60}.pass_meter_30{width:30%;background-color:#f60}.pass_meter_40{width:40%;background-color:#f60}.pass_meter_50{width:50%;background-color:#039}.pass_meter_60{width:60%;background-color:#039}.pass_meter_70{width:70%;background-color:#060}.pass_meter_80{width:80%;background-color:#060}.pass_meter_90{width:90%;background-color:#0c0}.pass_meter_100{width:100%;background-color:#0c0}.pass_meter_base,.pass_meter_base{float:left;height:5px;text-align:left}.pass_meter{background-color:#ccc;width:185px;height:5px;border:0 solid black;margin:0;padding:0}#ps-rating{display:inline;margin-top:-20px;float:right;margin-right:80px}</style>';
                $html .= self::icheckbluecss();
                $html .= self::logincss();
                break;
        }

        return $html;
    }
    /* generate swiper js for media types */
    public static function swiperjs($type = 'video', $selector = false)
    {
        return '<script type="text/javascript">$(document).ready(function(){const swiper' . ucfirst($type) . '=new Swiper("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-' . $type . '",{direction:"horizontal",loop:false,slidesPerView:"auto",freeMode:true,slideToClickedSlide:false,spaceBetween:0,grabCursor:true,navigation:{nextEl:"' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-next-' . $type . '",prevEl:"' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-prev-' . $type . '",},on:{init:function(){$("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-prev, ' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-next").removeAttr("style");' . ((!isset($_GET['do']) and $type != 'promoted') ? '$("#' . $type . '-content .swiper-slide:first").addClass("swiper-slide-current");' : null) . '$("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-prev-' . $type . '.swiper-button-disabled").prev().addClass("ml-0");this.slideTo($("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-' . $type . ' .swiper-slide-current").index());}}});});</script>';
    }
    public static function swiperjs_unmin($type = 'video', $selector = false)
    {
        return '<script type="text/javascript">
        $(document).ready(function(){
            const swiper' . ucfirst($type) . ' = new Swiper("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-' . $type . '", {
              // Optional parameters
              direction: "horizontal",
              loop: false,
              slidesPerView: "auto",
              freeMode: true,
              slideToClickedSlide: false,
              spaceBetween: 0,
              grabCursor:true,

              // Navigation arrows
              navigation: {
                nextEl: "' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-next-' . $type . '",
                prevEl: "' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-prev-' . $type . '",
              },

              on: {
                init: function(){
                    $("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-prev, ' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-next").removeAttr("style");
                    ' . ((!isset($_GET['do']) and $type != 'promoted') ? '$("#' . $type . '-content .swiper-slide:first").addClass("swiper-slide-current");' : null) . '
                    $("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-button-prev-' . $type . '.swiper-button-disabled").prev().addClass("ml-0");
                    this.slideTo($("' . ($selector ? '#' . $selector . ' ' : null) . '.swiper-' . $type . ' .swiper-slide-current").index());
                },
              },
            });
        });
        </script>';
    }
    /* swiper display tweak */
    public static function ssd()
    {
        return 'block';
    }
    public static function sso()
    {
        return ' style="opacity:0"';
    }
    public static function smarty_swiper()
    {
        global $smarty;

        $smarty->assign('ssd', self::ssd());
        $smarty->assign('sso', self::sso());
    }
    /* load frontend javascript plugins (minified) */
    public static function jsplugins()
    {
        global $cfg, $href, $section, $smarty, $class_filter;

        $html   = null;
        $uid    = (int) $_SESSION['USER_ID'];
        $mobile = VHref::isMobile();

        switch ($section) {
            case VHref::getKey("index"):
                $html .= '<script type="text/javascript">jQuery(window).load(function(){thumbFade()});</script>';
                break;
            case VHref::getKey("watch"):
                $blocked = $smarty->getTemplateVars('blocked');
                if (!$blocked) {
                    $html .= '<script type="text/javascript">var _rel = "' . $cfg['main_url'] . '/' . VHref::getKey("files") . '";</script>';
                    $html .= '<script type="text/javascript" src="' . $cfg['javascript_url'] . '/min/view.init0.min.js?_' . time() . '"></script>';
                    $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/linkify/linkify.init.js"></script>';
                    $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                    $html .= $cfg['comment_emoji'] == 1 ? '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>' : null;
                    $html .= self::swiperjs('main');
                    if (isset($_GET['l'])) {
                        $hh = '$("#main_header").outerHeight()';
                        if ($mobile) {
                            $hh = '($("#main_header").outerHeight() + $("#view-player").outerHeight() + 10)';
                            $html .= '<script>$(document).ready(function(){var frame = document.getElementById("vs-chat");frame.addEventListener("load",function(){var h=window.innerHeight-' . $hh . ';if(h<320)h=320;$("#vs-chat").css("height","100%").css("min-height",h).css("max-height",h);});$(window).on("resize",function(){var h=window.innerHeight-' . $hh . ';if(h<320)h=320;$("#vs-chat").css("height","100%").css("min-height",h).css("max-height",h);});});</script>';
                        }
                    }
                    $html .= $smarty->fetch("tpl_frontend/tpl_viewjs_min.tpl");
                    if ($mobile and isset($_GET['d'])) {
                        $html .= '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.2.146/pdf.min.js"></script>';
                        $html .= '<script src="https://cdn.jsdelivr.net/gh/dealfonso/pdfjs-viewer@1.1/pdfjs-viewer.min.js"></script>';
                    }
                }
                break;
            case VHref::getKey("playlist"):
                break;
            case VHref::getKey("upload"):
                $error_message = $smarty->getTemplateVars('error_message');
                if ($error_message == '') {
                    $html .= '<script type="text/javascript" src="' . $cfg['javascript_url'] . '/uploader/plupload.full.min.js"></script>';
                    $html .= '<script type="text/javascript" src="' . $cfg['javascript_url'] . '/uploader/jquery.plupload.queue/jquery.plupload.queue.js"></script>';
                    $html .= $smarty->fetch("tpl_frontend/tpl_file/tpl_uploadjs.tpl");
                }
                break;
            case VHref::getKey("import"):
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/grabber/grabber.js"></script>';
                $html .= '<script type="text/javascript">' . $smarty->fetch("f_scripts/be/js/settings-accordion.js") . '</script>';
                break;
            case VHref::getKey("files"):
            case VHref::getKey("subscriptions"):
            case VHref::getKey("following"):
                $html .= ($section == VHref::getKey("subscriptions") or $section == VHref::getKey("following")) ? '<script type="text/javascript">$(document).ready(function(){$("a#inline").fancybox({ minWidth: "80%",  margin: 20 });$(".menu-panel-entry-active").click(function(event){if($("#session-accordion li:first").hasClass("menu-panel-entry-active")){var _url=current_url+menu_section+"?cfg";$.fancybox({type:"ajax",minWidth:"80%",minHeight:"80%",margin:20,href:_url});}});});</script>' : null;
                $html .= ($section == VHref::getKey("files") and $cfg['file_playlists'] == 1) ? '<script type="text/javascript">$(document).ready(function(){$(document).on({click:function(){var new_pl_url=current_url+menu_section+"?s=file-menu-entry6&m=1&a=pl-new&t="+(typeof $(this).attr("rel-type")!="undefined"?$(this).attr("rel-type"):"video");$.fancybox({type:"ajax",minWidth:"80%",margin: 10,href:new_pl_url,height:"auto",autoHeight:"true",autoResize:"true",autoCenter:"true"});}},"#new-playlist");$(document).on("click",".plcfg-popup",function(){cfg_pl_url=current_url+menu_section+"?s="+$(".pl-entry.menu-panel-entry-active").attr("id")+"&m=1&a=pl-cfg";$.fancybox({type:"ajax",minWidth:"80%",minHeight:"80%",margin:20,href:cfg_pl_url});});});</script>' : null;
                $html .= '<script type="text/javascript" src="' . $cfg['javascript_url'] . '/jquery.sortable.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/linkify/linkify.init.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                $html .= '<script type="text/javascript">$(document).ready(function(){jQuery(document).on({click:function(e){e.preventDefault();if($(this).hasClass("swiper-slide-tnav")){window.location=$(this).attr("href");return;}t=$(".view-mode-type.active").attr("id").replace("view-mode-", "");h=$(this).attr("href");if($("#"+t+"-content .content-current nav ul a[href=\'"+h+"\']").parent().hasClass("tab-current"))return;$("#"+t+"-content .swiper-slide").removeClass("swiper-slide-current").removeClass("swiper-slide-active");$(this).parent().addClass("swiper-slide-current");$("#"+t+"-content section").removeClass("content-current");$(h).addClass("content-current");if (!$(".tabs").hasClass("list-cr-tabs")){$(h + " .section-tabs li").removeClass("tab-current");$(h + " .section-tabs li a[href=\'"+h+"\']").parent().addClass("tab-current");$("#"+t+"-content "+h+" nav ul a[href=\'"+h+"\']").parent().click();}else{$("#"+t+"-content ul.cr-tabs a[href=\'"+h+"\']").parent().click();}}}, ".swiper .swiper-slide a");});</script>';

                break;
            case VHref::getKey("messages"):
                $html .= '<script type="text/javascript" src="' . $cfg['javascript_url'] . '/jquery.sortable.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                break;
            case VHref::getKey("files_edit"):
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/jquery.form.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                $html .= self::swiperjs('tnav');
                break;

            case VHref::getKey("account"):
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/jquery.form.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                break;
            case VHref::getKey("browse"):
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/linkify/linkify.init.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                /*jquery.jscroll.min.js*/
                $html .= '<script type="text/javascript">!function(t){function e(){var e,i,n={height:a.innerHeight,width:a.innerWidth};return n.height||(e=r.compatMode,(e||!t.support.boxModel)&&(i="CSS1Compat"===e?f:r.body,n={height:i.clientHeight,width:i.clientWidth})),n}function i(){return{top:a.pageYOffset||f.scrollTop||r.body.scrollTop,left:a.pageXOffset||f.scrollLeft||r.body.scrollLeft}}function n(){var n,l=t(),r=0;if(t.each(d,function(t,e){var i=e.data.selector,n=e.$element;l=l.add(i?n.find(i):n)}),n=l.length)for(o=o||e(),h=h||i();n>r;r++)if(t.contains(f,l[r])){var a,c,p,s=t(l[r]),u={height:s.height(),width:s.width()},g=s.offset(),v=s.data("inview");if(!h||!o)return;g.top+u.height>h.top&&g.top<h.top+o.height&&g.left+u.width>h.left&&g.left<h.left+o.width?(a=h.left>g.left?"right":h.left+o.width<g.left+u.width?"left":"both",c=h.top>g.top?"bottom":h.top+o.height<g.top+u.height?"top":"both",p=a+"-"+c,v&&v===p||s.data("inview",p).trigger("inview",[!0,a,c])):v&&s.data("inview",!1).trigger("inview",[!1])}}var o,h,l,d={},r=document,a=window,f=r.documentElement,c=t.expando;t.event.special.inview={add:function(e){d[e.guid+"-"+this[c]]={data:e,$element:t(this)},l||t.isEmptyObject(d)||(l=setInterval(n,250))},remove:function(e){try{delete d[e.guid+"-"+this[c]]}catch(i){}t.isEmptyObject(d)&&(clearInterval(l),l=null)}},t(a).bind("scroll resize scrollstop",function(){o=h=null}),!f.addEventListener&&f.attachEvent&&f.attachEvent("onfocusin",function(){h=null})}(jQuery);</script>';
                $html .= $cfg['new_layout'] == 1 ? self::swiperjs('categories') : self::swiperjs('main');
                $html .= '<script type="text/javascript">$(document).ready(function(){jQuery(document).on({click:function(e){' . ($cfg['new_layout'] == 0 ? 'e.preventDefault();' : null) . 'h=$(this).attr("href");if($(".tabs nav ul a[href=\'"+h+"\']").parent().hasClass("tab-current"))return;$(".swiper-slide").removeClass("swiper-slide-current");$(this).parent().addClass("swiper-slide-current");$(".tabs nav ul a[href=\'"+h+"\']").parent().click()}}, ".swiper .swiper-slide a");});</script>';
                break;
            case VHref::getKey("channels"):
                $type = 'promoted';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                $html .= '<script type="text/javascript">$(document).ready(function(){let fs = [];$(".featured.channels .swiper").each(function(i, el){var id=$(this).attr("id");fs[i]=new Swiper(".swiper-"+id,{direction:"horizontal",loop:false,slidesPerView:"auto",freeMode:true,slideToClickedSlide:false,spaceBetween:0,grabCursor:true,navigation:{nextEl: ".swiper-button-next-"+id,prevEl:".swiper-button-prev-" + id},on:{init:function(){$(".swiper-ph-" + id).hide();$(".swiper-button-prev,.swiper-button-next").removeAttr("style");$(".swiper-top-off" + id).fadeIn(200);}}});});const swiper' . ucfirst($type) . ' = new Swiper(".swiper-' . $type . '",{direction:"horizontal",loop:false,slidesPerView:"auto",freeMode:true,slideToClickedSlide:false,spaceBetween:0,grabCursor:true,navigation:{nextEl:".swiper-button-next-' . $type . '",prevEl:".swiper-button-prev-' . $type . '"},on:{init:function(){$(".swiper-ph-' . $type . '").hide();$(".swiper-button-prev,.swiper-button-next").removeAttr("style");$(".swiper-top-' . $type . '-off").fadeIn(200);}}});$(window).on("orientationchange",function(){setTimeout(()=>{swiper' . ucfirst($type) . '.update();swiper' . ucfirst($type) . '.updateSize();swiper' . ucfirst($type) . '.updateProgress();swiper' . ucfirst($type) . '.updateSlides();swiper' . ucfirst($type) . '.updateSlidesClasses();$(".featured.channels .swiper").each(function(i, el){fs[i].update();fs[i].updateSize();fs[i].updateProgress();fs[i].updateSlides();fs[i].updateSlidesClasses();});},500)})});</script>';
                /*jquery.jscroll.min.js*/
                $html .= '<script type="text/javascript">!function(t){function e(){var e,i,n={height:a.innerHeight,width:a.innerWidth};return n.height||(e=r.compatMode,(e||!t.support.boxModel)&&(i="CSS1Compat"===e?f:r.body,n={height:i.clientHeight,width:i.clientWidth})),n}function i(){return{top:a.pageYOffset||f.scrollTop||r.body.scrollTop,left:a.pageXOffset||f.scrollLeft||r.body.scrollLeft}}function n(){var n,l=t(),r=0;if(t.each(d,function(t,e){var i=e.data.selector,n=e.$element;l=l.add(i?n.find(i):n)}),n=l.length)for(o=o||e(),h=h||i();n>r;r++)if(t.contains(f,l[r])){var a,c,p,s=t(l[r]),u={height:s.height(),width:s.width()},g=s.offset(),v=s.data("inview");if(!h||!o)return;g.top+u.height>h.top&&g.top<h.top+o.height&&g.left+u.width>h.left&&g.left<h.left+o.width?(a=h.left>g.left?"right":h.left+o.width<g.left+u.width?"left":"both",c=h.top>g.top?"bottom":h.top+o.height<g.top+u.height?"top":"both",p=a+"-"+c,v&&v===p||s.data("inview",p).trigger("inview",[!0,a,c])):v&&s.data("inview",!1).trigger("inview",[!1])}}var o,h,l,d={},r=document,a=window,f=r.documentElement,c=t.expando;t.event.special.inview={add:function(e){d[e.guid+"-"+this[c]]={data:e,$element:t(this)},l||t.isEmptyObject(d)||(l=setInterval(n,250))},remove:function(e){try{delete d[e.guid+"-"+this[c]]}catch(i){}t.isEmptyObject(d)&&(clearInterval(l),l=null)}},t(a).bind("scroll resize scrollstop",function(){o=h=null}),!f.addEventListener&&f.attachEvent&&f.attachEvent("onfocusin",function(){h=null})}(jQuery);</script>';
                break;
            case VHref::getKey("channel"):
                $cm = $smarty->getTemplateVars('channel_module');
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/linkify/linkify.init.js"></script>';
                $html .= $cfg['comment_emoji'] == 1 ? '<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>' : null;
                $html .= self::swiperjs('main');
                $html .= self::swiperjs('channel');
                $html .= '<script type="text/javascript">$(document).ready(function(){jQuery(document).on({click:function(e){
                    if(!$(this).parent().parent().parent().hasClass("swiper-channel")||$(this).parent().parent().parent().hasClass("swiper-main")){e.preventDefault();}h=$(this).attr("href");if (typeof $("#section-playlists").html()=="undefined"){if($(".tabs nav ul a[href=\'"+h+"\']").parent().hasClass("tab-current")){return;}}
                        $(this).parent().parent().find(".swiper-slide").removeClass("swiper-slide-current");
                        $(this).parent().addClass("swiper-slide-current");if (typeof $("#section-playlists").html()=="undefined"){$(".tabs nav ul a[href=\'"+h+"\']").parent().click();} else {var apt=$("section.filter .view-mode-type.active").attr("id").replace("view-mode-", "");$("#section-playlists .content-current").removeClass("content-current");$("section"+h).addClass("content-current");$(".tabs .content-current nav ul a[href=\'"+h+"\']").parent().click();}}},".swiper .swiper-slide a");});</script>';
                $html .= '<script type="text/javascript">$("pre.hp-pre,.act-title span.act-list-action + span").linkify({defaultProtocol:"https",validate:{email:function(value){return false}},ignoreTags:["script","style"]});</script>';
                $html .= '<script type="text/javascript">(function(){[].slice.call(document.querySelectorAll(".tabs")).forEach(function(el){new CBPFWTabs(el)})})();function isOldSafari(){return!!navigator.userAgent.match(" Safari/")&&!navigator.userAgent.match(" Chrome")&&(!!navigator.userAgent.match(" Version/6.0")||!!navigator.userAgent.match(" Version/5."))}function oldSafariCSSfix(){return}(function(){jQuery(document).on({click:function(){}},"#channel-tabs ul:not(#main-content ul):not(.fileThumbs) li")})();jQuery(window).load(function(){oldSafariCSSfix()});jQuery(window).resize(function(){oldSafariCSSfix()});function html2amp(str){return str.replace(/&amp;/g,"&")}$(".main-filter-mode").dlmenu({animationClasses:{classin:"dl-animate-in-5",classout:"dl-animate-out-5"}});</script>';
                /*jquery.jscroll.min.js*/
                $html .= '<script type="text/javascript">!function(t){function e(){var e,i,n={height:a.innerHeight,width:a.innerWidth};return n.height||(e=r.compatMode,(e||!t.support.boxModel)&&(i="CSS1Compat"===e?f:r.body,n={height:i.clientHeight,width:i.clientWidth})),n}function i(){return{top:a.pageYOffset||f.scrollTop||r.body.scrollTop,left:a.pageXOffset||f.scrollLeft||r.body.scrollLeft}}function n(){var n,l=t(),r=0;if(t.each(d,function(t,e){var i=e.data.selector,n=e.$element;l=l.add(i?n.find(i):n)}),n=l.length)for(o=o||e(),h=h||i();n>r;r++)if(t.contains(f,l[r])){var a,c,p,s=t(l[r]),u={height:s.height(),width:s.width()},g=s.offset(),v=s.data("inview");if(!h||!o)return;g.top+u.height>h.top&&g.top<h.top+o.height&&g.left+u.width>h.left&&g.left<h.left+o.width?(a=h.left>g.left?"right":h.left+o.width<g.left+u.width?"left":"both",c=h.top>g.top?"bottom":h.top+o.height<g.top+u.height?"top":"both",p=a+"-"+c,v&&v===p||s.data("inview",p).trigger("inview",[!0,a,c])):v&&s.data("inview",!1).trigger("inview",[!1])}}var o,h,l,d={},r=document,a=window,f=r.documentElement,c=t.expando;t.event.special.inview={add:function(e){d[e.guid+"-"+this[c]]={data:e,$element:t(this)},l||t.isEmptyObject(d)||(l=setInterval(n,250))},remove:function(e){try{delete d[e.guid+"-"+this[c]]}catch(i){}t.isEmptyObject(d)&&(clearInterval(l),l=null)}},t(a).bind("scroll resize scrollstop",function(){o=h=null}),!f.addEventListener&&f.attachEvent&&f.attachEvent("onfocusin",function(){h=null})}(jQuery);</script>';
                $html .= '<script type="text/javascript">var speed=4;function parallax(){var $slider=document.getElementById("bg-channel-image");var yPos=window.pageYOffset / speed;yPos=-yPos;var coords=\'50%\'+yPos+\'px\';$slider.style.backgroundPosition = coords;}window.addEventListener("scroll", function(){parallax();});</script>';
                break;
            case VHref::getKey("subscribers"):
            case VHref::getKey("affiliate"):
            case VHref::getKey("tokens"):
                $html .= $smarty->fetch("tpl_frontend/tpl_affiliatejs_min.tpl");
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                break;
            case VHref::getKey("search"):
                $html .= '<script type="text/javascript">var q = "' . $class_filter->clr_str($_GET['q']) . '";var current_url=base;var menu_section="' . (((int) $_GET['tf'] == 5 or (int) $_GET['tf'] == 7) ? VHref::getKey("files") : VHref::getKey("search")) . '"; var search_menu_section="' . VHref::getKey("search") . '";</script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/linkify/linkify.init.js"></script>';
                $html .= (isset($_GET['tf']) and (int) $_GET['tf'] == 6) ? self::swiperjs('promoted') : self::swiperjs('main');
                $html .= (isset($_GET['tf']) and (int) $_GET['tf'] == 6) ? null : '<script type="text/javascript">$(document).ready(function(){jQuery(document).on({click:function(e){e.preventDefault();h=$(this).attr("href");if($(".tabs nav ul a[href=\'"+h+"\']").parent().hasClass("tab-current"))return;$(".swiper-slide").removeClass("swiper-slide-current");$(this).parent().addClass("swiper-slide-current");$(".tabs nav ul a[href=\'"+h+"\']").parent().click()}}, ".swiper .swiper-slide a");});</script>';
                /*jquery.jscroll.min.js*/
                $html .= '<script type="text/javascript">!function(t){function e(){var e,i,n={height:a.innerHeight,width:a.innerWidth};return n.height||(e=r.compatMode,(e||!t.support.boxModel)&&(i="CSS1Compat"===e?f:r.body,n={height:i.clientHeight,width:i.clientWidth})),n}function i(){return{top:a.pageYOffset||f.scrollTop||r.body.scrollTop,left:a.pageXOffset||f.scrollLeft||r.body.scrollLeft}}function n(){var n,l=t(),r=0;if(t.each(d,function(t,e){var i=e.data.selector,n=e.$element;l=l.add(i?n.find(i):n)}),n=l.length)for(o=o||e(),h=h||i();n>r;r++)if(t.contains(f,l[r])){var a,c,p,s=t(l[r]),u={height:s.height(),width:s.width()},g=s.offset(),v=s.data("inview");if(!h||!o)return;g.top+u.height>h.top&&g.top<h.top+o.height&&g.left+u.width>h.left&&g.left<h.left+o.width?(a=h.left>g.left?"right":h.left+o.width<g.left+u.width?"left":"both",c=h.top>g.top?"bottom":h.top+o.height<g.top+u.height?"top":"both",p=a+"-"+c,v&&v===p||s.data("inview",p).trigger("inview",[!0,a,c])):v&&s.data("inview",!1).trigger("inview",[!1])}}var o,h,l,d={},r=document,a=window,f=r.documentElement,c=t.expando;t.event.special.inview={add:function(e){d[e.guid+"-"+this[c]]={data:e,$element:t(this)},l||t.isEmptyObject(d)||(l=setInterval(n,250))},remove:function(e){try{delete d[e.guid+"-"+this[c]]}catch(i){}t.isEmptyObject(d)&&(clearInterval(l),l=null)}},t(a).bind("scroll resize scrollstop",function(){o=h=null}),!f.addEventListener&&f.attachEvent&&f.attachEvent("onfocusin",function(){h=null})}(jQuery);</script>';
                break;
            case VHref::getKey("manage_channel"):
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/jquery.form.min.js"></script>';
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/cropper/cropper.min.js"></script>';
                $html .= '<script type="text/javascript">$(document).ready(function(){$(document).on("click", ".cr-popup", function() {crop_url = current_url + menu_section + "?s=channel-menu-entry3&do=edit-crop&t=" + $(this).attr("rel-photo").substr(0, 10);$.fancybox({ type: "ajax", minWidth: "80%", minHeight: "80%", margin: 20, href: crop_url });});$(document).on("click", ".gcr-popup", function() {crop_url = current_url + menu_section + "?s=channel-menu-entry3&do=edit-gcrop&t=" + $(this).attr("rel-photo").substr(0, 10);$.fancybox({ type: "ajax", minWidth: "80%", minHeight: "80%", margin: 20, href: crop_url });});$(document).on("click", ".del-popup", function() {crop_url = current_url + menu_section + "?s=channel-menu-entry3&do=delete-crop&t=" + $(this).attr("rel-photo").substr(0, 10);$.fancybox({ type: "ajax", minWidth: "80%", minHeight: "80%", margin: 20, href: crop_url });});});</script>';
                $html .= isset($_GET['r']) ? '<script type="text/javascript">(function () {[].slice.call(document.querySelectorAll(".tabs")).forEach(function (el) {new CBPFWTabs(el);});})();$(document).ready(function() {$(".tabs ul li#l2").click();});</script>' : null;
                $html .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/swiper/swiper-bundle.min.js"></script>';
                break;
            case VHref::getKey("signin"):
            case VHref::getKey("signup"):
            case VHref::getKey("x_recovery"):
            case VHref::getKey("service"):
            case VHref::getKey("renew"):
            case VHref::getKey("x_payment"):
                $html .= $smarty->fetch("tpl_frontend/tpl_signupjs_min.tpl");
                $html .= '<script type="text/javascript">$(document).ready(function(){$(".icheck-box input").each(function(){var self=$(this);self.iCheck({checkboxClass:"icheckbox_square-blue",radioClass:"iradio_square-blue",increaseArea:"20%"})});});</script>';

                break;
        }

        return $html;
    }
    public static function nrf($num)
    {
        if ($num > 1000) {
            $x               = round($num);
            $x_number_format = number_format($x);
            $x_array         = explode(',', $x_number_format);
            $x_parts         = array('K', 'M', 'B', 'T');
            $x_count_parts   = count($x_array) - 1;
            $x_display       = $x;
            $x_display       = $x_array[0] . ((int) $x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
            $x_display .= $x_parts[$x_count_parts - 1];

            return $x_display;
        }

        return $num;
    }
    public static function doThemeSwitch()
    {
        global $class_filter, $cfg;

        if ($_POST['c']) {
            $be           = (int) $_GET['be'];
            $t            = $class_filter->clr_str($_POST['c']);
            $s            = $be == 1 ? 'theme_name_be' : 'theme_name';
            $_SESSION[$s] = $t;

            if ($be == 0) {
                $_SESSION['USER_THEME'] = (strpos($t, 'dark') !== false ? 'dark' : 'light');
            }
        }
    }
    public static function themeSwitch()
    {
        global $language, $cfg, $backend_access_url;

        $dm       = 0;
        $_section = (strstr($_SERVER['REQUEST_URI'], $backend_access_url) == true) ? 'backend' : 'frontend';
        $s        = $_section == 'backend' ? 'theme_name_be' : 'theme_name';

        $tn = isset($_SESSION[$s]) ? $_SESSION[$s] : $cfg[$s];
        if (strpos($tn, 'dark') !== false) {
            $dm = 1;
        }

        $sel_on    = $dm == 1 ? 'selected' : null;
        $sel_off   = $dm == 0 ? 'selected' : null;
        $check_on  = $dm == 1 ? 'checked="checked"' : null;
        $check_off = $dm == 0 ? 'checked="checked"' : null;
        $sw_on     = $language['frontend.global.switchoff'];
        $sw_off    = $language['frontend.global.switchon'];

        $switch = VGenerate::entrySwitch('theme-switch', '', $sel_on, $sel_off, $sw_on, $sw_off, 'theme_switch', $check_on, $check_off);

        $html = '<style>.ts{float:right}.tsl .ts{float:left; margin-left:35px}.tsl{display:none}.theme-switch{margin-top:-3px;margin-right:5px}.tsl .theme-switch{margin-top:7px}.tsl .switch{margin-bottom:0}</style><div class="place-right"><div class="theme-switch">' . $switch . '</div><div class="clearfix"></div></div><script type="text/javascript">jQuery(document).on({click:function(){$("#theme-preloader").show();var be=$("body").hasClass("be")?1:0;t=$(this);c="' . str_replace('dark', '', $cfg['theme_name']) . '";cd="";if(t.is(":checked")){cd="dark";}else{}th=cd+c;$("html").attr("data-theme", th);if(be==0){$("#fe-color").attr("href","' . $cfg['main_url'] . '/f_scripts/fe/css/theme/"+cd+"theme.min.css");}$("#be-color").attr("href","' . $cfg['main_url'] . '/f_scripts/be/css/theme/"+cd+"theme_backend.min.css");$.post("' . $cfg['main_url'] . '?a=color&be="+be,{c:th},function(data){if(t.is(":checked")){$("body,.border-wrapper").addClass("dark");$("body").removeClass("scroll-light").addClass("scroll-dark");$("#dark-mode-state-text").text("' . $language['frontend.global.on.text'] . '");if(typeof $("#vs-chat").html()!="undefined")document.getElementById("vs-chat").contentWindow.postMessage({"viz":"th0","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");}else{$("body,.border-wrapper").removeClass("dark");$("body").removeClass("scroll-dark").addClass("scroll-light");$("#dark-mode-state-text").text("' . $language['frontend.global.off.text'] . '");if(typeof $("#vs-chat").html()!="undefined")document.getElementById("vs-chat").contentWindow.postMessage({"viz":"th1","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");}});setTimeout(()=>{$("#theme-preloader").fadeOut("fast",function(){});},300);}},"input[name=theme_switch_check]");</script>';

        return $html;
    }
    public static function socialMediaLinks()
    {
        global $cfg;

        $html = null;
        $sml  = unserialize($cfg['social_media_links']);

        for ($i = 1; $i <= 10; $i++) {
            if (is_array($sml) and isset($sml[$i]['title'])) {
                $html .= '<a href="' . $sml[$i]['url'] . '" target="_blank" title="' . $sml[$i]['title'] . '"><i class="' . $sml[$i]['icon'] . '"></i></a>';
            }
        }

        return $html;
    }
    public static function offlineSettings()
    {
        global $cfg, $class_database, $language;

        $pcfg = $class_database->getConfigurations('offline_mode_settings');
        $sml  = unserialize($pcfg['offline_mode_settings']);

        $input_tpl = '<div id="sm-#NR#">';
        $input_tpl .= '<div id="url-entry#NR#" class="sm-url-entry">';
        $input_tpl .= '<a href="javascript:;" onclick="$(this).parent().next().stop().slideToggle(200)">Image #NR#.</a> - ';
        $input_tpl .= '<label><a href="javascript:;" onclick="$(this).parent().parent().parent().next().stop().detach();$(this).parent().parent().parent().detach()">' . $language['frontend.global.delete.small'] . '</a></label>';
        $input_tpl .= '</div>';
        $input_tpl .= '<div id="url-entry-details#NR#" class="url-entry-details" rel-id="#NR#" style="display:none">';
        $input_tpl .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label style="margin-top:0">' . $language['backend.menu.entry2.sub1.sm.url'] . '</label>', 'left-float', 'sml[#NR#][url]', 'login-input', '#V2#');
        $input_tpl .= '</div>';
        $input_tpl .= '</div>';

        $input_code = '<script type="text/javascript">var ht="' . str_replace('"', "'", $input_tpl) . '"</script>';
        $input_code .= '<a href="javascript:;" class="place-right sml-add">' . $language['backend.menu.entry2.sub1.sm.add'] . '</a><div class="clearfix"></div>';
        $input_code .= '<div id="url-entry-details-list">';
        if (isset($sml[1]['url'])) {
            foreach ($sml as $i => $vals) {
                $l_url = is_array($sml) ? $sml[$i]['url'] : null;

                $input_code .= str_replace(array('#NR#', '#V2#'), array($i, $l_url), $input_tpl);
            }
        }
        $input_code .= '</div>';

        return $input_code;
    }
    public static function isImage($pathToFile)
    {
        if (!file_exists($pathToFile)) {
            return false;
        }

        if (false === exif_imagetype($pathToFile)) {
            return false;
        }

        return true;
    }
    /* icheck css */
    private static function icheckcss()
    {
        return '<style>.cbp-spmenu-push{width:100%;min-height:740px}.push-part{width:calc(100% - 60px)}.push-full{width:calc(100% - 300px)}.blue .accordion{border-top:1px solid #013d6c;border-right:1px solid #013d6c;border-left:1px solid #013d6c;font-size:16px;border-bottom:1px solid #282b30 !important}.blue .accordion,.blue .accordion li{margin:0;padding:0;border:0}.blue .accordion a{padding:0;background:#1f1f1f;text-decoration:none;display:block;color:#9398a2;border-bottom:1px solid #0c0d0f;border-top:1px solid #282b30;height:60px;overflow:hidden}.blue .accordion ul li a{padding-left:60px;height:30px;line-height:30px;font-size:14px;border:0}.blue .accordion ul li a.sub_menu::before{font-family:\'icomoonBe\';speak:none;font-style:normal;font-weight:normal;font-variant:normal;text-transform:none;line-height:1;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;content:"\e605";margin-right:5px;margin-left:-1px;font-size:10px}.blue .accordion a.dcjq-parent.active{color:white}.blue .accordion a:hover{background:#25272b;color:white}
.icheckbox_square-blue,.iradio_square-blue{display:inline-block;*display:inline;vertical-align:middle;margin:3px 5px 3px 0;padding:0;width:16px;height:16px;background:url(../blue/images/blue.png) no-repeat;border:0;cursor:pointer}.settings_content{margin-top:10px}.icheckbox_square-blue{background-position:0 0}.icheckbox_square-blue.hover{background-position:-18px 0}.icheckbox_square-blue.checked{background-position:-36px 0}.icheckbox_square-blue.disabled{background-position:-54px 0;cursor:default}.icheckbox_square-blue.checked.disabled{background-position:-72px 0}.iradio_square-blue{background-position:-90px 0;width:17px}.iradio_square-blue.hover{background-position:-108px 0}.iradio_square-blue.checked{background-position:-126px 0}.iradio_square-blue.disabled{background-position:-145px 0;cursor:default}.iradio_square-blue.checked.disabled{background-position:-163px 0}.icheckbox_line-blue,.iradio_line-blue{position:relative;display:block;margin:0;padding:5px 15px 5px 38px;font-size:13px;line-height:17px;color:#fff;background:#06a2cb;border:0;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;cursor:pointer}.icheckbox_line-blue .icheck_line-icon,.iradio_line-blue .icheck_line-icon{position:absolute;top:50%;left:13px;width:13px;height:11px;margin:-5px 0 0 0;padding:0;overflow:hidden;background:url(../blue/images/line.png) no-repeat;border:0}.icheckbox_line-blue.hover,.icheckbox_line-blue.checked.hover,.iradio_line-blue.hover{background:#17b3dc}.icheckbox_line-blue.checked,.iradio_line-blue.checked{background:#3e83b7}.icheckbox_line-blue.checked .icheck_line-icon,.iradio_line-blue.checked .icheck_line-icon{background-position:-15px 0}.icheckbox_line-blue.disabled,.iradio_line-blue.disabled{background:#add7f0;cursor:default}.icheckbox_line-blue.disabled .icheck_line-icon,.iradio_line-blue.disabled .icheck_line-icon{background-position:-30px 0}.icheckbox_line-blue.checked.disabled,.iradio_line-blue.checked.disabled{background:#add7f0}.icheckbox_line-blue.checked.disabled .icheck_line-icon,.iradio_line-blue.checked.disabled .icheck_line-icon{background-position:-45px 0}.icheck-box.ask .iradio_line-blue{margin-right:10px}.selector{margin:0;position:relative}*[id^=\'categ-entry-form\'] .selector{margin-bottom:10px}.selector i{color:#737373;height:30px;line-height:30px;position:absolute;right:0;text-align:center;top:0;width:30px;z-index:112}.selector::after{clear:both;content:".";display:block;height:0;overflow:hidden;visibility:hidden}.selector select{position:absolute;left:-1000em}.selector label,.selector .select-box,.selector button{float:left;margin-right:29px}.selector label{padding-top:.2em}.selector .select-box{position:relative;width:100%}.selector .select-box .trigger{background:none repeat scroll 0 0 #fff;border:medium none;color:#000;cursor:pointer;font-size:14px;font-weight:normal;height:40px;line-height:30px;margin:0;padding:5px 9px}#trigger-ipp_select{line-height:20px;height:initial}.selector .select-box .choices{background:none repeat scroll 0 0 #fff;border-bottom-left-radius:3px;border-bottom-right-radius:3px;color:#505050;display:none;left:0;list-style:outside none none;margin:0;padding:0;position:absolute;top:28px;width:100%;max-height:295px;overflow-y:scroll}.selector .select-box .choices li{cursor:pointer;display:block;margin-bottom:.3em;padding:5px 5px 5px 15px;font-size:13px;font-weight:normal;color:#505050}.selector .select-box .choices li:hover{background:none repeat scroll 0 0 #f1f1f1}.choices{position:absolute;z-index:200}.selector .icon-times{color:#505050;content:"\f0a3";font-size:10px}.selector .iconBe-chevron-down{font-size:10px;border-left:1px solid #f1f1f1}.entry-form-class .select-box .trigger::before,#ct-set-form .select-box .trigger::before{font-family:"icomoonBe";content:"\f0a3";position:absolute;right:8px;padding-left:7px;border-left:1px solid #e0e0e0}#paging-top .select-box .trigger::before{font-family:"icomoonBe";content:"\f0a3";position:absolute;right:8px;padding-left:7px;border-left:1px solid #e0e0e0}.entry-form-class .selector .select-box .trigger,#ct-set-form .selector .select-box .trigger{background:none repeat scroll 0 0 #f5f5f5;box-shadow:0 2px 3px rgba(0,0,0,0.1) inset}.entry-form-class .selector .select-box .choices,#ct-set-form .selector .select-box .choices{background:none repeat scroll 0 0 #f5f5f5;box-shadow:0 2px -1px rgba(0,0,0,0.1) inset}.selector.fe .select-box .choices{position:relative;top:auto}</style>';
    }
    /* icheckblue css */
    private static function icheckbluecss()
    {
        return '<style>.icheckbox_square-blue,.iradio_square-blue{display:inline-block;*display:inline;vertical-align:middle;margin:3px 5px 3px 0;padding:0;width:16px;height:16px;background:url(../blue/images/blue.png) no-repeat;border:0;cursor:pointer}.settings_content{margin-top:10px}.icheckbox_square-blue{background-position:0 0}.icheckbox_square-blue.hover{background-position:-18px 0}.icheckbox_square-blue.checked{background-position:-36px 0}.icheckbox_square-blue.disabled{background-position:-54px 0;cursor:default}.icheckbox_square-blue.checked.disabled{background-position:-72px 0}.iradio_square-blue{background-position:-90px 0;width:17px}.iradio_square-blue.hover{background-position:-108px 0}.iradio_square-blue.checked{background-position:-126px 0}.iradio_square-blue.disabled{background-position:-145px 0;cursor:default}.iradio_square-blue.checked.disabled{background-position:-163px 0}.icheckbox_line-blue,.iradio_line-blue{position:relative;display:block;margin:0;padding:5px 15px 5px 38px;font-size:13px;line-height:17px;color:#fff;background:#06a2cb;border:0;-webkit-border-radius:3px;-moz-border-radius:3px;border-radius:3px;cursor:pointer}.icheckbox_line-blue .icheck_line-icon,.iradio_line-blue .icheck_line-icon{position:absolute;top:50%;left:13px;width:13px;height:11px;margin:-5px 0 0 0;padding:0;overflow:hidden;background:url(../blue/images/line.png) no-repeat;border:0}.icheckbox_line-blue.hover,.icheckbox_line-blue.checked.hover,.iradio_line-blue.hover{background:#17b3dc}.icheckbox_line-blue.checked,.iradio_line-blue.checked{background:#3e83b7}.icheckbox_line-blue.checked .icheck_line-icon,.iradio_line-blue.checked .icheck_line-icon{background-position:-15px 0}.icheckbox_line-blue.disabled,.iradio_line-blue.disabled{background:#add7f0;cursor:default}.icheckbox_line-blue.disabled .icheck_line-icon,.iradio_line-blue.disabled .icheck_line-icon{background-position:-30px 0}.icheckbox_line-blue.checked.disabled,.iradio_line-blue.checked.disabled{background:#add7f0}.icheckbox_line-blue.checked.disabled .icheck_line-icon,.iradio_line-blue.checked.disabled .icheck_line-icon{background-position:-45px 0}.icheck-box.ask .iradio_line-blue{margin-right:10px}.selector{margin:0;position:relative}*[id^=\'categ-entry-form\'] .selector{margin-bottom:10px}.selector i{color:#737373;height:30px;line-height:30px;position:absolute;right:0;text-align:center;top:0;width:30px;z-index:112}.selector::after{clear:both;content:".";display:block;height:0;overflow:hidden;visibility:hidden}.selector select{position:absolute;left:-1000em}.selector label,.selector .select-box,.selector button{float:left;margin-right:29px}.selector label{padding-top:.2em}.selector .select-box{position:relative;width:100%}.selector .select-box .trigger{background:none repeat scroll 0 0 #fff;border:medium none;color:#000;cursor:pointer;font-size:14px;font-weight:normal;height:45px;line-height:35px;margin:0;padding:5px 9px}#trigger-ipp_select{line-height:20px;height:initial}.selector .select-box .choices{background:none repeat scroll 0 0 #fff;border-bottom-left-radius:3px;border-bottom-right-radius:3px;color:#505050;display:none;left:0;list-style:outside none none;margin:0;padding:0;position:absolute;top:28px;width:100%;max-height:295px;overflow-y:scroll}.selector .select-box .choices li{cursor:pointer;display:block;margin-bottom:.3em;padding:5px 5px 5px 15px;font-size:13px;font-weight:normal;color:#505050}.selector .select-box .choices li:hover{background:none repeat scroll 0 0 #f1f1f1}.choices{position:absolute;z-index:200}.selector .icon-times{color:#505050;content:"\f0a3";font-size:10px}.selector .iconBe-chevron-down{font-size:10px;border-left:1px solid #f1f1f1}.entry-form-class .select-box .trigger::before,#ct-set-form .select-box .trigger::before{font-family:"icomoonBe";content:"\f0a3";position:absolute;right:8px;padding-left:7px;border-left:1px solid #e0e0e0}#paging-top .select-box .trigger::before{font-family:"icomoonBe";content:"\f0a3";position:absolute;right:8px;padding-left:7px;border-left:1px solid #e0e0e0}.entry-form-class .selector .select-box .trigger,#ct-set-form .selector .select-box .trigger{background:none repeat scroll 0 0 #f5f5f5;box-shadow:0 2px 3px rgba(0,0,0,0.1) inset}.entry-form-class .selector .select-box .choices,#ct-set-form .selector .select-box .choices{background:none repeat scroll 0 0 #f5f5f5;box-shadow:0 2px -1px rgba(0,0,0,0.1) inset}.selector.fe .select-box .choices{position:relative;top:auto}</style>';
    }
    private static function logincss()
    {
        return '<style>.login-page-old{width:480px;position:relative;margin:0 auto;left:30%;margin-left:-240px;padding:20px 0}.login-margins{margin:20px}.promo-list li{width:60%;margin:0 auto;font-size:14px;color:#777;padding:5px 0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:middle}.promo-list li i{margin-right:5px;color:#444}.login-page .user-form article{text-align:left}#password-recovery-form article h3{text-align:left;display:block}.login-page .tabs .content-title{padding-left:0}.login-page .content-title [class^="icon-"]{margin-right:0}.form-buttons{margin-bottom:0;margin-top:15px}.login-page .outer-border-wrapper{border:1px solid rgba(40,44,42,0.1);background:white;padding:0 20px;border-top:0}.login-page.payment-page{padding-top:0}.user-form{line-height:initial}.user-form .row{display:block;width:100%}.user-form .input-signin{width:100%}.login-page .tabs-style-topline nav li.tab-current a{background:white;box-shadow:inset 0 3px 0 #06a2cb;color:#06a2cb}.user-form .label-signin,.user-form span.input-signin.top-padding2{color:#8f8f8f;font-size:14px;font-weight:300;margin-bottom:0;margin-top:0;line-height:30px}.user-form .line{margin-bottom:10px}.user-form input[type="text"],.user-form input[type="email"],.user-form input[type="password"]{background:none repeat scroll 0 0 #f5f5f5;border:medium none;box-shadow:0 2px 3px rgba(0,0,0,0.1) inset;clear:both;font-size:.75rem;margin-bottom:5px;padding:15px;width:100%}.user-form input[type="text"]:focus,.user-form input[type="email"]:focus,.user-form input[type="password"]:focus,.user-form textarea:focus{background:none repeat scroll 0 0 #fff;box-shadow:0 0 0 2px #06a2cb,0 2px 3px rgba(0,0,0,0.2) inset,0 5px 5px rgba(0,0,0,0.15);outline:medium none}span.input-signin button.search-button.form-button{background-color:#06a2cb;box-shadow:none;color:white;cursor:pointer;font-family:"Roboto",Arial,Helvetica,sans-serif;font-size:13px;font-weight:500;margin-left:4px;margin-right:10px;margin-bottom:20px;padding:10px;transition:all .3s ease 0s;width:40%;border:0}span.input-signin button.search-button.form-button.fb-login-button{background-color:#3b5998;box-shadow:none}span.input-signin button.search-button.form-button.fb-login-button:hover{background-color:#4669B4;color:#fff}span.input-signin button.search-button.form-button.fb-login-button:hover::before{color:#fff;margin-right:10px;border-right:1px solid #fff}span.input-signin button.search-button.form-button.gp-login-button{background-color:#dd4b39;box-shadow:none}span.input-signin button.search-button.form-button.gp-login-button:hover{background-color:#E3695A;color:#fff}span.input-signin button.search-button.form-button.gp-login-button:hover::before{color:#fff;margin-right:10px;border-right:1px solid #fff}span.input-signin button.search-button.form-button::before{font-family:"icomoon";content:"\e605";color:white;margin-right:10px;border-right:1px solid white;padding-right:10px;transition:all .3s ease 0s}span.input-signin button.search-button.form-button.auth-check-button::before{font-family:"icomoon";content:"\e99b"}span.input-signin button.search-button.form-button.fb-login-button::before{font-family:"icomoon";content:"\e648"}span.input-signin button.search-button.form-button.gp-login-button::before{font-family:"icomoon";content:"\ea80"}span.input-signin button.search-button.form-button.apply-button::before{font-family:"icomoon";content:"\f00c"}span.input-signin button.search-button.form-button.continue-button::before{font-family:"icomoonBe";content:"\f03e"}span.input-signin button.search-button.form-button:hover{background-color:#92cefb;color:#fff}span.input-signin button.search-button.form-button:hover::before{color:#fff;margin-right:10px;border-right:1px solid #fff}#r-image,#l-image{cursor:pointer}#membership_info .ul-disc-list{margin-top:10px}#membership_info .ul-disc-list li{font-size:13px}#membership_info .ul-disc-list li::before{font-family:"icomoon";content:"\f00c";margin-right:5px}.ul-disc-list .bold{font-weight:bold}.pk-descr{margin-top:10px;font-size:14px}.dark .pk-text{color:#fff}.pk-text{color:#505050}.blued{color:#06a2cb}#auth-register-form h4{font-weight:bold;color:#06a2cb;margin-bottom:20px}.auth-username-check-response{padding:0 10px 10px 0}.err-red{color:red}.conf-green{color:green}.hr .inner{background:#fff;border-radius:100px;display:inline-block;width:auto;padding:9px 10px;font-size:15px;font-size:16px;font-style:italic;line-height:0}.hr{box-shadow:0 1px 0 #fff,transparent 0 0 0;border-bottom:1px solid #ddd;margin:0 auto 40px !important;position:relative;height:8px;text-align:center;line-height:initial}.tpl_signin .hr{padding-top:20px;height:12px}#reset-password-button{max-width:150px;margin-top:20px}</style>';
    }
    /* pdfjs styles */
    private static function pdfjs_css()
    {
        // https://cdn.jsdelivr.net/gh/dealfonso/pdfjs-viewer@1.1/pdfjs-viewer.min.css
        return '<style>.pdfjs-viewer{overflow:auto;border:1px solid #aaa;background:#ccc}.pdfjs-viewer.horizontal-scroll{display:flex}.pdfjs-viewer.horizontal-scroll .pdfpage{margin-left:1em;margin-top:.25em!important;margin-bottom:.25em!important;display:block}.pdfpage{position:relative;margin-bottom:1em;margin-top:1em;margin-left:auto;margin-right:auto;box-shadow:0 4px 8px 0 rgba(0,0,0,.1),0 6px 20px 0 rgba(0,0,0,.09)}.pdfpage canvas{position:absolute;left:0;top:0;height:100%;width:100%}.pdfpage.placeholder{display:flex;margin-bottom:0!important;margin-top:0!important;height:100%;width:100%}.pdfpage .content-wrapper{margin:0!important;padding:0!important;display:flex!important}.pdfpage .content-wrapper .loader{border:2px solid #f3f3f3;border-top:3px solid #3498db;border-radius:50%;width:24px;height:24px;animation:spin 1s linear infinite;margin:auto}@keyframes spin{0%{transform:rotate(0)}100%{transform:rotate(360deg)}}.pdfjs-toolbar{width:100%;height:32px;background:#ddd;z-index:100;vertical-align:middle;display:flex;margin:0;padding:0}.pdfjs-toolbar *{margin:auto 0}.pdfjs-toolbar span{margin-right:.5em;margin-left:.5em;width:4em!important;font-size:12px}.pdfjs-toolbar a.button,.pdfjs-toolbar button,.pdfjs-toolbar label.button{min-width:26px;height:28px;border:none;padding:2px 4px 0;margin:auto 1px;border-radius:2px;line-height:12px;font-size:14px;background-color:#ddd;cursor:pointer}.pdfjs-toolbar button i,.pdfjs-toolbar label.button i{font-size:26px;padding:0;margin:0}.pdfjs-toolbar a.button:hover,.pdfjs-toolbar button:hover,.pdfjs-toolbar label.button:hover{background-color:#ccc}button.pushed{background-color:#aaa!important}.pdfjs-toolbar a.button{color:inherit}.pdfjs-toolbar .divider{flex:1}.pdfjs-toolbar .v-sep{width:0;height:20px;border-left:1px solid #bbb}.pdfjs-toolbar .h-sep{width:100%;height:0;border-top:1px solid #bbb;margin:.25em 0}.pdfjs-toolbar .dropdown.dropdown-right,.pdfjs-toolbar .dropdown.right{float:right}.pdfjs-toolbar .dropdown.dropdown-right .dropdown-content,.pdfjs-toolbar .dropdown.right .dropdown-content{right:0;left:auto}.pdfjs-toolbar .dropdown-value{background-color:#ccc;padding:0 4px 2;cursor:pointer}.pdfjs-toolbar .dropdown-value i{width:auto;font-size:12px}.pdfjs-toolbar .dropdown-content{display:none;position:absolute;margin-top:0;background-color:#eee;min-width:10em;z-index:1;font-size:12px;box-shadow:0 4px 8px 0 rgba(0,0,0,.1),0 6px 20px 0 rgba(0,0,0,.09)}.pdfjs-toolbar .dropdown-content a{all:initial;font:inherit;color:#000;padding:6px 8px;text-decoration:none;display:flex;cursor:pointer}.pdfjs-toolbar .dropdown-content i{font-size:16px;padding-right:.5em}.pdfjs-toolbar .dropdown-content a:hover{background-color:#ddd}.dropdown .dropdown-content:hover,.pdfjs-toolbar .dropdown:hover .dropdown-content{display:block}</style>';
    }
}
function giphyreplace($string)
{
    require_once 'class_giphy/autoload.php';

    $api_key       = "";
    $allowed_hosts = array('giphy.com', 'www.giphy.com');

    if (strpos($string, 'giphy.com') !== false) {
        $gif_ids = array();
        $url     = array();
        $img     = array();

        $ostring = $string;
        $x       = explode(" ", $string);
        foreach ($x as $text) {
            $u = parse_url($text);

            if (strpos($text, 'giphy.com') !== false) {
                $url[] = $text;

                if (strpos($text, '-') !== false) {
                    $p         = explode("-", $text);
                    $gif_ids[] = $p[count($p) - 1];
                } else {
                    $p         = explode("/", $text);
                    $gif_ids[] = $p[count($p) - 1];
                }
            }
        }

        if (isset($gif_ids[0])) {
            $api_instance = new GPH\Api\DefaultApi();

            foreach ($gif_ids as $gif_id) {
                try {
                    $_k      = 'fixed_height';
                    $result  = $api_instance->gifsGifIdGet($api_key, $gif_id);
                    $gif_url = $result["data"]["images"][$_k]["url"];
                    $img[]   = '<img src="' . $gif_url . '" width="' . $result["data"]["images"][$_k]["width"] . '" height="' . $result["data"]["images"][$_k]["height"] . '" style="width:' . $result["data"]["images"][$_k]["width"] . 'px !important;height:' . $result["data"]["images"][$_k]["height"] . 'px !important;display:block">';
                } catch (Exception $e) {
                    echo 'Exception when calling DefaultApi->gifsGifIdGet: ', $e->getMessage(), PHP_EOL;
                }
            }
        }

        return str_replace($url, $img, $string);
    }

    return $string;
}
