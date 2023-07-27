<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;
use App\Models\CityUser;
use App\Models\CityAccessLog;
use App\Models\CityKindergarten;
use App\Util\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityUserController extends Controller
{
    /**
     * 一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        $users = CityUser::list();
        $kindergarten_names = CityKindergarten::names();
        foreach ($users as $idx => $user) {
            $users[$idx]->kindergarten_name = self::_kindergarten_name($kindergarten_names, $user->kindergarten_id);
        }
        return response()->json($users);
    }

    /**
     * アクセスログ一覧表示
     * @param  Request $request
     * @return \Illuminate\Http\Response
     */
    public function accessloglist(Request $request)
    {
        $count = $request->get('count', 50);
        $access_logs = CityAccessLog::list($count);
        $logs = [];
        $kindergarten_names = CityKindergarten::names();
        foreach ($access_logs as $idx => $access_log) {
            $row = [];
            $row['ip'] = $access_log->ip;
            if ( empty($access_log->user) ) {
                $row['user_name'] = '削除済みユーザー';
                $row['kindergarten_name'] = '---';
            } else {
                $row['user_name'] = $access_log->user->name;
                $row['kindergarten_name'] = self::_kindergarten_name($kindergarten_names, $access_log->user->kindergarten_id);
            }

            $row['datetime'] = (string) $access_log->create_datetime;
            $logs[] = $row;
        }

        return response()->json($logs);
    }

    private function _kindergarten_name($kindergarten_names, $kindergarten_id)
    {
        if (isset($kindergarten_names[$kindergarten_id])) {
            return $kindergarten_names[$kindergarten_id];
        }
        return CityKindergarten::KINDERGARTEN_ID_ZERO_NAME;
    }

    /**
     * Create the specified resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user_id = 0;
        $validator = self::_validation($request, $user_id);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        DB::beginTransaction();
        try {
            $user = CityUser::create([
                'kindergarten_id' => $request->get('kindergarten_id'),
                'name' => $request->get('name'),
                'pass' => md5($request->get('pass')),
                'active' => ($request->get('active')!='')? $request->get('active') : CityUser::ACTIVE_DEFAULT,
                'order' => ($request->get('order')!='')? $request->get('order') : CityUser::ORDER_DEFAULT,
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
     * @param CityUser $user
     */
    private static function _validation(Request $request, $user_id)
    {
        // バリデーション
        $validator = \Validator::make($request->all(), [
            'kindergarten_id'  => 'required|numeric',
            'name'             => "required|alpha_dash|max:128|unique:App\Models\CityUser,name,{$user_id}",
            'pass'             => "required|alpha_dash|min:3",
            'active'           => 'required',
        ],
        [],
        [
            'kindergarten_id' => '所属',
            'name'            => 'ユーザー名',
            'pass'            => 'パスワード',
            'active'          => '有効／無効',
            'order'           => '表示順',
        ]);

        return $validator;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CityUser $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(CityUser $user, Request $request)
    {
        $validator = self::_validation($request, $user->id);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        DB::beginTransaction();
        try {
            $user->kindergarten_id = $request->get('kindergarten_id');
            $user->name  = $request->get('name');
            $user->pass  = md5($request->get('pass'));
            $user->active = ($request->get('active')!='')? $request->get('active') : CityUser::ACTIVE_DEFAULT;
            $user->order = ($request->get('order')!='')? $request->get('order') : CityUser::ORDER_DEFAULT;
            $user->save();

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
     * @param CityUser $user
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(CityUser $user, Request $request)
    {
        DB::beginTransaction();
        try {
            $user->delete();
            // CityUser::find($id)->delete();

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
     * Login
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $name = $request->get('name');
        $pass = $request->get('pass');
        $login_user = CityUser::get_login($name, $pass);
        $login_user_id = (!empty($login_user) && isset($login_user->id)) ? $login_user->id: 0;
        $request->merge(['user_id' => $login_user_id]);

        $validator = self::_login_validation($request);
        if ($validator->fails()) {
            $response_data = [
                'validation_error' => collect($validator->errors())->flatten(),
            ];
            return response()->json($response_data);
        }

        \Login::set_user($login_user);

        DB::beginTransaction();
        try {
            CityAccessLog::create([
                'user_id' => $login_user_id,
                // 'ip' => $request->ip(),
                'ip' => self::_get_ip(),
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
     * @param CityUser $user
     */
    private static function _login_validation(Request $request)
    {
        // バリデーション
        $validator = \Validator::make($request->all(), [
            'name'             => "required",
            'pass'             => "required",
            'user_id'          => "required_with_all:name,pass|exists:App\Models\CityUser,id",
        ],
        [],
        [
            'name'            => 'ユーザー名',
            'pass'            => 'パスワード',
            'user_id'         => 'ユーザー名またはパスワード',
        ]);

        return $validator;
    }

    /**
     * Login
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function direct_login(Request $request)
    {
        $login_user = [];
        $name = $request->get('name');
        if( ! empty($name)) {
            $login_user = CityUser::get_login_name($name);
        }
        $hash = $request->get('hash');
        if( ! empty($hash)) {
            $login_user = CityUser::get_login_hash($hash);
        }
        if( ! empty($login_user)) {
            \Login::set_user($login_user);
            $login_user_id = (!empty($login_user) && isset($login_user->id)) ? $login_user->id: 0;
            CityAccessLog::create([
                'user_id' => $login_user_id,
                'ip' => self::_get_ip(),
            ]);
            $expire_time = CityUser::LOGIN_COOKIE_EXPIRE + time();
            setcookie(CityUser::LOGIN_NAME, $login_user->name, $expire_time);
            setcookie(CityUser::LOGIN_PASS, $login_user->name, $expire_time);
        }

        return redirect('/');
    }

    private static function _get_ip()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            // \Log::info('_get_ip : ', $_SERVER);
            if (array_key_exists($key, $_SERVER) === true){
                // \Log::info('_get_ip key : '.$key);
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();
    }
}
