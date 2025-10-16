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

class VPayment extends VSignup
{
    /* check for expired subscription */
    public function checkSubscription($user_id)
    {
        global $class_database, $class_redirect, $cfg;

        $expire_time = $class_database->singleFieldValue('db_packusers', 'expire_time', 'usr_id', intval($user_id));
        if ($expire_time < date("Y-m-d H:i:s")) {
            $_SESSION['renew_id']  = intval($user_id);
            $_SESSION['USER_ID']   = null;unset($_SESSION['USER_ID']);
            $_SESSION['USER_KEY']  = null;unset($_SESSION['USER_KEY']);
            $_SESSION['USER_NAME'] = null;unset($_SESSION['USER_NAME']);
            $class_redirect->to('', $cfg['main_url'] . '/' . VHref::getKey('renew'));
        } else {
            $_SESSION['renew_id'] = '';
        }
    }
    /* get and assign membership types */
    public function getPackTypes($paid = '0')
    {
        global $db, $smarty;
        switch ($paid) {
            case '0':$q = $db->execute("SELECT * FROM `db_packtypes` WHERE `pk_active`='1' ORDER BY `pk_id`;");
                break;
            case '1':$q = $db->execute("SELECT * FROM `db_packtypes` WHERE `pk_active`='1' AND `pk_price` > 0 ORDER BY `pk_id`;");
                break;
        }
        $smarty->assign('memberships', $q->getrows());
    }
    /* check if membership db entry is active */
    public function checkActivePack($pack_id)
    {
        global $class_database;
        $active = $class_database->singleFieldValue('db_packtypes', 'pk_active', 'pk_id', intval($pack_id));
        if ($active == 1) {
            return true;
        } else {
            return false;
        }

    }
    /* get membership id */
    public function getPackID($user_id)
    {
        global $db;
        $q = $db->execute(sprintf("SELECT `pk_id` FROM `db_packusers` WHERE `usr_id`='%s' LIMIT 1;", intval($user_id)));
        return $q->fields['pk_id'];
    }
    /* get membership name */
    public function getUserPack($user = '')
    {
        global $db, $smarty;
        switch ($user) {
            case '':$user       = intval($_SESSION['USER_ID']);
                break;default:$user = intval($user);}
        $q       = $db->execute(sprintf("SELECT `pk_name` FROM `db_packtypes` WHERE `pk_id`='%s' LIMIT 1;", intval(self::getPackID($user))));
        $pk_name = $q->fields['pk_name'];
        $smarty->assign('pk_name', $pk_name);
        return $q->fields['pk_name'];
    }
    /* update free account usage */
    public function updateFreeUsage($user_id)
    {
        global $db;
        $q = $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_free_sub`='1', `usr_active`='1', `usr_status`='1' WHERE `usr_id`='%s' LIMIT 1;", intval($user_id)));
    }
    /* update free account membership after registration */
    public function updateFreeAccount($pk_id, $expire_time, $user_id)
    {
        global $db, $class_database;

        $q = $db->execute(sprintf("UPDATE `db_packusers` SET `pk_id`='%s', `subscribe_time`='%s', `expire_time`='%s', `pk_paid`='0' WHERE `usr_id`='%s' LIMIT 1;", intval($pk_id), date("Y-m-d H:i:s"), $expire_time, intval($user_id)));
        if ($db->Affected_Rows() == 0) {
            $ins = array(
                "pk_id"          => intval($pk_id),
                "usr_id"         => intval($user_id),
                "subscribe_time" => date("Y-m-d H:i:s"),
                "expire_time"    => $expire_time,
            );
            $new = $class_database->doInsert('db_packusers', $ins);
        }
    }
    /* updating free membership registration */
    public function updateFreeEntry()
    {
        global $db, $class_database, $language, $cfg;
        $user_id    = intval(base64_decode($_POST['usr_id']));
        $pk_id      = intval(base64_decode($_POST['pk_id']));
        $free_usage = $class_database->singleFieldValue('db_accountuser', 'usr_free_sub', 'usr_id', $user_id);

        switch ($free_usage) {
            case '0':
                $pk_per      = $class_database->singleFieldValue('db_packtypes', 'pk_period', 'pk_id', $pk_id);
                $expire_time = date("Y-m-d H:i:s", strtotime("+" . $pk_per . " day"));

                self::updateFreeUsage($user_id);
                self::updateFreeAccount($pk_id, $expire_time, $user_id);
                if ($db->Affected_Rows() > 0) {
                    return $language['notif.notice.signup.success'] . $cfg['website_shortname'] . $language['notif.notice.signup.success.more'];
                } else {
                    return false;
                }

                break;
            default:
                return false;
                break;
        }
    }
    /* payment setup */
    public function preparePayment()
    {
        global $db, $cfg, $class_smarty, $language, $smarty;

        $notice_message = $language['notif.notice.signup.payment'];
        $pack_id        = intval(base64_decode($_GET['p']));
        $user_id        = intval(base64_decode($_GET['u']));
        $user_info      = $user_id > 0 ? VUserinfo::getUserInfo($user_id) : null;

        $err_show = (VUserinfo::existingUsername($user_info['uname']) and self::checkActivePack($pack_id)) ? null : 'tpl_error_max';
        $err_show = ($pack_id == 0 or $user_id == 0 or $cfg['paid_memberships'] == 0) ? 'tpl_error_max' : $err_show;

        switch ($err_show) {
            case 'tpl_error_max':break;
            default:
                $q         = $db->execute(sprintf("SELECT * FROM `db_packtypes` WHERE `pk_id`='%s';", $pack_id));
                $pk_info   = $q->getrows();
                $pk_period = $pk_info['0']['pk_period'];
                $err_show  = ($q->recordcount() > 0) ? null : 'tpl_error_max';
                $smarty->assign('pk_info', $pk_info);
        }
        $notice_message = $err_show == 'tpl_error_max' ? null : $notice_message;
        $error_message  = $err_show;

        if (intval($_POST['frontend_membership_type_sel']) != '') {
//select different membership
            $u = intval(base64_decode($_POST['frontend_membership_id']) > 0) ? $_POST['frontend_membership_id'] : null;
            $p = base64_encode(intval($_POST['frontend_membership_type_sel']));
            header('Location: ' . $cfg['main_url'] . '/' . VHref::getKey('signup') . '/' . VHref::getKey('x_payment') . '?p=' . $p . '&u=' . $u);
            die;
        }
        if ($error_message == '') {
            self::buildSelectOptions($pk_period);
            self::packWords($pk_period);
        }
        $class_smarty->displayPage('frontend', 'tpl_payment', $error_message, $notice_message);
        die;
    }
    /* confirm before submitting payment */
    public function continuePayment()
    {
        global $db, $smarty, $language, $cfg;
        $q           = $db->execute(sprintf("SELECT * FROM `db_packtypes` WHERE `pk_id`='%s';", intval(base64_decode($_POST['pk_id']))));
        $pk_info     = $q->getrows();
        $pk_period   = $pk_info[0]['pk_period'];
        $pk_currency = $pk_info[0]['pk_priceunitname'];

        if ($pk_period == 30 or $pk_period == 31 or $pk_period == 365) {$pk_totalprice = intval($_POST['pk_period_sel']) * $pk_info[0]['pk_price'];} else {
            $pk_totalprice = $pk_info[0]['pk_price'];
        }

        self::packWords($pk_period);

        switch ($pk_period) {
            case 30:$pk_dur = $language['frontend.global.months'];
                break;
            case 31:$pk_dur = $language['frontend.global.months'];
                break;
            case 365:$pk_dur = $language['frontend.global.years'];
                break;
            default:$pk_dur = self::packWords($pk_period);
        }
        $pk_totalprice = (($cfg['discount_codes'] == 1) and (($pk_totalprice - self::discountCheck()) > 0)) ? ($pk_totalprice - self::discountCheck()) : $pk_totalprice;
        $smarty->assign('pk_totalprice', $pk_totalprice);
        $smarty->assign('pk_info', $pk_info);
        $smarty->display('tpl_frontend/tpl_auth/tpl_payment_confirm.tpl');
    }
    /* process payment */
    public function doPayment($action)
    {
        global $db, $cfg, $language, $class_smarty, $smarty, $class_database;

        $q           = $db->execute(sprintf("SELECT * FROM `db_packtypes` WHERE `pk_id`='%s';", intval(base64_decode($_POST['pk_id']))));
        $pk_info     = $q->getrows();
        $pk_period   = $pk_info[0]['pk_period'];
        $pk_currency = $pk_info[0]['pk_priceunitname'];

        if ($pk_period == 30 or $pk_period == 31 or $pk_period == 365) {$pk_totalprice = intval($_POST['pk_period_sel']) * $pk_info[0]['pk_price'];} else {
            $pk_totalprice = $pk_info[0]['pk_price'];
        }

        $pk_totalprice = (($cfg['discount_codes'] == 1) and (($pk_totalprice - self::discountCheck()) > 0)) ? ($pk_totalprice - self::discountCheck()) : $pk_totalprice;

        $p             = new VPaypal;
        $p->paypal_url = $cfg['paypal_test'] == 1 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
        $pp_mail       = $cfg['paypal_test'] == 1 ? $cfg['paypal_test_email'] : $cfg['paypal_email'];
        $invoice       = intval(base64_decode($_POST['usr_id'])) . '|' . intval(base64_decode($_POST['pk_id'])) . '|' . intval($_POST['pk_period_sel']) . ' ' . $pk_period . '|' . $pk_totalprice;

        switch ($action) {
            case 'process':
                $p->add_field('business', $pp_mail);
                $p->add_field('return', $cfg['main_url'] . '/' . VHref::getKey('signup') . '?do=success');
                $p->add_field('cancel_return', $cfg['main_url'] . '/' . VHref::getKey('signup') . '?do=cancel');
                $p->add_field('notify_url', $cfg['main_url'] . '/' . VHref::getKey('signup') . '?do=ipn');
                $p->add_field('item_name', $cfg['website_shortname'] . $language['payment.notif.fe.txt5'] . $pk_info[0]['pk_name']);
                $p->add_field('item_number', $invoice);
                $p->add_field('currency_code', $pk_currency);
                $p->add_field('amount', $pk_totalprice);
                $p->add_field('custom', self::discountCheck());

                $p->submit_paypal_post();
                break;

            case 'ipn':
                if ($p->validate_ipn()) {
                    $new          = 0;
                    $ipn_info     = explode('|', urldecode($p->ipn_data['item_number']));
                    $ipn_usr_id   = $ipn_info[0];
                    $ipn_pk_id    = $ipn_info[1];
                    $ipn_pk_per   = $ipn_info[2];
                    $ipn_pk_price = $ipn_info[3];
                    $_per         = explode(' ', $ipn_pk_per);
                    $ipn_period   = $_per[0] > 0 ? $_per[0] * $_per[1] : $_per[1];
                    $expire_time  = date("Y-m-d, H:i:s A", strtotime('+' . $ipn_period . ' days'));

                    $c = $class_database->singleFieldValue('db_packusers', 'pk_id', 'usr_id', intval($ipn_usr_id));
                    if ($c != '') {
                        $m = array("live", "video", "short", "image", "audio", "document", "blog");
                        $r = $db->execute(sprintf("SELECT `pk_total_live`, `pk_total_video`, `pk_total_short`, `pk_total_image`, `pk_total_audio`, `pk_total_doc`, `pk_total_blog` FROM `db_packusers` WHERE `usr_id`='%s' LIMIT 1;", intval($ipn_usr_id)));

                        foreach ($m as $mod) {
                            $db_mod   = $mod == 'document' ? 'doc' : $mod;
                            $db_total = $r->fields["pk_total_" . $db_mod];

                            if ($cfg[$mod . "_module"] == 1 and $db_total > 0) {
                                $q = $db->execute(sprintf("UPDATE `db_%sfiles` SET `is_subscription`='0' WHERE `usr_id`='%s' LIMIT %s;", $db_mod, intval($ipn_usr_id), $db_total));
                            }
                        }

                        $db->execute(sprintf("UPDATE
                        `db_accountuser`, `db_packusers`
                        SET
                        `db_packusers`.`pk_id`='%s',
                        `db_packusers`.`pk_usedspace`='0',
                        `db_packusers`.`pk_usedbw`='0',
                        `db_packusers`.`pk_total_live`='0',
                        `db_packusers`.`pk_total_video`='0',
                        `db_packusers`.`pk_total_short`='0',
                        `db_packusers`.`pk_total_image`='0',
                        `db_packusers`.`pk_total_audio`='0',
                        `db_packusers`.`pk_total_doc`='0',
                        `db_packusers`.`pk_total_blog`='0',
                        `db_packusers`.`pk_paid`='%s',
                        `db_packusers`.`pk_paid_total`=`pk_paid_total`+ %s,
                        `db_packusers`.`subscribe_time`='%s',
                        `db_packusers`.`expire_time`='%s',
                        `db_accountuser`.`usr_active`='1',
                        `db_accountuser`.`usr_status`='1'
                        WHERE
                        `db_accountuser`.`usr_id`='%s' AND
                        `db_packusers`.`usr_id`='%s';", intval($ipn_pk_id), $p->ipn_data['mc_gross'], $p->ipn_data['mc_gross'], date("Y-m-d H:i:s"), $expire_time, intval($ipn_usr_id), intval($ipn_usr_id)));
                    } else {
                        $ins = array(
                            "usr_id"         => intval($ipn_usr_id),
                            "pk_id"          => intval($ipn_pk_id),
                            "pk_usedspace"   => 0,
                            "pk_usedbw"      => 0,
                            "pk_total_video" => 0,
                            "pk_total_short" => 0,
                            "pk_total_live"  => 0,
                            "pk_total_image" => 0,
                            "pk_total_audio" => 0,
                            "pk_total_doc"   => 0,
                            "pk_total_blog"  => 0,
                            "pk_paid"        => $p->ipn_data['mc_gross'],
                            "pk_paid_total"  => $p->ipn_data['mc_gross'],
                            "subscribe_time" => date("Y-m-d H:i:s"),
                            "expire_time"    => $expire_time,
                        );
                        $add   = $class_database->doInsert('db_packusers', $ins);
                        $db_id = $db->Insert_ID();

                        if ($db_id > 0) {
                            $new = 1;

                            $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_active`='1', `usr_status`='1' WHERE `usr_id`='%s' LIMIT 1;", intval($ipn_usr_id)));
                        }
                    }
                    if ($db->Affected_Rows() > 0 or $new == 1) {
                        $notifier     = new VNotify;
                        $website_logo = $smarty->fetch($cfg['templates_dir'] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
                        $user_data    = VUserinfo::getUserInfo($ipn_usr_id);
                        $cfg          = $class_database->getConfigurations('backend_email,backend_username');
                        /* user notification */
                        $_replace = array(
                            '##TITLE##'        => $language['payment.notification.subject.fe'],
                            '##LOGO##'         => $website_logo,
                            '##H2##'           => $language['recovery.forgot.password.h2'] . $user_data['uname'] . ',',
                            '##PACK_NAME##'    => self::getUserPack($ipn_usr_id),
                            '##PAID_TOTAL##'   => $ipn_pk_price . $p->ipn_data['mc_currency'],
                            '##PACK_EXPIRE##'  => $expire_time,
                            '##PAID_RECEIPT##' => "",
                            '##YEAR##'         => date('Y'),
                        );
                        $notifier->dst_mail = VUserinfo::getUserEmail($ipn_usr_id);
                        $notifier->dst_name = $user_data['uname'];
                        $notifier->Mail('frontend', 'payment_notification_fe', $_replace);
                        $_output[] = VUserinfo::getUserName($ipn_usr_id) . ' -> payment_notification_fe -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                        /* admin notification */
                        if ($cfg['backend_notification_payment'] == 1) {
                            $notifier           = new VNotify;
                            $notifier->msg_subj = $language['payment.notification.subject.be'] . $p->ipn_data['payer_email'];
                            $notifier->dst_mail = $cfg['backend_email'];
                            $notifier->dst_name = $cfg['backend_username'];
                            foreach ($p->ipn_data as $key => $value) {$receipt .= $key . ': ' . $value . '<br />';}
                            $_replace = array(
                                '##TITLE##'        => $language['payment.notification.subject.be'] . $p->ipn_data['payer_email'],
                                '##LOGO##'         => $website_logo,
                                '##H2##'           => $language['recovery.forgot.password.h2'] . $cfg['backend_username'] . ',',
                                '##SUB_NAME##'     => $user_data['uname'],
                                '##PACK_NAME##'    => self::getUserPack($ipn_usr_id),
                                '##PAID_TOTAL##'   => $ipn_pk_price . $p->ipn_data['mc_currency'],
                                '##PACK_EXPIRE##'  => $expire_time,
                                '##PAID_RECEIPT##' => urldecode($receipt),
                                '##YEAR##'         => date('Y'),
                            );
                            $notifier->Mail('backend', 'payment_notification_be', $_replace);
                            $_output[] = $cfg['backend_username'] . ' -> payment_notification_be -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                        }
                        $log_mail = '.mailer.log';
                        VServer::logToFile($log_mail, implode("\n", $_output));

                    }
                } else {die("404");}
                break;

            case 'success':
                $class_smarty->displayPage('frontend', 'tpl_signin', '', $language['notif.success.request']);
                break;

            case 'cancel':
                $class_smarty->displayPage('frontend', 'tpl_signup', $language['notif.error.invalid.request'] . $language['notif.error.invalid.request.extra'], '');
                break;
        }
    }
    /* check discount code */
    public function discountCheck()
    {
        global $class_filter, $db;

        if ($_POST['frontend_pkinfo_discount'] != '') {
            $q      = $db->execute(sprintf("SELECT `dc_amount` FROM `db_packdiscounts` WHERE `dc_active`='1' and BINARY `dc_code`='%s' LIMIT 1;", $class_filter->clr_str($_POST['frontend_pkinfo_discount'])));
            $amount = $q->fields['dc_amount'];
            if ($amount > 0) {
                return $amount;
            } else {
                return 0;
            }

        }
    }
    /* text for membership durations */
    public function packWords($pk_period)
    {
        global $language, $smarty;

        $numbers_array = explode(',', $language['frontend.pkinfo.pkdur2']);
        $words_array   = explode(',', $language['frontend.pkinfo.pkdur1']);
        $words_key     = array_keys($numbers_array, $pk_period);
        $smarty->assign('pk_periodtext', $words_array[$words_key[0]]);
        return $words_array[$words_key[0]];
    }
    /* membership select list options */
    public function buildSelectOptions($pk_period)
    {
        global $cfg, $smarty, $language;

        $pm      = ($cfg['paypal_payments'] == 1 and $cfg['moneybookers_payments'] == 1) ? $language['backend.menu.members.entry1.sub1.m1'] . ',' . $language['backend.menu.members.entry1.sub1.m2'] : (($cfg['paypal_payments'] == 1 and $cfg['moneybookers_payments'] == 0) ? $language['backend.menu.members.entry1.sub1.m1'] : (($cfg['paypal_payments'] == 0 and $cfg['moneybookers_payments'] == 1) ? $language['backend.menu.members.entry1.sub1.m2'] : null));
        $methods = explode(',', $pm);

        switch ($pk_period) {
            case 365:for ($i = 1; $i <= 5; $i++) {$period_opts .= '<option value="' . $i . '">' . $i . '</option>';}break;
            case ($pk_period == 31 or $pk_period == 30): for ($i = 1; $i <= 12; $i++) {$period_opts .= '<option value="' . $i . '">' . $i . '</option>';}
        }
        foreach ($methods as $k => $v) {
            $payment_method_opts .= '<option value="' . $v . '">' . $v . '</option>'; 
        }
        $smarty->assign('pk_period_opts', $period_opts);
        $smarty->assign('pk_payment_method_opts', $payment_method_opts);
    }
}
