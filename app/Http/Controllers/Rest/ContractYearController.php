<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
// use App\Models\EveningTime;
// use App\Models\MorningTime;
// use App\Models\ContractEvening;
// use App\Models\ContractMorning;
use App\Models\Classroom;
use App\Models\Child;
use App\Models\Grade;
use App\Models\Calendar;
use App\Models\Roster;
use App\Models\Attendance;
use App\Util\Csv;
use App\Util\Date;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractYearController extends CommonController
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
        $sheet = self::_createSheetCount($bizyear);
        $filename = '預かり人数表_'.$bizyear.'年度'.'.csv';
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
        $sheet = self::_createSheetCount($bizyear);
        return response()->json($sheet);
    }

    /**
     * 一覧用の配列データ
     */
    private static function _createSheetCount(int $bizyear)
    {
        $sheet = array();
        // 見出し行
        $firstline = array('');
        $secondline = array('');
        $bizmonths = Date::BIZ_MONTHS;
        foreach ($bizmonths as $month) {
            $firstline[] = $month.'月';
            $firstline[] = '';
            $secondline[] = '早朝';
            $secondline[] = '薄暮';
        }

        // $_calendar = new Calendar;

        for ($day=1; $day<=31; $day++) {
            $line = array();
            $line[] = $day.'日';
            foreach ($bizmonths as $month) {
                $year = Date::bizyear_to_year($bizyear, $month);
                $date = Date::date($year,$month,$day);
                $line[] = self::_count_contract_using($bizyear, $date, $is_morning=true);
                $line[] = self::_count_contract_using($bizyear, $date, $is_morning=false);
                // $_calendar->get_calendar($date);
                // $line[] = self::_count_contract($_calendar, $date, $is_morning=true);
                // $line[] = self::_count_contract($_calendar, $date, $is_morning=false);
            }

            $sheet[] = $line;
        }

        // $totalline = array();
        // foreach ($sheet as $line) {
        //     foreach ($line as $idx => $cell) {
        //         if ( ! isset($totalline[$idx])) {
        //             $totalline[$idx] = 0;
        //             if ($idx == 0) $totalline[$idx] = '合計';
        //             elseif ($idx == 1) $totalline[$idx] = '';
        //         }
        //         if ($idx == 0 || $idx == 1) {
        //             continue;
        //         }
        //         $totalline[$idx] += $cell;
        //     }
        // }
        // $sheet[] = $totalline;

        array_unshift($sheet, $secondline);
        array_unshift($sheet, $firstline);

        return $sheet;
    }

    // 実際に利用した人数
    //   出席者のみ、
    //   出席簿による「延長利用しない」は除外
    private static function _count_contract_using($bizyear, $date, $is_morning=false) {
        $query = Roster::query()
            ->select('rosters.id')
            ->leftJoin('attendances', 'rosters.id', '=', 'attendances.roster_id')
            ->leftJoin('classrooms', 'rosters.classroom_id', '=', 'classrooms.id')
            ->where('attendances.date', '=', $date)
            ->where('classrooms.bizyear', '=', $bizyear)
        ;
        if($is_morning) {
            $query->where('attendances.morning_using', '=', Attendance::MORNING_USING_TRUE);
        } else {
            $query
                ->where('attendances.evening_time_id', '>', Attendance::EVENING_EVENING_TIME_ID_DEFAULT)
                ->where('attendances.early', '=', null)
                ;
        }

        $using_rosters = $query->get();
        return count($using_rosters);
    }

    // 契約および休日を条件にした予定人数
    private static function _count_contract($_calendar, $date, $is_morning=false) {
        $is_evening = ! $is_morning;
        $rosters = Roster::contractlist($date, $is_morning, $is_evening);
        // return count($rosters);

        $count = 0;
        foreach ($rosters as $roster) {
            // 休日の場合は、除外
            if ($_calendar->is_dayoff($date, $roster->classroom->grade)) {
                continue;
            }
            $count++;
        }
        return $count;
    }

}
