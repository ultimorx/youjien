@extends('layouts.city')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/city/absence.js')}}?{{filemtime('js/city/absence.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>病欠理由</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <p>表示順は、各園の「クラス出席簿」の欠席理由や「欠席集計表」の表示順に影響します。</p>
    <p class="text-danger font-weight-bold">病欠理由の状態を未使用にすると、各園の画面には表示されなくなります。また、過去の集計処理からも除外されます。</p>
    <p>「出席停止」「忌引」「病欠」「事故欠」の種類は、変更できません。</p>
</div>

<div class="row d-print-none">
    <div class="col-md-12 pl-0">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">新規追加</button>
    </div>
</div>

<div class="row" id="list" v-cloak style="width:700px;">
    <table class="table">
        <colgroup>
            <col style="width:100px;">
            <col style="width:200px;">
            <col style="width:200px;">
            <col style="width:100px;">
            <col style="width:100px;">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>種類</th>
            <th>名称</th>
            <th>表示順</th>
            <th>状態</th>
        </thead>
        <tbody class="text-center">
            <tr v-for="(row) in list"　v-bind:class="{'lightgrey': isActive(row.active) }">
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                </td>
                <td>@{{ row.absence_type_name }}</td>
                <td>@{{ row.name }}</td>
                <td>@{{ row.order }}</td>
                <td>@{{ viewActive(row.active) }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.city.modal.edit-absence')

@endsection()
