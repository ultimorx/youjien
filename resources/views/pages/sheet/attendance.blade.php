@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
td:nth-child(1) {
    width: 80px;
}
td:nth-child(2)
{
    text-align: left !important;
    white-space: nowrap;
}
table.print tr:nth-child(1),
table.print tr:last-child
{
    font-weight: bold;
}
</style>
@endsection

@section('scripts')
<script>
var URL_LIST = '/api/sheet/attendance/list';
var URL_DOWNLOAD = '/api/sheet/attendance/download';
</script>
<script src="{{asset('js/sheet/list_yearmonth_classroom.js')}}?{{filemtime('js/sheet/list_yearmonth_classroom.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>出席統計</h4>

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/yearmonth-classroom-select')
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-info ml-auto" onclick="print();">この画面を印刷</button>
        <button class="btn btn-success ml-auto" id="download">エクセル出力</button>
    </div>
</div>

<div class="row mb-2 d-none d-print-block">
    <h5>
        <span id="yearmonth"></span>　<span id="classroom"></span>
    </h5>
</div>

<div class="row" id="list" v-cloak>
    <table class="table print"><!-- .table print -->
        <tbody>
            <tr v-for="(line, line_idx) in sheet">
                <td v-for="(cell, cell_idx) in line">@{{ cell }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-print-none d-none sample">
    <img src="{{ asset('img/class.png') }}" alt="" style="min-width: 1000px;">
</div>

@endsection()
