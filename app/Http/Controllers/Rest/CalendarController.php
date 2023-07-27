<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\Classroom;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $year = Date::bizyear_to_year($bizyear, $month);
        // 年月指定でカレンダーを作成する処理は無効化 21.11.4
        // ここで作成する処理は、年度管理がまだ導入される前にしたために必要だった。
        // 年度管理が導入されたため不要となる。
        // Calendar::creates($year, $month);
        $calendars = Calendar::list($year, $month);

        $lists = array();
        foreach ($calendars as $calendar) {
            $line = array();
            $line['id'] = $calendar->id;
            $line['date'] = $calendar->date;
            $line['ijyouji'] = $calendar->ijyouji;
            $line['mimanji'] = $calendar->mimanji;
            $line['note'] = $calendar->note;
            $line['week'] = Date::week($calendar->date);
            $line['week_idx'] = Date::week_idx($calendar->date);
            $lists[] = $line;
        }

        return response()->json($lists);
    }

    /**
     * 休み
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getdayoff(Request $request)
    {
        $date = $request->get('date');
        $classroom_id = $request->get('classroom_id');
        $grade = Classroom::get_grade($classroom_id);

        $calendar = new Calendar;
        $is_dayoff = $calendar->is_dayoff($date, $grade);
        $dayoff = ($is_dayoff)? Calendar::DAYOFF_TRUE_TEXT: Calendar::DAYOFF_FALSE_TEXT;
        return response()->json($dayoff);
    }

    /**
     * 休み
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function getdayoffs(Request $request)
    {
        $date = $request->get('date');
        $_calendar = new Calendar;
        $calendar = $_calendar->get_calendar($date);
        $dayoffs = [
            'mimanji' => Calendar::mimanji_dayoff_text($calendar),
            'ijyouji' => Calendar::ijyouji_dayoff_text($calendar)
        ];
        return response()->json($dayoffs);
    }

    /**
     * 保存
     * @param Calendar $calendar
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function dayoff(Calendar $calendar, Request $request)
    {
        $ijyouji = ($request->get('ijyouji') == Calendar::DAYOFF_TRUE) ? Calendar::DAYOFF_TRUE: Calendar::DAYOFF_FALSE;
        $mimanji = ($request->get('mimanji') == Calendar::DAYOFF_TRUE) ? Calendar::DAYOFF_TRUE: Calendar::DAYOFF_FALSE;
        try {
            $calendar['ijyouji'] = $ijyouji;
            $calendar['mimanji'] = $mimanji;
            $calendar->save();

            DB::commit();
            return response()->json($calendar);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 保存
     * @param Calendar $calendar
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function save_note(Calendar $calendar, Request $request)
    {
        $note = $request->get('note');
        try {
            $calendar['note'] = $note;
            $calendar->save();

            DB::commit();
            return response()->json($calendar);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 年度週の設定
     */
    public function set_bizweek()
    {
        Calendar::update_bizweek();
        echo '年度週の設定を行いました。Calendar::update_bizweek()';
        return '';
    }

    /**
     * 閲覧動作
     */
    public function test()
    {
        $d = Calendar::list_week_first_days($bizyear=2021);
        // dd(count($d), $d);
        foreach($d as $v){
            echo $v->date.'<br>';
        }
    }
}
