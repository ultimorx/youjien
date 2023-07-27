<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Roster;
use App\Models\Child;
use App\Util\Csv\ChildCsv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MorningTime;//削除予定

class RosterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Roster::with([
                'child',
                'classroom',
                'classroom.grade',
                'contract_mornings',
                'contract_evenings',
                'contract_arrive_bus',
                'contract_depart_bus'
        ]);
        if ($request->filled('class')) {
            $query = $query->where('classroom_id', '=', $request->query('class'));
        }

        $query->orderBy('number');

        return response()->json($query->get());
    }

    public function save(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $classroom_id = $request->get('classroom_id');
        $child_id = $request->get('child_id');

        DB::beginTransaction();
        try {
            $change_before_classroom_id = 0;
            $post = Roster::where('bizyear', $bizyear)->where('child_id', $child_id)->first();
            if (empty($post)) {
                $post = Roster::create([
                    'bizyear' => $bizyear,
                    'child_id' => $child_id,
                    'classroom_id' => $classroom_id,
                ]);
            } else {
                $change_before_classroom_id = $post->classroom_id;
                $post->bizyear = $bizyear;
                $post->child_id = $child_id;
                $post->classroom_id = $classroom_id;
                $post->save();
            }

            // 生年月日順で出席番号の更新
            // 選択したクラス
            self::_update_roster_number($classroom_id);
            // 選択から外れたクラス（前クラスの更新）
            if( ! empty($change_before_classroom_id) ) {
                self::_update_roster_number($change_before_classroom_id);
            }

            DB::commit();
            return response()->json($post);
            // return response()->json($rosters);
        }
        catch (\Throwable $ex) {
            DB::rollBack();
            report($ex); // ログ出力
            return response($ex->getMessage(), 500);
        }
    }

    // 出席番号の更新
    private static function _update_roster_number($classroom_id)
    {
        // 生年月日順で出席番号の更新
        $rosters = Roster::sort_birthday($classroom_id);
        $number = 0;
        foreach ($rosters as $idx => $roster) {
            $number++;
            $roster->number = $number;
            $roster->save();
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

        $classroom_id = $request->get('classroom_id');
        $bizyear = Classroom::get_bizyear($classroom_id);
        DB::beginTransaction();
        try {
            $child = Child::create([
                'number'        => $request->get('number'), //市役所作成の通番
                'name'          => $request->get('name'), //園児名
                'kana'          => $request->get('kana'), //園児名かな
                'birthday'      => $request->get('birthday'), //生年月日
                'gender'        => $request->get('gender'),  //生年月日
                'remarks'       => $request->get('remarks'),
                'move_in_date'  => $request->get('move_in_date'),
                'move_out_date' => $request->get('move_out_date'),
            ]);
            ;
            $roster = Roster::create([
                'bizyear'      => $bizyear,
                'classroom_id' => $classroom_id,
                'child_id'     => $child->id,
                'number'       => $request->get('number'), //園児連番＝出席番号
                'bus_id'       => empty($request->get('contract_depart_bus')) ? Roster::BUS_ID_DEFAULT :$request->get('bus'),
            ]);

            foreach ((array)$request->get('contract_arrive_bus') as $month) {
                $roster->contract_arrive_bus()->create(['month' => $month]);
            }

            foreach ((array)$request->get('contract_depart_bus') as $month) {
                $roster->contract_depart_bus()->create(['month' => $month]);
            }

            foreach ((array)$request->get('contract_mornings') as $month => $morning_time_id) {
                if (blank($morning_time_id)) continue; // 契約なしは保存しない
                $roster->contract_mornings()->create(['month' => $month, 'morning_time_id' => $morning_time_id]);
            }

            foreach ((array)$request->get('contract_evenings') as $month => $evening_time_id) {
                if (blank($evening_time_id)) continue; // 契約なしは保存しない
                $roster->contract_evenings()->create(['month' => $month, 'evening_time_id' => $evening_time_id]);
            }

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
            'number'        => 'required|numeric|max:99',
            'name'          => "required|string|max:128",
            'kana'          => "required|string|max:128",
            'birthday'      => 'required|date',
            'gender'        => 'required|numeric|max:2',
            'move_in_date'  => 'nullable|date',
            'move_out_date' => 'nullable|date',
            'bus'           => 'required_with:contract_depart_bus',
        ],
        [],
        [
            'number'        => '出席番号',
            'name'          => '園児名',
            'kana'          => '園児名かな',
            'birthday'      => '生年月日',
            'gender'        => '性別',
            'move_in_date'  => '転入日',
            'move_out_date' => '転出日',
            'contract_depart_bus' => '降園バス',
            'bus'           => '降園で使用するバス',
        ]);

        // $validator->sometimes('bus', 'required', function($input) {
        //     // dump($input->contract_depart_bus);
        //     return ! empty($input->contract_depart_bus);
        // });
        return $validator;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Roster $roster
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Roster $roster, Request $request)
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
            // // クラス変更する場合
            // if ( ! empty($request->get('classroom_id')) ) {
            //     $roster->number = $request->get('classroom_id');
            // }
            if ( ! empty($request->get('number')) ) {
                $roster->number = $request->get('number');
            }
            $roster->bus_id = empty($request->get('contract_depart_bus')) ? Roster::BUS_ID_DEFAULT :$request->get('bus');
            $roster->save();

            $roster->child->update([
                'name'          => $request->get('name'),
                'kana'          => $request->get('kana'),
                'birthday'      => $request->get('birthday'),
                'gender'        => $request->get('gender'),
                'remarks'       => $request->get('remarks'),
                'move_in_date'  => $request->get('move_in_date'),
                'move_out_date' => $request->get('move_out_date'),
            ]);

            $roster->contract_arrive_bus()->forceDelete();
            foreach ((array)$request->get('contract_arrive_bus') as $month) {
                $roster->contract_arrive_bus()->create(['month' => $month]);
            }

            $roster->contract_depart_bus()->forceDelete();
            foreach ((array)$request->get('contract_depart_bus') as $month) {
                $roster->contract_depart_bus()->create(['month' => $month]);
            }

            $roster->contract_mornings()->forceDelete();
            foreach ((array)$request->get('contract_mornings') as $month => $morning_time_id) {
                if (blank($morning_time_id)) continue; // 契約なしは保存しない
                $roster->contract_mornings()->create(['month' => $month, 'morning_time_id' => $morning_time_id]);
            }

            $roster->contract_evenings()->forceDelete();
            foreach ((array)$request->get('contract_evenings') as $month => $evening_time_id) {
                if (blank($evening_time_id)) continue; // 契約なしは保存しない
                $roster->contract_evenings()->create(['month' => $month, 'evening_time_id' => $evening_time_id]);
            }

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
     * @param int $classroom_id
     * @return \Illuminate\Http\Response
     */
    public function download(int $classroom_id = null)
    {
        // ファイル作成(public直下)
        // ダウンロード後にファイルを削除
        // ダウンロード完了前にウィンドウを閉じるとファイルが残る
        return response()->download(ChildCsv::export($classroom_id))->deleteFileAfterSend(true);
    }

    /**
     * 年度指定の園児ID配列とクラス別の人数配列
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function child_ids_for_bizeyar(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $rosters = Roster::bizyear($bizyear);
        $child_ids = [];
        $classroom_child_counts = [];
        foreach ($rosters as $roster) {
            $child_ids[$roster->child_id] = $roster->classroom_id;
            if( ! isset($classroom_child_counts[$roster->classroom_id]) ) {
                $classroom_child_counts[$roster->classroom_id] = 0;
            }
            $classroom_child_counts[$roster->classroom_id]++;
        }

        return response()->json([
            'child_ids' => $child_ids,
            'classroom_child_counts' => $classroom_child_counts,
        ]);
    }
}
