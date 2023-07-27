<?php

namespace App\Models;

class CityInformation extends CityDbModel
{
    protected $table = 'informations';

    const DISPLAY_TRUE = 1;
    const DISPLAY_FALSE = 0;

    public static function list()
    {
        return self::query()
            ->orderBy('public_date', 'desc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function list_actives()
    {
        return self::query()
            ->where('display', '=', self::DISPLAY_TRUE)
            ->where('public_date', '<=', date('Y-m-d'))
            ->orderBy('public_date', 'desc')
            ->orderBy('id', 'asc')
            ->get();
    }

}
