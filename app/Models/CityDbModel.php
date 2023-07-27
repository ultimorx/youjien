<?php

namespace App\Models;

class CityDbModel extends SoftDeleteModel
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        // DB切替
        $this->connection = 'motosuyoujienCity';
        // \Log::debug('CityDbModel : $this->connection : '.$this->connection);
    }
}
