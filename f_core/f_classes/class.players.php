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

class VPlayers
{
    /* embedded video players */
    public static function playerEmbedCodes($src, $info, $width, $height, $m = '', $be = false)
    {
        global $cfg, $href;

        switch ($src) {
            case "dailymotion-old":
                $ec = '<iframe width="' . $width . '" height="' . $height . '" src="https://www.dailymotion.com/embed/video/' . $info["key"] . '?autoplay=' . ($be == '' ? 0 : 1) . '" frameborder="0" allowFullScreen></iframe>';
                break;

            case "youtube-old":
                $ec = '<iframe class="youtube-player" type="text/html" width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $info["key"] . '?wmode=opaque&autoplay=' . ($be == '' ? 0 : 1) . '" frameborder="0" allowfullscreen></iframe>';
                break;

            case "vimeo-old":
                $ec = '<iframe width="' . $width . '" height="' . $height . '" src="https://player.vimeo.com/video/' . $info["key"] . '?autoplay=' . ($be == '' ? 0 : 1) . '" frameborder="0" allowFullScreen></iframe>';
                $ec = $m != '' ? '<iframe width="' . $width . '" height="' . $height . '" src="https://vimeo.com/m/' . $info["key"] . '" frameborder="0" allowFullScreen></iframe>' : $ec;
                break;

            default:
                $ec = null;

                if ($src != "youtube" and isset($info["url"]) and $info["url"] != '') {
                    $ec = '<script type="text/javascript">$.post("' . ($be ? $cfg["main_url"] . '/' . VHref::getKey("watch") : null) . '",{\'ec\':\'' . secured_encrypt($info["url"]) . '\'},function(data){a = JSON.parse(data);if(typeof a.e!="undefined"){$(a.e).prependTo("#siteContent")}else if(typeof a.c!="undefined"){$("#view-player").html(a.c);$(".video_player_holder.joshwhotv #view-player").css("padding-bottom",0);$("#file-share-embed").val(a.c)}});</script>';
                } elseif ($src == "youtube" and isset($info["url"]) and $info["url"] != '') {
                    $ec = '<iframe class="youtube-player" type="text/html" width="' . $width . '" height="' . $height . '" src="https://www.youtube.com/embed/' . $info["key"] . '?autoplay=' . ($be == '' ? ($info["cfg"]["vjs_autostart"]) : 1) . '" frameborder="0" allowfullscreen></iframe>';
                }
                break;
        }
        return $ec;
    }
    /* init */
    public function playerInit($section)
    {
        global $cfg, $class_filter;

        switch ($section) {
            case "backend": //backend file manager
                $_id     = 'view-player';
                $_width  = '100%';
                $_height = (isset($_GET["a"]) ? '100%' : ($cfg["video_player"] == 'flow' ? '99%' : '100%'));
                break;
            case "view": //view files page
                $_id     = 'view-player';
                $_width  = '100%';
                $_height = isset($_GET["i"]) ? '530px' : (isset($_GET["a"]) ? '560px' : ($cfg["video_player"] == 'flow' ? '560px' : '560px'));
                $_height = '100%';
                break;
            case "embed": //embed files
                $t       = isset($_GET["a"]) ? 'audio' : (isset($_GET["l"]) ? 'live' : (isset($_GET["s"]) ? 'short' : 'video'));
                $_id     = 'view-player-' . $class_filter->clr_str($_GET[$t[0]]);
                $_width  = '100%';
                $_height = (($t == 'audio' and !isset($_GET["p"])) ? '100%' : '100%');
                break;
            case "channel": //personal channel page
            case "channel_audio": //personal channel page
                $_id     = 'player-loader';
                $_width  = 640;
                $_height = ((isset($_GET["a"]) and $_GET["do"] == 'load-audio') ? 445 : 445);
                break;
            case "edit": //editing files page
                $_id     = 'player-edit';
                $_width  = 500;
                $_height = (isset($_GET["a"]) ? 320 : 320);
                break;
            case "main": //homepage player
                $_id     = $cfg["video_player"] . '-player-home';
                $_width  = 352;
                $_height = 226;
                break;
        }
        return array($_id, $_width, $_height);
    }

