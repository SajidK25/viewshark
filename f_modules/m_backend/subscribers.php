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
| Copyright (c) 2013-2017 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('backend', 'language.dashboard');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.subscriber');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.files.menu');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message  = null;
$notice_message = null;
$ipn_check      = ($_POST and isset($_GET["do"]) and $_GET["do"] == 'ipn') ? true : false;
$cfg[]          = $class_database->getConfigurations('paypal_test,paypal_test_email,affiliate_module,affiliate_tracking_id,affiliate_view_id,affiliate_maps_api_key,affiliate_token_script,affiliate_payout_figure,affiliate_payout_units,affiliate_payout_currency,affiliate_payout_share,sub_shared_revenue,subscription_payout_currency,sub_threshold');
$cfg["is_be"]   = 1;
$logged_in      = !$ipn_check ? VLogin::checkBackend(VHref::getKey("be_subscribers")) : null;

if ($cfg["user_subscriptions"] == 1 and $ipn_check) {
//validate ipn when admin pays shared rev subs
    $p             = new VPaypalSubscribe;
    $p->paypal_url = $cfg["paypal_test"] == 1 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

    if ($p->validate_ipn()) {
        $ipn_info = explode('|', urldecode($p->ipn_data["item_number"]));
        $type     = $ipn_info[0];
        $id       = $ipn_info[1];
        $key      = $ipn_info[2];
        $ch_key   = md5($cfg["global_salt_key"] . $id);

        $cc = $db->execute(sprintf("SELECT A.`sub_payout`, A.`sub_amount`, A.`sub_currency`, B.`usr_email`, B.`usr_user` FROM `db_subinvoices` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`db_id`='%s' AND A.`sub_paid`='0' LIMIT 1;", (int) $id));

        if ($ch_key !== $key or !$cc->fields["usr_user"]) {
            die("not valid");
        }

        $tt = 0;
        if ($cc->fields["sub_payout"]) {
            $sp = unserialize($cc->fields["sub_payout"]);
            $tt = count($sp);

            if ($tt > 0) {
                $db->execute(sprintf("UPDATE `db_subinvoices` SET `sub_paid`='1', `pay_date`='%s' WHERE `db_id`='%s' LIMIT 1;", date("Y-m-d H:i:s"), (int) $id));
                $db->execute(sprintf("UPDATE `db_subpayouts` SET `is_paid`='1' WHERE `db_id` IN (%s) AND `is_paid`='0';", implode(',', $sp)));
            }
        }

        if ($tt > 0 and $db->Affected_Rows() > 0) {
            $notifier     = new VNotify;
            $website_logo = $smarty->fetch($cfg["templates_dir"] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
            $cfg          = $class_database->getConfigurations('backend_email,backend_username,affiliate_payout_currency');
            $dd           = $db->execute(sprintf("SELECT `sub_time` FROM `db_subpayouts` WHERE `db_id` IN (%s) ORDER BY `db_id` ASC;", implode(',', $sp)));
            $fnew         = $dd->getrows();
            $sd           = $fnew[0]['sub_time'];
            $ed           = $fnew[count($fnew) - 1]['sub_time'];
            $ss           = date_create($sd);
            $sd           = date_format($ss, "Y.m.d");
            $ss           = date_create($ed);
            $ed           = date_format($ss, "Y.m.d");
            /* user notification */
            $notifier->msg_subj = $language["payment.notification.sub.pay.fe"];
            $_replace           = array(
                '##TITLE##'     => $language["payment.notification.sub.pay.fe"],
                '##LOGO##'      => $website_logo,
                '##H2##'        => $language["recovery.forgot.password.h2"] . $cc->fields["usr_user"] . ',',
                '##TIMEFRAME##' => $sd . ' - ' . $ed,
                '##SUBS##'      => $tt,
                '##AMOUNT##'    => $cc->fields["sub_amount"] . ' ' . $cc->fields["sub_currency"],
                '##YEAR##'      => date('Y'),
            );
            $notifier->dst_mail = $cc->fields["usr_email"];
            $notifier->dst_name = $cc->fields["usr_user"];
            $notifier->Mail('frontend', 'payment_subscriber', $_replace);
            $_output[] = $cfg["backend_username"] . ' -> payment_subscriber -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
            $log_mail  = '.mailer.log';
            VServer::logToFile($log_mail, implode("\n", $_output));
        }
    } else {
        die("not valid");
    }

}

$analytics = false;

if (isset($_GET["do"])) {
    switch ($_GET["do"]) {
        case "showstats":
            echo $ht = VSubscriber::userStats();
            break;
        case "autocomplete":
            $ac = VGenerate::processAutoComplete('account_user');
            break;
    }
}

if (!isset($_GET["do"])) {
    $smarty->assign('file_type', 'sub');
    if (isset($_GET["rg"]) and (int) $_GET["rg"] == 1) {
        $smarty->assign('html_payouts', VSubscriber::html_payouts(1));
    } else {
        $smarty->assign('html_payouts', VSubscriber::html_payouts());
    }

    $class_smarty->displayPage('backend', 'backend_tpl_subscriber', $error_message, $notice_message);
}
