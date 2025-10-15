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

class VHref
{
    /* channel url format */
    public static function channelURL(array $data)
    {
        global $cfg;

        return (!isset($data['old']) ? $cfg['main_url'] . '/@' . $data['username'] : $cfg['main_url'] . '/' . VHref::getKey("channel") . '/' . $data['user_key'] . '/' . $data['username']);
    }
    /* url keys */
    public static function getKey($key)
    {
        require 'f_core/config.href.php';

        return $href[$key];
    }
    public static function isAnyMobile()
    {
        $detect = new MobileDetect;

        return $detect->isMobile();
    }
    public static function isMobile($get_tablet = false)
    {
        $detect = new MobileDetect;

        if (!$get_tablet and $detect->isMobile()) {
            return true;
        }
        if ($get_tablet and $detect->isMobile() && !$detect->isTablet()) {
            return true;
        }
        return false;
    }
    /* get current section for library */
    public static function currentSection()
    {
        global $cfg, $class_filter, $href, $class_database;

        $cache = false;
        $uri   = $class_filter->clr_str($_SERVER['REQUEST_URI']);
        $a     = explode("/", $uri);
        $t     = count($a);

        switch ($a[$t - 1]) {
            case $href['uploads']:
            case "":
                $_i = 'file-menu-entry1';
                break;

            case $href['favorites']:
                $_i = 'file-menu-entry2';
                break;

            case $href['liked']:
                $_i = 'file-menu-entry3';
                break;

            case $href['history']:
                $_i = 'file-menu-entry4';
                break;

            case $href['watchlist']:
                $_i = 'file-menu-entry5';
                break;

            case $href['playlists']:
                $_i = 'file-menu-entry6';
                break;

            case $href['comments']:
                $_i = 'file-menu-entry7';
                break;

            case $href['responses']:
                $_i = 'file-menu-entry8';
                break;

            default:
                $_i = false;
                break;
        }
        if ($a[$t - 2] == $href['subscriptions'] or $a[$t - 2] == $href['following']) {
            $usr_key = $class_filter->clr_str($a[$t - 1]);
            $usr_id  = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key, ($cache ? 3600 * 3 : false));
            $_i      = $a[$t - 2] == $href['subscriptions'] ? 'subs-menu-entry' . $usr_id : 'fsub-menu-entry' . $usr_id;
        }

        $_s = isset($_GET['s']) ? $class_filter->clr_str($_GET['s']) : $_i;

