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

class VPaypalToken
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
    public function VPaypalToken()
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
    /* add configuration */
    public function add_field($field, $value)
    {
    }
    /* submit to paypal */
    public function submit_paypal_post()
    {
    }
    /* validate IPN */
    public function validate_ipn()
    {
        define("LOG_FILE", $this->ipn_log_file);
        define("DEBUG", 0);

        $ver = false;
        $adr = $_SERVER[REM_ADDR];
        //$ppn             = array('173.0.80.0/24', '173.0.81.0/24', '173.0.82.0/24', '173.0.83.0/24');
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
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }

        }
        // read the post from PayPal system and add 'cmd'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exits = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exits == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
            $this->ipn_data[$key] = $value;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paypal_url);
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        if (DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

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
        $payment_amount   = $_POST['mc_gross'];
        $payment_currency = $_POST['mc_currency'];
        $payment_type     = $_POST['custom'];
        $item             = explode('|', urldecode($_POST['item_number']));
        $usr_id           = $item[0];
        $item_id          = $item[1];
        $item_amt         = $item[2];
        $hash             = $item[3];
        $paypal_string    = $usr_id . '|' . $item_id . '|' . $item_amt;

        if (strcmp($res, "VERIFIED") == 0) {
            if ($payment_status == 'Completed' and $payment_type == 'tk') {
                $this->ipn_response = 'VERIFIED TOKEN PAYOUT';
                $this->log_ipn_results(true);
                return true;
            } elseif ($payment_status == 'Completed' and $receiver_email == $this->paypal_mail and self::checkItem($item_id, $item_amt, $payment_amount, $payment_currency, $paypal_string, $hash)) {
                $this->ipn_response = 'VERIFIED';
                $this->log_ipn_results(true);
                return true;
            } else {
                $this->ipn_response = 'FAILED';
                $this->log_ipn_results(false);
                return false;
            }
        } else if (strcmp($res, "INVALID") == 0) {
            $this->ipn_response = 'FAILED';
            $this->log_ipn_results(false);
            return false;
        }
    }
    /* validate paid item */
    public function checkItem($item_id, $item_amt, $payment_amount, $payment_currency, $paypal_string, $hash)
    {
        global $db, $cfg, $class_filter;

        if (md5($paypal_string . $cfg['global_salt_key']) != $hash) {
            return false;
        }

        $q = $db->execute(sprintf("SELECT `tk_price`, `tk_currency`, `tk_amount` FROM `db_livetoken` WHERE `tk_id`='%s' AND `tk_amount`='%s' AND `tk_active`='1' LIMIT 1;", (int) $item_id, (int) $item_amt));

        $price = $q->fields['tk_price'];
        $unit  = $q->fields['tk_currency'];

        if ($price > 0 and $unit != '') {
            $discount = 0;
            $total    = $price;

            if (bccomp($payment_amount, $total, 2) == 0 and $payment_currency == $unit) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
    /* logging */
    public function log_ipn_results($success)
    {
        if (!$this->ipn_log) {
            return;
        }

        $text = '[' . date('m/d/Y g:i A') . '] - ';
        $text .= ($success) ? "SUCCESS!\n" : "FAIL: " . $this->last_error . "\n";
        $text .= "PP IPN POST Vars:\n";
        foreach ($this->ipn_data as $key => $value) {
            $text .= "$key=$value, ";
        }
        $text .= "\nTOKEN PP IPN Server Response:\n " . $this->ipn_response;
        if (!file_exists($this->ipn_log_file)) {touch($this->ipn_log_file);}
        $fp = fopen($this->ipn_log_file, 'a');

        fwrite($fp, $text . "\n\n");
        fclose($fp);
    }
}
