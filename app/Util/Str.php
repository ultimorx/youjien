<?php

namespace App\Util;

class Str
{
    const JOIN = "-";

    /**
     * @param string
     * @return string
     */
    public static function shorten($string)
    {
        return str_replace(array('　', ' '), '', $string);
    }

    /**
     * @param string
     * @return string
     */
    public static function url2link($txt)
    {
        return preg_replace_callback(
            '/(https|http):\/\/[\w\.\‾\-\/\?\&\+\=\:\@\%\#\;]+/',
            function ($matches) {
                return '<a href="'.$matches[0].'" target="_blank">'.$matches[0].'</a>';
            },
            $txt
        );
    }
}
