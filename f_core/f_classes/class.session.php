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

class VSession
{
    /* initialize sessions */
    public static function init()
    {
        global $cfg, $class_language, $class_filter, $section, $href, $class_database;

        $m   = 'files';
        $sec = $cfg['session_lifetime'] * 60;

        ini_set('display_errors', 0);
        if ($m == 'memcached') {
            new VmemcacheSessionHandler();

            ini_set('session.save_handler', 'memcached');
            ini_set('session.save_path', 'localhost:11211');
            ini_set('session.use_only_cookies', 1);
        } else {
            ini_set('session.save_handler', 'files');
            ini_set('session.save_path', $cfg['main_dir'] . '/f_data/data_sessions');
            ini_set('session.use_only_cookies', 0);
        }
        ini_set('session.name', $cfg['session_name']);
        ini_set('session.use_cookies', 1);
        ini_set('session.gc_maxlifetime', $sec);

        if (ini_get('date.timezone') == '') {
            ini_set('date.timezone', $cfg['date_timezone']);
            date_default_timezone_set($cfg['date_timezone']);
        }

        VSession::_start();
        VSession::_sessionInit();
    }
    private static function _start()
    {
        session_start();
    }
    private static function _sessionInit()
    {
        global $cfg, $class_language, $class_filter, $section, $href, $db;

        require 'f_core/config.backend.php';
        require 'f_core/config.language.php';

        $sec = $cfg['session_lifetime'] * 60;

        $_section = (strstr($_SERVER['REQUEST_URI'], $backend_access_url) == true) ? 'backend' : 'frontend';
        /* session language check */
        $_lang  = langTypes();
        $_count = count($_lang);

        foreach ($_lang as $lk => $lv) {
            if ($lv['lang_default'] == 1) {
                $_f  = $lk;
                $_fl = $lv['lang_flag'];
            }
        }
        if ($_section == 'frontend' and isset($_SESSION['USER_ID']) and empty($_POST) and empty($_GET)) {
            $nnr                           = VHome::countNewNotifications();
            $_SESSION['new_notifications'] = $nnr >= 10 ? '9+' : $nnr;
        }
        if (!isset($_SESSION['lang_count']) or (isset($_SESSION['lang_count']) and (int) $_SESSION['lang_count'] != $_count)) {
            $_SESSION['lang_count'] = $_count;
        }
        if (($_section == 'frontend' and $_SESSION['fe_lang'] == '') or ($_section == 'backend' and $_SESSION['be_lang'] == '')) {
            //language sessions
            $_SESSION['fe_lang'] = $_f;
            $_SESSION['fe_flag'] = $_fl;
            $_SESSION['be_lang'] = $_f;
            $_SESSION['be_flag'] = $_fl;
        }
        if ($section == $href['search']) {
            //search filter sessions
            $q  = isset($_GET['q']) ? $class_filter->clr_str($_GET['q']) : null;
            $tf = isset($_GET['tf']) ? (int) $_GET['tf'] : 0;
            $uf = isset($_GET['uf']) ? (int) $_GET['uf'] : 0;
            $df = isset($_GET['df']) ? (int) $_GET['df'] : 0;
            $ff = isset($_GET['ff']) ? (int) $_GET['ff'] : 0;

            $_SESSION['q']  = $q;
            $_SESSION['tf'] = $tf;
            $_SESSION['uf'] = $uf;
            $_SESSION['df'] = $df;
            $_SESSION['ff'] = $ff;
        }
        if ($_section == 'frontend' and !isset($_SESSION['sbm'])) {
            $_SESSION['sbm'] = 1;
        }
        if (isset($_SESSION['USER_ID']) and (!isset($_SESSION['USER_NAME']) or !isset($_SESSION['USER_KEY']))) {
            $q                       = $db->execute(sprintf("SELECT A.`usr_id`, A.`usr_key`, A.`usr_user`, A.`usr_partner`, A.`affiliate_badge`, A.`usr_affiliate`, A.`usr_password`, A.`usr_dname`, A.`usr_theme` FROM `db_accountuser` A WHERE A.`usr_id`='%s' AND A.`usr_status`='1' LIMIT 1;", (int) $_SESSION['USER_ID']));
            $session_reg2            = 'USER_NAME';
            $session_val2            = $q->fields['usr_user'];
            $session_reg3            = 'USER_KEY';
            $session_val3            = $q->fields['usr_key'];
            $session_reg4            = $session_val3 . '_list';
            $session_val4            = 0;
            $session_reg5            = 'USER_DNAME';
            $session_val5            = $q->fields['usr_dname'];
            $session_reg6            = 'USER_AFFILIATE';
            $session_val6            = $q->fields['usr_affiliate'];
            $session_reg7            = 'USER_BADGE';
            $session_val7            = $q->fields['affiliate_badge'];
            $session_reg8            = 'USER_PARTNER';
            $session_val8            = $q->fields['usr_partner'];
            $session_reg9            = 'USER_THEME';
            $session_val9            = $q->fields['usr_theme'];
            $_SESSION[$session_reg2] = $session_val2;
            $_SESSION[$session_reg3] = $session_val3;
            $_SESSION[$session_reg4] = $session_val4;
            $_SESSION[$session_reg5] = $session_val5;
            $_SESSION[$session_reg6] = $session_val6;
            $_SESSION[$session_reg8] = $session_val8;
            $_SESSION[$session_reg9] = $session_val9;
            if ($session_val6 == 1 || $session_val8 == 1) {
                $_SESSION[$session_reg7] = $session_val7;
            }
        }
    }
    public static function isLoggedIn()
    {
        return ((int) $_SESSION['USER_ID'] > 0);
    }
}
