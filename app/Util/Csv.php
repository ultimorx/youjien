<?php

namespace App\Util;

use App\Util\Convert;

class Csv extends \SplFileObject
{
    private $file_path;
    public $errors = [];

    public function __construct($file_path)
    {
        parent::__construct($file_path);
        $this->file_path = $file_path;
        $this->setFlags(
            \SplFileObject::DROP_NEW_LINE // 行末の改行を読み飛ばします。
            | \SplFileObject::READ_AHEAD  // 先読み/巻き戻しで読み出します。
            | \SplFileObject::SKIP_EMPTY  // ファイルの空行を読み飛ばします。
            | \SplFileObject::READ_CSV    // CSV 列として行を読み込みます。
        );
    }

    /**
     * @param int $sheet
     * @param string $filename
     * @return string $filename
     */
    public static function export($sheet, $filename)
    {
        $filename = \Login::get_view_kindergarten_name().'_'.$filename;
        touch($filename);
        $fp = fopen($filename, 'w');

        // データ書き込み
        foreach ($sheet as $line) {
            $row = [];
            foreach ($line as $cell) {
                $row[] = $cell;
            }
            fputcsv($fp, Convert::sjis($row));
            // fputcsv($fp, Convert::unicode($row));
        }
        fclose($fp);

        return $filename;
    }

}
