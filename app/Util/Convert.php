<?php

namespace App\Util;

class Convert
{
    /**
     * @param string
     * @return string
     */
    public static function sjis($string)
    {
        return mb_convert_encoding($string, 'SJIS-win', 'UTF-8');
    }

    /**
     * @param string
     * @return string
     */
    public static function unicode($string)
    {
        return mb_convert_encoding($string, 'UTF-16LE', 'UTF-8');
    }

}