    /* subtitles */
    public function FPsubtitle($sub_file)
    {
        global $cfg;

        $sub_dir = $cfg["main_dir"] . '/f_data/data_subtitles/';

        $sub_files = array_values(array_diff(scandir($sub_dir), array('..', '.', '.htaccess')));

        if ($sub_files[0]) {
            foreach ($sub_files as $k => $sub) {
                if ($sub_file == md5($sub)) {
                    return $sub;
                }
            }
        }
        return;
    }
    /* file urls */
    public function getFileUrl($type, $file_key, $usr_key)
    {
        global $cfg, $class_database;

        $cfg[] = $class_database->getConfigurations('stream_method,stream_server,stream_lighttpd_key,stream_lighttpd_prefix,stream_lighttpd_url,stream_lighttpd_secure,stream_rtmp_location');
        switch ($type) {
            case "l":$tbl = 'live';
                $file1        = null;
                $file2        = null;
                break;

            case "v":
            case "s":
                $tbl = $type == 's' ? 'short' : 'video';
                switch ($cfg["stream_method"]) {
                    case "":
                    case "0":
                    case "1":
                        $flv_url = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.flv';
                        $mp4_url = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mp4';
                        break;
                    case "2":
                        $flv_url = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.flv';
                        $mp4_url = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mp4';
                        break;
                    case "3":
                        $flv_url = $cfg["stream_rtmp_location"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.flv';
                        $mp4_url = $cfg["stream_rtmp_location"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mp4';
                        break;
                }
                $file1 = $flv_url;
                $file2 = $mp4_url;
                break;
            case "a":
                $tbl   = 'audio';
                $file1 = $cfg["media_files_url"] . '/' . $usr_key . '/a/' . $file_key . '.mp3';
                $file2 = null;
                break;
        }

        return array($file1, $file2, $tbl);
    }

    public function fileSources($type, $usr_key, $file_key, $srv = '', $is_short = false)
    {
        global $db, $class_database, $cfg;

        $f     = array();
        $cfg[] = $class_database->getConfigurations('stream_server,stream_method,stream_lighttpd_secure,stream_lighttpd_url,conversion_video_previews,conversion_audio_previews,conversion_live_previews');

        $cc  = $db->execute(sprintf("SELECT `old_file_key`, `has_preview`, `file_name` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $file_key));
        $old = $cc->fields["old_file_key"];
        $hpv = $cc->fields["has_preview"];

        $previews = (($type == 'live' and $cfg["conversion_live_previews"] == 1 and $hpv == 1) or ($type == 'video' and $cfg["conversion_video_previews"] == 1 and $hpv == 1) or ($type == 'audio' and $cfg["conversion_audio_previews"] == 1 and $hpv == 1)) ? true : false;
        if ($previews and isset($_SESSION["USER_ID"]) and (int) $_SESSION["USER_ID"] > 0) {
            $vuid = $class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $file_key);
            if ($vuid > 0) {
                if ($vuid == (int) $_SESSION["USER_ID"]) {
                    $previews = false;
                } else {
                    $ss = $db->execute(sprintf("SELECT `db_id`, `sub_list` FROM `db_subscriptions` WHERE `usr_id`='%s' LIMIT 1;", (int) $_SESSION["USER_ID"]));
                    if ($ss->fields["db_id"]) {
                        $subs = unserialize($ss->fields["sub_list"]);
                        if (in_array($vuid, $subs)) {
                            $sb = $db->execute(sprintf("SELECT `db_id` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' LIMIT 1;", (int) $_SESSION["USER_ID"], $vuid, date("Y-m-d H:i:s")));
                            if ($sb->fields["db_id"] > 0) {
                                $previews = false;
                            } else {
                                $previews = true;
                            }
                        }
                    }

                    if ($previews) {
                        $ts = $db->execute(sprintf("SELECT `db_id` FROM `db_subtemps` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' AND `active`='1' LIMIT 1;", (int) $_SESSION["USER_ID"], $vuid, date("Y-m-d H:i:s")));

                        if ($ts->fields["db_id"]) {
                            $previews = false;
                        }

                    }
                }
            }
        } elseif (isset($_GET["section"]) and $_GET["section"] == 'backend' and isset($_GET["pv"])) {
            $previews = (bool) $_GET["pv"];
        }
        $previews = !$previews ? ($old == 1 ? true : false) : $previews;

        $rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        $url = VGenerate::fileURL($type, $file_key, 'upload');
        $gs  = !$previews ? md5($cfg["global_salt_key"] . $file_key) : $file_key;

        $f["360p"] = array(
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.' . (!$is_short ? '360p' : 'short') . '.mp4',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.360p.webm',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.360p.ogv');
        $f["480p"] = array(
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.480p.mp4',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.480p.webm',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.480p.ogv');
        $f["720p"] = array(
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.720p.mp4',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.720p.webm',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.720p.ogv');
        $f["1080p"] = array(
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.1080p.mp4',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.1080p.webm',
            $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.1080p.ogv');

        if ($cfg["conversion_mp4_360p_active"] == 0) {unset($f["360p"][0]);}
        if ($cfg["conversion_mp4_480p_active"] == 0) {unset($f["480p"][0]);}
        if ($cfg["conversion_mp4_720p_active"] == 0) {unset($f["720p"][0]);}
        if ($cfg["conversion_mp4_1080p_active"] == 0) {unset($f["1080p"][0]);}
        if ($cfg["conversion_vpx_360p_active"] == 0) {unset($f["360p"][1]);}
        if ($cfg["conversion_vpx_480p_active"] == 0) {unset($f["480p"][1]);}
        if ($cfg["conversion_vpx_720p_active"] == 0) {unset($f["720p"][1]);}
        if ($cfg["conversion_vpx_1080p_active"] == 0) {unset($f["1080p"][1]);}
        if ($cfg["conversion_ogv_360p_active"] == 0) {unset($f["360p"][2]);}
        if ($cfg["conversion_ogv_480p_active"] == 0) {unset($f["480p"][2]);}
        if ($cfg["conversion_ogv_720p_active"] == 0) {unset($f["720p"][2]);}
        if ($cfg["conversion_ogv_1080p_active"] == 0) {unset($f["1080p"][2]);}

        if ($type == 'audio') {
            $f = array();

            $f["360p"] = array(
                $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.mp4',
                $url . '/' . $usr_key . '/' . $type[0] . '/' . $gs . '.mp3',
            );
        }

        return $f;
    }
    public function channelSources($type, $usr_key, $file_key)
    {
        global $db, $class_database, $cfg;

        $cfg[] = $class_database->getConfigurations('stream_server,stream_lighttpd_secure,stream_lighttpd_url');
        $src   = array();
        $f     = self::fileSources($type, $usr_key, $file_key);
        $rs    = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");

        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        if (($cfg["conversion_mp4_360p_active"] == 0 and $cfg["conversion_mp4_480p_active"] == 0 and $cfg["conversion_mp4_720p_active"] == 0 and $cfg["conversion_mp4_1080p_active"] == 0) and ($cfg["conversion_vpx_360p_active"] == 0 and $cfg["conversion_vpx_480p_active"] == 0 and $cfg["conversion_vpx_720p_active"] == 0 and $cfg["conversion_vpx_1080p_active"] == 0) and ($cfg["conversion_ogv_360p_active"] == 0 and $cfg["conversion_ogv_480p_active"] == 0 and $cfg["conversion_ogv_720p_active"] == 0 and $cfg["conversion_ogv_1080p_active"] == 0) and ($cfg["conversion_flv_360p_active"] == 1 or $cfg["conversion_flv_480p_active"] == 1)) {
            return self::getFLVsrc($type, $usr_key, $file_key, 1);
        } else {
            $url = VGenerate::fileURL($type, $file_key, 'upload');
            foreach ($f as $k => $v) {
                if ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 1) {
                    $l0   = explode("/", $v[0]);
                    $loc0 = $cfg["media_files_dir"] . '/' . $l0[6] . '/' . $l0[7] . '/' . $l0[8];
                    $l1   = explode("/", $v[1]);
                    $loc1 = $cfg["media_files_dir"] . '/' . $l1[6] . '/' . $l1[7] . '/' . $l1[8];
                    $l2   = explode("/", $v[2]);
                    $loc2 = $cfg["media_files_dir"] . '/' . $l2[6] . '/' . $l2[7] . '/' . $l2[8];
                    $l3   = explode("/", $v[3]);
                    $loc3 = $cfg["media_files_dir"] . '/' . $l3[6] . '/' . $l3[7] . '/' . $l3[8];
                    $l4   = explode("/", $v[4]);
                    $loc4 = $cfg["media_files_dir"] . '/' . $l4[6] . '/' . $l4[7] . '/' . $l4[8];
                } elseif ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 0) {
                    $loc0 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[2]);
                    $loc3 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[3]);
                    $loc4 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[4]);
                } else {
                    $loc0 = str_replace($url, $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($url, $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($url, $cfg["media_files_dir"], $v[2]);
                    $loc3 = str_replace($url, $cfg["media_files_dir"], $v[3]);
                    $loc4 = str_replace($url, $cfg["media_files_dir"], $v[4]);
                }

                if (is_file($loc0)) {
                    $src[] = $k . '.' . substr($v[0], -3);
                }
                if (is_file($loc1)) {
                    $src[] = $k . '.' . substr($v[1], -4);
                }
                if (is_file($loc2)) {
                    $src[] = $k . '.' . substr($v[2], -3);
                }
                if (is_file($loc3)) {
                    $src[] = $k . '.' . substr($v[3], -3);
                }
                if (is_file($loc4)) {
                    $src[] = $k . '.' . substr($v[4], -3);
                }
            }
        }
        return implode(',', $src);
    }
    public function getFLVsrc($type, $usr_key, $file_key, $ch = '', $fp = '')
    {
        global $db, $class_database, $cfg;
        $f   = array();
        $src = array();
        $f1  = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.360p.flv';
        $f2  = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.480p.flv';
        $f3  = $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.flv';

        $loc1 = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $f1);
        $loc2 = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $f2);
        $loc3 = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $f3);

        $f["360p"] = array(
            $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.360p.flv',
            $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.flv');

        $f["480p"] = array(
            $cfg["media_files_url"] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.480p.flv');

        foreach ($f as $k => $v) {
            $loc0 = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $v[0]);
            $loc1 = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $v[1]);
            if ($ch == '') {
                if ($fp == '') {
                    $src[] = '{' . (is_file($loc0) ? 'file: "' . $v[0] . '",' : null) . (is_file($loc1) ? 'file: "' . $v[1] . '",' : null) . ' label: "' . $k . '", mediaid: "' . $file_key . '" }';
                } else {
                    if (is_file($loc0)) {
                        $src[] = '{ flv: "' . $v[0] . '" }';
                    }
                }
            } else {
                if (is_file($loc0)) {
                    $src[] = $k . '.' . substr($v[0], -3);
                }
                if (is_file($loc1)) {
                    $src[] = $k . '.' . substr($v[1], -3);
                }
            }
        }
        return implode(',', $src);
    }
    public function buildFileSources($type, $file_key, $usr_key, $section, $fp = '')
    {
        require_once 'class.be.servers.php';
        global $db, $class_database, $cfg;

        $src = array();

        $rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        $sql = sprintf("SELECT
                                                A.`server_type`, A.`lighttpd_url`, A.`lighttpd_secdownload`, A.`lighttpd_prefix`, A.`lighttpd_key`, A.`cf_enabled`, A.`cf_dist_type`,
                                                A.`cf_signed_url`, A.`cf_signed_expire`, A.`cf_key_pair`, A.`cf_key_file`,
                                                B.`upload_server`
                                                FROM
                                                `db_servers` A, `db_%sfiles` B
                                                WHERE
                                                B.`file_key`='%s' AND
                                                B.`upload_server` > '0' AND
                                                A.`server_id`=B.`upload_server` LIMIT 1;", $type, $file_key);
        $srv              = $db->execute($sql);
        $cf_signed_url    = $srv->fields["cf_signed_url"];
        $cf_signed_expire = $srv->fields["cf_signed_expire"];
        $cf_key_pair      = $srv->fields["cf_key_pair"];
        $cf_key_file      = $srv->fields["cf_key_file"];

        if (($cfg["conversion_mp4_360p_active"] == 0 and $cfg["conversion_mp4_480p_active"] == 0 and $cfg["conversion_mp4_720p_active"] == 0 and $cfg["conversion_mp4_1080p_active"] == 0) and ($cfg["conversion_vpx_360p_active"] == 0 and $cfg["conversion_vpx_480p_active"] == 0 and $cfg["conversion_vpx_720p_active"] == 0 and $cfg["conversion_vpx_1080p_active"] == 0) and ($cfg["conversion_ogv_360p_active"] == 0 and $cfg["conversion_ogv_480p_active"] == 0 and $cfg["conversion_ogv_720p_active"] == 0 and $cfg["conversion_ogv_1080p_active"] == 0) and ($cfg["conversion_flv_360p_active"] == 1 or $cfg["conversion_flv_480p_active"] == 1)) {
            return self::getFLVsrc($type, $usr_key, $file_key, '', $fp);
        } else {
            $f   = self::fileSources($type, $usr_key, $file_key, $srv);
            $url = VGenerate::fileURL($type, $file_key, 'upload');

            foreach ($f as $k => $v) {
                if (($srv->fields["lighttpd_url"] != '' and $srv->fields["lighttpd_secdownload"] == 1) or ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 1)) {
                    $l0   = explode("/", $v[0]);
                    $loc0 = $cfg["media_files_dir"] . '/' . $l0[6] . '/' . $l0[7] . '/' . $l0[8];
                    $l1   = explode("/", $v[1]);
                    $loc1 = $cfg["media_files_dir"] . '/' . $l1[6] . '/' . $l1[7] . '/' . $l1[8];
                    $l2   = explode("/", $v[2]);
                    $loc2 = $cfg["media_files_dir"] . '/' . $l2[6] . '/' . $l2[7] . '/' . $l2[8];
                    $l3   = explode("/", $v[3]);
                    $loc3 = $cfg["media_files_dir"] . '/' . $l3[6] . '/' . $l3[7] . '/' . $l3[8];
                    $l4   = explode("/", $v[4]);
                    $loc4 = $cfg["media_files_dir"] . '/' . $l4[6] . '/' . $l4[7] . '/' . $l4[8];
                } elseif ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 0) {
                    $loc0 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[2]);
                    $loc3 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[3]);
                    $loc4 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[4]);
                } else {
                    $loc0 = str_replace($url, $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($url, $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($url, $cfg["media_files_dir"], $v[2]);
                    $loc3 = str_replace($url, $cfg["media_files_dir"], $v[3]);
                    $loc4 = str_replace($url, $cfg["media_files_dir"], $v[4]);

                    if (($srv->fields["server_type"] == 's3' or $srv->fields["server_type"] == 'ws') and $srv->fields["cf_enabled"] == 1 and $cf_signed_url == 1) {
                        if ($srv->fields["cf_dist_type"] == 'r' and $fp != '') {
                            $v[0] = strstr($v[0], $usr_key);
                            $v[1] = strstr($v[1], $usr_key);
                            $v[2] = strstr($v[2], $usr_key);
                            $v[3] = strstr($v[3], $usr_key);
                            $v[4] = strstr($v[4], $usr_key);
                        }

                        if ($type == 'audio') {
                            $rtmp = $srv->fields["cf_enabled"] == 1 ? $class_database->singleFieldValue('db_servers', 'cf_dist_domain', 'server_id', $srv->fields["upload_server"]) : $class_database->singleFieldValue('db_servers', 's3_bucketname', 'server_id', $srv->fields["upload_server"]);

                            $v[0] = strstr($v[0], $usr_key);
                        }

                        $v[0] = VbeServers::getSignedURL($v[0], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                        $v[1] = VbeServers::getSignedURL($v[1], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                        $v[2] = VbeServers::getSignedURL($v[2], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                        $v[3] = VbeServers::getSignedURL($v[3], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                        $v[4] = VbeServers::getSignedURL($v[4], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                    }
                }
                if ($fp == '') {
                    if (($srv->fields["server_type"] == 's3' or $srv->fields["server_type"] == 'ws') and $srv->fields["cf_dist_type"] == 'r' and $type == 'video') {
                        if ($k == '360p') {
                            $rtmp     = $srv->fields["cf_enabled"] == 1 ? $class_database->singleFieldValue('db_servers', 'cf_dist_domain', 'server_id', $srv->fields["upload_server"]) : $class_database->singleFieldValue('db_servers', 's3_bucketname', 'server_id', $srv->fields["upload_server"]);
                            $smilname = $cfg["media_files_dir"] . '/' . $usr_key . '/v/' . $file_key . '.smil';

                            $v360p     = $usr_key . '/v/' . $file_key . '.360p.mp4';
                            $v360ploc  = $cfg["media_files_dir"] . '/' . $usr_key . '/v/' . $file_key . '.360p.mp4';
                            $v480p     = $usr_key . '/v/' . $file_key . '.480p.mp4';
                            $v480ploc  = $cfg["media_files_dir"] . '/' . $usr_key . '/v/' . $file_key . '.480p.mp4';
                            $v720p     = $usr_key . '/v/' . $file_key . '.720p.mp4';
                            $v720ploc  = $cfg["media_files_dir"] . '/' . $usr_key . '/v/' . $file_key . '.720p.mp4';
                            $v1080p    = $usr_key . '/v/' . $file_key . '.1080p.mp4';
                            $v1080ploc = $cfg["media_files_dir"] . '/' . $usr_key . '/v/' . $file_key . '.1080p.mp4';

                            if ($cf_signed_url == 1 and $srv->fields["cf_enabled"] == 1) {
                                $v360p  = VbeServers::getSignedURL($v360p, $cf_signed_expire, $cf_key_pair, $cf_key_file);
                                $v480p  = VbeServers::getSignedURL($v480p, $cf_signed_expire, $cf_key_pair, $cf_key_file);
                                $v720p  = VbeServers::getSignedURL($v720p, $cf_signed_expire, $cf_key_pair, $cf_key_file);
                                $v1080p = VbeServers::getSignedURL($v1080p, $cf_signed_expire, $cf_key_pair, $cf_key_file);

                                $smil = '
<smil>
    <head>
        <meta base="rtmp://' . $rtmp . '/cfx/st/mp4/" />
    </head>
    <body>
        <switch>
            ' . (is_file($v360ploc) ? '<video src="' . $v360p . '" height="360" system-bitrate="300000" width="640" />' : null) . '
            ' . (is_file($v480ploc) ? '<video src="' . $v480p . '" height="480" system-bitrate="900000" width="852" />' : null) . '
            ' . (is_file($v720ploc) ? '<video src="' . $v720p . '" height="720" system-bitrate="5000000" width="1280" />' : null) . '
            ' . (is_file($v1080ploc) ? '<video src="' . $v1080p . '" height="1080" system-bitrate="7500000" width="1920" />' : null) . '
        </switch>
    </body>
</smil>
';

                                if (!is_file($smilname)) {
                                    touch($smilname);
                                } //else {
                                if (!$handle = fopen($smilname, 'w')) {
                                    exit;
                                }
                                if (fwrite($handle, $smil) === false) {
                                    exit;
                                }
                                fclose($handle);
                            }

                            $src[] = '{ file: "' . str_replace($cfg["media_files_dir"], $cfg["media_files_url"], $smilname) . '" }';
                        }
                    } else {
                        if ($type == 'audio' and ($srv->fields["server_type"] == 's3' or $srv->fields["server_type"] == 'ws') and $srv->fields["cf_dist_type"] == 'r' and $srv->fields["cf_enabled"] == 1) {
                            $v[0]  = 'rtmp://' . $rtmp . '/cfx/st/mp4:' . $v[0];
                            $src[] = '{' . (is_file($loc0) ? 'file: "' . $v[0] . '"' : null) . '}';
                        } else {
                            $src[] = '{' . (is_file($loc0) ? 'file: "' . $v[0] . '",' : (is_file($loc3) ? 'file: "' . $v[3] . '",' : null)) . (is_file($loc1) ? 'file: "' . $v[1] . '",' : null) . (is_file($loc2) ? 'file: "' . $v[2] . '",' : null) . ' label: "' . $k . '", mediaid: "' . $file_key . '" }';
                        }
                    }
                } else {
                    if (($srv->fields["server_type"] == 's3' or $srv->fields["server_type"] == 'ws') and $srv->fields["cf_enabled"] == 1 and $srv->fields["cf_dist_type"] == 'r') {
                        if (is_file($loc0)) {
                            $src[] = '{ mp4: "mp4:' . str_replace($url . '/', '', $v[0]) . '" }';
                        }
                    } else {
                        if (is_file($loc0)) {
                            $src[] = '{ mp4: "' . $v[0] . '" }';
                        }
                        if (is_file($loc3)) {
                            $src[] = '{ flv: "' . $v[3] . '" }';
                        }
                        if (is_file($loc1)) {
                            $src[] = '{ webm: "' . $v[1] . '" }';
                        }
                        if (is_file($loc2)) {
                            $src[] = '{ ogg: "' . $v[2] . '" }';
                        }
                    }
                }
            }
        }

        return implode($src, ',');
    }

    /* video ads */
    public function getVideoAds($file_key)
    {
        global $db, $class_database, $cfg, $language;

        $pre_str  = null;
        $post_str = null;
        $acfg     = array();
        $pcfg     = unserialize($class_database->singleFieldValue('db_fileplayers', 'db_config', 'db_name', 'jw_local'));

        $res = $db->execute(sprintf("SELECT `jw_ads` FROM `db_videofiles` WHERE `file_key`='%s' LIMIT 1;", $file_key));
        $ads = $res->fields["jw_ads"];

        if ($ads != '') {
//found ads assigned to video
            $ar     = unserialize($ads);
            $pre    = $ar[0];
            $post   = $ar[1];
            $client = $class_database->singleFieldValue('db_jwadentries', 'ad_client', 'ad_id', (intval($pre) > 0 ? $pre : (intval($post) > 0 ? $post : $ar[2])));
        } else {
//no video ads assigned/generate a random ad
            $pres  = array();
            $posts = array();
            $mids  = array();

            $res = $db->execute(sprintf("SELECT `jw_ads` FROM `db_videofiles` WHERE `file_key`!='%s';", $file_key));
            if ($res->fields["jw_ads"]) {
                while (!$res->EOF) {
                    $_ar  = unserialize($res->fields["jw_ads"]);
                    $pre  = $_ar[0];
                    $post = $_ar[1];

                    if (intval($pre) > 0) {
//exclude these prerolls (already assigned)
                        $pres[] = $pre;
                    }
                    if (intval($post) > 0) {
//exclude these postrolls (already assigned)
                        $posts[] = $post;
                    }
                    if ($_ar[2] != '') {

                        $_mr = array();
                        $_mr = $_ar;
                        unset($_mr[0]);
                        unset($_mr[1]);
                        foreach ($_mr as $_m) {
//exclude these midrolls (already assigned)
                            $mids[] = $_m;
                        }
                    }

                    $res->MoveNext();
                }
            } else {
                $pres[]  = -1;
                $posts[] = -1;
                $mids[]  = -1;
            }
            $ac         = array('ima', 'vast');
            $ad_clients = array_rand($ac, 1);

            if (count($pres) >= 0) {
                $p1  = $db->execute(sprintf("SELECT `ad_id` FROM `db_jwadentries` WHERE `ad_position`='pre' AND `ad_client`='%s' AND `ad_active`='1' AND `ad_id` NOT IN (%s) ORDER BY RAND() LIMIT 1;", $ac[$ad_clients], implode(',', $pres)));
                $pre = $p1->fields["ad_id"];
            }
            if (count($posts) >= 0) {
                $p2   = $db->execute(sprintf("SELECT `ad_id` FROM `db_jwadentries` WHERE `ad_position`='post' AND `ad_client`='%s' AND `ad_active`='1' AND `ad_id` NOT IN (%s) ORDER BY RAND() LIMIT 1;", $ac[$ad_clients], implode(',', $posts)));
                $post = $p2->fields["ad_id"];
            }
            if (count($mids) >= 0) {
                $p3 = $db->execute(sprintf("SELECT `ad_id` FROM `db_jwadentries` WHERE `ad_position`='offset' AND `ad_client`='%s' AND `ad_active`='1' AND `ad_id` NOT IN (%s) ORDER BY `ad_offset` ASC;", $ac[$ad_clients], implode(',', $mids)));
                if ($p3->fields["ad_id"]) {

                    $ar    = array();
                    $ar[0] = 0;
                    $ar[1] = 0;

                    while (!$p3->EOF) {
                        $ar[] = $p3->fields["ad_id"];

                        $p3->MoveNext();
                    }
                }
            }
            $client = $ac[$ad_clients];
        }

        if (intval($pre) > 0) {
//preroll
            $pre_res = $db->execute(sprintf("SELECT `ad_key`, `ad_tag`, `ad_format`, `ad_server` FROM `db_jwadentries` WHERE `ad_id`='%s' LIMIT 1;", $pre));
            $pre_key = $pre_res->fields["ad_key"];
            $pre_tag = $pre_res->fields["ad_tag"];
            $pre_frm = $pre_res->fields["ad_format"];
            $pre_srv = $pre_res->fields["ad_server"];
            $pre_cst = $cfg["main_url"] . '/' . VHref::getKey("vast") . '?v=' . $pre_key;

            $pre_str = sprintf("pre%s: { offset: \"pre\", tag: \"%s\" %s }", $pre_key, (($pre_srv == 'custom' and $pre_tag == 'auto') ? $pre_cst : $pre_tag), ($pre_frm == 'nonlinear' ? ', type: "nonlinear"' : null));
            $acfg[]  = $pre_str;
        }
        if ($ar[2] != '') {
//midrolls
            $mr = array();
            $mr = $ar;
            unset($mr[0]);
            unset($mr[1]);

            $mid_res = $db->execute(sprintf("SELECT `ad_key`, `ad_tag`, `ad_offset`, `ad_format`, `ad_server` FROM `db_jwadentries` WHERE `ad_id` IN (%s);", implode(',', $mr)));
            if ($mid_res->fields["ad_key"]) {
                while (!$mid_res->EOF) {
                    $mid_key = $mid_res->fields["ad_key"];
                    $mid_tag = $mid_res->fields["ad_tag"];
                    $mid_frm = $mid_res->fields["ad_format"];
                    $mid_off = $mid_res->fields["ad_offset"];
                    $mid_srv = $mid_res->fields["ad_server"];
                    $mid_cst = $cfg["main_url"] . '/' . VHref::getKey("vast") . '?v=' . $mid_key;

                    $mid_str = sprintf("mid%s: { offset: %s, tag: \"%s\" %s }", $mid_key, $mid_off, (($mid_srv == 'custom' and $mid_tag == 'auto') ? $mid_cst : $mid_tag), ($mid_frm == 'nonlinear' ? ', type: "nonlinear"' : null));
                    $acfg[]  = $mid_str;

                    $mid_res->MoveNext();
                }
            }
        }
        if (intval($post) > 0) {
//postroll

            $post_res = $db->execute(sprintf("SELECT `ad_key`, `ad_tag`, `ad_format`, `ad_server` FROM `db_jwadentries` WHERE `ad_id`='%s' LIMIT 1;", $post));
            $post_key = $post_res->fields["ad_key"];
            $post_tag = $post_res->fields["ad_tag"];
            $post_frm = $post_res->fields["ad_format"];
            $post_srv = $post_res->fields["ad_server"];
            $post_cst = $cfg["main_url"] . '/' . VHref::getKey("vast") . '?v=' . $post_key;

            $post_str = sprintf("post%s: { offset: \"post\", tag: \"%s\" %s }", $post_key, (($post_srv == 'custom' and $post_tag == 'auto') ? $post_cst : $post_tag), ($post_frm == 'nonlinear' ? ', type: "nonlinear"' : null));
            $acfg[]   = $post_str;
        }

        $adv = '
        client: "' . $client . '",
        schedule: {
        ' . implode(',', $acfg) . '
        }
        ' . ($pcfg["jw_adv_msg"] != '' ? ', admessage: "' . $language[$pcfg["jw_adv_msg"]] . '"' : null) . '
    ';

        return $adv;
    }
    /* build video js file sources */
    public function buildVideoJSSources($type, $file_key, $usr_key, $section, $fp = '', $is_short = false)
    {
        require_once 'class.be.servers.php';
        global $db, $class_database, $cfg;

        $src = array();
        $fmt = array();
        $res = array();

        $rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        $sql = sprintf("SELECT
                                                A.`server_type`, A.`lighttpd_url`, A.`lighttpd_secdownload`, A.`lighttpd_prefix`, A.`lighttpd_key`, A.`cf_enabled`, A.`cf_dist_type`,
                                                A.`cf_signed_url`, A.`cf_signed_expire`, A.`cf_key_pair`, A.`cf_key_file`,
                                                A.`s3_bucketname`, A.`s3_accesskey`, A.`s3_secretkey`,
                                                B.`upload_server`, B.`has_preview`
                                                FROM
                                                `db_servers` A, `db_%sfiles` B
                                                WHERE
                                                B.`file_key`='%s' AND
                                                B.`upload_server` > '0' AND
                                                A.`server_id`=B.`upload_server` LIMIT 1;", $type, $file_key);
        $srv              = $db->execute($sql);
        $cf_signed_url    = $srv->fields["cf_signed_url"];
        $cf_signed_expire = $srv->fields["cf_signed_expire"];
        $cf_key_pair      = $srv->fields["cf_key_pair"];
        $cf_key_file      = $srv->fields["cf_key_file"];

        if (($cfg["conversion_mp4_360p_active"] == 0 and $cfg["conversion_mp4_480p_active"] == 0 and $cfg["conversion_mp4_720p_active"] == 0 and $cfg["conversion_mp4_1080p_active"] == 0) and ($cfg["conversion_vpx_360p_active"] == 0 and $cfg["conversion_vpx_480p_active"] == 0 and $cfg["conversion_vpx_720p_active"] == 0 and $cfg["conversion_vpx_1080p_active"] == 0) and ($cfg["conversion_ogv_360p_active"] == 0 and $cfg["conversion_ogv_480p_active"] == 0 and $cfg["conversion_ogv_720p_active"] == 0 and $cfg["conversion_ogv_1080p_active"] == 0) and ($cfg["conversion_flv_360p_active"] == 1 or $cfg["conversion_flv_480p_active"] == 1)) {
            return self::getFLVsrc($type, $usr_key, $file_key, '', $fp);
        } else {
            $f   = self::fileSources($type, $usr_key, $file_key, $srv, $is_short);
            $url = VGenerate::fileURL($type, $file_key, 'upload');

            foreach ($f as $k => $v) {
                if (($srv->fields["lighttpd_url"] != '' and $srv->fields["lighttpd_secdownload"] == 1) or ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 1)) {
                    $l0   = explode("/", $v[0]);
                    $loc0 = $cfg["media_files_dir"] . '/' . $l0[6] . '/' . $l0[7] . '/' . $l0[8];
                    $l1   = explode("/", $v[1]);
                    $loc1 = $cfg["media_files_dir"] . '/' . $l1[6] . '/' . $l1[7] . '/' . $l1[8];
                    $l2   = explode("/", $v[2]);
                    $loc2 = $cfg["media_files_dir"] . '/' . $l2[6] . '/' . $l2[7] . '/' . $l2[8];
                    $l3   = explode("/", $v[3]);
                    $loc3 = $cfg["media_files_dir"] . '/' . $l3[6] . '/' . $l3[7] . '/' . $l3[8];
                    $l4   = explode("/", $v[4]);
                    $loc4 = $cfg["media_files_dir"] . '/' . $l4[6] . '/' . $l4[7] . '/' . $l4[8];
                } elseif ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 0) {
                    $loc0 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[2]);
                    $loc3 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[3]);
                    $loc4 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[4]);
                } else {
                    $loc0 = str_replace($url, $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($url, $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($url, $cfg["media_files_dir"], $v[2]);
                    $loc3 = str_replace($url, $cfg["media_files_dir"], $v[3]);
                    $loc4 = str_replace($url, $cfg["media_files_dir"], $v[4]);

                    $fp = 1;

                    if (($srv->fields["server_type"] == 's3' or $srv->fields["server_type"] == 'ws') and $srv->fields["cf_enabled"] == 1 and $cf_signed_url == 1) {
                        if ($srv->fields["cf_dist_type"] == 'r' and $fp != '') {
                            $v[0] = strstr($v[0], $usr_key);
                            $v[1] = strstr($v[1], $usr_key);
                            $v[2] = strstr($v[2], $usr_key);
                            $v[3] = strstr($v[3], $usr_key);
                            $v[4] = strstr($v[4], $usr_key);
                        }

                        if ($type == 'audio') {
                            $rtmp = $srv->fields["cf_enabled"] == 1 ? $class_database->singleFieldValue('db_servers', 'cf_dist_domain', 'server_id', $srv->fields["upload_server"]) : $class_database->singleFieldValue('db_servers', 's3_bucketname', 'server_id', $srv->fields["upload_server"]);

                            $v[0] = strstr($v[0], $usr_key);
                        }

                        if (($srv->fields["server_type"] == 's3' or $srv->fields["server_type"] == 'ws') and $srv->fields["cf_dist_type"] == 'r') {
                            $s3_accesskey  = $srv->fields["s3_accesskey"];
                            $s3_secretkey  = $srv->fields["s3_secretkey"];
                            $s3_bucketname = $srv->fields["s3_bucketname"];

                            $v[0] = VbeServers::getS3SignedURL($s3_accesskey, $s3_secretkey, $v[0], $s3_bucketname, $cf_signed_expire, $srv->fields["server_type"]);
                            $v[1] = VbeServers::getS3SignedURL($s3_accesskey, $s3_secretkey, $v[1], $s3_bucketname, $cf_signed_expire, $srv->fields["server_type"]);
                            $v[2] = VbeServers::getS3SignedURL($s3_accesskey, $s3_secretkey, $v[2], $s3_bucketname, $cf_signed_expire, $srv->fields["server_type"]);
                            $v[3] = VbeServers::getS3SignedURL($s3_accesskey, $s3_secretkey, $v[3], $s3_bucketname, $cf_signed_expire, $srv->fields["server_type"]);
                            $v[4] = VbeServers::getS3SignedURL($s3_accesskey, $s3_secretkey, $v[4], $s3_bucketname, $cf_signed_expire, $srv->fields["server_type"]);
                        } else {
                            $v[0] = VbeServers::getSignedURL($v[0], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                            $v[1] = VbeServers::getSignedURL($v[1], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                            $v[2] = VbeServers::getSignedURL($v[2], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                            $v[3] = VbeServers::getSignedURL($v[3], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                            $v[4] = VbeServers::getSignedURL($v[4], $cf_signed_expire, $cf_key_pair, $cf_key_file);
                        }
                    }
                }
                if (is_file($loc0)) {
                    $src[] = "{ src: '" . $v[0] . "', type: '" . ($type == 'short' ? 'video' : $type) . "/mp4', label: '" . $k . "', res: " . str_replace('p', '', $k) . " }";
                }
                if (is_file($loc1)) {
                    $src[] = "{ src: '" . $v[1] . "', type: '" . ($type == 'short' ? 'video' : $type) . "/" . ($type == 'audio' ? "mp3" : "webm") . "', label: '" . $k . "', res: " . str_replace('p', '', $k) . " }";
                }
            }
        }
        return implode($src, ',');
    }
    /* Video.js javascript */
    public function VJSJS($section, $usr_key = '', $file_key = '', $is_hd = '', $next_file_key = '', $next_pl_key = '')
    {
        global $cfg, $class_filter, $class_database, $language, $db, $smarty, $bl_stat, $is_shorts;

        $cfg[] = $class_database->getConfigurations('stream_method,stream_server,stream_lighttpd_key,stream_lighttpd_prefix,stream_lighttpd_url,stream_lighttpd_secure,stream_rtmp_location,conversion_video_previews,conversion_live_previews,conversion_audio_previews');
        $_get  = $section == 'embed' ? 'vjs_embed' : 'vjs_local';
        $_cfg  = unserialize($class_database->singleFieldValue('db_fileplayers', 'db_config', 'db_name', $_get));

        $p       = self::playerInit($section);
        $_vid    = $class_filter->clr_str($_GET["v"]);
        $_id     = $p[0];
        $_width  = $p[1];
        $_height = $p[2];
        $type    = isset($_GET["s"]) ? 's' : (isset($_GET["l"]) ? 'l' : (isset($_GET["v"]) ? 'v' : (isset($_GET["i"]) ? 'i' : (isset($_GET["a"]) ? 'a' : (isset($_GET["d"]) ? 'd' : null)))));
        $backend = (isset($_GET["section"]) and $_GET["section"] == 'backend') ? true : false;

        if ($usr_key == 'video' or $usr_key == 'live') {
            $_for    = 'video';
            $usr_key = '';
        } else {
            $_for = null;
        }

        $usr_key  = $class_filter->clr_str($usr_key);
        $file_key = $class_filter->clr_str($file_key);
        $is_hd    = $class_filter->clr_str($is_hd);

        $fsrc        = self::getFileUrl($type, $file_key, $usr_key);
        $file1       = $fsrc[0];
        $file2       = $fsrc[1];
        $tbl         = $fsrc[2];
        $ee          = $db->execute(sprintf("SELECT `usr_id`, `old_file_key`, `thumb_cache`, `embed_src`, `embed_url`, `has_preview`, `comments` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $tbl, $file_key));
        $esrc        = $ee->fields["embed_src"];
        $eurl        = str_replace('shorts', 'embed', $ee->fields["embed_url"]);
        $hpv         = $ee->fields["has_preview"];
        $vuid        = $ee->fields["usr_id"];
        $old         = $ee->fields["old_file_key"];
        $is_short    = isset($_GET["s"]) ?: $is_shorts;
        $vcomm       = $ee->fields["comments"];
        $thumb_cache = $ee->fields["thumb_cache"];
        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;

        if (isset($_SESSION["USER_NAME"])) {
            $bl_stat = VContacts::getBlockStatus($vuid, $_SESSION["USER_NAME"]); //am I blocked
            $bl_opt  = VContacts::getBlockCfg('bl_files', $vuid, $_SESSION["USER_NAME"]);

            if ($bl_stat == 1 and $bl_opt == 1) {
                return;
            }

        }

        $image   = VGenerate::thumbSigned($tbl, $file_key, array($usr_key, $thumb_cache));
        $flv_dir = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $file1);
        $mp4_dir = str_replace($cfg["media_files_url"], $cfg["media_files_dir"], $file2);

        $is_mobile = VHref::isMobile();
        $src       = self::buildVideoJSSources($tbl, $file_key, $usr_key, ($section == 'channel' ? ($_for == 'video' ? 'channel' : 'channel2') : $section), '', $is_short);

        $previews                = (($tbl == 'live' and $cfg["conversion_live_previews"] == 1 and $hpv == 1) or ($tbl == 'video' and $cfg["conversion_video_previews"] == 1 and $hpv == 1) or ($tbl == 'audio' and $cfg["conversion_audio_previews"] == 1 and $hpv == 1)) ? true : false;
        $_cfg["vjs_advertising"] = $smarty->getTemplateVars('vjs_advertising');

        if ($previews and isset($_SESSION["USER_ID"]) and (int) $_SESSION["USER_ID"] > 0) {
            if ($vuid > 0) {
                if ($vuid == (int) $_SESSION["USER_ID"]) {
                    $previews = false;
                } else {
                    $ss = $db->execute(sprintf("SELECT `db_id`, `sub_list` FROM `db_subscriptions` WHERE `usr_id`='%s' LIMIT 1;", (int) $_SESSION["USER_ID"]));
                    if ($ss->fields["db_id"]) {
                        $subs = unserialize($ss->fields["sub_list"]);
                        if (in_array($vuid, $subs)) {
                            $sb = $db->execute(sprintf("SELECT `db_id` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' LIMIT 1;", (int) $_SESSION["USER_ID"], $vuid, date("Y-m-d H:i:s")));
                            if ($sb->fields["db_id"] > 0) {
                                $previews = false;
                            } else {
                                $previews = true;
                            }

                        }
                    }
                    if ($previews) {
                        $ts = $db->execute(sprintf("SELECT `db_id` FROM `db_subtemps` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' AND `active`='1' LIMIT 1;", (int) $_SESSION["USER_ID"], $vuid, date("Y-m-d H:i:s")));

                        if ($ts->fields["db_id"]) {
                            $previews = false;
                        }
                    }
                }
            }
        } elseif (isset($_GET["section"]) and $_GET["section"] == 'backend' and isset($_GET["pv"])) {
            $previews = (bool) $_GET["pv"];
        }
        $previews = !$previews ? ($old == 1 ? true : false) : $previews;

        if ($is_mobile) {
            $csql = sprintf("SELECT
                                                 A.`upload_server`, A.`has_preview`, A.`old_file_key`,
                                                 B.`server_type`, B.`cf_enabled`, B.`cf_signed_url`, B.`cf_signed_expire`, B.`cf_key_pair`, B.`cf_key_file`,
                                                 B.`s3_bucketname`, B.`s3_accesskey`, B.`s3_secretkey`, B.`cf_dist_type`
                                                 FROM
                                                 `db_%sfiles` A, `db_servers` B
                                                 WHERE
                                                 A.`file_key`='%s' AND
                                                 A.`upload_server`>'0' AND
                                                 A.`upload_server`=B.`server_id`
                                                 LIMIT 1", $tbl, $file_key);
            $cf = $db->execute($csql);

            if ($cf->fields["upload_server"] > 0) {
                $server_type       = $cf->fields["server_type"];
                $cf_enabled        = $cf->fields["cf_enabled"];
                $cf_signed_url     = $cf->fields["cf_signed_url"];
                $cf_signed_expire  = $cf->fields["cf_signed_expire"];
                $cf_key_pair       = $cf->fields["cf_key_pair"];
                $cf_key_file       = $cf->fields["cf_key_file"];
                $s3_bucket         = $cf->fields["s3_bucketname"];
                $aws_access_key_id = $cf->fields["s3_accesskey"];
                $aws_secret_key    = $cf->fields["s3_secretkey"];
                $dist_type         = $cf->fields["cf_dist_type"];
                $old               = $cf->fields["old_file_key"];
                $hpv               = $cf->fields["has_preview"];
            } else {
                $cc  = $db->execute(sprintf("SELECT `old_file_key`, `has_preview` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $tbl, $file_key));
                $old = $cc->fields["old_file_key"];
                $hpv = $cc->fields["has_preview"];
            }

            $fk = $previews ? $file_key : md5($cfg["global_salt_key"] . $file_key);

            $type_ext = $tbl[0] == 'a' ? 'mp4' : 'mob.mp4';
            $a_url    = VGenerate::fileURL($tbl, $file_key, 'upload') . '/' . $usr_key . '/' . $tbl[0] . '/' . $fk . '.' . $type_ext;

            if (($server_type == 's3' or $server_type == 'ws') and $cf_enabled == 1 and $cf_signed_url == 1) {
                $file_path = $usr_key . '/' . $type[0] . '/' . $fk . '.' . $type_ext;

                if (($server_type == 's3' or $server_type == 'ws') and $dist_type == 'r') {
                    $a_url = VbeServers::getS3SignedURL($aws_access_key_id, $aws_secret_key, $file_path, $s3_bucket, $cf_signed_expire, $server_type);
                } else {
                    $a_url = VbeServers::getSignedURL($a_url, $cf_signed_expire, $cf_key_pair, $cf_key_file);
                }
            }

            $src = sprintf("{ src: '%s', type: 'video/mp4', label: '360p' }", $a_url);
            if ($is_short) {
                $src = str_replace('mob', 'short', $src);
            }
            $ad_client = $smarty->getTemplateVars('ad_client');

            if ($esrc == 'local' and $_cfg["vjs_advertising"] == 1 and $ad_client == 'ima') {
                $mob_src = "_src = '" . $a_url . "';";
                //return "_src = '".$a_url."';";
            }
        }

        $load_preview = $previews;
        $hls          = false;
        $js           = '';

        if ($tbl == 'live') {
            $sl    = $db->execute(sprintf("SELECT `vod_server`, `stream_key`, `file_name`, `stream_vod`, `stream_ended`, `stream_live` FROM `db_livefiles` WHERE `file_key`='%s' LIMIT 1;", $file_key));
            $sk    = $sl->fields["stream_key"];
            $fn    = $sl->fields["file_name"];
            $srv   = $sl->fields["vod_server"];
            $ended = $sl->fields["stream_ended"];

            if ($srv > 0 and $sl->fields["stream_live"] == 0) {
//get vod server
                $_rs = $db->execute(sprintf("SELECT `srv_host`, `srv_port`, `srv_https` FROM `db_liveservers` WHERE `srv_type`='vod' AND `srv_id`='%s' AND `srv_active`='1' LIMIT 1;", $srv));
                if ($_rs->fields["srv_host"]) {
                    $cfg["live_vod_server"] = sprintf("%s://%s:%s/vod", ($_rs->fields["srv_https"] == 1 ? 'https' : 'http'), $_rs->fields["srv_host"], $_rs->fields["srv_port"]);
                } else {
                    $_rs = $db->execute(sprintf("SELECT `srv_host`, `srv_port`, `srv_https` FROM `db_liveservers` WHERE `srv_type`='vod' AND `srv_active`='1' ORDER BY RAND() LIMIT 1;"));

                    $cfg["live_vod_server"] = sprintf("%s://%s:%s/vod", ($_rs->fields["srv_https"] == 1 ? 'https' : 'http'), $_rs->fields["srv_host"], $_rs->fields["srv_port"]);
                }
            } elseif ($sl->fields["stream_live"] == 1) {
//load balancer login here
                $_rs = $db->execute(sprintf("SELECT `srv_host`, `srv_port`, `srv_https` FROM `db_liveservers` WHERE `srv_type`='lbs' AND `srv_active`='1' ORDER BY RAND() LIMIT 1;", $srv));
                if ($_rs->fields["srv_host"]) {
//get a free server
                    $lbs = sprintf("%s://%s:%s", ($_rs->fields["srv_https"] == 1 ? 'https' : 'http'), $_rs->fields["srv_host"], $_rs->fields["srv_port"]);

                    $vs                     = json_decode(VServer::curl_tt($lbs . "/freeserver"));
                    $cfg["live_hls_server"] = sprintf("%s://%s:%s/hls", $vs->protocol, $vs->ip, $vs->port);
                } else {
//load a random server
                    $_rs = $db->execute(sprintf("SELECT `srv_host`, `srv_port`, `srv_https` FROM `db_liveservers` WHERE `srv_type`='stream' AND `srv_active`='1' ORDER BY RAND() LIMIT 1;"));

                    $cfg["live_hls_server"] = sprintf("%s://%s:%s/hls", ($_rs->fields["srv_https"] == 1 ? 'https' : 'http'), $_rs->fields["srv_host"], $_rs->fields["srv_port"]);
                }
            }
            $msg = $ended == '1' ? $language["view.files.player.lang1"] : $language["view.files.player.lang2"];
            $js .= 'videojs.addLanguage(\'en\', {"The media could not be loaded, either because the server or network failed or because the format is not supported.": "' . $msg . '"});';
            $js .= 'videojs.addLanguage(\'en\', {"HLS.js error: networkError - fatal: true - levelLoadError": "' . $msg . '"});';

            if ($sl->fields["stream_live"] == 0 and $sl->fields["stream_vod"] == 1 and $load_preview) {
                $_f  = explode("-", $fn);
                $_ff = str_replace('out', 'p', $_f[1]);
                $pn  = $_f[0] . $_ff . '.mp4';

                $hls = "'sources': [{'type':'video/mp4', 'src':'" . $cfg["live_vod_server"] . "/" . $pn . "'}],";
                if ($mob_src != '') {
                    return $mob_src = "_src = '" . $cfg["live_vod_server"] . "/" . $pn . "';";
                }

            } else if ($sl->fields["stream_live"] == 0 and $sl->fields["stream_vod"] == 1 and !$load_preview) {
                $hls = "'sources': [{'type':'video/mp4', 'src':'" . $cfg["live_vod_server"] . "/" . $fn . ".mp4'}],";
                if ($mob_src != '') {
                    return $mob_src = "_src = '" . $cfg["live_vod_server"] . "/" . $fn . ".mp4';";
                }

            } else if ($sl->fields["stream_live"] == 1) {
                $hls = "'sources': [{'type':'application/x-mpegURL', 'src':'" . $cfg["live_hls_server"] . "/" . $sk . "/index.m3u8', 'withCredentials': true}],";
                if ($mob_src != '') {
                    return $mob_src = "_src = '" . $cfg["live_hls_server"] . "/" . $sk . "/index.m3u8';";
                }

            }
        }

        $colors = array('cyan' => '#00997a', 'default' => '#06a2cb', 'green' => '#199900', 'orange' => '#f28410', 'pink' => '#ec7ab9', 'purple' => '#b25c8b', 'red' => '#dd1e2f');
        $ccode  = '#06a2cb';
        foreach ($colors as $cn => $cc) {
            if (strpos($cfg["theme_name"], $cn) !== false) {
                $ccode = $cc;
            }
        }

        $js .= "var player = videojs('view-player-" . $file_key . "', {'html5':{'hlsjsConfig':{'debug': false}},'controls': " . ($_cfg['vjs_layout_controls'] == 1 ? 'true' : 'false') . ",'autoplay': " . ((isset($_GET["p"]) or $_cfg['vjs_autostart'] == 1) ? 'true' : 'false') . ",'loop': " . ($_cfg['vjs_loop'] == 1 ? 'true' : 'false') . ",'muted': " . ($_cfg['vjs_muted'] == 1 ? 'true' : 'false') . ",'playbackRates': [0.5, 1, 1.5, 2]," . $hls . "" . ($esrc != 'local' ? "'techOrder': ['" . $esrc . "']," : null) . "" . ($esrc != 'local' ? "'sources': [{ 'type': 'video/" . $esrc . "', 'src': '" . $eurl . "' }], '" . $esrc . "': { 'ytcontrols': 1 }," : null) . "'plugins': {'videoJsResolutionSwitcher': {'default': 'high','dynamicLabel': true},'watermark': {'image': '" . $_cfg['vjs_logo_file'] . "','url': '" . ($section == 'embed' ? $cfg["main_url"] . '/' . VGenerate::fileHref($tbl[0], $file_key, $title) : $_cfg['vjs_logo_url']) . "','position': '" . $_cfg['vjs_logo_position'] . "','fadeTime': '" . $_cfg['vjs_logo_fade'] . "'}}}, function() {var ht = '<svg onclick=\"player.play()\" version=\"1.1\" id=\"play\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\" x=\"0px\" y=\"0px\" height=\"100px\" width=\"100px\" viewBox=\"0 0 100 100\" enable-background=\"new 0 0 100 100\" xml:space=\"preserve\"><path class=\"stroke-solid\" fill=\"none\" stroke=\"grey\"  d=\"M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7C97.3,23.7,75.7,2.3,49.9,2.5\"/><path class=\"stroke-dotted\" fill=\"none\" stroke=\"" . $ccode . "\"  d=\"M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7C97.3,23.7,75.7,2.3,49.9,2.5\"/><path class=\"icon\" fill=\"" . $ccode . "\" d=\"M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z\"/></svg>';$(ht).insertAfter('.vjs-loading-spinner');var ht2=$('.pv-text');$(ht2).insertBefore('.vjs-control-bar').show();" . (!$hls ? ($esrc == 'local' ? "var player = this; window.player = player;" . (($tbl == 'video' or !$is_mobile or $is_short) ? "player.updateSrc([" . $src . "]);" : "") : "var player = this; window.player=player;player.on('resolutionchange', function(){})") : null) . "});";
        $js .= "player.poster('" . $image . "');player.contextmenuUI({content: [" . ($_cfg['vjs_rc_text1'] != '' ? "{href: '" . $_cfg['vjs_rc_link1'] . "',label: '" . $_cfg['vjs_rc_text1'] . "'}," : null) . "" . ($_cfg['vjs_rc_text2'] != '' ? "{href: '" . $_cfg['vjs_rc_link2'] . "',label: '" . $_cfg['vjs_rc_text2'] . "'}," : null) . "" . ($_cfg['vjs_rc_text3'] != '' ? "{href: '" . $_cfg['vjs_rc_link3'] . "',label: '" . $_cfg['vjs_rc_text3'] . "'}," : null) . "" . ($_cfg['vjs_rc_text4'] != '' ? "{href: '" . $_cfg['vjs_rc_link4'] . "',label: '" . $_cfg['vjs_rc_text4'] . "'}," : null) . "" . ($_cfg['vjs_rc_text5'] != '' ? "{href: '" . $_cfg['vjs_rc_link5'] . "',label: '" . $_cfg['vjs_rc_text5'] . "'}" : null) . "]});" . (($_cfg["vjs_related"] == 1 and !$backend) ? "player.suggestedVideoEndcap({header: '" . str_replace('##TYPE##', $language["frontend.global." . $tbl[0] . ".p.c"], $language["view.files.related.alt"]) . "',suggestions: getSuggested()});" : null) . "" . (((($load_preview or $pp->fields["old_file_key"] == 1) and is_file($cfg['media_files_dir'] . "/" . $usr_key . "/t/" . $file_key . "/p/thumbnails.vtt")) or (!$load_preview and is_file($cfg['media_files_dir'] . "/" . $usr_key . "/t/" . $file_key . "/p/" . md5($cfg["global_salt_key"] . $file_key) . "/thumbnails.vtt"))) ? "player.on('loadedmetadata', function() { player.thumbnails({'width': '140','height': '105','basePath': '" . $cfg['media_files_url'] . "/" . $usr_key . "/t/" . $file_key . "/p/" . ((!$load_preview and $pp->fields["old_file_key"] == 0) ? md5($cfg["global_salt_key"] . $file_key) . "/" : null) . "'});});" : null) . "player.on('ended', function() {" . ($next_file_key != '' ? "document.location = '" . $cfg["main_url"] . '/' . VGenerate::fileHref(($_GET["v"] != '' ? 'v' : ($_GET['s'] != '' ? 's' : 'a')), $next_file_key, '') . '&p=' . $next_pl_key . "';" : "if($('input[name=autoplay_switch_check]').is(':checked')){u = $('.vs-column.full-thumbs.first-entry .full-details-holder a').attr('href');if (typeof u == 'undefined') {u = $('.suggested-list li:first.vs-column.thirds figcaption a').attr('href');}document.location = u;}") . "});" . ($_cfg['vjs_autostart'] == 1 ? "$(document).ready(function(){ setTimeout(function(){ $('.vjs-big-play-button').click() }, 100); });" : null) . "function getSuggested() {if (typeof($('.related-column .suggested-list li:first').html()) == 'undefined') {return [];}a = '[';$('.related-column .suggested-list li').each(function() {t = $(this);title = t.find('.mediaThumb').attr('alt').replace(/\|/g, '').replace(/\"/g, '\'');url = t.find('.full-details-holder h3 a').attr('href');image = t.find('.mediaThumb.loaded').attr('src');if (typeof image == 'undefined') {image = t.find('.mediaThumb').attr('data-src');}a += '{ \"title\": \"'+title+'\", \"url\": \"'+url+'\", \"image\": \"'+image+'\", \"target\": \"_self\" },';});a = a.substring(0, a.length-1);a += ']';return JSON.parse(a);}";
        $js .= "player.on('loadedmetadata', function() {" . ((isset($_GET["p"]) or $_cfg['vjs_autostart'] == 1) ? "player.play();" : null) . "if (!player.paused() && player.muted() && typeof $('.muted-overlay').html() == 'undefined'){jQuery('<div class=\"muted-overlay\" onclick=\"player.muted(false); jQuery(this).detach();\"><button class=\"save-entry-button button-grey search-button\" value=\"1\" name=\"overlay_unmute\"><i class=\"icon-volume-mute2\"></i><span>" . $language["view.files.unmute"] . "</span></button></div>').insertBefore('.vjs-poster')} });";
        $js .= ($esrc == "youtube") ? "player.on('pause',function(){jQuery('<div id=\"pause-overlay\" style=\"background-image:url(" . $image . ")\"></div>').insertBefore(\"#play\")});player.on('play',function(){jQuery(\"#pause-overlay\").detach()});" : null;
        if ($tbl == 'live' and $sl->fields["stream_live"] == 1) {
            $js .= "player.on('error',()=>{const error=player.error();if(error.code===2){jQuery.post('".$cfg["main_url"]."/".VHref::getKey('lstatus')."?l=".$file_key."',{a:'".secured_encrypt($file_key)."'},function(data){if(data.ls===1){player.src([{'type':'application/x-mpegURL','src':'" . $cfg["live_hls_server"] . "/" . $sk . "/index.m3u8'}]);player.play()}else{jQuery('.vjs-modal-dialog-content').text('".$language["view.files.player.lang1"]."');}});}});";
        } else if ($tbl == 'short') {
            $js .= "player.on('error',()=>{const error=player.error();if(error.code===1150)$('.tpl_shorts #shorts-arrows').show()});";
        }

        if ($is_short and !$backend) {
            if ($cfg["file_comments"] == 1 and $vcomm != 'none') {
                $csql     = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_%scomments` WHERE `file_key`='%s' AND `c_approved`='1';", $tbl, $file_key));
                $ctotal   = $csql->fields["total"];
                $comments = true;
            } else {
                $ctotal   = '<span class="text-disabled">' . $language["frontend.global.disabled.text"] . '</span>';
                $comments = false;
            }
            $js .= "player.on('ready',function(){var eh=((typeof $('#comm-post-response').html()!='undefined')?$('#comm-post-response').height():0);
        var aspectRatio=9/16;var w;var h=$('body').height()-127-eh;if(h<=560)h=560;var w=h*aspectRatio;var tw=$(window).outerWidth();
        var ph='calc(100vh - 55px)';
        " . ($esrc !== "" ? "$('<div id=\"v-mask\" class=\"v-mask\"></div>').insertBefore('.vjs-poster');" : null) . "
        if (tw <= 576) {ph='calc(100vh - 55px)'}$('.video-js').css('width', w).css('height', ph);
        setTimeout(()=>{jQuery('.vjs-play-control, .vjs-volume-menu-button').insertAfter('.vjs-big-play-button');},10);
        $('<div class=\"overlay-data\"><div class=\"overlay-title\"></div><div class=\"overlay-user\"><div class=\"overlay-channel\"></div><div class=\"overlay-action\"><div class=\"profile_count\"></div></div></div></div>').appendTo('#view-player');
        $('<div class=\"overlay-buttons\"><div class=\"d-flex d-column\"></div></div>').insertBefore(\".overlay-data\");
        " . ($comments ? "$('<div class=\"overlay-comments\"><div class=\"d-flex d-column\"><div id=\"div-comments\" class=\"targetDiv border-wrappers\">" . VGenerate::simpleDivWrap('', 'comment-loader-before', '') . VGenerate::simpleDivWrap('', 'comment-loader', '') . "</div></div></div>').insertAfter(\".overlay-buttons\");" : null) . "
        " . ($comments ? "$('.overlay-comments').css('width', w).css('height', $('.vjs-poster').height());" : null) . "
        $('.like-wrap').appendTo('.overlay-buttons > div');
        " . ($comments ? "$('<div class=\"comment-wrapper\" rel=\"tooltip\" title=\"" . $language["subnav.entry.comments"] . "\"><div class=\"showSingle-lb sh_button btn_auto comm-comm\" " . ($comments ? "target=\"comment\"" : null) . "><i class=\"icon-bubble\"></i></div><div class=\"comments-text sh_button likes_count\" onclick=\"$(this).prev().click()\">" . $ctotal . "</div></div>').insertAfter('.like-wrap');" : null) . "
        $('.view-actions-wrapper div.showSingle-lb[target=\"share\"]').appendTo('.overlay-buttons > div').wrap('<div class=\"share-wrap\" rel=\"tooltip\" title=\"'+$('div.showSingle-lb[target=\"share\"]').attr('title')+'\"></div>');
        var st=$('div.showSingle-lb[target=\"share\"]').text();
        $('div.showSingle-lb[target=\"share\"]').text('');$('div.showSingle-lb[target=\"share\"]').removeAttr('rel').removeAttr('title');$('div.showSingle-lb[target=\"share\"]').html('<i class=\"icon-redo2\"></i>');
        $('<div class=\"share-text sh_button likes_count\"></div>').appendTo('.share-wrap').text(st);
        $('.view-actions-wrapper div.showSingle-lb[target=\"favorite\"]').insertAfter('.overlay-buttons div.share-wrap').wrap('<div class=\"favorite-wrap\" rel=\"tooltip\" title=\"'+$('div.showSingle-lb[target=\"favorite\"]').attr('title')+'\"></div>');
        var st=$('div.showSingle-lb[target=\"favorite\"]').text();
        $('div.showSingle-lb[target=\"favorite\"]').text('');$('div.showSingle-lb[target=\"favorite\"]').removeAttr('rel').removeAttr('title');$('div.showSingle-lb[target=\"favorite\"]').html('<i class=\"icon-plus\"></i>');
        $('<div class=\"favorite-text sh_button likes_count\"></div>').appendTo('.favorite-wrap').text(st);
        $('.view-actions-wrapper div.showSingle-lb[target=\"more\"]').appendTo('.overlay-buttons > div').wrap('<div class=\"more-wrap\" rel=\"tooltip\" title=\"'+$('div.showSingle-lb[target=\"more\"]').attr('title')+'\"></div>');
        if (($('.video-js').width() + $('.overlay-comments').width() + 100) >= tw) {jQuery('#view-player').addClass('bottom-comments');var ml = (tw <= 768) ? 46 : 58;} else {jQuery('#view-player').removeClass('bottom-comments');}
        var odw = tw >= 1320 ? 360 : $('.video-js').width()-20;
        odw = odw <= 748 ? (w <= 576 ? 210 : w) : odw;
        $('.overlay-data').css('width', odw);
        if (tw <= 1320) $('.overlay-data').addClass('on-p'); else $('.overlay-data').removeClass('on-p');
        if ($('.video-js').width()<386) {
            $('.overlay-user').css('flex-direction', 'column');
            $('.overlay-action .profile_count').css('justify-content', 'start');
            $('.overlay-action').css('margin-bottom', '20px').css('margin-right', '0');
        } else {
            $('.overlay-user').css('flex-direction', 'column');
            $('.overlay-action .profile_count').css('justify-content', 'start');
            $('.overlay-action').css('margin-bottom', '20px').css('margin-right', '15px');
        }
        $('.title-text:first').appendTo('.overlay-title');
        if (tw <= 576) { $('#view-player').addClass('s-576'); } else { $('#view-player').removeClass('s-576'); }
        if (tw <= 576) { $('.channel_image:first').prependTo('.overlay-buttons > div') } else {
            $('.channel_image:first').appendTo('.overlay-channel'); }
        $('.vdc-1:first > div > a').prependTo('.overlay-channel');
        " . (!isset($_SESSION["USER_ID"]) ? "$('a[target=\"follow\"]').appendTo('.overlay-action .profile_count');" : "$('a.follow-action, a.unfollow-action, .channel-owner-wrap .profile_count a').appendTo('.overlay-action .profile_count');") . "
        $('#shorts-preloader').detach();$('#view-player').removeClass('no-display');
        });";

            $js .= "
        window.addEventListener('resize',function(e){checkSwiperMenus()});
        if(screen.orientation){screen.orientation.addEventListener('change',function(e){setTimeout(() => { checkSwiperMenus()}, 100);})}
        function checkSwiperMenus(){var eh=((typeof $('#comm-post-response').html()!='undefined')?$('#comm-post-response').height():0);
        var aspectRatio=9/16;var w;var h=$('body').height()-127-eh;if(h<=560)h=560;var w=h*aspectRatio;var tw=$(window).outerWidth();
        if(screen.orientation){if(screen.orientation.type.startsWith('portrait')&&$('body').width()<= 560) {jQuery('.video-js').css('width',$('body').width());}else if(screen.orientation.type.startsWith('landscape')) {jQuery('.video-js').css('width',w);}}
        if (($('.video-js').width()+$('.overlay-comments').width()+100)>=tw){jQuery('#view-player').addClass('bottom-comments');var ml=(tw<=768)?46:58;}else{jQuery('#view-player').removeClass('bottom-comments');}
        if(w<=315){w=315}var pb = (tw <= 1560 ? '100px' : '150px');
        " . ($comments ? "$('.overlay-comments').css('width', w).css('height', $('.vjs-poster').height());" : null) . "
        var odw = tw >= 1320 ? 360 : $('.video-js').width()-20;
        odw = odw <= 748 ? (w <= 576 ? 210 : w) : odw;
        var odp = tw <= 1320 ? '46%' : '15%';
        $('.overlay-data').css('width', odw);
        if (tw <= 1320) $('.overlay-data').addClass('on-p'); else $('.overlay-data').removeClass('on-p');
        if ($('#view-player').hasClass('with-comments')) {
            var ww1 = $('.video-js').width()-30;
            $('.overlay-data').css('left','calc(46% - '+ww1+'px)').css('transform', 'translateX(calc(-50% + '+(ww1/2)+'px))');
        }
        if(h>=768) $('.video-js').width('100%');
        var sh=!$('#view-player').hasClass('bottom-comments')?$('.video-js').height():360;
        var ct=sh - ($('#comment-load .file-views-nr').height() + $('#comment-load #comm-post-response').height() + $('#comment-load #comm-post-form').height() + 20);
        $('.posted-comments').css('height', ct);
        if (tw<=576){jQuery('#view-player').addClass('s-576');}else{jQuery('#view-player').removeClass('s-576');}
        if (tw<=576){jQuery('.channel_image:first').prependTo('.overlay-buttons > div') }else {jQuery('.channel_image:first').appendTo('.overlay-channel');}if ($('.video-js').width()<386) {jQuery('.overlay-user').css('flex-direction', 'column');jQuery('.overlay-action .profile_count').css('justify-content', 'start');jQuery('.overlay-action').css('margin-bottom', '20px').css('margin-right', '0');} else {jQuery('.overlay-user').css('flex-direction', 'column');$('.overlay-action .profile_count').css('justify-content', 'start');$('.overlay-action').css('margin-bottom', '20px').css('margin-right', '15px');}}";
        }

        if ($tbl == 'audio' and $is_mobile) {
            $js .= '$("#view-player-' . $file_key . ' audio").attr("src", "' . str_replace('.mp4', '.mp3', $a_url) . '");';
        }

        if ($is_mobile and $esrc == 'local' and $_cfg["vjs_advertising"] == 1 and $ad_client == 'ima') {
            return $mob_src = "_src = '" . $a_url . "';";
        }

        return $js;
    }

    /* jw file subtitles */
    public function buildFileSubs($file_key, $section)
    {
        global $cfg, $class_database, $class_filter;

        $type = isset($_GET["a"]) ? 'audio' : (isset($_GET["l"]) ? 'live' : 'video');

        $sub_dir = $cfg["main_dir"] . '/f_data/data_subtitles/';
        $sub_url = $cfg["main_url"] . '/f_data/data_subtitles/';

        $_cfg = unserialize($class_database->singleFieldValue('db_fileplayers', 'db_config', 'db_name', 'jw_' . ($section == 'embed' ? 'embed' : 'local')));
        $s    = $class_database->singleFieldValue('db_' . $type . 'subs', 'jw_subs', 'file_key', $file_key);

        $js = '[';
        if ($s != '' and $_cfg["jw_captions_enabled"] == '1') {
            $ss        = array();
            $sub       = unserialize($s);
            $sub_files = array_values(array_diff(scandir($sub_dir), array('..', '.')));

            foreach ($sub as $s => $v) {
                $ss[] = '{"file":"' . self::mdcheck($s, $sub_files) . '","label":"' . $v["label"] . '","kind":"subtitles"' . ($v["default"] == 1 ? ',"default":true' : null) . '}';
            }
            $js .= (count($ss) > 0) ? implode(',', $ss) : null;
        }
        $js .= ']';

        return $js;
    }
    /* subtitles md5 check */
    public function mdcheck($key, $array)
    {
        global $cfg;

        foreach ($array as $v) {
            if ($key == md5($v)) {
                return $cfg["main_url"] . '/f_data/data_subtitles/' . $v;
            }
        }
        return;
    }

    /* adobe reader pdf embedding */
    public function DOCJS($section, $usr_key = '', $file_key = '', $is_hd = '', $next_file_key = '', $next_pl_key = '')
    {
        global $cfg, $class_database, $db, $language;

        $p   = self::playerInit($section);
        $_id = $p[0];
        $_w  = $p[1];
        $_h  = $p[2];

        $js       = '';
        $vuid     = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);
        $hpv      = $class_database->singleFieldValue('db_docfiles', 'has_preview', 'file_key', $file_key);
        $ofk      = $class_database->singleFieldValue('db_docfiles', 'old_file_key', 'file_key', $file_key);
        $usr_key  = $usr_key == '' ? '"+$(".thumb-entry-wrap-bg") . attr("rel-usr")+"' : $usr_key;
        $file_key = $file_key == '' ? '"+$(".thumb-entry-wrap-bg") . attr("rel-key")+"' : $file_key;
        $preview  = (($hpv == 1 and $cfg["conversion_doc_previews"] == 1) or $ofk) ? true : false;

        if ($preview) {
            if ($vuid == (int) $_SESSION["USER_ID"]) {
                $preview = false;
            } else {
                $ss = $db->execute(sprintf("SELECT`db_id`, `sub_list`FROM`db_subscriptions`WHERE`usr_id` = '%s'LIMIT1;", (int) $_SESSION["USER_ID"]));
                if ($ss->fields["db_id"]) {
                    $subs = unserialize($ss->fields["sub_list"]);
                    if (in_array($vuid, $subs)) {
                        $sq = sprintf("SELECT`db_id`FROM`db_subusers`WHERE`usr_id` = '%s' and `usr_id_to` = '%s' and `pk_id` > '0' and `expire_time` >= '%s'LIMIT1;", (int) $_SESSION["USER_ID"], $vuid, date("Y - m - dH:i:s"));
                        $sb = $db->execute($sq);
                        if ($sb->fields["db_id"]) {
                            $preview = false;
                        }

                    }
                }

                if (!$subbed) {
                    $ts = $db->execute(sprintf("SELECT`db_id`FROM`db_subtemps`WHERE`usr_id` = '%s' and `usr_id_to` = '%s' and `pk_id` > '0' and `expire_time` >= '%s' and `active` = '1'LIMIT1;", (int) $_SESSION["USER_ID"], $vuid, date("Y - m - dH:i:s")));

                    if ($ts->fields["db_id"]) {
                        $preview = false;
                    }
                }
            }
        }

        if (isset($_GET["section"]) and $_GET["section"] == 'backend' and isset($_GET["pv"])) {
            $preview = (bool) $_GET["pv"];
        }
        $gs  = !$preview ? md5($cfg["global_salt_key"] . $file_key) : $file_key;
        $src = VGenerate::thumbSigned(array("type" => "doc", "server" => "upload", "key" => '/' . $usr_key . '/d/' . $gs . '.pdf'), array($file_key, $gs), $usr_key, 0, 1);

        if (VHref::isMobile()) {
            $js .= '$("document").ready(function(){
            var url = "' . $src . '";
            pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.2.146/pdf.worker.min.js";

        let pdfViewer = new PDFjsViewer($(".pdfjs-viewer"), {
            onZoomChange:function (zoom) {
                zoom = parseInt(zoom * 10000) / 100;
                $(".zoomval").text(zoom+"%");
            },
            onActivePageChanged:function (page, pageno) {
                $(".pageno").text(pageno+"/"+this.getPageCount());
                pdfViewer.setZoom("fit");
            }
        });
        pdfViewer.loadDocument(url).then(function(){pdfViewer.setZoom("fit");pdfViewer.first();$(".pdfjs-viewer").css("height",($(".pdfpage").height()+32))});

        $("#pdfViewer-first").on("click", function(){pdfViewer.first()});
        $("#pdfViewer-prev").on("click", function(){pdfViewer.prev();return false});
        $("#pdfViewer-next").on("click", function(){pdfViewer.next();return false});
        $("#pdfViewer-last").on("click", function(){pdfViewer.last()});
        $("#pdfViewer-zoomOut").on("click", function(){pdfViewer.setZoom("out")});
        $("#pdfViewer-zoomIn").on("click", function(){pdfViewer.setZoom("in")});
        $("#pdfViewer-zoomWidth").on("click", function(){pdfViewer.setZoom("width")});
        $("#pdfViewer-zoomHeight").on("click", function(){pdfViewer.setZoom("height")});
        $("#pdfViewer-zoomFit").on("click", function(){pdfViewer.setZoom("fit")});
    });
';
            return $js;
        }

        $js .= '$(document).ready(function(){';
        $js .= '$("#' . $p[0] . '").html(\'<embed src="' . $src . '" width="100%" height="100%">\');';
        $js .= '});';

        return $js;
    }
    /* js for image viewing and slideshow */
    public function imageJS($section, $pl_key = '')
    {
        $js = '$(document).ready(function(){ $(".fancybox").fancybox({margin: 20}); $.fancybox.open({ minWidth: "70%", minHeight: "80%", margin: 20 }); });';

        return $js;
    }
}
