<?php

namespace App\Models;

use App\Models\Calendar;
use App\Models\Event;
use App\Util\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Action extends SoftDeleteModel
{
    public static function monthlist($bizyear, $month)
    {
        // $count = self::count_bizyear($bizyear);
        // if($count == 0) {
        //     self::creates_bizyear($bizyear);
        // }

        $year = Date::bizyear_to_year($bizyear, $month);
        $query = self::query()
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->orderBy('date', 'asc')
            ->orderBy('grade_id', 'asc');
        return $query->get();
    }

    public static function bizweeklist($date, $grade_id)
    {
        $calendar = Calendar::row($date);
        if(empty($calendar)) {
            return [];
        }
        return self::query()
            ->where('bizyear', '=', $calendar->bizyear)
            ->where('bizweek', '=', $calendar->bizweek)
            ->where('grade_id', '=', $grade_id)
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function eventlist($event_id)
    {
        $query = self::query()
            ->where('event_id', '=', $event_id)
            ->orderBy('grade_id', 'asc');
        return $query->get();
    }

    // public static function _bizweeklist($bizyear, $bizweek, $grade_id)
    // {
    //     return self::query()
    //         ->where('bizyear', '=', $bizyear)
    //         ->where('bizweek', '=', $bizweek)
    //         ->where('grade_id', '=', $grade_id)
    //         ->orderBy('date', 'asc')
    //         ->get();
    // }

    public static function count_bizyear($bizyear)
    {
        $kindergarten_id = 1;
        $query = self::query()
            ->select( DB::raw('count(`id`) as `count`') )
            ->where('kindergarten_id', '=', $kindergarten_id)
            ->where('bizyear', '=', $bizyear)
            ->first();
        return  $query->count;
    }

    public static function creates_bizyear($bizyear)
    {
        $calendars = Calendar::list_bizyear($bizyear);
        self::creates($calendars);
    }


    // 検証
    // 同じ日に複数存在する。
    // event_idは年度のよって異なる。
    // eventsテーブルにコピー追加後の新しいevent_idに書き換える必要がある。
    public static function creates($calendars)
    {
        $kindergarten_id = 1;
        $grades = Grade::list();

        DB::beginTransaction();
        try {
            foreach ($calendars as $calendar) {
                foreach ($grades as $grade) {
                    $grade_id = $grade->id;
                    $bizyear = $calendar->bizyear;
                    $bizweek = $calendar->bizweek;
                    $date = $calendar->date;
                    $week_idx = $calendar->week_idx;

                    $recode = self::query()->where('date', $date)->where('kindergarten_id', $kindergarten_id)->where('grade_id', $grade_id)->first();
                    if (empty($recode)) {
                        // 前年度データがあればコピー用に取得
                        $before = self::query()->where('bizyear', $bizyear-1)->where('bizweek', $bizweek)->where('week_idx', $week_idx)->where('kindergarten_id', $kindergarten_id)->where('grade_id', $grade_id)->first();
                        // ex test $before = self::query()->where('bizyear', 2020)->where('bizweek', 2)->where('week_idx', 1)->where('kindergarten_id', 1)->where('grade_id', 1)->first();
                        $before_action = ($before->action) ?? '';
                        $before_action_id = ($before->event_id) ?? '';

                        self::create([
                            'kindergarten_id' => $kindergarten_id,
                            'bizyear' => $bizyear,
                            'bizweek' => $bizweek,
                            'week_idx' => $week_idx,
                            'date' => $date,
                            'grade_id' => $grade_id,
                            'action' => $before_action,
                            'event_id' => $before_action_id,
                        ]);
                    }
                }
            }
            DB::commit();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
        }
    }

    public static function copy_by_bizyear($bizyear)
    {
        $kindergarten_id = 1;
        $before_actions = self::query()
            ->where('bizyear', '=', $bizyear-1)
            ->orderBy('id', 'asc')
            ->get();
        foreach ($before_actions as $before_action) {
            $calendar = Calendar::get_date($bizyear, $before_action->bizweek, $before_action->week_idx);
            if( empty($calendar) ) {
                continue;
            }
            $event_id = 0;
            if( ! empty($before_action->event_id) ) {
                $event_id = Event::new_event_id($before_action->event_id);
                $event_id = empty($event_id)? 0: $event_id;
            }

            DB::beginTransaction();
            try {
                $insert = self::create([
                    'kindergarten_id' => $kindergarten_id,
                    'bizyear' => $calendar->bizyear,
                    'bizweek' => $calendar->bizweek,
                    'week_idx' => $calendar->week_idx,
                    'date' => $calendar->date,
                    'grade_id' => $before_action->grade_id,
                    'action' => $before_action->action,
                    'event_id' => $event_id,
                ]);
                DB::commit();
            }
            catch (\Throwable $ex) {
                DB::rollBack();
                report($ex); // ログ出力
            }
        }
    }

    public static function insert($date, $grade_id, $action, $event_id)
    {
        $kindergarten_id = 1;
        $calendar = Calendar::row($date);
        DB::beginTransaction();
        try {
            $insert = self::create([
                'kindergarten_id' => $kindergarten_id,
                'bizyear' => $calendar->bizyear,
                'bizweek' => $calendar->bizweek,
                'week_idx' => $calendar->week_idx,
                'date' => $date,
                'grade_id' => $grade_id,
                'action' => $action,
                'event_id' => $event_id,
            ]);
            DB::commit();
            return $insert->id;
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return '';
        }
    }

}
