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

    $error_message  = null;
    $notice_message = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $rs = array();
        $us = array();
        $ls = array();
        $cn = explode(',', $language["supported_currency_names"]);
        $cu = explode(',', $language["supported_currency_codes"]);

        $cid          = $class_filter->clr_str($_POST["a"]);
        $file_key     = $class_filter->clr_str($_POST["b"]);
        $get_token_nr = isset($_POST["c"]);

        $ch = $db->execute(sprintf("SELECT A.`db_id`, A.`usr_id`, A.`usr_key`, B.`usr_tokencount` FROM `db_livechat` A, `db_accountuser` B WHERE A.`chat_id`='%s' AND A.`stream_id`='%s' AND A.`usr_id`=B.`usr_id` LIMIT 1;", $cid, $file_key));

        if (!$ch->fields["db_id"] or ($ch->fields["db_id"] and $ch->fields["usr_id"] == 0)) {
            $ls["l"] = "auth";

            echo json_encode($ls);
            return;
        }
        $usr_id = $ch->fields["usr_id"];
        $us[0]  = (int) $ch->fields["usr_tokencount"];

        if ($get_token_nr) {
            echo json_encode($us);
            return;
        }

        $pp            = $class_database->getConfigurations('paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email');
        $paypal_url    = $pp["paypal_test"] == 0 ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $paypal_mail   = $pp["paypal_test"] == 0 ? $pp["paypal_email"] : $pp["paypal_test_email"];
        $paypal_return = rawurlencode($cfg["main_url"] . '/' . VHref::getKey('watch') . '?l=' . $file_key . '&fst=1');
        $paypal_cancel = rawurlencode($cfg["main_url"] . '/' . VHref::getKey('watch') . '?l=' . $file_key);
        $paypal_ipn    = rawurlencode($cfg["main_url"] . '/' . VHref::getKey('tokenpayment') . '?do=ipn');

        $paypal_param = array(
            "cmd"           => "_xclick",
            "rm"            => 2,
            "business"      => $paypal_mail,
            "return"        => $paypal_return,
            "cancel_return" => $paypal_cancel,
            "notify_url"    => $paypal_ipn,
            "item_name"     => "##ITEM_NAME##",
            "item_number"   => "##PP_ITEM##",
            "currency_code" => "##PP_CURRENCY##",
            "amount"        => "##PP_AMOUNT##",
            "custom"        => "",
        );
        $params = array();
        foreach ($paypal_param as $pk => $pv) {
            $params[] = sprintf("%s=%s", $pk, $pv);
        }

        $paypal_link = sprintf("%s?%s", $paypal_url, implode("&", $params));

        $tks = $db->execute(sprintf("SELECT `tk_id`, `tk_name`, `tk_slug`, `tk_price`, `tk_currency`, `tk_amount` FROM `db_livetoken` WHERE `tk_active`='1';"));
        if ($tks->fields["tk_id"]) {
            $i = 0;

            while (!$tks->EOF) {
                $rs[$i][0] = $tks->fields["tk_id"];
                $rs[$i][1] = $tks->fields["tk_name"];
                $rs[$i][2] = $tks->fields["tk_price"];
                $rs[$i][3] = $tks->fields["tk_currency"];
                $rs[$i][4] = $tks->fields["tk_amount"];

                $paypal_string  = $usr_id . '|' . $rs[$i][0] . '|' . $rs[$i][4];
                $paypal_item    = rawurlencode($paypal_string . '|' . md5($paypal_string . $cfg["global_salt_key"]));
                $paypal_name    = rawurlencode($cfg["website_shortname"] . ' - ' . $rs[$i][1]);
                $paypal_search  = array("##ITEM_NAME##", "##PP_ITEM##", "##PP_CURRENCY##", "##PP_AMOUNT##");
                $paypal_replace = array($paypal_name, $paypal_item, $rs[$i][3], $rs[$i][2]);

                $rs[$i][5] = str_replace($paypal_search, $paypal_replace, $paypal_link);

                $ak        = array_search($rs[$i][3], $cn);
                $rs[$i][3] = $cu[$ak];

                $i += 1;
                $tks->MoveNext();
            }
        }

        $ls["u"] = $us;
        $ls["l"] = $rs;

        echo json_encode($ls);
    }
}
