@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
table.print  {
    border-width: 2px;
}
table.print td {
    border-width: 2px;
}
</style>
@endsection

@section('scripts')
<script>
var URL_LIST = '/api/sheet/attendance/stats/list';
var URL_DOWNLOAD = '/api/sheet/attendance/stats/download';
var KEY_GENDER_M = 1;
var KEY_GENDER_W = 2;
var KEY_DAYS = 'childcareDays';
</script>
<script src="{{asset('js/sheet/list_year_classroom_grade.js')}}?{{filemtime('js/sheet/list_year_classroom_grade.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>統計表</h4>

<div class="row mb-2 d-print-none">
    <div class="col-md-6 pl-0">
        @include('components/year-classroom-grade-select-form')
    </div>
    <div class="col-md-2 text-right">
        <button class="btn btn-info ml-auto d-print-none" onclick="print();">この画面を印刷</button>
    </div>
</div>

<div class="row mb-2 d-none d-print-block">
    <h5>
        <span id="year"></span>　<span id="classroom"></span>
    </h5>
</div>

<table class="print" id="list">
    <colgroup>
        <col style="width:60px;">
        <col style="width:60px;">
        <col style="width:60px;">
        <col style="width:60px;">
        <col style="width:45px;">
        <col style="width:45px;">
        <col style="width:45px;">
        <col style="width:45px;">
        <col style="width:45px;">
        <col style="width:45px;">
        <col style="width:45px;">
        <col style="width:60px;">
        <col>
    </colgroup>
    <tbody>
        <tr class="midashi">
            <td rowspan="2"><em>校長印</em></td>
            <td rowspan="2"><em>教頭印</em></td>
            <td rowspan="2"><em>係　印</em></td>
            <td rowspan="2"><em>担任印</em></td>
            <td colspan="3" rowspan="2" class="none">&nbsp;</td>
            <td colspan="4">児童生徒数</td>
            <td rowspan="2"><em>授業日数</em></td>
        </tr>
        <tr>
            <td><em>転入学</em></td>
            <td><em>転退学</em></td>
            <td><em>在　籍</em></td>
            <td><em>全　欠</em></td>
        </tr>

        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="10"><em>第１学期</em></td>
            <td rowspan="2">4</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[4][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[4][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[4][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">5</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[5][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[5][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[5][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">6</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[6][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[6][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[6][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">7</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[7][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[7][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[7][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td class="bold" rowspan="2">計</td>
            <td class="bold dot_b">男</td>
            <td class="bold total dot_b" v-for="(cell) in sheet['firstterm'][KEY_GENDER_M]">@{{ cell }}</td>
            <td class="bold total dot_b none" rowspan="2">&nbsp;</td>
            <td class="bold total dot_b none" rowspan="2">&nbsp;</td>
            <td class="bold total" rowspan="2">@{{ sheet['firstterm'][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t total" v-for="(cell) in sheet['firstterm'][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>

        <tr class="bold">
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="14"><em>第２学期</em></td>
            <td rowspan="2">8</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[8][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[8][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[8][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">9</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[9][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[9][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[9][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">10</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[10][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[10][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[10][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">11</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[11][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[11][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[11][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">12</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[12][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[12][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[12][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td class="bold" rowspan="2">計</td>
            <td class="bold dot_b">男</td>
            <td class="bold total dot_b" v-for="(cell) in sheet['secondterm'][KEY_GENDER_M]">@{{ cell }}</td>
            <td class="bold total dot_b none" rowspan="2">&nbsp;</td>
            <td class="bold total dot_b none" rowspan="2">&nbsp;</td>
            <td class="bold total" rowspan="2">@{{ sheet['secondterm'][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t total" v-for="(cell) in sheet['secondterm'][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td class="bold" rowspan="2"><em>累計</em></td>
            <td class="bold dot_b">男</td>
            <td class="bold total dot_b" v-for="(cell) in sheet['secondtermtotal'][KEY_GENDER_M]">@{{ cell }}</td>
            <td class="bold total dot_b none" rowspan="2">&nbsp;</td>
            <td class="bold total dot_b none" rowspan="2">&nbsp;</td>
            <td class="bold total" rowspan="2">@{{ sheet['secondtermtotal'][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t total" v-for="(cell) in sheet['secondtermtotal'][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>

        <tr class="bold">
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="10"><em>第３学期</em></td>
            <td rowspan="2">1</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[KEY_GENDER_M][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[KEY_GENDER_M][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[KEY_GENDER_M][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">2</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[KEY_GENDER_W][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[KEY_GENDER_W][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[KEY_GENDER_W][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">3</td>
            <td class="dot_b">男</td>
            <td class="dot_b" v-for="(cell) in sheet[3][KEY_GENDER_M]">@{{ cell }}</td>
            <td rowspan="2">@{{ sheet[3][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t" v-for="(cell) in sheet[3][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td class="bold" rowspan="2">計</td>
            <td class="bold dot_b">男</td>
            <td class="bold total dot_b" v-for="(cell) in sheet['thirdterm'][KEY_GENDER_M]">@{{ cell }}</td>
            <td class="bold total none" rowspan="2">&nbsp;</td>
            <td class="bold total none" rowspan="2">&nbsp;</td>
            <td class="bold total" rowspan="2">@{{ sheet['thirdterm'][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t total" v-for="(cell) in sheet['thirdterm'][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>
        <tr>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td rowspan="2">&nbsp;</td>
            <td class="bold" rowspan="2"><em>累計</em></td>
            <td class="bold dot_b">男</td>
            <td class="bold total dot_b" v-for="(cell) in sheet['thirdtermtotal'][KEY_GENDER_M]">@{{ cell }}</td>
            <td class="bold total none" rowspan="2">&nbsp;</td>
            <td class="bold total none" rowspan="2">&nbsp;</td>
            <td class="bold total" rowspan="2">@{{ sheet['thirdtermtotal'][KEY_DAYS] }}</td>
        </tr>
        <tr>
            <td class="dot_t">女</td>
            <td class="dot_t total" v-for="(cell) in sheet['thirdtermtotal'][KEY_GENDER_W]">@{{ cell }}</td>
        </tr>

    </tbody>
</table>

<div class="d-print-none d-none sample">
    <img src="{{ asset('img/stats.png') }}" alt="">
</div>

@endsection()
