@extends('layouts.print')

@section('title')
月間行事予定
@endsection

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
/* 列、行固定
thead {  position: sticky; top: 0; z-index:1; background-color: #343a40; }
td:nth-child(1){ position: sticky; left: 0; background-color: #eee; }
*/
/* th { white-space: nowrap; } */
</style>

@endsection

@section('scripts')
<script>
var URL_LIST = '/api/mst/calendar';
</script>
<script src="{{asset('js/mst/calendar_bizmonth.js')}}?{{filemtime('js/mst/calendar_bizmonth.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4><span id="yearmonth"></span>行事予定表</h4>

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

<!-- <div class="row mb-2 d-none d-print-block">
    <h5 id="yearmonth"></h5>
</div> -->

<div id="list" v-cloak>
    <table class="table">
        <colgroup>
            <col style="width:80px;">
            <col style="width:50px;">
            <col style="width:35%;">
            <col style="width:35%;">
            <col style="width:auto;">
        </colgroup>

        <thead>
            <tr>
                <td>日</td>
                <td>曜日</td>
                <td>園内</td>
                <td>園外</td>
                <td>備考</td>
            </tr>
        </thead>

        <tbody v-if="sheet">
            <tr v-for="(line) in sheet" _v-bind:class="{ 'week_sat': isSat(line.week_idx), 'week_san': isSan(line.week_idx)}" class="vtop">
                <td class="text-center">@{{ viewDate(line.date) }}</td>
                <td class="text-center">@{{ line.week }}</td>
                <td class="pre_wrap">@{{ viewEvents(in_events[line.date]) }}</td>
                <td class="pre_wrap">@{{ viewEvents(city_out_events[line.date]) }}@{{ viewEvents(out_events[line.date]) }}</td>
                <td class="pre_wrap">@{{ line.note }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection()
