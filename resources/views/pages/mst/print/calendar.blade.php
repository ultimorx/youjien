@extends('layouts.print')

@section('title')
年間予定管理
@endsection

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
/* 列、行固定
thead {  position: sticky; top: 0; z-index:1; background-color: #343a40; }
td:nth-child(1){ position: sticky; left: 0; background-color: #eee; }
*/
th { white-space: nowrap; }
.grade5hide td:nth-child(7), .grade5hide th:nth-child(7) { display: none; }
.grade4hide td:nth-child(8), .grade4hide th:nth-child(8) { display: none; }
.grade3hide td:nth-child(9), .grade3hide th:nth-child(9) { display: none; }
.grade2hide td:nth-child(10), .grade2hide th:nth-child(10) { display: none; }
.grade1hide td:nth-child(11), .grade1hide th:nth-child(11) { display: none; }
textarea{ width: 300px; height: 200px; }

.checkboxes input[type=checkbox] { transform: scale(1.5); margin: 0 2px 0 2px; }
#list select { min-width: 100px; }

</style>

@endsection

@section('scripts')
<script>
var URL_LIST = '/api/mst/calendar';
</script>
<script src="{{asset('js/mst/calendar.js')}}?{{filemtime('js/mst/calendar.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>年間予定管理</h4>

<div class="row mb-2 d-print-none">
    <div class="col-md-4 pl-0" _style="background-color: red;">
        @php
        const IS_COMPONENTS_PARAM_READY_AND_ACTIVE = true;
        @endphp
        @include('components/yearmonth-select')
    </div>
    <div class="col-md-8 text-right">
        <button class="btn-sm btn-info" onclick="print();">この画面を印刷</button>
        <button class="btn-sm btn-outline-dark" onclick="history.back();">前の場面に戻る</button>
    </div>
</div>

<div class="row mb-2 d-none d-print-block">
    <h5 id="yearmonth"></h5>
</div>

<div class="_row table-responsive _scroll" id="list" v-cloak>
    <table class="table" id="listtable">
        <colgroup>
            <col style="width:50px;">
            <col style="width:60px;">
            <col style="width:60px;">
            <col style="width:200px;">
            <col style="width:150px;">
            <col style="width:auto;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
        </colgroup>

        <thead>
            <tr>
                <td rowspan="2"></td>
                <td rowspan="2" class="_small">未満児<br>休日</td>
                <td rowspan="2" class="_small">以上児<br>休日</td>
                <td rowspan="2">園内行事</td>
                <td rowspan="2">園外行事</td>
                <td rowspan="2">備考</td>
                <td colspan="5">子どもの活動及び・配慮事項</td>
            </tr>
            <tr>
                <td v-for="(grade) in grades">@{{ grade.name }}</td>
            </tr>
        </thead>

        <tbody>
            <tr v-for="(line) in sheet" _v-bind:class="{ 'week_sat': isSat(line.week_idx), 'week_san': isSan(line.week_idx)}" class="vtop">
                <td class="text-center">@{{ viewDate(line.date) }}<p class="pt-1 sub small">@{{ line.week }}</p></td>
                <td class="text-center"  _v-bind:class="{ 'dayoff': isDayoff(line.mimanji) }">
                    <span  v-if="isDayoff(line.mimanji)">
                        <strong class="ml-1 mr-1">休</strong>
                    </span>
                </td>
                <td class="text-center" _v-bind:class="{ 'dayoff': isDayoff(line.ijyouji) }">
                    <span v-if="isDayoff(line.ijyouji)">
                        <strong class="ml-1 mr-1">休</strong>
                    </span>
                </td>
                <td>
                    <div class="textlist" v-if="in_events[line.date]">
                        <p v-for="(event, idx) in in_events[line.date]">
                            <span>@{{ event.name }}</span>
                        </p>
                    </div>
                </td>
                <td>
                    <div class="textlist" v-if="city_out_events[line.date]">
                        <p v-for="(event, idx) in city_out_events[line.date]">
                            <span class="city_text">@{{ event.name }}</span>
                        </p>
                    </div>

                    <div class="textlist" v-if="out_events[line.date]">
                        <p v-for="(event, idx) in out_events[line.date]">
                            <span>@{{ event.name }}</span>
                        </p>
                    </div>
                </td>
                <td class="pre_wrap">@{{ line.note }}</td>
                <td v-for="(grade) in grades">
                    <ul class="textlist" v-if="actions[action_key(line.date, grade.id)]">
                        <li v-for="(act, idx) in actions[action_key(line.date, grade.id)]">
                            <span>@{{ act.action }}<small class="brk">@{{ event_name(act.event_id) }}</small></span>
                        </li>
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
</div>

@endsection()
