<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Grade;
use App\Models\Roster;
use App\Util\Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ChildController extends CommonController
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $children = Child::list();
        return response()->json($children);
    }

    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $grade = $request->get('grade');
        $children = Child::search($bizyear, $grade);
        return response()->json($children);
    }

    /**
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $bizyear = $request->get('bizyear');
        $grade = $request->get('grade');
        $grades = Grade::get_search_param_to_array($grade);
        $grade_name = Grade::get_search_param_grade_name($grades);

        $sheet = self::_createSheet($bizyear, $grade);
        $filename = $bizyear.'年度'.$grade_name.'.csv';
        $file = Csv::export($sheet, $filename);
        return self::_download($file);
    }

    /**
     * ダウンロード用の配列データ
     */
    private static function _createSheet(int $bizyear, $grade)
    {
        $sheet = array();
        // 見出し行
        $firstline = array(
            '園児名',
            'かな',
            '生年月日',
            '性別',
            '備考',
            '転入日',
            '転出日',
        );

        $children = Child::search($bizyear, $grade);
        foreach ($children as $child) {
            $line = array();
            $line[] = $child->name;
            $line[] = $child->kana;
            $line[] = $child->birthday;
            $line[] = $child->gender;
            $line[] = $child->remarks;
            $line[] = $child->move_in_date;
            $line[] = $child->move_out_date;
            $sheet[] = $line;
        }
        array_unshift($sheet, $firstline);

        return $sheet;
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

        DB::beginTransaction();
        try {
            $post = Child::updateOrCreate([
              'id' => $request->get('id'),
            ],
            [
              'name' => $request->get('name'),
              'kana' => $request->get('kana'),
              'birthday' => $request->get('birthday'),
              'gender' => $request->get('gender'),
              'remarks' => $request->get('remarks'),
              'move_in_date' => $request->get('move_in_date'),
              'move_out_date' => $request->get('move_out_date'),
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
            'name'          => "required|string|max:128",
            'kana'          => "required|string|max:128",
            'birthday'      => 'required|date',
            'gender'        => 'required|numeric|max:2',
            'move_in_date'  => 'nullable|date',
            'move_out_date' => 'nullable|date',
        ],
        [],
        [
            'name'          => '園児名',
            'kana'          => '園児名かな',
            'birthday'      => '生年月日',
            'gender'        => '性別',
            'move_in_date'  => '転入日',
            'move_out_date' => '転出日',
        ]);

        return $validator;
    }


    public function import(Request $request)
    {
        function _response($save_list=[], $result_msg='', $error_msg='') {
            return response()->json([
                'save_list' => $save_list,
                'result_msg' => $result_msg,
                'error_msg' => $error_msg,
                ]);
        }
        function _response_error($error_msg='') {
            return _response([],'',$error_msg);
        }

        if( empty($request->file('file')) ){
            return _response_error('【未選択エラー】ファイルを選択してください。');
        }

        if( strpos(strtolower($request->file('file')->getClientOriginalName()), 'csv') === false ){
            return _response_error('【ファイルエラー】CSVファイルを選択してください。');
        }

        $file_path = $request->file('file')->path();
        $file = new \SplFileObject($file_path);
        $file->setFlags(
            \SplFileObject::DROP_NEW_LINE // 行末の改行を読み飛ばします。
            | \SplFileObject::READ_AHEAD  // 先読み/巻き戻しで読み出します。
            | \SplFileObject::SKIP_EMPTY  // ファイルの空行を読み飛ばします。
            | \SplFileObject::READ_CSV    // CSV 列として行を読み込みます。
        );

        // 一行ずつ処理
        $saved = [];
        $errors = [];
        $is_utf8_import_encoding = false;
        foreach($file as $idx => $line) {
            if ($idx == 0) { // 1行目は見出し
                // utf8の必須はなし
                // if( mb_detect_encoding($line[0]) != 'UTF-8' ) {
                //     return _response_error('【文字コードエラー】Excel保存時に「CSV UTF-8 (コンマ区切り)」で保存したファイルが取り込み可能です。');
                // }
                $is_utf8_import_encoding = ( mb_detect_encoding($line[0]) == 'UTF-8' );
                continue;
            }

            if ( empty($line[0]) || empty($line[1]) || empty($line[2]) || empty($line[3]) ) {
                continue;
            }

            if ( ! $is_utf8_import_encoding) {
                mb_convert_variables('UTF-8', 'SJIS-win', $line); // SJIS → UTF-8に変換
            }

            $birthday_timestamp = strtotime($line[2]);
            if( empty($birthday_timestamp) || $birthday_timestamp === false ) {
                $errors[] = ($idx+1).'行目：生年月日が正しく処理ができませんでした。';
                continue;
            }
            $birthday = date('Y-m-d', $birthday_timestamp);

            switch ($line[3]) {
                case '男':
                    $gender = 1;
                    break;
                case '女':
                    $gender = 2;
                    break;
                default:
                    $gender = 1;
                    break;
            }
            $saved[] = Child::create([
                'name'      => $line[0],
                'kana'      => $line[1],
                'birthday'  => $birthday,
                'gender'    => $gender,
                'remarks'   => isset($line[4])? $line[4]:'',
            ]);
        }

        $msg = count($saved).'件取り込みました。';
        $err = join('<br>',$errors);
        return _response($saved, $msg, $err);
    }

    /**
     * Delete the specified resource in storage.
     *
     * @param Child $child
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Child $child, Request $request)
    {
        DB::beginTransaction();
        try {
            $child->delete();

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
