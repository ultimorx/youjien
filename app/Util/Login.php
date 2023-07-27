<?php

namespace App\Util;

use \App\Models\CityKindergarten;

class Login
{
    const VIEW_KINDERGARTEN_ID = 'view_kindergarten_id';
    const USER_ID = 'user_id';
    const KINDERGARTEN_ID = 'kindergarten_id';
    const USER_NAME = 'user_name';

    public static function set_user($user)
    {
        \Sess::set_login_user_info([
            self::USER_ID => $user->id,
            self::KINDERGARTEN_ID => $user->kindergarten_id,
            self::USER_NAME => $user->name,
        ]);

        // \Sess::set_view_kindergarten_id($user->kindergarten_id);
        self::set_view_kindergarten($user->kindergarten_id);
    }

    public static function set_view_kindergarten($id)
    {
        $kindergarten = CityKindergarten::get($id);
        if( ! empty($kindergarten)) {
            \Sess::set_view_kindergarten_id($kindergarten->id);
            \Sess::set_view_kindergarten_name($kindergarten->name);
            \Sess::set_view_kindergarten_db_name($kindergarten->db_name);
        }
    }

    public static function clear()
    {
        \Sess::clear();
    }

    public static function get_login_user_id()
    {
        $login = \Sess::get_login_user_info();
        return $login[self::USER_ID];
    }

    public static function get_login_user_name()
    {
        $login = \Sess::get_login_user_info();
        return $login[self::USER_NAME];
    }

    public static function is_city()
    {
        $login = \Sess::get_login_user_info();
        if( empty($login) || !isset($login[self::KINDERGARTEN_ID]) ){
            return false;
        }
        return ($login[self::KINDERGARTEN_ID] == 0);
    }

    public static function has_user()
    {
        // dd(\Sess::get_login_user_info());
        return ! empty(\Sess::get_login_user_info());
    }

    public static function has_view_kindergarten_id()
    {
        return ! empty(\Sess::get_view_kindergarten_id());
    }

    public static function get_view_kindergarten_name()
    {
        return \Sess::get_view_kindergarten_name();
    }

    public static function get_view_type()
    {
        return self::is_city()? 'city': 'kindergarten';
    }


}
