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
var URL_LIST = '/api/mst/rosters';
</script>
<script src="{{asset('js/mst/roster.js')}}?{{filemtime('js/mst/roster.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>クラス決め</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <ul>
        <li>新年度のクラス決め、年度途中でのクラス替えを行います。</li>
        <li>該当する<span class="badge badge-secondary">クラス名</span>のボタンを押します。</li>
        <li>クラスボタンを押すと、<strong class="red">生年月日を元に出席番号が自動変更</strong>します。</li>
        <li>園児の契約設定は、<a href="/children">園児一覧</a>ページで行います。</li>
    </ul>
</div>

<div class="row d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/year-grade-select-count')
    </div>
    <div class="col-md-4 text-right">
        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">入園する園児を追加</button> -->
    </div>
</div>


<div class="row" id="list" v-cloak style="width:auto;">
    <table class="table">
        <colgroup>
            <col style="width:60px;">
            <col style="width:200px;">
            <col style="width:120px;">
            <col style="width:50px;">
            <col style="width:80px;" v-for="(classroom) in classrooms">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>園児名</th>
            <th>生年月日</th>
            <th>性別</th>
            <th v-for="(classroom) in classrooms">@{{ classroom.name }}<br><span class="unit_person">@{{ count_classroom_child(classroom.id) }}</span></th>
        </thead>
        <tbody class="text-center">
            <tr class="text-center" v-for="(child) in list" v-bind:class="{ 'table-secondary': child.move_out_date }">
                <td>
                    <button type="button" class="btn btn-primary city_hidden" data-toggle="modal" data-target="#edit-area" @click="edit(child)">編集</button>
                </td>
                <td><div class="small">@{{ child.kana }}</div>@{{ child.name }}</td>
                <td>@{{ child.birthday }}</td>
                <td>@{{ child.gender === 1 ? '男' : '女' }}</td>
                <td class="pl-2 pr-2 small" v-for="(classroom, idx) in classrooms" v-bind:class="{ 'table-warning': match_child_classroom(child.id, classroom.id)}">
                    <button type="button" class="btn btn-secondary text-white small city_hidden" @click="save(child.id, classroom.id)"  v-bind:class="{ 'btn-warning': match_child_classroom(child.id, classroom.id)}">@{{ classroom.name }}</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.mst.modal.edit-child')

@endsection()
