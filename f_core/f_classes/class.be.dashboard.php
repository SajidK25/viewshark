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

class VbeDashboard
{
    private static $cfg;

    private static $db;
    private static $db_cache;
    private static $dbc;
    private static $filter;
    private static $language;
    private static $smarty;

    public function __construct()
    {
        global $cfg, $class_filter, $class_database, $db, $language, $smarty;

        self::$cfg      = $cfg;
        self::$db       = $db;
        self::$dbc      = $class_database;
        self::$filter   = $class_filter;
        self::$language = $language;
        self::$smarty   = $smarty;
        self::$db_cache = false;
    }
    /* update unseen notifications to last 100 */
    public function updateNotifications()
    {
        $last = 100;
        $sql  = sprintf("UPDATE `db_notifications` SET `seen`='1' WHERE `id` NOT IN (SELECT `id` FROM (SELECT `id` FROM `db_notifications` ORDER BY `id` DESC LIMIT %s) AS subquery);", $last);

        self::$db->execute($sql);
    }
    /* get counts by week */
    private function getWeekStats($type, $interval = 'this')
    {
        if ($type === 'user') {
            $fn    = 'usr_joindate';
            $q1    = "B.`usr_joindate`, B.`usr_id`";
            $usr_q = $usr_key != '' ? sprintf("AND B.`usr_key`='%s'", $usr_key) : null;
            $sql   = sprintf("SELECT %s FROM `db_accountuser` B WHERE YEARweek(STR_TO_DATE(B.`usr_joindate`, '%s')) = YEARweek(CURRENT_DATE%s) %s;", $q1, '%Y-%m-%d', ($interval == 'last' ? " - INTERVAL 7 DAY" : null), $usr_q);
        } elseif ($type === 'earnings') {
            $fn    = 'subscribe_time';
            $q1    = "A.`subscribe_time`, B.`usr_id`, C.`pk_priceunit`, SUM(A.`pk_paid_total`) AS `total`";
            $usr_q = $usr_key != '' ? sprintf("AND B.`usr_key`='%s'", $usr_key) : null;
            $sql   = sprintf("SELECT %s FROM `db_packusers` A, `db_accountuser` B, `db_packtypes` C WHERE YEAR(STR_TO_DATE(A.`subscribe_time`, '%s')) = YEAR(CURRENT_DATE%s) AND A.`usr_id`=B.`usr_id` AND A.`pk_id`=C.`pk_id` %s;", $q1, '%Y-%m-%d', ($interval == 'last' ? " - INTERVAL 1 YEAR" : null), $usr_q);
        } else {
            $fn    = 'upload_date';
            $q1    = "A.`upload_date`, B.`usr_id`";
            $usr_q = $usr_key != '' ? sprintf("AND B.`usr_key`='%s'", $usr_key) : null;
            $sql   = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE YEARweek(STR_TO_DATE(A.`upload_date`, '%s')) = YEARweek(CURRENT_DATE%s) AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, '%Y-%m-%d', ($interval == 'last' ? " - INTERVAL 7 DAY" : null), $usr_q);

        }
        $rs = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_weekstats'], $sql) : self::$db->execute($sql);

        $t    = array();
        $fi   = array();
        $last = array();

        if ($rs->fields['usr_id']) {
            $sum = $type === 'earnings' ? $rs->fields['total'] : 0;

            while (!$rs->EOF) {
                $sub = $type === 'earnings' ? -12 : -9;
                $str = substr($rs->fields[$fn], 0, $sub);

                $t[] = $str != '' ? $str : '';

                $rs->MoveNext();
            }
        }

        $t = array_count_values($t);

        foreach ($t as $key => $val) {
            $fi[strtotime($key)] = $type === 'earnings' ? $sum : $val;
        }

        ksort($fi);

        foreach ($fi as $key => $val) {
            $date        = $type === 'earnings' ? date("Y-m", $key) : date("Y-m-d", $key);
            $last[$date] = $val;
        }

