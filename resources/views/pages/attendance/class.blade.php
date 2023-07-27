@php
$disease_type_marks = config('const.disease_type_marks');
$inputTimeTooltip = config('const.inputTimeTooltip');
$a=2;
@endphp

@extends('layouts.default')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
@endsection

@section('scripts')
<script src="{{asset('js/attendance.js')}}?{{filemtime('js/attendance.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>クラス出席簿</h4>

<div class="row d-print-none">
    @include('components/classroom-select-form')
</div>

<div class="row d-none d-print-block">
    <h5 id="classroom_name"></h5>
    <span id="classroom_teacher" class="teacher mx-3"></span>
</div>

<div class="row mb-2" id="console">
    <div class="col-md-8 pl-0">
        @include('components/date-select')
    </div>
    <div class="col-md-4 text-right d-none" id="util-btns">
        <button class="btn btn-primary ml-auto d-print-none city_hidden" id="all-arrive">残りを出席にする</button>
        <button class="btn btn-info ml-auto d-print-none" onclick="print();">この画面を印刷</button>
    </div>
</div>

<div class="row d-print-none" id="dayoff" v-cloak>
    <h6><span id="classroom_age_type"></span>：@{{ dayoff }}</h6>
</div>

<div class="row d-none" id="children-list"><!--  v-bind:class="{ loading: is_loading }" -->

    <div class="col-md-12 d-print-none alert alert-warning small" role="alert">
        <span class="badge badge-secondary">早退</span>ボタンを押して時間を入力すると、早退時間が降園時間になります。
    </div>

    <table class="table table-striped" v-cloak>
        <colgroup>
            <col style="width:30px;">
            <col style="width:auto;">
            <col style="width:50px;">
            <col style="width:100px;">
            <col style="width:70px;">
            <col style="width:70px;">
            <col style="width:70px;">
            <col style="width:70px;">
            <col style="width:70px;">
            <col style="width:70px;">
            <col style="width:100px;">
            <col>
        </colgroup>
        <thead>
            <tr>
                <th>No.</th>
                <th>園児名</th>
                <th>性別</th>
                <th>出席</th>
                <th>遅刻</th>
                <th>早退</th>
                <th>バス</th>
                <th>早朝</th>
                <th>延長</th>
                <th>降園</th>
                <th>欠席</th>
            </tr>
        </thead>
        <tbody class="text-center">
            <tr v-for="(roster, index) in rosters" v-bind:class="" v-bind:data-child_id="roster.id" v-bind:key="index">
                <th scope="row"><!-- 出席番号 -->
                    @{{ roster.number }}
                </th>
                <td class="children_name"><!-- 園児名 -->
                    <small>@{{ roster.child.kana }}</small><br>@{{ roster.child.name }}
                    <small class="d-none"><br>
                        roster.id: @{{ roster.id }}<br>
                        attendance.id: @{{ roster.attendance.id }}<br>
                        <!-- at-at: @{{ roster.attendance.attendance }}<br>
                        at-date: @{{ roster.attendance.date }} -->
                    </small>
                </td>
                <td><!-- 性別 -->
                    @{{ roster.child.gender === 1 ? '男' : '女' }}
                </td>
                <td><!-- 出席 -->
                    <span v-if="roster.attendance.attendance === null">
                        <button class="btn btn-primary city_hidden" @click="arrive(roster.attendance)">出席</button>
                    </span>
                    <span v-else-if="roster.attendance.attendance === 0">
                        <button class="btn btn-outline-primary small city_hidden" @click="arrive(roster.attendance)">出席に変更</button>
                    </span>
                    <span v-else>
                        <strong>出席</strong>
                    </span>
                </td>
                <td><!-- 遅刻 -->
                    <span v-if="roster.attendance.attendance === 1">
                        <span v-if="roster.attendance.late === null">
                            <button class="btn btn-secondary city_hidden" @click="roster.attendance.late = ''">遅刻</button>
                        </span>
                        <span v-else-if="roster.attendance.late == ''">
                            <input type="time" min="09：00" class="form-control empty" style="width: 100%;" v-model="late" @blur="lateUpdate(roster.attendance)" v-on:keyup.enter="lateUpdate(roster.attendance)" data-toggle="tooltip" data-trigger="manual" data-placement="top" title="{{ $inputTimeTooltip }}">
                            <small class="time_rule"></small>
                            <button class="btn btn-light btn-sm city_hidden" @click="roster.attendance.late = null">取消</button>
                        </span>
                        <span v-else>
                            <time>@{{ roster.attendance.late }}</time>
                            <button class="btn btn-light btn-sm city_hidden" @click="lateUpdate(roster.attendance, 'cancel')">取消</button>
                        </span>
                    </span>
                    <span v-else class="disable"></span>
                </td>
                <td><!-- 早退 -->
                    <span v-if="roster.attendance.attendance === 1">
                        <span v-if="roster.attendance.early === null">
                            <button class="btn btn-secondary city_hidden" @click="roster.attendance.early = ''">早退</button>
                        </span>
                        <span v-else-if="roster.attendance.early == ''">
                            <input type="time" min="09：00" class="form-control empty" style="width: 100%;" v-model="early" @blur="earlyUpdate(roster.attendance)" v-on:keyup.enter="earlyUpdate(roster.attendance)" data-toggle="tooltip" data-trigger="manual" data-placement="top" title="{{ $inputTimeTooltip }}">
                            <small class="time_rule"></small>
                            <button class="btn btn-light btn-sm city_hidden" @click="roster.attendance.early = null">取消</button>
                        </span>
                        <span v-else>
                            <time>@{{ roster.attendance.early }}</time>
                            <button class="btn btn-light btn-sm city_hidden" @click="earlyUpdate(roster.attendance, 'cancel')">取消</button>
                        </span>
                    </span>
                    <span v-else class="disable"></span>
                </td>
                <td><!-- バス -->
                    <span v-if="roster.contract_depart_bus.length >= 1">
                        <span v-if="roster.attendance.attendance === 1">
                            <select @change=busUpdate(roster.attendance) v-model="roster.attendance.bus_id" class="form-control">
                                <option disabled>選択...</option>
                                <option value="0">
                                    利用しない
                                </option>
                                @foreach($buses as $bus)
                                <option value="{{ $bus->id }}" data-absence_type="{{ $bus->name }}">
                                {{ $bus->name }}
                                </option>
                                @endforeach()
                            </select>
                            <span><!-- v-if="roster.attendance.bus_id > 0" -->
                                <small class="text-truncate small">@{{ getBusName(roster.bus_id) }}</small>
                            </span>
                        </span>
                        <span v-else class="disable"></span>
                    </span>
                    <span v-else class="uncontracted"></span>
                </td>
                <td><!-- 早朝 -->
                    <span v-if="roster.contract_mornings.length >= 1">
                        <span v-if="roster.attendance.morning_using == 1" class="morning_used"></span>
                        <span v-else class="disable"></span>
                    </span>
                    <span v-else class="uncontracted"></span>
                </td>
                <td><!-- 延長 -->
                    <span v-if="roster.contract_evenings.length >= 1">
                        <span  v-if="roster.attendance.attendance === 1">
                            <select @change=eveningUpdate(roster.attendance) v-model="roster.attendance.evening_time_id" class="form-control">
                                <option disabled>選択...</option>
                                <option value="0">
                                    利用しない
                                </option>
                                <option v-if="roster.contract_evenings[0].evening_time_id" v-bind:value="roster.contract_evenings[0].evening_time_id">
                                    <!-- @{{ roster.contract_evenings[0].evening_time_id }},
                                    @{{ roster.attendance.evening_time_id }},  -->
                                    延長する
                                </option>
                            </select>
                            <span  v-if="roster.contract_evenings[0].evening_time_id == roster.attendance.evening_time_id">
                                <small class="text-truncate">@{{ getEveningTimeName(roster.contract_evenings[0].evening_time_id) }}</small>
                            </span>
                        </span>
                        <span v-else class="disable"></span>
                    </span>
                    <span v-else class="uncontracted"></span>
                </td>
                <td>
                    <time v-if="roster.attendance.outtime" class="small departed"></time><!-- 降園時間は非表示 @{{ roster.attendance.outtime }} -->
                    <span v-else class="disable"></span>
                </td>
                <td>
                    <span v-if="roster.attendance.attendance === null">
                        <button class="btn btn-dark city_hidden" @click="absence(roster.attendance)">欠席</button>
                    </span>
                    <span v-else-if="roster.attendance.attendance === 1">
                        <button class="btn btn-outline-dark small city_hidden" @click="absence(roster.attendance)">欠席に変更</button>
                    </span>
                    <span v-else>
                        <span v-if="roster.attendance.diseases_id == 0" class="alert-danger">
                            <small>欠席理由</small>
                            <select id="disease-list" class="form-control empty" @change=diseaseUpdate(roster.attendance) v-model="disease">
                                <option disabled>選択...</option>
                                @foreach($diseases as $disease)
                                <option value="{{ $disease->id }}" data-absence_type="{{ $disease->absence_type }}">
                                {{ $disease_type_marks[$disease->absence_type] ?? '' }} {{ $disease->id }} {{ $disease->name }}
                                <!-- {{ $disease->id }}, {{ $disease->absence_type }} ,  -->
                                </option>
                                @endforeach()
                            </select>
                        </span>
                        <span v-else>
                            <strong>欠席</strong>
                            <br>
                            <!-- .text-truncate 改行させない -->
                            <small class="text-truncate">@{{ getDiseaseName(roster.attendance.diseases_id ) }}</small>
                            <button class="btn btn-light btn-sm city_hidden" @click="cancel(roster.attendance)">取消</button>
                        </span>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- モーダル --}}
@include('pages.children.edit-area')

@endsection()
