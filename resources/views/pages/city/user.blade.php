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
<script>
var URL_LIST = '/api/city/user';
</script>
<script src="{{asset('js/city/user.js')}}?{{filemtime('js/city/user.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>ユーザー</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <p>システムにログインするユーザーを管理します。</p>
</div>

<div class="row d-print-none">
    <div class="col-md-12 pl-0">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">ユーザー追加</button>
        <!-- <button class="btn btn-success ml-auto" id="csv-download">エクセル出力</button> -->
    </div>
</div>

<div class="row" id="list" v-cloak style="width:650px;">
    <table class="table">
        <colgroup>
            <col style="width:100px;">
            <col style="width:250px;">
            <col style="width:200px;">
            <col style="width:100px;">
            <col style="width:100px;">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>所属</th>
            <th>ユーザー名</th>
            <th>表示順</th>
            <th>ログイン</th>
        </thead>
        <tbody class="text-center">
            <tr v-for="(row) in list"　v-bind:class="{'lightgrey': isActive(row.active) }">
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                 </td>
                <td>@{{ row.kindergarten_name }}</td>
                <td>@{{ row.name }}</td>
                <td>@{{ row.order }}</td>
                <td>@{{ viewActive(row.active) }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.city.modal.edit-user')

@endsection()
