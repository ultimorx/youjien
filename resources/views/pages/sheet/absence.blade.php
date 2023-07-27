@extends('layouts.default-wide')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
td:nth-child(3)
{
    text-align: left !important;
    white-space: nowrap;
}
</style>
@endsection

@section('scripts')
<script>
var URL_LIST = '/api/sheet/absence/list';
var URL_DOWNLOAD = '/api/sheet/absence/download';
</script>
<script src="{{asset('js/sheet/list_yearmonth_classroom.js')}}?{{filemtime('js/sheet/list_yearmonth_classroom.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>欠席集計表</h4>

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @php
        $all_classroom = true;
        @endphp
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
    <table class="table print">
        <tbody>
            <tr v-for="(line, line_idx) in sheet">
                <td v-for="(cell, cell_idx) in line">@{{ cell }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-print-none d-none sample">
    <img src="{{ asset('img/disease.png') }}" alt="" style="width: 1000px;">
    <img src="{{ asset('img/disease2.png') }}" alt="" style="width: 1000px;">
</div>

@endsection()
