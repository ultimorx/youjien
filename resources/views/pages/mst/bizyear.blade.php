@php
use App\Models\Bizyear;
@endphp
@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style media="print">
td:nth-child(1)
{
    display: none;
}
</style>

@endsection

@section('scripts')
<script>
var URL_LIST = '/api/mst/bizyear';
</script>
<script src="{{asset('js/mst/bizyear.js')}}?{{filemtime('js/mst/bizyear.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>年度設定</h4>

<div class="row d-print-none">
    <div class="col-md-12 pl-0">
        <button type="button" class="btn btn-primary city_hidden" data-toggle="modal" data-target="#edit-area" id="create">新年度の追加</button>
        <!-- <button class="btn btn-success ml-auto" id="csv-download">エクセル出力</button> -->
    </div>
</div>

<div class="row" id="list" v-cloak style="width:300px;">
    <table class="table">
        <colgroup>
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col>
        </colgroup>
        <thead>
            <th>編集</th>
            <th>年度</th>
            <th>状態</th>
        </thead>
        <tbody>
            <tr class="text-center" v-for="(row) in list">
                <td>
                    <button type="button" class="btn btn-primary city_hidden" data-toggle="modal" data-target="#edit-area" @click="edit(row)">編集</button>
                </td>
                <td>@{{ row.bizyear }}</td>
                <td class="bg-warning" v-if="row.run==2">@{{ row.runstate.name }}</td>
                <td class="bg-secondary text-white" v-else-if="row.run==3">@{{ row.runstate.name }}</td>
                <td v-else>@{{ row.runstate.name }}</td>
            </tr>
        </tbody>
    </table>

</div>

<div class="row mt-2" role="note">
    <strong>年度切替時の運用について</strong>
</div>
<div class="row alert mb-3" role="note">
    <ul>
        <li>
            新年度に向けてクラス登録やクラス決めを行う場合は、年度の状態を<strong>準備中</strong>にします。
        </li>
        <li>
            前年度と新年度でシステムを利用する場合は、各年度の状態を<strong>稼働中</strong>にします。
        </li>
        <li>
            前年度のシステムを利用しない場合は、年度の状態を<strong>終了</strong>にします。
        </li>
    </ul>
</div>


<div class="row mt-2" role="note">
    <strong>データコピーについて</strong>
</div>
<div class="row alert mb-3" role="note">
    <ul>
        <li>
            新年度の追加時に、前年度のデータコピーを行います。
        </li>
        <li>
            <strong>週と曜日</strong>を基準にしてコピーされます。
        </li>
        <li>
            コピーされるデータは次の通りです。<br>
            ・休日設定<br>
            ・園内行事、園外行事<br>
            ・子どもの活動及び・配慮事項<br>
            ・週のねらい
        </li>
    </ul>
</div>

{{-- モーダル --}}
@include('pages.mst.modal.edit-bizyear')

@endsection()
