@php
use App\Models\Bizyear;
@endphp
@extends('layouts.default-wide')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
/* thead {  position: sticky; top: 0; z-index:1; background-color: #343a40; } */
.table thead th {  position: sticky; top: 0; z-index:1; background-color: #343a40; }
th:nth-child(1), td:nth-child(1){ width: 100px; }
th:nth-child(2), td:nth-child(2){ width: 30%; min-width: 200px; }
th:nth-child(3), td:nth-child(3){ width: 30%; min-width: 200px; }
th:nth-child(4), td:nth-child(4){ width: auto; }

textarea{ width: 100%; height: 200px; }
</style>
<style media="print">
td:nth-child(1)
{
    display: none;
}
</style>

@endsection

@section('scripts')
<script>
</script>
<script src="{{asset('js/mst/aim.js')}}?{{filemtime('js/mst/aim.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>週のねらい</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <ul>
        <li>編集したい「年度」「学年」を選択します。</li>
        <li class="city_hidden">「遊び」「生活」「備考」の入力欄は<strong>自動保存</strong>されます。</li>
        <li>入力された内容は、<a href="/mst/reports">幼児園日誌</a>に表示されます。</li>
    </ul>
</div>

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/year-grade-select')
    </div>
    <!-- <div class="col-md-4 text-right">
        <button class="btn btn-info ml-auto" onclick="print();">この画面を印刷</button>
        <button class="btn btn-success ml-auto" id="download">エクセル出力</button>
    </div> -->
</div>

<div class="row mb-2 d-none d-print-block">
    <h5 id="yearmonth"></h5>
</div>

<div class="_row table-responsive scroll" id="list" v-cloak>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>遊び</th>
                <th>生活</th>
                <th>備考</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(line) in sheet">
                <td class="text-center">
                    @{{ viewDate(line.date) }}
                    <p class="pt-1 small sub"><span class="bizweek">@{{ line.bizweek }}</span></p>
                </td>
@if (\Login::is_city())
                <td  class="pre_wrap">@{{ line.play }}</td>
                <td  class="pre_wrap">@{{ line.life }}</td>
                <td  class="pre_wrap">@{{ line.note }}</td>
@else
                <td><textarea @blur="update(line)" v-model="inputs[line.id]['play']">@{{ line.play }}</textarea></td>
                <td><textarea @blur="update(line)" v-model="inputs[line.id]['life']">@{{ line.life }}</textarea></td>
                <td><textarea @blur="update(line)" v-model="inputs[line.id]['note']">@{{ line.note }}</textarea></td>
@endif
            </tr>
        </tbody>
    </table>
</div>


@endsection()
