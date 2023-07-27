<?php

namespace App\Util\Csv;

use App\Models\Child;

class ChildCsv extends \SplFileObject
{
    private $file_path;
    public $errors = [];

    const COLUMN_SIZE = 8; // カラム数

    const COL_NUMBER        = 0; // 市役所作成の通番
    const COL_NAME          = 1; // 園児名
    const COL_KANA          = 2; // 園児名かな
    const COL_BIRTHDAY      = 3; // 生年月日
    const COL_GENDER        = 4; // 性別
    const COL_MOVE_IN_DATE  = 5; // 転入日
    const COL_MOVE_OUT_DATE = 6; // 転出日
    const COL_REMARKS       = 7; // 備考

    const HEADER = [
        '市役所作成の通番',
        '園児名',
        '園児名かな',
        '生年月日',
        '性別',
        '転入日',
        '転出日',
        '備考'
    ];

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
     * @param int $classroom_id
     * @return string $filename
     */
    public static function export($classroom_id)
    {
        $filename = date('Ymd') . "_child.csv";

        touch($filename);
        $fp = fopen($filename, 'w');

        $children = Child::query()
            ->join('rosters', 'rosters.child_id', '=', 'children.id')
            ->where('rosters.classroom_id', '=', $classroom_id)
            ->get();

        // ヘッダー書き込み
        $header = self::HEADER;
        mb_convert_variables('SJIS-win', 'UTF-8', $header); // SJISに変換
        fputcsv($fp, $header);

        // データ書き込み
        foreach ($children as $child) {
            $new_row = [];
            $new_row[self::COL_NUMBER]        = $child->number;
            $new_row[self::COL_NAME]          = $child->name;
            $new_row[self::COL_KANA]          = $child->kana;
            $new_row[self::COL_BIRTHDAY]      = $child->birthday;
            $new_row[self::COL_GENDER]        = $child->gender ? '男' : '女';
            $new_row[self::COL_MOVE_IN_DATE]  = $child->move_in_date;
            $new_row[self::COL_MOVE_OUT_DATE] = $child->move_out_date;
            $new_row[self::COL_REMARKS]       = $child->remarks;

            mb_convert_variables('SJIS-win', 'UTF-8', $new_row); // SJISに変換

            fputcsv($fp, $new_row);
        }
        fclose($fp);

        return $filename;
    }

}
