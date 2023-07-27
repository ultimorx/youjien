@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
tr:nth-child(1) td,
tr:last-child td
{
    font-weight: bold;
}
</style>
<style media="print">
td:nth-child(1)
{
    display: none;
}
tr:nth-child(1) td,
tr:last-child td
{
    font-weight: bold;
}
</style>
@endsection

@section('scripts')
<script>
var URL_LIST = '/api/sheet/contract/list';
var URL_DOWNLOAD = '/api/sheet/contract/list/download';
var URL_DETAIL = '/sheet/contract/child';
</script>
<script src="{{asset('js/sheet/list_link.js')}}?{{filemtime('js/sheet/list_link.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>時間外保育契約者一覧</h4>

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
                <td v-for="(cell, cell_idx) in line">
                    <span v-if="cell_idx == 0 && line_idx >= 1 && line_idx<(sheet.length-1)" class="btn btn-primary" @click="detail(cell, line)">詳細</span> <!-- data-toggle="modal" data-target="#popup-area" -->
                    <span v-else>@{{ cell }}</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="d-print-none d-none sample">
    <img src="{{ asset('img/contract_text1.png') }}" alt="" style="max-width: 1000px;">
    <img src="{{ asset('img/contract_text2.png') }}" alt="" style="max-width: 1000px;">
    <img src="{{ asset('img/contract_list.png') }}" alt="" style="max-width: 1000px;">
</div>


@endsection()
