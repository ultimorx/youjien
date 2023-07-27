<?php

namespace App\Models;

class CityKindergarten extends CityDbModel
{
    protected $table = 'kindergartens';
    const KINDERGARTEN_ID_ZERO_NAME = '本巣市役所／システム管理';

    public static function list()
    {
        return self::query()
            ->orderBy('order', 'asc')
            ->get();
    }

    public static function names()
    {
        $kindergartens = self::list();
        $names = [];
        foreach ($kindergartens as $kindergarten) {
            $names[$kindergarten->id] = $kindergarten->name;
        }
        return $names;
    }

    public static function get($id)
    {
        return self::query()->where('id', '=', $id)->first();
    }
}
