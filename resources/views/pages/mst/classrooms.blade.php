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
var URL_LIST = '/api/mst/classroom';
</script>
<script src="{{asset('js/mst/classroom.js')}}?{{filemtime('js/mst/classroom.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>クラス設定</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <p>準備中または稼働中の年度のクラスが表示されます。</p>
</div>

<div class="row d-print-none">
    <div class="col-md-12 pl-0">
        <button type="button" class="btn btn-primary city_hidden" data-toggle="modal" data-target="#edit-area" id="create">新年度のクラス追加</button>
        <!-- <button class="btn btn-success ml-auto" id="csv-download">エクセル出力</button> -->
    </div>
</div>

<div class="row" id="list" v-cloak style="width:650px;">
    <table class="table">
        <colgroup>
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:150px;">
            <col style="width:150px;">
            <col style="width:100px;">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>年度</th>
            <th>学年</th>
            <th>クラス名</th>
            <th>担任</th>
            <th>表示順</th>
        </thead>
        <tbody class="text-center">
            <tr v-for="(row) in list">
                <td>
                    <button type="button" class="btn btn-primary city_hidden" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                  </td>
                <td>@{{ row.bizyear }}</td>
                <td>@{{ row.grade.name }}</td>
                <td>@{{ row.name }}</td>
                <td>@{{ row.teacher }}</td>
                <td>@{{ row.order }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.mst.modal.edit-classroom')

@endsection()
