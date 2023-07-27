<?php

namespace App\Http\Controllers\Rest;

use App\Http\Controllers\Controller;

class CommonController extends Controller
{

    /**
     * ダウンロード
     */
    protected static function _download($file)
    {
        // ファイル作成(public直下)
        // ダウンロード後にファイルを削除
        // ダウンロード完了前にウィンドウを閉じるとファイルが残る
        return response()->download($file)->deleteFileAfterSend(true);
    }
}
