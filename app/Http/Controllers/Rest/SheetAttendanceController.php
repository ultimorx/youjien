<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Roster;
use App\Models\Child;
use App\Models\Attendance;
use App\Models\Disease;
use App\Util\Csv;
use App\Util\Date;
use App\Util\Time;
use App\Util\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SheetAttendanceController extends CommonController
{
    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $classroom_id = $request->get('classroom_id');
        $classroom = Classroom::query()->where('id', '=', $classroom_id)->first();
        $sheet = self::_createSheet($bizyear, $month, $classroom_id);
        $filename = '出席統計_'.$classroom->name.'_'.$bizyear.'年度'.$month.'月'.'.csv';
        $file = Csv::export($sheet, $filename);
        return self::_download($file);
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $classroom_id = $request->get('classroom_id');
        $sheet = self::_createSheet($bizyear, $month, $classroom_id);
        return response()->json($sheet);
    }

    /**
     * 契約者一覧用の配列データ
     */
    private static function _createSheet(int $bizyear, int $month, int $classroom_id)
    {
        $year = Date::bizyear_to_year($bizyear, $month);
        $lastday = Date::lastday($year, $month);
        $sheet = array();
        // 見出し行
        $firstline = array(
            '出席番号',
            '園児名',
            // '園児かな',
        );
        for ($day=1; $day<=$lastday; $day++) {
            $date = Date::date($year,$month,$day);
            $week = Date::week($date);
            $firstline[] = $day.$week;
        }

        // 欠席理由
        // $diseases = Disease::query()->orderBy('order')->get(); // 22.9.12無効化
        $diseases = Disease::list();
        $disease_names = [];
        foreach ($diseases as $disease) {
            $disease_names[$disease->id] = Disease::absence_types_mark($disease->absence_type);
        }

        // 合計配列の初期化
        $attendance_totals = [];
        for ($day=1; $day<=$lastday; $day++) {
            $attendance_totals[$day] = 0;
        }

        $rosters = self::_roster_classroom($classroom_id, $year, $month);
        foreach ($rosters as $roster) {
            $line = array();
            $line[] = $roster->number;
            $line[] = $roster->child->name;
            // $line[] = $roster->child->kana;

            // 指定した年月の園児の出欠データを取得
            $attendances = self::_attendances($roster->id, $year, $month);
            $attendance_dates = array();
            foreach ($attendances as $attendance) {
                $attendance_dates[$attendance->date] = $attendance;
            }

            for ($day=1; $day<=$lastday; $day++) :
                $date = Date::date($year,$month,$day);

                if( ! array_key_exists($date, $attendance_dates) ) {
                    $line[] = '';
                    continue;
                }

                $attendance = $attendance_dates[$date];

                if ($attendance->attendance == Attendance::ATTENDANCE_TRUE) {
                    // 出席
                    $at = '1';

                    if (!empty($attendance->late)) {
                        // 遅刻
                        $at .= Attendance::LATE_MARK;
                    }
                    if (!empty($attendance->early)) {
                        // 早退
                        $at .= Attendance::EARLY_MARK;
                    }
                    $line[] = $at;

                    $attendance_totals[$day]++;
                } elseif ($attendance->attendance === Attendance::ATTENDANCE_FALSE) {
                    // 欠席
                    // 欠席理由はあるか
                    $disease_name = '未';
                    if ( ! empty($attendance->diseases_id) && isset($disease_names[$attendance->diseases_id])) {
                        $disease_name = $disease_names[$attendance->diseases_id]?: '未';
                    }
                    // $line[] = '.';
                    // $line[] = $attendance->diseases_id;
                    $line[] = $disease_name;
                } else {
                    // 出席／欠席の未選択
                    $line[] = '';
                }

            endfor;
            $sheet[] = $line;
        }

        // 合計行 0を空白文字に変更
        foreach ($attendance_totals as $key => $value) {
            if($value == 0) $attendance_totals[$key] = '';
        }

        // 添字を初期化
        $attendance_totals = array_values($attendance_totals);
        // 合計行の先頭3列分を挿入
        array_unshift($attendance_totals, '出席合計');
        // array_unshift($attendance_totals, '');
        array_unshift($attendance_totals, '');

        $sheet[] = $attendance_totals;

        array_unshift($sheet, $firstline);

        return $sheet;
    }

    private static function _roster_classroom($classroom_id, $year, $month) {
        $query = Roster::with(['child'])
            ->select('rosters.*')
            ->leftJoin('children', 'children.id', '=', 'rosters.child_id')
            ->where('rosters.classroom_id', '=', $classroom_id)
            ->where(function($query) use($year, $month) {
                    $query
                    ->where('children.move_in_date', '=', Child::MOVE_IN_DATE_DEFAULT)
                    ->orWhere('children.move_in_date', 'LIKE', Date::year_month($year, $month).'%')
                    ->orWhere('children.move_in_date', '<', Date::year_month($year, $month).'-01')
                    ;
                })
            ->where(function($query) use($year, $month) {
                    $query
                    ->where('children.move_out_date', '=', Child::MOVE_OUT_DATE_DEFAULT)
                    ->orWhere('children.move_out_date', 'LIKE', Date::year_month($year, $month).'%')
                    ->orWhere('children.move_out_date', '>', Date::year_month($year, $month).'-01')
                    ;
                })
            ->orderBy('rosters.number', 'asc')
            // ->groupBy('rosters.id')
        ;
        return $query->get();
    }

    private static function _attendances($roster_id, $year, $month) {
        $query = Attendance::query()
            ->where('attendances.roster_id', '=', $roster_id)
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ;
        return $query->get();
    }

}
