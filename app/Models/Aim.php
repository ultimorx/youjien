<?php

namespace App\Models;

use App\Models\Grade;
use App\Util\Date;
use Illuminate\Support\Facades\DB;

class Aim extends SoftDeleteModel
{
    public static function __list_week_first_days($bizyear)
    {
        $days = array();
        $bizweek = 0;
        foreach(Date::BIZ_MONTHS as $month) : // 4月〜3月
            $year = Date::bizyear_to_year($bizyear, $month);
            $lastday = Date::lastday($year, $month);
            for ($day=1; $day <= $lastday; $day++) :
                $date = Date::date($year,$month,$day);
                $week_idx = Date::week_idx($date);

                $output_flg = false;
                // 4/1をリストに追加
                if($month==4 && $day==1) :
                    $output_flg = true;
                endif;
                // 月曜を追加
                if(Date::is_mon($week_idx)) :
                    $bizweek++;
                    $output_flg = true;
                endif;

                if($output_flg) :
                    $days[] = [
                        'date' => $date,
                        'week_idx' => $week_idx,
                        'bizweek' => $bizweek,
                    ];
                endif;
            endfor;
        endforeach;
        return $days;
    }

    public static function list_week_first_days($bizyear, $grade_id)
    {
        $days = self::_list_week_first_days($bizyear, $grade_id);
        if (count($days) == 0) {
            // 年度と学年のデータがなければ、新規作成
            $calendars = Calendar::list_week_first_days($bizyear);
            self::creates($calendars, $grade_id);
            $days = self::_list_week_first_days($bizyear, $grade_id);
        }
        return $days;
    }

    public static function _list_week_first_days($bizyear, $grade_id)
    {
        $kindergarten_id = 1;
        return self::query()
            ->where('kindergarten_id', '=', $kindergarten_id)
            ->where('bizyear', '=', $bizyear)
            ->where('grade_id', '=', $grade_id)
            ->orderBy('date', 'asc')
            ->get();
    }


    public static function list($year, $month)
    {
        return self::query()
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function row($date, $grade_id)
    {
        $calendar = Calendar::row($date);
        if(empty($calendar)) {
            return [];
        }
         return self::query()
            ->where('bizyear', '=', $calendar->bizyear)
            ->where('bizweek', '=', $calendar->bizweek)
            ->where('grade_id', '=', $grade_id)
            ->first();
    }


    public static function copy_by_bizyear($bizyear)
    {
        $calendars = Calendar::list_week_first_days($bizyear);
        $grades = Grade::list(Grade::DESC);
        foreach ($grades as $grade) {
            self::creates($calendars, $grade->id);
        }
    }

    public static function creates($calendars, $grade_id)
    {
        $kindergarten_id = 1;

        DB::beginTransaction();
        try {
            foreach ($calendars as $calendar) {
                $recode = self::query()->where('date', $calendar->date)->where('kindergarten_id', $kindergarten_id)->where('grade_id', $grade_id)->first();
                if (empty($recode)) {
                    // 前年度データがあればコピー用に取得
                    $before = self::query()->where('kindergarten_id', $kindergarten_id)->where('bizyear', $calendar->bizyear-1)->where('bizweek', $calendar->bizweek)->where('grade_id', $grade_id)->first();
                    $before_play = ($before->play) ?? '';
                    $before_life = ($before->life) ?? '';
                    $before_note = ($before->note) ?? '';

                    self::create([
                        'kindergarten_id' => $kindergarten_id,
                        'bizyear' => $calendar->bizyear,
                        'bizweek' => $calendar->bizweek,
                        'grade_id' => $grade_id,
                        'date' => $calendar->date,
                        'play' => $before_play,
                        'life' => $before_life,
                        'note' => $before_note,
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
