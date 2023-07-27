@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/bus.js')}}?{{filemtime('js/bus.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>バス一覧</h4>

<div class="row mb-3">
    @include('components/date-select')
    <button class="btn btn-info ml-auto d-print-none" onclick="print();">この画面を印刷</button>
</div>

<div class="row position-relative" id="children-list" v-cloak>
    <table>
        <tr v-for="bus in buses" v-show="bus.rosters.length >= 1">
            <td>@{{ bus.name }}</td>
            <td class="pl-2">@{{ bus.rosters.length }}人</td>
        </tr>
    </table>

    <div class="position-absolute fixed-right d-print-none">
        <button type="button" class="btn btn-secondary city_hidden" @click="check()" v-if="buses.length != 0">すべてチェックする</button>
        <button type="button" class="btn btn-primary city_hidden" @click="depart()" v-if="buses.length != 0">チェックしたものを降園にする</button>
    </div>
    <table class="table table-striped sheet" v-for="bus in buses">
        <caption>
            @{{ bus.name }}の一覧
            <span class="pl-2">@{{ bus.rosters.length }}人</span>
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
            <tr v-for="(roster, index) in bus.rosters">
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
                    <strong v-if="roster.attendance.outtime" class="departed"></strong><!-- 降園時間は非表示 @{{ roster.attendance.outtime }} -->
                    <div class="custom-control custom-checkbox city_hidden" v-else>
                        <input type="checkbox" class="custom-control-input" v-bind:id="'check['+roster.attendance.id+']'" name="check[]" v-bind:value="roster.attendance.id">
                        <label class="custom-control-label" v-bind:for="'check['+roster.attendance.id+']'"></label>
                    </div>
                </td>
                <td>
                    <span v-if="roster.attendance.early" class="earlyed"></span>
                    <span v-else>
                        <button type="button" class="btn btn-danger city_hidden" @click="revert(roster.attendance)" v-if="roster.attendance.outtime">降園取消</button>
                        <button type="button" class="btn btn-warning city_hidden" @click="wait(roster.attendance)" v-else>お迎えに変更</button>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection()
