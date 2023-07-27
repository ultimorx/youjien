<?php

namespace App\Models;

//use Illuminate\Http\Request;
//use Illuminate\Support\Carbon;

class Classroom extends SoftDeleteModel
{
    const RUN_TRUE = 1;
    const RUN_FALSE = 0;

    public function roster()
    {
        return $this->hasMany(Roster::class);
    }

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function bizyear()
    {
        return $this->belongsTo(Bizyear::class, 'bizyear', 'bizyear');
    }

    public static function is_multi_ready_and_actives()
    {
        $list = Self::ready_and_actives();
        $bizyear_count = 0;
        $tmp_bizyear = '';
        foreach ($list as $v) {
            if( $v->bizyear != $tmp_bizyear ) {
                $bizyear_count++;
                $tmp_bizyear = $v->bizyear;
            }
        }
        return ($bizyear_count >= 2);
    }

    public static function list()
    {
        return self::with(['grade'])
            ->orderBy('bizyear', 'desc')
            ->orderBy('order', 'asc')
            ->orderBy('grade_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function ready_and_actives()
    {
        return self::with(['grade'])
            ->join('bizyears', 'bizyears.bizyear', '=', 'classrooms.bizyear')
            ->where('bizyears.run', '=', Runstate::RUN_ACTIVE)
            ->orWhere('bizyears.run', '=', Runstate::RUN_READY)
            ->orderBy('classrooms.bizyear', 'desc')
            ->orderBy('order', 'asc')
            ->orderBy('grade_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function actives()
    {
        // return self::with(['grade', 'bizyear'])
        //     ->where('run', '=', self::RUN_TRUE)
        //     ->orderBy('bizyear', 'desc')
        //     ->orderBy('order', 'asc')
        //     ->get();
        return self::with(['grade'])
            ->join('bizyears', 'bizyears.bizyear', '=', 'classrooms.bizyear')
            ->where('bizyears.run', '=', Runstate::RUN_ACTIVE)
            ->orderBy('classrooms.bizyear', 'desc')
            ->orderBy('order', 'asc')
            ->orderBy('grade_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function bizyears($bizyear)
    {
        return self::with(['grade'])
            ->where('bizyear', '=', $bizyear)
            ->orderBy('order', 'asc')
            ->orderBy('grade_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    public static function search($bizyear=null, $grade_id=null)
    {
        $query = self::with(['grade'])
            ->orderBy('order', 'asc')
            ->orderBy('grade_id', 'asc')
            ->orderBy('id', 'asc')
            ;
        if ( ! empty($bizyear) ) {
            $query->where('bizyear', '=', $bizyear);
        }
        if ( ! empty($grade_id) ) {
            $query->where('grade_id', '=', $grade_id);
        }
        return $query->get();
    }

    public static function get_grade($classroom_id)
    {
        $classroom = self::with(['grade'])
            ->where('id', '=', $classroom_id)
            ->first();
        return $classroom->grade;
    }

    public static function get_bizyear($classroom_id)
    {
        $classroom = self::query()->where('id', '=', $classroom_id)->first();
        return $classroom->bizyear;
    }

    public static function name($classroom_id)
    {
        $classroom = self::query()->where('id', '=', $classroom_id)->first();
        return $classroom->name;
    }

    public static function row($classroom_id)
    {
        return self::query()->where('id', '=', $classroom_id)->first();
    }

    public static function row_bizyear($classroom_id, $bizyear)
    {
        return self::query()->where('id', '=', $classroom_id)->where('bizyear', '=', $bizyear)->first();
    }
}
