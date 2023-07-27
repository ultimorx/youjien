<?php

namespace App\Models;

class CityDisease extends CityDbModel
{
    protected $table = 'diseases';

    const ACTIVE_TRUE = 1;
    const ACTIVE_FALSE = 0;
    const ACTIVE_DEFAULT = self::ACTIVE_TRUE;

    const ABSENCE_TYPE_BYOUKETSU = 10;
    const ABSENCE_TYPE_TEISHI    = 20;
    const ABSENCE_TYPE_KIBIKI    = 30;
    const ABSENCE_TYPE_JIKOKETSU = 40;
    const ABSENCE_TYPES = [
        self::ABSENCE_TYPE_TEISHI    => '出席停止',
        self::ABSENCE_TYPE_KIBIKI    => '忌引',
        self::ABSENCE_TYPE_BYOUKETSU => '病欠',
        self::ABSENCE_TYPE_JIKOKETSU => '事故欠'
    ];
    const ABSENCE_TYPES_MARKS = [
        self::ABSENCE_TYPE_TEISHI    => 'テ',
        self::ABSENCE_TYPE_KIBIKI    => 'キ',
        self::ABSENCE_TYPE_BYOUKETSU => '／',
        self::ABSENCE_TYPE_JIKOKETSU => '×'
    ];

    public static function absence_types_mark($absence_types)
    {
        return (self::ABSENCE_TYPES_MARKS[$absence_types])? : '';
    }

    public static function absence_type_name($absence_type)
    {
        return (self::ABSENCE_TYPES[$absence_type])? : '';
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public static function list_all()
    {
        return self::query()
            ->orderBy('order', 'asc')
            ->get();
    }

    public static function list($absence_type=null)
    {
        $query = self::query()
            ->where('active', '=', self::ACTIVE_TRUE)
            ->orderBy('order', 'asc');

        if ( !empty($absence_type) ) {
            $query->where('absence_type', '=', $absence_type);
        }

        return $query->get();
    }
}
