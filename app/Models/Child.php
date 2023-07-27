<?php

namespace App\Models;

//use Illuminate\Http\Request;
//use Illuminate\Support\Carbon;
use App\Models\Grade;
use App\Models\Roster;
use App\Util\Date;

class Child extends SoftDeleteModel
{
    const MOVE_IN_DATE_DEFAULT = null;
    const MOVE_OUT_DATE_DEFAULT = null;

    const GENDER_M = 1;
    const GENDER_W = 2;
    const GENDERS = array(self::GENDER_M, self::GENDER_W);
    const GENDER_NAMES = array(self::GENDER_M => '男', self::GENDER_W => '女');

    protected $attributes = [
        'move_in_date' => self::MOVE_IN_DATE_DEFAULT,
        'move_out_date' => self::MOVE_OUT_DATE_DEFAULT,
    ];

    public function roster()
    {
        return $this->hasMany(Roster::class);
    }

    public static function list()
    {
        return self::query()
            ->orderBy('birthday', 'asc')
            ->get();
    }

    public static function search($bizyear, $grade)
    {
        if( empty($bizyear))
        {
            $today = date('Y-m-d');
            $bizyear = Date::bizyear($today);
        }
        $max_year = $bizyear;
        $min_year = $bizyear - 6;

        // 学年指定
        if( ! empty($grade)) {
            $grades = Grade::get_search_param_to_array($grade);
            $limit_max = Grade::get_search_param_limit_max($grades);
            $limit_min = Grade::get_search_param_limit_min($grades);
            $max_year = $bizyear - $limit_max;
            $min_year = $bizyear - $limit_min;
        }

        $max_date = (string)$max_year . '-4-2'; // 4月2日生まれから
        $min_date = (string)$min_year . '-4-1'; // 4月1日生まれまで
        $bizyear_first_date = (string)($bizyear) . '-4-1'; // 4月1日を新年度の開始日とする

        // return self::query()
        return self::with(['roster'])
            ->where(function($query) use($max_date, $min_date) {
                $query
                ->where('birthday', '<', $max_date)
                ->where('birthday', '>', $min_date)
                ;
            })
            ->where(function($query) use($bizyear_first_date) {
                $query
                ->where('move_out_date', '=', null)
                ->orWhere('move_out_date', '>=', $bizyear_first_date)
                ;
            })
            ->orderBy('birthday', 'asc')
            ->get();

    }

    public static function test()
    {
        return self::query()
        // return self::with(['roster'])
            ->orderBy('birthday', 'desc')
            ->get();
    }
}
