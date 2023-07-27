<?php

namespace App\Models;

use Carbon\Carbon;

class EveningTime extends SoftDeleteModel
{

    public function getTimeAttribute($value)
    {
        return Carbon::parse($value)->format("H:i");
    }

    /**
     * 延長契約者数
     *   クラス指定
     *   月指定
     *   時間ID指定
     */
    public static function count($classroom_id, $month, $evening_time_id)
    {
        $query = Self::query()
            ->join('contract_evenings', 'contract_evenings.evening_time_id', '=', 'evening_times.id')
                ->where('contract_evenings.month', '=', $month)
            ->join('rosters', 'rosters.id', '=', 'contract_evenings.roster_id')
                ->where('rosters.classroom_id', '=', $classroom_id)
            ->where('evening_times.id', '=', $evening_time_id)
            ->groupBy('contract_evenings.evening_time_id')
        ;
        return $query->count();
    }

    /**
     * 延長契約者の名簿ID
     *   クラス指定
     *   月指定
     *   時間ID指定
     */
    public static function roster_ids($classroom_id, $month, $evening_time_id)
    {
        $query = Self::query()
            ->select('rosters.id as roster_id')
            ->join('contract_evenings', 'contract_evenings.evening_time_id', '=', 'evening_times.id')
                ->where('contract_evenings.month', '=', $month)
            ->join('rosters', 'rosters.id', '=', 'contract_evenings.roster_id')
                ->where('rosters.classroom_id', '=', $classroom_id)
            ->where('evening_times.id', '=', $evening_time_id)
        ;
        return $query->get();
    }

}
