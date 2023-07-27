@php
use App\Models\Bizyear;
@endphp
@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style media="print">
td:nth-child(1)
{
    display: none;
}
</style>

@endsection

@section('scripts')
<script>
var URL_LIST = '/api/mst/children';
</script>
<script src="{{asset('js/mst/children.js')}}?{{filemtime('js/mst/children.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>園児管理</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <ul>
        <li>すでに登録されている園児は、年度毎に学年が自動で繰り上がります。</li>
        <li>新年度に入園する園児は、<span class="badge badge-primary">入園する園児を追加</span>を押して登録します。</li>
        <li>今年度で退園する園児は、<span class="badge badge-primary">編集</span>を押して<strong>転出日</strong>を入力します。</li>
        <li>指定した年度の在籍対象となる園児のみ一覧に表示されます。</li>

    </ul>
</div>
<div class="kindergarten_hidden alert alert-warning small" role="note">
    <strong>【園児CSV登録について】</strong><br>
    <ul>
        <li>園児CSV登録ができるのは本巣市ユーザーのみです。</li>
        <li>各園では<span class="badge badge-primary">園児CSV登録</span>ボタンも表示されません。</li>
    </ul>
    <br>
    <strong>【園児削除について】</strong><br>
    <ul>
        <li>過去も含めクラス決めを行ったことのある園児は、在籍したとみなし削除はできません。</li>
        <li>新規登録後（クラス決めを行う前）は園児の削除が可能です。</li>
        <li>削除ができるのは本巣市ユーザーのみです。</li>
        <li>各園では削除はできません。（この説明文も表示されません）</li>
    </ul>
</div>

<div class="row d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/year-grade-select-count')
    </div>
    <div class="col-md-4 text-right">
        <a class="btn btn-primary kindergarten_hidden" href="{{ url('mst/children/import') }}">園児CSV登録</a>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">入園する園児を追加</button>
        <button class="btn btn-success ml-auto" id="csv-download">エクセル出力</button>
    </div>
</div>


<div class="row" id="list" v-cloak style="width:100%;">
    <table class="table">
        <colgroup>
            <col style="width:60px;">
            <col style="width:200px;">
            <col style="width:120px;">
            <col style="width:50px;">
            <col style="width:auto;">
            <col style="width:120px;">
            <col style="width:120px;">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>園児名</th>
            <th>生年月日</th>
            <th>性別</th>
            <th>備考</th>
            <th>転入日</th>
            <th>転出日</th>
        </thead>
        <tbody class="text-center">
            <tr class="text-center" v-for="(row) in list" v-bind:class="{ 'table-secondary': row.move_out_date }">
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                </td>
                <td><div class="small">@{{ row.kana }}</div>@{{ row.name }}</td>
                <td>@{{ row.birthday }}</td>
                <td>@{{ row.gender === 1 ? '男' : '女' }}</td>
                <td class="text-left text-truncate position-relative" style="max-width: 100px;">
                    <span class="stretched-link" v-bind:title="row.remarks">@{{ row.remarks }}</span>
                </td>
                <td class="small">@{{ row.move_in_date }}</td>
                <td class="small">@{{ row.move_out_date }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.mst.modal.edit-child')

@endsection()
