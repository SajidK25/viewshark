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

define('FEE', true);

class VSubscriber
{
    /* calculate paypal fee */
    private static function fee($total)
    {
        return (FEE ? round((((3.49 / 100) * $total) + 0.49), 2) : 0);
    }
    /* get payout currentcy */
    private static function getPayoutCurrency($uid = false)
    {
        global $cfg, $db;

        $uid      = !$uid ? (int) $_SESSION['USER_ID'] : $uid;
        $currency = $cfg['subscription_payout_currency'];

        $ui = $db->execute(sprintf("SELECT `usr_sub_share`, `usr_sub_perc`, `usr_sub_currency` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $uid));
        if ($ui->fields['usr_sub_share'] == 1) {
            $currency = $ui->fields['usr_sub_currency'];
        }

        return $currency;
    }
    /* generate html payout reports */
    public static function html_payouts($graphs = false)
    {
        include 'f_core/config.backend.php';
        global $language, $class_database, $class_filter, $class_language, $db, $cfg, $smarty, $href, $section;

        $type    = 'sub';
        $usr_key = ($cfg['is_be'] == 1 and isset($_GET['uk'])) ? $class_filter->clr_str($_GET['uk']) : (!$cfg['is_be'] ? $_SESSION['USER_KEY'] : false);
        $f       = isset($_GET['f']) ? $class_filter->clr_str($_GET['f']) : (!$graphs ? 'thismonth' : null);
        $side    = (!$cfg['is_be'] and $section == $href['subscribers']) ? 'frontend' : ($cfg['is_be'] == 1 ? 'backend' : false);

        if (!$side) {
            return;
        }

        $subs_min = isset($_SESSION['subs_min']) ? $_SESSION['subs_min'] : 0;
        $subs_max = isset($_SESSION['subs_max']) ? $_SESSION['subs_max'] : 0;

        if (isset($_POST['subs_limit_min']) or isset($_POST['subs_limit_max'])) {
            $_SESSION['subs_min'] = (int) $_POST['subs_limit_min'];
            $_SESSION['subs_max'] = (int) $_POST['subs_limit_max'];

            $subs_min = $_SESSION['subs_min'];
            $subs_max = $_SESSION['subs_max'];
        }

        if ($f and !$graphs) {
            switch ($f) {
                default:
                    if ($_POST and isset($_POST['custom_date_start']) and $_POST['custom_date_start'] != '') {
                        $sdf = $class_filter->clr_str($_POST['custom_date_start']);
                        $edf = $class_filter->clr_str($_POST['custom_date_end']);
                    }
                    break;
                case "":
                case "thismonth":
                    $w   = self::getMonth(date("Y"), (date("m")));
                    $t   = count($w) - 1;
                    $sdf = $w[0];
                    $edf = $w[$t];
                    break;
                case "lastmonth":
                    $w   = self::getMonth(date("Y"), (date("m") - 1));
                    $t   = count($w) - 1;
                    $sdf = $w[0];
                    $edf = $w[$t];
                    break;
                case "last3months":
                    $w   = self::getMonth(date('Y', strtotime("-3 month")), date('m', strtotime("-3 month")));
                    $t   = count($w) - 1;
                    $sdf = $w[0];
                    $w   = self::getMonth(date('Y', strtotime("-1 month")), date('m', strtotime("-1 month")));
                    $t   = count($w) - 1;
                    $edf = $w[$t];
                    break;
                case "last6months":
                    $w   = self::getMonth(date('Y', strtotime("-6 month")), date('m', strtotime("-6 month")));
                    $t   = count($w) - 1;
                    $sdf = $w[0];
                    $w   = self::getMonth(date('Y', strtotime("-1 month")), date('m', strtotime("-1 month")));
                    $t   = count($w) - 1;
                    $edf = $w[$t];
                    break;
                case "thisyear":
                    $sdf = sprintf("%s-01-01", date("Y"));
                    $edf = sprintf("%s-12-31", date("Y"));
                    break;
                case "lastyear":
                    $sdf = sprintf("%s-01-01", (date("Y") - 1));
                    $edf = sprintf("%s-12-31", (date("Y") - 1));
                    break;
            }
        }

        $uk  = ($cfg['is_be'] == 1 and isset($_GET['uk'])) ? $class_filter->clr_str($_GET['uk']) : (!$cfg['is_be'] ? $_SESSION['USER_KEY'] : false);
        $fk  = isset($_GET['fk']) ? $class_filter->clr_str($_GET['fk']) : false;
        $tab = isset($_GET['tab']) ? $class_filter->clr_str($_GET['tab']) : false;

        switch ($tab) {
            default:
            case "section-unpaid":
                $is_paid = "='0'";
                break;
            case "section-paid":
                $is_paid = "='1'";
                break;
            case "section-all":
                $is_paid = ">='0'";
                break;
        }

        if (!$graphs) {
            $sql = sprintf("SELECT
                         A.`usr_user`, A.`usr_dname`, A.`ch_dname`, A.`usr_sub_share`, A.`usr_sub_perc`, A.`usr_sub_email`,
                         B.`db_id`, B.`usr_id_to`, B.`is_cancel`, B.`cancel_time`
                         FROM
                         `db_accountuser` A, `db_subpayouts` B
                         WHERE
                         A.`usr_id`=B.`usr_id_to` AND
                         date(B.`sub_time`) BETWEEN '%s' AND '%s' AND
                         %s
                         B.`is_paid`%s
                         GROUP BY B.`usr_id_to`;", $sdf, $edf, ($uk > 0 ? sprintf("A.`usr_key`='%s' AND", $uk) : null), $is_paid);
        } else {
            switch ($f) {
                default:
                case "":
                case "week":
                    $wa  = array();
                    $wnr = isset($_GET['w']) ? (int) $_GET['w'] : false;

                    for ($i = 1; $i <= 52; $i++) {
                        $wa[] = $i;
                    }

                    if (in_array($wnr, $wa)) {
                        $w    = self::getStartAndEndDateWeek($wnr, date("Y"));
                        $tsdf = $w['week_start'];
                        $tedf = $w['week_end'];
                        $w    = self::getStartAndEndDateWeek($wnr - 1, date("Y"));
                        $lsdf = $w['week_start'];
                        $ledf = $w['week_end'];
                    } else {
                        $w    = self::getThisWeekDates();
                        $tsdf = $w[0];
                        $tedf = $w[6];
                        $w    = self::getLastWeekDates();
                        $lsdf = $w[0];
                        $ledf = $w[6];
                    }

                    $sql  = self::weekSQL($lsdf, $ledf);
                    $sql2 = self::weekSQL($tsdf, $tedf);
                    $res2 = $db->execute($sql2);
                    break;

                case "month":
                    $ma  = array();
                    $mnr = isset($_GET['m']) ? (int) $_GET['m'] : false;

                    for ($i = 1; $i <= 12; $i++) {
                        $ma[] = $i;
                    }

                    if (in_array($mnr, $ma)) {
                        $w    = self::getStartAndEndDateMonth($mnr, date("Y"));
                        $tsdf = $w['month_start'];
                        $tedf = $w['month_end'];
                        $w    = self::getStartAndEndDateMonth($mnr - 1, date("Y"));
                        $lsdf = $w['month_start'];
                        $ledf = $w['month_end'];
                    } else {
                        $w    = self::getMonth(date("Y"), (date("m")));
                        $t    = count($w) - 1;
                        $tsdf = $w[0];
                        $tedf = $w[$t];
                        $w    = self::getMonth(date("Y"), (date("m") - 1));
                        $t    = count($w) - 1;
                        $lsdf = $w[0];
                        $ledf = $w[$t];
                    }

                    $sql  = self::monthSQL($lsdf, $ledf);
                    $sql2 = self::monthSQL($tsdf, $tedf);
                    $res2 = $db->execute($sql2);
                    break;

                case "year":
                    $ya  = array();
                    $yy  = date("Y");
                    $ynr = isset($_GET['y']) ? (int) $_GET['y'] : false;

                    for ($i = $yy - 5; $i <= $yy + 5; $i++) {
                        $ya[] = $i;
                    }

                    if (in_array($ynr, $ya)) {
                        $tsdf = sprintf("%s-01-01", $ynr);
                        $tedf = sprintf("%s-12-31", $ynr);
                        $lsdf = sprintf("%s-01-01", $ynr - 1);
                        $ledf = sprintf("%s-12-31", $ynr - 1);
                    } else {
                        $tsdf = sprintf("%s-01-01", $yy);
                        $tedf = sprintf("%s-12-31", $yy);
                        $lsdf = sprintf("%s-01-01", $yy - 1);
                        $ledf = sprintf("%s-12-31", $yy - 1);
                    }

                    $sql  = self::yearSQL($lsdf, $ledf);
                    $sql2 = self::yearSQL($tsdf, $tedf);
                    $res2 = $db->execute($sql2);
                    break;
            }
        }

        $pcurrency = self::getPayoutCurrency();

        $res1 = $db->execute($sql);

        $html = self::tpl_filter();
        $html .= '<div id="lb-wrapper">';

        if (!$graphs) {
            $html .= !$cfg['is_be'] ? '<div class="place-left mt7">' . str_replace('##AMT##', $cfg['sub_threshold'], $language['account.entry.payout.threshold']) . '</div>' : '<div class="place-left mt10">' . str_replace('##AMT##', $cfg['sub_threshold'], $language['account.entry.payout.threshold.the']) . '</div>';
            $html .= '<div class="clearfix"></div>';
        }

        $html .= '<div id="info-text"></div>';
        $html .= '<div id="v-tabs" class="tabs tabs-style-line">';
        $html .= '<div class="view-mode-type active" rel-t="sub"></div>';

        if (!$graphs) {
            $html .= '
                    <nav>
                        <ul id="tb">
                            <li class="' . ((!$tab or $tab == 'section-unpaid') ? 'tab-current' : null) . '"><a href="#section-unpaid" class="icon icon-warning"><span>' . $language['account.entry.payout.unpaid'] . '</span></a></li>
                            <li class="' . ($tab == 'section-paid' ? 'tab-current' : null) . '"><a href="#section-paid" class="icon icon-check"><span>' . $language['account.entry.payout.paid'] . '</span></a></li>
                            <li class="' . ($tab == 'section-all' ? 'tab-current' : null) . '"><a href="#section-all" class="icon icon-paypal"><span>' . $language['frontend.global.alltxt'] . '</span></a></li>
                        </ul>
                        <div class="open-close-reports">
                            ' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '
                        </div>
                        <div class="clearfix"></div>
                    </nav>
            ';
        }

        $html .= '<div class="content-wrap">';
        if (!$graphs) {
            $html .= '<section id="section-unpaid">';
            $html .= self::tpl_payout_accordion($res1, $sdf, $edf, $subs_min, $subs_max, $pcurrency);
            $html .= '</section>';
            $html .= '<section id="section-paid"></section>';
            $html .= '<section id="section-all"></section>';
        } else {
            switch ($f) {
                case "year":
                    if ($res1->fields['YeNumber']) {
//last year
                        $_u = array();
                        $_t = array();
                        $_s = array();
                        $_e = array();

                        for ($i = 1; $i <= 12; $i++) {
                            $_u[] = self::nf($res1->fields["Orders" . $i]);
                            $_t[] = self::nf($res1->fields["Sales" . $i]);
                            $_s[] = self::nf($res1->fields["SharedSales" . $i]);
                            $_e[] = self::nf($res1->fields["Sales" . $i] - $res1->fields["SharedSales" . $i]);
                        }

                        $smarty->assign('lwtotal', self::nf($res1->fields['SalesYear']));
                        $smarty->assign('lwshared', self::nf($res1->fields['SharedSalesYear']));
                        $smarty->assign('lwearned', self::nf($res1->fields['SalesYear'] - $res1->fields['SharedSalesYear']));
                        $smarty->assign('lwsubscribers', $res1->fields['SubscribersYear']);
                        $smarty->assign('lwsubscriptions', $res1->fields['OrdersYear']);
                        $smarty->assign('lws', json_encode($_u));
                        $smarty->assign('tw1', json_encode($_t));
                        $smarty->assign('sw1', json_encode($_s));
                        $smarty->assign('ew1', json_encode($_e));
                    } else {
                        $smarty->assign('lwtotal', 0);
                        $smarty->assign('lwshared', 0);
                        $smarty->assign('lwearned', 0);
                        $smarty->assign('lwsubscribers', 0);
                        $smarty->assign('lwsubscriptions', 0);
                        $smarty->assign('lws', '[]');
                        $smarty->assign('tw1', '[]');
                        $smarty->assign('sw1', '[]');
                        $smarty->assign('ew1', '[]');
                    }

                    if ($res2->fields['YeNumber']) {
//this year
                        $_u = array();
                        $_t = array();
                        $_s = array();
                        $_e = array();

                        for ($i = 1; $i <= 12; $i++) {
                            $_u[] = self::nf($res2->fields["Orders" . $i]);
                            $_t[] = self::nf($res2->fields["Sales" . $i]);
                            $_s[] = self::nf($res2->fields["SharedSales" . $i]);
                            $_e[] = self::nf($res2->fields["Sales" . $i] - $res2->fields["SharedSales" . $i]);
                        }

                        $smarty->assign('twtotal', self::nf($res2->fields['SalesYear']));
                        $smarty->assign('twshared', self::nf($res2->fields['SharedSalesYear']));
                        $smarty->assign('twearned', self::nf($res2->fields['SalesYear'] - $res2->fields['SharedSalesYear']));
                        $smarty->assign('twsubscribers', $res2->fields['SubscribersYear']);
                        $smarty->assign('twsubscriptions', $res2->fields['OrdersYear']);
                        $smarty->assign('tws', json_encode($_u));
                        $smarty->assign('tw2', json_encode($_t));
                        $smarty->assign('sw2', json_encode($_s));
                        $smarty->assign('ew2', json_encode($_e));
                    } else {
                        $smarty->assign('twtotal', 0);
                        $smarty->assign('twshared', 0);
                        $smarty->assign('twearned', 0);
                        $smarty->assign('twsubscribers', 0);
                        $smarty->assign('twsubscriptions', 0);
                        $smarty->assign('tws', '[]');
                        $smarty->assign('tw2', '[]');
                        $smarty->assign('sw2', '[]');
                        $smarty->assign('ew2', '[]');
                    }
                    break;

                case "month":
                    if ($res1->fields['MnNumber']) {
//last month
                        $_u = array();
                        $_t = array();
                        $_s = array();
                        $_e = array();

                        for ($i = 1; $i <= 31; $i++) {
                            $_u[] = self::nf($res1->fields["Orders" . $i]);
                            $_t[] = self::nf($res1->fields["Sales" . $i]);
                            $_s[] = self::nf($res1->fields["SharedSales" . $i]);
                            $_e[] = self::nf($res1->fields["Sales" . $i] - $res1->fields["SharedSales" . $i]);
                        }

                        $smarty->assign('lwtotal', self::nf($res1->fields['SalesMonth']));
                        $smarty->assign('lwshared', self::nf($res1->fields['SharedSalesMonth']));
                        $smarty->assign('lwearned', self::nf($res1->fields['SalesMonth'] - $res1->fields['SharedSalesMonth']));
                        $smarty->assign('lwsubscribers', $res1->fields['SubscribersMonth']);
                        $smarty->assign('lwsubscriptions', $res1->fields['OrdersMonth']);
                        $smarty->assign('lws', json_encode($_u));
                        $smarty->assign('tw1', json_encode($_t));
                        $smarty->assign('sw1', json_encode($_s));
                        $smarty->assign('ew1', json_encode($_e));
                    } else {
                        $smarty->assign('lwtotal', 0);
                        $smarty->assign('lwshared', 0);
                        $smarty->assign('lwearned', 0);
                        $smarty->assign('lwsubscribers', 0);
                        $smarty->assign('lwsubscriptions', 0);
                        $smarty->assign('lws', '[]');
                        $smarty->assign('tw1', '[]');
                        $smarty->assign('sw1', '[]');
                        $smarty->assign('ew1', '[]');
                    }

                    if ($res2->fields['MnNumber']) {
//this month
                        $_u = array();
                        $_t = array();
                        $_s = array();
                        $_e = array();

                        for ($i = 1; $i <= 31; $i++) {
                            $_u[] = self::nf($res2->fields["Orders" . $i]);
                            $_t[] = self::nf($res2->fields["Sales" . $i]);
                            $_s[] = self::nf($res2->fields["SharedSales" . $i]);
                            $_e[] = self::nf($res2->fields["Sales" . $i] - $res2->fields["SharedSales" . $i]);
                        }

                        $smarty->assign('twtotal', self::nf($res2->fields['SalesMonth']));
                        $smarty->assign('twshared', self::nf($res2->fields['SharedSalesMonth']));
                        $smarty->assign('twearned', self::nf($res2->fields['SalesMonth'] - $res2->fields['SharedSalesMonth']));
                        $smarty->assign('twsubscribers', $res2->fields['SubscribersMonth']);
                        $smarty->assign('twsubscriptions', $res2->fields['OrdersMonth']);
                        $smarty->assign('tws', json_encode($_u));
                        $smarty->assign('tw2', json_encode($_t));
                        $smarty->assign('sw2', json_encode($_s));
                        $smarty->assign('ew2', json_encode($_e));
                    } else {
                        $smarty->assign('twtotal', 0);
                        $smarty->assign('twshared', 0);
                        $smarty->assign('twearned', 0);
                        $smarty->assign('twsubscribers', 0);
                        $smarty->assign('twsubscriptions', 0);
                        $smarty->assign('tws', '[]');
                        $smarty->assign('tw2', '[]');
                        $smarty->assign('sw2', '[]');
                        $smarty->assign('ew2', '[]');
                    }
                    break;

                default:
                case "":
                case "week":
                    if ($res1->fields['WkNumber']) {
//last week
                        /* week starting sunday */
                        //$_u = array(self::nf($res1->fields['OrdersSun']), self::nf($res1->fields['OrdersMon']), self::nf($res1->fields['OrdersTue']), self::nf($res1->fields['OrdersWed']), self::nf($res1->fields['OrdersThu']), self::nf($res1->fields['OrdersFri']), self::nf($res1->fields['OrdersSat']));
                        /* week starting monday */
                        $_u = array(self::nf($res1->fields['OrdersMon']), self::nf($res1->fields['OrdersTue']), self::nf($res1->fields['OrdersWed']), self::nf($res1->fields['OrdersThu']), self::nf($res1->fields['OrdersFri']), self::nf($res1->fields['OrdersSat']), self::nf($res1->fields['OrdersSun']));
                        /* week starting sunday */
                        //$_t = array(self::nf($res1->fields['SalesSun']), self::nf($res1->fields['SalesMon']), self::nf($res1->fields['SalesTue']), self::nf($res1->fields['SalesWed']), self::nf($res1->fields['SalesThu']), self::nf($res1->fields['SalesFri']), self::nf($res1->fields['SalesSat']));
                        /* week starting monday */
                        $_t = array(self::nf($res1->fields['SalesMon']), self::nf($res1->fields['SalesTue']), self::nf($res1->fields['SalesWed']), self::nf($res1->fields['SalesThu']), self::nf($res1->fields['SalesFri']), self::nf($res1->fields['SalesSat']), self::nf($res1->fields['SalesSun']));
                        /* week starting sunday */
                        //$_s = array(self::nf($res1->fields['SharedSalesSun']), self::nf($res1->fields['SharedSalesMon']), self::nf($res1->fields['SharedSalesTue']), self::nf($res1->fields['SharedSalesWed']), self::nf($res1->fields['SharedSalesThu']), self::nf($res1->fields['SharedSalesFri']), self::nf($res1->fields['SharedSalesSat']));
                        /* week starting monday */
                        $_s = array(self::nf($res1->fields['SharedSalesMon']), self::nf($res1->fields['SharedSalesTue']), self::nf($res1->fields['SharedSalesWed']), self::nf($res1->fields['SharedSalesThu']), self::nf($res1->fields['SharedSalesFri']), self::nf($res1->fields['SharedSalesSat']), self::nf($res1->fields['SharedSalesSun']));
                        /* week starting sunday */
                        //$_e = array(self::nf($res1->fields['SalesSun'] - $res1->fields['SharedSalesSun']), self::nf($res1->fields['SalesMon'] - $res1->fields['SharedSalesMon']), self::nf($res1->fields['SalesTue'] - $res1->fields['SharedSalesTue']), self::nf($res1->fields['SalesWed'] - $res1->fields['SharedSalesWed']), self::nf($res1->fields['SalesThu'] - $res1->fields['SharedSalesThu']), self::nf($res1->fields['SalesFri'] - $res1->fields['SharedSalesFri']), self::nf($res1->fields['SalesSat'] - $res1->fields['SharedSalesSat']));
                        /* week starting monday */
                        $_e = array(self::nf($res1->fields['SalesMon'] - $res1->fields['SharedSalesMon']), self::nf($res1->fields['SalesTue'] - $res1->fields['SharedSalesTue']), self::nf($res1->fields['SalesWed'] - $res1->fields['SharedSalesWed']), self::nf($res1->fields['SalesThu'] - $res1->fields['SharedSalesThu']), self::nf($res1->fields['SalesFri'] - $res1->fields['SharedSalesFri']), self::nf($res1->fields['SalesSat'] - $res1->fields['SharedSalesSat']), self::nf($res1->fields['SalesSun'] - $res1->fields['SharedSalesSun']));

                        $smarty->assign('lwtotal', self::nf($res1->fields['SalesWeek']));
                        $smarty->assign('lwshared', self::nf($res1->fields['SharedSalesWeek']));
                        $smarty->assign('lwearned', self::nf($res1->fields['SalesWeek'] - $res1->fields['SharedSalesWeek']));
                        $smarty->assign('lwsubscribers', $res1->fields['SubscribersWeek']);
                        $smarty->assign('lwsubscriptions', $res1->fields['OrdersWeek']);
                        $smarty->assign('lws', json_encode($_u));
                        $smarty->assign('tw1', json_encode($_t));
                        $smarty->assign('sw1', json_encode($_s));
                        $smarty->assign('ew1', json_encode($_e));
                    } else {
                        $smarty->assign('lwtotal', 0);
                        $smarty->assign('lwshared', 0);
                        $smarty->assign('lwearned', 0);
                        $smarty->assign('lwsubscribers', 0);
                        $smarty->assign('lwsubscriptions', 0);
                        $smarty->assign('lws', '[]');
                        $smarty->assign('tw1', '[]');
                        $smarty->assign('sw1', '[]');
                        $smarty->assign('ew1', '[]');
                    }
                    if ($res2->fields['WkNumber']) {
//this week
                        /* week starting sunday */
                        //$_u = array(self::nf($res2->fields['OrdersSun']), self::nf($res2->fields['OrdersMon']), self::nf($res2->fields['OrdersTue']), self::nf($res2->fields['OrdersWed']), self::nf($res2->fields['OrdersThu']), self::nf($res2->fields['OrdersFri']), self::nf($res2->fields['OrdersSat']));
                        /* week starting monday */
                        $_u = array(self::nf($res2->fields['OrdersMon']), self::nf($res2->fields['OrdersTue']), self::nf($res2->fields['OrdersWed']), self::nf($res2->fields['OrdersThu']), self::nf($res2->fields['OrdersFri']), self::nf($res2->fields['OrdersSat']), self::nf($res2->fields['OrdersSun']));
                        /* week starting sunday */
                        //$_t = array(self::nf($res2->fields['SalesSun']), self::nf($res2->fields['SalesMon']), self::nf($res2->fields['SalesTue']), self::nf($res2->fields['SalesWed']), self::nf($res2->fields['SalesThu']), self::nf($res2->fields['SalesFri']), self::nf($res2->fields['SalesSat']));
                        /* week starting monday */
                        $_t = array(self::nf($res2->fields['SalesMon']), self::nf($res2->fields['SalesTue']), self::nf($res2->fields['SalesWed']), self::nf($res2->fields['SalesThu']), self::nf($res2->fields['SalesFri']), self::nf($res2->fields['SalesSat']), self::nf($res2->fields['SalesSun']));
                        /* week starting sunday */
                        //$_s = array(self::nf($res2->fields['SharedSalesSun']), self::nf($res2->fields['SharedSalesMon']), self::nf($res2->fields['SharedSalesTue']), self::nf($res2->fields['SharedSalesWed']), self::nf($res2->fields['SharedSalesThu']), self::nf($res2->fields['SharedSalesFri']), self::nf($res2->fields['SharedSalesSat']));
                        /* week starting monday */
                        $_s = array(self::nf($res2->fields['SharedSalesMon']), self::nf($res2->fields['SharedSalesTue']), self::nf($res2->fields['SharedSalesWed']), self::nf($res2->fields['SharedSalesThu']), self::nf($res2->fields['SharedSalesFri']), self::nf($res2->fields['SharedSalesSat']), self::nf($res2->fields['SharedSalesSun']));
                        /* week starting sunday */
                        //$_e = array(self::nf($res2->fields['SalesSun'] - $res2->fields['SharedSalesSun']), self::nf($res2->fields['SalesMon'] - $res2->fields['SharedSalesMon']), self::nf($res2->fields['SalesTue'] - $res2->fields['SharedSalesTue']), self::nf($res2->fields['SalesWed'] - $res2->fields['SharedSalesWed']), self::nf($res2->fields['SalesThu'] - $res2->fields['SharedSalesThu']), self::nf($res2->fields['SalesFri'] - $res2->fields['SharedSalesFri']), self::nf($res2->fields['SalesSat'] - $res2->fields['SharedSalesSat']));
                        /* week starting monday */
                        $_e = array(self::nf($res2->fields['SalesMon'] - $res2->fields['SharedSalesMon']), self::nf($res2->fields['SalesTue'] - $res2->fields['SharedSalesTue']), self::nf($res2->fields['SalesWed'] - $res2->fields['SharedSalesWed']), self::nf($res2->fields['SalesThu'] - $res2->fields['SharedSalesThu']), self::nf($res2->fields['SalesFri'] - $res2->fields['SharedSalesFri']), self::nf($res2->fields['SalesSat'] - $res2->fields['SharedSalesSat']), self::nf($res2->fields['SalesSun'] - $res2->fields['SharedSalesSun']));

                        $smarty->assign('twtotal', self::nf($res2->fields['SalesWeek']));
                        $smarty->assign('twshared', self::nf($res2->fields['SharedSalesWeek']));
                        $smarty->assign('twearned', self::nf($res2->fields['SalesWeek'] - $res2->fields['SharedSalesWeek']));
                        $smarty->assign('twsubscribers', $res2->fields['SubscribersWeek']);
                        $smarty->assign('twsubscriptions', $res2->fields['OrdersWeek']);
                        $smarty->assign('tws', json_encode($_u));
                        $smarty->assign('tw2', json_encode($_t));
                        $smarty->assign('sw2', json_encode($_s));
                        $smarty->assign('ew2', json_encode($_e));
                    } else {
                        $smarty->assign('twtotal', 0);
                        $smarty->assign('twshared', 0);
                        $smarty->assign('twearned', 0);
                        $smarty->assign('twsubscribers', 0);
                        $smarty->assign('twsubscriptions', 0);
                        $smarty->assign('tws', '[]');
                        $smarty->assign('tw2', '[]');
                        $smarty->assign('sw2', '[]');
                        $smarty->assign('ew2', '[]');
                    }
                    break;
            }
            $smarty->assign('pcurrency', self::getPayoutCurrency());

            $html .= $smarty->fetch('tpl_' . $side . '/tpl_subscriber/tpl_subgraphs.tpl');
        }
        $html .= '</div>'; //end content-wrap
        $html .= '</div>'; //end v-tabs
        $html .= '</div>';
        $html .= !$graphs ? self::spJS() : null;

        return $html;
    }
    /* week sql */
    private static function weekSQL($sdf, $edf)
    {
        global $class_filter, $class_database, $cfg;

        $uk  = ($cfg['is_be'] == 1 and isset($_GET['uk'])) ? $class_filter->clr_str($_GET['uk']) : (!$cfg['is_be'] ? $_SESSION['USER_KEY'] : false);
        $uid = false;

        if ($uk > 0) {
            $uid = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $uk);
        }

        return sprintf("SELECT
                        week( date(o.sub_time), 1 ) as WkNumber,
                        sum( if( weekday( date(o.sub_time) ) = 6, 1, 0 ) * o.pk_paid ) as SalesSun,
                        sum( if( weekday( date(o.sub_time) ) = 6, 1, 0 )) as OrdersSun,
                        sum( if( weekday( date(o.sub_time) ) = 0, 1, 0 ) * o.pk_paid ) as SalesMon,
                        sum( if( weekday( date(o.sub_time) ) = 0, 1, 0 )) as OrdersMon,
                        sum( if( weekday( date(o.sub_time) ) = 1, 1, 0 ) * o.pk_paid ) as SalesTue,
                        sum( if( weekday( date(o.sub_time) ) = 1, 1, 0 )) as OrdersTue,
                        sum( if( weekday( date(o.sub_time) ) = 2, 1, 0 ) * o.pk_paid ) as SalesWed,
                        sum( if( weekday( date(o.sub_time) ) = 2, 1, 0 )) as OrdersWed,
                        sum( if( weekday( date(o.sub_time) ) = 3, 1, 0 ) * o.pk_paid ) as SalesThu,
                        sum( if( weekday( date(o.sub_time) ) = 3, 1, 0 )) as OrdersThu,
                        sum( if( weekday( date(o.sub_time) ) = 4, 1, 0 ) * o.pk_paid ) as SalesFri,
                        sum( if( weekday( date(o.sub_time) ) = 4, 1, 0 )) as OrdersFri,
                        sum( if( weekday( date(o.sub_time) ) = 5, 1, 0 ) * o.pk_paid ) as SalesSat,
                        sum( if( weekday( date(o.sub_time) ) = 5, 1, 0 )) as OrdersSat,

                        sum( if( weekday( date(o.sub_time) ) = 6, 1, 0 ) * o.pk_paid_share ) as SharedSalesSun,
                        sum( if( weekday( date(o.sub_time) ) = 6, 1, 0 )) as OrdersSun,
                        sum( if( weekday( date(o.sub_time) ) = 0, 1, 0 ) * o.pk_paid_share ) as SharedSalesMon,
                        sum( if( weekday( date(o.sub_time) ) = 0, 1, 0 )) as OrdersMon,
                        sum( if( weekday( date(o.sub_time) ) = 1, 1, 0 ) * o.pk_paid_share ) as SharedSalesTue,
                        sum( if( weekday( date(o.sub_time) ) = 1, 1, 0 )) as OrdersTue,
                        sum( if( weekday( date(o.sub_time) ) = 2, 1, 0 ) * o.pk_paid_share ) as SharedSalesWed,
                        sum( if( weekday( date(o.sub_time) ) = 2, 1, 0 )) as OrdersWed,
                        sum( if( weekday( date(o.sub_time) ) = 3, 1, 0 ) * o.pk_paid_share ) as SharedSalesThu,
                        sum( if( weekday( date(o.sub_time) ) = 3, 1, 0 )) as OrdersThu,
                        sum( if( weekday( date(o.sub_time) ) = 4, 1, 0 ) * o.pk_paid_share ) as SharedSalesFri,
                        sum( if( weekday( date(o.sub_time) ) = 4, 1, 0 )) as OrdersFri,
                        sum( if( weekday( date(o.sub_time) ) = 5, 1, 0 ) * o.pk_paid_share ) as SharedSalesSat,
                        sum( if( weekday( date(o.sub_time) ) = 5, 1, 0 )) as OrdersSat,

                        sum( o.pk_paid ) as SalesWeek,
                        sum( o.pk_paid_share ) as SharedSalesWeek,
                        sum( 1 ) as OrdersWeek,
                        count(distinct(o.usr_id)) as SubscribersWeek
                        from
                        db_subpayouts o
                        where
                        %s
                        date(o.sub_time) BETWEEN '%s' AND '%s'
                        group by
                        week(date(o.sub_time), 1)", ($uid ? sprintf("o.usr_id_to='%s' and", $uid) : null), $sdf, $edf);
    }
    /* month sql */
    private static function monthSQL($sdf, $edf)
    {
        global $class_filter, $class_database, $cfg;

        $uk  = ($cfg['is_be'] == 1 and isset($_GET['uk'])) ? $class_filter->clr_str($_GET['uk']) : (!$cfg['is_be'] ? $_SESSION['USER_KEY'] : false);
        $uid = false;

        if ($uk > 0) {
            $uid = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $uk);
        }

        $mn = array();
        for ($i = 1; $i <= 31; $i++) {
            $mn[] = "sum( if( dayofmonth( date(o.sub_time) ) = " . $i . ", 1, 0 ) * o.pk_paid ) as Sales" . $i;
            $mn[] = "sum( if( dayofmonth( date(o.sub_time) ) = " . $i . ", 1, 0 ) * o.pk_paid_share ) as SharedSales" . $i;
            $mn[] = "sum( if( dayofmonth( date(o.sub_time) ) = " . $i . ", 1, 0 )) as Orders" . $i;
        }

        return sprintf("SELECT
                        month( date(o.sub_time)) as MnNumber,
                        %s,
                        sum( o.pk_paid ) as SalesMonth,
                        sum( o.pk_paid_share ) as SharedSalesMonth,
                        sum( 1 ) as OrdersMonth,
                        count(distinct(o.usr_id)) as SubscribersMonth
                        from
                        db_subpayouts o
                        where
                        %s
                        date(o.sub_time) BETWEEN '%s' AND '%s'
                        group by
                        month( date(o.sub_time) )", implode(',', $mn), ($uid ? sprintf("o.usr_id_to='%s' and", $uid) : null), $sdf, $edf);
    }
    /* year sql */
    private static function yearSQL($sdf, $edf)
    {
        global $class_filter, $class_database, $cfg;

        $uk  = ($cfg['is_be'] == 1 and isset($_GET['uk'])) ? $class_filter->clr_str($_GET['uk']) : (!$cfg['is_be'] ? $_SESSION['USER_KEY'] : false);
        $uid = false;

        if ($uk > 0) {
            $uid = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $uk);
        }

        $mn = array();
        for ($i = 1; $i <= 12; $i++) {
            $mn[] = "sum( if( month( date(o.sub_time) ) = " . $i . ", 1, 0 ) * o.pk_paid ) as Sales" . $i;
            $mn[] = "sum( if( month( date(o.sub_time) ) = " . $i . ", 1, 0 ) * o.pk_paid_share ) as SharedSales" . $i;
            $mn[] = "sum( if( month( date(o.sub_time) ) = " . $i . ", 1, 0 )) as Orders" . $i;
        }

        return sprintf("SELECT
                        year( date(o.sub_time)) as YeNumber,
                        %s,
                        sum( o.pk_paid ) as SalesYear,
                        sum( o.pk_paid_share ) as SharedSalesYear,
                        sum( 1 ) as OrdersYear,
                        count(distinct(o.usr_id)) as SubscribersYear
                        from
                        db_subpayouts o
                        where
                        %s
                        date(o.sub_time) BETWEEN '%s' AND '%s'
                        group by
                        year( date(o.sub_time) )", implode(',', $mn), ($uid ? sprintf("o.usr_id_to='%s' and", $uid) : null), $sdf, $edf);
    }
    /* number format */
    private static function nf($i)
    {
        return number_format($i, 2);
    }
    /* subscriber payouts js */
    private static function spJS()
    {
        global $cfg, $class_filter;

        $tab    = isset($_GET['tab']) ? $class_filter->clr_str($_GET['tab']) : false;
        $mobile = VHref::isMobile();

        $html .= '<script type="text/javascript">
            $(document).ready(function(){
            (function () {
                        [].slice.call(document.querySelectorAll(".tabs")).forEach(function (el) {
                                new CBPFWTabs(el);
                        }); })();
                        ' . ((isset($_GET['rg']) or isset($_GET['rp'])) ? '$("#t-nav li:first").removeClass("tab-current");' : null) . '
                        $("#t-nav ul li a").on("click", function(e){
                            e.preventDefault();window.location=$(this).attr("href");return false;
                        });
                        $(".swiper-top-tnav .swiper-slide a").on("click", function(e){
                            e.preventDefault();window.location=$(this).attr("href");return false;
                        });
                        ' . (($tab == 'section-paid' or $tab == 'section-all') ? '$("#tb li:first").removeClass("tab-current");' : null) . '
                        $(document).on("click", ".tabs ul#tb li", function() {
                            var t = $(this).find("a").attr("href").replace("#", "");
                            var currentLocation = window.location.href;

                            $("#custom-date-form").attr("action", currentLocation.replace(/&tab=section-[0-9a-zA-Z]+/g, "") + "&tab="+t).submit();
                        });
                $(".fviews-range").click(function(){
                    t = $(this);
                    i = t.attr("rel-id");
                    fk = t.attr("rel-fk");
                    sd = t.attr("rel-s");
                    ed = t.attr("rel-e");
                    u = "?a=' . ($cfg['is_be'] == 1 ? '1' : md5($_SESSION['USER_KEY'])) . '&t=' . $type . '&f=range&fk="+fk;
                    $("input[name=\'custom_date_start\']").val(sd);
                    $("input[name=\'custom_date_end\']").val(ed);
                    $("#custom-date-form-"+i).attr("action", u).submit();
                });
                $(".fviews").click(function(){
                    t = $(this);
                    i = t.attr("rel-id");
                    fk = t.attr("rel-fk");
                    f = t.attr("rel-f");
                    u = "?a=' . ($cfg['is_be'] == 1 ? '1' : md5($_SESSION['USER_KEY'])) . '&t=' . $type . '&f="+f+"&fk="+fk;
                    $("#custom-date-form-"+i).attr("action", u).submit();
                });
            });
            $(document).on({click: function () {
                var _id = "";
                if ($(".fancybox-wrap").width() > 0) {
                        _id = ".fancybox-inner ";
                }

                $(_id + ".responsive-accordion div.responsive-accordion-head").addClass("active");
                $(_id + ".responsive-accordion div.responsive-accordion-panel").addClass("active").show();
                $(_id + ".responsive-accordion i.responsive-accordion-plus").hide();
                $(_id + ".responsive-accordion i.responsive-accordion-minus").show();
                thisresizeDelimiter();
            }}, "#all-open");

            $(document).on({click: function () {
                var _id = "";
                if ($(".fancybox-wrap").width() > 0) {
                        _id = ".fancybox-inner ";
                }

                $(_id + ".responsive-accordion div.responsive-accordion-head").removeClass("active");
                $(_id + ".responsive-accordion div.responsive-accordion-panel").removeClass("active").hide();
                $(_id + ".responsive-accordion i.responsive-accordion-plus").show();
                $(_id + ".responsive-accordion i.responsive-accordion-minus").hide();
                $("#ct_entry").val("");
                thisresizeDelimiter();
            }}, "#all-close");

            $(document).on({click: function () {
                var t = $(this);
                var u = t.attr("rel-usr");
                var f = t.parent().parent().next();

                t.parent().mask("");
                //t.parent().load("?do=showstats&u="+u+"&tab=' . $tab . '", function(data){
                $.post("?do=showstats&u="+u+"&tab=' . $tab . '", f.serialize(), function(data) {
                    t.parent().unmask();
                    t.replaceWith(data);
                });
                thisresizeDelimiter();
            }}, ".load-stats");

        </script>';

        $html .= VGenerate::declareJS('$("#lb-wrapper ul.responsive-accordion").mouseover(function(){$(this).find(".responsive-accordion-head").addClass("h-msover");$(this).find(".responsive-accordion-panel").addClass("p-msover");}).mouseout(function(){$(this).find(".responsive-accordion-head").removeClass("h-msover");$(this).find(".responsive-accordion-panel").removeClass("p-msover");});');

        return $html;
    }

    /* get user stats */
    public static function userStats()
    {
        include 'f_core/config.backend.php';
        global $db, $cfg, $class_filter, $class_database, $language;

        $html = null;
        $uid  = (int) $_GET['u'];
        $tab  = isset($_GET['tab']) ? $class_filter->clr_str($_GET['tab']) : null;

        if ($_POST and $uid > 0) {
            $pp        = $db->execute(sprintf("SELECT `usr_user`, `usr_sub_share`, `usr_sub_perc`, `usr_sub_currency`, `usr_sub_email` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $uid));
            $sp        = $pp->fields['usr_sub_share'] == 1 ? $pp->fields['usr_sub_perc'] : $cfg['sub_shared_revenue'];
            $sub_email = $pp->fields['usr_sub_email'];
            $sd        = $class_filter->clr_str($_POST['custom_date_start']);
            $ed        = $class_filter->clr_str($_POST['custom_date_end']);

            switch ($tab) {
                default:
                case "section-unpaid":
                    $is_paid = "='0'";
                    break;
                case "section-paid":
                    $is_paid = "='1'";
                    break;
                case "section-all":
                    $is_paid = ">='0'";
                    break;
            }

            $sql = sprintf("SELECT
                            A.`db_id`, A.`usr_id`, A.`pk_id`, A.`pk_paid`, A.`sub_id`, A.`sub_time`, A.`txn_id`, A.`is_paid`,
                            A.`is_cancel`, A.`cancel_time`,
                            B.`usr_user`,
                            C.`pk_name`, C.`pk_priceunitname`
                            FROM `db_subpayouts` A, `db_accountuser` B, `db_subtypes` C
                            WHERE
                            A.`usr_id_to`='%s' AND
                            A.`usr_id`=B.`usr_id` AND
                            A.`pk_id`=C.`pk_id` AND
                            A.`is_paid`%s AND
                            date(A.`sub_time`) BETWEEN '%s' AND '%s'
                            ORDER BY A.`db_id` DESC;",
                $uid, $is_paid, $sd, $ed);

            $rs = $db->execute($sql);

            if ($rs->fields['db_id']) {
                $ids        = array();
                $dbs        = array();
                $multis     = array();
                $owed       = array();
                $owed[$uid] = 0;

                while (!$rs->EOF) {
                    $db_id         = $rs->fields['db_id'];
                    $db_uid        = $rs->fields['usr_id'];
                    $db_user       = $rs->fields['usr_user'];
                    $db_pack       = $rs->fields['pk_name'];
                    $db_subid      = $rs->fields['sub_id'];
                    $db_subdate    = $rs->fields['sub_time'];
                    $db_paid       = $rs->fields['pk_paid'];
                    $is_paid       = $rs->fields['is_paid'];
                    $is_cancel     = $rs->fields['is_cancel'];
                    $db_canceldate = $rs->fields['cancel_time'];
                    $db_paidc      = $pp->fields['usr_sub_share'] == 1 ? $pp->fields['usr_sub_currency'] : $rs->fields['pk_priceunitname'];
                    $db_perc       = $sp;

                    if (!is_array($ids[$db_user])) {
                        $ids[$db_user] = array();

                        $db_owed = ($db_perc / 100) * $db_paid;
                        $db_fee  = self::fee($db_owed);
                        $owed[$uid] += $db_owed - $db_fee;

                        $st = (($tab == 'section-all' or $tab == 'section-paid') and $is_paid == 1) ? ' - <span class="conf-green"><i class="icon-check"></i> ' . $language['backend.sub.dash.payout.paid'] . '</span>' : null;
                        $st .= $is_cancel == 1 ? ' <span class="err-red">Canceled ' . $db_canceldate . '</span>' : null;
                        $ids[$db_user][] = $language['backend.sub.dash.payout.subscription'] . '<b>' . $db_pack . '</b> (' . $db_subdate . ')<br><i class="icon-check"></i> <b>' . $db_paid . ' ' . $db_paidc . ' ' . $language['backend.sub.dash.payout.total'] . '</b><br><i class="icon-check"></i> <b>' . $db_owed . ' ' . $db_paidc . ' share (' . $db_perc . '%)</b><br><i class="icon-check"></i> <b>' . $db_fee . ' ' . $db_paidc . ' ' . $language['backend.sub.dash.payout.ppfee'] . '</b><br><i class="icon-point-right"></i> <b>' . ($db_owed - $db_fee) . ' ' . $db_paidc . ' ' . $language['backend.sub.dash.payout.owed'] . '</b>' . $st;
                        $dbs[]           = $db_id;
                    } else {
                        $multis[$db_user] = array();

                        $db_owed = ($db_perc / 100) * $db_paid;
                        $db_fee  = self::fee($db_owed);
                        $owed[$uid] += $db_owed - $db_fee;

                        $st = (($tab == 'section-all' or $tab == 'section-paid') and $is_paid == 1) ? ' - <span class="conf-green"><i class="icon-check"></i> ' . $language['backend.sub.dash.payout.paid'] . '</span>' : null;
                        $st .= $is_cancel == 1 ? ' <span class="err-red">Canceled ' . $db_canceldate . '</span>' : null;
                        $ids[$db_user][] = $language['backend.sub.dash.payout.subscription'] . '<b>' . $db_pack . '</b> (' . $db_subdate . ')<br><i class="icon-check"></i> <b>' . $db_paid . ' ' . $db_paidc . ' ' . $language['backend.sub.dash.payout.total'] . '</b><br><i class="icon-check"></i> <b>' . $db_owed . ' ' . $db_paidc . ' share (' . $db_perc . '%)</b><br><i class="icon-check"></i> <b>' . $db_fee . ' ' . $db_paidc . ' ' . $language['backend.sub.dash.payout.ppfee'] . '</b><br><i class="icon-point-right"></i> <b>' . ($db_owed - $db_fee) . ' ' . $db_paidc . ' ' . $language['backend.sub.dash.payout.owed'] . '</b> - ' . $st;
                        $dbs[]           = $db_id;
                    }

                    $rs->MoveNext();
                }
                $s = 1;
                foreach ($ids as $sub => $subs) {
//invoices
                    $ss = 1;

                    $html .= '<div style="padding: 5px 0;"><label>' . $s . '</label>. <a href="javascript:;" class="filter-tag f-16" onclick="$(\'.' . str_replace(' ', '-', $sub) . '-' . $uid . '-subs\').stop().slideToggle(\'fast\');">' . $sub . '</a></div>';
                    foreach ($subs as $subinfo) {
                        $html .= '<div class="' . str_replace(' ', '-', $sub) . '-' . $uid . '-subs" style="display: none; margin-left: 30px; padding: 2px 0;"><label>' . $ss . '.</label> ' . $subinfo . '</div>';
                        $ss += 1;
                    }

                    $s += 1;
                }

                $tt = 0;
                foreach ($owed as $uu) {
                    $tt += $uu;
                }
                $pay_now = $sub_email == '' ? ' <span class="err-red">' . $language['backend.sub.dash.payout.noaddr'] . '</span>' : null;

                if ($cfg['is_be'] == 1) {
                    $db->execute(sprintf("DELETE FROM `db_subinvoices` WHERE `create_date` < DATE_SUB(NOW(), INTERVAL 2 HOUR) AND `sub_paid`='0';"));

                    $sr = serialize($dbs);
                    $c  = $db->execute(sprintf("SELECT `db_id`, `sub_paid`, `pay_date` FROM `db_subinvoices` WHERE `usr_id`='%s' AND `sub_payout`='%s';", $uid, $sr));
                    if (!$c->fields['db_id']) {
                        if ($tab == '' or $tab == 'section-unpaid') {
                            $ins = array('usr_id' => $uid, 'sub_amount' => $tt, 'sub_currency' => $db_paidc, 'sub_payout' => $sr, 'create_date' => date("Y-m-d H:i:s"));

                            $class_database->doInsert('db_subinvoices', $ins);
                            $done     = $db->insert_Id();
                            $is_paid  = 0;
                            $pay_date = null;
                        }
                    } else {
                        $done     = $c->fields['db_id'];
                        $is_paid  = $c->fields['sub_paid'];
                        $pay_date = $c->fields['pay_date'];
                    }

                    $pp_base  = $cfg['paypal_test'] == 1 ? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com';
                    $p_notify = urlencode($cfg['main_url'] . '/' . $backend_access_url . '/' . VHref::getKey('be_subscribers') . '?do=ipn');
                    $p_cancel = urlencode($cfg['main_url'] . '/' . $backend_access_url . '/' . VHref::getKey('be_subscribers') . '?rp=1&ppcancel');
                    $p_return = urlencode($cfg['main_url'] . '/' . $backend_access_url . '/' . VHref::getKey('be_subscribers') . '?rp=1');
                    $pk_key   = md5($cfg['global_salt_key'] . $done);

                    $pp_link_shared = sprintf("%s/cgi-bin/webscr?cmd=_xclick&business=%s&return=%s&cancel_return=%s&notify_url=%s&tax=0&currency_code=%s&item_name=%s&item_number=%s&quantity=1&amount=%s&custom=sub",
                        $pp_base, $sub_email, $p_return, $p_cancel, $p_notify, $db_paidc, urlencode($cfg['website_shortname'] . ' ' . $language['backend.sub.dash.payout.subpayout']), urlencode('s|' . $done . '|' . $pk_key), $tt);

                    $btn     = '<button name="pay_now" onclick="window.open(\'' . $pp_link_shared . '\');return false;" class="button-grey search-button form-button" style="margin-top: 10px;" type="button" value="1" rel-usr="" onfocus="blur();"><i class="icon-paypal"></i> <span>' . $language['backend.sub.dash.payout.paynow'] . '</span></button>';
                    $pay_now = $sub_email != '' ? sprintf("%s", $btn) : ' <span class="err-red">' . $language['backend.sub.dash.payout.noaddr'] . '</span>';
                    $pay_now = ($is_paid == 1 or $tab == 'section-paid') ? '<span class="conf-green"><i class="icon-check"></i> ' . $language['backend.sub.dash.payout.paid'] . ' ' . ($pay_date != '0000-00-00 00:00:00' ? $pay_date : null) . '</span>' : $pay_now;
                    $pay_now = $tab == 'section-all' ? null : $pay_now;
                }

                $html .= '<hr><br>';
                $html .= '<div>' . $language['backend.sub.dash.payout.owed.total'] . '<b>' . $owed[$uid] . ' ' . $db_paidc . '</b>' . $pay_now . '</div>';
            }
        }
        return $html;
    }
    private static function sum()
    {
        return array_sum(func_get_args());
    }
    /* generate affiliate badge */
    public static function affiliateBadge($usr_affiliate, $af_badge)
    {
        global $language;

        if ($usr_affiliate == 1 and $af_badge != '') {
            return '<i class="affiliate-icon ' . $af_badge . '" rel="tooltip" title="' . $language['frontend.global.verified'] . '"></i> ';
        }
    }
    /* generate affiliate badge */
    public static function getAffiliateBadge($usr_key)
    {
        global $db, $class_filter;

        $usr_key = $class_filter->clr_str($usr_key);

        $q = $db->execute(sprintf("SELECT `affiliate_badge` FROM `db_accountuser` WHERE `usr_key`='%s' AND `usr_affiliate`='1' LIMIT 1;", $usr_key));
        $b = $q->fields['affiliate_badge'];

        if ($b != '') {
            return '<i class="affiliate-icon ' . $b . '"></i> ';
        }
    }
    /* new nav menu */
    private static function nav_menu()
    {
        global $href, $language, $cfg;

        $html = '
        <div class="swiper-ph swiper-ph-tnav" style="height:43px !important"></div>
        <div class="swiper-top swiper-top-tnav mb-0" style="display:' . VGenerate::ssd() . '">
            <div class="swiper-button-prev swiper-button-prev-tnav"' . VGenerate::sso() . '></div>
            <div class="swiper-button-next swiper-button-next-tnav"' . VGenerate::sso() . '></div>
            <div class="swiper swiper-tnav">
                <div class="swiper-wrapper">
                    <div id="account-menu-entry13" class="swiper-slide' . ((!isset($_GET['rg']) and !isset($_GET['rp'])) ? ' swiper-slide-current' : null) . '" rel-m="' . $href['subscribers'] . '"><a class="" href="' . $cfg['main_url'] . '/' . $href['subscribers'] . '"><span><i class="icon-user"></i> ' . $language['account.entry.overview'] . '</span></a>
                    </div>
                ' . ($_SESSION['USER_PARTNER'] == 1 ? '
                    <div id="account-menu-entry9" class="swiper-slide' . (isset($_GET['rg']) ? ' swiper-slide-current' : null) . '" rel-m="' . $href['subscribers'] . '"><a class="" href="' . $cfg['main_url'] . '/' . $href['subscribers'] . '?rg=' . md5($_SESSION['USER_KEY']) . '"><span><i class="icon-bars"></i> ' . $language['account.entry.act.graph'] . '</span></a>
                    </div>
                    <div id="account-menu-entry12" class="swiper-slide' . (isset($_GET['rp']) ? ' swiper-slide-current' : null) . '" rel-m="' . $href['subscribers'] . '"><a class="" href="' . $cfg['main_url'] . '/' . $href['subscribers'] . '?rp=' . md5($_SESSION['USER_KEY']) . '"><span><i class="icon-paypal"></i> ' . $language['account.entry.payout.rep'] . '</span></a>
                    </div>
                ' : null) . '
                </div>
            </div>
        </div>
        ';

        return $html;
    }
    /* generate payout accordions */
    private static function tpl_payout_accordion($res, $sdf, $edf, $subs_min, $subs_max, $currency = 'USD')
    {
        include 'f_core/config.backend.php';
        global $language, $class_database, $class_filter, $class_language, $db, $cfg, $smarty;

        $type = 'sub';
        $f    = isset($_GET['f']) ? $class_filter->clr_str($_GET['f']) : false;
        $rp   = isset($_GET['rp']) ? $class_filter->clr_str($_GET['rp']) : false;
        $tab  = isset($_GET['tab']) ? $class_filter->clr_str($_GET['tab']) : false;

        switch ($f) {
            case "":
                $_f = $language['account.entry.f.thismonth'];
                break;
            case "today":
                $_f = !$rp ? self::tpl_filter_text($language['account.entry.f.today'], 'f', true) : $language['account.entry.f.lastmonth'];
                break;
            case "yesterday":
            case "thisweek":
            case "lastweek":
            case "thismonth":
            case "lastmonth":
            case "thisyear":
            case "lastyear":
            case "last3months":
            case "last6months":
                $_f = $language["account.entry.f." . $f];
                break;
            case "date":
                $_f = isset($_POST['custom_date']) ? $class_filter->clr_str($_POST['custom_date']) : $language['account.entry.f.date'];
                break;
            case "range":
                $_f = (isset($_POST['custom_date_start']) and isset($_POST['custom_date_end'])) ? $class_filter->clr_str($_POST['custom_date_start']) . ' <span class="red">--</span> ' . $class_filter->clr_str($_POST['custom_date_end']) : $language['account.entry.f.range'];
                $_f = (isset($_POST['custom_date_start']) and isset($_POST['custom_date_end'])) ? date('M j', strtotime($class_filter->clr_str($_POST['custom_date_start']))) . ' <span class="red">--</span> ' . date('M j, o', strtotime($class_filter->clr_str($_POST['custom_date_end']))) : $language['account.entry.f.range'];
                break;
        }

        switch ($tab) {
            default:
            case "section-unpaid":
                $is_paid = "='0'";
                break;
            case "section-paid":
                $is_paid = "='1'";
                break;
            case "section-all":
                $is_paid = ">='0'";
                break;
        }

        $html .= '<div class="entry-list vs-column full">';
        if ($res->fields['usr_id_to']) {
            while (!$res->EOF) {
                $usr_user  = $res->fields['usr_user'];
                $usr_dname = $res->fields['usr_dname'];
                $ch_dname  = $res->fields['ch_dname'];
                $usr_id_to = $res->fields['usr_id_to'];
                $usr_perc  = $res->fields['usr_sub_share'] == 1 ? $res->fields['usr_sub_perc'] : $cfg['sub_shared_revenue'];
                $tt        = $db->execute(sprintf("SELECT COUNT(`db_id`) AS `subscriptions` FROM `db_subpayouts` WHERE `usr_id_to`='%s' AND `sub_time` BETWEEN '%s' AND '%s' AND `is_paid`%s GROUP BY `usr_id`;", $usr_id_to, $sdf, $edf, $is_paid));
                $snr1      = $tt->recordcount();
                $tt        = $db->execute(sprintf("SELECT COUNT(`db_id`) AS `subscriptions`, SUM(`" . ($cfg['is_be'] == 1 ? 'pk_paid' : 'pk_paid') . "`) AS `totals` FROM `db_subpayouts` WHERE `usr_id_to`='%s' AND `sub_time` BETWEEN '%s' AND '%s' AND `is_paid`%s GROUP BY `usr_id_to`;", $usr_id_to, $sdf, $edf, $is_paid));
                $snr2      = $tt->fields['subscriptions'];
                $snr3      = round($tt->fields['totals'], 2);
                $sfee      = self::fee($snr3);
                /* show at least n subscriptions */
                $title = sprintf("<span class=\"entry-left\"><i class=\"icon-user\"></i> <span class=\"bold\">%s (%s)</span></span> <span class=\"entry-right\"><span class=\"greyed-out\">[%s " . $language['backend.sub.dash.payout.subscribers'] . ", %s " . $language['backend.sub.dash.payout.subscriptions'] . "]</span> <span class=\"rep-amt\">%s %s " . $language['backend.sub.dash.payout.total'] . "</span></span>", $usr_user, ($usr_dname != '' ? $usr_dname : ($ch_dname != '' ? $ch_dname : $usr_user)), $snr1, $snr2, $snr3, $currency);

                if (($subs_min > 0 and $snr2 < $subs_min) or ($subs_max > 0 and $snr2 > $subs_max)) {
                    $html .= '';
                } else {

                    $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
                    $html .= '<li>';
                    $html .= '<div class="responsive-accordion-head">';
                    $html .= '<article><h3 class="content-title">' . $title . '</h3>';
                    $html .= '<div class="place-right expand-entry"><i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-plus" style="display: block;"></i><i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-minus" style="display: none;"></i></div>';
                    $html .= '</article>';
                    $html .= '</div>';
                    $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                    $html .= '<div class="vs-column full sub-nrs">';
                    $html .= '<p>' . str_replace(array('##USER##', '##PERC##'), array($usr_user, $usr_perc), $language['backend.sub.dash.payout.receiving']) . '</p>';
                    $html .= '<div><button name="load_stats" class="button-grey search-button form-button load-stats" type="button" value="1" rel-usr="' . $usr_id_to . '" onfocus="blur();"><span><i class="icon-list"></i> ' . $language['backend.sub.dash.payout.sublist'] . '</span></button></div>';
                    $html .= '</div>';

                    $html .= '<form id="custom-date-form-' . $res->fields['db_id'] . '" name="custom_date_form_db" method="post" action="" target="_blank" class="no-display">
                        <input type="hidden" name="custom_date_submit" value="1">
                                    <input type="hidden" name="custom_date" value="">
                                    <input type="hidden" name="custom_date_start" value="' . $sdf . '">
                                    <input type="hidden" name="custom_date_end" value="' . $edf . '">
                                    <input type="hidden" name="custom_country" value="">
                                    <input type="hidden" name="subs_limit_min" value="' . $subs_min . '">
                                    <input type="hidden" name="subs_limit_max" value="' . $subs_max . '">
                                </form>';
                    $html .= '</div>';
                    $html .= '</li>';
                    $html .= '</ul>';

                }

                @$res->MoveNext();
            }
        } else {
            $html .= '<br><div class="no-content"><i class="icon-search"></i> ' . $language['backend.sub.dash.payout.nores'] . '</div>';
        }
        $html .= '</div>';

        return $html;
    }
    /* cron script, check and update followers and subs numbers */
    public static function update_followers_subs()
    {
        global $cfg, $db, $class_database;
        $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_followcount`=0, `usr_subcount`=0"));

        $rs = $db->execute(sprintf("SELECT DISTINCT(`usr_id`), COUNT(`sub_id`) AS `total` FROM `db_followers` GROUP BY `usr_id`;"));
        if ($rs->fields['usr_id']) {
            while (!$rs->EOF) {
                $usr_id = $rs->fields['usr_id'];
                $total  = $rs->fields['total'];

                $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_followcount`='%s' WHERE `usr_id`='%s' LIMIT 1;", $total, $usr_id));

                $rs->MoveNext();
            }
        }

        $rs = $db->execute(sprintf("SELECT DISTINCT(`usr_id`), COUNT(`sub_id`) AS `total` FROM `db_subscribers` GROUP BY `usr_id`;"));
        if ($rs->fields['usr_id']) {
            while (!$rs->EOF) {
                $usr_id = $rs->fields['usr_id'];
                $total  = $rs->fields['total'];

                $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_subcount`='%s' WHERE `usr_id`='%s' LIMIT 1;", $total, $usr_id));

                $rs->MoveNext();
            }
        }

        $rs = $db->execute(sprintf("SELECT DISTINCT(`usr_id_to`), COUNT(`usr_id`) AS `total` FROM `db_subtemps` WHERE `active`='1' GROUP BY `usr_id_to`;"));
        if ($rs->fields['usr_id_to']) {
            while (!$rs->EOF) {
                $usr_id = $rs->fields['usr_id_to'];
                $total  = $rs->fields['total'];

                $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_subcount`=`usr_subcount`+%s WHERE `usr_id`='%s' LIMIT 1;", $total, $usr_id));

                $rs->MoveNext();
            }
        }
    }
    /* cron script, check and update expired subs */
    public static function check_expired_subs()
    {
        global $cfg, $db, $class_database;

        $f  = 0;
        $t  = 0;
        $dd = date("Y-m-d H:i:s");
        $rs = $db->execute(sprintf("SELECT
                            A.`db_id`, A.`usr_id`, A.`usr_id_to`, A.`subscriber_id`,
                            B.`usr_user`
                            FROM
                            `db_subusers` A, `db_accountuser` B
                            WHERE
                            A.`pk_id`>'0' AND
                            A.`expire_time`<='%s' AND
                            A.`usr_id`=B.`usr_id`;",
            $dd));

        if ($rs->fields['db_id']) {
            $vview = new VView;

            while (!$rs->EOF) {
                $usr_id    = $rs->fields['usr_id'];
                $usr_user  = $rs->fields['usr_user'];
                $usr_id_to = $rs->fields['usr_id_to'];
                $sub_id    = $rs->fields['subscriber_id'];
                $sub_act   = VView::chSubscribe(1, $usr_id_to, false, $usr_id, $usr_user);

                if ($db->Affected_Rows() > 0) {
                    $db->execute(sprintf("UPDATE `db_subusers` SET `pk_id`='0' WHERE `db_id`='%s' LIMIT 1;", $rs->fields['db_id']));

                    $f += 1;
                }

                $rs->MoveNext();
            }
        }

        $rs = $db->execute(sprintf("SELECT
                            COUNT(A.`db_id`) AS `total`
                            FROM
                            `db_subtemps` A
                            WHERE
                            A.`pk_id`>'0' AND
                            A.`expire_time`<='%s' AND
                            A.`active`='1';",
            $dd));

        if ($rs->fields['total'] > 0) {
            $rs = $db->execute(sprintf("UPDATE `db_subtemps` SET `pk_id`='0', `active`='0' WHERE `expire_time`<='%s';", $dd));

            $t = $db->Affected_Rows();
        }

        echo $f . ' subs processed' . ' / ' . $t . ' temps processed';
    }
    /* sync live vods */
    public static function sync_vods()
    {
        global $db, $cfg, $class_database, $class_filter;

        $fk    = array();
        $pcfg  = $class_database->getConfigurations('live_cron_salt');
        $ssk   = $pcfg['live_cron_salt'];
        $date  = date("Y-m-d");
        $ydate = date("Y-m-d", strtotime('-1 days'));

        if (!isset($_GET['s'])) {
            return;
        }

        $str = $class_filter->clr_str($_GET['s']);
        if ($str != md5($date . $ssk) and $str != md5($ydate . $ssk)) {
            return;
        }

        $rs = $db->execute(sprintf("SELECT `file_key` FROM `db_livetemps`;"));
        if ($rs->fields['file_key']) {
            while (!$rs->EOF) {
                $fk[] = $rs->fields['file_key'];

                $rs->MoveNext();
            }
        }

        echo json_encode($fk);
    }
    /* sync live vods disk usage */
    public static function sync_df()
    {
        global $db, $cfg, $class_database, $class_filter;

        $allowed = array('127.0.0.1');

        $fk    = array();
        $pcfg  = $class_database->getConfigurations('live_cron_salt');
        $ssk   = $pcfg['live_cron_salt'];
        $date  = date("Y-m-d");
        $ydate = date("Y-m-d", strtotime('-1 days'));

        if (!isset($_GET['s']) and !in_array($_SERVER[REM_ADDR], $allowed)) {
            return;
        }

        $str = $class_filter->clr_str($_GET['s']);
        if ($str != md5($date . $ssk) and $str != md5($ydate . $ssk)) {
            return;
        }

        $srv = $class_filter->clr_str($_GET['a']);
        $df  = $class_filter->clr_str($_GET['_']);

        $update = $db->execute(sprintf("UPDATE `db_liveservers` SET `srv_freespace`='%s' WHERE `srv_slug`='%s' LIMIT 1;", $df, $srv));
    }
    /* sync subs and follows with chat server */
    public static function sync_subs()
    {
        global $db, $cfg, $class_database, $class_filter;

        $fu    = array();
        $su    = array();
        $pcfg  = $class_database->getConfigurations('live_cron_salt');
        $ssk   = $pcfg['live_cron_salt'];
        $date  = date("Y-m-d");
        $ydate = date("Y-m-d", strtotime('-1 days'));

        if (!isset($_GET['s'])) {
            return;
        }

        $str = $class_filter->clr_str($_GET['s']);
        if ($str != md5($date . $ssk) and $str != md5($ydate . $ssk)) {
            return;
        }

        /* get followers */
        if ($cfg['user_follows'] == 1) {
            $fu[] = array();
            $rs   = $db->execute(sprintf("SELECT A.`db_id`, A.`usr_id`, A.`sub_id`, B.`usr_user` FROM `db_followers` A, `db_accountuser` B WHERE A.`sub_id`=B.`usr_id` AND A.`usr_id`>'0';"));

            if ($rs->fields['db_id']) {
                while (!$rs->EOF) {
                    $fu[$rs->fields['usr_id']][] = $rs->fields['usr_user'];

                    $rs->MoveNext();
                }
            }
        }
        /* get subscribers */
        if ($cfg['user_subscriptions'] == 1) {
            $su[] = array();
            $rs   = $db->execute(sprintf("SELECT
                            A.`usr_id_to`, B.`usr_user`
                            FROM
                            `db_subusers` A, `db_accountuser` B
                            WHERE
                            A.`pk_id`>'0' AND
                            A.`expire_time`>='%s' AND
                            A.`usr_id`=B.`usr_id` AND
                            B.`usr_status`='1';",
                date("Y-m-d H:i:s")));

            if ($rs->fields['usr_id_to']) {
                while (!$rs->EOF) {
                    $uu        = $rs->fields['usr_id_to'];
                    $su[$uu][] = $rs->fields['usr_user'];

                    $rs->MoveNext();
                }
            }

            $rs = $db->execute(sprintf("SELECT
                            A.`usr_id_to`, B.`usr_user`
                            FROM
                            `db_subtemps` A, `db_accountuser` B
                            WHERE
                            A.`pk_id`>'0' AND
                            A.`expire_time`>='%s' AND
                            A.`usr_id`=B.`usr_id` AND
                            B.`usr_status`='1';",
                date("Y-m-d H:i:s")));

            if ($rs->fields['usr_id_to']) {
                while (!$rs->EOF) {
                    $uu = $rs->fields['usr_id_to'];

                    if (!is_array($su[$uu])) {
                        $su[$uu] = array();
                    }

                    if (!in_array($rs->fields['usr_user'], $su[$uu])) {
                        $su[$uu][] = $rs->fields['usr_user'];
                    }

                    $rs->MoveNext();
                }
            }
        }

        $final = array("followers" => $fu, "subscribers" => $su);

        echo json_encode($final);
    }
    /* unsubscribe request */
    public static function unsub_request($upage_id, $uid = false)
    {
        global $db;

        if (!$uid) {
            $uid = (int) $_SESSION['USER_ID'];
        }

        $upage_id = (int) $upage_id;

        if ($uid == 0 or $upage_id == 0 or $uid == $upage_id) {
            return;
        }

        $ss = $db->execute(sprintf("SELECT `subscriber_id` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' LIMIT 1;", $uid, $upage_id));

        if ($ss->fields['subscriber_id']) {
            return self::change_subscription_status($ss->fields['subscriber_id'], 'Cancel');
        }
    }
    /* Performs an Express Checkout NVP API operation as passed in $action */
    private static function change_subscription_status($profile_id, $action)
    {
        global $cfg, $db, $class_database;

        $pcfg = $class_database->getConfigurations('paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email,paypal_api_user,paypal_api_pass,paypal_api_sign');
        $purl = $pcfg['paypal_test'] == 1 ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';

        $au = $pcfg['paypal_api_user'];
        $ap = $pcfg['paypal_api_pass'];
        $as = $pcfg['paypal_api_sign'];

        $api_request = 'USER=' . urlencode($au)
        . '&PWD=' . urlencode($ap)
        . '&SIGNATURE=' . urlencode($as)
        . '&VERSION=98'
        . '&METHOD=ManageRecurringPaymentsProfileStatus'
        . '&PROFILEID=' . urlencode($profile_id)
        . '&ACTION=' . urlencode($action)
        . '&NOTE=' . urlencode('Profile action: ' . $action . ' - ' . $profile_id);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $purl); // For live transactions, change to 'https://api-3t.paypal.com/nvp'
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        // Uncomment these to turn off server and peer verification

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        // Set the API parameters for this transaction
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_request);
        // Request response from PayPal
        $response = curl_exec($ch);
        // If no response was received from PayPal there is no point parsing the response
        if (!$response) {
            curl_close($ch);
            return 'Calling PayPal to change_subscription_status failed: ' . curl_error($ch) . '(' . curl_errno($ch) . ')';
        } else {
            curl_close($ch);
            // An associative array is more usable than a parameter string
            parse_str($response, $parsed_response);

            return $parsed_response;
        }
    }
    /* generate sorting section */
    private static function tpl_filter()
    {
        global $language, $cfg, $class_filter, $smarty;

        $type = 'sub';

        $f  = isset($_GET['f']) ? $class_filter->clr_str($_GET['f']) : false;
        $r  = isset($_GET['r']) ? $class_filter->clr_str($_GET['r']) : false;
        $c  = isset($_GET['c']) ? $class_filter->clr_str($_GET['c']) : false;
        $a  = isset($_GET['a']) ? $class_filter->clr_str($_GET['a']) : false;
        $o  = isset($_GET['o']) ? $class_filter->clr_str($_GET['o']) : false;
        $rp = isset($_GET['rp']) ? $class_filter->clr_str($_GET['rp']) : false;
        $rg = isset($_GET['rg']) ? $class_filter->clr_str($_GET['rg']) : false;

        $_s = array();

        switch ($f) {
            case "":
                $_s[] = !$rg ? self::tpl_filter_text($language['account.entry.f.thismonth'], 'f', true) : self::tpl_filter_text($language['backend.sub.label.week'], 'f', true);
                break;
            case "week":
                $_s[] = self::tpl_filter_text((isset($_GET['w']) ? $language['backend.sub.label.wnr'] . (int) $_GET['w'] : $language["backend.sub.label." . $f]), 'f', false);
                break;
            case "month":
                $_s[] = self::tpl_filter_text((isset($_GET['m']) ? $language['backend.sub.label.mnr'] . $language['backend.sub.label.months'][(int) $_GET['m']] : $language["backend.sub.label." . $f]), 'f', false);
                break;
            case "year":
                $_s[] = self::tpl_filter_text((isset($_GET['y']) ? $language['backend.sub.label.ynr'] . (int) $_GET['y'] : $language["backend.sub.label." . $f]), 'f', false);
                break;
            case "today":
                $_s[] = !$rp ? self::tpl_filter_text($language['account.entry.f.today'], 'f', true) : self::tpl_filter_text($language['account.entry.f.lastmonth'], 'f', true);
                break;
            case "yesterday":
            case "thisweek":
            case "lastweek":
            case "thismonth":
            case "lastmonth":
            case "thisyear":
            case "lastyear":
            case "last3months":
            case "last6months":
                $_s[] = self::tpl_filter_text($language["account.entry.f." . $f], 'f', (($rp and $f == 'thismonth') ? true : false));
                break;
            case "date":
                $_v   = isset($_POST['custom_date']) ? $class_filter->clr_str($_POST['custom_date']) : $language['account.entry.f.date'];
                $_s[] = self::tpl_filter_text($_v, 'f');
                break;
            case "range":
                $_v   = (isset($_POST['custom_date_start']) and isset($_POST['custom_date_end'])) ? $class_filter->clr_str($_POST['custom_date_start']) . ' <span class="red">---</span> ' . $class_filter->clr_str($_POST['custom_date_end']) : $language['account.entry.f.range'];
                $_s[] = self::tpl_filter_text($_v, 'f');
                break;
        }
        if ($o) {
            $_s = array();
        }

        if (isset($_GET['fk']) and $_GET['fk'] != '') {
            $_s[] = self::tpl_filter_text($language['account.entry.search.res'], 'fk');
        }
        if (isset($_GET['uk']) and $_GET['uk'] != '') {
            $_s[] = self::tpl_filter_text($language['account.entry.search.res'], 'uk');
        }

        $wnr = array();
        if ($rg) {
            if ($f == '' or $f == 'week') {
                for ($i = 1; $i <= 52; $i++) {
                    $wnr[] = sprintf("<li><a rel-w=\"%s\" href=\"?rg=1&t=sub&f=week&w=%s\"%s>%s</a></li>", $i, $i, (((isset($_GET['w']) and (int) $_GET['w'] == $i) or (!isset($_GET['w']) and date("W") == $i)) ? ' class="active"' : null), $i);
                }
            } elseif ($f == 'month') {
                $mn = $language['backend.sub.label.months'];
                for ($i = 1; $i <= 12; $i++) {
                    $wnr[] = sprintf("<li><a rel-m=\"%s\" href=\"?rg=1&t=sub&f=month&m=%s\"%s>%s</a></li>", $i, $i, (((isset($_GET['m']) and (int) $_GET['m'] == $i) or (!isset($_GET['m']) and date("m") == $i)) ? ' class="active"' : null), $mn[$i]);
                }
                $smarty->assign('mn', $mn);
            } elseif ($f == 'year') {
                $yy = date("Y");
                for ($i = $yy - 5; $i <= $yy + 5; $i++) {
                    $wnr[] = sprintf("<li><a rel-y=\"%s\" href=\"?rg=1&t=sub&f=year&y=%s\"%s>%s</a></li>", $i, $i, (((isset($_GET['y']) and (int) $_GET['y'] == $i) or (!isset($_GET['y']) and date("Y") == $i)) ? ' class="active"' : null), $i);
                }
            }
        }

        $subs_min = isset($_SESSION['subs_min']) ? $_SESSION['subs_min'] : 0;
        $subs_max = isset($_SESSION['subs_max']) ? $_SESSION['subs_max'] : 0;

        $_i_fe = '<div class="pull-right" rel="tooltip" title="Show/hide all filters"><i class="toggle-all-filters icon icon-eye2" onclick="if($(\'#search-boxes\').is(\':hidden\')){$(\'section.filter,section.inner-search,#search-boxes\').css({\'display\':\'inline-block\'});if(!$(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}else{$(\'section.filter,section.inner-search,#search-boxes\').hide();if($(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}"></i></div>';
        $_i_fe = null;
        $_i_be = '<i class="toggle-all-filters pull-right icon icon-eye2" onclick="if($(\'#search-boxes\').is(\':hidden\')){$(\'section.filter,section.inner-search,#search-boxes\').css({\'display\':\'inline-block\'});if(!$(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}else{$(\'section.filter,section.inner-search,#search-boxes\').hide();if($(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}" rel="tooltip" title="Show/hide all filters"></i>';
        $_i    = $cfg['is_be'] == 1 ? $_i_be : $_i_fe;

        $html = !$cfg['is_be'] ? self::nav_menu() : null;
        $html .= '
                                <article>
                                        <h3 class="content-title"><i class="icon-' . (isset($_GET['g']) ? 'globe' : (isset($_GET['o']) ? 'bars' : (isset($_GET['rp']) ? 'paypal' : 'bars'))) . '"></i>' . (isset($_GET['g']) ? $language['account.entry.act.maps'] : (isset($_GET['o']) ? $language['account.entry.act.comp'] : (isset($_GET['rp']) ? $language['account.entry.payout.rep'] : $language['backend.sub.label.stats']))) . $_i . '</h3>
                                        <section class="filter">
                                                ' . (!$o ? '
                                                <div class="btn-group viewType vmtop pull-right">
                                                    <button rel="tooltip" onclick="$(\'#time-sort-filters\').stop().slideToggle(\'fast\');' . (($rg and ($f == '' or $f == 'week' or $f == 'month' or $f == 'year')) ? '$(\'#week-sort-filters\').stop().slideToggle(\'fast\');' : null) . '$(\'.view-mode-filters\').toggleClass(\'active\');" title="' . $language['account.entry.filters.show'] . '" class="viewType_btn viewType_btn-default view-mode-filters ' . (isset($_GET['f']) ? 'active' : null) . '" rel-t="filter"><span class="icon-filter"></span> <span class="shf">' . $language['account.entry.filters.show'] . '</span></button>
                                                </div>
                                                ' : null) . '
                                        </section>
                                        ' . ($cfg['is_be'] == 1 ? '
                                        <section class="inner-search">
                                            <div>
                                                <form id="view-limits" class="entry-form-class" method="post" action="">
                                                <input type="text" name="subs_limit_min_off" id="view-limit-min-off" value="' . $subs_min . '" rel="tooltip" title="' . $language['backend.sub.dash.payout.min.subs'] . '">
                                                <input type="text" name="subs_limit_max_off" id="view-limit-max-off" value="' . $subs_max . '" rel="tooltip" title="' . $language['backend.sub.dash.payout.max.subs'] . '">
                                                <input type="submit" name="subs_limit_submit" class="no-display">
                                            </form>
                                            </div>
                                        </section>
                                        <div id="search-boxes">
                                            <div>' . ($cfg['is_be'] == 1 ? $smarty->fetch('tpl_backend/tpl_subscriber/tpl_search_inner.tpl') : null) . '</div>
                                        </div>
                                        ' : null) . '
                                        <div class="clearfix"></div>
                                        <div class="line" style="margin-bottom:0"></div>
                                </article>
                                <section class="filtertext">
                                    ' . ($_s[0] != '' ? ' <span class="filter-text">' . $language['files.menu.active.filters'] . ' ' . implode('', $_s) . '</span>' : null) . '
                                </section>
                                <div class="clearfix"></div>
                                <div class="line" style="margin-bottom:0"></div>
                                ' . ((!$o) ? '
                                <article id="time-sort-filters" style="display: ' . (isset($_GET['f']) ? 'block' : 'none') . ';">
                                    <h3 class="content-title content-filter"><i class="icon-filter"></i>' . $language['account.entry.filter.results'] . '</h3>
                                    <section class="filter">
                                        ' . self::tpl_filters($type) . '
                                    </section>
                                        <div class="clearfix"></div>
                                </article>
                                ' : null) . '
                                ' . (($rg and ($f == '' or $f == 'week' or $f == 'month' or $f == 'year')) ? '
                                <article id="week-sort-filters" style="display: ' . (isset($_GET['f']) ? 'block' : 'none') . ';">
                                    <h3 class="content-title content-filter no-display"><i class="icon-calendar"></i>' . $language['backend.sub.label.wnr'] . '</h3>
                                    <section class="filter">
                                        <ul class="content-filters">' . implode(' ', $wnr) . '</ul>
                                    </section>
                                    <div class="clearfix"></div>
                                </article>
                                ' : null) . '
                                <form id="custom-date-form" name="custom_date_form" method="post" action="" class="no-display">
                                    <input type="hidden" name="custom_date_submit" value="1">
                                    <input type="hidden" name="custom_date" id="custom-date" value="' . (isset($_POST['custom_date']) ? $class_filter->clr_str($_POST['custom_date']) : null) . '">
                                    <input type="hidden" name="custom_date_start" id="custom-date-start" value="' . (isset($_POST['custom_date_start']) ? $class_filter->clr_str($_POST['custom_date_start']) : null) . '">
                                    <input type="hidden" name="custom_date_end" id="custom-date-end" value="' . (isset($_POST['custom_date_end']) ? $class_filter->clr_str($_POST['custom_date_end']) : null) . '">
                                    <input type="hidden" name="custom_country" id="custom-country" value="' . (isset($_POST['custom_country']) ? $class_filter->clr_str($_POST['custom_country']) : null) . '">
                                        <input type="hidden" name="subs_limit_min" id="view-limit-min" value="' . $subs_min . '">
                                        <input type="hidden" name="subs_limit_max" id="view-limit-max" value="' . $subs_max . '">
                                </form>
                        </form>

        ';

        return $html;
    }
    /* generated filter html section */
    private static function tpl_filters($type, $hidden = false)
    {
        global $class_filter, $language;

        $f  = isset($_GET['f']) ? $class_filter->clr_str($_GET['f']) : false;
        $rp = isset($_GET['rp']) ? $class_filter->clr_str($_GET['rp']) : false;

        $html = '
                                        <div id="filter-section-' . $type . '"' . ($hidden ? ' style="display: none;"' : null) . '>
                                        ' . (!isset($_GET['rg']) ? '
                                            <ul class="content-filters">
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="thisweek"' . ($f == 'thisweek' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.thisweek'] . '</a></li>' : null) . '
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="lastweek"' . ($f == 'lastweek' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.lastweek'] . '</a></li>' : null) . '
                                                <li><a href="javascript:;" rel-t="thismonth"' . (($f == 'thismonth' or (!$f and $rp)) ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.thismonth'] . '</a></li>
                                                <li><a href="javascript:;" rel-t="lastmonth"' . ($f == 'lastmonth' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.lastmonth'] . '</a></li>
                                                <li><a href="javascript:;" rel-t="last3months"' . ($f == 'last3months' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.last3months'] . '</a></li>
                                                <li><a href="javascript:;" rel-t="last6months"' . ($f == 'last6months' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.last6months'] . '</a></li>
                                                ' . ($rp ? '<li><a href="javascript:;" rel-t="thisyear"' . ($f == 'thisyear' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.thisyear'] . '</a></li>' : null) . '
                                                ' . ($rp ? '<li><a href="javascript:;" rel-t="lastyear"' . ($f == 'lastyear' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['account.entry.f.lastyear'] . '</a></li>' : null) . '
                                                <li><a href="javascript:;" rel-t="range" class="rpickoff' . $type . ($f == 'range' ? ' active' : null) . '"><i class="icon-calendar"></i> ' . $language['account.entry.f.range'] . '</a></li>
                                            </ul>
                                        ' : '
                                            <ul class="content-filters">
                                                <li><a href="javascript:;" rel-t="week"' . (($f == 'week' or $f == '') ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['backend.sub.label.week'] . '</a></li>
                                                <li><a href="javascript:;" rel-t="month"' . ($f == 'month' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['backend.sub.label.month'] . '</a></li>
                                                <li><a href="javascript:;" rel-t="year"' . ($f == 'year' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language['backend.sub.label.year'] . '</a></li>
                                            </ul>
                                        ') . '
                                            <div class="dpick' . $type . '" style="position: absolute; right: 0"></div>
                                            <div class="rpick' . $type . '" style="position: absolute; right: 0; z-index: 100; margin-top: 8px; display: none;"></div>
                                        </div>
        ';

        return $html;
    }
    /* generate filter text */
    private static function tpl_filter_text($lang, $param, $remove = false)
    {
        global $language;

        return '<a class="filter-tag" href="javascript:;" rel="nofollow" rel-def="' . (!$remove ? 0 : 1) . '" rel-t="' . $param . '">' . $lang . ' ' . (!$remove ? '<i class="icon-times" rel="tooltip" title="' . $language['account.entry.clear.filter'] . '"></i>' : '<i class="icon-check" rel="tooltip" title="' . $language['account.entry.def.filter'] . '"></i>') . '</a>';
    }
    /* generate month date range */
    private static function getMonth($year, $month)
    {
        // this calculates the last day of the given month
        $year  = $month == 0 ? $year - 1 : $year;
        $month = $month == 0 ? 12 : $month;
        $last  = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $date = new DateTime();
        $res  = array();

        $i = 0;
        // iterates through days
        for ($day = 1; $day <= $last; $day++) {
            $date->setDate($year, $month, $day);
            $res[$i] = $date->format("Y-m-d");
            $i += 1;
        }

        return $res;
    }
    /* generate this week date range */
    private static function getThisWeekDates()
    {
        $date = date("m/d/Y");
        // parse about any English textual datetime description into a Unix timestamp
        $ts = strtotime($date);
        // find the year (ISO-8601 year number) and the current week
        $year     = date('o', $ts);
        $week     = date('W', $ts);
        $thisWeek = array();
        // print week for the current date
        for ($i = 1; $i <= 7; $i++) {
            // timestamp from ISO week date format
            $ts         = strtotime($year . 'W' . $week . $i);
            $thisWeek[] = date("Y-m-d", $ts);
        }
        return $thisWeek;
    }
    /* generate last week date range */
    private static function getLastWeekDates()
    {
        $lastWeek = array();

        $prevMon     = abs(strtotime("last week monday"));
        $currentDate = abs(strtotime("last week sunday"));

        $seconds = 86400; //86400 seconds in a day

        $dayDiff = ceil(($currentDate - $prevMon) / $seconds);

        if ($dayDiff < 6) {
            $dayDiff += 1; //if it's monday the difference will be 0, thus add 1 to it
            $prevMon = strtotime("previous monday", strtotime("-$dayDiff day"));
        }

        $prevMon = date("Y-m-d", $prevMon);

        // create the dates from Monday to Sunday
        for ($i = 0; $i < 7; $i++) {
            $d          = date("Y-m-d", strtotime($prevMon . " + $i day"));
            $lastWeek[] = $d;
        }

        return $lastWeek;
    }
    /* get start and end of week based on week number */
    private static function getStartAndEndDateWeek($week, $year)
    {
        $dto = new DateTime();

        $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
        $ret['week_end']   = $dto->modify('+6 days')->format('Y-m-d');

        return $ret;
    }
    /* get start and end of month based on month number */
    private static function getStartAndEndDateMonth($month, $year)
    {
        if ($month < 10) {
            $month = '0' . $month;
        }

        $ret['month_start'] = date($year . '-' . $month . '-01');
        $ret['month_end']   = date($year . '-' . $month . '-' . date('t', strtotime($ret['month_start'])));

        return $ret;
    }
    /* live stream viewers number */
    public static function get_live_viewers()
    {
        global $class_filter, $cfg, $db, $class_database;

        if (!isset($_GET['s']) and !isset($_GET['t']) and !isset($_GET['l']) and !isset($_GET['e'])) {
            return;
        }

        if (isset($_GET['s'])) {
            $s = $class_filter->clr_str($_GET['s']);
            $s = (int) $_GET['s'];if ($s == 0) {
                return;
            }

            $ss = "chat_view_" . $s;

            $longip = ip2long($_SERVER[REM_ADDR]);
            $ci     = $db->execute(sprintf("SELECT `db_id`, `ts` FROM `db_liveviewers` WHERE `file_key`='%s' AND `longip`='%s' LIMIT 1;", $s, $longip));
            if (!$ci->fields['db_id']) {
                $ins = array("file_key" => $s, "longip" => $longip, "ts" => time());
                $class_database->doInsert('db_liveviewers', $ins);
            } else {
                $ts = $ci->fields['ts'];
                if ($ts < strtotime("-120 seconds")) {
                    $db->execute(sprintf("UPDATE `db_liveviewers` SET `ts`='%s' WHERE `db_id`='%s' LIMIT 1;", time(), $ci->fields['db_id']));
                }
            }
        }

        if (isset($_GET['e'])) {
            $t = isset($_GET['v']) ? (int) $_GET['v'] : (isset($_GET['l']) ? (int) $_GET['l'] : 0);
            if ($t == 0) {
                return;
            }

            $type = isset($_GET['v']) ? 'video' : (isset($_GET['l']) ? 'live' : (isset($_GET['s']) ? 'short' : null));
            if ($type) {
                $db->execute(sprintf("UPDATE `db_%sfiles` SET `file_views`=`file_views`+1 WHERE `file_key`='%s' LIMIT 1;", $type, $t));
            }

        }

        if (isset($_GET['l'])) {
            $t = (int) $_GET['l'];if ($t == 0) {
                return;
            }

            $tt = $db->execute(sprintf("SELECT `file_like` FROM `db_livefiles` WHERE `file_key`='%s' LIMIT 1;", $t));
            $nr = $tt->fields['file_like'];

            echo (int) $nr;
        }

        if (isset($_GET['t'])) {
            $t = (int) $_GET['t'];if ($t == 0) {
                return;
            }

            $tt = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_liveviewers` WHERE `file_key`='%s' AND `ts` > %s LIMIT 1;", $t, strtotime("-5 minutes")));
            $nr = $tt->fields['total'];

            echo (int) $nr;
        }
    }
}
