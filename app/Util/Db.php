<?php

namespace App\Util;

class Db
{
    // public static function switch_db($change_db)
    // {
    //     // $database_default = config('database.default');
    //     // dd($database_default);
    //     // $db_connections = config('database.connections');
    //     // dd($db_connections);
    //
    //     $path = base_path('.env');
    //     // DB_CONNECTION=mysql
    //     if (file_exists($path)) {
    //        file_put_contents($path, str_replace(
    //         'DB_CONNECTION=' . config('database.default'),
    //         'DB_CONNECTION=' . $change_db,//ここに変更したい値を記述
    //         file_get_contents($path)
    //        ));
    //     }
    // }

    /**
     * @param
     * @return
     */
    public static function export()
    {
        $mysqldump_dir = '';

        // mysqldump実行ファイルの格納ディレクトリ確認
        // MAMPの場合
        $dir = '/Applications/MAMP/Library/bin/';
        if(is_dir($dir)) {
            $mysqldump_dir = $dir;
        }

        // XAMPPの場合
        $dir = 'C:\xampp\mysql\bin\\';
        if(file_exists($dir)) {
            $mysqldump_dir = $dir;
        }

        // mysqldump実行ファイルの格納ディレクトリの有無
        if(empty($mysqldump_dir)) {
            return false;
        }

        // 保存先
        $file_Path = './tmp/';	// パス
        // $file_Name =  '出欠管理システムDBbackup'.date('ymd_Hi').'.sql'; // Edgeで文字化け ファイル名(SQLファイル)
        $file_Name =  'YoujienKanriDBbackup'.date('ymd_Hi').'.sql'; // ファイル名(SQLファイル)
        $savePath = $file_Path . $file_Name;

        // dumpコマンド
        $mysql = config('database.connections.mysql');
        $database_name 		= $mysql['database'];
        $database_host 		= $mysql['host'];
        $acount_name 		= $mysql['username'];
        $acount_password 	= $mysql['password'];
        $command =  $mysqldump_dir.'mysqldump --single-transaction --default-character-set=binary ' . $database_name . ' --host=' . $database_host . ' --user=' . $acount_name . ' --password=' . $acount_password . ' > ' . $savePath;
        system($command);

        // ダウンロード
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file_Name . '"');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Length: ' . filesize($savePath));
        readfile($savePath);

        // ファイル削除
        unlink($savePath);

        return true;
    }



}
