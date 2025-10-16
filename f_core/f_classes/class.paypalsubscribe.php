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

class VPaypalSubscribe
{
    public $paypal_host; // holds the paypal hostname
    public $paypal_url; // holds the paypal verification url
    public $paypal_mail; // holds your primary paypal email
    public $ipn_log; // bool: log IPN results to text file?
    public $ipn_log_file; // filename of the IPN log
    public $last_error; // holds the last error encountered
    public $ipn_response; // holds the IPN response from paypal
    public $ipn_data = array(); // array contains the POST values for IPN
    public $fields   = array(); // array holds the fields to submit to paypal
    /* paypal setup */
    public function VPaypalSubscribe()
    {
        global $cfg, $class_database;

        $pp = $class_database->getConfigurations('paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email');

        $this->paypal_host  = $pp['paypal_test'] == 0 ? 'www.paypal.com' : 'www.sandbox.paypal.com';
        $this->paypal_url   = $pp['paypal_test'] == 0 ? 'https://www.paypal.com/cgi-bin/webscr' : 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        $this->paypal_mail  = $pp['paypal_test'] == 0 ? $pp['paypal_email'] : $pp['paypal_test_email'];
        $this->ipn_log      = $pp['paypal_logging'] == 1 ? true : false;
        $this->ipn_log_file = $cfg['main_dir'] . '/' . $pp['paypal_log_file'];
        $this->last_error   = '';
        $this->ipn_response = '';
    }
    /* validate IPN */
    public function validate_ipn()
    {
        define("LOG_FILE", $this->ipn_log_file);
        define("DEBUG", 0);

        global $cfg, $class_filter;

        $ver = false;
        $adr = $_SERVER[REM_ADDR];
        //$ppn         = array('173.0.80.0/24', '173.0.81.0/24', '173.0.82.0/24', '173.0.83.0/24');
        $ppn = array(
            '173.0.80.0/21',
            '173.0.80.0/22',
            '173.0.81.0/24',
            '173.0.84.0/24',
            '173.0.88.0/21',
            '173.0.88.0/24',
            '173.0.93.0/24',
            '173.0.94.0/24',
            '173.0.95.0/24',
            '64.4.240.0/21',
            '64.4.240.0/22',
            '64.4.240.0/24',
            '64.4.241.0/24',
            '64.4.242.0/24',
            '64.4.243.0/24',
            '64.4.244.0/22',
            '64.4.244.0/24',
            '64.4.246.0/24',
            '64.4.247.0/24',
            '64.4.248.0/22',
            '64.4.248.0/23',
            '64.4.248.0/24',
            '64.4.249.0/24',
            '64.4.250.0/23',
            '64.4.250.0/24',
            '66.211.168.0/22',
            '66.211.168.0/23',
            '66.211.170.0/23',
            '91.243.72.0/23',
        );

        foreach ($ppn as $range) {
            if (VIPrange::ip_in_range($adr, $range)) {
                $ver = true;
            }

        }

        if (!$ver) {
            return false;
        }

        $myPost         = array();
        $raw_post_data  = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);

        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);

            if (count($keyval) == 2) {
                if ($keyval[0] == 'payment_date') {
                    if (substr_count($keyval[1], '+') == 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }

                }

                $myPost[$keyval[0]] = rawurldecode($keyval[1]);
            }
        }
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exits = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exits == true && get_magic_quotes_gpc() == 1) {
                $value = rawurlencode(stripslashes($value));
            } else {
                $value = rawurlencode($value);
            }
            $req .= "&$key=$value";
            $this->ipn_data[$key] = $value;
        }
        $req = str_replace('%2B', '%20', $req);

        $ch = curl_init($this->paypal_url);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        if (DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('User-Agent: PHP-IPN-Verification-Script', 'Connection: Close'));

        $res = curl_exec($ch);

        if (curl_errno($ch) != 0) {
// cURL error
            if (DEBUG == true) {
                error_log(date('[Y-m-d H:i e] ') . "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
            }
        } else {
            // Log the entire HTTP response if debug is switched on.
            if (DEBUG == true) {
                error_log(date('[Y-m-d H:i e] ') . "HTTP request of validation request:" . curl_getinfo($ch, CURLINFO_HEADER_OUT) . " for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
                error_log(date('[Y-m-d H:i e] ') . "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
            }
        }

        curl_close($ch);

        $tokens = explode("\r\n\r\n", trim($res));
        $res    = trim(end($tokens));

        $payment_status   = $_POST['payment_status'];
        $receiver_email   = $_POST['receiver_email'];
        $payment_amount   = $_POST['mc_gross'] != '' ? $_POST['mc_gross'] : $_POST['mc_amount3'];
        $payment_currency = $_POST['mc_currency'];
        $discount         = $_POST['custom'];
        $item             = explode('|', rawurldecode($_POST['item_number']));
        $item_id          = $item[1];
        $pk_key           = $item[2];
        $quantity         = $item[4];
        $price_fr         = $item[6];
        $ch_pk_key        = md5($cfg['global_salt_key'] . $item_id);

        if ($_POST['txn_type'] == 'recurring_payment_suspended_due_to_max_failed_payment') {
            $sub_id    = $_POST['recurring_payment_id'];
            $sub_check = self::checkSuspend($sub_id);

            $this->ipn_response = 'RECURRING PAYMENT SUSPENDED';
            $this->log_ipn_results(true);

            return $sub_check;
        }

        if ($pk_key !== $ch_pk_key) {
            $this->ipn_response = 'FAILEDPKEY';
            $this->log_ipn_results(false);

            return false;
        } elseif (strcmp($res, "VERIFIED") == 0) {
            // check the payment_status is Completed
            // check that txn_id has not been previously processed - not really needed for now
            // check that receiver_email is your Primary PayPal email
            // check that payment_amount/payment_currency are correct
            // return true for membership updating
            if ($_POST['txn_type'] == 'subscr_cancel' or $_POST['txn_type'] == 'subscr_eot') {
                $sub_check          = self::checkCancel($item_id, $_POST['subscr_id']);
                $this->ipn_response = 'VERIFIED CANCELATION';
                $this->log_ipn_results(true);

                return $sub_check;
            } elseif ($payment_status == 'Completed') {
                if ($_POST['custom'] == 'sub') {
                    $this->ipn_response = 'VERIFIED PAYOUT';
                    $this->log_ipn_results(true);

                    return true;
                } elseif ($receiver_email == $this->paypal_mail) {
                    if (self::checkItem($item_id, $price_fr, $discount, $payment_amount, $payment_currency)) {
                        $this->ipn_response = 'VERIFIED SUBSCRIPTION PAYMENT';
                        $this->log_ipn_results(true);

                        return true;
                    } else {
                        return false;
                    }

                }
            } else {
                $this->ipn_response = $_POST['txn_type'] == 'subscr_signup' ? 'VERIFIED RECURRING SUBSCRIPTION' : 'FAILED';
                $this->log_ipn_results(false);

                return false;
            }
        } elseif (strcmp($res, "INVALID") == 0) {
            $this->ipn_response = "FAILED2";
            $this->log_ipn_results(false);

            return false;
        }
    }
    /* validate paid item */
    public function checkItem($item_id, $price_fr, $discount, $payment_amount, $payment_currency)
    {
        global $db;

        $q     = $db->execute(sprintf("SELECT `pk_price`, `pk_priceunitname` FROM `db_subtypes` WHERE `pk_id`='%s' AND `pk_active`='1' LIMIT 1;", (int) $item_id));
        $price = $q->fields['pk_price'];
        $unit  = $q->fields['pk_priceunitname'];

        if ($price > 0 and $unit != '') {
            $discount = $discount == '' ? 0 : $discount;
            $total    = (($price * $price_fr) - $discount);

            if (bccomp($payment_amount, $total, 2) == 0 and $payment_currency == $unit) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    /* validate suspend sub request */
    private static function checkSuspend($sub_id)
    {
        global $db, $class_filter;

        $sub_id = $class_filter->clr_str($sub_id);

        $rs = $db->execute(sprintf("SELECT `db_id` FROM `db_subusers` WHERE `subscriber_id`='%s' LIMIT 1;", $sub_id));
        if ($rs->fields['db_id']) {
            $db->execute(sprintf("UPDATE `db_subusers` SET `pk_id`='0', `expire_time`='%s' WHERE `subscriber_id`='%s' LIMIT 1;", date("Y-m-d H:i:s"), $sub_id));

            if ($db->Affected_Rows() > 0) {
                return true;
            }

        }

        return false;
    }
    /* validate cancel sub request */
    private static function checkCancel($item_id, $sub_id)
    {
        global $db, $class_filter;

        $sub_id = $class_filter->clr_str($sub_id);

        $rs = $db->execute(sprintf("SELECT `db_id` FROM `db_subusers` WHERE `pk_id`='%s' AND `subscriber_id`='%s' LIMIT 1;", (int) $item_id, $sub_id));
        if ($rs->fields['db_id']) {
            return true;
        } else {
            return false;
        }

    }
    /* logging */
    public function log_ipn_results($success)
    {
        if (!$this->ipn_log) {
            return;
        }

        $text = '[' . date('m/d/Y g:i A') . '] - ';
        $text .= ($success or $_POST['txn_type'] == 'subscr_signup') ? "SUCCESS!\n" : "FAIL: " . $this->last_error . "\n";
        $text .= "PP IPN POST Vars:\n";
        foreach ($this->ipn_data as $key => $value) {
            $text .= "$key=$value, ";
        }
        $text .= "\nSUBSCRIPTION PP IPN Server Response:\n " . $this->ipn_response;
        if (!file_exists($this->ipn_log_file)) {touch($this->ipn_log_file);}
        $fp = fopen($this->ipn_log_file, 'a');

        fwrite($fp, $text . "\n\n");
        fclose($fp);
    }
}
