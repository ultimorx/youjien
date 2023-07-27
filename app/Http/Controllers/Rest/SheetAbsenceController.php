<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Roster;
use App\Models\Attendance;
use App\Models\Disease;
use App\Util\Csv;
use App\Util\Date;
use App\Util\Time;
use App\Util\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SheetAbsenceController extends CommonController
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
        if(empty($classroom_id)) {
            $classroom_name = '園全体';
        } else {
            $classroom = Classroom::query()->where('id', '=', $classroom_id)->first();
            $classroom_name = $classroom->name;
        }

        $sheet = self::_createSheet($bizyear, $month, $classroom_id);
        $filename = '欠席集計表_'.$classroom_name.'_'.$bizyear.'年度'.$month.'月'.'.csv';
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
            '',
            '',
            '',
        );
        for ($day=1; $day<=$lastday; $day++) {
            $date = Date::date($year,$month,$day);
            $week = Date::week($date);
            $firstline[] = $day.$week;
        }
        $firstline[] = '計';

        // 小計配列と合計配列の初期化
        $init_totals = [];
        for ($day=1; $day<=$lastday; $day++) $init_totals[$day] = 0;
        $sub_totals = $init_totals;
        $attendance_totals = $init_totals;

        $sheet_disease_types = array(
            Disease::ABSENCE_TYPE_TEISHI,
            Disease::ABSENCE_TYPE_KIBIKI,
            Disease::ABSENCE_TYPE_BYOUKETSU,
            Disease::ABSENCE_TYPE_JIKOKETSU,
        );
        foreach ($sheet_disease_types as $row_idx => $disease_type) :
            // $diseases = Disease::query()->where('absence_type', '=', $disease_type)->orderBy('order')->get(); // 22.9.12無効化
            $diseases = Disease::list($disease_type);

            foreach ($diseases as $cell_idx => $disease) :
                $line = array();

                // 1列目
                if($cell_idx == 0 && $disease_type == Disease::ABSENCE_TYPE_TEISHI) {
                    $line[] = '出停忌引等';
                } elseif ($cell_idx == 0 && $disease_type == Disease::ABSENCE_TYPE_BYOUKETSU) {
                    $line[] = '欠席';
                } else {
                    $line[] = '';
                }

                $line[] = ($cell_idx != 0)? '' : Disease::absence_type_name($disease_type).'('.Disease::absence_types_mark($disease_type).')';
                $line[] = $disease->name;

                // 指定した年月の欠席データを取得
                $attendances = self::_attendance_disease_count($disease->id, $year, $month, $classroom_id);
                $attendance_dates = array();
                foreach ($attendances as $attendance) {
                    $attendance_dates[$attendance->date] = $attendance;
                }

                $row_total = 0;
                for ($day=1; $day<=$lastday; $day++) :
                    $date = Date::date($year,$month,$day);

                    if( ! array_key_exists($date, $attendance_dates) ) {
                        $line[] = '';
                        continue;
                    }

                    $count = $attendance_dates[$date]->count;
                    $line[] = $count;
                    $row_total += $count;
                    $sub_totals[$day] += $count;
                    $attendance_totals[$day] += $count;

                endfor;
                $line[] = $row_total;
                $sheet[] = $line;

                // 忌引きまでデータ代入後、集計行を挿入
                if($disease_type == Disease::ABSENCE_TYPE_KIBIKI || $disease_type == Disease::ABSENCE_TYPE_JIKOKETSU) {
                    $totals = $sub_totals;
                    $sub_totals = $init_totals;
                    $totals[] = array_sum($totals);

                    $midashi = '';
                    if($disease_type == Disease::ABSENCE_TYPE_KIBIKI) $midashi = '出席停止小計';
                    if($disease_type == Disease::ABSENCE_TYPE_JIKOKETSU) $midashi = '欠席小計';
                    // 合計行の先頭3列分を挿入
                    array_unshift($totals, $midashi);
                    array_unshift($totals, '');
                    array_unshift($totals, '');
                    $sheet[] = $totals;
                }
            endforeach;
        endforeach;

        // 合計行 0を空白文字に変更
        // foreach ($attendance_totals as $key => $value) if($value == 0) $attendance_totals[$key] = '';

        $attendance_totals[] = array_sum($attendance_totals);
        // 合計行の先頭3列分を挿入
        array_unshift($attendance_totals, '欠席合計');
        array_unshift($attendance_totals, '');
        array_unshift($attendance_totals, '');

        $sheet[] = $attendance_totals;

        array_unshift($sheet, $firstline);

        return $sheet;
    }

    private static function _attendance_disease_count($disease_id, $year, $month, $classroom_id) {
        $query = Attendance::query()
            ->select(
                'attendances.date',
                DB::raw('count(`attendances`.`diseases_id`) as `count`')
            )
            ->where('attendances.diseases_id', '=', $disease_id)
            ->where('attendances.date', 'LIKE', Date::year_month($year, $month).'%')
            ->groupby('attendances.date');
            ;

        if( ! empty($classroom_id)) {
            $query
                ->leftJoin('rosters', 'rosters.id', '=', 'attendances.roster_id')
                ->where('rosters.classroom_id', '=', $classroom_id)
            ;
        }
        return $query->get();
    }

}