        return $_s;
    }
    /* guest permissions check */
    public static function guestPermissions($for, $next)
    {
        global $class_database, $cfg;

        if ((int) $_SESSION['USER_ID'] > 0) {
            return true;
        }

        $cfg[] = $class_database->getConfigurations($for);

        if ($cfg[$for] == 0) {
            $next = $next == 'live' ? 'broadcasts' : $next;
            header("Location: " . $cfg['main_url'] . "/" . VHref::getkey("signin") . "?next=" . $next);
        }
    }
    /* page titles */
    public static function getPageMeta($for)
    {
        global $cfg, $section, $href, $class_filter, $db, $language, $class_database;

        $db_cache = false;
        $section  = $section == '' ? self::getKey("index") : $section;

        switch ($section) {
            case $href['watch']:
            case $href['files_edit']:
            case $href['channel']:
            case $href['see_comments']:
            case $href['respond']:
                if ($section == $href['watch'] or $section == $href['see_comments'] or $section == $href['respond'] or $section == $href['files_edit']) {
                    $t     = isset($_GET['l']) ? 'live' : (isset($_GET['v']) ? 'video' : (isset($_GET['i']) ? 'image' : (isset($_GET['a']) ? 'audio' : (isset($_GET['d']) ? 'doc' : (isset($_GET['b']) != '' ? 'blog' : (isset($_GET['s']) ? 'short' : null))))));
                    $k     = $class_filter->clr_str($_GET[$t[0]]);
                    $sql   = sprintf("SELECT `file_title`, `file_tags`, SUBSTRING(`file_description`, 1, 160) as `file_description` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $t, $k);
                    $res   = $db_cache ? $db->CacheExecute($cfg['cache_view_current'], $sql) : $db->execute($sql);
                    $title = $res->fields['file_title'];
                    $descr = $res->fields['file_description'];
                    $tags  = $res->fields['file_tags'];
                } else {
                    $adr = $class_filter->clr_str($_SERVER['REQUEST_URI']);
                    if (strpos($adr, $href['channels']) !== false) {
                        $param = array_pop(explode($href['channel'], str_replace($href['channels'], '---', $adr)));
                    } else {
                        $param = array_pop(explode($href['channel'], $adr));
                    }
                    $e    = explode('/', $param);
                    $e    = array_values(array_filter(explode('/', trim($adr))));
                    $ukey = $class_filter->clr_str(strstr($e[0], '?', true));
                    $ukey = explode('?', $e[0]);
                    if ($ukey[0][0] == '@') {
                        $name = $class_filter->clr_str(str_replace('@', '', $ukey[0]));
                        $sql  = sprintf("SELECT A.`ch_title`, A.`ch_descr`, A.`ch_tags`, A.`usr_user` FROM `db_accountuser` A WHERE A.`usr_user`='%s' LIMIT 1;", str_replace('@', '', preg_replace('/[^a-zA-Z0-9_.\-]/', '', $name)));
                    }
                    $res   = $db_cache ? $db->CacheExecute(3600, $sql) : $db->execute($sql);
                    $user  = $res->fields['usr_user'];
                    $title = $res->fields['ch_title'];
                    $descr = $res->fields['ch_descr'];
                    $tags  = $res->fields['ch_tags'];
                }

                switch ($for) {
                    case "title":
                        return $meta_title = (($section == $href['watch'] or $section == $href['see_comments'] or $section == $href['respond']) ? ($section == $href['see_comments'] ? $language['view.files.comm.page.all'] : ($section == $href['respond'] ? $language['respond.text.title'] : null)) . $res->fields['file_title'] : ($title != '' ? $title : $user)) . ' - ' . $cfg['head_title'];
                        break;
                    case "description":
                        return $meta_descr = $descr != '' ? VUserinfo::truncateString(trim(preg_replace('/\s+/', ' ', $descr)), 161) : VUserinfo::truncateString(trim(preg_replace('/\s+/', ' ', $cfg['metaname_description'])), 161);
                        break;
                    case "tags":
                        $ft = [];
                        $xp = explode(" ", $tags);
                        foreach ($xp as $tag) {
                            if (strlen($tag) > 3 and !in_array($tag, $ft)) {
                                $ft[] = $tag;
                            }
                        }
                        return $meta_tags = $tags != '' ? implode(', ', $ft) : $cfg['metaname_keywords'];
                        break;
                }
                break;
            case $href['playlist']:
                $sep = ' | ';
                $t   = isset($_GET['l']) ? 'live' : (isset($_GET['v']) ? 'video' : (isset($_GET['i']) ? 'image' : (isset($_GET['a']) ? 'audio' : (isset($_GET['d']) ? 'doc' : (isset($_GET['b']) != '' ? 'blog' : (isset($_GET['s']) ? 'short' : null))))));

                $k     = $class_filter->clr_str($_GET[$t[0]]);
                $sql   = sprintf("SELECT `pl_name`, SUBSTRING(`pl_descr`, 1, 160) as `pl_descr`, `pl_tags` FROM `db_%splaylists` WHERE `pl_key`='%s' LIMIT 1;", $t, $k);
                $res   = $db_cache ? $db->CacheExecute($cfg['cache_playlist_details_meta'], $sql) : $db->execute($sql);
                $title = $res->fields['pl_name'];
                $descr = $res->fields['pl_descr'];
                $tags  = $res->fields['pl_tags'];

                switch ($for) {
                    case "title":
                        return $meta_title = $title . ' - ' . $cfg['head_title'];
                        break;
                    case "description":
                        return $meta_descr = $descr != '' ? $descr : $cfg['metaname_description'];
                        break;
                    case "tags":
                        return $meta_tags = $tags != '' ? str_replace(' ', ', ', $tags) : $cfg['metaname_keywords'];
                        break;
                }
                break;
            default:
                $sep = ' - ';
                switch ($for) {
                    case "title":
                        $_title = $sep . $cfg['head_title'];

                        switch ($section) {
                            case $href['index']:
                                $meta_title .= $cfg['head_title'] . ' - ' . $language['frontend.global.tag'];
                                break;
                            case $href['browse']:
                                $t = VTemplate::browseType();
                                $meta_title .= $language["frontend.global." . $t[0] . ".p.c"] . $_title;
                                break;
                            case $href['upload']:
                            case $href['import']:
                                $meta_title .= $language['frontend.global.upload'] . $_title;
                                break;
                            case $href['blogs']:
                                $meta_title .= $language['frontend.global.blogs'] . $_title;
                                break;
                            case $href['playlists']:
                                $meta_title .= $language['frontend.global.playlists'] . $_title;
                                break;
                            case $href['channels']:
                                $meta_title .= $language['frontend.global.channels'] . $_title;
                                break;
                            case $href['account']:
                                $meta_title .= $language['account.entry.my.account'] . $_title;
                                break;
                            case $href['subscribers']:
                                $meta_title .= $language['subnav.entry.subpanel'] . $_title;
                                break;
                            case $href['following']:
                                $meta_title .= $language['files.menu.follows'] . $_title;
                                break;
                            case $href['affiliate']:
                                $meta_title .= $language['account.entry.act.panel'] . $_title;
                                break;
                            case $href['tokens']:
                                $meta_title .= $language['subnav.entry.tokenpanel'] . $_title;
                                break;
                            case $href['manage_channel']:
                                $meta_title .= $language['subnav.entry.channel.my'] . $_title;
                                break;
                            case $href['files']:
                                $meta_title .= $language['subnav.entry.files.my'] . $_title;
                                break;
                            case $href['contacts']:
                            case $href['messages']:
                                $meta_title .= $language['subnav.entry.contacts.messages'] . $_title;
                                break;
                            case $href['subscriptions']:
                                $meta_title .= $language['subnav.entry.sub'] . $_title;
                                break;
                            case $href['signin']:
                                $meta_title .= $language['frontend.global.signin'] . $_title;
                                break;
                            case $href['signup']:
                                $meta_title .= $language['frontend.global.signup'] . $_title;
                                break;
                            case $href['service']:
                                $meta_title .= $language['frontend.global.recovery'] . $_title;
                                break;
                            case $href['renew']:
                                $meta_title .= $language['frontend.pkinfo.renew'] . $_title;
                                break;
                            case $href['search']:
                                $meta_title .= $language['frontend.global.searchtext'] . $_title;
                                break;
                            case $href['confirm_email']:
                                $meta_title .= $language['frontend.global.emailverify'] . $_title;
                                break;
                            case $href['page']:
                                switch ($_GET['t']) {
                                    case "page-live":$t = $language['footer.menu.item11'];
                                        break;
                                    case "page-affiliate":
                                        $cfg[] = $class_database->getConfigurations('affiliate_tracking_id,affiliate_view_id,affiliate_maps_api_key,affiliate_payout_share,affiliate_payout_currency,affiliate_payout_units,affiliate_payout_figure,affiliate_module,affiliate_requirements_min,affiliate_requirements_type,paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email,user_subscriptions,channel_views,affiliate_geo_maps');
                                        $t     = $language['footer.menu.item12'];
                                        break;
                                    case "page-partner":
                                        $cfg[] = $class_database->getConfigurations('sub_shared_revenue,subscription_payout_currency,channel_views,sub_threshold,partner_requirements_min,partner_requirements_type');
                                        $t     = $language['footer.menu.item13'];
                                        break;
                                    case "page-help":$t = $language['footer.menu.item1'];
                                        break;
                                    case "page-terms":$t = $language['footer.menu.item6'];
                                        break;
                                    case "page-privacy":$t = $language['footer.menu.item7'];
                                        break;
                                    case "page-dmca":$t = $language['footer.menu.item14'];
                                        break;
                                    case "page-copyright":
                                        global $smarty;
                                        $smarty->assign('c_year', date("Y"));
                                        $t = $language['footer.menu.item3'];
                                        break;
                                    default:$t = $language['footer.menu.item1'];
                                        break;
                                }
                                $meta_title .= $t . $_title;
                                break;
                            default:break;
                        }
                        return $meta_title;
                        break;
                    case "description":
                        return $meta_descr = $cfg['metaname_description'];
                        break;
                    case "tags":
                        return $meta_tags = $cfg['metaname_keywords'];
                        break;
                }
                break;
        }
    }
}
