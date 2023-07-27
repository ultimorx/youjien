<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\EveningTime;
use App\Models\Roster;
use App\Util\Date;
use Illuminate\Http\Request;

class DepartController extends Controller
{
    /**
     * バス一覧
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function bus(Request $request)
    {
        $date = $request->get('date');
        return response()->json(Roster::buslist($date));
    }

    /**
     * お迎え一覧
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function daytime(Request $request)
    {
        $date = $request->get('date');
        return response()->json(Roster::daytimelist($date));
    }

    /**
     * 延長一覧
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function evening(Request $request)
    {
        $date = $request->get('date');
        $evening_time_rostors = array();

        $evening_times = EveningTime::query()->orderBy('order')->get();
        foreach ($evening_times as $evening_time) {
            $evening_time_rostors[] = array(
                'time' => $evening_time->time,
                'rosters' => Roster::eveninglist($date, $evening_time->id)
            );
        }

        return response()->json($evening_time_rostors);
    }

}
