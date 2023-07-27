<?php

namespace App\Models;

//use Illuminate\Http\Request;
//use Illuminate\Support\Carbon;

class Bizyear extends SoftDeleteModel
{
    protected $primaryKey = 'bizyear';

    public function classroom()
    {
        return $this->hasMany(Classroom::class);
    }

    public function runstate()
    {
        return $this->belongsTo(Runstate::class, 'run', 'id');
    }

    public static function is_multi_active()
    {
        $actives = Self::actives();
        return (count($actives) >= 2);
    }

    public static function list()
    {
        // return self::query()
        return self::with(['runstate'])
            ->orderBy('bizyear', 'desc')
            ->get();
    }

    public static function ready_and_actives()
    {
        return self::query()
            ->where('run', '=', Runstate::RUN_ACTIVE)
            ->orWhere('run', '=', Runstate::RUN_READY)
            ->orderBy('bizyear', 'desc')
            ->get();
    }

    public static function actives()
    {
        return self::query()
            ->where('run', '=', Runstate::RUN_ACTIVE)
            ->orderBy('bizyear', 'desc')
            ->get();
    }

    public static function finish_and_actives()
    {
        return self::query()
            ->where('run', '=', Runstate::RUN_ACTIVE)
            ->orWhere('run', '=', Runstate::RUN_FINISH)
            ->orderBy('bizyear', 'desc')
            ->get();
    }
}
