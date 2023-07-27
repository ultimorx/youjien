<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\CityDisease;
use App\Models\CityKindergarten;
use App\Models\Disease;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityDiseaseController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $diseases = CityDisease::list_all();
        foreach ($diseases as $idx => $disease) {
            $diseases[$idx]->absence_type_name = Disease::absence_type_name($disease->absence_type);
        }
        return response()->json($diseases);
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

        $values = [
            'absence_type' => $request->get('absence_type'),
            'name' => $request->get('name'),
            'order' => $request->get('order'),
            'active' => ($request->get('active')!='')? $request->get('active') : Disease::ACTIVE_DEFAULT,
        ];

        DB::beginTransaction();
        try {

            CityDisease::create($values);

            // // 各園DB内のテーブルに追加
            // $kindergartens = CityKindergarten::list();
            // foreach ($kindergartens as $kindergarten) :
            //     \Sess::set_view_kindergarten_id($kindergarten->id);
            //     　::create($values);
            // endforeach;
            // \Sess::set_view_kindergarten_id(0);

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
     * @param CityDisease $disease
     */
    private static function _validation(Request $request)
    {
        // バリデーション
        $validator = \Validator::make($request->all(), [
            'absence_type'  => 'required',
            'name'          => "required",
            'order'         => "required",
            'active'        => 'required',
        ],
        [],
        [
            'absence_type' => '種類',
            'name'         => '名称',
            'order'        => '表示順',
            'active'       => '有効／無効',
        ]);

        return $validator;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityDisease $disease
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(CityDisease $disease, Request $request)
    {
        $validator = self::_validation($request);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        $disease_id = $disease->id;
        DB::beginTransaction();
        try {
            self::_update($disease, $request);

            // // 各園DB内のテーブルを更新
            // $kindergartens = CityKindergarten::list();
            // foreach ($kindergartens as $kindergarten) :
            //     \Sess::set_view_kindergarten_id($kindergarten->id);
            //     $disease = Disease::find($disease_id);
            //     if( ! empty($disease)) {
            //         self::_update($disease, $request);
            //     }
            // endforeach;
            // \Sess::set_view_kindergarten_id(0);

            DB::commit();
            return response()->json();
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }
    private static function _update($disease, $request){
        $disease->absence_type  = $request->get('absence_type');
        $disease->name          = $request->get('name');
        $disease->order         = $request->get('order');
        $disease->active        = ($request->get('active')!='')? $request->get('active') : Disease::ACTIVE_DEFAULT;
        $disease->save();
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param CityDisease $disease
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(CityDisease $disease, Request $request)
    {
        $disease_id = $disease->id;

        DB::beginTransaction();
        try {
            $disease->delete();

            // // 各園DB内のテーブルを更新
            // $kindergartens = CityKindergarten::list();
            // foreach ($kindergartens as $kindergarten) :
            //     \Sess::set_view_kindergarten_id($kindergarten->id);
            //     $disease = Disease::find($disease_id);
            //     if( ! empty($disease)) {
            //         $disease->delete();
            //     }
            // endforeach;
            // \Sess::set_view_kindergarten_id(0);

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
