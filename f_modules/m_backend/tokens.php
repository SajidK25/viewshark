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
| Copyright (c) 2013-2020 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('backend', 'language.dashboard');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.subscriber');
include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.signup');

$error_message  = null;
$notice_message = null;
$ipn_check      = ($_POST and isset($_GET["do"]) and $_GET["do"] == 'ipn') ? true : false;
$cfg[]          = $class_database->getConfigurations('paypal_test,paypal_test_email,affiliate_module,affiliate_tracking_id,affiliate_view_id,affiliate_maps_api_key,affiliate_token_script,affiliate_payout_figure,affiliate_payout_units,affiliate_payout_currency,affiliate_payout_share,sub_shared_revenue,subscription_payout_currency,sub_threshold,token_threshold');
$cfg["is_be"]   = 1;
$logged_in      = !$ipn_check ? VLogin::checkBackend(VHref::getKey("be_tokens")) : null;

if ($ipn_check) {
//validate ipn
    $p             = new VPaypalToken;
    $p->paypal_url = $cfg["paypal_test"] == 1 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

    if ($p->validate_ipn()) {
        $ipn_info = explode('|', urldecode($p->ipn_data["item_number"]));
        $type     = $ipn_info[0];
        $id       = $ipn_info[1];
        $key      = $ipn_info[2];
        $ch_key   = md5($cfg["global_salt_key"] . $id);

        $cc = $db->execute(sprintf("SELECT A.`tk_payout`, A.`tk_amount`, A.`tk_currency`, B.`usr_email`, B.`usr_user` FROM `db_tokeninvoices` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`db_id`='%s' AND A.`tk_paid`='0' LIMIT 1;", (int) $id));

        if ($ch_key !== $key or !$cc->fields["usr_user"]) {
            die("not valid");
        }

        $tt = 0;
        if ($cc->fields["tk_payout"]) {
            $sp  = unserialize($cc->fields["tk_payout"]);
            $ttc = count($sp);

            if ($ttc > 0) {
                $t  = $db->execute(sprintf("SELECT SUM(`tk_amount`) AS `total_tokens` FROM `db_tokendonations` WHERE `db_id` IN (%s) AND `is_paid`='0';", implode(',', $sp)));
                $tt = $t->fields["total_tokens"];
                $db->execute(sprintf("UPDATE `db_tokendonations` SET `is_paid`='1' WHERE `db_id` IN (%s) AND `is_paid`='0';", implode(',', $sp)));
                $receipt = null;
                foreach ($p->ipn_data as $key => $value) {$receipt .= $key . ': ' . $value . "\n";}
                $db->execute(sprintf("UPDATE `db_tokeninvoices` SET `tk_paid`='1', `pay_date`='%s', `txn_id`='%s', `txn_receipt`='%s' WHERE `db_id`='%s' LIMIT 1;", date("Y-m-d H:i:s"), $class_filter->clr_str($p->ipn_data["txn_id"]), $receipt, (int) $id));
            }
        }

        if ($tt > 0 and $db->Affected_Rows() > 0) {
            $notifier     = new VNotify;
            $website_logo = $smarty->fetch($cfg["templates_dir"] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
            $dd           = $db->execute(sprintf("SELECT `tk_date` FROM `db_tokendonations` WHERE `db_id` IN (%s) ORDER BY `db_id` ASC;", implode(',', $sp)));
            $fnew         = $dd->getrows();
            $sd           = $fnew[0]['tk_date'];
            $ed           = $fnew[count($fnew) - 1]['tk_date'];
            $ss           = date_create($sd);
            $sd           = date_format($ss, "Y.m.d");
            $ss           = date_create($ed);
            $ed           = date_format($ss, "Y.m.d");
            /* user notification */
            $notifier->msg_subj = $language["payment.notification.tk.pay.fe"];
            $_replace           = array(
                '##TITLE##'     => $notifier->msg_subj,
                '##LOGO##'      => $website_logo,
                '##H2##'        => $language["recovery.forgot.password.h2"] . $cc->fields["usr_user"] . ',',
                '##TIMEFRAME##' => $sd . ' - ' . $ed,
                '##TOKENS##'    => $tt,
                '##AMOUNT##'    => $cc->fields["tk_amount"] . ' ' . $cc->fields["tk_currency"],
                '##YEAR##'      => date('Y'),
            );
            $notifier->dst_mail = $cc->fields["usr_email"];
            $notifier->dst_name = $cc->fields["usr_user"];
            $notifier->Mail('frontend', 'payout_tokens', $_replace);

            $_output[] = $cfg["backend_username"] . ' -> payout_tokens -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
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
            echo $ht = VToken::userStats();
            break;
        case "autocomplete":
            $ac = VGenerate::processAutoComplete('account_user');
            break;
    }
}

if (!isset($_GET["do"])) {
    $smarty->assign('file_type', 'sub');
    if (isset($_GET["rg"]) and (int) $_GET["rg"] == 1) {
        $smarty->assign('html_payouts', VToken::html_payouts(1));
    } else {
        $smarty->assign('html_payouts', VToken::html_payouts());
    }

    $class_smarty->displayPage('backend', 'backend_tpl_token', $error_message, $notice_message);
}
