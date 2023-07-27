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
use App\Util\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractChildController extends CommonController
{
    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $roster_id = $request->get('roster_id');
        $roster = Roster::with(['child','classroom'])->where('id', '=', $roster_id)->first();
        $child = $roster->classroom->name.Str::shorten($roster->child->name);
        $sheet = self::_createSheet($bizyear, $month, $roster_id);
        $filename = '時間外保育_'.$child.'_'.$bizyear.'年度'.$month.'月'.'.csv';
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
        $roster_id = $request->get('roster_id');
        $sheet = self::_createSheet($bizyear, $month, $roster_id);
        return response()->json($sheet);
    }

    /**
     * 契約者一覧用の配列データ
     */
    private static function _createSheet(int $bizyear, int $month, int $roster_id)
    {
        $year = Date::bizyear_to_year($bizyear, $month);
        $lastday = Date::lastday($year, $month);
        $sheet = array();
        // 見出し行
        $firstline = array(
            '日付',
            '曜日',
            '早朝利用',
            '降園時間',
            '延長超過時間',
        );

        $roster = self::_roster_evening($roster_id, $month);

        $attendances = self::_attendances($roster_id, $year, $month);
        $attendance_dates = array();
        foreach ($attendances as $attendance) {
            $attendance_dates[$attendance->date] = $attendance;
        }

        $morning_total = 0;
        $evening_total = 0;
        $evening_overtime_total_sec = 0;

        // dd($roster->evening_time);
        $evening_sec = (isset($roster->evening_time)) ? Time::time_to_sec($roster->evening_time): 0;
        // $evening_sec = Time::time_to_sec('14:00:00');

        for ($day=1; $day<=$lastday; $day++) {
            $date = Date::date($year,$month,$day);
            $week = Date::week($date);

            if( array_key_exists($date, $attendance_dates) ) {

                $line = array();
                $line[] = $date;
                $line[] = $week;
                $line[] = empty($attendance_dates[$date]->morning_using)? '': '○';
                $line[] = Time::hour_minute($attendance_dates[$date]->outtime);

                if( ! empty($attendance_dates[$date]->morning_using)) {
                    // 早朝利用した場合
                    $morning_total++;
                }
                $overtime_sec = 0;
                if( ! empty($evening_sec) && ! empty($attendance_dates[$date]->outtime)) {
                    // 延長契約あり & 降園済みの場合
                    $outtime_sec = Time::time_to_sec($attendance_dates[$date]->outtime);
                    if($evening_sec < $outtime_sec) {
                        // 降園時間が延長時間を超過した場合
                        $overtime_sec = $outtime_sec - $evening_sec;
                        $evening_overtime_total_sec += $overtime_sec;
                        $evening_total++;
                    }
                }
                $line[] = ($overtime_sec > 0) ? Time::sec_to_time($overtime_sec) : '';

            } else {
                $line = array($date, $week, '', '', '');
            }

            $sheet[] = $line;
        }

        $totalline = array('合計', '', $morning_total, $evening_total);
        $totalline[] = ($evening_overtime_total_sec > 0) ? Time::sec_to_time($evening_overtime_total_sec) : '';
        $sheet[] = $totalline;

        array_unshift($sheet, $firstline);

        return $sheet;
    }

    private static function _roster_evening($roster_id, $month) {
        $query = Roster::query()
            ->select(
                'rosters.*',
                'contract_evenings.evening_time_id',
                'evening_times.time as evening_time'
                )
            ->leftJoin('contract_evenings', function ($join) use($month) { // 外部結合
                $join
                ->on('contract_evenings.roster_id', '=', 'rosters.id')
                ->where('contract_evenings.month', '=', $month)
                ;
            })
            ->leftJoin('evening_times', 'evening_times.id', '=', 'contract_evenings.evening_time_id')
            ->where('rosters.id', '=', $roster_id)
            ->where('contract_evenings.evening_time_id', '<>', '')
        ;
        return $query->first();
    }

    private static function _attendances($roster_id, $year, $month) {
        $query = Attendance::query()
            ->where('attendances.roster_id', '=', $roster_id)
            ->where('date', 'LIKE', Date::year_month($year, $month).'%')
            ;
        return $query->get();
    }

}
