<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\CityOutEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityOutEventController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        // $list = CityOutEvent::list();
        // return response()->json($list);
        $bizyear = $request->get('bizyear');
        // dd($bizyear);
        if(empty($bizyear) or !is_numeric($bizyear)) {
            $list = CityOutEvent::list();
        } else {
            $list = CityOutEvent::bizyearlist($bizyear);
        }
        return response()->json($list);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function bizyears(Request $request)
    {
        $list = CityOutEvent::bizyears();
        return response()->json($list);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function bizyearlist(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $list = CityOutEvent::bizyearlist($bizyear);
        return response()->json($list);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function monthlist(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $list = CityOutEvent::monthlist($bizyear, $month);
        return response()->json($list);
    }

    /**
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validator = self::_validation($request);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        $date = $request->get('date');
        $name = $request->get('name');
        $event_id = CityOutEvent::insert($date, $name);
        return response()->json($event_id);
    }

    /**
     * Validation
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @param CityOutEvent $event
     */
    private static function _validation(Request $request)
    {
        // バリデーション
        $validator = \Validator::make($request->all(), [
            'date' => 'required',
            'name' => "required",
        ],
        [],
        [
            'date' => '日付',
            'name' => '名称',
        ]);

        return $validator;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityOutEvent $event
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(CityOutEvent $event, Request $request)
    {
        $validator = self::_validation($request);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        $name = $request->get('name');
        $date = $request->get('date');
        $event = CityOutEvent::save_update($event, $date, $name);
        return response()->json($event);
    }

    /**
     * 削除
     * @param CityOutEvent $event
     * @return \Illuminate\Http\Response
     */
    public function delete(CityOutEvent $event)
    {
        DB::beginTransaction();
        try {
            $event->delete();

            DB::commit();
            return response()->json(true);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            action($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

}
