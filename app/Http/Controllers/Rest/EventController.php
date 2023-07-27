<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function monthlist(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $month = $request->get('month');
        $list = Event::monthlist($bizyear, $month);
        return response()->json($list);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function bizweeklist(Request $request)
    {
        // $bizyear = $request->get('bizyear');
        // $bizweek = $request->get('bizweek');
        // $list = Event::bizweeklist($bizyear, $bizweek);
        $date = $request->get('date');
        $list = Event::bizweeklist($date);
        return response()->json($list);
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $date = $request->get('date');
        $event_name = $request->get('event_name');
        $event_type = $request->get('event_type');
        $event_id = Event::insert($date, $event_name, $event_type);
        return response()->json($event_id);
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create_in(Request $request)
    {
        $event_name = $request->get('event_name');
        $date = $request->get('date');
        $event_id = Event::insert($date, $event_name, Event::TYPE_IN);
        return response()->json($event_id);
    }

    /**
     * 更新
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Event $event, Request $request)
    {
        $input_name = $request->get('name');
        $input_date = $request->get('date');
        $event_date = $event['date'];
        $event = Event::save_update($event, $event_date, $input_date, $input_name);
        return response()->json($event);
    }

    /**
     * 削除
     * @param Event $event
     * @return \Illuminate\Http\Response
     */
    public function remove(Event $event)
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
