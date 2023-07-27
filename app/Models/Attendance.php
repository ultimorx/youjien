<?php

namespace App\Models;

use Carbon\Carbon;

class Attendance extends SoftDeleteModel
{
    const ATTENDANCE_DEFAULT = null;
    const ATTENDANCE_TRUE = 1;
    const ATTENDANCE_FALSE = 0;
    const MORNING_USING_TRUE = 1;
    const MORNING_USING_FALSE = 0;
    const EVENING_EVENING_TIME_ID_DEFAULT = 0;
    const BUS_ID_DEFAULT = 0;
    const ABSENCE_TYPE_DEFAULT = 0;
    const DISEASES_ID_DEFAULT = 0;
    // const ABSENCE_TYPES = [
    //     10 => '出席停止',
    //     20 => '病欠',
    //     30 => '忌引',
    //     40 => '事故欠'
    // ];
    const LATE_MARK = 'チ';
    const EARLY_MARK = 'ハ';

    protected $attributes = [
        'attendance' => NULL,
        'morning_using' => self::MORNING_USING_FALSE,
    ];

    public function getLateAttribute($value)
    {
        // NULL や文字列など時間ではない値の場合、Carbonでは現在時刻が返却される
        return empty($value)? NULL: Carbon::parse($value)->format("H:i");
    }
    public function getEarlyAttribute($value)
    {
        return empty($value)? NULL: Carbon::parse($value)->format("H:i");
    }
    public function getOuttimeAttribute($value)
    {
        return empty($value)? NULL: Carbon::parse($value)->format("H:i");
    }

    public function roster()
    {
        return $this->belongsTo(Roster::class);
    }

    public function disease()
    {
        return $this->belongsTo(Disease::class);
    }

    public function Bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
