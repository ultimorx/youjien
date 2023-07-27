@php
use App\Models\CityInformation;
use App\Util\Str;
$informations = CityInformation::list_actives();

function viewDate($d) {
    return date('y/n/j', strtotime($d));
}
@endphp

@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/top.js')}}?{{filemtime('js/top.js')}}"></script>
@endsection

@include('components/header')

@section('content')

<!--
Sess::get_view_kindergarten_id : {{ Sess::get_view_kindergarten_id() }}<br>
Sess::get_view_kindergarten_name : {{ Sess::get_view_kindergarten_name() }}<br>
<br>
-->

<h5 class="float-left mb-0">本巣市からの連絡</h5>
<small id="information_hide_message" class="float-left btn btn-outline-secondary" style="display:none;">件名のみ表示</small>
<small id="information_show_message" class="float-left btn btn-outline-secondary">内容を表示</small>

<div class="information_wrap" id="information">
@foreach($informations as $information)
    <div class="row information_rec">
        <div class="col-2 strong">
            {{ viewDate($information->public_date) }}
@if (strtotime($information->public_date) >= strtotime('-7 day'))
            <span class="red small">新着</span>
@endif
        </div>
        <div class="col-10 strong information_title" ref_id="info{{ $information->id }}">{{ $information->title }}</div>
        <div class="col-12 mt-1 ml-3 pre_wrap information_message" id="info{{ $information->id }}">{!! Str::url2link($information->message) !!}</div>
    </div>
@endforeach()
</div>

<h4>登園<small>（園児が来た時に行うこと）</small></h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-primary btn-lg" href="{{ url('arrive') }}">早朝一覧</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-primary btn-lg" href="{{ url('attendance/class') }}">クラス出席簿</a>
    </span>
</nav>

<h4>降園<small>（園児が帰る時に行うこと）</small></h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-success btn-lg" href="{{ url('depart/bus') }}">バス一覧</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-success btn-lg" href="{{ url('depart/daytime') }}">お迎え一覧</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-success btn-lg" href="{{ url('depart/evening') }}">延長一覧</a>
    </span>
</nav>

<h4>基本情報<small>（日報や各園児の延長契約など）</small></h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-info btn-lg" href="{{ url('mst/reports') }}">幼児園日誌</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-info btn-lg" href="{{ url('children') }}">園児一覧</a>
    </span>
    <!-- <span class="nav-item nav-link">
        <a class="btn btn-info btn-lg" href="{{ url('mst/calendar') }}">休日設定</a>
    </span> -->
</nav>

<h4>利用状況</h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/attendance') }}">出席統計</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/attendance/total') }}">出席統計集計</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/absence') }}">欠席集計表</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/attendance/stats') }}">統計表</a>
    </span>
<!-- </nav>

<h4>時間外保育</h4> -->
<!-- <nav class="nav nav-pills" style="margin-bottom:15px;"> -->
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/contract/count') }}">時間外保育契約者数</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/contract/list') }}">時間外保育契約者一覧</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/contract/month') }}">預かり人数表（月間）</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/contract/year') }}">預かり人数表（年間）</a>
    </span>
    <!-- <span class="nav-item nav-link">
        <a class="btn btn-danger btn-lg" href="{{ url('sheet/contract/child') }}">契約者詳細</a>
    </span> -->
</nav>

<h4>年度更新</h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-secondary btn-lg" href="{{ url('mst/bizyears') }}">年度設定</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-secondary btn-lg" href="{{ url('mst/classrooms') }}">クラス設定</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-secondary btn-lg" href="{{ url('mst/children') }}">園児管理</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-secondary btn-lg" href="{{ url('mst/rosters') }}">クラス決め</a>
    </span>
</nav>

<h4>指導計画</h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-dark btn-lg" href="{{ url('mst/calendar') }}">年間予定管理</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-dark btn-lg" href="{{ url('mst/aims') }}">週のねらい</a>
    </span>
</nav>

<!--
<h4>バックアップ</h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <div class="nav-item nav-link">
        <a class="btn btn-secondary btn-lg" href="{{ url('db/export') }}">登録データのバックアップをダウンロード</a>
        <br>
        <span class="pt-3">ダウンロードしたデータを必ず外部ハードディスク等に保存してください。</span>
    </div>
</nav>
-->

<h4>マニュアル</h4>
<div style="margin-bottom:15px; margin-left:18px;">
    <ul class="">
        <li><a class="" href="/pdf/manual01.pdf" target="_blank">出欠管理について</a></li>
        <li><a class="" href="/pdf/manual02.pdf" target="_blank">年度更新の手順</a></li>
        <li><a class="" href="/pdf/manual03.pdf" target="_blank">週のねらい、年間予定、幼児園日誌の手順</a></li>
    </ul>
</div>

<?php /*
<h4>検証</h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
      <a class="" href="{{ url('test/bizdays') }}">年度の日付と週数表示</a>
    </span>
</nav>
*/?>

<div class="row alert alert-warning" role="alert" style="margin-top: 30px;">
    <div class="col-md-12">
        <h5 class="d-block">システムご利用の注意点</h5>
    </div>
    <ul>
        <li>時間は24時間表記となります。入力時も午後2時は14:00と入力してください。</li>
        <li>最後の操作から2時間経過すると自動的にログアウトします。</li>
    </ul>
</div>


@endsection()
