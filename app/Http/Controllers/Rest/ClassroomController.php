<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Grade;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassroomController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $classrooms = Classroom::list();
        return response()->json($classrooms);
    }

    /**
     * 稼働中一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function actives(Request $request)
    {
        $classrooms = Classroom::actives();
        return response()->json($classrooms);
    }

    /**
     * 準備中または稼働中一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function ready_and_actives(Request $request)
    {
        $classrooms = Classroom::ready_and_actives();
        return response()->json($classrooms);
    }

    /**
     * 年度指定一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function bizyear(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $classrooms = Classroom::bizyears($bizyear);
        return response()->json($classrooms);
    }

    /**
     * 検索一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search_for_mst_roster(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $grade = $request->get('grade');
        $grades = Grade::get_search_param_to_array($grade);
        $grade_id = Grade::get_search_param_grade_id($grades);
        $classrooms = Classroom::search($bizyear, $grade_id);
        return response()->json($classrooms);
    }

    /**
     * 保存 21/1/12未使用の可能性あり　今後要確認
     * @param Classroom $classroom
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Classroom $classroom, Request $request)
    {
        try {
            $classroom['ijyouji'] = $ijyouji;
            $classroom['mimanji'] = $mimanji;
            $classroom->save();

            DB::commit();
            return response()->json($classroom);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
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

        DB::beginTransaction();
        try {
            $classroom = Classroom::create([
                'bizyear'       => $request->get('bizyear'),
                'grade_id'      => $request->get('grade_id'),
                'name'          => $request->get('name'),
                'teacher'       => empty($request->get('teacher')) ? '' :$request->get('teacher'),
                'order'         => empty($request->get('order')) ? '' :$request->get('order'),
            ]);

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
     * Validation
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    private static function _validation(Request $request)
    {
        // バリデーション
        $validator = \Validator::make($request->all(), [
            'bizyear'       => 'required|numeric',
            // 'bizyear'       => 'required|numeric|max:4|min:4',
            // 'bizyear'       => 'required|year',
            'grade_id'      => 'required|numeric',
            'name'          => "required|string|max:128",
            'teacher'       => "max:128",
            'order'         => 'numeric',
            // 参考'bus'           => 'required_with:contract_depart_bus',
        ],
        [],
        [
            'bizyear'       => '年度',
            'grade_id'      => '学年',
            'name'          => 'クラス名',
            'teacher'       => '担任名',
            'order'         => '表示順',
        ]);

        return $validator;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Classroom $classroom
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Classroom $classroom , Request $request)
    {
        $validator = self::_validation($request);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        DB::beginTransaction();
        try {
            $classroom->bizyear  = $request->get('bizyear');
            $classroom->grade_id = $request->get('grade_id');
            $classroom->name     = $request->get('name');
            $classroom->teacher  = empty($request->get('teacher')) ? '' :$request->get('teacher');
            // $classroom->order    = $request->get('order');
            $classroom->order = empty($request->get('order')) ? 1 :$request->get('order');
            $classroom->save();

            DB::commit();
            return response()->json();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

}
