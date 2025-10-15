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

class VServer
{
    private static function err()
    {
        header("Location: /error");
        exit;
    }
    public static function var_check()
    {
        $_gd = array('Smarty_', 'cache_locking', 'cache_dir', 'use_sub_dirs', 'VARCHAR', 'rlike', '(case', '( case', 'concat', 'elt(', 'elt (', 'INFORMATION_SCHEMA', '(select', '( select', 'select(', 'select (', 'union all', 'union select', 'union+all', 'union+select', 'exec(', 'exec (', 'sleep(', 'sleep (', '\x', '\u0', chr(0), 'fromCharCode', 'Set.constructor', 'script:', 'script :', '\00', 'eval(', 'eval (', 'alert(', 'alert (', 'xlink', 'toString', '<?', '#!');
        $_gp = array('Smarty_', 'cache_locking', 'cache_dir', 'use_sub_dirs', 'VARCHAR', 'rlike', '(case', '( case', 'concat', 'elt(', 'elt (', 'INFORMATION_SCHEMA', '(select', '( select', 'select(', 'select (', 'union all', 'union select', 'union+all', 'union+select', 'exec(', 'exec (', 'sleep(', 'sleep (', '\x', '\u0', chr(0), 'fromCharCode', 'Set.constructor', 'script:', 'script :', '\00', 'eval(', 'eval (', 'alert(', 'alert (', 'xlink', 'toString', '<?');

        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                foreach ($_gd as $deny) {
                    if (stripos($value, $deny) !== false) {
                        $p = json_encode($_GET);
                        error_log(date("Y-m-d H:i:s") . ": Get: $p\n", 3, REQUEST_LOG);
                        error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                        error_log(date("Y-m-d H:i:s") . ": [" . $value . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                        self::err();
                    }
                }
            }
        }

