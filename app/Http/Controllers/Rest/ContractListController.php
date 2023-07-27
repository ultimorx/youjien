<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\EveningTime;
use App\Models\MorningTime;
use App\Models\Classroom;
use App\Models\ContractEvening;
use App\Models\ContractMorning;
use App\Models\Roster;
use App\Models\Attendance;
use App\Util\Csv;
use App\Util\Date;
use App\Util\Time;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractListController extends CommonController
{
    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $sheet = self::_createSheet($bizyear, $month);
        $filename = '時間外保育契約者一覧_'.$bizyear.'年度'.$month.'月'.'.csv';
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
        $sheet = self::_createSheet($bizyear, $month, $id_flag=true);
        return response()->json($sheet);
    }

    /**
     * 契約者一覧用の配列データ
     */
    private static function _createSheet(int $bizyear, int $month, $id_flag=false)
    {
        $year = Date::bizyear_to_year($bizyear, $month);
        $sheet = array();
        // 見出し行
        $firstline = array(
            'クラス名',
            '園児名',
            'かな',
            '早朝保育',
            '延長保育',
            '延長超過回数',
            '延長超過時間',
        );

        if($id_flag) {
            // 先頭列に追加
            array_unshift($firstline, '個人月詳細');
        }

        $rosters = self::_roster_contract($bizyear, $month);
        $morning_total = 0;
        $evening_total = 0;
        foreach ($rosters as $roster) {
            $line = array();
            if($id_flag) {
                $line[] = $roster->id;
            }
            $line[] = $roster->classroom->name;
            $line[] = $roster->child->name; //$roster->number . ' ' .
            $line[] = $roster->child->kana;
            $line[] = Time::hour_minute($roster->morning_time);
            $line[] = Time::hour_minute($roster->evening_time);
            if( ! empty($roster->morning_time)) {
                $morning_total++;
            }
            if( ! empty($roster->evening_time)) {
                $evening_total++;
                $evening_overtime = self::_attendance_evening_overtime($roster->id, $roster->evening_time, $year, $month);
                $line[] = $evening_overtime->count;
                $line[] = Time::hour_minute($evening_overtime->total_time);
            } else {
                $line[] = '';
                $line[] = '';
            }

            $sheet[] = $line;
        }

        $totalline = array('合計', '', '', $morning_total.'人', $evening_total.'人', '', '');
        if($id_flag) {
            $totalline = array('合計', '', '', '', $morning_total.'人', $evening_total.'人', '', '');
        }

        $sheet[] = $totalline;

        array_unshift($sheet, $firstline);

        return $sheet;
    }

    private static function _roster_contract($bizyear, $month) {
        $query = Roster::with([
                'child',
                'classroom' => function ($query) use ($bizyear) {
                    $query->where('bizyear', '=', $bizyear);
                },
                'classroom.grade',
                'contract_mornings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                },
                'contract_evenings' => function ($query) use ($month) {
                    $query->where('month', '=', $month);
                }
            ])
            ->select(
                'rosters.*',
                'contract_mornings.morning_time_id',
                'morning_times.time as morning_time',
                'contract_evenings.evening_time_id',
                'evening_times.time as evening_time'
                )
            ->join('classrooms', function ($join) use($bizyear) { // 内部結合
                $join
                ->on('classrooms.id', '=', 'rosters.classroom_id')
                ->where('classrooms.bizyear', '=', $bizyear)
                ;
            })
            ->leftJoin('contract_mornings', function ($join) use($month)  { // 外部結合
                $join
                ->on('contract_mornings.roster_id', '=', 'rosters.id')
                ->where('contract_mornings.month', '=', $month)
                ;
            })
            ->leftJoin('contract_evenings', function ($join) use($month) { // 外部結合
                $join
                ->on('contract_evenings.roster_id', '=', 'rosters.id')
                ->where('contract_evenings.month', '=', $month)
                ;
            })
            ->leftJoin('morning_times', 'morning_times.id', '=', 'contract_mornings.morning_time_id')
            ->leftJoin('evening_times', 'evening_times.id', '=', 'contract_evenings.evening_time_id')
            ->join('children', 'children.id', '=', 'rosters.child_id')
            ->where('contract_mornings.morning_time_id', '<>', '')
            ->orWhere('contract_evenings.evening_time_id', '<>', '')
            ->orderBy('classrooms.order', 'asc')
            ->orderBy('rosters.number', 'asc')
        ;
        // dd($query->toSql());

        return $query->get();
    }

    private static function _attendance_evening_overtime($roster_id, $evening_time, $year, $month) {
        // $evening_time = '14:00:00';
        $query = Attendance::query()
            ->select(
                DB::raw('count(`outtime`) as `count`'),
                // DB::raw('sum( time_to_sec(`outtime`)) as `total_sec`'),
                // DB::raw('sec_to_time(sum( time_to_sec(`outtime`))) as `total_time`'),
                DB::raw('sec_to_time(sum( time_to_sec(`outtime`) - time_to_sec("' . $evening_time . '") )) as `total_time`')
            )
            ->where('roster_id', '=', $roster_id)
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ->whereNotNull('outtime')
            ->whereRaw('`outtime` > "'.$evening_time.'"')
            // ->where('outtime', '>', $evening_time) // オペラントエラーになるため、whereRawを使用
            ;
        // dd($query->toSql());
        return $query->first();
    }

}
