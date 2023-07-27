<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SwitchDbModel;
class SoftDeleteModel extends SwitchDbModel
{
    use SoftDeletes;

    protected $perPage = 100;

    protected $dates = [
        'create_datetime',
        'update_datetime',
        'delete_datetime'
    ];

    protected $guarded = [
        self::DELETED_AT,
        self::CREATED_AT,
        self::UPDATED_AT
    ];

    const CREATED_AT = 'create_datetime';
    const UPDATED_AT = 'update_datetime';
    const DELETED_AT = 'delete_datetime';
}
