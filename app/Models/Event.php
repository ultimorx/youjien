<?php

namespace App\Models;

use App\Models\Calendar;
use App\Models\Action;
use App\Util\Date;
use Illuminate\Support\Facades\DB;

class Event extends SoftDeleteModel
{
    const TYPE_IN  = 10; //園内行事
    const TYPE_OUT = 20; //園外行事

    public static function monthlist($bizyear, $month)
    {
        // $count = self::count_bizyear($bizyear);
        // if($count == 0) {
        //     self::creates_bizyear($bizyear);
        // }

        $year = Date::bizyear_to_year($bizyear, $month);
        return self::query()
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function bizweeklist($date)
    {
        $calendar = Calendar::row($date);
        if(empty($calendar)) {
            return [];
        }
        return self::query()
            ->where('bizyear', '=', $calendar->bizyear)
            ->where('bizweek', '=', $calendar->bizweek)
            ->get();
    }

    public static function _bizweeklist($bizyear, $bizweek)
    {
        return self::query()
            ->where('bizyear', '=', $bizyear)
            ->where('bizweek', '=', $bizweek)
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function new_event_id($before_id)
    {
        $event = self::query()
            ->where('before_id', '=', $before_id)
            ->first();
        return empty($event)? '': $event->id;
    }

    public static function copy_by_bizyear($bizyear)
    {
        $kindergarten_id = 1;
        $before_events = self::query()
            ->where('bizyear', '=', $bizyear-1)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($before_events as $before_event) {
            $calendar = Calendar::get_date($bizyear, $before_event->bizweek, $before_event->week_idx);
            if( empty($calendar) ) {
                continue;
            }
            DB::beginTransaction();
            try {
                $insert = self::create([
                    'kindergarten_id' => $kindergarten_id,
                    'bizyear' => $bizyear,
                    'bizweek' => $calendar->bizweek,
                    'week_idx' => $calendar->week_idx,
                    'date' => $calendar->date,
                    'name' => $before_event->name,
                    'type' => $before_event->type,
                    'note' => '',
                    'before_id' => $before_event->id,
                ]);
                DB::commit();
            }
            catch (\Throwable $ex) {
                DB::rollBack();
                report($ex); // ログ出力
            }
        }
    }

    public static function insert($date, $event_name, $event_type)
    {
        $kindergarten_id = 1;
        $calendar = Calendar::row($date);
        if( empty($calendar) ) {
            return;
        }
        DB::beginTransaction();
        try {
            $insert = self::create([
                'kindergarten_id' => $kindergarten_id,
                'bizyear' => $calendar->bizyear,
                'bizweek' => $calendar->bizweek,
                'week_idx' => $calendar->week_idx,
                'date' => $date,
                'name' => $event_name,
                'type' => $event_type,
                'note' => '',
                'before_id' => 0,
            ]);
            DB::commit();
            return $insert->id;
        }
        catch (\Throwable $ex) {
            dd($ex);
            DB::rollBack();
            report($ex); // ログ出力
            return '';
        }
    }

    public static function save_update($event, $event_date, $input_date, $input_name)
    {
        $actions = array();
        $is_date_update = false;
        if( ! is_null($input_date) && ($event_date != $input_date))
        {
            $calendar = Calendar::row($input_date);
            if( $calendar ) {
                $is_date_update = true;
            }
        }
        try {
            $event['name'] = $input_name;
            if($is_date_update) {
                // events更新
                $event['date'] = $input_date;
                $event['bizyear'] = $calendar->bizyear;
                $event['bizweek'] = $calendar->bizweek;
                $event['week_idx'] = $calendar->week_idx;

                $event_id = $event['id'];
                // actions更新
                DB::table('actions')
                    ->where('event_id', $event_id)
                    ->update([
                        'date' => $input_date,
                        'bizyear' => $calendar->bizyear,
                        'bizweek' => $calendar->bizweek,
                        'week_idx' => $calendar->week_idx,
                    ]);
                $actions = Action::eventlist($event_id);
            }
            $event->save();

            DB::commit();

            return array(
                'event' => $event,
                'actions' => $actions,
            );
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }
}
