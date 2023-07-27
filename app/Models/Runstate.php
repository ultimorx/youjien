<?php

namespace App\Models;

//use Illuminate\Http\Request;
//use Illuminate\Support\Carbon;

class Runstate extends SoftDeleteModel
{
    const RUN_READY = 1;
    const RUN_ACTIVE = 2;
    const RUN_FINISH = 3;

    public function bizyear()
    {
        return $this->hasMany(Bizyear::class);
    }

    public static function list()
    {
        return self::query()
            ->orderBy('id', 'asc')
            ->get();
    }
}
