@php
use App\Models\CityKindergarten;

$kindergartens = CityKindergarten::list();
@endphp
@extends('layouts.city')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/top.js')}}?{{filemtime('js/top.js')}}"></script>
@endsection

@include('components/header')

@section('content')

<!-- Sess::get_view_kindergarten_id : {{ Sess::get_view_kindergarten_id() }}<br> -->

<h4>市役所メニュー</h4>
<nav class="nav nav-pills" style="margin-bottom:15px;">
    <span class="nav-item nav-link">
        <a class="btn btn-navy btn-lg" href="{{ url('city/information') }}">連絡</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-navy btn-lg" href="{{ url('city/absence') }}">病欠理由</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-navy btn-lg" href="{{ url('city/event') }}">園外行事</a>
    </span>
    <span class="nav-item nav-link">
        <a class="btn btn-navy btn-lg" href="{{ url('city/user') }}">ユーザー</a>
    </span>
    <!-- <span class="nav-item nav-link">
        <a class="btn btn-navy btn-lg" href="{{ url('city/') }}">集計?</a>
    </span> -->
    <span class="nav-item nav-link">
        <a class="btn btn-navy btn-lg" href="{{ url('city/accesslog') }}">ログイン履歴</a>
    </span>
</nav>

<h4>各園の状況確認</h4>
<ul class="ml-3">
    @foreach($kindergartens as $kindergarten)
    <li>
        <h5>
            <a class="text-navy" href="{{ url('/?id=').$kindergarten->id }}">{{ $kindergarten->name }}</a>
        </h5>
    </li>
    @endforeach()
</ul>

<div class="row alert alert-warning" role="alert" style="margin-top: 50px;">
    <div class="col-md-12">
        <h5 class="d-block">システムご利用の注意点</h5>
    </div>
    <ul>
        <li>最後の操作から2時間経過すると自動的にログアウトします。</li>
    </ul>
</div>
@endsection()
