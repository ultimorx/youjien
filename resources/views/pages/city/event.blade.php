@php
use App\Models\CityOutEvent;
use App\Util\Date;

$bizyears = CityOutEvent::bizyears();
$today = date('Y-m-d');
$current_bizyear = Date::bizyear($today);
@endphp

@extends('layouts.city')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/city/event.js')}}?{{filemtime('js/city/event.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>園外行事</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <p><strong>全園の共通園外行事</strong>を登録、各園の「年間予定管理」ページに表示されます。</p>
    <p>市が登録した共通園外行事は、市が変更・削除ができます。（園では変更・削除できません。）</p>
    <p><strong>個別園外行事</strong>は各園で「年間予定管理」ページで追加を行います。（個別園外行事は、市では変更・削除できません。）</p>
</div>

<div class="row d-print-non" style="width:600px;">
    <div class="col-md-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">新規追加</button>
    </div>
    <div class="col-md-8 pl-0 text-right">
        <div class="form-group">
            <label for="yearmonth-select-list" class="small">年度</label>
            <select id="year-select-list" class="form-control" name="year" style="width:auto; display:inline-block; margin-right:10px;">
                <option value="">すべて</option>
                @foreach($bizyears as $bizyear)
                <option value="{{ $bizyear->bizyear }}" {{ ($bizyear->bizyear == $current_bizyear)? "selected": "" }}>{{ $bizyear->bizyear }}年度</option>
                @endforeach()
            </select>
            <!-- <button type="button" class="btn-sm _btn-primary mini" id="page_reload">ページ再読込</button> -->
            <small id="page_reload" class="dotted cursor">ページ再読込</small>
        </div>
    </div>
</div>

<div class="row" id="list" v-cloak style="width:600px;">
    <table class="table">
        <colgroup>
            <col style="width:100px;">
            <col style="width:200px;">
            <col style="width:300px;">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>日付</th>
            <th>園外行事</th>
        </thead>
        <tbody class="text-center">
            <tr v-for="(row) in list">
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                </td>
                <td>@{{ row.date }}</td>
                <td class="text-left">@{{ row.name }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.city.modal.edit-event')

@endsection()
