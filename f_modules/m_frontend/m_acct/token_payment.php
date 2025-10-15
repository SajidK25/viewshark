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

define('_ISVALID', true);

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('backend', 'language.members.entries');
include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

$cfg = $class_database->getConfigurations('paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email,backend_notification_payment,backend_email,backend_username');

$error_message  = null;
$notice_message = null;

if ($_POST and isset($_GET["do"]) and $_GET["do"] == "ipn") {
    $p             = new VPaypalToken;
    $p->paypal_url = $cfg["paypal_test"] == 1 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

    if ($p->validate_ipn()) {
        $ipn_info   = explode('|', urldecode($p->ipn_data["item_number"]));
        $usr_id     = (int) $ipn_info[0];
        $item_id    = (int) $ipn_info[1];
        $item_amt   = (int) $ipn_info[2];
        $item_price = $class_filter->clr_str($p->ipn_data["mc_gross"]);
        $txn_id     = $class_filter->clr_str($p->ipn_data["txn_id"]);
        $hash       = $ipn_info[3];
        $pps        = $usr_id . '|' . $item_id . '|' . $item_amt;

        if ($hash == md5($pps . $cfg["global_salt_key"])) {
            $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_tokencount`=`usr_tokencount`+%s WHERE `usr_id`='%s' LIMIT 1;", $item_amt, $usr_id));

            if ($db->Affected_Rows() > 0) {
                $receipt = null;
                foreach ($p->ipn_data as $key => $value) {$receipt .= $key . ': ' . $value . '<br>';}

                $ins_array = array(
                    'usr_id'      => $usr_id,
                    'tk_id'       => $item_id,
                    'tk_amount'   => $item_amt,
                    'tk_price'    => $item_price,
                    'tk_date'     => date("Y-m-d H:i:s"),
                    'txn_id'      => $txn_id,
                    'txn_receipt' => str_replace('<br>', "\n", $receipt),
                );
                $class_database->doInsert('db_tokenpayments', $ins_array);

                /* mail notifications */
                $notifier     = new VNotify;
                $website_logo = $smarty->fetch($cfg["templates_dir"] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
                $user_data    = VUserinfo::getUserInfo($usr_id);
                /* user notification */
                $_replace = array(
                    '##TITLE##' => $language["payment.notification.token.subj"],
                    '##LOGO##'  => $website_logo,
                    '##H2##'    => $language["recovery.forgot.password.h2"] . $user_data["uname"] . ',',
                    '##NR##'    => $item_amt,
                    '##YEAR##'  => date('Y'),
                );
                $notifier->dst_mail = VUserinfo::getUserEmail($usr_id);
                $notifier->dst_name = $user_data["uname"];
                $notifier->Mail('frontend', 'token_notification_fe', $_replace);

                $_output[] = $user_data["uname"] . ' -> token_notification_fe -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                /* admin notification */
                if ($cfg["backend_notification_payment"] == 1) {
                    include 'f_core/config.backend.php';
                    $main_url = $cfg["main_url"] . '/' . $backend_access_url;

                    $notifier->msg_subj = $language["payment.notification.token.subj.be"] . urldecode($p->ipn_data["payer_email"]);
                    $notifier->dst_mail = $cfg["backend_email"];
                    $notifier->dst_name = $cfg["backend_username"];

                    $_replace = array(
                        '##TITLE##'        => $notifier->msg_subj,
                        '##LOGO##'         => $website_logo,
                        '##H2##'           => $language["recovery.forgot.password.h2"] . $cfg["backend_username"] . ',',
                        '##USER##'         => '<a href="' . $main_url . '/' . VHref::getKey('be_members') . '?u=' . $user_data["key"] . '" target="_blank">' . $user_data["uname"] . '</a>',
                        '##NR##'           => $item_amt,
                        '##PAID##'         => $p->ipn_data["mc_gross"] . $p->ipn_data["mc_currency"],
                        '##PAID_RECEIPT##' => urldecode($receipt),
                        '##YEAR##'         => date('Y'),
                    );
                    $notifier->Mail('backend', 'token_notification_be', $_replace);

                    $_output[] = $cfg["backend_username"] . ' -> token_notification_be -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                }

                $log_mail = '.mailer.log';
                VServer::logToFile($log_mail, implode("\n", $_output));
            }
        }
    }
}