        if ($type === 'earnings') {
            $m = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');

            for ($i = 0; $i < 12; $i++) {
                $dates[] = $interval === 'this' ? date('Y-m', strtotime($m[$i])) : date('Y-m', strtotime("$m[$i] " . (date("Y") - 1)));
            }
        } else {
            $inputDate      = date('Y-m-d');
            $inputDateArray = explode('-', $inputDate);
            $dayNr          = date('N', strtotime($inputDate));
            $sunday         = ($dayNr == 7) ? true : false;
            $call           = $sunday ? 'this' : 'last';

            for ($i = 0; $i < 7; $i++) {
                $dates[] = $interval === 'this' ? date('Y-m-d', strtotime($call . ' sunday + ' . $i . ' day')) : date('Y-m-d', strtotime($call . ' sunday - ' . ($i + 1) . ' day'));
            }

        }

        foreach ($dates as $key => $date) {
            $final_js[$key] = !isset($last[$date]) ? 0 : $last[$date];
        }

        if ($interval == 'last' and $type !== 'earnings') {
            krsort($final_js);
        }
        if ($type === 'earnings') {
            //print_r($fi);
        }
        return $final_js;
    }
    /* get file count stats from database */
    private function getFileCounts($type)
    {
        $total = array();

        $usr_key = self::$filter->clr_str(substr($_GET['u'], 1));
        $usr_q   = $usr_key != '' ? sprintf("AND B.`usr_key`='%s'", $usr_key) : null;

        $q1 = "A.`usr_id`, B.`usr_id`, COUNT(*) AS `total`";

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`active`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`active`='0' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`approved`='0' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_flag`>'0' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`is_featured`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`privacy`='public' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`privacy`='private' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`privacy`='personal' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_mobile`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        if ($type == 'video') {
            $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_hd`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
            $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
            $total[] = $rs->fields['total'];

            $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_type`='embed' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
            $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
            $total[] = $rs->fields['total'];
        } elseif ($type == 'doc') {
            $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_pdf`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
            $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
            $total[] = $rs->fields['total'];

            $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_swf`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
            $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
            $total[] = $rs->fields['total'];
        }

        $sql     = sprintf("SELECT %s FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`is_promoted`='1' AND A.`usr_id`=B.`usr_id` %s;", $q1, $type, $usr_q);
        $rs      = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_dashboard_filecounts'], $sql) : self::$db->execute($sql);
        $total[] = $rs->fields['total'];

        return $total;
    }
    /* assign arrays to template vars/javascript for graphs */
    public function assignStats()
    {
        $date = date("Y-m-d");
        $sql  = sprintf("SELECT `data`, `time` FROM `db_dashboard` WHERE `date`='%s' LIMIT 1;", $date);

        $rs = self::$db->execute($sql);

        $data = $rs->fields['data'] ? unserialize($rs->fields['data']) : $this->getCounts();

        $lcount = $data['lcount'];
        $vcount = $data['vcount'];
        $scount = $data['scount'];
        $icount = $data['icount'];
        $acount = $data['acount'];
        $dcount = $data['dcount'];
        $bcount = $data['bcount'];

        $this_week_live  = $data['this_week_live'];
        $this_week_video = $data['this_week_video'];
        $this_week_short = $data['this_week_short'];
        $this_week_image = $data['this_week_image'];
        $this_week_audio = $data['this_week_audio'];
        $this_week_doc   = $data['this_week_doc'];
        $this_week_blog  = $data['this_week_blog'];

        $last_week_live  = $data['last_week_live'];
        $last_week_video = $data['last_week_video'];
        $last_week_short = $data['last_week_short'];
        $last_week_image = $data['last_week_image'];
        $last_week_audio = $data['last_week_audio'];
        $last_week_doc   = $data['last_week_doc'];
        $last_week_blog  = $data['last_week_blog'];

        $this_week_users = $data['this_week_users'];
        $last_week_users = $data['last_week_users'];

        $this_year_earnings = $data['this_year_earnings'];
        $last_year_earnings = $data['last_year_earnings'];

        $tpl = null;

        if (self::$cfg['live_module'] == 1) {
            $tpl         = self::$smarty->assign('lcount', $lcount);
            $mod_count[] = 'live';
        }
        if (self::$cfg['video_module'] == 1) {
            $tpl         = self::$smarty->assign('vcount', $vcount);
            $mod_count[] = 'video';
        }
        if (self::$cfg['short_module'] == 1) {
            $tpl         = self::$smarty->assign('scount', $scount);
            $mod_count[] = 'short';
        }
        if (self::$cfg['image_module'] == 1) {
            $tpl         = self::$smarty->assign('icount', $icount);
            $mod_count[] = 'image';
        }
        if (self::$cfg['audio_module'] == 1) {
            $tpl         = self::$smarty->assign('acount', $acount);
            $mod_count[] = 'audio';
        }
        if (self::$cfg['document_module'] == 1) {
            $tpl         = self::$smarty->assign('dcount', $dcount);
            $mod_count[] = 'doc';
        }
        if (self::$cfg['blog_module'] == 1) {
            $tpl         = self::$smarty->assign('bcount', $bcount);
            $mod_count[] = 'blog';
        }

        $tpl = self::$smarty->assign('mod_total', count($mod_count));

        $tpl = self::$smarty->assign('this_week_live', implode(',', $this_week_live));
        $tpl = self::$smarty->assign('this_week_video', implode(',', $this_week_video));
        $tpl = self::$smarty->assign('this_week_short', implode(',', $this_week_short));
        $tpl = self::$smarty->assign('this_week_image', implode(',', $this_week_image));
        $tpl = self::$smarty->assign('this_week_audio', implode(',', $this_week_audio));
        $tpl = self::$smarty->assign('this_week_doc', implode(',', $this_week_doc));
        $tpl = self::$smarty->assign('this_week_blog', implode(',', $this_week_blog));

        $tpl = self::$smarty->assign('last_week_live', implode(',', $last_week_live));
        $tpl = self::$smarty->assign('last_week_video', implode(',', $last_week_video));
        $tpl = self::$smarty->assign('last_week_short', implode(',', $last_week_short));
        $tpl = self::$smarty->assign('last_week_image', implode(',', $last_week_image));
        $tpl = self::$smarty->assign('last_week_audio', implode(',', $last_week_audio));
        $tpl = self::$smarty->assign('last_week_doc', implode(',', $last_week_doc));
        $tpl = self::$smarty->assign('last_week_blog', implode(',', $last_week_blog));

        $tpl = self::$smarty->assign('this_week_users', implode(',', $this_week_users));
        $tpl = self::$smarty->assign('last_week_users', implode(',', $last_week_users));

        $tpl = self::$smarty->assign('this_year_earnings', implode(',', $this_year_earnings));
        $tpl = self::$smarty->assign('last_year_earnings', implode(',', $last_year_earnings));
    }
    /* cron job */
    public function updateDashboardStats()
    {
        $date = date("Y-m-d");
        $sql  = sprintf("SELECT `data`, `time` FROM `db_dashboard` WHERE `date`='%s' LIMIT 1;", $date);

        $rs = self::$db->execute($sql);

        if ($rs->fields['data']) {
//found data, try to update
            $time     = $rs->fields['time'];
            $dateFrom = strtotime("$date $time");
            $dateAgo  = strtotime("-6 hours");

            if ($dateFrom > $dateAgo) {
                // less than 6 hours ago, no action
                echo "no action taken\n";
            } else {
                // more than 6 hours ago, update numbers
                $data   = $this->getCounts();
                $sql    = sprintf("UPDATE `db_dashboard` SET `data`='%s', `time`='%s' WHERE `date`='%s' LIMIT 1;", serialize($data), date("H:i:s"), $date);
                $action = self::$db->execute($sql);

                if (self::$db->Affected_Rows() > 0) {
                    echo "dashboard stats updated\n";
                } else {
                    echo "dashboard stats unchanged\n";
                }
            }
        } else {
//no stats, add new entry
            $data = $this->getCounts();

            $insert_data = array("date" => $date, "time" => date("H:i:s"), "data" => serialize($data));

            $action = self::$dbc->doInsert("db_dashboard", $insert_data);
        }
    }
    /* get counts in cron job */
    private function getCounts()
    {
        $data = array();

        $data['lcount'] = $this->getFileCounts('live');
        $data['vcount'] = $this->getFileCounts('video');
        $data['scount'] = $this->getFileCounts('short');
        $data['icount'] = $this->getFileCounts('image');
        $data['acount'] = $this->getFileCounts('audio');
        $data['dcount'] = $this->getFileCounts('doc');
        $data['bcount'] = $this->getFileCounts('blog');

        $data['this_week_live']  = $this->getWeekStats('live');
        $data['this_week_video'] = $this->getWeekStats('video');
        $data['this_week_short'] = $this->getWeekStats('short');
        $data['this_week_image'] = $this->getWeekStats('image');
        $data['this_week_audio'] = $this->getWeekStats('audio');
        $data['this_week_doc']   = $this->getWeekStats('doc');
        $data['this_week_blog']  = $this->getWeekStats('blog');

        $data['last_week_live']  = $this->getWeekStats('live', 'last');
        $data['last_week_video'] = $this->getWeekStats('video', 'last');
        $data['last_week_short'] = $this->getWeekStats('short', 'last');
        $data['last_week_image'] = $this->getWeekStats('image', 'last');
        $data['last_week_audio'] = $this->getWeekStats('audio', 'last');
        $data['last_week_doc']   = $this->getWeekStats('doc', 'last');
        $data['last_week_blog']  = $this->getWeekStats('blog', 'last');

        $data['this_week_users'] = $this->getWeekStats('user');
        $data['last_week_users'] = $this->getWeekStats('user', 'last');

        $data['this_year_earnings'] = $this->getWeekStats('earnings');
        $data['last_year_earnings'] = $this->getWeekStats('earnings', 'last');

        return $data;
    }
    /* add new notification */
    public function addNotification($type, $subject, $message)
    {
        $dbc    = self::$dbc;
        $insert = array('type' => $type, 'subject' => $subject, 'body' => $message, 'date' => date('Y-m-d H:i:s'));

        $dbc->doInsert('db_notifications', $insert);
    }
    /* notification list lightbox */
    public function getNotifications()
    {
        $language = self::$language;

        $html = '   <div id="lb-wrapper">
                    <article>
                        <h3 class="content-title"><i class="icon-notification"></i> ' . $language['frontend.global.notifications'] . '</h3>
                        <div class="line"></div>
                    </article>
                    ' . $this->listNotifications() . '
                </div>
            ';

        return $html;
    }
    /* get number of new notifications */
    public function getNewNotifications()
    {
        $db = self::$db;

        $sql = "SELECT COUNT(`id`) AS `total` FROM `db_notifications` WHERE `seen`='0';";

        $res = $db->execute($sql);

        return $res->fields['total'];
    }
    /* get list of notifications */
    private function listNotifications()
    {
        $db       = self::$db;
        $smarty   = self::$smarty;
        $language = self::$language;

        $html = null;
        $js   = null;

        if ($_POST and isset($_POST['cb_uaction']) and is_array($_POST['cb_uaction'])) {
            $verified = array();
            $selected = $_POST['cb_uaction'];

            foreach ($selected as $entry_id) {
                if ((int) $entry_id > 0) {
                    $verified[] = $entry_id;
                }
            }
            if ($verified[0] > 0) {
                $t   = count($verified);
                $sql = sprintf("UPDATE `db_notifications` SET `seen`='1' WHERE `id` IN (%s) LIMIT %s;", implode(',', $verified), $t);

                $db->execute($sql);

                $js .= 'a = parseInt($("#new-notifications-nr").text()); b = ' . $t . '; $("#new-notifications-nr").text(a-b);';
            } else {
                $js .= '$("#new-notifications-nr").text("0");';
            }
        }

        $sql = "SELECT `id`, `type`, `subject`, `body`, `date` FROM `db_notifications` WHERE `seen`='0' ORDER BY `id` DESC;";

        $res = $db->execute($sql);

        if ($res->fields['id']) {
            $html .= '<div id="notif-wrapper">';
            $html .= $smarty->fetch('tpl_backend/tpl_settings/ct-switch-js.tpl');
            $html .= '
                    <div class="section-top-bar">
                        <div class="place-left icheck-box all" id="checkselect-all-entries"><input type="checkbox" class="no-top-margin" name="cb_allaction" value="1" onclick="if($(this).is(\':checked\')){$(\'.cb-uaction\').attr(\'checked\', true);}else{$(\'.cb-uaction\').attr(\'checked\', false);}" /></div>
                        <div class="sortings"><button onfocus="blur();" value="1" type="button" class="button-grey search-button form-button save-entry-button notif-clear" id="btn-0-save_changes" name="save_changes" style="margin-bottom:0;margin-top:0px;line-height:1;padding:5px;min-width:185px"><span>' . $language['frontend.global.clear.sel'] . '</span></button></div>
                        <div class="page-actions">' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '</div>
                        <div class="clearfix"></div>
                    </div>
                ';

            $html .= '<form id="notifications-form" method="post" action="">';
            $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger notifications-accordion be-files">';

            $et = null;
            while (!$res->EOF) {
                switch ($res->fields['type']) {
                    case "file_flagging":
                        $icon = 'icon-flag';
                        $et   = null;
                        break;
                    case "backend_notification_signup":
                        $icon = 'icon-user';
                        $et   = null;
                        break;
                    case "new_upload_be":
                        $icon = 'icon-upload';
                        $b    = $res->fields['body'];
                        $r    = preg_match_all('~<a(.*?)href="([^"]+)"(.*?)>~', $b, $matches);
                        $a    = $matches[2];
                        $u    = explode("/", $a[0]);
                        $k    = str_replace(VHref::getKey("be_files") . '?k=', '', $u[count($u) - 1]);
                        $t    = substr($k, 0, 1);

                        switch ($t) {
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
                        $k  = substr($k, 1);
                        $s  = self::$dbc->singleFieldValue('db_' . $tbl . 'files', 'approved', 'file_key', $k);
                        $et = $s == '' ? '<b>(deleted)</b>' : ($s == 0 ? '<b>' . self::$language['backend.files.text.req'] . '</b>' : null);

                        break;
                    case "payment_notification_be":
                        $icon = 'iconBe-coin';
                        $et   = null;
                        break;
                    case "account_removal":
                        $icon = 'iconBe-x';
                        $et   = null;
                        break;
                }
                $html .= '
                        <li>
                            <div>
                            <div class="responsive-accordion-head d-flex d-column ai-start">
                                <div class="d-flex ai-center">
                                    <div class="place-left icheck-box fetched"><input type="checkbox" class="no-top-margin cb-uaction" name="cb_uaction[]" value="' . $res->fields['id'] . '" /></div>
                                    <div class="entry-title ct-bullet-label-off place-left link"><i class="' . $icon . '"></i> ' . $res->fields['subject'] . ' ' . ($et) . '</div>
                                </div>
                                <div>
                                    <div class="entry-type ct-bullet-label-off" style="margin-left:36px"><span class="greyed-out">' . VUserinfo::timeRange($res->fields['date']) . '</span></div>
                                </div>
                                <div class="place-right expand-entry">
                                    <i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>
                                    <i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>
                                </div>
                            </div>
                            <div class="responsive-accordion-panel" style="display: none;">
                                ' . $res->fields['body'] . '
                            </div>
                            </div>
                        </li>
                    ';

                $res->MoveNext();
            }

            $html .= '</ul>';
            $html .= '</form>';
            $html .= '</div>';

            $js .= '$(".icheck-box.all input").each(function () {
                        var self = $(this);
                        self.iCheck({
                            checkboxClass: "icheckbox_square-blue",
                            radioClass: "iradio_square-blue",
                            increaseArea: "20%"
                        });
                    });
                    $(".icheck-box.fetched input").each(function () {
                        var self = $(this);
                        self.iCheck({
                            checkboxClass: "icheckbox_square-blue",
                            radioClass: "iradio_square-blue",
                            increaseArea: "20%"
                        });
                    });

                    $("#checkselect-all-entries input").on("ifChecked", function () { $(".notifications-accordion input").iCheck("check");});
                    $("#checkselect-all-entries input").on("ifUnchecked", function () { $(".notifications-accordion input").iCheck("uncheck"); });

                    $(".notif-clear").on("click", function() {
                        $("#lb-wrapper").mask("");
                        $.post( "' . VHref::getKey('be_dashboard') . '?s=notif&a=clear", $( "#notifications-form" ).serialize(), function(data) {
                            $("#lb-wrapper").replaceWith(data);
                            $("#lb-wrapper").unmask();
                        });
                    });
            ';

            $html .= '<script type="text/javascript">' . $smarty->fetch('f_scripts/be/js/settings-accordion.js') . '</script>';
        } else {
            $html .= '<p>' . $language['backend.no.notifications'] . '</p>';
        }
        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }

}
