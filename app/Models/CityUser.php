<?php

namespace App\Models;

class CityUser extends CityDbModel
{
    protected $table = 'users';

    const ROLE_DEFAULT = 0;
    const ACTIVE_TRUE = 1;
    const ACTIVE_FALSE = 0;
    const ACTIVE_DEFAULT = self::ACTIVE_TRUE;
    const ORDER_DEFAULT = 1;

    const LOGIN_NAME = 'login_name';
    const LOGIN_PASS = 'login_pass';
    const LOGIN_COOKIE_EXPIRE = 60 * 60 * 24 * 30; //30日


    public function kindergarten()
    {
        return $this->belongsTo(CityKindergarten::class);
    }

    public static function list()
    {
        return self::with(['kindergarten'])
            ->orderBy('order', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function get_login($name, $pass)
    {
        return self::query()
        ->where('active', '=', self::ACTIVE_TRUE)
        ->where('name', '=', $name)
        ->where('pass', '=', md5($pass))
        ->first();
    }

    public static function get_login_name($name)
    {
        return self::query()
        ->where('active', '=', self::ACTIVE_TRUE)
        ->where('name', '=', $name)
        ->first();
    }

    public static function get_login_hash($hash)
    {
        return self::query()
        ->where('active', '=', self::ACTIVE_TRUE)
        ->where('hash', '=', $hash)
        ->first();
    }

    // public static function is_exist($name, $pass)
    // {
    //     $user = self::query()->where('name', '=', $name)->where('pass', '=', md5($pass))->first();
    //     return !empty($user);
    // }
}
