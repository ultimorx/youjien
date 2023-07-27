<?php

namespace App\Models;

class ContractMorning extends SoftDeleteModel
{
    public function roster()
    {
        return $this->hasMany(Roster::class);
    }
    public function MorningTime()
    {
        return $this->belongsTo(MorningTime::class);
    }
}
