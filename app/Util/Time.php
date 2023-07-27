<?php

namespace App\Util;

class Time
{
    /**
     * @param string time 00:00:00
     * @return string time 00:00
     */
    public static function hour_minute($time)
    {
        if( empty($time) ) {
            return '';
        }
        list($hour,$minute) = explode(':', $time);
        return ($hour + 0) . ':' . ($minute);
    }

    /**
     * @param string time 00:00:00
     * @return string second
     */
    public static function time_to_sec($time)
    {
        if( empty($time) ) {
            return 0;
        }
        list($hour,$minute) = explode(':', $time);
        return ($hour + 0) * 3600 + ($minute + 0) * 60;
    }

    /**
     * @param string time 00:00:00
     * @return string time 00:00
     */
    public static function sec_to_time($second)
    {
        if( empty($second) ) {
            return '00:00';
        }
        $h = (int)($second / 3600);
        $m = (int)($second - ($h * 3600)) / 60;
        return sprintf('%01d', $h) . ':' . sprintf('%02d', $m);
    }
}
