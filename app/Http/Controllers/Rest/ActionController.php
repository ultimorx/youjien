<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Action;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
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
        $list = Action::monthlist($bizyear, $month);
        return response()->json($list);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function bizweeklist(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $bizweek = $request->get('bizweek');
        $date = $request->get('date');
        $grade_id = $request->get('grade_id');
        $list = Action::bizweeklist($date, $grade_id);
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
        $grade_id = $request->get('grade_id');
        $action = $request->get('action');
        $event_id = $request->get('event_id');
        $action_id = Action::insert($date, $grade_id, $action, $event_id);
        return response()->json($action_id);
    }


    /**
     * 更新
     * @param Action $action
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Action $action, Request $request)
    {
        try {
            $action['action'] = $request->get('action');
            $action['event_id'] = $request->get('event_id');
            $action->save();

            DB::commit();
            return response()->json($action);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            action($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    /**
     * 削除
     * @param Action $action
     * @return \Illuminate\Http\Response
     */
    public function remove(Action $action)
    {
        DB::beginTransaction();
        try {
            $action->delete();

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
