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

defined('_ISVALID') or header('Location: /error');

class VLoginRemember extends VLogin
{
    /* check if login remembered */
    public function checkLogin($section)
    {
        global $db, $class_filter, $cfg;

        $membership = ($cfg['paid_memberships'] == 1) ? include_once 'class.payment.php' : null;

        switch ($section) {
            case 'backend':$check_name  = 'ADMIN_NAME';
            case 'frontend':$check_name = 'USER_NAME';
        }

        if (!isset($_SESSION[$check_name]) and isset($_COOKIE['l'])) {
            $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sha1($_SERVER['HTTP_USER_AGENT']) : null;
            $remote_addr     = (isset($_SERVER[REM_ADDR]) and ip2long($_SERVER[REM_ADDR])) ? ip2long($_SERVER[REM_ADDR]) : null;
            $cookie_dec      = secured_decrypt($_COOKIE['l']);
            if (!$cookie_dec) {
                return false;
            }
            $cookie = json_decode($cookie_dec, true);

            if (is_array($cookie)) {
                if ($cookie[$section . "_check"] == sha1($http_user_agent . $remote_addr)) {
                    $db_user = $class_filter->clr_str($cookie[$section . "_username"]);
                    $db_user = preg_replace('/[^a-zA-Z0-9_.\-]/', '', $db_user);
                    $db_pass = $class_filter->clr_str($cookie[$section . "_password"]);

                    switch ($section) {
                        case 'backend':
                            $db_query     = sprintf("SELECT `cfg_data` FROM `db_settings` WHERE `id` IN (4,5) LIMIT 2;");
                            $session_reg1 = 'ADMIN_NAME';
                            $session_reg2 = 'ADMIN_PASS';
                            $db_result    = $db->execute($db_query);
                            if ($db_result->recordcount() > 1 and $db_info = $db_result->getrows() and $db_user == $db_info[0]['cfg_data'] and md5($db_pass) == md5($db_info[1]['cfg_data'])) {
                                $_SESSION[$session_reg1] = $db_info[0]['cfg_data'];
                                $_SESSION[$session_reg2] = $db_info[1]['cfg_data'];
                                self::setLogin($section, $db_info[0]['cfg_data'], $db_info[1]['cfg_data']);
                            }
                            break;
                        case 'frontend':
                            $db_query     = sprintf("SELECT `usr_id`, `usr_user`, `usr_password` FROM `db_accountuser` WHERE `usr_user`='%s' LIMIT 1;", $db_user);
                            $session_reg1 = 'USER_ID';
                            $session_reg2 = 'USER_NAME';
                            $db_result    = $db->execute($db_query);
                            if ($db_result->recordcount() > 0 and $db_info = $db_result->getrows() and md5($db_pass) == md5($db_info[0]['usr_password'])) {
                                $membership              = ($cfg['paid_memberships'] == 1) ? VPayment::checkSubscription(intval($db_info[0]['usr_id'])) : null;
                                $_SESSION[$session_reg1] = $db_info[0]['usr_id'];
                                $_SESSION[$session_reg2] = $db_info[0]['usr_user'];
                                $login_update            = self::updateOnLogin($db_info[0]['usr_id']);
                                $log_activity            = ($cfg['activity_logging'] == 1 and $action = new VActivity(intval($db_info[0]['usr_id']), 0)) ? $action->addTo('log_signin') : null;
                                self::setLogin($section, $db_info[0]['usr_user'], $db_info[0]['usr_password']);
                            }
                            break;
                    }
                }
            }
        }
    }
    /* set remembered login */
    public function setLogin($section, $username, $password)
    {
        $http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? sha1($_SERVER['HTTP_USER_AGENT']) : null;
        $remote_addr     = isset($_SERVER[REM_ADDR]) && ip2long($_SERVER[REM_ADDR]) ? ip2long($_SERVER[REM_ADDR]) : null;
        $cookie_array    = array('section' => $section, $section . '_username' => $username, $section . '_password' => $password, $section . '_check' => sha1($http_user_agent . $remote_addr));
        $cookie          = secured_encrypt(json_encode($cookie_array));

        setcookie('l', $cookie, SET_COOKIE_OPTIONS);
    }
    /* clear remembered login */
    public function clearLogin($section)
    {
        setcookie('l', '', DEL_COOKIE_OPTIONS);
    }
}
