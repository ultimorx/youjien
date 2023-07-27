<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Roster;
use App\Models\Child;
use App\Models\ContractMorning;
use App\Models\ContractEvening;
use App\Models\ContractDepartBus;
use App\Models\MorningTime;
use App\Models\Grade;
use App\Models\Calendar;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * クラス出席簿の一覧取得
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function classroom(Request $request)
    {
        $date = $request->get('date');
        $classroom_id = $request->get('classroom_id');
        return response()->json(Roster::classrooms($date, $classroom_id));
    }

    /**
     * 早朝一覧の一覧取得
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function earlylist(Request $request)
    {
        $date = $request->get('date');
        $morning_time_rostors = array();

        $morning_times = MorningTime::query()->orderBy('order')->get();
        foreach ($morning_times as $morning_time) {
            $morning_time_rostors[] = array(
                'time' => $morning_time->time,
                // 'rosters' => Roster::with_earlylist($date, $morning_time->id) // 削除予定
                'rosters' => Roster::earlylist($date, $morning_time->id)
            );
        }

        return response()->json($morning_time_rostors);
    }

    public function creates(Request $request)
    {
        $date = $request->get('date');
        // $bizyear = Date::bizyear($date);

        // // 該当日の出欠レコードを確認
        //    updateOrCreate : 必要
        //    firstOrCreate : 不要
        // $attendance_date_count = Attendance::query()->where('date', '=', $date)->count();
        // if ($attendance_date_count >= 1) {
        //     return response()->json('Already exists');
        // }

        $rosters = Roster::contractlist($date);
        $_calendar = new Calendar;
        $_calendar->set_calendar($date);

        DB::beginTransaction();

        try {
            foreach ($rosters as $roster) {
                // 休日の場合は、除外
                if ($_calendar->is_dayoff($date, $roster->classroom->grade)) {
                    continue;
                }

                $is_exists = Attendance::query()->where('roster_id', '=', $roster->id)->where('date', '=', $date)->exists();
                // \Log::info('at--------', [$roster->id, $date, $is_exists]);
                if( $is_exists ) {
                    continue;
                }

                Attendance::create([
                    'roster_id' => $roster->id,
                    'date' => $date,
                    'bus_id' => empty($roster->bus_id) ? Attendance::BUS_ID_DEFAULT: $roster->bus_id,
                    'morning_using' => Attendance::MORNING_USING_FALSE,
                    'evening_time_id' => Attendance::EVENING_EVENING_TIME_ID_DEFAULT,
                ]);


                // $attendance = Attendance::firstOrCreate( // updateOrCreate より firstOrCreate のほうがいいかも
                //     [
                //         'roster_id' => $roster->id,
                //         'date' => $date
                //     ],
                //     [
                //         'bus_id' => empty($roster->bus_id) ? Attendance::BUS_ID_DEFAULT: $roster->bus_id,
                //         'morning_using' => Attendance::MORNING_USING_FALSE,
                //         'evening_time_id' => Attendance::EVENING_EVENING_TIME_ID_DEFAULT,
                //     ]
                // );
            }
            DB::commit();
            return response()->json('Create Recodes. ' . count($rosters));
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();

        try {
            $attendance = Attendance::find(1);
            $attendance->delete();

            DB::commit();
            return response()->json();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Attendance $attendance
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Attendance $attendance, Request $request)
    {
        DB::beginTransaction();
        try {
            foreach ($attendance->toArray() as $key => $value) {
                if ($request->missing($key)) continue;
                $update_value = $request->input($key);
                $attendance[$key] = $update_value;
            }
            $attendance->save();

            DB::commit();
            return response()->json();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 降園一括
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function reparts(Request $request)
    {
        if ($request->missing('check')) return response()->json();
        DB::beginTransaction();
        try {
            Attendance::whereIn('id', $request->input('check'))
                ->update(['outtime' => date('H:i')]);
            DB::commit();
            return response()->json();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 出席一括
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function arrives(Request $request)
    {
        $attendance_ids = $request->input('attendance_ids');
        foreach ($attendance_ids as $attendance_id) {
            if( empty($attendance_id)) continue;
            $attendance = Attendance::query()->where('id', '=', $attendance_id)->first();
            if( empty($attendance)) continue;
            self::arrive($attendance, $request);
        }
        return response()->json();
    }
    /**
     * 出席
     * @param Attendance $attendance
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public static function arrive(Attendance $attendance, Request $request)
    {
        $roster_id = $attendance['roster_id'];
        $date = $attendance['date'];
        $month = Date::month($date);

        // 出席時点の降園バス契約を確認
        $contract_depart_bus = ContractDepartBus::query()
            ->where('roster_id', '=', $roster_id)
            ->where('month', '=', $month)
            ->get();
        // dump(count($contract_depart_bus));
        $bus_id = Roster::BUS_ID_DEFAULT;
        if( ! empty($contract_depart_bus)) {
            // 出席時点の降園バスIDを取得
            $roster = Roster::query()->where('id', '=', $roster_id)->first();
            if ( ! empty($roster->bus_id)) {
                $bus_id = $roster->bus_id;
            }
        }

        // 出席時点の延長時間IDを取得
        $contract_evenings = ContractEvening::query()
            ->where('roster_id', '=', $roster_id)
            ->where('month', '=', $month)
            ->first();
        $evening_time_id = 0;
        if (! empty($contract_evenings->evening_time_id)) {
            $evening_time_id = $contract_evenings->evening_time_id;
        }
        try {
            $attendance['attendance'] = Attendance::ATTENDANCE_TRUE;
            $attendance['bus_id'] = $bus_id;
            $attendance['evening_time_id'] = $evening_time_id;
            // 出席の場合は、欠席区分、疾患を初期値に戻す
            $attendance['absence_type'] = Attendance::ABSENCE_TYPE_DEFAULT;
            $attendance['diseases_id'] = Attendance::DISEASES_ID_DEFAULT;
            // 早朝利用フラグ
            if ( ! $request->missing('morning_using')) {
                $attendance['morning_using'] = Attendance::MORNING_USING_TRUE;
            }
            $attendance->save();

            DB::commit();
            return response()->json($attendance);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 欠席
     * @param Attendance $attendance
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function absence(Attendance $attendance, Request $request)
    {
        try {
            $attendance['attendance'] = Attendance::ATTENDANCE_FALSE;
            // $attendance['absence_type'] = null;
            // 欠席の場合は、遅刻、早退などを初期値に戻す
            $attendance['late'] = null;
            $attendance['early'] = null;
            $attendance['morning_using'] = Attendance::MORNING_USING_FALSE;
            $attendance['evening_time_id'] = Attendance::EVENING_EVENING_TIME_ID_DEFAULT;
            $attendance['bus_id'] = Attendance::BUS_ID_DEFAULT;
            $attendance['outtime'] = null;
            $attendance['pick_up'] = null;
            $attendance->save();

            DB::commit();
            return response()->json($attendance);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 出席欠席
     * @param Attendance $attendance
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function cancel(Attendance $attendance, Request $request)
    {
        try {
            $attendance['attendance'] = Attendance::ATTENDANCE_DEFAULT;
            $attendance['late'] = null;
            $attendance['early'] = null;
            $attendance['morning_using'] = Attendance::MORNING_USING_FALSE;
            $attendance['evening_time_id'] = Attendance::EVENING_EVENING_TIME_ID_DEFAULT;
            $attendance['bus_id'] = Attendance::BUS_ID_DEFAULT;
            $attendance['outtime'] = null;
            $attendance['pick_up'] = null;
            $attendance['absence_type'] = Attendance::ABSENCE_TYPE_DEFAULT;
            $attendance['diseases_id'] = Attendance::DISEASES_ID_DEFAULT;
            $attendance->save();

            DB::commit();
            return response()->json($attendance);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function test(Request $request)
    {
        $date = $request->get('date');
        $morning_time_rostors = array();

        $morning_times = MorningTime::query()->orderBy('order')->get();
        foreach ($morning_times as $morning_time) {
            $morning_time_rostors[] = array(
                'time' => $morning_time->time,
                // 'rosters' => Roster::get_attendances($date, $morning_time->id)
                'rosters' => Roster::earlylist($date, $morning_time->id)
            );
        }

        return response()->json($morning_time_rostors);
    }


}
