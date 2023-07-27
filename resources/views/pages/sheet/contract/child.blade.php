@extends('layouts.close')

@php
use App\Models\Roster;
use App\Util\Time;
$roster = Roster::contract_month($roster_id, $month);
$morning_time = (isset($roster->morning_time)) ? Time::hour_minute($roster->morning_time): '契約なし';
$evening_time = (isset($roster->evening_time)) ? Time::hour_minute($roster->evening_time): '契約なし';
@endphp

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
table.print tr:nth-child(1),
table.print tr:last-child
{
    font-weight: bold;
}
</style>
@endsection

@section('scripts')
<script>
var URL_LIST = '/api/sheet/contract/child';
var URL_DOWNLOAD = '/api/sheet/contract/child/download';
var ROSTER_ID = {{ $roster_id }};
var DEFAULT_YEAR = {{ $bizyear }};
var DEFAULT_MONTH = {{ $month }};
</script>
<script src="{{asset('js/sheet/list_child.js')}}?{{filemtime('js/sheet/list_child.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>時間外保育契約者詳細</h4>

<h5>{{$roster->classroom->name}}　{{$roster->child->name}}<small>（{{$roster->child->kana}}）</small></h5>

<div class="row mb-2 d-none d-print-block">
    <h5 id="yearmonth"></h5>
</div>

<h6>早朝契約：{{ $morning_time }}</h6>
<h6>延長契約：{{ $evening_time }}</h6>
<div class="row mb-2 d-print-none">
    <div class="col-md-4 pl-0">
        @include('components/month-select')
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-info ml-auto" onclick="print();">この画面を印刷</button>
        <button class="btn btn-success ml-auto" id="download">エクセル出力</button>
    </div>
</div>


<div class="row" id="list" v-cloak>
    <div class="col-md-8">
        <table class="table print">
            <tbody>
                <tr v-for="(line, line_idx) in sheet">
                    <td v-for="(cell, cell_idx) in line">@{{ cell }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!--
<div class="d-print-none">
<img src="{{ asset('img/contract_child.png') }}" alt="" style="width: 1000px;">
</div>
-->

@endsection()
