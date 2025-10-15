<?php
defined('_ISVALID') or header('Location: /error');

// Minimal polyfills for PHP 8.1+ removals used in this codebase

if (!function_exists('strftime')) {
    function strftime($format, $timestamp = null)
    {
        $timestamp = $timestamp ?? time();
        // Map common strftime tokens to date() tokens
        $replacements = [
            '%a' => 'D',      // abbreviated weekday name
            '%A' => 'l',      // full weekday name
            '%d' => 'd',      // day of month, zero-padded
            '%e' => 'j',      // day of month, no leading zero
            '%u' => 'N',      // ISO-8601 numeric day of week (1-7)
            '%w' => 'w',      // numeric day of week (0-6)
            '%b' => 'M',      // abbreviated month name
            '%B' => 'F',      // full month name
            '%m' => 'm',      // month (01-12)
            '%y' => 'y',      // 2-digit year
            '%Y' => 'Y',      // 4-digit year
            '%H' => 'H',      // hour 00-23
            '%I' => 'h',      // hour 01-12
            '%M' => 'i',      // minute 00-59
            '%S' => 's',      // second 00-59
            '%T' => 'H:i:s',  // time
            '%p' => 'A',      // AM/PM
            '%P' => 'a',      // am/pm
            '%z' => 'O',      // GMT offset
            '%Z' => 'T',      // timezone abbreviation
            '%%' => '%',      // literal percent
        ];
        $phpFormat = strtr($format, $replacements);
        return date($phpFormat, is_numeric($timestamp) ? (int) $timestamp : strtotime((string) $timestamp));
    }
}

