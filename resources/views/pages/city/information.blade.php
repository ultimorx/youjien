@extends('layouts.city')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/city/information.js')}}?{{filemtime('js/city/information.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>連絡</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <p>全園向けの連絡事項を登録します。</p>
    <p>公開日を過ぎたら各園のページに表示されます。公開日が未来の場合は、園には表示されません。</p>
    <p>表示項目で「非表示」を選択すると各園には表示されません。</p>
</div>

<div class="row d-print-none">
    <div class="col-md-12 pl-0">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" id="create">新規追加</button>
    </div>
</div>

<div class="row" id="list" v-cloak style="width:900px;">
    <table class="table">
        <colgroup>
            <col style="width:100px;">
            <col style="width:70px;">
            <col style="width:120px;">
            <col style="width:610px;">
        </colgroup>
        <thead>
            <th>編集</th>
            <th>表示</th>
            <th>公開日</th>
            <th>件名<br>内容</th>
        </thead>
        <tbody class="text-center">
            <tr v-for="(row) in list"　v-bind:class="{'lightgrey': isDisplay(row.display) }">
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                </td>
                <td>@{{ viewDisplay(row.display) }}</td>
                <td>@{{ row.public_date }}</td>
                <td class="text-left">
                    <strong>@{{ row.title }}</strong><br>
                    <div class="pre_wrap" v-html="row.pre_message"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.city.modal.edit-information')

@endsection()
