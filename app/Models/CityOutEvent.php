<?php

namespace App\Models;

use App\Util\Date;
use Illuminate\Support\Facades\DB;

class CityOutEvent extends CityDbModel
{
    protected $table = 'out_events';

    public static function list()
    {
        return self::query()
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

    }

    public static function bizyearlist($bizyear)
    {
        return self::query()
            ->where('bizyear', '=', $bizyear)
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function monthlist($bizyear, $month)
    {
        $year = Date::bizyear_to_year($bizyear, $month);
        return self::query()
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->orderBy('date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function bizyears()
    {
        return self::query()
            ->select(
                DB::raw('`bizyear`')
            )
            ->groupBy('bizyear')
            ->orderBy('bizyear', 'desc')
            ->get();
    }

    public static function insert($date, $name)
    {
        DB::beginTransaction();
        try {
            $insert = self::create([
                'bizyear' => Date::bizyear($date),
                'date' => $date,
                'name' => $name,
                'note' => '',
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

    public static function save_update($event, $date, $name)
    {
        try {
            $event['bizyear'] = Date::bizyear($date);
            $event['date'] = $date;
            $event['name'] = $name;
            $event->save();
            DB::commit();
            return $event;
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

}
