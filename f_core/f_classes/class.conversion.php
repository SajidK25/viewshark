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

define('PRESET', 'fast');
define('PRESET_PASS1', 'fast_firstpass');
define('PRESET_PASS2', 'fast');
define('FFMPEG_X264', true);
define('FFMPEG_V', ':v');
define('FFMPEG_CHROMA', '');
define('FFMPEG_PREVIEW', 30);
define('FFMPEG_SHORTS', 60);
define('FFMPEG_THREADS', 1);
define('FFMPEG_FAAC', 'aac -strict 2'); //or libfdk_aac

/* CONVERSION QUE */

class VQue
{
    private $cfg;
    private $db;

    public $type;

    public function __construct()
    {
        global $cfg, $class_database, $db;

        $this->type = 'video';
        $this->db   = $db;

        $rs = $this->db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%server_%' OR `cfg_name` LIKE '%thumb%' OR `cfg_name`='log_%';");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        $rs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        $this->cfg = $cfg;
    }

    public function __destruct()
    {}
    /* load file type */
    public function load($t)
    {
        $this->type = $t;

        return true;
    }
    /* check for running conversion processes */
    public function check()
    {
        $found = 0;

        $file_dl     = "download_" . $this->type . ".php";
        $file_conv   = "convert_" . $this->type . ".php";
        $file_xfer   = "transfer.php";
        $file_ffmpeg = "ffmpeg";

        $commands = array();

        exec("ps ax", $commands);

        if (count($commands) > 0) {
            foreach ($commands as $command) {
                if (strpos($command, $file_dl) === false and strpos($command, $file_conv) === false and strpos($command, $file_xfer) === false and strpos($command, $file_ffmpeg) === false) {
                    //what ?
                } else {
                    $found++;
                }
            }
        }
        if ($found > 0) {
            echo "Another process is running (" . $this->type . ").\n";
            return false;
        }
        /* regular process here */
        echo "Made it through all checks, conversion can start (" . $this->type . ").\n";
        return true;
    }
    /* start conversion if check succeeds */
    public function startConversion()
    {
        /* conversion state:
        0 = idle (waiting to start)
        1 = running
        2 = completed
         */
        $sql      = sprintf("SELECT A.`file_key`, A.`usr_key` FROM `db_%sque` A, `db_%sfiles` B WHERE A.`start_time`='0000-00-00 00:00:00' AND A.`end_time`='0000-00-00 00:00:00' AND A.`state`='0' AND A.`file_key`=B.`file_key` ORDER BY A.`q_id` ASC LIMIT 1;", $this->type, $this->type);
        $res      = $this->db->execute($sql);
        $file_key = $res->fields["file_key"];
        $usr_key  = $res->fields["usr_key"];

        if ($file_key != '' and $usr_key != '') {
            $convert  = 'convert_' . $this->type . '.php';
            $cmd      = $this->cfg["server_path_php"] . ' ' . $this->cfg["main_dir"] . '/f_modules/m_frontend/m_file/' . $convert . ' ' . $file_key . ' ' . $usr_key;
            $db_state = $this->db->execute(sprintf("UPDATE `db_%sque` SET start_time='%s', `state`='1' WHERE `file_key`='%s' AND `usr_key`='%s' AND `state`='0' LIMIT 1;", $this->type, date("Y-m-d H:i:s"), $file_key, $usr_key));

            exec(escapeshellcmd($cmd) . ' >/dev/null &');
        }
    }
    /* clear database cache */
    public function cache_clear()
    {
        $aged = 3;
        $dir  = $this->cfg['db_cache_dir'];
        $cmd = "find " . $dir . "/ -mtime +" . $aged . " | grep '.cache' | xargs rm -rf";

        exec(escapeshellcmd($cmd) . ' >/dev/null &');
    }
}

/* VIDEO CONVERSION */

class VVideo
{
    private $cfg;

    public $data      = array();
    public $log_clean = false;

    private $log = false;
    private $log_file;
    private $log_file_live;

    private $flv_formats = array(
        'flv', 'flv1', 'vp3', 'vp5', 'vp6', 'vp6a', 'vp6f',
    );

