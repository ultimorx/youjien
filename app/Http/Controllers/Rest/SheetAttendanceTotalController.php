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

class SheetAttendanceTotalController extends CommonController
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
        $filename = '出席統計集計_'.$classroom->name.'_'.$bizyear.'年度'.$month.'月'.'.csv';
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
        $firstline = array();
        $secondline = array(
            '出席番号',
            '園児名',
            // '園児かな',
            '出停忌引等',
            '出席日数',
            '欠席日数',
            '遅刻',
            '早退',
            '出停忌引等',
            '出席日数',
            '欠席日数',
            '遅刻',
            '早退',
        );

        // 見出し配列、合計配列の初期化
        $attendance_totals = [];
        for ($i=0; $i<count($secondline); $i++) {
            $firstline[] = '';
            $attendance_totals[] = 0;
        }
        $firstline[2] = '今月';
        $firstline[7] = '累積';

        $rosters = self::_roster_classroom($classroom_id, $year, $month);
        foreach ($rosters as $roster) {
            $line = array();
            $line[] = $roster->number;
            $line[] = $roster->child->name;
            // $line[] = $roster->child->kana;

            // 指定した年月の園児の出欠データを取得
            $attendance = self::_attendance_count($roster->id, $year, $month);
            $line[] = $attendance->count_kibiki;
            $line[] = $attendance->count_attendances_true;
            $line[] = $attendance->count_attendances_false;
            $line[] = $attendance->count_late;
            $line[] = $attendance->count_early;
            $attendance_totals[2] += $attendance->count_kibiki;
            $attendance_totals[3] += $attendance->count_attendances_true;
            $attendance_totals[4] += $attendance->count_attendances_false;
            $attendance_totals[5] += $attendance->count_late;
            $attendance_totals[6] += $attendance->count_early;

            // 指定した年月までの累積を取得
            $attendance = self::_attendance_count($roster->id, $year, $month, $total_flg=true);
            $line[] = $attendance->count_kibiki;
            $line[] = $attendance->count_attendances_true;
            $line[] = $attendance->count_attendances_false;
            $line[] = $attendance->count_late;
            $line[] = $attendance->count_early;
            $attendance_totals[7] += $attendance->count_kibiki;
            $attendance_totals[8] += $attendance->count_attendances_true;
            $attendance_totals[9] += $attendance->count_attendances_false;
            $attendance_totals[10] += $attendance->count_late;
            $attendance_totals[11] += $attendance->count_early;

            $sheet[] = $line;
        }

        // 合計行 0を空白文字に変更
        foreach ($attendance_totals as $key => $value) {
            if($value == 0) $attendance_totals[$key] = '';
        }

        // 添字を初期化
        $attendance_totals = array_values($attendance_totals);

        $sheet[] = $attendance_totals;

        array_unshift($sheet, $secondline);
        array_unshift($sheet, $firstline);

        return $sheet;
    }

    private static function _roster_classroom($classroom_id, $year, $month) {
        $query = Roster::with(['child'])
            ->select('rosters.*')
            ->leftJoin('children', 'children.id', '=', 'rosters.child_id')
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
            ->where('rosters.classroom_id', '=', $classroom_id)
            ->orderBy('rosters.number', 'asc')
            // ->groupBy('rosters.id')
        ;
        return $query->get();
    }

    private static function _attendance_count($roster_id, $year, $month, $total_flg=false) {
        $query = Attendance::query()
            ->select(
                // 条件付きカウント件数取得
                DB::raw('count(`attendances`.`attendance` = ' . Attendance::ATTENDANCE_TRUE . ' or null) as `count_attendances_true`'),
                DB::raw('count(`attendances`.`attendance` = ' . Attendance::ATTENDANCE_FALSE . ' or null) as `count_attendances_false`'),
                DB::raw('count(`attendances`.`late`) as `count_late`'),
                DB::raw('count(`attendances`.`early`) as `count_early`'),
                DB::raw(
                    'count(
                        (
                            `diseases`.`absence_type` = ' . Disease::ABSENCE_TYPE_KIBIKI . '
                            or
                            `diseases`.`absence_type` = ' . Disease::ABSENCE_TYPE_TEISHI . '
                        )
                        or null
                    ) as `count_kibiki`'
                )
            )
            ->leftJoin('diseases', 'diseases.id', '=', 'attendances.diseases_id')
            ->where('attendances.roster_id', '=', $roster_id)
            ;

        if($total_flg){
            $query
                ->where(function($query) use($year, $month) {
                    $query
                    ->where('attendances.date', 'LIKE', Date::year_month($year, $month).'%')
                    ->orWhere('attendances.date', '<', Date::date($year, $month, 1));
                });
        } else {
            $query->where('attendances.date', 'LIKE', Date::year_month($year, $month).'%');
        }

        // dd($query->toSql());
        // return $query->first();

        // 出停忌引等は欠席に含めない
        $attendance = $query->first();
        $attendance->count_attendances_false = $attendance->count_attendances_false - $attendance->count_kibiki;
        return $attendance;
    }
}
