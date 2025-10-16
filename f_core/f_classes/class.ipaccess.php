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

class VIPaccess
{
    /* check IP range */
    public function banIPrange_db($ip)
    {
        global $db;

        $check = 0;
        $q     = $db->execute("SELECT `ban_ip` FROM `db_banlist` WHERE `ban_active`='1';");
        if ($q->fields['ban_ip']) {
            while (!$q->EOF) {
                $check = (VIPrange::ip_in_range($ip, $q->fields['ban_ip']) == 1) ? 1 : 0;
                if ($check == 1) {return $check;}
                @$q->MoveNext();
            }
        }
        return $check;
    }
    public function banIPrange_single($ip, $range)
    {
        return $check = (VIPrange::ip_in_range($ip, $range) == 1) ? 1 : 0;
    }
    /* section access based on ip lists */
    public function sectionAccess($backend_access_url)
    {
        global $class_database, $class_filter, $cfg, $section;
        $u        = $_SERVER['REQUEST_URI'];
        $_section = (strstr($u, $backend_access_url) == true) ? 'backend' : 'frontend';

        if ($u == '' || strpos($u, VHref::getKey('publish')) !== false || strpos($u, VHref::getKey('publish_done')) !== false || strpos($u, VHref::getKey('record_done')) !== false) {
            return;
        }
        /* offline mode */
        if ($_section == 'frontend' and $cfg['website_offline_mode'] == 1 and $section != VHref::getKey("soon") and !isset($_SESSION['ADMIN_NAME'])) {
            header("Location:" . $cfg['main_url'] . '/' . VHref::getKey("soon"));
            exit;
        }
        /* check for banned IP */
        $ip       = $class_filter->clr_str(self::getUserIP());
        $ip_ban   = $class_database->singleFieldValue('db_banlist', 'ban_active', 'ban_ip', $ip);
        $ip_range = self::banIPrange_db($ip);

        if ($ip_ban == 1 or $ip_range == 1) {
            $fe_access = 0;
        } else {
            /* check IP from lists */
            if ($_SERVER['REQUEST_URI'] == '') {
                return false;
            }

            $cfg       = $class_database->getConfigurations('website_ip_based_access,list_ip_access,backend_ip_based_access,list_ip_backend');
            $fe_access = ($cfg['website_ip_based_access'] == 1 and !self::checkIPlist($cfg['list_ip_access'])) ? 0 : 1;
            $be_access = ($cfg['backend_ip_based_access'] == 1 and !self::checkIPlist($cfg['list_ip_backend'])) ? 0 : 1;
        }
        $fe_error = ($fe_access == 0 and $_section == 'frontend') ? die('<h1><b>Not Found</b></h1>The requested URL / was not found on this server.') : null;
        $be_error = ($be_access == 0 and $_section == 'backend') ? die('<h1><b>Not Found</b></h1>The requested URL / was not found on this server.') : null;
    }
    /* check for allowed email domains */
    public function emailDomainCheck($mail = '')
    {
        global $cfg;

        $file         = str_replace("\n", ',', file_get_contents($cfg['list_email_domains']));
        $file         = str_replace("\r", '', $file);
        $file         = (substr($file, -1, 1) == ',') ? substr($file, 0, -1) : $file;
        $domain_f     = $mail == '' ? trim($_POST['frontend_signup_emailadd']) : trim($mail);
        $domain       = substr(strstr($domain_f, '@'), 1);
        $domain_array = explode(',', $file);
        if (in_array($domain, $domain_array)) {
            return true;
        } else {
            return false;
        }

    }
    /* check remote ip in ip lists */
    public function checkIPlist($path)
    {
        global $class_filter, $cfg;

        $path      = $cfg['main_dir'] . '/' . $path;
        $file      = str_replace("\n", ',', file_get_contents($path));
        $file      = str_replace("\r", '', $file);
        $file      = (substr($file, -1, 1) == ',') ? substr($file, 0, -1) : $file;
        $remote_ip = $class_filter->clr_str(self::getUserIP());
        $ip_array  = explode(',', $file);
        if (!in_array($remote_ip, $ip_array)) {
            foreach ($ip_array as $ip) {
                $check = (VIPrange::ip_in_range($remote_ip, $ip) == 1) ? 1 : 0;
                if ($check == 1) {
                    break;
                } else {
                    $check = 0;
                }

            }
            if ($check == 1) {
                return true;
            } else {
                return false;
            }

        } else {
            return true;
        }

    }
    public static function getUserIP()
    {
        global $class_filter;
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER[REM_ADDR];
        $ip      = false;

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $class_filter->clr_str($ip);
    }
}
