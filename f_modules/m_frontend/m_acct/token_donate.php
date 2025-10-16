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

define('_ISVALID', true);

if (isset($_SERVER["HTTP_ORIGIN"]) === true) {
    $origin = $_SERVER["HTTP_ORIGIN"];

    $allowed_origins = array(
        "http://192.168.100.77",
        "http://192.168.100.77:3000",
    );

    if (in_array($origin, $allowed_origins, true) === true) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET,POST');
        header('Access-Control-Allow-Headers: VS-Custom-Header');
    }

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        exit; // OPTIONS request wants only the policy, we can stop here
    }

    $main_dir = realpath(dirname(__FILE__) . '/../../../');
    set_include_path($main_dir);

    include_once 'f_core/config.core.php';
    include_once $class_language->setLanguageFile('backend', 'language.members.entries');
    include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

    $cfg = $class_database->getConfigurations('paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email,backend_notification_payment,backend_email,backend_username');

    $error_message  = null;
    $notice_message = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $out = array("valid" => 0);

        $cc     = $class_filter->clr_str($_POST["a"]);
        $ff     = $class_filter->clr_str($_POST["b"]);
        $estr   = $class_filter->clr_str($_POST["c"]);
        $amount = $class_filter->clr_str($_POST["d"]);

        $rs = $db->execute(sprintf("SELECT `db_id`, `channel_owner`, `channel_id`, `usr_id`, `usr_key`, `chat_user` FROM `db_livechat` WHERE `chat_id`='%s' AND `stream_id`='%s' LIMIT 1;", $cc, $ff));

        if ($rs->fields["db_id"]) {
            $ch_owner = $rs->fields["channel_owner"];
            $ch_user  = $rs->fields["chat_user"];
            $ch_id    = $rs->fields["channel_id"];
            $usr_id   = $rs->fields["usr_id"];
            $usr_key  = $rs->fields["usr_key"];

            $p 		  = $db->execute(sprintf("SELECT `usr_key`, `usr_photo`, `usr_profileinc`, `usr_tokencount` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
            if ($p->fields['usr_key']) {
          		$tokens = $p->fields['usr_tokencount'];
          		$pimg 	= VUseraccount::getProfileImage_inc($p->fields['usr_key'], $p->fields['usr_photo'], $p->fields['usr_profileinc']);
            } else {
          		echo json_encode($out); return;
            }

            $cstr = md5($ff . $cc . $ch_user . $amount . $cfg["live_chat_salt"]);

            if ($estr == $cstr and $amount <= $tokens) {
                $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_tokencount`=`usr_tokencount`-%s WHERE `usr_id`='%s' LIMIT 1;", $amount, $usr_id));

                if ($db->Affected_Rows() > 0) {
                    $ins = array(
                        "tk_from"      => $usr_id,
                        "tk_to"        => $ch_id,
                        "tk_from_user" => $ch_user,
                        "tk_to_user"   => $ch_owner,
                        "tk_amount"    => $amount,
                        "tk_date"      => date("Y-m-d H:i:s"),
                    );
                    $class_database->doInsert('db_tokendonations', $ins);

                    if ($db->Affected_Rows() > 0) {
                        /* mail notifications */
                        $notifier     = new VNotify;
                        $website_logo = $smarty->fetch($cfg["templates_dir"] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
                        $user_data    = VUserinfo::getUserInfo($ch_id);
                        /* user notification */
                        $_replace = array(
                            '##TITLE##' => $language["payment.notification.donate.subj"],
                            '##LOGO##'  => $website_logo,
                            '##H2##'    => $language["recovery.forgot.password.h2"] . $user_data["uname"] . ',',
                            '##NR##'    => '<b>' . $amount . '</b>',
                            '##USER##'  => '<a href="' . VHref::channelURL(["username" => $ch_user]) . '" target="_blank">' . $ch_user . '</a>',
                            '##YEAR##'  => date('Y'),
                        );
                        $notifier->dst_mail = VUserinfo::getUserEmail($ch_id);
                        $notifier->dst_name = $user_data["uname"];
                        $notifier->Mail('frontend', 'token_donation_fe', $_replace);

                        $_output[] = $user_data["uname"] . ' -> token_donation_fe -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                        /* admin notification */
                        if ($cfg["backend_notification_payment"] == 1) {
                            include 'f_core/config.backend.php';
                            $main_url = $cfg["main_url"] . '/' . $backend_access_url;

                            $notifier->msg_subj = $language["payment.notification.donate.subj"];
                            $notifier->dst_mail = $cfg["backend_email"];
                            $notifier->dst_name = $cfg["backend_username"];
                            $user_data2         = VUserinfo::getUserInfo($ch_id);

                            $_replace = array(
                                '##TITLE##' => $notifier->msg_subj,
                                '##LOGO##'  => $website_logo,
                                '##SUBJ##'  => str_replace(array('##USER1##', '##USER2##', '##NR##'), array($ch_user, $user_data2["uname"], $amount), $language["payment.notification.donate.subj.be"]),
                                '##H2##'    => $language["recovery.forgot.password.h2"] . $cfg["backend_username"] . ',',
                                '##USER1##' => '<a href="' . $main_url . '/' . VHref::getKey('be_members') . '?u=' . $usr_key . '" target="_blank">' . $ch_user . '</a>',
                                '##USER2##' => '<a href="' . $main_url . '/' . VHref::getKey('be_members') . '?u=' . $user_data2["key"] . '" target="_blank">' . $user_data2["uname"] . '</a>',
                                '##NR##'    => '<b>' . $amount . '</b>',
                                '##YEAR##'  => date('Y'),
                            );
                            $notifier->Mail('backend', 'token_donation_be', $_replace);

                            $_output[] = $cfg["backend_username"] . ' -> token_donation_be -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                        }

                        $log_mail = '.mailer.log';
                        VServer::logToFile($log_mail, implode("\n", $_output));

                        $out["pimg"] = $pimg; $out["valid"] = 1;
                        echo json_encode($out);

                        return;
                    }
                }
            }
        }

        echo json_encode($out);
        return;
    }
}