    public function __construct()
    {
        global $cfg, $class_database, $db;

        $cfg[] = $class_database->getConfigurations('log_video_conversion,thumbs_width,thumbs_height,thumbs_nr,paid_memberships');
        $rs    = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%server_%';");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }

        $cs = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_conversion`;");
        while (!$cs->EOF) {
            $cfg[$cs->fields["cfg_name"]] = $cs->fields["cfg_data"];
            @$cs->MoveNext();
        }

        $this->cfg = $cfg;
        $this->log = ($cfg["log_video_conversion"] == 1) ? true : false;
    }

    public function __destruct()
    {
        // we need to delete the log file here
        if ($this->log_clean === true) {
            VFile::delete($this->log_file);
        }
    }
    public function time_to_seconds($timecode)
    {
        $hours_end = strpos($timecode, ':', 0);
        $hours     = substr($timecode, 0, $hours_end);
        $mins      = substr($timecode, $hours_end + 1, 2);
        $secs      = trim(substr($timecode, $hours_end + 4));
        if ($secs == '') {
            $mins  = $hours;
            $secs  = $mins;
            $hours = 0;
        }
        return ($hours * 3600) + ($mins * 60) + $secs;
    }
    public function ffescape($file)
    {
        $s = array("$");
        $r = array("\\$");

        return str_replace($s, $r, $file);
    }

    public function load($file)
    {
        $cfg['server_path_ffprobe'] = $this->cfg["server_path_ffprobe"];
        if (!file_exists($file)) {
            return false;
        }
        $file = self::ffescape($file);

        // for some data ffmpeg cannot be trusted and mediainfo would be better, however
        // for what we need (actually width/height and format) its good enough (no need
        // for another dependency)
        $this->data = array();
        /* info detect */
        $this->data['cmd'] = sprintf("%s -print_format json -show_format -show_streams \"%s\"", $cfg['server_path_ffprobe'], $file);

        ob_start();
        passthru($this->data["cmd"]);
        $ffmpeg_output = ob_get_contents();
        ob_end_clean();
        $info = json_decode($ffmpeg_output, true);

        if ($info["streams"][0]["codec_type"] === 'video') {
            $vidx = 0;
            $aidx = $info["streams"][0]["codec_name"] == 'h263' ? 0 : 1;
        } elseif ($info["streams"][1]["codec_type"] === 'video') {
            $vidx = 1;
            $aidx = 0;
        } elseif ($info["streams"][2]["codec_type"] === 'video') {
            $vidx = 2;
            $aidx = 1;
        }
        if ($info["streams"][2]["codec_type"] === 'audio') {
            $aidx = 2;
        }

        $this->data["container"]        = $info["streams"][$vidx]["codec_long_name"];
        $this->data["duration_seconds"] = ($info["streams"][$vidx]["duration"] != '' ? $info["streams"][$vidx]["duration"] : ($info["streams"][$aidx]["duration"] != '' ? $info["streams"][$aidx]["duration"] : ($info["format"]["duration"] != '' ? $info["format"]["duration"] : 0)));
        $this->data["start"]            = $info["streams"][$vidx]["start_time"];
        $this->data["bitrate"]          = round(intval($info["format"]['bit_rate']) / 1000, 0);

        $r_frame_rate = $info["streams"][$vidx]['r_frame_rate'];
        $fr           = explode("/", $r_frame_rate);
        $r_frame_rate = floor(intval($fr[0]) / intval($fr[1]));

        $this->data["audio"]           = $info["streams"][$aidx]["codec_name"];
        $this->data["audio_frequency"] = ((int) $info["streams"][$aidx]["sample_rate"] > 0 ? $info["streams"][$aidx]["sample_rate"] : 44100);
        $this->data["audio_stereo"]    = $info["streams"][$aidx]["channel_layout"];
        $this->data["audio_bitrate"]   = floor(intval($info["streams"][$aidx]['bit_rate']) / 1000);

        if (isset($this->data['duration_seconds']) && isset($this->data['framerate'])) {
            $this->data['frames'] = ceil($this->data['duration_seconds'] * $this->data['framerate']);
        }

        $this->data["width"]  = $info["streams"][$vidx]["width"];
        $this->data["height"] = $info["streams"][$vidx]["height"];
        $this->data["rotate"] = 0;

        if (isset($info["streams"][$vidx]["tags"]["rotate"])) {
            $this->data["rotate"] = $info["streams"][$vidx]["tags"]["rotate"];
        } elseif (isset($info["streams"][$vidx]["side_data_list"][0]["rotation"])) {
            $this->data["rotate"] = $info["streams"][$vidx]["side_data_list"][0]["rotation"];
        }

        if ($this->data["rotate"] != 0) {
            $this->data["width"]  = $this->data["rotate"] == 180 ? $this->data["width"] : $info["streams"][$vidx]["height"];
            $this->data["height"] = $this->data["rotate"] == 180 ? $this->data["height"] : $info["streams"][$vidx]["width"];

            $this->data["src_width"]  = $info["streams"][$vidx]["width"];
            $this->data["src_height"] = $info["streams"][$vidx]["height"];
        }

        $rcmp = round($this->data['width'] / $this->data['height'], 4);

        if ($rcmp == round(16 / 9, 4)) {
            $this->data['dar']       = '16:9';
            $this->data['dar_float'] = 16 / 9;
        } elseif ($rcmp == round(16 / 10, 4)) {
            $this->data['dar']       = '16:10';
            $this->data['dar_float'] = 16 / 10;
        } elseif ($rcmp == round(37 / 20, 4)) {
            $this->data['dar']       = '37:20';
            $this->data['dar_float'] = 37 / 20;
        } elseif ($rcmp == round(21 / 9, 4)) {
            $this->data['dar']       = '21:9';
            $this->data['dar_float'] = 21 / 9;
        } elseif ($rcmp == round(11 / 5, 4)) {
            $this->data['dar']       = '11:5';
            $this->data['dar_float'] = 11 / 5;
        } elseif ($rcmp == round(47 / 20, 4)) {
            $this->data['dar']       = '47:20';
            $this->data['dar_float'] = 47 / 20;
        } elseif ($rcmp == round(5 / 4, 4)) {
            $this->data['dar']       = '5:4';
            $this->data['dar_float'] = 5 / 4;
        } elseif ($rcmp == round(5 / 3, 4)) {
            $this->data['dar']       = '5:3';
            $this->data['dar_float'] = 5 / 3;
        } elseif ($rcmp == round(128 / 75, 4)) {
            $this->data['dar']       = '128:75';
            $this->data['dar_float'] = 128 / 75;
        } elseif ($rcmp == round(180 / 320, 4)) {
            $this->data['dar']       = '180:320';
            $this->data['dar_float'] = 180 / 320;
        } elseif ($rcmp == round(320 / 132, 4)) {
            $this->data['dar']       = '320:132';
            $this->data['dar_float'] = 320 / 132;
        } elseif ($rcmp == round(320 / 360, 4)) {
            $this->data['dar']       = '320:360';
            $this->data['dar_float'] = 320 / 360;
        } elseif ($rcmp == round(320 / 120, 4)) {
            $this->data['dar']       = '320:120';
            $this->data['dar_float'] = 320 / 120;
        } elseif ($rcmp == round(640 / 180, 4)) {
            $this->data['dar']       = '640:180';
            $this->data['dar_float'] = 640 / 180;
        } elseif ($rcmp == round(240 / 160, 4)) {
            $this->data['dar']       = '240:160';
            $this->data['dar_float'] = 240 / 160;
        } elseif ($rcmp == round(160 / 200, 4)) {
            $this->data['dar']       = '160:200';
            $this->data['dar_float'] = 160 / 200;
        } elseif ($rcmp == round(68 / 120, 4)) {
            $this->data['dar']       = '68:120';
            $this->data['dar_float'] = 68 / 120;
        } elseif ($rcmp == round(88 / 160, 4)) {
            $this->data['dar']       = '88:160';
            $this->data['dar_float'] = 88 / 160;
        } elseif ($rcmp == round(60 / 80, 4)) {
            $this->data['dar']       = '60:80';
            $this->data['dar_float'] = 60 / 80;
        } elseif ($rcmp == round(212 / 120, 4)) {
            $this->data['dar']       = '212:120';
            $this->data['dar_float'] = 212 / 120;
        } elseif ($rcmp == round(120 / 212, 4)) {
            $this->data['dar']       = '120:212';
            $this->data['dar_float'] = 120 / 212;
        } elseif ($rcmp == round(72 / 88, 4)) {
            $this->data['dar']       = '72:88';
            $this->data['dar_float'] = 72 / 88;
        } elseif ($rcmp == round(180 / 100, 4)) {
            $this->data['dar']       = '180:100';
            $this->data['dar_float'] = 180 / 100;
        } elseif ($rcmp == round(80 / 142, 4)) {
            $this->data['dar']       = '80:142';
            $this->data['dar_float'] = 80 / 142;
        } elseif ($rcmp == round(142 / 80, 4)) {
            $this->data['dar']       = '142:80';
            $this->data['dar_float'] = 142 / 80;
        } elseif ($rcmp == round(160 / 94, 4)) {
            $this->data['dar']       = '160:94';
            $this->data['dar_float'] = 160 / 94;
        } elseif ($rcmp == round(80 / 106, 4)) {
            $this->data['dar']       = '80:106';
            $this->data['dar_float'] = 80 / 106;
        } elseif ($rcmp == round(100 / 56, 4)) {
            $this->data['dar']       = '100:56';
            $this->data['dar_float'] = 100 / 56;
        } elseif ($rcmp == round(427 / 240, 4)) {
            $this->data['dar']       = '427:240';
            $this->data['dar_float'] = 427 / 240;
        } elseif ($rcmp == round(4 / 3, 4)) {
            $this->data['dar']       = '4:3';
            $this->data['dar_float'] = 4 / 3;
        } elseif ($rcmp == round(2 / 1, 4)) {
            $this->data['dar']       = '2:1';
            $this->data['dar_float'] = 2 / 1;
        } elseif ($rcmp == round(1 / 1, 4)) {
            $this->data['dar']       = '1:1';
            $this->data['dar_float'] = 1 / 1;
        } else {
            $this->ratio($this->data['width'], $this->data['height']);
        }

        if ($info['streams'][$vidx]['side_data_list'][0]['rotation'] == -90) {
            $nd                = explode(':', $this->data['dar']);
            $this->data['dar'] = $nd[1] . ':' . $nd[0];
        }

        return true;
    }

    public function get_data_string()
    {
        $string = null;
        foreach ($this->data as $key => $value) {
            $string .= $key . ' => ' . $value . "\n";
        }

        return $string;
    }

    public function get_data()
    {
        return $this->data;
    }
    public function ratio($a, $b)
    {
        $gcd = function ($a, $b) use (&$gcd) {
            return ($a % $b) ? $gcd($b, $a % $b) : $b;
        };

        $g                       = $gcd($a, $b);
        $this->data['dar']       = $a / $g . ':' . $b / $g;
        $f                       = ($a / $g) / ($b / $g);
        $this->data['dar_float'] = $f;
    }

    public function get_dimensions($resize_width, $resize_height, $force = false)
    {
        $width  = $this->data['width'];
        $height = $this->data['height'];

        // width/height lower than the resize dimenstions...keep video size (some codecs wont keep aspect ratio)
        if ($width <= $resize_width && $height <= $resize_height) {
            $new_width  = $width;
            $new_height = $height;
            // reduce width
        } elseif ($resize_width / $width <= $resize_height / $height) {
            $new_width  = $resize_width;
            $new_height = round($height * $resize_width / $width);
            // reduce height
        } else {
            $new_width  = round($width * $resize_height / $height);
            $new_height = $resize_height;
        }

        if ($new_width > $resize_width or $force !== false) {
            return array(
                'width'  => round($new_width / 16) * 16,
                'height' => round($new_height / 16) * 16,
            );
        }

        // most codecs require size to be even
        return array(
            'width'  => round($new_width / 2) * 2,
            'height' => round($new_height / 2) * 2,
        );
    }

    public function get_bitrate($width, $height, $bitrate, $fix = false, $cv = '360p')
    {
        if ($fix !== false) {
            return $bitrate;
        }

        //need to implement bitrate calculations for FLV
        $area        = $width * $height;
        $resize_area = $this->cfg['conversion_mp4_' . $cv . '_resize_w'] * $this->cfg['conversion_mp4_' . $cv . '_resize_h'];

        return round((1 / 2 * $area / $resize_area + 1 / 2 * sqrt($area / $resize_area)) * $bitrate);
    }

    public function get_mobile_bitrate($width, $height, $bitrate)
    {
        $area        = $width * $height;
        $resize_area = $this->cfg['conversion_mp4_ipad_resize_w'] * $this->cfg['conversion_mp4_ipad_resize_h'];

        return round((1 / 2 * $area / $resize_area + 1 / 2 * sqrt($area / $resize_area)) * $bitrate);
    }

    public function get_aspect()
    {
        return $this->data['dar'];
    }

    public function convert_to_flv($src, $dst, $short = false, $cv = '360p')
    {
        $function = 'convert_to_flv_ffmpeg';
        return $this->$function($src, $dst, $short, $cv);
    }

    public function convert_to_flv_ffmpeg($src, $dst, $short, $cv = '360p')
    {
//deprecated
        return;

        if (!file_exists($this->cfg['server_path_ffmpeg'])) {
            return false;
        }
        $src = self::ffescape($src);

        $options = '-acodec libmp3lame -ar ' . $this->cfg['conversion_flv_' . $cv . '_srate_audio'] . ' -ab ' . $this->cfg['conversion_flv_' . $cv . '_bitrate_audio'] . 'k';
        if ($this->cfg['conversion_flv_' . $cv . '_resize'] == 1) {
            $dimensions = $this->get_dimensions($this->cfg['conversion_flv_' . $cv . '_resize_w'], $this->cfg['conversion_flv_' . $cv . '_resize_h']);
            $aspect     = $this->get_aspect();
            $options .= ' -aspect ' . $aspect . ' -s ' . $dimensions['width'] . 'x' . $dimensions['height'];
        }

        if ($this->cfg['conversion_flv_' . $cv . '_bitrate_mt'] == 'fixed') {
            $options .= ' -b' . FFMPEG_V . ' ' . $this->cfg['conversion_flv_' . $cv . '_bitrate_video'] . 'k';
        } else {
            $bitrate = $this->get_bitrate($dimensions['width'], $dimensions['height'], $this->cfg['conversion_flv_' . $cv . '_bitrate_video'], true);
            $options .= ' -b' . FFMPEG_V . ' ' . $bitrate . 'k';
        }

        $cmd = sprintf("%s -i \"%s\" -y -f flv %s \"%s\"", $this->cfg['server_path_ffmpeg'], $src, $options, $dst);
        $this->log($cmd);
        exec($cmd . ' 2>&1', $output);
        $this->log(implode("\n", $output));

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            return true;
        }

        return false;
    }

    public function convert_to_flv_mencoder($src, $dst)
    {
    }

    public function convert_to_mp4($src, $dst, $short = false, $cv = '360p')
    {
        $this->cfg['conversion_mp4_360p_resize_w_min'] = 100; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_360p_resize_h_min'] = 100; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_mp4_480p_resize_w_min'] = 600; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_480p_resize_h_min'] = 400; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_mp4_720p_resize_w_min'] = 800; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_720p_resize_h_min'] = 600; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_mp4_1080p_resize_w_min'] = 1200; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_1080p_resize_h_min'] = 800; // MIN RESIZE HEIGHT - HD

        if ($this->data['width'] < $this->cfg['conversion_mp4_' . $cv . '_resize_w_min'] or
            $this->data['height'] < $this->cfg['conversion_mp4_' . $cv . '_resize_h_min']) {
            $this->log('Not converting to H264 (' . $this->data['format'] . ':' . $this->data['width'] . 'x' . $this->data['height'] . ')!');
            return false;
        }

        $function = 'convert_to_mp4_ffmpeg';
        return $this->$function($src, $dst, $short, $cv);
    }
    public function convert_to_mp4_short($src, $dst, $short = false, $cv = 'short')
    {
        $this->cfg['conversion_mp4_360p_resize_w_min'] = 100; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_360p_resize_h_min'] = 100; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_mp4_480p_resize_w_min'] = 600; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_480p_resize_h_min'] = 400; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_mp4_720p_resize_w_min'] = 800; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_720p_resize_h_min'] = 600; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_mp4_1080p_resize_w_min'] = 1200; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_mp4_1080p_resize_h_min'] = 800; // MIN RESIZE HEIGHT - HD

        $function = 'convert_to_mp4_short_ffmpeg';
        return $this->$function($src, $dst, $short, $cv);
    }
    public function convert_to_vpx($src, $dst, $short = false, $cv = '360p')
    {
        $this->cfg['conversion_vpx_360p_resize_w_min'] = 100; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_vpx_360p_resize_h_min'] = 100; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_vpx_480p_resize_w_min'] = 600; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_vpx_480p_resize_h_min'] = 400; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_vpx_720p_resize_w_min'] = 800; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_vpx_720p_resize_h_min'] = 600; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_vpx_1080p_resize_w_min'] = 1200; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_vpx_1080p_resize_h_min'] = 800; // MIN RESIZE HEIGHT - HD

        if ($this->data['width'] < $this->cfg['conversion_vpx_' . $cv . '_resize_w_min'] or
            $this->data['height'] < $this->cfg['conversion_vpx_' . $cv . '_resize_h_min']) {

            $this->log('Not converting to WEBM/' . $cv . ' (' . $this->data['format'] . ':' . $this->data['width'] . 'x' . $this->data['height'] . ')!');
            return false;
        }

        $function = 'convert_to_vpx_ffmpeg';
        return $this->$function($src, $dst, $short, $cv);
    }

    public function convert_to_ogv($src, $dst, $short = false, $cv = '360p')
    {
        $this->cfg['conversion_ogv_360p_resize_w_min'] = 100; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_ogv_360p_resize_h_min'] = 100; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_ogv_480p_resize_w_min'] = 600; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_ogv_480p_resize_h_min'] = 400; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_ogv_720p_resize_w_min'] = 800; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_ogv_720p_resize_h_min'] = 600; // MIN RESIZE HEIGHT - HD

        $this->cfg['conversion_ogv_1080p_resize_w_min'] = 1200; // MIN RESIZE WIDTH - HD
        $this->cfg['conversion_ogv_1080p_resize_h_min'] = 800; // MIN RESIZE HEIGHT - HD

        if ($this->data['width'] < $this->cfg['conversion_ogv_' . $cv . '_resize_w_min'] or
            $this->data['height'] < $this->cfg['conversion_ogv_' . $cv . '_resize_h_min']) {

            $this->log('Not converting to OGV/' . $cv . ' (' . $this->data['format'] . ':' . $this->data['width'] . 'x' . $this->data['height'] . ')!');
            return false;
        }

        $function = 'convert_to_ogv_ffmpeg';
        return $this->$function($src, $dst, $short, $cv);
    }

    public function convert_to_mp4_short_ffmpeg($src, $dst, $short, $cv = 'short')
    {
        if (!file_exists($this->cfg['server_path_ffmpeg'])) {
            return false;
        }
        $options = '-t ' . FFMPEG_SHORTS . ' -keyint_min 25 -threads ' . FFMPEG_THREADS . ' -pix_fmt yuv420p';

        $src = self::ffescape($src);
        $cmd = sprintf("%s -y -i %s %s -acodec %s -vcodec libx264 -crf 3 -vf \"crop=ih*(9/16):ih\" %s 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, FFMPEG_FAAC, $dst, $this->log_file_live);

        $output = array();
        $this->log($cmd);
        exec($cmd);
        $lines = file($this->log_file_live);
        foreach ($lines as $line_num => $line) {
            $output[] = $line;
        }
        $this->log(implode("", $output));

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            return true;
        }

        return false;
    }
    public function convert_to_mp4_ffmpeg($src, $dst, $short, $cv = '360p')
    {
        if (!file_exists($this->cfg['server_path_ffmpeg'])) {
            return false;
        }
        $src = self::ffescape($src);

        $options = '-keyint_min 25 -vcodec libx264 -threads ' . FFMPEG_THREADS . ' -pix_fmt yuv420p';
        $options_audio = ' -acodec ' . FFMPEG_FAAC . ' -ab ' . ($this->cfg['conversion_mp4_' . $cv . '_bitrate_audio'] > $this->data['audio_bitrate'] ? $this->data['audio_bitrate'] : $this->cfg['conversion_mp4_' . $cv . '_bitrate_audio']) . 'k -ar ' . $this->cfg['conversion_mp4_' . $cv . '_srate_audio'];

        $hd_bitrate        = $this->cfg['conversion_mp4_' . $cv . '_bitrate_video'];
        $hd_bitrate_method = $this->cfg['conversion_mp4_' . $cv . '_bitrate_mt'];
        if ($this->cfg['conversion_mp4_' . $cv . '_encoding'] == 2) {
            $hd_bitrate_method = ($hd_bitrate_method == 'fixed') ? 'fixed' : 'auto';
        }

        if ($this->cfg['conversion_mp4_' . $cv . '_resize'] == 1) {
            $dimensions = $this->get_dimensions($this->cfg['conversion_mp4_' . $cv . '_resize_w'], $this->cfg['conversion_mp4_' . $cv . '_resize_h']);
            $aspect     = $this->get_aspect();
            $options .= ' -aspect ' . $aspect . ' -s ' . $dimensions['width'] . 'x' . $dimensions['height'];
        }

        if ($hd_bitrate_method == 'fixed') {
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $hd_bitrate ? $hd_bitrate : $this->data['bitrate']) . 'k';
        } elseif ($hd_bitrate_method == 'auto') {
            $bitrate = $this->get_bitrate($this->data['width'], $this->data['height'], $this->data['bitrate'], false, $cv);
            $bitrate = $bitrate > $hd_bitrate ? $hd_bitrate : $bitrate;
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $bitrate ? $bitrate : $this->data['bitrate']) . 'k';
        } else {
            $options .= ' -crf 22';
        }

        if (isset($this->data['crop'])) {
            $options .= sprintf(" -vf \"%s\"", $this->data['crop']);
        }

        if ($short === true and $this->data['duration_seconds'] > FFMPEG_PREVIEW) {
            $options .= ' -t ' . FFMPEG_PREVIEW;
        }
        $profile = (FFMPEG_X264 === true) ? ' -profile' . FFMPEG_V . ' high -preset ' . PRESET : ' -vpre ' . PRESET;

        if ($this->cfg['conversion_mp4_' . $cv . '_encoding'] == 1) {
            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
        } else {
            $output = array();
            $rnd    = VUserinfo::generateRandomString(5);
            $pl     = $this->cfg["logging_dir"] . '/log_conv/' . date("Y.m.d") . '/ffmpegpass-' . $rnd;

            $cmd = sprintf("%s -i \"%s\" -y %s%s -acodec " . FFMPEG_FAAC . " -pass 1 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);

            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));

            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s -pass 2 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
            exec(sprintf("rm -rf %s*", $pl));
        }

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            return true;
        }

        return false;
    }
    public function convert_to_vpx_ffmpeg($src, $dst, $short = '', $cv = '360p')
    {
        if (!file_exists($this->cfg['server_path_ffmpeg'])) {
            return false;
        }
        $src = self::ffescape($src);

        $options       = '-keyint_min 25 -pix_fmt yuv420p';
        $options_audio = ' -acodec libvorbis -ab ' . ($this->cfg['conversion_vpx_' . $cv . '_bitrate_audio'] > $this->data['audio_bitrate'] ? $this->data['audio_bitrate'] : $this->cfg['conversion_vpx_' . $cv . '_bitrate_audio']) . 'k -ar ' . $this->cfg['conversion_vpx_' . $cv . '_srate_audio'];

        $hd_bitrate        = $this->cfg['conversion_vpx_' . $cv . '_bitrate_video'];
        $hd_bitrate_method = $this->cfg['conversion_vpx_' . $cv . '_bitrate_mt'];

        if ($this->cfg['conversion_vpx_' . $cv . '_encoding'] == 2) {
            $hd_bitrate_method = ($hd_bitrate_method == 'fixed') ? 'fixed' : 'auto';
        }

        if ($this->cfg['conversion_vpx_' . $cv . '_resize'] == 1) {
            $dimensions = $this->get_dimensions($this->cfg['conversion_vpx_' . $cv . '_resize_w'], $this->cfg['conversion_vpx_' . $cv . '_resize_h']);
            $aspect     = $this->get_aspect();
            $options .= ' -aspect ' . $aspect . ' -s ' . $dimensions['width'] . 'x' . $dimensions['height'];
        }

        if ($hd_bitrate_method == 'fixed') {
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $hd_bitrate ? $hd_bitrate : $this->data['bitrate']) . 'k';
        } elseif ($hd_bitrate_method == 'auto') {
            $bitrate = $this->get_bitrate($this->data['width'], $this->data['height'], $this->data['bitrate'], false, $cv);
            $bitrate = $bitrate > $hd_bitrate ? $hd_bitrate : $bitrate;
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $bitrate ? $bitrate : $this->data['bitrate']) . 'k';
        } else {
            $options .= ' -crf 22';
        }

        if ($short === true and $this->data['duration_seconds'] > FFMPEG_PREVIEW) {
            $options .= ' -t ' . FFMPEG_PREVIEW;
        }
        $profile = null;

        if ($this->cfg['conversion_vpx_' . $cv . '_encoding'] == 1) {
            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
        } else {
            $output = array();
            $rnd    = VUserinfo::generateRandomString(5);
            $pl     = $this->cfg["logging_dir"] . '/log_conv/' . date("Y.m.d") . '/ffmpegpass-' . $rnd;

            $cmd = sprintf("%s -i \"%s\" -y %s%s -pass 1 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s -pass 2 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
            exec(sprintf("rm -rf %s*", $pl));
        }

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            return true;
        }

        return false;
    }

    public function convert_to_ogv_ffmpeg($src, $dst, $short = '', $cv = '360p')
    {
        if (!file_exists($this->cfg['server_path_ffmpeg'])) {
            return false;
        }
        $src = self::ffescape($src);

        $options       = '-keyint_min 25 -pix_fmt yuv420p';
        $options_audio = ' -acodec libvorbis -ab ' . ($this->cfg['conversion_ogv_' . $cv . '_bitrate_audio'] > $this->data['audio_bitrate'] ? $this->data['audio_bitrate'] : $this->cfg['conversion_ogv_' . $cv . '_bitrate_audio']) . 'k -ar ' . $this->cfg['conversion_ogv_' . $cv . '_srate_audio'];

        $hd_bitrate        = $this->cfg['conversion_ogv_' . $cv . '_bitrate_video'];
        $hd_bitrate_method = $this->cfg['conversion_ogv_' . $cv . '_bitrate_mt'];

        if ($this->cfg['conversion_ogv_' . $cv . '_encoding'] == 2) {
            $hd_bitrate_method = ($hd_bitrate_method == 'fixed') ? 'fixed' : 'auto';
        }

        if ($this->cfg['conversion_ogv_' . $cv . '_resize'] == 1) {
            $dimensions = $this->get_dimensions($this->cfg['conversion_ogv_' . $cv . '_resize_w'], $this->cfg['conversion_ogv_' . $cv . '_resize_h']);
            $aspect     = $this->get_aspect();
            $options .= ' -aspect ' . $aspect . ' -s ' . $dimensions['width'] . 'x' . $dimensions['height'];
        }

        if ($hd_bitrate_method == 'fixed') {
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $hd_bitrate ? $hd_bitrate : $this->data['bitrate']) . 'k';
        } elseif ($hd_bitrate_method == 'auto') {
            $bitrate = $this->get_bitrate($this->data['width'], $this->data['height'], $this->data['bitrate'], false, $cv);
            $bitrate = $bitrate > $hd_bitrate ? $hd_bitrate : $bitrate;
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $bitrate ? $bitrate : $this->data['bitrate']) . 'k';
        } else {
            $options .= ' -crf 22';
        }

        if ($short === true and $this->data['duration_seconds'] > FFMPEG_PREVIEW) {
            $options .= ' -t ' . FFMPEG_PREVIEW;
        }
        $profile = null;

        if ($this->cfg['conversion_ogv_' . $cv . '_encoding'] == 1) {
            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
        } else {
            $output = array();
            $rnd    = VUserinfo::generateRandomString(5);
            $pl     = $this->cfg["logging_dir"] . '/log_conv/' . date("Y.m.d") . '/ffmpegpass-' . $rnd;

            $cmd = sprintf("%s -i \"%s\" -y %s%s -pass 1 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s -pass 2 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
            exec(sprintf("rm -rf %s*", $pl));
        }

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            return true;
        }

        return false;
    }

    public function convert_to_mp4_mencoder($src, $dst)
    {
    }

    public function convert_to_mp4_handbrake($src, $dst)
    {
    }

    public function convert_to_ipod($src, $dst)
    {
    }

    public function convert_to_mobile($src, $dst, $short = false)
    {
        $this->cfg['conversion_mp4_ipad_resize_w_min'] = 100; // MIN RESIZE WIDTH - MOBILE
        $this->cfg['conversion_mp4_ipad_resize_h_min'] = 100; // MIN RESIZE HEIGHT - MOBILE

        if ($this->data['width'] < $this->cfg['conversion_mp4_ipad_resize_w_min'] or
            $this->data['height'] < $this->cfg['conversion_mp4_ipad_resize_h_min']) {
            $this->log('Not converting to Mobile MP4 (' . $this->data['format'] . ':' . $this->data['width'] . 'x' . $this->data['height'] . ')!');
            return false;
        }

        $function = 'convert_to_mobile_ffmpeg';
        return $this->$function($src, $dst, $short);
    }

    public function convert_to_mobile_ffmpeg($src, $dst, $short)
    {
        if (!file_exists($this->cfg['server_path_ffmpeg'])) {
            return false;
        }
        $src = self::ffescape($src);

        $options = ' -y -vcodec libx264 -threads ' . FFMPEG_THREADS . ' -pix_fmt yuv420p';

        if ($short === true and $this->data['duration_seconds'] > FFMPEG_PREVIEW) {
            $options .= ' -t ' . FFMPEG_PREVIEW;
        }

        $options_audio = ' -acodec ' . FFMPEG_FAAC . ' -ac 2 -ab ' . ($this->cfg['conversion_mp4_ipad_bitrate_audio'] > $this->data['audio_bitrate'] ? $this->data['audio_bitrate'] : $this->cfg['conversion_mp4_ipad_bitrate_audio']) . 'k -ar ' . $this->cfg['conversion_mp4_ipad_srate_audio'];

        if ($this->cfg['conversion_mp4_ipad_resize'] == 1) {
            $dimensions = $this->get_dimensions($this->cfg['conversion_mp4_ipad_resize_w'], $this->cfg['conversion_mp4_ipad_resize_h'], true);
            $aspect     = $this->get_aspect();
            $options .= ' -aspect ' . $aspect . ' -s ' . $dimensions['width'] . 'x' . $dimensions['height'];
            if ($dimensions['width'] > 320) {
                $ipod_preset = 'ipod640';
            } elseif ($dimensions['width'] <= 320) {
                $ipod_preset = 'ipod320';
            }
        } else {
            $ipod_preset = 'ipod640';
        }

        $options .= " -flags +loop -cmp " . FFMPEG_CHROMA . "chroma -partitions 0 -subq 1 -trellis 0 -refs 1 -coder 0 -me_range 16 -g 300 -keyint_min 25 -sc_threshold 40 -i_qfactor 0.71 -maxrate 10M -bufsize 10M -rc_eq 'blurCplx^(1-qComp)' -qcomp 0.6 -qmin 10 -qmax 51 -qdiff 4 -f mp4";

        if ($this->cfg['conversion_mp4_ipad_bitrate_mt'] == 'auto') {
            $bitrate = $this->get_mobile_bitrate($this->cfg['conversion_mp4_ipad_resize_w'], $this->cfg['conversion_mp4_ipad_resize_h'], $this->cfg['conversion_mp4_ipad_bitrate_video']);
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->data['bitrate'] > $bitrate ? $bitrate : $this->data['bitrate']) . 'k';
        } else {
            $options .= ' -b' . FFMPEG_V . ' ' . ($this->cfg['conversion_mp4_ipad_bitrate_video'] > $this->data['bitrate'] ? $this->data['bitrate'] : $this->cfg['conversion_mp4_ipad_bitrate_video']) . 'k';
        }

        $profile = (FFMPEG_X264 === true) ? ' -profile' . FFMPEG_V . ' baseline -level 1.3 -preset ' . PRESET : ' -vpre ' . PRESET . ' -vpre ' . $ipod_preset . ' -f ipod';

        if ($this->cfg['conversion_mp4_ipad_encoding'] == 1) {
            $output = array();
            $cmd    = sprintf("%s -i \"%s\"%s%s%s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));
        } else {
            $output = array();
            $rnd    = VUserinfo::generateRandomString(5);
            $pl     = $this->cfg["logging_dir"] . '/log_conv/' . date("Y.m.d") . '/ffmpegpass-' . $rnd;

            $cmd = sprintf("%s -i \"%s\" -y %s%s -acodec " . FFMPEG_FAAC . " -pass 1 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));

            $output = array();
            $cmd    = sprintf("%s -i \"%s\" -y %s%s%s -pass 2 -passlogfile %s \"%s\" 1> %s 2>&1", $this->cfg['server_path_ffmpeg'], $src, $options, $options_audio, $profile, $pl, $dst, $this->log_file_live);
            $this->log($cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("", $output));

            exec(sprintf("rm -rf %s*", $pl));
        }

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            return true;
        }

        return false;
    }

    public function convert_to_mobile_mencoder($src, $dst)
    {
    }

    public function convert_to_mobile_handbrake($src, $dst)
    {
    }
    public function extract_preview_thumbs_short($file, $video_id, $user_key, $full_thumbs = false)
    {
        $ss = false;
        $tt = false;

        $this->data['duration_seconds'] = $this->data['duration_seconds'] >= 60 ? 60 : $this->data['duration_seconds'];

        if ($full_thumbs and $this->data['duration_seconds'] >= 9) {
            $ss = 5;
            $tt = 3;
        } elseif ($full_thumbs and $this->data['duration_seconds'] < 9 and $this->data['duration_seconds'] >= 5) {
            $ss = 3;
            $tt = 3;
        } elseif ($full_thumbs and $this->data['duration_seconds'] < 5 and $this->data['duration_seconds'] >= 1) {
            $ss = 1;
            $tt = 3;
        } elseif ($full_thumbs and $this->data['duration_seconds'] <= 2) {
            $ss = 1;
            $tt = 1;
        }
        if ($this->data["rotate"] == 90 or $this->data["rotate"] == -90) {
            $f = 'hflip,';
        } elseif ($this->data["rotate"] == 180) {
            $f = 'vflip,hflip,';
        } elseif ($this->data["rotate"] == 270) {
            $f = 'hflip,hflip,';
        } else {
            $f = '';
        }

        if ($full_thumbs and $ss and $tt) {
            $size = array(360, 640);
            $out  = $this->cfg["media_files_dir"] . "/" . $user_key . "/s/" . md5($video_id . "_preview") . ".mp4";
            if ($full_thumbs == 2 and file_exists($out)) {
                unlink($out);
            }

            $cmd = sprintf("%s -ss %s -t %s -i %s -y -an -filter_complex \"%sscale=iw*min(%s/iw\,%s/ih):ih*min(%s/iw\,%s/ih),pad=%s:%s:(%s-iw)/2:(%s-ih)/2\" %s", $this->cfg['server_path_ffmpeg'], $ss, $tt, $file, $f, $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $out);
            $this->log("\nGenerating thumbnail preview short video\n\n");
            $this->log($cmd);
            $this->log("\n");
            exec($cmd . ' 2>&1', $output);
            $this->log(implode("\n", $output));

            if ($full_thumbs == 2) {
                return;
            }
        }
    }

    public function extract_preview_thumbs($file, $video_id, $user_key, $full_thumbs = false)
    {
        $ss = false;
        $tt = false;

        if ($full_thumbs and $this->data['duration_seconds'] >= 9) {
            $ss = 5;
            $tt = 3;
        } elseif ($full_thumbs and $this->data['duration_seconds'] < 9 and $this->data['duration_seconds'] >= 5) {
            $ss = 3;
            $tt = 3;
        } elseif ($full_thumbs and $this->data['duration_seconds'] < 5 and $this->data['duration_seconds'] >= 1) {
            $ss = 1;
            $tt = 3;
        } elseif ($full_thumbs and $this->data['duration_seconds'] <= 2) {
            $ss = 1;
            $tt = 1;
        }
        if ($this->data["rotate"] == 90 or $this->data["rotate"] == -90) {
            $f = 'hflip,';
        } elseif ($this->data["rotate"] == 180) {
            $f = 'vflip,hflip,';
        } elseif ($this->data["rotate"] == 270) {
            $f = 'hflip,hflip,';
        } else {
            $f = '';
        }

        if ($full_thumbs and $ss and $tt) {
            $size = array(640, 360);
            $out  = $this->cfg["media_files_dir"] . "/" . $user_key . "/v/" . md5($video_id . "_preview") . ".mp4";
            if ($full_thumbs == 2 and file_exists($out)) {
                unlink($out);
            }

            $cmd = sprintf("%s -ss %s -t %s -i %s -y -an -filter_complex \"%sscale=iw*min(%s/iw\,%s/ih):ih*min(%s/iw\,%s/ih),pad=%s:%s:(%s-iw)/2:(%s-ih)/2\" %s", $this->cfg['server_path_ffmpeg'], $ss, $tt, $file, $f, $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $out);
            $this->log("\nGenerating thumbnail preview video\n\n");
            $this->log($cmd);
            $this->log("\n");
            exec($cmd . ' 2>&1', $output);
            $this->log(implode("\n", $output));

            if ($full_thumbs == 2) {
                return;
            }
        }
        $interval = 3;
        $file     = self::ffescape($file);
        $script   = $this->cfg["main_dir"] . "/f_modules/m_frontend/m_file/thumbnail_preview.php";
        $output   = $this->cfg["media_files_dir"] . "/" . $user_key . "/t/" . $video_id . "/p/" . ($full_thumbs ? md5($this->cfg["global_salt_key"] . $video_id) . "/" : null);

        $cmd = sprintf("%s %s -i \"%s\" -o \"%s\" -t %s", $this->cfg['server_path_php'], $script, $file, $output, $interval);

        if (!is_dir($output)) {
            mkdir($output, 0777, true);
        }

        $this->log("\nGenerating player preview thumbnails\n\n");
        $this->log($cmd);
        $this->log("\n");
        exec($cmd . ' 2>&1', $output);
        $this->log(implode("\n", $output));
    }

    public function extract_thumbs_short($file, $video_id, $user_key, $frames = array(), $plus = 0)
    {
        global $db;

        $this->data['duration_seconds'] = $this->data['duration_seconds'] >= 60 ? 60 : $this->data['duration_seconds'];

        if (is_array($file)) {
            $file  = $file[0];
            $thumb = 1;
        }
        if (!file_exists($file) or !is_file($file)) {
            return false;
        }
        $file = self::ffescape($file);

        if (!$frames) {
            $frames = $thumb == 1 ? $this->generate_frames($this->data['duration_seconds'], 1) : $this->generate_frames($this->data['duration_seconds']);
        }

        $dst_dir = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/';
        $tmp_dir = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/out/';

        if (!VFileinfo::createFolder($dst_dir) or
            !VFileinfo::createFolder($tmp_dir)) {
            return false;
        }

        $cfg["thumbs_width"]  = 320;
        $cfg["thumbs_height"] = 180;
        $tcache               = VFiles::thumb_cache('short', $video_id, $plus);
        $i                    = ($thumb == 1 ? 0 : 1) . $tcache;
        $size                 = $thumb == 1 ? array(360, 640) : array(180, 320);

        foreach ($frames as $key => $seconds) {
            $dst = $dst_dir . $i . '.jpg';
            $st  = (($key == 0 and $this->cfg["thumbs_method"] == 'rand') ? str_replace(',', '.', $_SESSION["fr"]) : str_replace(',', '.', $seconds));
            $st  = $st >= $seconds ? round($seconds / 2) : $st;
            $st  = ($st <= 2 or $st == 0) ? "0.5" : $st;
            if ($this->data["rotate"] == 90 or $this->data["rotate"] == -90) {
                $f = ' -vf hflip';
            } elseif ($this->data["rotate"] == 180) {
                $f = ' -vf vflip,hflip';
            } elseif ($this->data["rotate"] == 270) {
                $f = ' -vf hflip,hflip';
            } else {
                $f = null;
            }

            if ((int) $this->data["rotate"] > 0 and $this->data["height"] > $this->data["width"]) {

            } else {

            }

            $cmd = sprintf("%s -ss %s -itsoffset -1 -i \"%s\" -vframes 1 %s -filter%s \"scale=iw*min(%s/iw\,%s/ih):ih*min(%s/iw\,%s/ih),pad=%s:%s:(%s-iw)/2:(%s-ih)/2\" -y \"%s\"",
                $this->cfg['server_path_ffmpeg'], $st, $file, $f, FFMPEG_V, $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $tmp_dir . '%03d.jpg');

            $this->log($cmd);
            exec($cmd . ' 2>&1', $output);
            $this->log(implode("\n", $output));

            if (file_exists($tmp_dir . '002.jpg')) {
                $src = $tmp_dir . '002.jpg';
            } elseif (file_exists($tmp_dir . '001.jpg')) {
                $src = $tmp_dir . '001.jpg';
            } else {
                $this->log('Failed to extract thumb!');
                continue;
            }

            if (isset($src)) {
                copy($src, $dst);

                $this->log("\n" . 'Copying thumb ' . $src . ' -> ' . $dst . '!' . "\n");

                if (file_exists($dst) && filesize($dst) > 100 and $thumb != 1) {
                    VFileinfo::doDelete($src);
                    ++$i;
                }
            }
        }

        VFileinfo::doDelete($tmp_dir);

        return ($i == 1) ? 0 : ($i - 1);
    }

    public function extract_thumbs($file, $video_id, $user_key, $frames = array(), $plus = 0)
    {
        global $db;

        if (is_array($file)) {
            $file  = $file[0];
            $thumb = 1;
        }
        if (!file_exists($file) or !is_file($file)) {
            return false;
        }
        $file = self::ffescape($file);

        if (!$frames) {
            $frames = $thumb == 1 ? $this->generate_frames($this->data['duration_seconds'], 1) : $this->generate_frames($this->data['duration_seconds']);
        }

        $dst_dir = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/';
        $tmp_dir = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $video_id . '/out/';

        if (!VFileinfo::createFolder($dst_dir) or
            !VFileinfo::createFolder($tmp_dir)) {
            return false;
        }

        $cfg["thumbs_width"]  = 320;
        $cfg["thumbs_height"] = 180;
        $tcache               = VFiles::thumb_cache('video', $video_id, $plus);
        $i                    = ($thumb == 1 ? 0 : 1) . $tcache;
        $size                 = $thumb == 1 ? array(640, 360) : array(320, 180);

        foreach ($frames as $key => $seconds) {
            $dst = $dst_dir . $i . '.jpg';
            $st  = (($key == 0 and $this->cfg["thumbs_method"] == 'rand') ? str_replace(',', '.', $_SESSION["fr"]) : str_replace(',', '.', $seconds));
            $st  = $st >= $seconds ? round($seconds / 2) : $st;
            $st  = ($st <= 2 or $st == 0) ? "0.5" : $st;
            if ($this->data["rotate"] == 90 or $this->data["rotate"] == -90) {
                $f = ' -vf hflip';
            } elseif ($this->data["rotate"] == 180) {
                $f = ' -vf vflip,hflip';
            } elseif ($this->data["rotate"] == 270) {
                $f = ' -vf hflip,hflip';
            } else {
                $f = null;
            }

            if ((int) $this->data["rotate"] > 0 and $this->data["height"] > $this->data["width"]) {

            } else {

            }

            $cmd = sprintf("%s -ss %s -itsoffset -1 -i \"%s\" -vframes 1 %s -filter%s \"scale=iw*min(%s/iw\,%s/ih):ih*min(%s/iw\,%s/ih),pad=%s:%s:(%s-iw)/2:(%s-ih)/2\" -y \"%s\"",
                $this->cfg['server_path_ffmpeg'], $st, $file, $f, FFMPEG_V, $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $tmp_dir . '%03d.jpg');

            $this->log($cmd);
            exec($cmd . ' 2>&1', $output);
            $this->log(implode("\n", $output));

            if (file_exists($tmp_dir . '002.jpg')) {
                $src = $tmp_dir . '002.jpg';
            } elseif (file_exists($tmp_dir . '001.jpg')) {
                $src = $tmp_dir . '001.jpg';
            } else {
                $this->log('Failed to extract thumb!');
                continue;
            }

            if (isset($src)) {
                copy($src, $dst);

                $this->log("\n" . 'Copying thumb ' . $src . ' -> ' . $dst . '!' . "\n");

                if (file_exists($dst) && filesize($dst) > 100 and $thumb != 1) {
                    VFileinfo::doDelete($src);
                    ++$i;
                }
            }
        }

        VFileinfo::doDelete($tmp_dir);

        return ($i == 1) ? 0 : ($i - 1);
    }

    public function generate_frames($duration, $nr = false)
    {
        if ($nr === false) {
            $nr = $this->cfg['thumbs_nr'];
        }

        $frames = array();
        $step   = floor($duration / $nr);
        if ($step <= 0) {
            $step = 1;
        }

        $i = 1;
        while ($i <= $nr) {
            switch ($this->cfg["thumbs_method"]) {
                case "cons":
                    $frames[] = ($duration / 3) + ($i * 3);
                    break;
                case "split":
                    $frames[] = $i * $step;
                    break;
                case "rand":
                default:
                    $fr = rand(1, floor($duration));
                    if ($nr == 1) {$_SESSION["fr"] = $fr;}
                    $frames[] = $fr;
                    break;
            }
            ++$i;
        }
        return $frames;
    }

    public function update_meta($src, $dst)
    {
        include 'f_core/config.version.php';
        if (!file_exists($this->cfg['server_path_yamdi'])) {
            return false;
        }
        $version['name'] = $this->cfg["website_shortname"];
        $creator         = $version['name'] . '-' . $version['major'] . '.' . $version['minor'];
        $cmd             = $this->cfg['server_path_yamdi'] . ' -i ' . $src . ' -o ' . $dst . ' -c ' . $creator;
        $this->log($cmd);
        exec(escapeshellcmd($cmd) . ' 2>&1', $output);
        $this->log(implode("\n", $output));

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            VFileinfo::doDelete($src);

            return true;
        }

        return false;
    }

    public function fix_moov_atom($src, $dst)
    {
        if (!file_exists($this->cfg['server_path_qt'])) {
            $this->log('Relocation moov atom (using moovrelocator)..');
            $moovrelocator = Moovrelocator::getInstance();
            if (!$moovrelocator->setInput($src) or
                !$moovrelocator->setOutput($dst) or
                !$moovrelocator->fix()) {
                return false;
            }

            return true;
        }

        $cmd = $this->cfg['server_path_qt'] . ' ' . $src . ' ' . $dst;
        $this->log($cmd);
        exec(escapeshellcmd($cmd) . ' 2>&1', $output);
        $this->log(implode("\n", $output));

        if (file_exists($dst) && is_file($dst) && filesize($dst) > 1000) {
            VFileinfo::doDelete($src);

            return true;
        }

        return false;
    }

    public function log_setup($video_id, $log = true)
    {
        global $cfg;

        if ($log === true) {
            $ddir      = 'log_conv/' . date("Y.m.d") . '/';
            $full_path = $cfg["logging_dir"] . '/' . $ddir . $path . '.video_' . $video_id . '.log';

            if (!file_exists($full_path)) {
                @touch($full_path);
            }

            if ($ddir != '' and !is_dir($cfg["logging_dir"] . '/' . $ddir)) {
                @mkdir($cfg["logging_dir"] . '/' . $ddir);
            }

        }
        $this->log           = ($log === true) ? true : false;
        $this->log_file      = $full_path;
        $this->log_file_live = str_replace('.log', '.live.log', $full_path);
    }

    public function log($data)
    {
        if ($this->log !== false) {
            if (!file_exists($this->log_file)) {
                @touch($this->log_file);
            }

            VFileinfo::write($this->log_file, $data . "\n", true);
        }
    }

    public function clean()
    {
        $this->data = array();
        if ($this->log_file) {
//            VFile::delete($this->log_file);
        }

        $this->log_file = '';
    }

    public function identify()
    {
        if (!$this->data) {
            return false;
        }

        $format = strtolower($this->data['format']);
        if (strpos($format, ' ')) {
            $format = substr($format, 0, strpos($format, ' '));
        }

        $audio = strtolower($this->data['audio']);
        if (strpos($audio, ' ')) {
            $audio = substr($audio, 0, strpos($audio, ' '));
        }

        if (in_array($format, $this->flv_formats)) {
            return 'flv';
        } elseif ($format == 'h263' && $audio == 'mp3') {
            return 'flv';
        } elseif ($format == 'h264' && $audio == 'mp3') {
            if (isset($this->data['container']) && $this->data['container'] == 'flv') {
                return 'flv';
            }

            return 'mp4';
        } elseif ($format == 'h264' && $audio == 'aac') {
            if (isset($this->data['container']) && $this->data['container'] == 'flv') {
                return 'flv';
            }

            return 'mp4';
        }

        return false;
    }

    public function duration()
    {
        return isset($this->data['duration_seconds']) ? $this->data['duration_seconds'] : 0;
    }
}

/* IMAGE CONVERSION */

class VImage
{
    public $log_clean = false;

    private $log = false;
    private $log_file;

    public function __construct()
    {
        global $cfg, $class_database, $db;

        $cfg[] = $class_database->getConfigurations('log_image_conversion,thumbs_width,thumbs_height,paid_memberships');
        $rs    = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%conversion_%' OR `cfg_name` LIKE '%server_%';");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }
        $this->cfg = $cfg;
        $this->log = ($cfg["log_image_conversion"] == 1) ? true : false;
    }
    public function __destruct()
    {
    }

    public function createThumbs_ffmpeg($src, $dst, $name, $width, $height, $image_id, $user_key)
    {
        if (!is_dir($dst)) {
            @mkdir($dst);
        }

        if ($this->is_transparent($src) or $this->is_ani($src)) {
            $ext = 'png';
        } else {
            $ext = substr($src, -3);
            $ext = $ext == 'peg' ? 'jpg' : strtolower($ext);
        }

        $dst = $dst . $name . '.' . $ext;

        $i       = $width == 640 ? 0 : 1;
        $size    = $width == 640 ? array(640, 360) : array(320, 180);
        $tmp_dir = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $image_id . '/out/';
        if (!is_dir($tmp_dir)) {
            @mkdir($tmp_dir);
        }

        $f    = null;
        $exif = $ext == 'jpg' ? exif_read_data($src) : array();

        if (isset($exif['Orientation'])) {
            switch ($exif['Orientation']) {
                case 3: // Need to rotate 180 deg
                    //                    $f = ' -vf vflip,hflip';
                    break;
                case 6: // Need to rotate 90 deg clockwise
                    $f = 'transpose=1,';
                    break;
                case 8: // Need to rotate 90 deg counter clockwise
                    $f = 'transpose=2,';
                    break;
            }
        }

        $cmd = sprintf("%s -i \"%s\" -vframes 1 -filter:v \"%sscale=iw*min(%s/iw\,%s/ih):ih*min(%s/iw\,%s/ih),pad=%s:%s:(%s-iw)/2:(%s-ih)/2\" -y \"%s\"",
            $this->cfg['server_path_ffmpeg'], $src, $f, $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $tmp_dir . '%03d.' . $ext);

        $this->log($cmd);
        exec($cmd . ' 2>&1', $output);
        $this->log(implode("\n", $output));

        if (file_exists($tmp_dir . '002.' . $ext)) {
            $src = $tmp_dir . '002.' . $ext;
        } elseif (file_exists($tmp_dir . '001.' . $ext)) {
            $src = $tmp_dir . '001.' . $ext;
        } else {
            $this->log('Failed to extract thumb!');
        }

        if (isset($src)) {
            copy($src, $dst);
            $this->log("\n" . 'Copying thumb ' . $src . ' -> ' . $dst . "\n");
            if (file_exists($dst) && filesize($dst) > 100) {
                if ($i == 0) {
                    VFileinfo::doDelete($src);
                }

                if ($ext == 'png') {
                    $this->log('Creating thumbnail symlink ' . $dst . ' -> ' . str_replace('.png', '.jpg', $dst) . "\n");

                    exec(sprintf("ln -sf %s %s", $dst, str_replace('.png', '.jpg', $dst)));
                }

                ++$i;
            }
        }

        VFileinfo::doDelete($tmp_dir);

        return ($i == 1) ? 0 : ($i - 1);
    }

    public function createThumbs($src, $dst, $name, $width, $height)
    {
        global $cfg;

        if (!is_dir($dst)) {
            @mkdir($dst);
        }

        $this->log('Input image: ' . $src . "\n");
        $this->log('Output location: ' . $dst . "\n");

        if ($this->is_ani($src)) {
            $image = $dst . $name . '.gif';
            if (copy($src, $image)) {
                $this->log('Animated GIF detected!' . "\n");
                $this->log('Copying image ' . $src . ' -> ' . $image . "\n");
                $this->log("\n" . 'Creating symlink ' . $image . ' -> ' . str_replace('.gif', '.jpg', $image) . "\n");
                $this->log("\n" . 'Generating thumbnails...' . "\n");

                exec(sprintf("ln -sf %s %s", $image, str_replace('.gif', '.jpg', $image)));
            }
        } else {
            $f   = 0;
            $ext = substr($src, -3);
            $ext = $ext == 'peg' ? 'jpg' : strtolower($ext);

            $exif = $ext == 'jpg' ? exif_read_data($src) : array();

            if (isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3: // Need to rotate 180 deg
                        $f = 180;
                        break;
                    case 6: // Need to rotate 90 deg clockwise
                        $f = 270;
                        break;
                    case 8: // Need to rotate 90 deg counter clockwise
                        $f = 90;
                        break;
                }
            }

            if ($this->is_transparent($src)) {
                $this->log('Transparent PNG detected!' . "\n");
                $image = $dst . $name . '.png';
                if ($f > 0) {
                    $this->log('Rotation processed: ' . $f . "\n");
                    $this->save_rotate_transparent($src, $f, $image);
                } else {
                    $this->log('Copying image ' . $src . ' -> ' . $image . "\n");
                    copy($src, $image);
                }

                $this->log("\n" . 'Creating symlink ' . $image . ' -> ' . str_replace('.png', '.jpg', $image) . "\n");
                $this->log("\n" . 'Generating thumbnails...' . "\n");

                exec(sprintf("ln -sf %s %s", $image, str_replace('.png', '.jpg', $image)));

                //add symlink from png to jpg
            } else {
                $image = $dst . $name . '.jpg';

                if ($f > 0) {
                    $this->log('JPEG rotation processed: ' . $f . "\n");
                    $this->log('Writing output image: ' . $image . "\n");
                    $this->save_rotate_jpeg($src, $f, $image);

                    $this->log("\n\n" . 'Generating thumbnails...' . "\n");
                } else {
                    $this->log('JPEG image processed: ' . $image . "\n");
                    $this->log("\n" . 'Generating thumbnails...' . "\n");

                    $thumb = PhpThumbFactory::create($src);
                    $thumb->resize($width, $height);
                    $thumb->save($image, 'jpg');
                }

            }
        }

        if (file_exists($image) and is_file($image)) {
            return true;
        }
    }
    public function save_rotate_jpeg($src, $angle, $image)
    {
        // Content type
        header('Content-type: image/jpeg');
        // Load
        $source = imagecreatefromjpeg($src);
        // Rotate
        $rotate = imagerotate($source, $angle, 0);
        // Output
        imagejpeg($rotate, $image);
        // Free the memory
        imagedestroy($source);
        imagedestroy($rotate);
    }
    public function save_rotate_transparent($src, $angle, $image)
    {
        $source = imagecreatefrompng($src) or die('Error opening file ' . $filename);
        imagealphablending($source, false);
        imagesavealpha($source, true);

        $rotation = imagerotate($source, $angle, -1);
        imagealphablending($rotation, false);
        imagesavealpha($rotation, true);

        header('Content-type: image/png');
        imagepng($rotation, $image);
        imagedestroy($source);
        imagedestroy($rotation);
    }
    public function log_setup($image_id, $log = true)
    {
        global $cfg;

        if ($log === true) {
            $ddir      = 'log_conv/' . date("Y.m.d") . '/';
            $full_path = $cfg["logging_dir"] . '/' . $ddir . $path . '.image_' . $image_id . '.log';

            if (!file_exists($full_path)) {
                @touch($full_path);
            }

            if ($ddir != '' and !is_dir($cfg["logging_dir"] . '/' . $ddir)) {
                @mkdir($cfg["logging_dir"] . '/' . $ddir);
            }

        }
        $this->log      = ($log === true) ? true : false;
        $this->log_file = $full_path;
    }

    public function log($data)
    {
        if ($this->log !== false) {
            if (!file_exists($this->log_file)) {
                @touch($this->log_file);
            }

            VFileinfo::write($this->log_file, $data . "\n", true);
        }
    }
    public function is_transparent($filename)
    {
        if (strlen($filename) == 0 || !file_exists($filename)) {
            return false;
        }

        if (ord(file_get_contents($filename, false, null, 25, 1)) & 4) {
            return true;
        }

        $contents = file_get_contents($filename);
        if (stripos($contents, 'PLTE') !== false && stripos($contents, 'tRNS') !== false) {
            return true;
        }

        return false;
    }

    public function is_ani($filename)
    {
        if (!($fh = @fopen($filename, 'rb'))) {
            return false;
        }

        $count = 0;
        //an animated gif contains multiple "frames", with each frame having a
        //header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C)

        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers
        while (!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); //read 100kb at a time
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $chunk, $matches);
        }

        fclose($fh);
        return $count > 1;
    }
}

/* AUDIO CONVERSION */

class VAudio
{
    private $cfg;

    public $data      = array();
    public $log_clean = false;

    private $log = false;
    private $log_file;

    public function __construct()
    {
        global $cfg, $class_database, $db;

        $cfg[] = $class_database->getConfigurations('log_audio_conversion,thumbs_width,thumbs_height,paid_memberships');
        $rs    = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%conversion_%' OR `cfg_name` LIKE '%server_%';");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }
        $this->cfg = $cfg;
        $this->log = ($cfg["log_audio_conversion"] == 1) ? true : false;
    }

    public function __destruct()
    {
    }

    public function time_to_seconds($timecode)
    {
        $hours_end = strpos($timecode, ':', 0);
        $hours     = substr($timecode, 0, $hours_end);
        $mins      = substr($timecode, $hours_end + 1, 2);
        $secs      = trim(substr($timecode, $hours_end + 4));

        if ($secs == '') {
            $mins  = $hours;
            $secs  = $mins;
            $hours = 0;
        }
        return ($hours * 3600) + ($mins * 60) + $secs;
    }

    public function load($file)
    {

        if (!file_exists($file)) {
            return false;
        }

        $this->data        = array();
        $this->data['cmd'] = sprintf("%s -i \"%s\"", $this->cfg["server_path_ffmpeg"], $file);

        exec($this->data['cmd'] . ' 2>&1', $output, $return);

        foreach ($output as $line) {
            if (preg_match('/Input #0, (.*?) from/', $line, $matches)) {
                $this->data['container'] = ltrim(trim($matches['1'], ','));
                continue;
            }

            if (!isset($this->data['bitrate']) && strstr($line, 'Duration:')) {
                $parts                           = explode(', ', trim($line));
                $this->data['duration_timecode'] = ltrim($parts['0'], 'Duration: ');
                $this->data['duration_seconds']  = self::time_to_seconds($this->data['duration_timecode']);
                $this->data['start']             = ltrim($parts['1'], 'start: ');
                $this->data['bitrate']           = (float) ltrim($parts['2'], 'bitrate: ');
                continue;
            }

            if (!isset($this->data['audio']) && preg_match('/Stream(.*): Audio: (.*)/', $line, $matches)) {
                $parts                         = explode(', ', trim($matches['2']));
                $this->data['audio']           = $parts['0'];
                $this->data['audio_frequency'] = (float) $parts['1'];
                $this->data['audio_stereo']    = $parts['2'];
                if (isset($parts['4'])) {
                    $this->data['audio_bitrate'] = (float) $parts['4'];
                } else {
                    $this->data['audio_bitrate'] = (isset($parts['3'])) ? (float) $parts['3'] : null;
                }
                continue;
            }

            if (isset($this->data['bitrate']) && isset($this->data['format']) && isset($this->data['audio'])) {
                break;
            }
        }

        if (isset($this->data['duration_seconds']) && isset($this->data['framerate'])) {
            $this->data['frames'] = ceil($this->data['duration_seconds'] * $this->data['framerate']);
        }

        return true;
    }

    public function get_data_string()
    {
        $string = null;
        foreach ($this->data as $key => $value) {
            $string .= $key . ' => ' . $value . "\n";
        }

        return $string;
    }

    private static function gs($k)
    {
        global $cfg;
        return md5($cfg["global_salt_key"] . $k);
    }
    public function convert_to_mp3($src, $file_key, $user_key, $short = false)
    {
        $dst_wav_full = $this->cfg["media_files_dir"] . '/' . $user_key . '/a/' . self::gs($file_key) . '.wav';
        $dst_mp3_full = $this->cfg["media_files_dir"] . '/' . $user_key . '/a/' . self::gs($file_key) . '.mp3';
        $dst_wav_prev = $this->cfg["media_files_dir"] . '/' . $user_key . '/a/' . $file_key . '.wav';
        $dst_mp3_prev = $this->cfg["media_files_dir"] . '/' . $user_key . '/a/' . $file_key . '.mp3';
        $dst_wav      = !$short ? $dst_wav_full : $dst_wav_prev;
        $dst_mp3      = !$short ? $dst_mp3_full : $dst_mp3_prev;
        $options      = null;

        if ($short == 1) {
            $options .= '-t ' . FFMPEG_PREVIEW;
        }

        $output = array();
        $cmd    = sprintf("%s -i \"%s\" %s -vn -acodec pcm_s16le -ac 1 -f wav %s  1> %s 2>&1", $this->cfg["server_path_ffmpeg"], $src, $options, $dst_wav, $this->log_file_live);

        $this->log($cmd);
        exec($cmd);
        $lines = file($this->log_file_live);
        foreach ($lines as $line_num => $line) {
            $output[] = $line;
        }
        $this->log(implode("\n", $output));

        if (file_exists($dst_wav) && is_file($dst_wav) && filesize($dst_wav) > 1000) {
            $output = array();
            $cmd    = sprintf("%s -b %s -s %s -h -p -T %s %s 1> %s 2>&1", $this->cfg["server_path_lame"], $this->cfg["conversion_mp3_bitrate"], $this->cfg["conversion_mp3_srate"], $dst_wav, $dst_mp3, $this->log_file_live);

            $this->log("\n\n" . $cmd);
            exec($cmd);
            $lines = file($this->log_file_live);
            foreach ($lines as $line_num => $line) {
                $output[] = $line;
            }
            $this->log(implode("\n", $output));

            if (file_exists($dst_mp3) && is_file($dst_mp3) && filesize($dst_mp3) > 1000) {
                @unlink($dst_wav);

                return true;
            }
        }
    }

    public function convert_to_aac($src, $file_key, $user_key, $short = false)
    {
        $dst_aac_full = $this->cfg["media_files_dir"] . '/' . $user_key . '/a/' . self::gs($file_key) . '.mp4';
        $dst_aac_prev = $this->cfg["media_files_dir"] . '/' . $user_key . '/a/' . $file_key . '.mp4';
        $dst_aac      = !$short ? $dst_aac_full : $dst_aac_prev;
        $options      = '';

        if ($short == 1) {
            $options = '-t ' . FFMPEG_PREVIEW;
        }

        $output = array();
        $cmd = sprintf("%s -i \"%s\" %s -vcodec copy -acodec copy %s -y", $this->cfg["server_path_ffmpeg"], $src, $options, $dst_aac);

        $this->log($cmd);
        exec($cmd);
        $lines = file($this->log_file_live);
        foreach ($lines as $line_num => $line) {
            $output[] = $line;
        }
        $this->log(implode("\n", $output));

        if (file_exists($dst_aac) && is_file($dst_aac) && filesize($dst_aac) > 1000) {
            return true;
        }
    }

    public function create_thumbs($type, $file_key, $user_key)
    {
        $width  = $type == 1 ? 320 : 640;
        $height = $type == 1 ? 180 : 360;
        $src    = $this->cfg["global_images_dir"] . '/audio.png';
        $dst    = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $file_key . '/';
        $name   = $type . '.jpg';

        if (!is_dir($dst)) {
            @mkdir($dst);
        }
        $cmd = $src . ' -> ' . $dst . $name;
        $this->log("\n" . $cmd);

        $thumb = PhpThumbFactory::create($src);
        $thumb->resize($width, $height);
        $thumb->save($dst . $name, 'jpg');

        if (file_exists($dst . $name) && is_file($dst . $name) && filesize($dst . $name) > 1000) {
            return true;
        }
    }

    public function log_setup($audio_id, $log = true)
    {
        global $cfg;

        if ($log === true) {
            $ddir      = 'log_conv/' . date("Y.m.d") . '/';
            $full_path = $cfg["logging_dir"] . '/' . $ddir . $path . '.audio_' . $audio_id . '.log';

            if (!file_exists($full_path)) {
                @touch($full_path);
            }

            if ($ddir != '' and !is_dir($cfg["logging_dir"] . '/' . $ddir)) {
                @mkdir($cfg["logging_dir"] . '/' . $ddir);
            }

        }
        $this->log           = ($log === true) ? true : false;
        $this->log_file      = $full_path;
        $this->log_file_live = str_replace('.log', '.live.log', $full_path);
    }

    public function log($data)
    {
        if ($this->log !== false) {
            if (!file_exists($this->log_file)) {
                @touch($this->log_file);
            }

            VFileinfo::write($this->log_file, $data . "\n", true);
        }
    }
}

/* DOCUMENT CONVERSION */

class VDocument
{
    private $cfg;

    public $data      = array();
    public $log_clean = false;

    private $log = false;
    private $log_file;

    public function __construct()
    {
        global $cfg, $class_database, $db;

        $cfg[] = $class_database->getConfigurations('log_doc_conversion,thumbs_width,thumbs_height');
        $rs    = $db->execute("SELECT `cfg_name`, `cfg_data` FROM `db_settings` WHERE `cfg_name` LIKE '%conversion_%' OR `cfg_name` LIKE '%server_%';");
        while (!$rs->EOF) {
            $cfg[$rs->fields["cfg_name"]] = $rs->fields["cfg_data"];
            @$rs->MoveNext();
        }
        $this->cfg = $cfg;
        $this->log = ($cfg["log_doc_conversion"] == 1) ? true : false;
    }

    public function __destruct()
    {
    }

    public function load($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $this->data = array();
        $doctype    = null;
        $ext        = VFileinfo::getExtension($file);

        switch ($ext) {
            case "doc":
            case "docx":
            case "odt":
            case "rtf":
            case "txt":
            case "html":
                $doctype = 'document';
                break;
            case "csv":
            case "ods":
            case "xls":
            case "xlt":
                $doctype = 'spreadsheet';
                break;
            case "eps":
            case "odp":
            case "pps":
            case "ppt":
            case "dpt":
                $doctype = 'presentation';
                break;
        }

        $this->data["ext"]     = $ext;
        $this->data["doctype"] = $doctype;

        return true;
    }
    private static function gs($k)
    {
        global $cfg;
        return md5($cfg["global_salt_key"] . $k);
    }
    public function convert_to_pdf($src, $file_key, $user_key, $short = false)
    {
        $dst_pdf_prev = $this->cfg["media_files_dir"] . '/' . $user_key . '/d/' . $file_key . '.pdf';
        $dst_pdf_full = $this->cfg["media_files_dir"] . '/' . $user_key . '/d/' . self::gs($file_key) . '.pdf';
        $dst_pdf      = !$short ? $dst_pdf_full : $dst_pdf_prev;
        $options      = $short ? '-e PageRange=1-2' : '';

        if ($this->data["ext"] != 'pdf') {
            $cmd = sprintf("%s -d %s %s -f pdf -o %s -v \"%s\"", $this->cfg["server_path_unoconv"], $this->data["doctype"], $options, $dst_pdf, $src);

            $this->log($cmd);
            exec($cmd . ' 2>&1', $output);
            $this->log(implode("\n", $output));
        } else {
            $this->log("\n" . 'Copying ' . $src . ' -> ' . $dst_pdf . '!' . "\n");
            copy($src, $dst_pdf);
        }

        if (file_exists($dst_pdf) && is_file($dst_pdf) && filesize($dst_pdf) > 1000) {
            return true;
        }
    }

    public function convert_to_swf($file_key, $user_key)
    {
        $src_pdf = $this->cfg["media_files_dir"] . '/' . $user_key . '/d/' . $file_key . '.pdf';
        $dst_swf = $this->cfg["media_files_dir"] . '/' . $user_key . '/d/' . $file_key . '.swf';

        if (file_exists($src_pdf)) {
            $cmd = sprintf("%s %s -o %s -f -T 9 -t -s storeallcharacters", $this->cfg["server_path_pdf2swf"], $src_pdf, $dst_swf);

            $this->log($cmd);
            exec($cmd . ' 2>&1', $output);
            $this->log(implode("\n", $output));
        } else {
            return false;
        }

        if (file_exists($dst_swf) && is_file($dst_swf) && filesize($dst_swf) > 1000) {
            return true;
        }
    }
    public function createThumbs_ffmpeg($dst, $name, $width, $height, $image_id, $user_key, $src = '', $short = false)
    {
        $src = $src == '' ? $this->cfg["global_images_dir"] . '/document.gif' : $src;

        if (!is_dir($dst)) {
            @mkdir($dst);
        }

        $dst     = $dst . $name . '.jpg';
        $i       = $width == 640 ? 0 : 1;
        $size    = $width == 640 ? ($short ? array(360, 640) : array(640, 360)) : ($short ? array(180, 320) : array(320, 180));
        $tmp_dir = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $image_id . '/out/';
        if (!is_dir($tmp_dir)) {
            @mkdir($tmp_dir);
        }
        if ($short) {
            $cmd = sprintf("%s -i \"%s\" -vf \"scale=w=%s:h=%s:force_original_aspect_ratio=2,crop=%s:%s\" %s", $this->cfg['server_path_ffmpeg'], $src, $size[0], $size[1], $size[0], $size[1], $tmp_dir . '%03d.jpg');
        } else {
            $cmd = sprintf("%s -i \"%s\" -vframes 1 -filter:v \"scale=iw*min(%s/iw\,%s/ih):ih*min(%s/iw\,%s/ih),pad=%s:%s:(%s-iw)/2:(%s-ih)/2\" -y \"%s\"",
                $this->cfg['server_path_ffmpeg'], $src, $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $size[0], $size[1], $tmp_dir . '%03d.jpg');
        }

        $this->log($cmd);
        exec($cmd . ' 2>&1', $output);
        $this->log(implode("\n", $output));

        if (file_exists($tmp_dir . '002.jpg')) {
            $src = $tmp_dir . '002.jpg';
        } elseif (file_exists($tmp_dir . '001.jpg')) {
            $src = $tmp_dir . '001.jpg';
        } else {
            $this->log('Failed to extract thumb!');
        }

        if (isset($src)) {
            copy($src, $dst);
            $this->log("\n" . 'Copying thumb ' . $src . ' -> ' . $dst . '!' . "\n");
            if (file_exists($dst) && filesize($dst) > 100) {
                VFileinfo::doDelete($src);
                ++$i;
            }
            if (file_exists($this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $image_id . '/1.tmp.jpg')) {
                unlink($this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $image_id . '/1.tmp.jpg');
            }
        }

        VFileinfo::doDelete($tmp_dir);

        return ($i == 1) ? 0 : ($i - 1);
    }

    public function create_thumbs($type, $file_key, $user_key)
    {
        $width  = $type == 1 ? 320 : 640;
        $height = $type == 1 ? 180 : 360;

        $dst  = $this->cfg["media_files_dir"] . '/' . $user_key . '/t/' . $file_key . '/';
        $name = $type . '.jpg';

        if (!is_dir($dst)) {
            @mkdir($dst);
        }

        $this->cfg["server_path_convert"] = null;

        if (file_exists($this->cfg["server_path_convert"])) {
            $src = $this->cfg["media_files_dir"] . '/' . $user_key . '/d/' . $file_key . '.pdf';
            $cmd = sprintf("%s %s[0] -thumbnail %sx%s %s", $this->cfg["server_path_convert"], $src, $width, $height, $dst . $name);

            $this->log("\n" . $cmd);
            exec($cmd . ' 2>&1');
        } else {
            $src = $this->cfg["global_images_dir"] . '/document.gif';
            $cmd = $src . ' -> ' . $dst . $name;
            $this->log("\n" . $cmd);

            $thumb = PhpThumbFactory::create($src);
            $thumb->resize($width, $height);
            $thumb->save($dst . $name, 'jpg');
        }

        if (file_exists($dst . $name) && is_file($dst . $name) && filesize($dst . $name) > 1000) {
            return true;
        }
    }

    public function log_setup($doc_id, $log = true)
    {
        global $cfg;

        if ($log === true) {
            $ddir      = 'log_conv/' . date("Y.m.d") . '/';
            $full_path = $cfg["logging_dir"] . '/' . $ddir . $path . '.doc_' . $doc_id . '.log';

            if (!file_exists($full_path)) {
                @touch($full_path);
            }

            if ($ddir != '' and !is_dir($cfg["logging_dir"] . '/' . $ddir)) {
                @mkdir($cfg["logging_dir"] . '/' . $ddir);
            }

        }
        $this->log      = ($log === true) ? true : false;
        $this->log_file = $full_path;
    }

    public function log($data)
    {
        if ($this->log !== false) {
            if (!file_exists($this->log_file)) {
                @touch($this->log_file);
            }

            VFileinfo::write($this->log_file, $data . "\n", true);
        }
    }
}
