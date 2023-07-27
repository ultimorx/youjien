<?php

namespace App\Models;

use App\Models\Grade;
use App\Util\Date;
use Illuminate\Support\Facades\DB;

class Calendar extends SoftDeleteModel
{
    const DAYOFF_TRUE = 1;
    const DAYOFF_FALSE = 0;
    const DAYOFF_TRUE_TEXT = '休み';
    const DAYOFF_FALSE_TEXT = '登園';
    const DAYOFF_TYPE_IJYOUJI = 'ijyouji';
    const DAYOFF_TYPE_MIMANJI = 'mimanji';

    public $calendars;

    public static function list($year, $month)
    {
        return self::query()
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function list_bizyear($bizyear)
    {
        return self::query()
            ->where('bizyear', $bizyear)
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function list_week_days($date)
    {
        $d = self::query()
            ->where('date', '=', $date)
            ->first();

        return self::query()
            ->where('bizyear', '=', $d->bizyear)
            ->where('bizweek', '=', $d->bizweek)
            ->orderBy('date', 'asc')
            ->get();
    }

    // 4/1と毎週月曜を取得
    public static function list_week_first_days($bizyear)
    {
        return self::query()
            ->where(function($query) use($bizyear) {
                $query
                ->where('bizyear', '=', $bizyear)
                ->where('week_idx', '=', Date::WEEK_MON) // 月曜
                ;
            })
            ->orWhere('date', '=', $bizyear.'-04-01')
            ->orderBy('date', 'asc')
            ->get();
    }

    // 毎週月曜のみを取得
    public static function x_list_week_first_days($bizyear)
    {
        return self::query()
            ->where('bizyear', '=', $bizyear)
            ->where('week_idx', '=', Date::WEEK_MON) // 月曜
            ->orderBy('date', 'asc')
            ->get();
    }

    // 休み DBデータ取得
    public function set_calendar($date)
    {
        $calendar = self::query()
            ->where('date', '=', $date)
            ->first();
        $this->calendars[$date] = $calendar;
    }
    // 休み データ取得
    public function get_calendar($date)
    {
        if(empty($this->calendars[$date])) {
            $this->set_calendar($date);
        }
        return $this->calendars[$date];
    }

    // 休み判定
    public function is_dayoff($date, $grade)
    {
        $calendar = $this->get_calendar($date);
        if ( empty($calendar)) {
            return self::DAYOFF_FALSE;
        }
        if ( Grade::is_ijyouji($grade) ) {
            return self::is_dayoff_ijyouji($calendar);
        } elseif ( Grade::is_mimanji($grade) ) {
            return self::is_dayoff_mimanji($calendar);
        }
        return self::DAYOFF_FALSE;
    }

    // 休み判定：以上児
    public static function is_dayoff_ijyouji($calendar)
    {
        if ( empty($calendar->ijyouji)) {
            return self::DAYOFF_FALSE;
        }
        return ($calendar->ijyouji == self::DAYOFF_TRUE);
    }

    // 休み判定：未満児
    public static function is_dayoff_mimanji($calendar)
    {
        if ( empty($calendar->mimanji)) {
            return self::DAYOFF_FALSE;
        }
        return ($calendar->mimanji == self::DAYOFF_TRUE);
    }

    // 以上児表示
    public static function ijyouji_dayoff_text($calendar)
    {
        return (self::is_dayoff_ijyouji($calendar))? self::DAYOFF_TRUE_TEXT: self::DAYOFF_FALSE_TEXT;
    }

    // 未満児表示
    public static function mimanji_dayoff_text($calendar)
    {
        return (self::is_dayoff_mimanji($calendar))? self::DAYOFF_TRUE_TEXT: self::DAYOFF_FALSE_TEXT;
    }

    // 休みの日数
    public static function count_dayoff($year, $month, $grade)
    {
        if ( Grade::is_ijyouji($grade) ) {
            $type = self::DAYOFF_TYPE_IJYOUJI;
        } elseif (Grade::is_mimanji($grade)) {
            $type = self::DAYOFF_TYPE_MIMANJI;
        } else {
            return 0;
        }

        $dayoff = self::query()
            ->select(
                DB::raw('count(`id`) as `count`')
            )
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->where($type, '=', self::DAYOFF_TRUE)

            ->first();
        return  $dayoff->count;
    }

    public static function row($date)
    {
         return self::query()
            ->where('date', '=', $date)
            ->first();
    }

    public static function get_date($bizyear, $bizweek, $week_idx)
    {
         return self::query()
            ->where('bizyear', '=', $bizyear)
            ->where('bizweek', '=', $bizweek)
            ->where('week_idx', '=', $week_idx)
            ->first();
    }

    public static function create_by_bizyear($bizyear)
    {
        function _key($bizweek, $week_idx) { return $bizweek.'-'.$week_idx; }

        // 前年度の休みの日付を取得；以上児
        $calendars = self::query()
            ->where('bizyear', '=', $bizyear-1)
            ->where('ijyouji', '=', self::DAYOFF_TRUE)
            ->get();
        $before_ijyoujis = array();
        foreach ($calendars as $calendar) {
            $key = _key($calendar->bizweek, $calendar->week_idx);
            $before_ijyoujis[$key] = '';
        }

        // 前年度の休みの日付を取得；未満児
        $calendars = self::query()
            ->where('bizyear', '=', $bizyear-1)
            ->where('mimanji', '=', self::DAYOFF_TRUE)
            ->get();
        $before_mimanji = array();
        foreach ($calendars as $calendar) {
            $key = _key($calendar->bizweek, $calendar->week_idx);
            $before_mimanji[$key] = '';
        }

        $bizweek = 0;
        foreach (Date::BIZ_MONTHS as $month) { // 4月〜3月
            $year = Date::bizyear_to_year($bizyear, $month);
            $lastday = Date::lastday($year, $month);

            DB::beginTransaction();

            try {
                for ($day=1; $day <= $lastday; $day++) {
                    $date = Date::date($year, $month, $day);
                    $week_idx = Date::week_idx($date);

                    // 年度週は月曜始まり
                    if(Date::is_mon($week_idx)) {
                        $bizweek++;
                    }

                    // 土日を休みにする
                    $ijyouji = self::DAYOFF_FALSE;
                    $mimanji = self::DAYOFF_FALSE;
                    if(Date::is_sat($week_idx)) {
                        $ijyouji = self::DAYOFF_TRUE;
                    }
                    if(Date::is_sun($week_idx)) {
                        $ijyouji = self::DAYOFF_TRUE;
                        $mimanji = self::DAYOFF_TRUE;
                    }

                    // 前年度が休みか
                    $key = _key($bizweek, $week_idx);
                    if( array_key_exists($key, $before_ijyoujis) ) {
                        $ijyouji = self::DAYOFF_TRUE;
                    }
                    if( array_key_exists($key, $before_mimanji) ) {
                        $mimanji = self::DAYOFF_TRUE;
                    }

                    // 新規追加時のみ実行
                    $recode = self::where('date', $date)->first();
                    if (empty($recode)) {
                        $recode = self::create([
                            'date'     => $date,
                            'bizyear'  => $bizyear,
                            'bizweek'  => $bizweek,
                            'week_idx' => $week_idx,
                            'ijyouji'  => $ijyouji,
                            'mimanji'  => $mimanji,
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

    public static function update_bizweek()
    {
        $bizyears = self::query()
            ->select(
                DB::raw('`bizyear`')
            )
            ->groupBy('bizyear')
            ->get();

        $v = [];
        foreach ($bizyears as $d) {
            $calendars = self::query()
                ->where('bizyear', '=', $d->bizyear)
                ->orderBy('date', 'asc')
                ->get();

                $bizweek = 0;
                foreach ($calendars as $calendar) {
                    $v[] = $calendar->date;
                    $week_idx = Date::week_idx($calendar->date);
                    // 年度週は月曜始まり
                    if(Date::is_mon($week_idx)) {
                        $bizweek++;
                    }
                    $calendar['bizweek'] = $bizweek;
                    $calendar['week_idx'] = $week_idx;
                    $calendar->save();
                }
        }
        // dd($v);
    }


    public static function __create_by_bizyear($bizyear)
    {
        foreach (Date::BIZ_MONTHS as $month) {
            $year = Date::bizyear_to_year($bizyear, $month);
            self::creates($year, $month);
        }
    }

    public static function __creates($year, $month)
    {
        $lastday = Date::lastday($year, $month);

        DB::beginTransaction();

        try {
            for ($day=1; $day <= $lastday; $day++) {
                $date = Date::date($year, $month, $day);
                $ijyouji = self::DAYOFF_FALSE;
                $mimanji = self::DAYOFF_FALSE;

                // 土日を休みにする
                $week_idx = Date::week_idx($date);
                if(Date::is_sat($week_idx)) {
                  $ijyouji = self::DAYOFF_TRUE;
                }
                if(Date::is_sun($week_idx)) {
                  $ijyouji = self::DAYOFF_TRUE;
                  $mimanji = self::DAYOFF_TRUE;
                }

                $recode = self::where('date', $date)->first();
                if (empty($recode)) {
                    $recode = self::create([
                        'date' => $date,
                        'ijyouji' => $ijyouji,
                        'mimanji' => $mimanji,
                    ]);
                }
                // self::firstOrCreate(
                //     [
                //         'date' => $date,
                //     ],
                //     [
                //         'ijyouji' => self::DAYOFF_FALSE,
                //         'mimanji' => self::DAYOFF_FALSE,
                //     ]
                // );
            }
            DB::commit();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
        }
    }
}
