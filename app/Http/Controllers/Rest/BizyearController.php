<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Bizyear;
use App\Models\Calendar;
use App\Models\Event;
use App\Models\Action;
use App\Models\Aim;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BizyearController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $bizyears = Bizyear::list();
        return response()->json($bizyears);
    }

    /**
     * 保存
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $validator = self::_validation($request);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        $bizyear = $request->get('bizyear');

        // 新規追加時のみ実行
        $recode = Bizyear::where('bizyear', $bizyear)->first();
        if (empty($recode)) {
            // 1. カレンダー　calendars　毎日
            // 2. 週のねらい　aims　月曜日　※「週のねらい」ページ描画時に実行　）
            // 3. 年間予定管理（行事）　events　日付重複あり
            // 4. 子どもの活動　actions　日付重複あり、event_idの更新
            Calendar::create_by_bizyear($bizyear);
            Aim::copy_by_bizyear($bizyear);
            Event::copy_by_bizyear($bizyear);
            Action::copy_by_bizyear($bizyear);
        }

        DB::beginTransaction();
        try {
            $post = Bizyear::updateOrCreate([
                'bizyear' => $bizyear,
            ],
            [
                'run' => $request->get('run'),
            ]);

            // updateOrCreateを使用しないパターン
            // $post = Bizyear::where('bizyear', $bizyear)->first();
            // if (empty($post)) {
            //     $post = Bizyear::create([
            //         'bizyear' => $bizyear,
            //         'run' => $run,
            //     ]);
            // } else {
            //     $post->bizyear = $bizyear;
            //     $post->run = $run;
            //     $post->save();
            // }

            DB::commit();
            return response()->json($post);
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
            'run'           => 'required|numeric',
        ],
        [],
        [
            'bizyear'       => '年度',
            'run'           => '状態',
        ]);

        return $validator;
    }
}
