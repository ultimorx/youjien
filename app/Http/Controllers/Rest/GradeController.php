<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $order = $request->get('order');
        if( ! ($order == Grade::ASC || $order == Grade::DESC) ) {
            $order = Grade::ASC;
        }
        $list = Grade::list($order);
        return response()->json($list);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function desc(Request $request)
    {
        $list = Grade::list(Grade::DESC);
        return response()->json($list);
    }
}
