<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Grade;
use App\Models\Child;
use App\Models\Roster;
use App\Models\Attendance;
use App\Models\Disease;
use App\Models\Calendar;
use App\Util\Csv;
use App\Util\Date;
use App\Util\Time;
use App\Util\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SheetAttendanceStatsController extends CommonController
{
    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    // public function download(Request $request)
    // {
    //     $bizyear = $request->get('bizyear');
    //     $bizyear = 2020;
    //     $classroom_id = $request->get('classroom_id');
    //     $grade_id = $request->get('grade_id');
    //     $classroom_name = ( ! empty($classroom_id)) ? Classroom::name($classroom_id) : '';
    //     $grade_name = ( ! empty($grade_id)) ? Grade::name($grade_id) : '';
    //
    //     $sheet = self::_createSheet($bizyear, $month, $classroom_id);
    //     $filename = '統計表_'.$grade_name.$classroom_name.'_'.$bizyear.'年度.csv';
    //     $file = Csv::export($sheet, $filename);
    //     return self::_download($file);
    // }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $classroom_id = $request->get('classroom_id');
        $grade_id = $request->get('grade_id');
        $sheet = self::_createSheet($bizyear, $classroom_id, $grade_id);
        return response()->json($sheet);
    }

    /**
     * 契約者一覧用の配列データ
     */
    private static function _createSheet(int $bizyear, int $classroom_id, int $grade_id)
    {
        // dd($bizyear, $classroom_id, $grade_id);
        // $grade_id = 1; // 以上児学年
        // $grade_id = 5; // 未満児学年
        // $classroom_id = 2;// 以上児クラス
        // $classroom_id = 9;// 未満児クラス
        // $classroom_id = 2;

        if ( ! empty($classroom_id)) {
            $grade = Classroom::get_grade($classroom_id);
        }
        if ( ! empty($grade_id)) {
            $grade = Grade::get_grade($grade_id);
        }

        $bizmonths = DATE::BIZ_MONTHS;

        $sheet = array();
        foreach ($bizmonths as $month) {
            $year = Date::bizyear_to_year($bizyear, $month);

            // 授業日数
            $lastday = Date::lastday($year, $month);
            $count_dayoff = Calendar::count_dayoff($year, $month, $grade);
            $count_dayon = $lastday - $count_dayoff;
            $sheet[$month]['childcareDays'] = $count_dayon;

            foreach (Child::GENDERS as $gender) {
                $child_move = self::_child_move($gender, $year, $month, $classroom_id, $grade_id, $bizyear);
                $count_child_exsist = self::_count_child_exsist($gender, $year, $month, $classroom_id, $grade_id, $bizyear);
                $count_child_absence = self::_count_child_absence($gender, $year, $month, $classroom_id, $grade_id, $bizyear, $count_dayon);
                $sheet[$month][$gender] = array(
                    'movein'=>$child_move->count_in,
                    'moveout'=>$child_move->count_out,
                    'count'=>$count_child_exsist,
                    'absence_count'=>$count_child_absence,
                );
            }
        }

        // 学期計
        function calc_term($termmonths, $sheet) {
            $termcounts = array();
            foreach ($termmonths as $month) {
                foreach ($sheet[$month] as $gender => $monthcounts) {
                    if( $gender=='childcareDays') {
                        if( ! isset($termcounts['childcareDays'])) $termcounts['childcareDays'] = 0;
                        $termcounts['childcareDays'] += $monthcounts;
                        continue;
                    }
                    if( ! ($gender==Child::GENDER_M || $gender==Child::GENDER_W)) continue;
                    foreach ($monthcounts as $idx => $count) {
                        if( ! ($idx=='movein' || $idx=='moveout')) continue;
                        if( ! isset($termcounts[$gender][$idx])) $termcounts[$gender][$idx] = 0;
                        $termcounts[$gender][$idx] += $count;
                    }
                }
            }
            return $termcounts;
        }
        $sheet['firstterm'] = calc_term(array(4,5,6,7), $sheet);
        $sheet['secondterm'] = calc_term(array(8,9,10,11,12), $sheet);
        $sheet['thirdterm'] = calc_term(array(1,2,3), $sheet);

        // 学期別累積
        function calc_term_total($termcounts, $default_datas=array()) {
            $termtotalcounts = $default_datas;
            foreach ($termcounts as $gender => $counts) {
                if( $gender=='childcareDays') {
                    if( ! isset($termtotalcounts['childcareDays'])) $termtotalcounts['childcareDays'] = 0;
                    $termtotalcounts['childcareDays'] += $counts;
                    continue;
                }
                if( ! ($gender==Child::GENDER_M || $gender==Child::GENDER_W)) continue;
                foreach ($counts as $idx => $count) {
                    if( ! isset($termtotalcounts[$gender][$idx])) $termtotalcounts[$gender][$idx] = 0;
                    $termtotalcounts[$gender][$idx] += $count;
                }
            }
            return $termtotalcounts;
        }
        $sheet['secondtermtotal'] = calc_term_total($sheet['firstterm']);
        $sheet['secondtermtotal'] = calc_term_total($sheet['secondterm'], $sheet['secondtermtotal']);
        $sheet['thirdtermtotal'] = calc_term_total($sheet['thirdterm'], $sheet['secondtermtotal']);

        return $sheet;
    }

    private static function _child_move($gender, $year, $month, $classroom_id, $grade_id, $bizyear) {
        $query = Child::query()
            ->select(
                DB::raw('count(`children`.`move_in_date`) as `count_in`'),
                DB::raw('count(`children`.`move_out_date`) as `count_out`')
            )
            ->leftJoin('rosters', 'rosters.child_id', '=', 'children.id')
            ->where('children.gender', '=', $gender)
            ->where(function($query) use($year, $month) {
                    $query
                    ->where('children.move_in_date', 'LIKE', Date::year_month($year, $month).'%')
                    ->orWhere('children.move_out_date', 'LIKE', Date::year_month($year, $month).'%');
                })
        ;
        // delete_datetimem条件を追加
        $query->where('rosters.delete_datetime', '=', null);
        $query->where('children.delete_datetime', '=', null);

        if ( ! empty($classroom_id)) {
            $query->where('rosters.classroom_id', '=', $classroom_id);
        }
        if ( ! empty($grade_id)) {
            $query
            ->leftJoin('classrooms', 'rosters.classroom_id', '=', 'classrooms.id')
            ->where('classrooms.grade_id', '=', $grade_id)
            ->where('classrooms.bizyear', '=', $bizyear);
        }

        return $query->first();
    }

    private static function _count_child_exsist($gender, $year, $month, $classroom_id, $grade_id, $bizyear) {
        $query = Child::query()
            ->select(
                // 在籍数
                DB::raw('count(`children`.`id`) as `count`')
            )
            ->leftJoin('rosters', 'rosters.child_id', '=', 'children.id')
            ->where('children.gender', '=', $gender)
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
        ;
        // delete_datetimem条件を追加
        $query->where('rosters.delete_datetime', '=', null);
        $query->where('children.delete_datetime', '=', null);

        if ( ! empty($classroom_id)) {
            $query->where('rosters.classroom_id', '=', $classroom_id);
        }
        if ( ! empty($grade_id)) {
            $query
            ->leftJoin('classrooms', 'rosters.classroom_id', '=', 'classrooms.id')
            ->where('classrooms.grade_id', '=', $grade_id)
            ->where('classrooms.bizyear', '=', $bizyear);
        }

        $child = $query->first();
        return $child->count;
    }

    private static function _count_child_absence($gender, $year, $month, $classroom_id, $grade_id, $bizyear, $count_dayon) {
        $query = Child::query()
            ->select(
                // 欠席数
                DB::raw('count(`attendances`.`attendance` = ' . Attendance::ATTENDANCE_FALSE . ' or null) as `count_attendances_false`')
            )
            ->leftJoin('rosters', 'rosters.child_id', '=', 'children.id')
            ->leftJoin('attendances', 'attendances.roster_id', '=', 'rosters.id')
            ->where('children.gender', '=', $gender)
            ->where('attendances.date', 'LIKE', Date::year_month($year, $month).'%')
            ->groupBy('attendances.roster_id');
        ;
        // delete_datetimem条件を追加
        $query->where('rosters.delete_datetime', '=', null);
        $query->where('children.delete_datetime', '=', null);

        if ( ! empty($classroom_id)) {
            $query->where('rosters.classroom_id', '=', $classroom_id);
        }
        if ( ! empty($grade_id)) {
            $query
            ->leftJoin('classrooms', 'rosters.classroom_id', '=', 'classrooms.id')
            ->where('classrooms.grade_id', '=', $grade_id)
            ->where('classrooms.bizyear', '=', $bizyear);
        }

        $children = $query->get();
        $count_all_absence = 0;
        foreach ($children as $child) {
            if ($child->count_attendances_false == $count_dayon) {
                $count_all_absence++;
            }
        }
        return $count_all_absence;
    }
}
