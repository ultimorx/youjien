<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Calendar;
use App\Models\Classroom;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function _index(Request $request)
    {
        // $bizyear = $request->get('bizyear');
        // $month = $request->get('month');
        // $year = Date::bizyear_to_year($bizyear, $month);
        // Calendar::creates($year, $month);
        // $calendars = Calendar::list($year, $month);

        $lists = array();
        foreach ($calendars as $calendar) {
            $line = array();
            $line['id'] = $calendar->id;
            $line['date'] = $calendar->date;
            $line['ijyouji'] = $calendar->ijyouji;
            $line['mimanji'] = $calendar->mimanji;
            $line['week'] = Date::week($calendar->date);
            $line['week_idx'] = Date::week_idx($calendar->date);
            $lists[] = $line;
        }

        return response()->json($lists);
    }

    /**
     * 毎の最初の日
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function biz_week(Request $request)
    {
        $date = $request->get('date');
        $classroom_id = $request->get('classroom_id');

        // クラスIDの年度チェック　クラスの年度と日付の年度は同じか？
        $date_bizyar = Date::bizyear($date);
        $classroom_bizyear = Classroom::get_bizyear($classroom_id);
        if ($date_bizyar != $classroom_bizyear) {
            return [];
        }

        // 日報を取得
        return Report::list_week_days($date, $classroom_id);
    }

    /**
     * 保存
     * @param Report $report
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Report $report, Request $request)
    {
        try {
            $report['life'] = $request->get('life');
            $report['health'] = $request->get('health');
            $report->save();

            DB::commit();
            return response()->json($report);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

}
