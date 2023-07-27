@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/mst/children_import.js')}}?{{filemtime('js/mst/children_import.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>園児CSV登録</h4>

<form accept-charset="utf-8" id="edit-form" class="_form-inline" method="post" enctype="multipart/form-data" action="/children/import">
    <div class="form-group row">
        <label class="col-3 col-form-label">CSVファイル</label>
        <div class="col-5">
            <div class="custom-file">
                <input type="file" name="csv" class="custom-file-input" id="csvfile">
                <label class="custom-file-label" for="csvfile" data-browse="参照">ファイル選択...</label>
            </div>

        </div>
        <div class="col-2">
            <a class="btn btn-light ml-2" href="/csv/sample_children_import.csv" target="_blank">サンプルCSV</a>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-3 col-form-label"></label>
        <div>
            <span id="btn-import" class="btn btn-primary">取り込み</span>
            <a href="{{ url('mst/children') }}" class="btn btn-light ml-5">園児一覧ページへ戻る</a>
        </div>
    </div>
</form>



<div class="row mt-4" id="list" v-cloak style="width:800px; display: none;">
    <h4>取り込み結果</h4>
    <p class="red" v-html="error_msg"></p>

    <div v-if="list!=null">
        <h5 class="mt-2">今回登録した園児一覧</h5>
        <p>@{{ result_msg }}</p>
        <table class="table">
            <colgroup>
                <col style="width:200px;">
                <col style="width:120px;">
                <col style="width:50px;">
                <col style="width:auto;">
            </colgroup>
            <thead>
                <th>園児名</th>
                <th>生年月日</th>
                <th>性別</th>
                <th>備考</th>
            </thead>
            <tbody class="text-center">
                <tr class="text-center" v-for="(row) in list">
                    <td><div class="small">@{{ row.kana }}</div>@{{ row.name }}</td>
                    <td>@{{ row.birthday }}</td>
                    <td>@{{ row.gender === 1 ? '男' : '女' }}</td>
                    <td class="text-left">
                        <p class="pre_wrap">@{{ row.remarks }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


@endsection()
