<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
// use App\Models\EveningTime;
// use App\Models\MorningTime;
// use App\Models\ContractEvening;
// use App\Models\ContractMorning;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Roster;
use App\Models\Attendance;
use App\Util\Csv;
use App\Util\Date;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractMonthController extends CommonController
{
    /**
     * 一覧ダウンロード
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $sheet = self::_createSheetCount($bizyear, $month);
        $filename = '預かり人数表_'.$bizyear.'年度'.$month.'月'.'.csv';
        $file = Csv::export($sheet, $filename);
        return self::_download($file);
    }

    /**
     * 一覧表示
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $sheet = self::_createSheetCount($bizyear, $month);
        return response()->json($sheet);
    }

    /**
     * 一覧用の配列データ
     */
    private static function _createSheetCount(int $bizyear, int $month)
    {
        $year = Date::bizyear_to_year($bizyear, $month);
        $lastday = Date::lastday($year, $month);
        $sheet = array();
        // 見出し行
        $firstline = array('','',$month.'月早朝','','','','',$month.'月薄暮','','','','');
        $secondline = array('','','3歳児','4歳児','5歳児','未満児','早朝合計','3歳児','4歳児','5歳児','未満児','薄暮合計');

        for ($day=1; $day<=$lastday; $day++) {
            $date = Date::date($year,$month,$day);
            $week = Date::week($date);
            $line = array();
            $line[0] = $date;
            $line[1] = $week;
            $line[2] = self::_count_contract_using($bizyear, $date, $jyouji_age=3, $is_morining=true);
            $line[3] = self::_count_contract_using($bizyear, $date, $jyouji_age=4, $is_morining=true);
            $line[4] = self::_count_contract_using($bizyear, $date, $jyouji_age=5, $is_morining=true);
            $line[5] = self::_count_contract_using($bizyear, $date, $jyouji_age=0, $is_morining=true);
            $line[6] = ($line[2] + $line[3] + $line[4] + $line[5]);
            $line[7] = self::_count_contract_using($bizyear, $date, $jyouji_age=3, $is_morining=false);
            $line[8] = self::_count_contract_using($bizyear, $date, $jyouji_age=4, $is_morining=false);
            $line[9] = self::_count_contract_using($bizyear, $date, $jyouji_age=5, $is_morining=false);
            $line[10] = self::_count_contract_using($bizyear, $date, $jyouji_age=0, $is_morining=false);
            $line[11] = ($line[7] + $line[8] + $line[9] + $line[10]);
            $sheet[] = $line;
        }

        $totalline = array();
        foreach ($sheet as $line) {
            foreach ($line as $idx => $cell) {
                if ( ! isset($totalline[$idx])) {
                    $totalline[$idx] = 0;
                    if ($idx == 0) $totalline[$idx] = '合計';
                    elseif ($idx == 1) $totalline[$idx] = '';
                }
                if ($idx == 0 || $idx == 1) {
                    continue;
                }
                $totalline[$idx] += $cell;
            }
        }
        $sheet[] = $totalline;

        array_unshift($sheet, $secondline);
        array_unshift($sheet, $firstline);

        return $sheet;
    }

    private static function _count_contract_using($bizyear, $date, $jyouji_age=0, $is_morining=false) {
        $query = Roster::query()
            ->select('rosters.id')
            ->leftJoin('attendances', 'rosters.id', '=', 'attendances.roster_id')
            ->leftJoin('classrooms', 'rosters.classroom_id', '=', 'classrooms.id')
            ->leftJoin('grades', 'classrooms.grade_id', '=', 'grades.id')
            ->where('attendances.date', '=', $date)
            ->where('classrooms.bizyear', '=', $bizyear)
        ;
        if($is_morining) {
            $query->where('attendances.morning_using', '=', Attendance::MORNING_USING_TRUE);
        } else {
            $query
                ->where('attendances.evening_time_id', '>', Attendance::EVENING_EVENING_TIME_ID_DEFAULT)
                ->where('attendances.early', '=', null)
                ;
        }

        if( ! empty($jyouji_age)) {
            $query->where('grades.age', '=', $jyouji_age);
        } else {
            $query->where('grades.age', '<', Grade::IJYOUJI_START_AGE);
            // $query->where('grades.age', '<=', 2);
        }

        $using_rosters = $query->get();
        return count($using_rosters);
    }

}
