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

class VBenchmark
{
    private static $timers = array();

    public static function start($name)
    {
        if (!isset(self::$timers[$name]) || !is_array(self::$timers[$name])) {
            self::$timers[$name] = array(
                'start'        => microtime(true),
                'stop'         => false,
                'memory_start' => function_exists('memory_get_usage') ? memory_get_usage() : 0,
                'memory_stop'  => false,
            );
        }
    }

    public static function stop($name)
    {
        if (isset(self::$timers[$name]) && self::$timers[$name]['stop'] === false) {
            self::$timers[$name]['stop']        = microtime(true);
            self::$timers[$name]['memory_stop'] = function_exists('memory_get_usage') ? memory_get_usage() : 0;
        }
    }

    public static function get($name, $decimals = 4)
    {
        if (!isset(self::$timers[$name])) {
            return false;
        }
        if (self::$timers[$name]['stop'] === false) {
            self::stop($name);
        }
        return array(
            'time'   => number_format(self::$timers[$name]['stop'] - self::$timers[$name]['start'], $decimals),
            'memory' => self::bytes(self::$timers[$name]['memory_stop'] - self::$timers[$name]['memory_start']),
        );
    }

    public static function bytes($bytes)
    {
        $i       = 0;
        $formats = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        while ($bytes >= 1024) {
            $bytes = $bytes / 1024;
            ++$i;
        }

        return number_format($bytes, ($i ? 2 : 0), ',', '.') . ' ' . $formats[$i];
    }
}
