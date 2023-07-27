<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\CityInformation;
use App\Models\CityAccessLog;
use App\Util\Date;
use App\Util\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityInformationController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $informations = CityInformation::list();
        foreach ($informations as $idx => $information) {
            $informations[$idx]->pre_message = Str::url2link($information->message);
        }
        return response()->json($informations);
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
            $information = CityInformation::create([
                'public_date' => $request->get('public_date'),
                'title' => $request->get('title'),
                'message' => $request->get('message'),
                // 'display' => $request->get('display'),
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
     * @param CityInformation $information
     */
    private static function _validation(Request $request)
    {
        // バリデーション
        $validator = \Validator::make($request->all(), [
            'public_date'  => 'required|date',
            'title'        => "required",
            'message'      => "required",
            // 'display'      => 'numeric',
        ],
        [],
        [
            'public_date' => '公開日',
            'title'       => '件名',
            'message'     => '内容',
            // 'display'    => '表示',
        ]);

        return $validator;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityInformation $information
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(CityInformation $information, Request $request)
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
            $information->public_date  = $request->get('public_date');
            $information->title  = $request->get('title');
            $information->message  = $request->get('message');
            $information->display  = $request->get('display');
            $information->save();

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
     * Delete the specified resource in storage.
     *
     * @param CityInformation $information
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(CityInformation $information, Request $request)
    {
        DB::beginTransaction();
        try {
            $information->delete();
            // CityInformation::find($id)->delete();

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
