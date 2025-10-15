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
| Copyright (c) 2013-2018 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

class VAffiliate
{
    /* get type */
    private static function getType()
    {
        global $class_filter;

        return (isset($_GET["t"]) ? $class_filter->clr_str($_GET["t"]) : 'video');
    }
    /* check if user is affiliate */
    public static function userCheck()
    {
        global $class_database, $cfg;

        $usr_id = (int) $_SESSION["USER_ID"];

        if ($usr_id > 0) {
            $is_affiliate = $class_database->singleFieldValue('db_accountuser', 'usr_affiliate', 'usr_id', $usr_id);
            if ($is_affiliate != 1) {
                header("Location: " . $cfg["main_url"] . '/' . VHref::getKey("account"));
                exit;
            }
        } else {
            header("Location: " . $cfg["main_url"] . '/' . VHref::getKey("signin"));
            exit;
        }
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
    /* save affiliate settings */
    public static function affiliateProfile($sub_panel = false)
    {
        global $cfg, $db, $class_filter, $language;

        $error_message = false;
        $user_id       = (int) $_SESSION["USER_ID"];
        $r             = false;

        if ($_POST) {
//            if (!$sub_panel) {
            $af_badge = $class_filter->clr_str($_POST[($sub_panel ? "user_partner_badge" : "user_affiliate_badge")]);

            $db->execute(sprintf("UPDATE `db_accountuser` SET `affiliate_badge`='%s' WHERE `usr_id`='%s' LIMIT 1;", $af_badge, $user_id));
            if ($db->Affected_Rows() > 0) {
                $r = true;

                $_SESSION["USER_BADGE"] = $af_badge;
            }
//            }
            $af_api_key = !$sub_panel ? $class_filter->clr_str($_POST["user_affiliate_maps_key"]) : false;
            if ($af_api_key) {
                $db->execute(sprintf("UPDATE `db_accountuser` SET `affiliate_maps_key`='%s' WHERE `usr_id`='%s' LIMIT 1;", $af_api_key, $user_id));
                if ($db->Affected_Rows() > 0) {
                    $r = true;
                }
            }

            $af_mail = !$sub_panel ? $class_filter->clr_str($_POST["user_affiliate_paypal"]) : $class_filter->clr_str($_POST["user_partner_paypal"]);
            if ($af_mail != '') {
                $ec            = new VValidation;
                $error_message = !$ec->checkEmailAddress($af_mail) ? $language["frontend.signup.email.invalid"] : $error_message;

                if (!$error_message) {
                    $field         = !$sub_panel ? 'affiliate_email' : 'usr_sub_email';
                    $em            = $db->execute(sprintf("SELECT `%s` FROM `db_accountuser` WHERE `usr_id`!='%s' AND `%s`='%s' LIMIT 1;", $field, $user_id, $field, $af_mail));
                    $error_message = $db->Affected_Rows() > 0 ? $language["notif.error.existing.email"] : $error_message;

                    if (!$error_message) {
                        $db->execute(sprintf("UPDATE `db_accountuser` SET `%s`='%s' WHERE `usr_id`='%s' LIMIT 1;", $field, $af_mail, $user_id));
                        if ($db->Affected_Rows() > 0) {
                            $r = true;
                        }
                    }
                }
            }

            if ($error_message) {
                return VGenerate::noticeTpl('', $error_message, '');
            } elseif ($r) {
                return VGenerate::noticeTpl('', '', $language["notif.success.request"]);
            }
        }
    }
    /* affiliate user dashboard/stats */
    public static function accountStats($user_key = '')
    {
        global $smarty, $language, $db, $cfg, $class_database;

        $user_key = !$user_key ? $_SESSION["USER_KEY"] : $user_key;
        $user_id  = (int) $_SESSION["USER_ID"];
        $mod_arr  = array("video" => "video", "live" => "live", "image" => "image", "audio" => "audio", "doc" => "document", "blog" => "blog");
        $my       = $db->execute(sprintf("SELECT `affiliate_email`, `affiliate_badge`, `affiliate_date`, `affiliate_maps_key` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $user_id));

        $html = '
            <div class="vs-column full">
                <article>
                    <h3 class="content-title"><i class="iconBe-coin"></i> ' . $language["account.entry.act.affiliate"] . '</h3>
                    <div class="line"></div>
                </article>
        ';

        foreach ($mod_arr as $db_tbl => $module) {
            if ($cfg[$module . "_module"] == 1) {
                $sql = sprintf("SELECT SUM(`p_amount_shared`) AS `total_balance` FROM `db_%spayouts` WHERE `usr_key`='%s' AND `p_paid`='0' AND `p_active`='1';", $db_tbl, $user_key);
                $rt  = $db->execute($sql);
                $t1  = round($rt->fields["total_balance"], 2);

                $sql = sprintf("SELECT SUM(`p_amount_shared`) AS `total_revenue`, COUNT(`file_key`) AS `total_payments` FROM `db_%spayouts` WHERE `usr_key`='%s' AND `p_paid`='1' AND `p_active`='1';", $db_tbl, $user_key);
                $rt  = $db->execute($sql);
                $t2  = round($rt->fields["total_revenue"], 2);
                $t3  = $rt->fields["total_payments"];

                $html .= '
                <div class="">
                    <div class="">
                        <ul class="' . $db_tbl . ' l vs-column thirds"><li class="l1"><i class="icon-' . $db_tbl . '"></i></li><li class="l2">' . str_replace('##TYPE##', $language["frontend.global." . $module[0] . ".c"], $language["account.entry.payout.stats.bal"]) . '</li><li class="l3"><i class="icon-coin"></i> ' . $t1 . ' ' . $cfg["affiliate_payout_currency"] . '</li></ul>
                        <ul class="' . $db_tbl . ' l vs-column thirds"><li class="l1"><i class="icon-' . $db_tbl . '"></i></li><li class="l2">' . str_replace('##TYPE##', $language["frontend.global." . $module[0] . ".c"], $language["account.entry.payout.stats.rev"]) . '</li><li class="l3"><i class="icon-coin"></i> ' . $t2 . ' ' . $cfg["affiliate_payout_currency"] . '</li></ul>
                        <ul class="' . $db_tbl . ' l vs-column thirds fit"><li class="l1"><i class="icon-' . $db_tbl . '"></i></li><li class="l2">' . str_replace('##TYPE##', $language["frontend.global." . $module[0] . ".c"], $language["account.entry.payout.stats.pay"]) . '</li><li class="l3"><i class="icon-paypal"></i> ' . $t3 . '</li></ul>
                    </div>
                    <div class="clearfix"></div>
                </div>
                ';
            }
        }

        $html .= '</div>';

        $af_badge = $my->fields["affiliate_badge"];
        $af_email = $my->fields["affiliate_email"];
        $af_date  = $my->fields["affiliate_date"];
        $af_maps  = $my->fields["affiliate_maps_key"];
        $af_date  = date('F j, o, H:i A', strtotime($af_date));
        $badges   = array('icon-check', 'icon-user', 'icon-coin', 'icon-thumbs-up', 'icon-paypal');

        $badge_ht = '<select class="badge-select-input" name="user_affiliate_badge">';
        $badge_ht .= '<option value=""' . ($af_badge == '' ? ' selected="selected"' : null) . '>' . $language["frontend.global.none"] . '</option>';
        foreach ($badges as $badge) {
            $badge_ht .= '<option value="' . $badge . '"' . ($badge == $af_badge ? ' selected="selected"' : null) . '><i class="' . $badge . '"></i> ' . $badge . '</option>';
        }
        $badge_ht .= '</select>';

        $smarty->assign('affiliate_date', $af_date);
        $smarty->assign('affiliate_email', $af_email);
        $smarty->assign('affiliate_badge', $af_badge);
        $smarty->assign('affiliate_maps_key', $af_maps);
        $smarty->assign('badge_ht', $badge_ht);
//        $smarty->assign('user_account_stats', VUseraccount::channelCountStats());
        $smarty->assign('html', $html);
    }
    /* generate payout reports */
    public static function payoutReports($type = 'video')
    {
        global $db, $class_database;

        $cfg = $class_database->getConfigurations('affiliate_module,affiliate_token_script,affiliate_view_id,affiliate_payout_figure,affiliate_payout_units,affiliate_payout_share');

        if ($cfg["affiliate_module"] == 0) {
            return;
        }

        //$cmd     = sprintf("/usr/local/bin/python %s", $cfg["affiliate_token_script"]);
        $cmd = sprintf("python %s", $cfg["affiliate_token_script"]);
        exec($cmd . ' 2>&1', $output);

        $tk = $output[0];

        $w  = self::getMonth(date("Y"), (date("m") - 1));
        $t  = count($w) - 1;
        $sd = $w[0];
        $ed = $w[$t];

        $price  = $cfg["affiliate_payout_figure"];
        $unit   = $cfg["affiliate_payout_units"];
        $min    = $unit;
        $minstr = '%3Bga%3AuniquePageviews%3E%3D' . $min;
        $apiurl = 'https://www.googleapis.com/analytics/v3/data/ga?ids=ga%3A' . $cfg["affiliate_view_id"] . '&start-date=2018-01-01&end-date=' . $ed . '&metrics=ga%3AuniquePageviews&dimensions=ga%3ApageTitle%2Cga%3Adimension1%2Cga%3Adimension3&filters=ga%3Adimension2%3D%3D' . $type . $minstr . '&access_token=' . $tk;

        $res     = VServer::curl_tt($apiurl);
        $json    = json_decode($res);
        $entries = is_array($json->rows) ? $json->rows : false;

        if ($entries) {
            foreach ($entries as $entry) {
                $views = $entry[3];
                $amt   = round((($views * $price) / $unit), 2);
                $q     = $db->execute(sprintf("SELECT `p_id`, `p_startdate`, `p_enddate`, `p_custom` FROM `db_%spayouts` WHERE `file_key`='%s' ORDER BY `p_id` DESC LIMIT 1;", $type, $entry[2]));

                if (!$q->fields["p_id"]) {
                    $ui = $db->execute(sprintf("SELECT `usr_id` FROM `db_accountuser` WHERE `usr_key`='%s' AND `usr_affiliate`='1' AND `usr_status`='1' LIMIT 1;", $entry[1]));
                    $uf = $db->execute(sprintf("SELECT `db_id` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $entry[2]));

                    if ($ui->fields["usr_id"] and $uf->fields["db_id"]) {
                        $ins = array(
                            'usr_id'          => $ui->fields["usr_id"],
                            'usr_key'         => $entry[1],
                            'file_key'        => $entry[2],
                            'p_startdate'     => $sd,
                            'p_enddate'       => $ed,
                            'p_views'         => $views,
                            'p_amount'        => $amt,
                            'p_amount_shared' => round((($cfg["affiliate_payout_share"] * $amt) / 100), 2),
                            'p_state'         => 0,
                        );
                        $ins = $class_database->doInsert('db_' . $type . 'payouts', $ins);
                    }
                } elseif ($q->fields["p_custom"] == '0') {
                    $q  = $db->execute(sprintf("SELECT SUM(`p_views`) AS `vtotal` FROM `db_%spayouts` WHERE `file_key`='%s'", $type, $entry[2]));
                    $vt = $q->fields["vtotal"];

                    if (($views - $vt) >= $unit) {
                        $views = ($views - $vt);
                        $amt   = round((($views * $price) / $unit), 2);

                        $ui = $db->execute(sprintf("SELECT `usr_id` FROM `db_accountuser` WHERE `usr_key`='%s' AND `usr_affiliate`='1' AND `usr_status`='1' LIMIT 1;", $entry[1]));
                        $uf = $db->execute(sprintf("SELECT `db_id` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $entry[2]));

                        if ($ui->fields["usr_id"] and $uf->fields["db_id"]) {
                            $ins = array(
                                'usr_id'          => $ui->fields["usr_id"],
                                'usr_key'         => $entry[1],
                                'file_key'        => $entry[2],
                                'p_startdate'     => $sd,
                                'p_enddate'       => $ed,
                                'p_views'         => $views,
                                'p_amount'        => $amt,
                                'p_amount_shared' => round((($cfg["affiliate_payout_share"] * $amt) / 100), 2),
                                'p_state'         => 0,
                            );
                            $ins = $class_database->doInsert('db_' . $type . 'payouts', $ins);
                        }
                    }
                }
            }
        }
    }
    /* generate custom payout reports */
    public static function payoutReports_custom($type = 'video')
    {
        global $db, $class_database;

        $cfg = $class_database->getConfigurations('affiliate_module,affiliate_token_script,affiliate_view_id,affiliate_payout_figure,affiliate_payout_units,affiliate_payout_share');

        if ($cfg["affiliate_module"] == 0) {
            return false;
        }

        //$cmd     = sprintf("/usr/local/bin/python %s", $cfg["affiliate_token_script"]);
        $cmd = sprintf("python %s", $cfg["affiliate_token_script"]);
        exec($cmd . ' 2>&1', $output);

        $tk = $output[0];

        $w  = self::getMonth(date("Y"), (date("m") - 1));
        $t  = count($w) - 1;
        $sd = $w[0];
        $ed = $w[$t];

        $rs = $db->execute(sprintf("SELECT `usr_id`, `usr_user`, `usr_key`, `affiliate_pay_custom`, `affiliate_custom` FROM `db_accountuser` WHERE `usr_affiliate`='1' AND `affiliate_custom`!='' AND `usr_status`='1';"));

        if ($rs->fields["usr_id"]) {
            while (!$rs->EOF) {
                $usr_key   = $rs->fields["usr_key"];
                $af_custom = ($rs->fields["usr_id"] and $rs->fields["affiliate_pay_custom"] == 1) ? unserialize($rs->fields["affiliate_custom"]) : false;

                if ($af_custom["share"] != '' and $af_custom["units"] != '' and $af_custom["figure"] != '' and $af_custom["currency"] != '') {
                    $cfg["affiliate_payout_figure"] = $af_custom["figure"];
                    $cfg["affiliate_payout_units"]  = $af_custom["units"];
                    $cfg["affiliate_payout_share"]  = $af_custom["share"];

                    $price  = $cfg["affiliate_payout_figure"];
                    $unit   = $cfg["affiliate_payout_units"];
                    $min    = $unit;
                    $minstr = '%3Bga%3AuniquePageviews%3E%3D' . $min;
                    $usrstr = '%3Bga%3Adimension1%3D%3D' . $usr_key;
                    $apiurl = 'https://www.googleapis.com/analytics/v3/data/ga?ids=ga%3A' . $cfg["affiliate_view_id"] . '&start-date=2018-01-01&end-date=' . $ed . '&metrics=ga%3AuniquePageviews&dimensions=ga%3ApageTitle%2Cga%3Adimension1%2Cga%3Adimension3&filters=ga%3Adimension2%3D%3D' . $type . $usrstr . $minstr . '&access_token=' . $tk;

                    $res     = VServer::curl_tt($apiurl);
                    $json    = json_decode($res);
                    $entries = is_array($json->rows) ? $json->rows : false;

                    if ($entries) {
                        foreach ($entries as $entry) {
                            $views = $entry[3];
                            $amt   = round((($views * $price) / $unit), 2);
                            $q     = $db->execute(sprintf("SELECT `p_id`, `p_startdate`, `p_enddate` FROM `db_%spayouts` WHERE `file_key`='%s' AND `p_custom`='1' ORDER BY `p_id` DESC LIMIT 1;", $type, $entry[2]));

                            if (!$q->fields["p_id"]) {
                                $uf = $db->execute(sprintf("SELECT `db_id` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $entry[2]));

                                if ($uf->fields["db_id"]) {
                                    $ins = array(
                                        'usr_id'          => $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $entry[1]),
                                        'usr_key'         => $entry[1],
                                        'file_key'        => $entry[2],
                                        'p_startdate'     => $sd,
                                        'p_enddate'       => $ed,
                                        'p_views'         => $views,
                                        'p_amount'        => $amt,
                                        'p_amount_shared' => round((($cfg["affiliate_payout_share"] * $amt) / 100), 2),
                                        'p_custom'        => 1,
                                        'p_state'         => 0,
                                    );
                                    $ins = $class_database->doInsert('db_' . $type . 'payouts', $ins);
                                }
                            } else {
                                $q  = $db->execute(sprintf("SELECT SUM(`p_views`) AS `vtotal` FROM `db_%spayouts` WHERE `file_key`='%s' AND `p_custom`='1'", $type, $entry[2]));
                                $vt = $q->fields["vtotal"];

                                if (($views - $vt) >= $unit) {
                                    $views = ($views - $vt);
                                    $amt   = round((($views * $price) / $unit), 2);

                                    $uf = $db->execute(sprintf("SELECT `db_id` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $entry[2]));

                                    if ($uf->fields["db_id"]) {
                                        $ins = array(
                                            'usr_id'          => $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $entry[1]),
                                            'usr_key'         => $entry[1],
                                            'file_key'        => $entry[2],
                                            'p_startdate'     => $sd,
                                            'p_enddate'       => $ed,
                                            'p_views'         => $views,
                                            'p_amount'        => $amt,
                                            'p_amount_shared' => round((($cfg["affiliate_payout_share"] * $amt) / 100), 2),
                                            'p_custom'        => 1,
                                            'p_state'         => 0,
                                        );
                                        $ins = $class_database->doInsert('db_' . $type . 'payouts', $ins);
                                    }
                                }
                            }
                        }
                    }
                }

                $rs->MoveNext();
            }
        }

        return true;
    }
    /* request/cancel affiliate modal */
    public static function affiliateRequest()
    {
        global $smarty, $class_filter, $cfg, $language;

        $_do = isset($_GET["do"]) ? $class_filter->clr_str($_GET["do"]) : false;

        if (!$_do) {
            return;
        }

        switch ($_do) {
            case "make-partner":
                $el = array('icon-user', $language["account.entry.btn.submit.prt"], $language["account.overview.request.cancel2a"]);
                break;
            case "make-affiliate":
                $el = array('icon-user', $language["account.entry.btn.submit"], $language["account.overview.request.cancel2"]);
                break;
            case "clear-partner":
                $el = array('icon-user', $language["account.entry.btn.terminate.prt"], $language["account.overview.request.cancel1a"]);
                break;
            case "clear-affiliate":
                $el = array('icon-user', $language["account.entry.btn.terminate"], $language["account.overview.request.cancel1"]);
                break;
        }
        $html = '
            <form id="profile-image-form" class="entry-form-class" method="post" action="" enctype="multipart/form-data">
                <article>
                    <h3 class="content-title"><i class="' . $el[0] . '"></i> ' . $el[1] . '</h3>
                    <div class="line"></div>
                </article>

            <div id="intabdiv">
                <div class="left-float left-align wdmax row" id="overview-userinfo-response"></div>
                <div class="left-float left-align wdmax">
                <div class="row">
                <p>' . $el[2] . '</p>
                <p>' . $language["account.overview.request.cancel3"] . '</p>
                </div>
                <div class="row" id="overview-userinfo-file">
                </div>
                </div>
                <div class="row">
                <div class="row" id="save-button-row">
                                    <button name="save_changes_btn" id="save-image-btn" class="save-entry-button button-grey search-button form-button ' . $_do . '-email" type="button" value="1"><span>' . $language["frontend.global.confirm"] . '</span></button>
                                    <a class="link cancel-trigger" href="javascript:;" onclick="$(\'.fancybox-close\').click()"><span>' . $language["frontend.global.cancel"] . '</span></a>
                                    <input type="hidden" name="sr" value="1">
                                </div>
                </div>
                </div>
                </form>
        ';
        $html .= '
            <script type="text/javascript">
                $(".' . $_do . '-email").on("click", function() {
                    u = current_url + menu_section + "?s=account-menu-entry13&do=' . $_do . '-email"
                    $(".fancybox-inner").mask(" ");
                    $.post(u, $("#profile-image-form").serialize(), function(data){
                        $("#overview-userinfo-response").html(data);
                        $(".fancybox-inner").unmask();
                    });
                });



// REMOVE THIS BUT TEST 1st
/*
                $(".clear-affiliate-email").on("click", function() {
                    u = current_url + menu_section + "?s=account-menu-entry13&do=clear-affiliate-email"
                    $(".fancybox-inner").mask(" ");
                    $.post(u, $("#profile-image-form").serialize(), function(data){
                        $("#overview-userinfo-response").html(data);
                        $(".fancybox-inner").unmask();
                    });
                });

                $(".make-affiliate-email").on("click", function() {
                    u = current_url + menu_section + "?s=account-menu-entry1&do=make-affiliate-email"
                    $(".fancybox-inner").mask(" ");
                    $.post(u, $("#profile-image-form").serialize(), function(data){
                        $("#overview-userinfo-response").html(data);
                        $(".fancybox-inner").unmask();
                    });
                });
*/
            </script>
        ';

        return $html;
    }
    /* affiliate request emails */
    public static function affiliateRequestEmail()
    {
        global $cfg, $language, $smarty, $class_filter, $db, $class_database, $class_language;

        include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

        $_do = $class_filter->clr_str($_GET["do"]);

        switch ($_do) {
            case "make-affiliate-email":
                $s1 = $language["affiliate.subject.new"];
                $s2 = 'affiliate_approve_request';
                break;
            case "make-partner-email":
                $s1 = $language["partner.subject.new"];
                $s2 = 'partner_approve_request';
                break;
            case "clear-affiliate-email":
                $s1 = $language["affiliate.subject.cancel"];
                $s2 = 'affiliate_cancel_request';
                break;
            case "clear-partner-email":
                $s1 = $language["partner.subject.cancel"];
                $s2 = 'affiliate_cancel_request';
                break;
        }
        if ($_POST) {
            include 'f_core/config.backend.php';

            $p  = sprintf("`usr_id` IN (%s) LIMIT 1;", (int) $_SESSION["USER_ID"]);
            $rs = $db->execute(sprintf("SELECT `usr_id`, `usr_email` FROM `db_accountuser` WHERE %s", $p));

            if ($rs->fields["usr_id"]) {
                while (!$rs->EOF) {
                    $user_id   = $rs->fields["usr_id"];
                    $user_data = VUserinfo::getUserInfo($user_id);
                    $mailto    = $rs->fields["usr_email"];

                    $notifier     = new VNotify;
                    $website_logo = $smarty->fetch($cfg["templates_dir"] . '/tpl_frontend/tpl_header/tpl_headerlogo_mail.tpl');
                    $cfg          = $class_database->getConfigurations('backend_email,backend_username,affiliate_payout_currency');

                    $notifier->msg_subj = $s1;
                    $_replace           = array(
                        '##TITLE##' => $s1,
                        '##LOGO##'  => $website_logo,
                        '##USER##'  => '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey("be_members") . '?u=' . $_SESSION["USER_KEY"] . '" target="_blank">' . $_SESSION["USER_NAME"] . '</a>',
                        '##H2##'    => $language["recovery.forgot.password.h2"] . $cfg["backend_username"] . ',',
                        '##YEAR##'  => date('Y'),
                    );
                    $notifier->dst_mail = $cfg["backend_email"];
                    $notifier->dst_name = $cfg["backend_username"];
                    $notifier->Mail('frontend', $_do, $_replace);
                    $_output[] = $cfg["backend_username"] . ' -> ' . $s2 . ' -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                    $log_mail  = '.mailer.log';
                    VServer::logToFile($log_mail, implode("\n", $_output));

                    $rs->MoveNext();
                }

                return VGenerate::noticeTpl('', '', $language["notif.success.request"]);
            }
        }
    }

    /* generate analytics data */
    public static function viewAnalytics()
    {
        global $smarty, $class_filter, $class_database, $cfg;

        if ($cfg["affiliate_module"] == 0) {
            return;
        }

        $type = self::getType();
        $f    = isset($_GET["f"]) ? $class_filter->clr_str($_GET["f"]) : false;

        //$cmd     = sprintf("/usr/local/bin/python %s", $cfg["affiliate_token_script"]);
        $cmd = sprintf("python %s", $cfg["affiliate_token_script"]);
        exec($cmd . ' 2>&1', $output);

        $tk = $output[0];

        $month = date("m");
        $year  = date("Y");
        $day   = date("d");

        $views_min = isset($_SESSION["views_min"]) ? $_SESSION["views_min"] : 0;
        $views_max = isset($_SESSION["views_max"]) ? $_SESSION["views_max"] : 0;

        if (isset($_POST["view_limit_min"]) or isset($_POST["view_limit_max"])) {
            $_SESSION["views_min"] = (int) $_POST["view_limit_min"];
            $_SESSION["views_max"] = (int) $_POST["view_limit_max"];

            $views_min = $_SESSION["views_min"];
            $views_max = $_SESSION["views_max"];
        }
        if ($views_min > 0) {
            $smarty->assign('views_min', ';ga:uniquePageviews>=' . $views_min);
        }
        if ($views_max > 0) {
            $smarty->assign('views_max', ';ga:uniquePageviews<=' . $views_max);
        }

        switch ($f) {
            default:
                if ($_POST and isset($_POST["custom_date"]) and $_POST["custom_date"] != '') {
                    if (isset($_GET["a"])) {
                        $sd = $class_filter->clr_str($_POST["custom_date"]);
                        $_a = explode("-", $sd);

                        $sd = $class_filter->clr_str($_POST["custom_date"]);
                        $ed = $sd;
                    } elseif (isset($_GET["g"])) {
                        $sd = $class_filter->clr_str($_POST["custom_date"]);
                        $ed = $sd;
                    }
                } elseif ($_POST and isset($_POST["custom_date_start"]) and $_POST["custom_date_start"] != '') {
                    $sd = $class_filter->clr_str($_POST["custom_date_start"]);
                    $ed = $class_filter->clr_str($_POST["custom_date_end"]);

                    $sd = $sd == '' ? $year . '-' . $month . '-' . $day : $sd;
                    $ed = $ed == '' ? $year . '-' . $month . '-' . $day : $ed;
                } else {
                    $sd = $year . '-' . $month . '-' . $day;
                    $ed = $sd;
                }
                break;
            case "":
            case "today":
                if (isset($_GET["rp"])) {
                    $w  = self::getMonth(date("Y"), (date("m") - 1));
                    $t  = count($w) - 1;
                    $sd = $w[0];
                    $ed = $w[$t];
                } else {
                    $sd = 'today';
                    $ed = 'today';
                }
                break;
            case "yesterday":
                $sd = 'yesterday';
                $ed = 'yesterday';
                break;
            case "thisweek":
                $w  = self::getThisWeekDates();
                $sd = $w[0];
                $ed = $w[6];
                break;
            case "lastweek":
                $w  = self::getLastWeekDates();
                $sd = $w[0];
                $ed = $w[6];
                break;
            case "thismonth":
                $w  = self::getMonth(date("Y"), date("m"));
                $t  = count($w) - 1;
                $sd = $w[0];
                $ed = $w[$t];
                break;
            case "lastmonth":
                $w  = self::getMonth(date("Y"), (date("m") - 1));
                $t  = count($w) - 1;
                $sd = $w[0];
                $ed = $w[$t];
                break;
            case "thisyear":
                $sd = sprintf("%s-01-01", date("Y"));
                $ed = sprintf("%s-12-31", date("Y"));
                break;
            case "lastyear":
                $sd = sprintf("%s-01-01", (date("Y") - 1));
                $ed = sprintf("%s-12-31", (date("Y") - 1));
                break;
            case "last3months":
            case "last6months":
                $start_date = $day . "-" . $month . "-" . $year;
                $start_time = strtotime($start_date);

                $end_time = strtotime("-" . ($f == 'last3months' ? 3 : 6) . " month", $start_time);
                $lw       = array();
                for ($i = $end_time; $i < $start_time; $i += 86400) {
                    $lw[] = date("Y-m-d", $i);
                }
                $t  = count($lw);
                $sd = $lw[0];
                $ed = $year . '-' . $month . '-' . $day;
                break;
        }

        if (isset($_GET["o"])) {
            $w = self::getThisWeekDates();
            $smarty->assign('twsd', $w[0]);
            $smarty->assign('twed', $w[6]);
            $w = self::getLastWeekDates();
            $smarty->assign('lwsd', $w[0]);
            $smarty->assign('lwed', $w[6]);
        }

        if (isset($_GET["a"]) or isset($_GET["o"])) {
            $html = self::tpl_filter();
        } elseif (isset($_GET["g"])) {
            $html = self::tpl_filter();

            $smarty->assign('tpl_continent_filters', self::tpl_continents());

            if ($cfg["is_be"] == 0) {
                $smarty->assign('affiliate_maps_api_key', $class_database->singleFieldValue('db_accountuser', 'affiliate_maps_key', 'usr_id', (int) $_SESSION["USER_ID"]));
            }

        } elseif (isset($_GET["rp"])) {
            $html = self::tpl_filter();
            $html .= self::html_payouts($sd, $ed);
        }

        if ($cfg["is_be"] == 1) {
            $uk = isset($_GET["uk"]) ? $class_filter->clr_str($_GET["uk"]) : '1';

            $smarty->assign('user_filter', $uk);
        }

        $smarty->assign('file_type', $type);
        $smarty->assign('file_filter', $f);
        $smarty->assign('sd', $sd);
        $smarty->assign('ed', $ed);
        $smarty->assign('tpl_token', $tk);
        $smarty->assign('tpl_html', $html);
    }
    /* generate html payout reports */
    private static function html_payouts($sd, $ed)
    {
        include 'f_core/config.backend.php';
        global $language, $class_database, $class_filter, $class_language, $db, $cfg, $smarty;

        $type    = self::getType();
        $usr_key = ($cfg["is_be"] == 1 and isset($_GET["uk"])) ? $class_filter->clr_str($_GET["uk"]) : (!$cfg["is_be"] ? $_SESSION["USER_KEY"] : false);
        $f       = isset($_GET["f"]) ? $class_filter->clr_str($_GET["f"]) : 'lastmonth';

        if ($f) {
            switch ($f) {
                default:
                    if ($_POST and isset($_POST["custom_date_start"]) and $_POST["custom_date_start"] != '') {
                        $sdf = $class_filter->clr_str($_POST["custom_date_start"]);
                        $edf = $class_filter->clr_str($_POST["custom_date_end"]);
                    }
                    break;
                case "":
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
                case "lastyear":
                    break;
            }
        }

        $views_min = isset($_SESSION["views_min"]) ? $_SESSION["views_min"] : 0;
        $views_max = isset($_SESSION["views_max"]) ? $_SESSION["views_max"] : 0;

        $uk  = ($cfg["is_be"] == 1 and isset($_GET["uk"])) ? $class_filter->clr_str($_GET["uk"]) : (!$cfg["is_be"] ? $_SESSION["USER_KEY"] : false);
        $fk  = isset($_GET["fk"]) ? $class_filter->clr_str($_GET["fk"]) : false;
        $tab = isset($_GET["tab"]) ? $class_filter->clr_str($_GET["tab"]) : false;

        $rt = $db->execute(sprintf("SELECT A.`p_id`, COUNT(A.`file_key`) AS `total_count`, SUM(A.`p_views`) AS `total_views`, SUM(A.`p_amount_shared`) AS `total_balance`,
                            B.`usr_id`,
                            C.`file_key`
                            FROM
                            `db_%spayouts` A, `db_accountuser` B, `db_%sfiles` C
                            WHERE
                            A.`usr_key`=B.`usr_key` AND
                            B.`usr_id`=C.`usr_id` AND
                            A.`file_key`=C.`file_key` AND
                            A.`p_startdate`>='%s' AND
                            A.`p_enddate`<='%s' AND
                            %s %s %s %s %s
                            A.`p_active`='1'
                            ORDER BY C.`file_title` ASC;",

            $type, $type, $sdf, $edf,
            ($fk ? "A.`file_key`='" . $fk . "' AND " : null),
            ($uk ? "A.`usr_key`='" . $uk . "' AND " : null),
            ($views_min > 0 ? "A.`p_views`>='" . $views_min . "' AND " : null),
            ($views_max > 0 ? "A.`p_views`<='" . $views_max . "' AND " : null),
            ($tab == 'section-all' ? null : ($tab == 'section-paid' ? "A.`p_paid`='1' AND " : "A.`p_paid`='0' AND "))
        )
        );

        $res = $db->execute(sprintf("SELECT A.`p_id`, A.`file_key`, A.`usr_key`, A.`p_views`, A.`p_amount`, A.`p_amount_shared`, A.`p_startdate`, A.`p_enddate`, A.`p_paid`, A.`p_paydate`,
                            B.`usr_user`, B.`usr_affiliate`, B.`affiliate_email`, B.`affiliate_pay_custom`, B.`affiliate_custom`, B.`usr_dname`,
                            C.`file_title`
                            FROM
                            `db_%spayouts` A, `db_accountuser` B, `db_%sfiles` C
                            WHERE
                            A.`usr_key`=B.`usr_key` AND
                            B.`usr_id`=C.`usr_id` AND
                            A.`file_key`=C.`file_key` AND
                            A.`p_startdate`>='%s' AND
                            A.`p_enddate`<='%s' AND
                            %s %s %s %s %s
                            A.`p_active`='1'
                            ORDER BY C.`file_title` ASC;",

            $type, $type, $sdf, $edf,
            ($fk ? "A.`file_key`='" . $fk . "' AND " : null),
            ($uk ? "A.`usr_key`='" . $uk . "' AND " : null),
            ($views_min > 0 ? "A.`p_views`>='" . $views_min . "' AND " : null),
            ($views_max > 0 ? "A.`p_views`<='" . $views_max . "' AND " : null),
            ($tab == 'section-all' ? null : ($tab == 'section-paid' ? "A.`p_paid`='1' AND " : "A.`p_paid`='0' AND "))
        )
        );

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="open-close-reports">';
        $html .= $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl");
        $html .= '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '<div id="info-text">' . str_replace(array('##PNR##', '##VNR##', '##TNR##', '##TCR##'), array($rt->fields("total_count"), (int) $rt->fields("total_views"), round($rt->fields("total_balance"), 2), $cfg["affiliate_payout_currency"]), $language["account.entry.payout.found"]) . '</div>';
        $html .= '<div id="v-tabs" class="tabs tabs-style-line">';

        $html .= '
                    <nav>
                        <ul id="tb">
                            <li class="' . ((!$tab or $tab == 'section-unpaid') ? 'tab-current' : null) . '"><a href="#section-unpaid" class="icon icon-warning"><span>' . $language["account.entry.payout.unpaid"] . '</span></a></li>
                            <li class="' . ($tab == 'section-paid' ? 'tab-current' : null) . '"><a href="#section-paid" class="icon icon-check"><span>' . $language["account.entry.payout.paid"] . '</span></a></li>
                            <li class="' . ($tab == 'section-all' ? 'tab-current' : null) . '"><a href="#section-all" class="icon icon-paypal"><span>' . $language["frontend.global.alltxt"] . '</span></a></li>
                        </ul>
                    </nav>
        ';

        $html .= '<div class="content-wrap">';
        $html .= '<section id="section-unpaid">';
        $html .= self::tpl_payout_accordion($res, $sdf, $edf, $views_min, $views_max);
        $html .= '</section>';
        $html .= '<section id="section-paid"></section>';
        $html .= '<section id="section-all"></section>';
        $html .= '</div>'; //end content-wrap
        $html .= '</div>'; //end v-tabs
        $html .= '</div>';

        $price = $cfg["affiliate_payout_figure"];
        $unit  = $cfg["affiliate_payout_units"];
        $html .= '<script type="text/javascript">
            $(document).ready(function(){
            (function () {
                        [].slice.call(document.querySelectorAll(".tabs")).forEach(function (el) {
                                new CBPFWTabs(el);
                        }); })();

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
                    u = "?a=' . ($cfg["is_be"] == 1 ? '1' : md5($_SESSION["USER_KEY"])) . '&t=' . $type . '&f=range&fk="+fk;
                    $("input[name=\'custom_date_start\']").val(sd);
                    $("input[name=\'custom_date_end\']").val(ed);
                    $("#custom-date-form-"+i).attr("action", u).submit();
                });
                $(".fviews").click(function(){
                    t = $(this);
                    i = t.attr("rel-id");
                    fk = t.attr("rel-fk");
                    f = t.attr("rel-f");
                    u = "?a=' . ($cfg["is_be"] == 1 ? '1' : md5($_SESSION["USER_KEY"])) . '&t=' . $type . '&f="+f+"&fk="+fk;
                    $("#custom-date-form-"+i).attr("action", u).submit();
                });
                $(".fgeo-range").click(function(){
                    t = $(this);
                    i = t.attr("rel-id");
                    fk = t.attr("rel-fk");
                    sd = t.attr("rel-s");
                    ed = t.attr("rel-e");
                    u = "?g=' . ($cfg["is_be"] == 1 ? '1' : md5($_SESSION["USER_KEY"])) . '&t=' . $type . '&f=range&c=xx&fk="+fk;
                    $("input[name=\'custom_date_start\']").val(sd);
                    $("input[name=\'custom_date_end\']").val(ed);
                    $("#custom-date-form-"+i).attr("action", u).submit();
                });
                $(".fgeo").click(function(){
                    t = $(this);
                    i = t.attr("rel-id");
                    fk = t.attr("rel-fk");
                    f = t.attr("rel-f");
                    u = "?g=' . ($cfg["is_be"] == 1 ? '1' : md5($_SESSION["USER_KEY"])) . '&t=' . $type . '&f="+f+"&c=xx&fk="+fk;
                    $("#custom-date-form-"+i).attr("action", u).submit();
                });
                $(".fcompare").click(function(){
                    t = $(this);
                    i = t.attr("rel-id");
                    fk = t.attr("rel-fk");
                    u = "?o=' . ($cfg["is_be"] == 1 ? '1' : md5($_SESSION["USER_KEY"])) . '&t=' . $type . '&fk="+fk;
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

        </script>';

        $html .= VGenerate::declareJS('$("#lb-wrapper ul.responsive-accordion").mouseover(function(){$(this).find(".responsive-accordion-head").addClass("h-msover");$(this).find(".responsive-accordion-panel").addClass("p-msover");}).mouseout(function(){$(this).find(".responsive-accordion-head").removeClass("h-msover");$(this).find(".responsive-accordion-panel").removeClass("p-msover");});');

        return $html;
    }
    /* generate affiliate badge */
    public static function affiliateBadge($usr_affiliate, $af_badge)
    {
        global $language;

//        if (($usr_affiliate == 1 and $af_badge != '') or (isset($_SESSION["USER_AFFILIATE"]) and (int) $_SESSION["USER_AFFILIATE"] == 1 and $af_badge != '') or (isset($_SESSION["USER_PARTNER"]) and (int) $_SESSION["USER_PARTNER"] == 1 and $af_badge != '')) {
        if ($usr_affiliate == 1 and $af_badge != '') {
            return '<i class="affiliate-icon ' . $af_badge . '" rel="tooltip" title="' . $language["frontend.global.verified"] . '"></i> ';
        }
    }
    /* check if user is allowed to submit affiliate or partner requests */
    public static function allowRequest($for = 'affiliate')
    {
        global $cfg, $db;

        $usr_id = (int) $_SESSION["USER_ID"];
        $min    = $cfg[$for . "_requirements_min"];
        $type   = $cfg[$for . "_requirements_type"];
        $sn     = $for == 'partner' ? 'USER_PARTNER_REQUEST' : 'USER_AFFILIATE_REQUEST';

        if (($for == 'affiliate' and $_SESSION["USER_AFFILIATE"] == 1) or ($for == 'partner' and $_SESSION["USER_PARTNER"] == 1)) {
            return;
        }

        switch ($type) {
            case '1': //min N videos views
            default:
                $sql   = sprintf("SELECT SUM(`file_views`) AS `total` FROM `db_videofiles` WHERE `usr_id`='%s'", $usr_id);
                $rs    = $db->execute($sql);
                $total = $rs->fields["total"];
                break;
            case '2': //min N channel views
                $sql   = sprintf("SELECT `ch_views` AS `total` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $usr_id);
                $rs    = $db->execute($sql);
                $total = $rs->fields["total"];
                break;
            case '3': //min N subscribers
                $sql   = sprintf("SELECT COUNT(`sub_id`) AS `total` FROM `db_subscribers` WHERE `usr_id`='%s' LIMIT 1;", $usr_id);
                $rs    = $db->execute($sql);
                $total = $rs->fields["total"];
                break;
            case '4': //min N followers
                $sql   = sprintf("SELECT COUNT(`sub_id`) AS `total` FROM `db_followers` WHERE `usr_id`='%s' LIMIT 1;", $usr_id);
                $rs    = $db->execute($sql);
                $total = $rs->fields["total"];
                break;
        }

        if ($total >= $min) {
            $_SESSION[$sn] = true;

            return true;
        }

        $_SESSION[$sn] = false;

        return false;
    }
    /* generate affiliate badge */
    public static function getAffiliateBadge($usr_key)
    {
        global $db, $class_filter, $language;

        $usr_key = $class_filter->clr_str($usr_key);

        $q = $db->execute(sprintf("SELECT `affiliate_badge` FROM `db_accountuser` WHERE `usr_key`='%s' AND `usr_affiliate`='1' LIMIT 1;", $usr_key));
        $b = $q->fields["affiliate_badge"];

        if ($b != '') {
            return '<i class="affiliate-icon ' . $b . '" rel="tooltip" title="' . $language["frontend.global.verified"] . '"></i> ';
        }
    }
    /* generate payout accordions */
    private static function tpl_payout_accordion($res, $sdf, $edf, $views_min, $views_max)
    {
        include 'f_core/config.backend.php';
        global $language, $class_database, $class_filter, $class_language, $db, $cfg, $smarty;

        $type = self::getType();
        $f    = isset($_GET["f"]) ? $class_filter->clr_str($_GET["f"]) : false;
        $rp   = isset($_GET["rp"]) ? $class_filter->clr_str($_GET["rp"]) : false;

        switch ($f) {
            case "":
            case "today":
                $_f = !$rp ? self::tpl_filter_text($language["account.entry.f.today"], 'f', true) : $language["account.entry.f.lastmonth"];
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
                $_f = isset($_POST["custom_date"]) ? $class_filter->clr_str($_POST["custom_date"]) : $language["account.entry.f.date"];
                break;
            case "range":
                $_f = (isset($_POST["custom_date_start"]) and isset($_POST["custom_date_end"])) ? $class_filter->clr_str($_POST["custom_date_start"]) . ' <span class="red">--</span> ' . $class_filter->clr_str($_POST["custom_date_end"]) : $language["account.entry.f.range"];
                $_f = (isset($_POST["custom_date_start"]) and isset($_POST["custom_date_end"])) ? date('M j', strtotime($class_filter->clr_str($_POST["custom_date_start"]))) . ' <span class="red">--</span> ' . date('M j, o', strtotime($class_filter->clr_str($_POST["custom_date_end"]))) : $language["account.entry.f.range"];
                break;
        }

        $html .= '<div class="entry-list vs-column full">';
        if ($res->fields["file_key"]) {
            while (!$res->EOF) {
                $db_id       = $res->fields["p_id"];
                $usr_user    = $res->fields["usr_user"];
                $usr_key     = $res->fields["usr_key"];
                $file_key    = $res->fields["file_key"];
                $usr_dname   = $res->fields["usr_dname"];
                $p_amount    = $res->fields["p_amount"];
                $p_amount_sh = $res->fields["p_amount_shared"];
                $p_views     = $res->fields["p_views"];
                $p_start     = $res->fields["p_startdate"];
                $p_end       = $res->fields["p_enddate"];
                $title       = $res->fields["file_title"];
                $af_mail     = $res->fields["affiliate_email"];
                $is_af       = $res->fields["usr_affiliate"];
                //$af_custom    = ($res->fields["affiliate_pay_custom"] == 1 and $res->fields["affiliate_custom"] != '') ? unserialize($res->fields["affiliate_custom"]) : false;
                $af_custom = $res->fields["affiliate_custom"] != '' ? unserialize($res->fields["affiliate_custom"]) : false;
                if ($res->fields["affiliate_pay_custom"] == 1 and $af_custom["share"] != '' and $af_custom["units"] != '' and $af_custom["figure"] != '' and $af_custom["currency"] != '') {
                    $cfg["affiliate_payout_currency"] = $af_custom["currency"];
                    $cfg["affiliate_payout_share"]    = $af_custom["share"];
                    $cfg["affiliate_payout_figure"]   = $af_custom["figure"];
                    $p_amount                         = round((($p_views * $cfg["affiliate_payout_figure"]) / $af_custom["units"]), 2);
                    $p_amount_sh                      = round((($cfg["affiliate_payout_share"] * $p_amount) / 100), 2);
                }
                $p_notify = urlencode($cfg["main_url"] . '/' . VHref::getKey('affiliate') . '?do=ipn');
                $p_cancel = urlencode($cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_affiliate') . '?rp=1&ppcancel');
                $p_return = urlencode($cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_affiliate') . '?rp=1');
                $pp_base  = $cfg['paypal_test'] == 1 ? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com';

                $usr_link       = sprintf("<a href=\"%s/%s/%s?u=%s\" target=\"_blank\">%s</a>", $cfg["main_url"], $backend_access_url, VHref::getKey('be_members'), $usr_key, ($usr_dname != '' ? $usr_dname . ' / ' . $usr_user : $usr_user));
                $pp_link_full   = sprintf("%s/cgi-bin/webscr?cmd=_xclick&business=%s&return=%s&cancel_return=%s&notify_url=%s&tax=0&currency=%s&item_name=%s&item_number=%s&quantity=1&amount=%s", $pp_base, $af_mail, $p_return, $p_cancel, $p_notify, $cfg["affiliate_payout_currency"], urlencode($title . ' [' . date('M j', strtotime($p_start)) . ' - ' . date('M j, o', strtotime($p_end)) . ']'), urlencode($type[0] . '|' . $db_id), $p_amount);
                $pp_link_shared = sprintf("%s/cgi-bin/webscr?cmd=_xclick&business=%s&return=%s&cancel_return=%s&notify_url=%s&tax=0&currency=%s&item_name=%s&item_number=%s&quantity=1&amount=%s", $pp_base, $af_mail, $p_return, $p_cancel, $p_notify, $cfg["affiliate_payout_currency"], urlencode($title . ' [' . date('M j', strtotime($p_start)) . ' - ' . date('M j, o', strtotime($p_end)) . ']'), urlencode($type[0] . '|' . $db_id), $p_amount_sh);

                $a_href = '<a href="' . ($cfg["is_be"] ? $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?k=' . $type[0] . $file_key : $cfg["main_url"] . '/' . VGenerate::fileHref($type[0], $file_key, $title)) . '" target="_blank"><img class="r-image" src="' . VGenerate::thumbSigned($type, $file_key, $usr_key, 0, 0, 1) . '"></a>';
                $title  = sprintf("%s <span class=\"rep-wrap\"><span class=\"greyed-out\">[%s - %s]</span>", $title, date('M j', strtotime($p_start)), date('M j, o', strtotime($p_end)));
                $title .= sprintf("<span class=\"rep-info\">%s views</span><span class=\"rep-amt\">$%s</span></span>", $p_views, ($cfg["is_be"] == 1 ? $p_amount : $p_amount_sh));

                $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
                $html .= '<li>';
                $html .= '<div class="responsive-accordion-head">';
                $html .= '<article><h3 class="content-title"><i class="icon-' . $type . '"></i> <span class="bold">' . $title . '</span></h3>';
                $html .= '<div class="place-right expand-entry"><i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i><i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i></div>';
                $html .= '</article>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<div class="vs-column half">';
                $html .= '<ul class="views-details views-thumbs">';
                $html .= '<li>' . $a_href . '</li>';
                $html .= '</ul>';
                $html .= '<ul class="views-details place-left">';
                $html .= $cfg["is_be"] == 1 ? '<li><i class="icon-user"></i> ' . $language["frontend.global.account"] . ': ' . $usr_link . '</li>' : null;
                $html .= '<li><i class="icon-coin"></i> ' . $language["account.entry.payout.amount"] . ': ' . $p_amount_sh . ' ' . $cfg["affiliate_payout_currency"] . (($res->fields["affiliate_pay_custom"] == 1 and $cfg["is_be"] == 1) ? ' <i class="icon-info2" rel="tooltip" title="' . $language["account.entry.payout.custom.text"] . '"></i>' : null) . '</li>';
                $html .= '<li><i class="icon-eye2"></i> ' . $language["frontend.global.views.stat"] . ': ' . $p_views . '</li>';
                $html .= '<li><i class="icon-calendar"></i> ' . $language["account.entry.payout.sd"] . ': ' . date('M j, o', strtotime($p_start)) . '</li>';
                $html .= '<li><i class="icon-calendar"></i> ' . $language["account.entry.payout.ed"] . ': ' . date('M j, o', strtotime($p_end)) . '</li>';
                $html .= '<li><i class="icon-' . ($res->fields["p_paid"] == 1 ? 'check' : 'warning') . '"></i> ' . $language["account.entry.payout.paid"] . ': <span class="' . ($res->fields["p_paid"] == 1 ? 'conf-green' : 'err-red') . '">' . ($res->fields["p_paid"] == 1 ? $language["frontend.global.yes"] : $language["frontend.global.no"]) . '</span></li>';
                $html .= '</ul>';
                $html .= '<div class="clearfix"></div>';
                $html .= '<span class="f-amt" rel-nr="' . $amt . '"><span>';
                $html .= '</div>';
                $html .= '<div class="vs-column half fit">';
                $html .= '<ul class="views-details">';
                $html .= $cfg["is_be"] == 1 ? '<li><i class="icon-paypal"></i> ' . ($af_mail == '' ? '<span class="">affiliate email not available</span>' : ($res->fields["p_paid"] == 0 ? '<a href="' . $pp_link_shared . '" target="_blank">' . $language["account.entry.payout.pay"] . '</a>' : $language["account.entry.payout.paydate"] . ': ' . date('M j, o, H:i:s A', strtotime($res->fields["p_paydate"])))) . '</li>' : null;
                $html .= (!$cfg["is_be"] and $res->fields["p_paid"] == 1) ? '<li><i class="icon-paypal"></i> ' . ($res->fields["p_paid"] == 1 ? $language["account.entry.payout.paydate"] . ': ' . date('M j, o, H:i:s A', strtotime($res->fields["p_paydate"])) : null) . '</li>' : null;
                $html .= '<li><i class="icon-pie"></i> <a href="javascript:;" class="fviews" rel-id="' . $db_id . '" rel-fk="' . $res->fields["file_key"] . '" rel-f="' . $f . '">' . $language["account.entry.act.views"] . '</a> ' . $_f . '</li>';
                $html .= '<li><i class="icon-pie"></i> <a href="javascript:;" class="fviews-range" rel-id="' . $db_id . '" rel-fk="' . $res->fields["file_key"] . '" rel-s="' . $p_start . '" rel-e="' . $p_end . '">' . $language["account.entry.act.views"] . '</a> ' . date('M j', strtotime($p_start)) . ' -- ' . date('M j, o', strtotime($p_end)) . '</li>';
                if (!$cfg["is_be"] and isset($af_custom["maps"]) and $af_custom["maps"] == 1) {
                    $html .= '<li><i class="icon-globe"></i> <a href="javascript:;" class="fgeo" rel-id="' . $db_id . '" rel-fk="' . $res->fields["file_key"] . '" rel-f="' . $f . '">' . $language["account.entry.act.maps"] . '</a> ' . $_f . '</li>';
                    $html .= '<li><i class="icon-globe"></i> <a href="javascript:;" class="fgeo-range" rel-id="' . $db_id . '" rel-fk="' . $res->fields["file_key"] . '" rel-s="' . $p_start . '" rel-e="' . $p_end . '">' . $language["account.entry.act.maps"] . '</a> ' . date('M j', strtotime($p_start)) . ' -- ' . date('M j, o', strtotime($p_end)) . '</li>';
                }
                $html .= '<li><i class="icon-bars"></i> <a href="javascript:;" class="fcompare" rel-id="' . $db_id . '" rel-fk="' . $res->fields["file_key"] . '">' . $language["account.entry.act.comp"] . '</a></li>';
                $html .= '</ul>';
                $html .= '<form id="custom-date-form-' . $db_id . '" name="custom_date_form_db" method="post" action="" target="_blank" class="no-display">
                        <input type="hidden" name="custom_date_submit" value="1">
                                    <input type="hidden" name="custom_date" value="">
                                    <input type="hidden" name="custom_date_start" value="' . $sdf . '">
                                    <input type="hidden" name="custom_date_end" value="' . $edf . '">
                                    <input type="hidden" name="custom_country" value="">
                                    <input type="hidden" name="view_limit_min" value="' . $views_min . '">
                                    <input type="hidden" name="view_limit_max" value="' . $views_max . '">
                                </form>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</li>';
                $html .= '</ul>';

                @$res->MoveNext();
            }
        } else {
            $html .= '<br><div class="no-content"><i class="icon-search"></i> Sorry, no results were found.</div>';
        }
        $html .= '</div>';

        return $html;
    }
    /* generate continents arrays */
    private static function tpl_arrays($key)
    {
        $a = array();

        $a['africa'] = array(
            '002' => 'c1.1',
            '015' => 'c1.2',
            '011' => 'c1.3',
            '017' => 'c1.4',
            '014' => 'c1.5',
            '018' => 'c1.6',
        );

        $a['america'] = array(
            '019' => 'c2.1',
            '021' => 'c2.2',
            '013' => 'c2.3',
            '005' => 'c2.4',
            '029' => 'c2.5',
        );

        $a['asia'] = array(
            '142' => 'c3.1',
            '143' => 'c3.2',
            '030' => 'c3.3',
            '034' => 'c3.4',
            '035' => 'c3.5',
            '145' => 'c3.6',
        );

        $a['europe'] = array(
            '150' => 'c4.1',
            '154' => 'c4.2',
            '155' => 'c4.3',
            '151' => 'c4.4',
            '039' => 'c4.5',
        );

        $a['oceania'] = array(
            '009' => 'c5.1',
            '053' => 'c5.2',
            '054' => 'c5.3',
            '057' => 'c5.4',
            '061' => 'c5.5',
        );

        return $a[$key];
    }
    /* generate continents list */
    private static function tpl_continents()
    {
        global $language, $cfg, $class_filter;

        $africa  = self::tpl_arrays('africa');
        $america = self::tpl_arrays('america');
        $asia    = self::tpl_arrays('asia');
        $europe  = self::tpl_arrays('europe');
        $oceania = self::tpl_arrays('oceania');

        $found = false;
        $r     = isset($_GET["r"]) ? $class_filter->clr_str($_GET["r"]) : false;
        if ($r) {
            $s = array($africa, $america, $asia, $europe, $oceania);

            foreach ($s as $continent) {
                if (isset($continent[$r])) {
                    $found = $language["account.entry.filter." . $continent[$r]];
                }
            }
        }
        $html = '
                <div id="entry-action-buttons2" class="dl-menuwrapper" style="display: none;">
                    <span class="dl-trigger actions-trigger" rel="tooltip" title="' . $language["account.entry.filter.cont"] . '"><i class=""></i> ' . (!$found ? $language["account.entry.filter.global"] : $found) . '</span>
                    <ul class="dl-menu">
                        <li><a href="javascript:;" rel-nr="world" class="cont-sort">' . $language["account.entry.filter.global"] . '</a></li>
                        <li><a href="javascript:;" rel-nr="002" class="cont-sort-off">' . $language["account.entry.filter.c1"] . '</a>' . self::tpl_continents_submenu($africa) . '</li>
                        <li><a href="javascript:;" rel-nr="019" class="cont-sort-off">' . $language["account.entry.filter.c2"] . '</a>' . self::tpl_continents_submenu($america) . '</li>
                        <li><a href="javascript:;" rel-nr="142" class="cont-sort-off">' . $language["account.entry.filter.c3"] . '</a>' . self::tpl_continents_submenu($asia) . '</li>
                        <li><a href="javascript:;" rel-nr="150" class="cont-sort-off">' . $language["account.entry.filter.c4"] . '</a>' . self::tpl_continents_submenu($europe) . '</li>
                        <li><a href="javascript:;" rel-nr="009" class="cont-sort-off">' . $language["account.entry.filter.c5"] . '</a>' . self::tpl_continents_submenu($oceania) . '</li>
                    </ul>
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $(".cont-sort").click(function() {
                            t = $(this);
                            rid = t.attr("rel-nr");
                            b = "' . ($cfg['is_be'] == 1 ? "" : $cfg["main_url"] . '/' . VHref::getKey("affiliate")) . '";
                            u = b + "?' . (isset($_GET["a"]) ? 'a=' . $class_filter->clr_str($_GET["a"]) : (isset($_GET["g"]) ? 'g=' . $class_filter->clr_str($_GET["g"]) : 'a')) . '&t=' . self::getType() . (isset($_GET["f"]) ? '&f=' . $class_filter->clr_str($_GET["f"]) : '&f=today') . '&c=' . (isset($_GET["c"]) ? $class_filter->clr_str($_GET["c"]) : (isset($_POST["custom_country"]) ? $class_filter->clr_str($_POST["custom_country"]) : 'xx')) . '&r="+rid;
                            u+= "' . (isset($_GET["fk"]) ? '&fk=' . $class_filter->clr_str($_GET["fk"]) : null) . '";
                            u+= "' . ((isset($_GET["uk"]) and !isset($_GET["fk"])) ? '&uk=' . $class_filter->clr_str($_GET["uk"]) : null) . '";

                            if ($(".content-filters li a.active").attr("rel-t") == "date" || $(".content-filters li a.active").attr("rel-t") == "range") {
                                $("#custom-date-form").attr("action", u).submit();
                            } else {
                                window.location = u;
                            }
                        });
                    });
                </script>
        ';

        return $html;
    }
    /* generate continent submenu */
    private static function tpl_continents_submenu($array)
    {
        global $language;

        $html = '<ul class="dl-submenu">';
        foreach ($array as $continent_id => $continent_lang) {
            $html .= '<li><a href="javascript:;" rel-nr="' . $continent_id . '" class="cont-sort">' . $language["account.entry.filter." . $continent_lang] . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
    /* generate filter text */
    private static function tpl_filter_text($lang, $param, $remove = false)
    {
        global $language;

        return '<a class="filter-tag" href="javascript:;" rel="nofollow" rel-def="' . (!$remove ? 0 : 1) . '" rel-t="' . $param . '">' . $lang . ' ' . (!$remove ? '<i class="icon-times" rel="tooltip" title="' . $language["account.entry.clear.filter"] . '"></i>' : '<i class="icon-check" rel="tooltip" title="' . $language["account.entry.def.filter"] . '"></i>') . '</a>';
    }
    /* generate sorting section */
    private static function tpl_filter()
    {
        global $language, $cfg, $class_filter, $smarty;

        $type = self::getType();

        $f  = isset($_GET["f"]) ? $class_filter->clr_str($_GET["f"]) : false;
        $r  = isset($_GET["r"]) ? $class_filter->clr_str($_GET["r"]) : false;
        $c  = isset($_GET["c"]) ? $class_filter->clr_str($_GET["c"]) : false;
        $a  = isset($_GET["a"]) ? $class_filter->clr_str($_GET["a"]) : false;
        $o  = isset($_GET["o"]) ? $class_filter->clr_str($_GET["o"]) : false;
        $rp = isset($_GET["rp"]) ? $class_filter->clr_str($_GET["rp"]) : false;

        $_s = array();

        switch ($f) {
            case "":
            case "today":
                $_s[] = !$rp ? self::tpl_filter_text($language["account.entry.f.today"], 'f', true) : self::tpl_filter_text($language["account.entry.f.lastmonth"], 'f', true);
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
                $_s[] = self::tpl_filter_text($language["account.entry.f." . $f], 'f', (($rp and $f == 'lastmonth') ? true : false));
                break;
            case "date":
                $_v   = isset($_POST["custom_date"]) ? $class_filter->clr_str($_POST["custom_date"]) : $language["account.entry.f.date"];
                $_s[] = self::tpl_filter_text($_v, 'f');
                break;
            case "range":
                $_v   = (isset($_POST["custom_date_start"]) and isset($_POST["custom_date_end"])) ? $class_filter->clr_str($_POST["custom_date_start"]) . ' <span class="red">---</span> ' . $class_filter->clr_str($_POST["custom_date_end"]) : $language["account.entry.f.range"];
                $_s[] = self::tpl_filter_text($_v, 'f');
                break;
        }

        if (!$c or $c == 'xx') {
            switch ($r) {
                case "":
                case "world":
                    if (!$a and !$rp) {
                        $_s[] = self::tpl_filter_text($language["account.entry.filter.global"], 'r', true);
                    }
                    break;
                default:
                    $africa  = self::tpl_arrays('africa');
                    $america = self::tpl_arrays('america');
                    $asia    = self::tpl_arrays('asia');
                    $europe  = self::tpl_arrays('europe');
                    $oceania = self::tpl_arrays('oceania');

                    $found = false;
                    $s     = array($africa, $america, $asia, $europe, $oceania);

                    foreach ($s as $continent) {
                        if (isset($continent[$r]) and !$found) {
                            $found = $language["account.entry.filter." . $continent[$r]];
                            $_s[]  = self::tpl_filter_text($found, 'r');
                        }
                    }
                    break;
            }
        } elseif ($c and $c != 'xx') {
            $_s[] = self::tpl_filter_text(strtoupper($c), 'c');
        }

        if ($o) {
            $_s = array();
        }

        if (isset($_GET["fk"]) and $_GET["fk"] != '') {
            $_s[] = self::tpl_filter_text($language["account.entry.search.res"], 'fk');
        }
        if (isset($_GET["uk"]) and $_GET["uk"] != '') {
            $_s[] = self::tpl_filter_text($language["account.entry.search.res"], 'uk');
        }

        $views_min = isset($_SESSION["views_min"]) ? $_SESSION["views_min"] : 0;
        $views_max = isset($_SESSION["views_max"]) ? $_SESSION["views_max"] : 0;

        $_i_fe = '<div class="pull-right" rel="tooltip" title="Show/hide all filters"><i class="toggle-all-filters icon icon-eye2" onclick="if($(\'#search-boxes\').is(\':hidden\')){$(\'section.filter,section.inner-search,#search-boxes,section.action\').css({\'display\':\'inline-block\'});if(!$(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}else{$(\'section.filter,section.inner-search,#search-boxes,section.action\').hide();if($(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}"></i></div>';
        $_i_be = '<i class="toggle-all-filters pull-right icon icon-eye2" onclick="if($(\'#search-boxes\').is(\':hidden\')){$(\'section.filter,section.inner-search,#search-boxes,section.action\').css({\'display\':\'inline-block\'});if(!$(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}else{$(\'section.filter,section.inner-search,#search-boxes,section.action\').hide();if($(\'.view-mode-filters\').hasClass(\'active\')){$(\'.view-mode-filters\').click();}}" rel="tooltip" title="Show/hide all filters"></i>';
        $_i    = $cfg["is_be"] == 1 ? $_i_be : $_i_fe;
//        $_i        = $_i_be;
        $html = '
                                <article>
                                        <h3 class="content-title"><i class="icon-' . (isset($_GET["g"]) ? 'globe' : (isset($_GET["o"]) ? 'bars' : (isset($_GET["rp"]) ? 'paypal' : 'pie'))) . '"></i>' . (isset($_GET["g"]) ? $language["account.entry.act.maps"] : (isset($_GET["o"]) ? $language["account.entry.act.comp"] : (isset($_GET["rp"]) ? $language["account.entry.payout.rep"] : $language["account.entry.act.views"]))) . $_i . '</h3>
                                        <div id="search-boxes">
                                        <section class="inner-search-off place-right">
                                            <form id="view-limits" class="entry-form-class" method="post" action="">
                                                <input type="text" name="view_limit_min_off" id="view-limit-min-off" value="' . $views_min . '" rel="tooltip" title="' . $language["account.entry.min.views"] . '">
                                                <input type="text" name="view_limit_max_off" id="view-limit-max-off" value="' . $views_max . '" rel="tooltip" title="' . $language["account.entry.max.views"] . '">
                                                <input type="submit" name="view_limit_submit" class="no-display">
                                            </form>
                                        </section>

                                        <section class="inner-search">
                                            <div>' . ($cfg["is_be"] == 1 ? $smarty->fetch('tpl_backend/tpl_affiliate/tpl_search_inner.tpl') : $smarty->fetch('tpl_frontend/tpl_affiliate/tpl_search_inner.tpl')) . '</div>
                                        </section>

                                        ' . ($cfg['is_be'] == 1 ? '
                                        <section class="inner-search">
                                            <div>' . $smarty->fetch('tpl_backend/tpl_affiliate/tpl_search_user.tpl') . '</div>
                                        </section>
                                        ' : null) . '

                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="line nlb top"></div>
                                        <section class="filter tft" style="float:left">
                                                <div class="btn-group viewType pull-right">
                            ' . ($cfg["video_module"] == 1 ? '<button rel="tooltip" id="view-mode-video" title="' . str_replace('##TYPE##', $language["frontend.global.v.c"], $language["account.entry.type.views"]) . '" class="viewType_btn viewType_btn-default view-mode-type' . ($type == 'video' ? ' active' : null) . '" rel-t="video"><span><i class="icon-video' . ($type == 'video' ? ' sort-selected' : null) . '"></i>' . str_replace('##TYPE##', $language["frontend.global.v.c"], $language["account.entry.type.views"]) . '</span></button>' : null) . '
                            ' . ($cfg["live_module"] == 1 ? '<button rel="tooltip" id="view-mode-live" title="' . str_replace('##TYPE##', $language["frontend.global.l.c"], $language["account.entry.type.views"]) . '" class="viewType_btn viewType_btn-default view-mode-type' . ($type == 'live' ? ' active' : null) . '" rel-t="live"><span><i class="icon-live' . ($type == 'live' ? ' sort-selected' : null) . '"></i>' . str_replace('##TYPE##', $language["frontend.global.l.c"], $language["account.entry.type.views"]) . '</span></button>' : null) . '
                                                        ' . ($cfg["image_module"] == 1 ? '<button rel="tooltip" id="view-mode-image" title="' . str_replace('##TYPE##', $language["frontend.global.i.c"], $language["account.entry.type.views"]) . '" class="viewType_btn viewType_btn-default view-mode-type' . ($type == 'image' ? ' active' : null) . '" rel-t="image"><span><i class="icon-image' . ($type == 'image' ? ' sort-selected' : null) . '"></i>' . str_replace('##TYPE##', $language["frontend.global.i.c"], $language["account.entry.type.views"]) . '</span></button>' : null) . '
                                                        ' . ($cfg["audio_module"] == 1 ? '<button rel="tooltip" id="view-mode-audio" title="' . str_replace('##TYPE##', $language["frontend.global.a.c"], $language["account.entry.type.views"]) . '" class="viewType_btn viewType_btn-default view-mode-type' . ($type == 'audio' ? ' active' : null) . '" rel-t="audio"><span><i class="icon-headphones' . ($type == 'audio' ? ' sort-selected' : null) . '"></i>' . str_replace('##TYPE##', $language["frontend.global.a.c"], $language["account.entry.type.views"]) . '</span></button>' : null) . '
                                                        ' . ($cfg["document_module"] == 1 ? '<button rel="tooltip" id="view-mode-doc" title="' . str_replace('##TYPE##', $language["frontend.global.d.c"], $language["account.entry.type.views"]) . '" class="viewType_btn viewType_btn-default view-mode-type' . ($type == 'doc' ? ' active' : null) . '" rel-t="doc"><span><i class="icon-file' . ($type == 'doc' ? ' sort-selected' : null) . '"></i>' . str_replace('##TYPE##', $language["frontend.global.d.c"], $language["account.entry.type.views"]) . '</span></button>' : null) . '
                                                        ' . ($cfg["blog_module"] == 1 ? '<button rel="tooltip" id="view-mode-blog" title="' . str_replace('##TYPE##', $language["frontend.global.b.c"], $language["account.entry.type.views"]) . '" class="viewType_btn viewType_btn-default view-mode-type' . ($type == 'blog' ? ' active' : null) . '" rel-t="blog"><span><i class="icon-blog' . ($type == 'blog' ? ' sort-selected' : null) . '"></i>' . str_replace('##TYPE##', $language["frontend.global.b.c"], $language["account.entry.type.views"]) . '</span></button>' : null) . '
                                                </div>
                                        </section>
                                        <section class="action">
                                                ' . (!$o ? '
                                                <div class="btn-group viewType pull-right">
                                                    <button rel="tooltip" onclick="$(\'#time-sort-filters\').stop().slideToggle(\'fast\'); $(\'.view-mode-filters\').toggleClass(\'active\');" title="' . $language["account.entry.filters.show"] . '" class="viewType_btn viewType_btn-default view-mode-filters ' . (isset($_GET["f"]) ? 'active' : null) . '" rel-t="filter"><span><i class="icon-filter"></i>' . $language["account.entry.filters.show"] . '</span></button>
                                                </div>
                                                ' : null) . '
                                        </section>
                                        <div class="clearfix"></div>
                                        <div class="line nlb bottom"></div>
                                </article>
                                <section class="filtertext">
                                    ' . ($_s[0] != '' ? ' <span class="filter-text">' . $language["files.menu.active.filters"] . ' ' . implode('', $_s) . '</span>' : null) . '
                                </section>
                                ' . ($_s[0] != '' ? '
                                <div class="clearfix"></div>
                                <div class="line" style="margin-bottom:0"></div>
                                ' : null) . '
                                ' . ((!$o) ? '
                                <article id="time-sort-filters" style="display: ' . (isset($_GET["f"]) ? 'block' : 'none') . ';">
                                    <h3 class="content-title content-filter"><i class="icon-filter"></i>' . $language["account.entry.filter.results"] . '</h3>
                                    <section class="filter">
                                        ' . (($cfg[($type == 'doc' ? 'document' : $type) . "_module"] == 1 and !$o) ? self::tpl_filters($type) : null) . '
                                    </section>
                                        <div class="clearfix"></div>
                                </article>
                                ' : null) . '
                                <form id="custom-date-form" name="custom_date_form" method="post" action="" class="no-display">
                                    <input type="hidden" name="custom_date_submit" value="1">
                                    <input type="hidden" name="custom_date" id="custom-date" value="' . (isset($_POST["custom_date"]) ? $class_filter->clr_str($_POST["custom_date"]) : null) . '">
                                    <input type="hidden" name="custom_date_start" id="custom-date-start" value="' . (isset($_POST["custom_date_start"]) ? $class_filter->clr_str($_POST["custom_date_start"]) : null) . '">
                                    <input type="hidden" name="custom_date_end" id="custom-date-end" value="' . (isset($_POST["custom_date_end"]) ? $class_filter->clr_str($_POST["custom_date_end"]) : null) . '">
                                    <input type="hidden" name="custom_country" id="custom-country" value="' . (isset($_POST["custom_country"]) ? $class_filter->clr_str($_POST["custom_country"]) : null) . '">
                                        <input type="hidden" name="view_limit_min" id="view-limit-min" value="' . $views_min . '">
                                        <input type="hidden" name="view_limit_max" id="view-limit-max" value="' . $views_max . '">
                                </form>
                        </form>

        ';

        return $html;
    }
    /* generated filter html section */
    private static function tpl_filters($type, $hidden = false)
    {
        global $class_filter, $language;

        $f  = isset($_GET["f"]) ? $class_filter->clr_str($_GET["f"]) : false;
        $rp = isset($_GET["rp"]) ? (int) $_GET["rp"] : false;

        $html = '
                                        <div id="filter-section-' . $type . '"' . ($hidden ? ' style="display: none;"' : null) . '>
                                            <ul class="content-filters">
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="today"' . ((!$f or $f == 'today') ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.today"] . '</a></li>' : null) . '
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="yesterday"' . ($f == 'yesterday' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.yesterday"] . '</a></li>' : null) . '
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="thisweek"' . ($f == 'thisweek' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.thisweek"] . '</a></li>' : null) . '
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="lastweek"' . ($f == 'lastweek' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.lastweek"] . '</a></li>' : null) . '
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="thismonth"' . ($f == 'thismonth' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.thismonth"] . '</a></li>' : null) . '
                                                <li><a href="javascript:;" rel-t="lastmonth"' . (($f == 'lastmonth' or (!$f and $rp)) ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.lastmonth"] . '</a></li>
                                                <li><a href="javascript:;" rel-t="last3months"' . ($f == 'last3months' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.last3months"] . '</a></li>
                                                <li><a href="javascript:;" rel-t="last6months"' . ($f == 'last6months' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.last6months"] . '</a></li>
                                                ' . ($rp ? '<li><a href="javascript:;" rel-t="thisyear"' . ($f == 'thisyear' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.thisyear"] . '</a></li>' : null) . '
                                                ' . ($rp ? '<li><a href="javascript:;" rel-t="lastyear"' . ($f == 'lastyear' ? ' class="active"' : null) . '><i class="icon-clock"></i> ' . $language["account.entry.f.lastyear"] . '</a></li>' : null) . '
                                                ' . (!$rp ? '<li><a href="javascript:;" rel-t="date" class="dpickoff' . $type . ($f == 'date' ? ' active' : null) . '"><i class="icon-calendar"></i> ' . $language["account.entry.f.date"] . '</a></li>' : null) . '
                                                <li><a href="javascript:;" rel-t="range" class="rpickoff' . $type . ($f == 'range' ? ' active' : null) . '"><i class="icon-calendar"></i> ' . $language["account.entry.f.range"] . '</a></li>
                                            </ul>
                                            <div class="dpick' . $type . '" style="position: absolute; right: 0"></div>
                                            <div class="rpick' . $type . '" style="position: absolute; right: 0; z-index: 100; margin-top: 8px; display: none;"></div>
                                        </div>
        ';

        return $html;
    }
}
