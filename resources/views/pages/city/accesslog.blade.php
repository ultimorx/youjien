@extends('layouts.city')

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
<script src="{{asset('js/city/accesslog.js')}}?{{filemtime('js/city/accesslog.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>ログイン履歴</h4>

<div id='accesslog' class="row d-print-none alert _alert-warning small" role="note">
    <p>システムにログインした最新@{{ count }}件までの履歴を表示します。</p>
</div>

<div class="row d-none">
    <div class="col-md-12 pl-0">
        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">ユーザー追加</button> -->
        <!-- <button class="btn btn-success ml-auto" id="csv-download">エクセル出力</button> -->
    </div>
</div>

<div class="row" id="list" v-cloak style="width:650px;">
    <table class="table">
        <colgroup>
            <col style="width:200px;">
            <col style="width:250px;">
            <col style="width:200px;">
        </colgroup>
        <thead>
            <th>日時</th>
            <th>所属</th>
            <th>ユーザー名</th>
        </thead>
        <tbody class="text-center">
            <tr v-for="(row) in list" v-bind:ip="row.ip">
                <td>@{{ row.datetime }}</td>
                <td>@{{ row.kindergarten_name }}</td>
                <td>@{{ row.user_name }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection()
