@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/children.js')}}?{{filemtime('js/children.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>園児一覧</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <ul>
        <li>園児の契約内容を登録します。</li>
        <li>年度途中でのクラス替えは、<a href="/mst/rosters">クラス決め</a>ページで行います。</li>
    </ul>
</div>

<div class="row d-print-none">
    <div class="col-md-8 pl-0">
        @php
        const IS_COMPONENTS_PARAM_READY_AND_ACTIVE = true;
        @endphp
        @include('components/classroom-select-form')
    </div>
    <div class="col-md-4 text-right">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">入園する園児を追加</button>
        <button class="btn btn-success ml-auto" id="csv-download">エクセル出力</button>
    </div>
</div>

<span id="date-area"></span>

<div class="row">
    <table class="table" id="children-list" v-cloak>
        <thead>
            <tr>
                <th rowspan="2">編集</th>
<!--
                <th rowspan="2">学年</th>
                <th rowspan="2">クラス</th> -->

                <th rowspan="2">No.</th>
                <th rowspan="2">園児名</th>
                <th rowspan="2">生年月日</th>
                <th rowspan="2">性別</th>
                <th colspan="2">バス</th>
                <th colspan="2">時間外保育</th>
                <th rowspan="2">備考</th>
                <th rowspan="2">転入日</th>
                <th rowspan="2">転出日</th>
            </tr>
            <tr>
                <th>登園</th>
                <th>降園</th>
                <th>早朝</th>
                <th>延長</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr v-for="(roster, index) in rosters" v-bind:data-child_id="roster.id" v-bind:key="roster.id" v-bind:class="{ 'table-secondary': roster.child.move_out_date }"><!-- table-dark, table-secondary -->
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" @click="edit(roster)">編集</button>
                </td>
<!--
                <td>@{{ roster.classroom.grade.name }}</td>
                <td>@{{ roster.classroom.name }}</td>
-->
                <td>@{{ roster.number }}</td>
                <td class="text-left">
                    <div class="small">@{{ roster.child.kana }}</div>
                    <div>@{{ roster.child.name }}</div>
                </td>
                <td>@{{ roster.child.birthday }}</td>
                <td>@{{ roster.child.gender === 1 ? '男' : '女' }}</td>
                <td>@{{ roster.contract_arrive_bus.length ? '○' : '-' }}</td>
                <td>@{{ roster.contract_depart_bus.length ? '○' : '-' }}</td>
                <td>@{{ roster.contract_mornings.length ? '○' : '-' }}</td>
                <td>@{{ roster.contract_evenings.length ? '○' : '-' }}</td>
                <td class="text-left text-truncate position-relative" style="max-width: 100px;">
                    <span class="stretched-link" v-bind:title="roster.child.remarks">@{{ roster.child.remarks }}</span>
                </td>
                <td class="small">@{{ roster.child.move_in_date }}</td>
                <td class="small">@{{ roster.child.move_out_date }}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.children.edit-area')

@endsection()