        if (!empty($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                foreach ($_gp as $deny) {
                    if (is_array($value)) {
                        foreach ($value as $thevalue) {
                            if (stripos($thevalue, $deny) !== false) {
                                $p = json_encode($_COOKIE);
                                error_log(date("Y-m-d H:i:s") . ": Cookie: $p\n", 3, REQUEST_LOG);
                                error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                                error_log(date("Y-m-d H:i:s") . ": [" . $thevalue . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                                self::err();
                            }
                        }
                    } else {
                        if (stripos($value, $deny) !== false) {
                            $p = json_encode($_COOKIE);
                            error_log(date("Y-m-d H:i:s") . ": Cookie: $p\n", 3, REQUEST_LOG);
                            error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                            error_log(date("Y-m-d H:i:s") . ": [" . $value . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                            self::err();
                        }
                    }
                }
            }
        }

        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                foreach ($_gp as $deny) {
                    if (is_array($value)) {
                        foreach ($value as $thevalue) {
                            if (is_array($thevalue)) {
                                foreach ($thevalue as $avalue) {
                                    if (stripos($avalue, $deny) !== false) {
                                        $p = json_encode($_POST);
                                        error_log(date("Y-m-d H:i:s") . ": Post: $p\n", 3, REQUEST_LOG);
                                        error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                                        error_log(date("Y-m-d H:i:s") . ": [" . $avalue . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                                        self::err();
                                    }
                                }
                            } else {
                                if (stripos($thevalue, $deny) !== false) {
                                    $p = json_encode($_POST);
                                    error_log(date("Y-m-d H:i:s") . ": Post: $p\n", 3, REQUEST_LOG);
                                    error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                                    error_log(date("Y-m-d H:i:s") . ": [" . $thevalue . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                                    self::err();
                                }
                            }
                        }
                    } else {
                        if (stripos($value, $deny) !== false) {
                            $p = json_encode($_POST);
                            error_log(date("Y-m-d H:i:s") . ": Post: $p\n", 3, REQUEST_LOG);
                            error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                            error_log(date("Y-m-d H:i:s") . ": [" . $value . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                            self::err();
                        }
                    }
                }
            }
        }

        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                $fileName = $_FILES[$key]["name"];

                if (strpos(strtolower($fileName), '.php') !== false or strpos(strtolower($fileName), '.phar') !== false or strpos(strtolower($fileName), '.pl') !== false or strpos(strtolower($fileName), '.asp') !== false or strpos(strtolower($fileName), '.htm') !== false or strpos(strtolower($fileName), '.cgi') !== false or strpos(strtolower($fileName), '.py') !== false or strpos(strtolower($fileName), '.sh') !== false or strpos(strtolower($fileName), '.cin') !== false or strpos(strtolower($fileName), '.bin') !== false) {
                    $p = json_encode($_FILES);
                    error_log(date("Y-m-d H:i:s") . ": Files: $p\n", 3, REQUEST_LOG);
                    error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n\n", 3, REQUEST_LOG);
                    self::err();
                }
                foreach ($_gp as $deny) {
                    if (is_array($value)) {
                        foreach ($value as $thevalue) {
                            if (stripos($thevalue, $deny) !== false) {
                                $p = json_encode($_FILES);
                                error_log(date("Y-m-d H:i:s") . ": Files: $p\n", 3, REQUEST_LOG);
                                error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                                error_log(date("Y-m-d H:i:s") . ": [" . $thevalue . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                                self::err();
                            }
                        }
                    } else {
                        if (stripos($_FILES[$key]['name'], $deny) !== false) {
                            $p = json_encode($_FILES);
                            error_log(date("Y-m-d H:i:s") . ": Files: $p\n", 3, REQUEST_LOG);
                            error_log(date("Y-m-d H:i:s") . ": Req: " . self::get_ip() . ": " . $_SERVER["REQUEST_URI"] . "\n", 3, REQUEST_LOG);
                            error_log(date("Y-m-d H:i:s") . ": [" . $_FILES[$key]['name'] . "]:[" . $deny . "]\n\n", 3, REQUEST_LOG);
                            self::err();
                        }
                    }
                }
            }
        }
    }

    public static function cookie_validation_set()
    {
        if (COOKIE_VALIDATION) {
            $set_cookie_options = SET_COOKIE_OPTIONS;
            $del_cookie_options = DEL_COOKIE_OPTIONS;

            $_data = trim(substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', mt_rand(1, 10))), 1, 3));
            if (!isset($_SESSION["cname"])) {$_SESSION["cname"] = strrev($_data);}
            if (!isset($_COOKIE[$_SESSION["cname"]])) {
                setcookie($_SESSION["cname"], urlencode(secured_encrypt($_data)), $set_cookie_options);
            }
        }
    }

    public static function cookie_validation_check()
    {
        $allowed_ips = COOKIE_VALIDATION_WHITELIST;

        if (COOKIE_VALIDATION and php_sapi_name() !== 'cli' and $_SERVER["REQUEST_METHOD"] !== "OPTIONS" and !in_array(self::get_ip(), $allowed_ips)) {
            if (!isset($_SESSION["rcount"])) {$_SESSION["rcount"] = 0;}

            if (!isset($_SESSION["cname"]) and !isset($_COOKIE[$_SESSION["cname"]])) {
                error_log(date("Y-m-d H:i:s") . ': ' . self::get_ip() . ': no-session-no-cookie' . "\n", 3, COOKIE_LOG);
                die();
            } elseif (isset($_SESSION["cname"]) and !isset($_COOKIE[$_SESSION["cname"]])) {
                if ($_SESSION["rcount"] <= 1 and empty($_GET)) {
                    $_SESSION["rcount"] += 1;
                    header("Refresh:0");
                    exit;
                } elseif ($_SESSION["rcount"] >= 3 and empty($_GET)) {
                    error_log(date("Y-m-d H:i:s") . ': ' . self::get_ip() . ': multi-reload => ' . $_SESSION["rcount"] . "\n", 3, COOKIE_LOG);
                    die();
                }
            } elseif (isset($_COOKIE[$_SESSION["cname"]])) {
                $cname = trim(secured_decrypt(urldecode($_COOKIE[$_SESSION["cname"]])));
                if ($cname == '') {
                    error_log(date("Y-m-d H:i:s") . ': ' . self::get_ip() . ': cookie-decrypt ' . "\n", 3, COOKIE_LOG);
                    die();
                }
            }
        }
    }

    /* curl get */
    public static function curl_tt($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    /* get free server */
    public static function getFreeServer($type = 'stream')
    {
        global $db;

        switch ($type) {
            case "bcast":
                $t   = 'lbb';
                $tt  = 'cast';
                $c   = 'freebcastserver';
                $adr = 'rtmp://##HOST##/live';
                break;

            case "stream":
                $t   = 'lbs';
                $tt  = 'stream';
                $c   = 'freeserver';
                $adr = '##PROTOCOL##://##HOST##:##PORT##';
                break;

            case "chat":
                $t   = 'lbc';
                $tt  = 'chat';
                $c   = 'freeserver';
                $adr = '##PROTOCOL##://##HOST##:##PORT##';
                break;
        }

        $sql = sprintf("SELECT `srv_id`, `srv_host`, `srv_port`, `srv_https` FROM `db_liveservers` WHERE `srv_type`='%s' AND `srv_active`='1' ORDER BY RAND() LIMIT 1;", $t);
        $rs  = $db->execute($sql);

        if ($rs->fields["srv_id"]) {
//load balancer found, get most free server
            $srv_host  = $rs->fields["srv_host"];
            $srv_port  = $rs->fields["srv_port"];
            $srv_https = $rs->fields["srv_https"];

            $lb_srv = sprintf("%s://%s:%s/%s", ($srv_https ? 'https' : 'http'), $srv_host, $srv_port, $c);
            $vs     = json_decode(VServer::curl_tt($lb_srv));

            if (isset($vs->ip)) {
                $server = str_replace(array('##PROTOCOL##', '##HOST##', '##PORT##'), array($vs->protocol, $vs->ip, $vs->port), $adr);
            }
        } else {
//without load balancer, get a random server
            $sql = sprintf("SELECT `srv_id`, `srv_host`, `srv_port`, `srv_https` FROM `db_liveservers` WHERE `srv_type`='%s' AND `srv_active`='1' ORDER BY RAND() LIMIT 1;", $tt);
            $rs  = $db->execute($sql);

            if ($rs->fields["srv_id"]) {
                $srv_host  = $rs->fields["srv_host"];
                $srv_port  = $rs->fields["srv_port"];
                $srv_https = $rs->fields["srv_https"] == 1 ? 'https' : 'http';

                if ((int) $srv_port == 443) {
                    $server = str_replace(array('##PROTOCOL##', '##HOST##', ':##PORT##'), array($srv_https, $srv_host, ''), $adr);
                } else {
                    $server = str_replace(array('##PROTOCOL##', '##HOST##', '##PORT##'), array($srv_https, $srv_host, $srv_port), $adr);
                }
            } else {
                $server = $adr;
            }
        }

        return $server;
    }
    /* memory */
    public function get_memory()
    {
        foreach (file('/proc/meminfo') as $ri) {
            $m[strtok($ri, ':')] = strtok('');
        }

        $i = '<div class="left-float">Memory usage: </div>';
        $i .= '<div class="left-float left-padding10">';
        $i .= 'MemTotal: ' . $m['MemTotal'] . "<br />";
        $i .= 'MemFree: ' . $m['MemFree'] . "<br />";
        $i .= 'Buffers: ' . $m['Buffers'] . "<br />";
        $i .= 'Cached: ' . $m['Cached'];
        $i .= '</div>';

        return $i;
    }
    /* remote IP */
    public static function get_remote_ip()
    {
        return self::get_ip();
    }
    /* remote IP */
    public static function get_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER[REM_ADDR];
        }

        return preg_replace('/[^a-zA-Z0-9:.\-]/', '', $ip);
    }
    public static function get_ip_old()
    {
        global $class_filter;
        return $class_filter->clr_str($_SERVER['SERVER_ADDR']);
    }
    /* log file */
    public static function logToFile($path, $str)
    {
        global $cfg;

        $wm = 'w';
        switch ($path) {
            case ".mailer.log":
                $ddir = 'log_mail/' . date("Y.m.d") . '/';
                break;
            default:
                $ddir = null;
                break;
        }
        $full_path = $cfg["logging_dir"] . '/' . $ddir . $path;
        $file_dir  = dirname($full_path);

        if (!file_exists($full_path)) {
            @touch($full_path);
        }

        if ($ddir != '' and !is_dir($cfg["logging_dir"] . '/' . $ddir)) {
            @mkdir($cfg["logging_dir"] . '/' . $ddir);
        }

        if (!is_dir($file_dir) or !is_writable($file_dir)) {
            return false;
        }

        if (is_file($full_path) && is_writable($full_path)) {
            $wm = 'a';
        }

        if (!$handle = fopen($full_path, $wm)) {
            return false;
        }

        if (fwrite($handle, $str . "\n") === false) {
            return false;
        }

        @fclose($handle);
    }
    /* background process */
    public static function bgProcess($cmd)
    {
        exec($cmd . '>/dev/null &');
    }
}
