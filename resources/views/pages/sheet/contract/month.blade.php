@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
table.print td:nth-child(1) {
    text-align: left !important;
    white-space: nowrap;
}
table.print td:nth-child(3),
table.print td:nth-child(7),
table.print td:nth-child(8),
table.print td:nth-child(12)
{
    border-left-width: 3px;
}

table.print tr:nth-child(1),
table.print tr:nth-child(2),
table.print tr:last-child
{
    font-weight: bold;
}
</style>
@endsection

@section('scripts')
<script>
var URL_LIST = '/api/sheet/contract/month/list';
var URL_DOWNLOAD = '/api/sheet/contract/month/download';
</script>
<script src="{{asset('js/sheet/list.js')}}?{{filemtime('js/sheet/list.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>預かり人数表（月間）</h4>

<img src="{{ asset('img/contract_count.png') }}" alt="" style="width: 1000px;" class="d-none">

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/yearmonth-select')
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-info ml-auto" onclick="print();">この画面を印刷</button>
        <button class="btn btn-success ml-auto" id="download">エクセル出力</button>
    </div>
</div>

<div class="row mb-2 d-none d-print-block">
    <h5 id="yearmonth"></h5>
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

@endsection()
