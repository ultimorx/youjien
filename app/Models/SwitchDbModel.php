<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class SwitchDbModel extends Model
{
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);

        // DB切替
        $view_kindergarten_db_name = \Sess::get_view_kindergarten_db_name();
        $this->connection = empty($view_kindergarten_db_name)? env('DB_CONNECTION', 'motosuyoujienCity'): $view_kindergarten_db_name;

        // kindergartens.id参照し、phpソース内にconnection_key判別する場合
        // $view_kindergarten_id = \Sess::get_view_kindergarten_id();
        //
        // // config/database.php connections配列のキーを指定
        // $kindergarten_connection_keys = [
        //     1 => 'motosuyoujienNeo', // 根尾幼児園
        //     2 => 'motosuyoujienKoumi', // 神海幼児園
        //     3 => 'motosuyoujienMotosu', // 本巣幼児園
        //     4 => 'motosuyoujienItonukiHigashi', // 糸貫東幼児園
        //     5 => 'motosuyoujienItonukiNishi', // 糸貫西幼児園
        //     6 => 'motosuyoujienShinsei', // 真正幼児園
        //     7 => 'motosuyoujienMakuwa', // 真桑幼児園
        //     8 => 'motosuyoujienDanjyo', // 弾正幼児園
        // ];
        //
        // if (array_key_exists($view_kindergarten_id, $kindergarten_connection_keys)) {
        //     $this->connection = $kindergarten_connection_keys[$view_kindergarten_id];
        // } else {
        //     $this->connection = 'mysql';
        // }

        // 検証用
        // $dd = debug_backtrace();
        // \Log::debug('CommonModel dd : ', $dd[1]);
        // // \Log::debug($dd[1]['function']);
        // // \Log::debug($dd[1]['class']);
        // // \Log::debug($this);
        // // \Log::debug('CommonModel ログ222 session と　DB', ['$this->connection' => $this->connection, '$kindergarten_id' => $kindergarten_id]);
        // \Log::debug('CommonModel ', [$this->connection, $kindergarten_id]);
        // \Log::debug(' ');
    }
}
