<?php

namespace App\Models;

use App\Models\Calendar;
use App\Util\Date;
use Illuminate\Support\Facades\DB;

class Report extends SoftDeleteModel
{
    public static function biz_week($date)
    {
        $days = array();
        $bizyear_week = 0;
        // 4月〜翌3月
        foreach(Date::BIZ_MONTHS as $month) :
            $year = ($month <= 3) ? $bizyear + 1 : $bizyear;
            $lastday = Date::lastday($year, $month);
            for ($day=1; $day <= $lastday; $day++) :
                $date = Date::date($year,$month,$day);
                $week_idx = Date::week_idx($date);
                $week = Date::week($date);

                $output_flg = false;
                // 4/1をリストに追加
                if($month==4 && $day==1) :
                    $output_flg = true;
                endif;
                // 月曜を追加
                if($week_idx == 1) :
                    $bizyear_week++;
                    $output_flg = true;
                endif;

                if($output_flg) :
                    // $days[] = $date.' W:'.$week_idx.' BizWN:'.$bizyear_week;
                    $days[] = [
                        'date' => $date,
                        'week_idx' => $week_idx,
                        'week' => $week,
                        'bizyear_week' => $bizyear_week,
                    ];
                endif;
            endfor;
        // endfor;
        endforeach;
        // $days = array(1,2,3,4,5);
        return $days;
    }

    public static function list($year, $month)
    {
        return self::query()
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function list_week_days($date, $classroom_id)
    {
        $d = self::get_date($date, $classroom_id);
        if (empty($d)) {
            // 新規作成
            $calendars = Calendar::list_week_days($date);
            self::creates($calendars, $classroom_id);
            $d = self::get_date($date, $classroom_id);
        }

        return self::query()
            ->where('bizyear', '=', $d->bizyear)
            ->where('bizweek', '=', $d->bizweek)
            ->where('classroom_id', '=', $classroom_id)
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function get_date($date, $classroom_id)
    {
         return self::query()
            ->where('date', '=', $date)
            ->where('classroom_id', '=', $classroom_id)
            ->first();
    }

    public static function creates($calendars, $classroom_id)
    {
        DB::beginTransaction();

        try {
            foreach ($calendars as $calendar) {
                $recode = self::query()->where('date', $calendar->date)->where('classroom_id', $classroom_id)->first();
                if (empty($recode)) {
                    self::create([
                        'kindergarten_id' => 1,
                        'bizyear' => $calendar->bizyear,
                        'bizweek' => $calendar->bizweek,
                        'date' => $calendar->date,
                        'classroom_id' => $classroom_id,
                    ]);
                }
            }
            DB::commit();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
        }
    }

}
