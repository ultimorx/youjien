@extends('layouts.login')
@php
use App\Models\CityUser;
@endphp
@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script>
var LOGIN_NAME = '<?php echo CityUser::LOGIN_NAME?>';
var LOGIN_PASS = '<?php echo CityUser::LOGIN_PASS?>';
var LOGIN_COOKIE_EXPIRE = '<?php echo CityUser::LOGIN_COOKIE_EXPIRE?>';
</script>
<script src="{{asset('js/login.js')}}?{{filemtime('js/login.js')}}"></script>
@endsection

@include('components/header')

@section('content')

<h4>ログイン</h4>

<form accept-charset="utf-8" id="login-form">
    <div class="alert alert-danger" v-if="validation_error">
        <ul class="list-unstyled">
            <li v-for="message in validation_error">@{{message}}</li>
        </ul>
    </div>
    <div class="form-group row">
        <label for="" class="col-4 col-form-label required">ユーザー名</label>
        <div class="">
            <input type="text" class="form-control" name="name" v-model="name">
        </div>
    </div>
    <div class="form-group row">
        <label for="" class="col-4 col-form-label required">パスワード</label>
        <div class="">
            <input type="password" class="form-control" name="pass" v-model="pass">
        </div>
    </div>

    <div class="form-group row">
        <label for="" class="col-4 col-form-label"></label>
        <div class="">
            <button type="button" class="btn btn-primary btn-login">ログイン</button>
        </div>
    </div>
</form>



@endsection()
