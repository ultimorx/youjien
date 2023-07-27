<?php

namespace App\Models;

class CityAccessLog extends CityDbModel
{
    protected $table = 'access_logs';

    public function user()
    {
        return $this->belongsTo(CityUser::class);
    }

    public static function list($limit=0)
    {
        $query = self::with(['user'])->orderBy('id', 'desc');
        if (!empty($limit) && is_numeric($limit)) {
            $query->limit($limit);
        }
        return $query->get();
    }

    public static function insert($user_id)
    {
        DB::beginTransaction();
        try {
            $insert = self::create([
                'user_id' => $user_id,
            ]);
            DB::commit();
            return $insert->id;
        }
        catch (\Throwable $ex) {
            dd($ex);
            DB::rollBack();
            report($ex); // ログ出力
            return '';
        }
    }

}
