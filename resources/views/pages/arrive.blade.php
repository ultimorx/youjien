@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/arrive.js')}}?{{filemtime('js/arrive.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>早朝一覧</h4>

<div class="row d-print-none alert alert-warning small" role="alert">
    このページで<span class="badge badge-primary">出席</span>を押すと<strong>早朝利用</strong>したことになります。
</div>

<div class="row mb-2">
    <div class="col-md-8 pl-0">
        @include('components/date-select')
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-info ml-auto d-print-none" onclick="print();">この画面を印刷</button>
    </div>
</div>

<div class="row" id="dayoff" v-cloak>
    <h6>未満児：@{{ mimanji }}　　以上児：@{{ ijyouji }}</h6>
</div>

<div class="row" id="children-list" v-cloak v-bind:class="{ loading: is_loading }">
    <table>
        <tr v-for="morning_time in morning_time_rostors" v-show="morning_time.rosters.length >= 1">
            <td>@{{ morning_time.time }}</td>
            <td class="pl-2">@{{ morning_time.rosters.length }}人</td>
        </tr>
    </table>

    </table>
    <table class="table table-striped _sheet" v-for="morning_time in morning_time_rostors" v-show="morning_time.rosters.length >= 1">
        <caption>
            登園時間 @{{ morning_time.time }}の一覧
            <span class="pl-2">@{{ morning_time.rosters.length }}人</span>
        </caption>
        <colgroup>
            <col style="width:70px;">
            <col style="width:200px;">
            <col style="width:auto;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col>
        </colgroup>
        <thead>
            <tr>
                <th>早朝</th>
                <th>クラス</th>
                <th>園児名</th>
                <th>出席</th>
                <th>欠席</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(roster, index) in morning_time.rosters">
                <th scope="row">@{{ morning_time.time }}</th>
                <td>
                    @{{ roster.classroom.name }}
                    <!-- （@{{ roster.classroom.grade.name }}） -->
                    <!-- <small>@{{ roster.classroom.bizyear }}年度</small> -->
                </td>
                <td><small>@{{ roster.child.kana }}</small><br>@{{ roster.child.name }}</td>
                <td class="text-center">
                    <span class="d-none">roster_id:"@{{ roster.id }}" at:"@{{ roster.attendance.attendance }}" at-date:"@{{ roster.attendance.date }}"</span>
                    <button v-if="roster.attendance.attendance === null" class="btn btn-primary city_hidden" @click="arrive(roster.attendance)">出席</button>
                    <button v-else-if="roster.attendance.attendance === 0" class="btn btn-outline-primary city_hidden" @click="arrive(roster.attendance)">出席</button>
                    <strong v-else>出席</strong>
                </td>
                <td class="text-center">
                    <button v-if="roster.attendance.attendance === null" class="btn btn-dark city_hidden" @click="absence(roster.attendance)">欠席</button>
                    <button v-else-if="roster.attendance.attendance === 1" class="btn btn-outline-dark city_hidden" @click="absence(roster.attendance)">欠席</button>
                    <strong v-else>欠席</strong>
                </td>
                <td>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection()
