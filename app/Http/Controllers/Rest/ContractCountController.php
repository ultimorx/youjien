<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\EveningTime;
use App\Models\MorningTime;
use App\Models\Classroom;
use App\Models\ContractEvening;
use App\Models\ContractMorning;
use App\Models\Roster;
use App\Util\Csv;
use App\Util\Date;

use Illuminate\Http\Request;

class ContractCountController extends CommonController
{
    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $sheet = self::_createSheetCount($bizyear, $month);
        $filename = '契約者数_'.$bizyear.'年度'.$month.'月'.'.csv';
        $file = Csv::export($sheet, $filename);
        return self::_download($file);
    }

    /**
     * 契約者数一覧用の配列データ
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
     * 契約者数一覧用の配列データ
     */
    private static function _createSheetCount(int $bizyear, int $month)
    {
        $sheet = array();
        // 見出し行
        $firstline = array('');
        $secondline = array('クラス名');

        // 早朝時間
        $morning_times = MorningTime::query()->orderBy('order')->get();
        foreach ($morning_times as $idx => $morning_time) {
            $firstline[] = ($idx == 0)? '早朝(1)': ''; // 早朝保育契約①
            $secondline[] = $morning_time->time;
        }
        $firstline[] = '';
        $secondline[] = '早朝計';

        // 延長時間
        $evening_times = EveningTime::query()->orderBy('order')->get();
        foreach ($evening_times as $idx => $evening_time) {
            $firstline[] = ($idx == 0)? '延長(2)': ''; // 延長保育契約②
            $secondline[] = $evening_time->time;
        }
        $firstline[] = '';
        $secondline[] = '延長計';

        $firstline[] = '両契約者';
        $secondline[] = '';

        $firstline[] = '合計(1)＋(2)';
        $secondline[] = '';

        // クラス別
        $classrooms = Classroom::query()->where('bizyear', '=', $bizyear)->orderBy('order')->get();
        foreach ($classrooms as $classroom) {
            $line = array();
            $line[] = $classroom->name;

            $morning_sum = 0;
            $morning_roster_ids = array();
            foreach ($morning_times as $morning_time) {
                $roster_ids = MorningTime::roster_ids($classroom->id, $month, $morning_time->id);
                $line[] = count($roster_ids);
                $morning_sum += count($roster_ids);
                foreach ($roster_ids as $v) {
                    $morning_roster_ids[$v->roster_id] = $v->roster_id;
                }
            }
            $line[] = $morning_sum;

            $evening_sum = 0;
            $evening_roster_ids = array();
            foreach ($evening_times as $evening_time) {
                $roster_ids = EveningTime::roster_ids($classroom->id, $month, $evening_time->id);
                $line[] = count($roster_ids);
                $evening_sum += count($roster_ids);
                foreach ($roster_ids as $v) {
                    $evening_roster_ids[$v->roster_id] = $v->roster_id;
                }
            }
            $line[] = $evening_sum;

            $both_sum = 0;
            foreach ($morning_roster_ids as $morning_roster_id) {
                 if (array_key_exists($morning_roster_id, $evening_roster_ids)) {
                    $both_sum++;
                 }
            }
            $line[] = $both_sum;

            $line[] = $morning_sum + $evening_sum;
            $sheet[] = $line;
        }

        $totalline = array();
        foreach ($sheet as $line) {
            foreach ($line as $idx => $cell) {
                if ( ! isset($totalline[$idx])) {
                    $totalline[$idx] = ($idx == 0)? '合計': 0;
                }
                if ($idx == 0) {
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

}
