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

class VFileinfo
{
    /* get conversion progress from log file */
    public function get_progress($file_key, $file_date = false)
    {
        global $cfg;

        $date    = $file_date === true ? $file_date : date("Y.m.d");
        $logfile = $cfg['logging_dir'] . '/log_conv/' . $date . '/.video_' . $file_key . '.live.log';

        if (!file_exists($logfile)) {
            return '<span class="encoding-pending">Pending encoding</span>';
        }

        $content = file_get_contents($logfile);

        if (!$content) {
            return '<span class="encoding-pending">In queue...</span>';
        }

        if ($content) {
            //get destination name
            preg_match("/Output #(.*?), (.*?), to '(.*?)':/", $content, $dmatches);
            $dst     = $dmatches[3];
            $d       = explode(".", $dst);
            $t       = count($d);
            $i       = (($t - 3) > 0) ? ($t - 3) : ($t - 2);
            $profile = strtoupper($d[($t - 1)]) . ' [' . $d[($i)] . ']';

            //get duration of source
            preg_match("/Duration: (.*?), start:/", $content, $matches);

            $rawDuration = $matches[1];

            //rawDuration is in 00:00:00.00 format. This converts it to seconds.
            $ar       = array_reverse(explode(":", $rawDuration));
            $duration = floatval($ar[0]);
            if (!empty($ar[1])) {
                $duration += intval($ar[1]) * 60;
            }

            if (!empty($ar[2])) {
                $duration += intval($ar[2]) * 60 * 60;
            }

            //get the time in the file that is already encoded
            preg_match_all("/time=(.*?) bitrate/", $content, $matches);

            $rawTime = array_pop($matches);

            //this is needed if there is more than one match
            if (is_array($rawTime)) {$rawTime = array_pop($rawTime);}

            //rawTime is in 00:00:00.00 format. This converts it to seconds.
            $ar   = array_reverse(explode(":", $rawTime));
            $time = floatval($ar[0]);
            if (!empty($ar[1])) {
                $time += intval($ar[1]) * 60;
            }

            if (!empty($ar[2])) {
                $time += intval($ar[2]) * 60 * 60;
            }

            //calculate the progress
            $progress = round(($time / $duration) * 100);
/*
$str = '
<ul style="display:none;font-weight: normal; font-size: 12px; line-height: 13px; margin: 5px; text-align: left;">
<li>' . $profile . '</li>
<li>Length: ' . $duration . '</li>
<li>Current: ' . $time . '</li>
<li>Progress: ' . $progress . '%</li>
</ul>
';
 */
            $str = '
                <ul class="encoding-ul">
                    <li><div class="encoding-progress" style="width:' . $progress . '%;">' . $profile . ' ' . $progress . '%</div></li>
                </ul>
            ';

            return $str;
        }
    }

    /* get directory size */
    public function getDirectorySize($path)
    {
        $total      = array();
        $totalsize  = 0;
        $totalcount = 0;
        $dircount   = 0;

        if (is_dir($path) and $handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $nextpath = $path . '/' . $file;
                if ($file != '.' && $file != '..' && !is_link($nextpath)) {
                    if (is_dir($nextpath)) {
                        $dircount++;
                        $result = self::getDirectorySize($nextpath);
                        $totalsize += $result['size'];
                        $totalcount += $result['count'];
                        $dircount += $result['dircount'];
                    } elseif (is_file($nextpath)) {
                        $totalsize += filesize($nextpath);
                        $totalcount++;
                    }
                }
            }
            closedir($handle);
        }

        $total['size']     = $totalsize;
        $total['count']    = $totalcount;
        $total['dircount'] = $dircount;

        return $total;
    }

    /* create folders */
    public function createFolder($path, $mode = 0755)
    {
        $path = self::safe($path);
        if (self::exists($path)) {
            return true;
        }
        $origmask = @umask(0);
        if (mkdir($path, $mode, true)) {
            @umask($origmask);
            return true;
        }
        @umask($origmask);
        return false;
    }
    /* existing */
    public function exists($path)
    {
        return (is_dir(self::safe($path))) ? true : false;
    }
    /* folder check */
    public function safe($path)
    {
        return html_entity_decode($path);

        $regex = array('#[^A-Za-z0-9:\_\-\.' . DIRECTORY_SEPARATOR . ' ]#');
        return preg_replace($regex, '', $path);
    }
    /* deleting */
    public function doDelete($path, $recursive = true)
    {
        $path = self::safe($path);

        if (!file_exists($path)) {
            return true;
        }
        if (!is_dir($path) or is_link($path)) {
            return unlink($path);
        }
        foreach (scandir($path) as $item) {
            if ($item == '.' or $item == '..') {
                continue;
            }
            if (!self::doDelete($path . '/' . $item)) {
                @chmod($path . '/' . $item, 0777);
                if (!self::doDelete($path . '/' . $item)) {
                    return false;
                }
            }
        }
        return rmdir($path);
    }
    /* write to log */
    public function write($file, $data, $append = false)
    {
        if ($append !== false) {
            return (bool) file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
        } else {
            return (bool) file_put_contents($file, $data, LOCK_EX);
        }
    }
    /* get extension from file name */
    public function getExtension($file)
    {
        $ext_info = mb_split("[/\\.]", strtolower($file));
        $ext_key  = count($ext_info) - 1;
        $ext_info = $ext_info[$ext_key];

        return strtolower($ext_info);
    }
    /* get permissions from file name */
    public function getPermissions($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $perms    = decoct(fileperms($file));
        return $p = is_file($file) ? substr($perms, 2) : (is_dir($file) ? substr($perms, 1) : null);
    }
    /* get file ownerships, Unix type listing */
    public function getOwnerships($file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $perms = fileperms($file);

        if (($perms & 0xC000) == 0xC000) { /* Socket */$info = 's';} elseif (($perms & 0xA000) == 0xA000) { /* Symbolic Link */$info = 'l';} elseif (($perms & 0x8000) == 0x8000) { /* Regular */$info = '-';} elseif (($perms & 0x6000) == 0x6000) { /* Block special */$info = 'b';} elseif (($perms & 0x4000) == 0x4000) { /* Directory */$info = 'd';} elseif (($perms & 0x2000) == 0x2000) { /* Character special */$info = 'c';} elseif (($perms & 0x1000) == 0x1000) { /* FIFO pipe */$info = 'p';} else { /* Unknown */$info = 'u';}

        /* Owner */
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
        /* Group */
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
        /* World */
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }
    /* recursive glob */
    public function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);

        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge($files, self::glob_recursive($dir . '/' . basename($pattern), $flags));
        }

        return $files;
    }
}
