@extends('layouts.print')

@section('title')
年間行事予定
@endsection

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
.items { display:flex; justify-content: space-between; flex-wrap: wrap; }
/* .items { display:table-cell; width:100%;} */
.item { width: 32% !important; display: table-cell;}
/* .item { width: 300px !important; display: table-cell;} */
/* .item:nth-child(2n) { page-break-after: always; } */
.items { page-break-after: always; }
#list table td:nth-child(1) { width:25px; }
#list table td:nth-child(2) { width:25px; }
#list table td:nth-child(3),#list table td:nth-child(4) { min-width:130px; }
#list table td{ vertical-align: top; }
</style>

@endsection

@section('scripts')
<script>
var URL_LIST = '/api/mst/calendar';
</script>
<script src="{{asset('js/mst/calendar_bizyear.js')}}?{{filemtime('js/mst/calendar_bizyear.js')}}"></script>
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

<div id="list" v-cloak>
    <div class="items" v-for="(months) in print_bizmonths">
        <table class="item table" v-for="(month) in months">
            <thead>
                <tr>
                    <td colspan="2">@{{ month }}月</td>
                    <td>園内</td>
                    <td>園外</td>
                </tr>
            </thead>

            <tbody v-if="sheet[month]">
                <tr v-for="(line) in sheet[month]">
                    <td class="text-center">@{{ viewDay(line.date) }}</td>
                    <td class="text-center">@{{ line.week }}</td>
                    <td class="pre_wrap">@{{ viewEvents(in_events[line.date]) }}</td>
                    <!-- <td class="pre_wrap" v-html=“txt_in_events[date]”></td> -->
                    <td class="pre_wrap">@{{ viewEvents(city_out_events[line.date]) }}@{{ viewEvents(out_events[line.date]) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection()
