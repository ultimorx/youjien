<?php

namespace App\Util;
use Illuminate\Support\Facades\Session;
class Sess
{
    const LOGIN_USER_INFO = 'login_user_info';
    const VIEW_KINDERGARTEN_ID = 'view_kindergarten_id';
    const VIEW_KINDERGARTEN_NAME = 'view_kindergarten_name';
    const VIEW_KINDERGARTEN_DB_NAME = 'view_kindergarten_db_name';

    private static function save($key, $value)
    {
        // session([$key => $value]);// グローバルヘルパ使う場合
        \Session::put($key, $value);// Sessionファサード使う場合
    }

    public static function clear()
    {
        // Session::forget(self::LOGIN_USER_INFO); // キーの単一指定
        Session::forget([
            self::LOGIN_USER_INFO,
            self::VIEW_KINDERGARTEN_ID,
            self::VIEW_KINDERGARTEN_NAME,
            self::VIEW_KINDERGARTEN_DB_NAME,
        ]);
    }

    public static function all()
    {
        return \Session::all();
    }
    public static function alllog()
    {
        \Log::debug('Sess ALL : ' , \Session::all());
    }

    public static function set_login_user_info($user)
    {
        self::save(self::LOGIN_USER_INFO, $user);
    }
    public static function get_login_user_info()
    {
        return \Session::get(self::LOGIN_USER_INFO);
    }

    public static function set_view_kindergarten_id($kindergarten_id)
    {
        self::save(self::VIEW_KINDERGARTEN_ID, $kindergarten_id);
    }
    public static function get_view_kindergarten_id($default = '')
    {
        return \Session::get(self::VIEW_KINDERGARTEN_ID, $default);
    }

    public static function set_view_kindergarten_name($kindergarten_name)
    {
        self::save(self::VIEW_KINDERGARTEN_NAME, $kindergarten_name);
    }
    public static function get_view_kindergarten_name($default = '')
    {
        return \Session::get(self::VIEW_KINDERGARTEN_NAME, $default);
    }

    public static function set_view_kindergarten_db_name($kindergarten_db_name)
    {
        self::save(self::VIEW_KINDERGARTEN_DB_NAME, $kindergarten_db_name);
    }
    public static function get_view_kindergarten_db_name($default = '')
    {
        return \Session::get(self::VIEW_KINDERGARTEN_DB_NAME, $default);
    }
}
