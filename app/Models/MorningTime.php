<?php

namespace App\Models;

use Carbon\Carbon;

class MorningTime extends SoftDeleteModel
{
    public function contract_morning()
    {
        return $this->hasMany(ContractMorning::class);
    }

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->format("H:i");
    }

    /**
     * 早朝契約者数
     *   クラス指定
     *   月指定
     *   時間ID指定
     */
    public static function count($classroom_id, $month, $morning_time_id)
    {
        $query = Self::query()
            ->join('contract_mornings', 'contract_mornings.morning_time_id', '=', 'morning_times.id')
                ->where('contract_mornings.month', '=', $month)
            ->join('rosters', 'rosters.id', '=', 'contract_mornings.roster_id')
                ->where('rosters.classroom_id', '=', $classroom_id)
            ->where('morning_times.id', '=', $morning_time_id)
            ->groupBy('contract_mornings.morning_time_id')
        ;
        return $query->count();
    }

    /**
     * 早朝契約者の名簿ID
     *   クラス指定
     *   月指定
     *   時間ID指定
     */
    public static function roster_ids($classroom_id, $month, $morning_time_id)
    {
        $query = Self::query()
            ->select('rosters.id as roster_id')
            ->join('contract_mornings', 'contract_mornings.morning_time_id', '=', 'morning_times.id')
                ->where('contract_mornings.month', '=', $month)
            ->join('rosters', 'rosters.id', '=', 'contract_mornings.roster_id')
                ->where('rosters.classroom_id', '=', $classroom_id)
            ->where('morning_times.id', '=', $morning_time_id)
        ;
        return $query->get();
    }
}
