@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/evening.js')}}?{{filemtime('js/evening.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>延長一覧</h4>

<div class="row mb-3">
    @include('components/date-select')
    <button class="btn btn-info ml-auto d-print-none" onclick="print();">この画面を印刷</button>
</div>

<div class="row position-relative" id="children-list" v-cloak>
    <!--
    <div class="position-absolute fixed-right d-print-none">
        <button type="button" class="btn btn-secondary" @click="check()" v-if="rosters.length != 0">すべてチェックする</button>
        <button type="button" class="btn btn-primary" @click="depart()" v-if="rosters.length != 0">チェックしたものを降園にする</button>
    </div>
    -->
    <table>
        <tr v-for="evening_time in evening_time_rostors" v-show="evening_time.rosters.length >= 1">
            <td>@{{ evening_time.time }}</td>
            <td class="pl-2">@{{ evening_time.rosters.length }}人</td>
        </tr>
    </table>

    <table class="table table-striped sheet" v-for="evening_time in evening_time_rostors" v-show="evening_time.rosters.length >= 1">
        <caption>
            延長一覧 @{{ evening_time.time }}
            <span class="pl-2">@{{ evening_time.rosters.length }}人</span>
        </caption>
        <thead>
            <tr>
                <th class="w-20">クラス</th>
                <th class="w-30">園児名</th>
                <th class="w-10">性別</th>
                <th class="w-10">降園</th>
                <th class="w-30">{{-- ボタン --}} </th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr v-for="(roster, index) in evening_time.rosters">
                <td class="text-left">
                    @{{ roster.classroom.name }}
                    <!-- @{{ roster.classroom.grade.name }} -->
                    <!-- <small>@{{ roster.classroom.bizyear }}年度</small> -->
                </td>
                <td class="text-left">
                    <div class="small">@{{ roster.child.kana }}</div>
                    <div>@{{ roster.child.name }}</div>
                </td>
                <td>@{{ roster.child.gender === 1 ? '男' : '女' }}</td>
                <td>
                    <strong v-if="roster.attendance.outtime">@{{ roster.attendance.outtime }}</strong>
                    <button type="button" class="btn btn-primary city_hidden" data-toggle="modal" data-target="#edit-area" @click="edit(roster.attendance)" v-else>降園</button>
                </td>
                <td>
                    <span v-if="roster.attendance.early" class="earlyed"></span>
                    <div v-else class="d-flex">
                        <button type="button" class="btn btn-secondary mr-1 city_hidden" data-toggle="modal" data-target="#edit-area" @click="edit(roster.attendance)" v-if="roster.attendance.outtime">修正</button>
                        <button type="button" class="btn btn-danger city_hidden" @click="revert(roster.attendance)" v-if="roster.attendance.outtime">降園取消</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.depart.evening.edit-area')

@endsection()
