<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Aim;
use App\Models\Calendar;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AimController extends Controller
{
    // /**
    //  * 一覧表示
    //  * @param  Request $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function x_index(Request $request)
    // {
    //     // $bizyear = $request->get('bizyear');
    //     // $month = $request->get('month');
    //     // $year = Date::bizyear_to_year($bizyear, $month);
    //     // Calendar::creates($year, $month);
    //     // $calendars = Calendar::list($year, $month);
    //
    //     $lists = array();
    //     foreach ($calendars as $calendar) {
    //         $line = array();
    //         $line['id'] = $calendar->id;
    //         $line['date'] = $calendar->date;
    //         $line['ijyouji'] = $calendar->ijyouji;
    //         $line['mimanji'] = $calendar->mimanji;
    //         $line['week'] = Date::week($calendar->date);
    //         $line['week_idx'] = Date::week_idx($calendar->date);
    //         $lists[] = $line;
    //     }
    //
    //     return response()->json($lists);
    // }

    /**
     * 週の最初の日（月曜）の一覧
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function biz_weekly_first_days(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $grade_id = $request->get('grade_id');
        $days = Aim::list_week_first_days($bizyear, $grade_id);
        return response()->json($days);
    }

    /**
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function week(Request $request)
    {
        $date = $request->get('date');
        $grade_id = $request->get('grade_id');
        $aim = Aim::row($date, $grade_id);
        return response()->json($aim);
    }

    /**
     * 保存
     * @param Report $aim
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Aim $aim, Request $request)
    {
        try {
            $aim['play'] = $request->get('play');
            $aim['life'] = $request->get('life');
            $aim['note'] = $request->get('note');
            $aim->save();

            DB::commit();
            return response()->json($aim);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }
}
