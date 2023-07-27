<?php

namespace App\Models;
use App\Util\Str;

class Grade extends SoftDeleteModel
{
    const IJYOUJI_START_AGE = 3;
    const IJYOUJI_AGE_TYPE = 10;
    const MIMANJI_AGE_TYPE = 20;
    const IJYOUJI_AGE_TYPE_NAME = '以上児';
    const MIMANJI_AGE_TYPE_NAME = '未満児';
    const ASC = 'asc';
    const DESC = 'desc';

    public function grade()
    {
        return $this->hasMany(Classroom::class);
    }

    public static function create_search_param($limit_max, $limit_min, $id, $name)
    {
        return $limit_max . Str::JOIN . $limit_min . Str::JOIN . $id . Str::JOIN . $name;
    }
    public static function get_search_param_to_array($data)
    {
        list($limit_max, $limit_min, $grade_id, $grade_name) = array_pad(explode(Str::JOIN, $data, 4), 4, null);
        return array(
            'limit_max' => (int)$limit_max,
            'limit_min' => (int)$limit_min,
            'grade_id' => (int)$grade_id,
            'grade_name' => (string)$grade_name,
        );
    }
    public static function get_search_param_limit_max($ary)
    {
        return $ary['limit_max'];
    }
    public static function get_search_param_limit_min($ary)
    {
        return $ary['limit_min'];
    }
    public static function get_search_param_grade_id($ary)
    {
        return $ary['grade_id'];
    }
    public static function get_search_param_grade_name($ary)
    {
        return $ary['grade_name'];
    }

    public static function list($order=self::ASC)
    {
        return self::query()
            ->orderBy('order', $order)
            ->get();
    }

    public static function name($grade_id)
    {
        $grade = self::query()->where('id', '=', $grade_id)->first();
        return $grade->name;
    }

    public static function get_grade($grade_id)
    {
        return self::query()->where('id', '=', $grade_id)->first();
    }

    // 以上児判定
    public static function is_ijyouji($grade)
    {
        return (self::IJYOUJI_START_AGE <= $grade->age);
    }
    // 未満児判定
    public static function is_mimanji($grade)
    {
        return ($grade->age < self::IJYOUJI_START_AGE);
    }
    // 未満児／以上児
    public static function age_type_name($grade)
    {
        return ($grade->age < self::IJYOUJI_START_AGE)? self::MIMANJI_AGE_TYPE_NAME: self::IJYOUJI_AGE_TYPE_NAME;
    }
}
