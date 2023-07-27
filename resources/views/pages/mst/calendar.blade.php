@extends('layouts.default-wide')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
/* thead {  position: sticky; top: 0; z-index:1; background-color: #343a40; } */
.table thead th {  position: sticky; top: 0; z-index:1; background-color: #343a40; }
th { white-space: nowrap; }
td:nth-child(1){ position: sticky; left: 0; background-color: #eee; }
/*
td:nth-child(2){ position: sticky; }
td:nth-child(3){ position: sticky; }
td:nth-child(4){ position: sticky; left: 0;}
td:nth-child(5){ position: sticky; left: 0;}
td:nth-child(6){ position: sticky; left: 0;} */

.grade5hide td:nth-child(7), .grade5hide th:nth-child(7) { display: none; }
.grade4hide td:nth-child(8), .grade4hide th:nth-child(8) { display: none; }
.grade3hide td:nth-child(9), .grade3hide th:nth-child(9) { display: none; }
.grade2hide td:nth-child(10), .grade2hide th:nth-child(10) { display: none; }
.grade1hide td:nth-child(11), .grade1hide th:nth-child(11) { display: none; }
textarea{ width: 300px; height: 200px; }

.checkboxes input[type=checkbox] { transform: scale(1.5); margin: 0 0px 0 4px; }
#list select { min-width: 100px; }

.txt_close {
    cursor: pointer;
    font-size: 12px;
    line-height: 25px;
}

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
<script src="{{asset('js/mst/calendar.js')}}?{{filemtime('js/mst/calendar.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>年間予定管理</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <ul>
        <li>「園内行事」「子どもの活動及び・配慮事項」は、<a href="/mst/reports">幼児園日誌</a>に表示されます。</li>
        <li class="city_hidden">入力内容は自動保存されません。<button class="badge">追加</button>や<button class="badge">更新</button>ボタンを押すと保存されます。</li>
    </ul>
</div>

<div class="row mb-2 d-print-none">
    <div class="col-md-4 pl-0" _style="background-color: red;">
        @php
        const IS_COMPONENTS_PARAM_READY_AND_ACTIVE = true;
        @endphp
        @include('components/yearmonth-select')
    </div>
    <div class="col-md-5 pt-1 checkboxes" _style="background-color: blue;" id="check" v-cloak>
        表示:
        <span v-for="(grade) in grades" class="pr-2">
            <input v-model="checkeds[grade.id]" type="checkbox" v-bind:id="'grade'+grade.id" v-on:change="change(grade.id)">
            <label v-bind:for="'grade'+grade.id">@{{ grade.name }}</label>
        </span>
    </div>
    <div class="col-md-3 pt-0">
        <a href="{{ url('mst/print/calendar?bizyear') }}" class="btn btn-info ml-auto d-print-none">年間行事予定</a>
        <a href="{{ url('mst/print/calendar?bizmonth') }}" class="btn btn-info ml-auto d-print-none">月間行事予定</a>
    </div>
</div>

<div class="row mb-2 d-none d-print-block">
    <h5 id="yearmonth"></h5>
</div>

<div class="_row table-responsive scroll" id="list" v-cloak>
    <table class="table" id="listtable">
        <colgroup>
            <col style="width:50px;">
            <col style="width:60px;">
            <col style="width:60px;">
            <col style="width:200px;">
            <col style="width:150px;">
            <col style="width:auto;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
            <col style="width:100px;">
        </colgroup>

        <thead class="_d-print-none">
            <tr>
                <th></th>
                <th class="_small">未満児<br>休日</th>
                <th class="_small">以上児<br>休日</th>
                <th>園内行事</th>
                <th>園外行事</th>
                <th>備考</th>
                <th v-for="(grade) in grades"><small>子どもの活動及び・配慮事項</small><br>@{{ grade.name }}</th>
            </tr>
        </thead>

        <!-- <thead class="_d-none d-print-block">
            <tr>
                <th rowspan="2"></th>
                <th rowspan="2" class="_small">未満児<br>休日</th>
                <th rowspan="2" class="_small">以上児<br>休日</th>
                <th rowspan="2">園内行事</th>
                <th rowspan="2">園外行事</th>
                <th rowspan="2">備考</th>
                <th colspan="5">子どもの活動及び・配慮事項</th>
            </tr>
            <tr>
                <th v-for="(grade) in grades">@{{ grade.name }}</th>
            </tr>
        </thead> -->

        <tbody>
            <tr v-for="(line, sheet_idx) in sheet" v-bind:class="{ 'week_sat': isSat(line.week_idx), 'week_san': isSan(line.week_idx)}" class="vtop">
                <td class="text-center">@{{ viewDate(line.date) }}<p class="pt-1 sub small">@{{ line.week }}</p></td>
                <td class="text-center"  v-bind:class="{ 'dayoff': isDayoff(line.mimanji) }">
                    <span  v-if="isDayoff(line.mimanji)">
                        <strong class="ml-1 mr-1">休</strong>
                        <p class="pt-1"><button class="btn-sm btn-dark city_hidden" @click="setDayon(line,type_mimanji)">取消</button></p>
                    </span>
                    <button v-else class="btn-sm btn-outline-dark city_hidden" @click="setDayoff(line,type_mimanji)">休園</button>
                </td>
                <td class="text-center" v-bind:class="{ 'dayoff': isDayoff(line.ijyouji) }">
                    <span v-if="isDayoff(line.ijyouji)">
                        <strong class="ml-1 mr-1">休</strong>
                        <p class="pt-1"><button class="btn-sm btn-dark city_hidden" @click="setDayon(line,type_ijuyouji)">取消</button></p>
                    </span>
                    <button v-else class="btn-sm btn-outline-dark city_hidden" @click="setDayoff(line,type_ijuyouji)">休園</button>
                </td>
                <td>
                    <div class="textlist" v-if="in_events[line.date]">
@if (\Login::is_city())
                        <p v-for="(event, idx) in in_events[line.date]">
                            <span class="rec_name">@{{ event.name }}</span>
                        </p>
@else
                        <p v-for="(event, idx) in in_events[line.date]" v-bind:id="n_event_name_id(event.id)" v-bind:class="n_event_rec(line.date, event_type_in)">
                            <a class="rec_name" @click="show_event_edit(event.id, line.date, event_type_in)">@{{ event.name }}</a>
                            <span class="rec_input">
                                <input v-model="input_in_events[line.date]">
                                <input v-model="input_in_event_dates[line.date]" type="date" class="small"><br>
                                <button class="btn-sm btn-outline-dark city_hidden" @click="update_event(line.date, event.id, event_type_in)">更新</button>
                                <button class="btn-sm btn-outline-dark city_hidden" @click="remove_event(line.date, event.id, event_type_in)">削除</button>
                                <span class="btn-light float-right txt_close" @click="close_event_edit(line.date, event_type_in)">入力欄を閉じる</span>
                            </span>
                        </p>
@endif
                    </div>
                    <div v-bind:id="n_ev_btn(line.date, event_type_in)">
                        <button class="btn-sm btn-outline-dark city_hidden" @click="show_event_add(line.date, event_type_in)">園内行事追加</button>
                    </div>
                    <div v-bind:id="n_ev_input(line.date, event_type_in)" class="hide">
                        <input v-model="input_in_events[line.date]" placeholder="追加する行事名を入力" class="mt-2"><!-- @blur="save_event(line.date)" -->
                        <button class="btn-sm btn-outline-dark city_hidden" @click="add_event_in(line.date)">追加</button>
                        <span class="btn-light float-right txt_close" @click="close_input_event(line.date, event_type_in)">入力欄を閉じる</span>
                    </div>
                </td>
                <td>
                    <div class="textlist" v-if="city_out_events[line.date]">
                        <p v-for="(event, idx) in city_out_events[line.date]">
                            <span class="rec_name city_text">@{{ event.name }}</span>
                        </p>
                    </div>

                    <div class="textlist" v-if="out_events[line.date]">
@if (\Login::is_city())
                        <p v-for="(event, idx) in out_events[line.date]">
                            <span class="rec_name">@{{ event.name }}</span>
                        </p>
@else
                        <p v-for="(event, idx) in out_events[line.date]" v-bind:id="n_event_name_id(event.id)" v-bind:class="n_event_rec(line.date, event_type_out)">
                            <a class="rec_name" @click="show_event_edit(event.id, line.date, event_type_out)">@{{ event.name }}</a>
                            <span class="rec_input">
                                <input v-model="input_out_events[line.date]">
                                <input v-model="input_out_event_dates[line.date]" type="date" class="small"><br>
                                <button class="btn-sm btn-outline-dark city_hidden" @click="update_event(line.date, event.id, event_type_out)">更新</button>
                                <button class="btn-sm btn-outline-dark city_hidden" @click="remove_event(line.date, event.id, event_type_out)">削除</button>
                                <span class="btn-light float-right txt_close" @click="close_event_edit(line.date, event_type_out)">入力欄を閉じる</span>
                            </span>
                        </p>
@endif
                    </div>
                    <div v-bind:id="n_ev_btn(line.date, event_type_out)">
                        <button class="btn-sm btn-outline-dark city_hidden" @click="show_event_add(line.date, event_type_out)">園外行事追加</button>
                    </div>
                    <div v-bind:id="n_ev_input(line.date, event_type_out)" class="hide">
                        <input v-model="input_out_events[line.date]" placeholder="追加する行事名を入力" class="mt-2"><!-- @blur="save_event(line.date)" -->
                        <button class="btn-sm btn-outline-dark city_hidden" @click="add_event_out(line.date)">追加</button>
                        <span class="btn-light float-right txt_close" @click="close_input_event(line.date, event_type_out)">入力欄を閉じる</span>
                    </div>
                </td>
                <td>
                    <div v-bind:id="n_nt_btn(line.id)" class="d-print-none">
                        <p class="pre_wrap">@{{ line.note }}</p>
                        <button class="btn-sm btn-outline-dark city_hidden" @click="show_input_note(line.id)">備考編集</button>
                    </div>
                    <div v-bind:id="n_nt_input(line.id)" class="hide d-print-none">
                        <textarea v-model="input_notes[line.id]" placeholder="備考を入力"></textarea>
                        <button class="btn-sm btn-outline-dark city_hidden" @click="save_note(line, sheet_idx)">保存</button>
                        <span class="btn-light float-right txt_close" @click="hide_input_note(line.id)">入力欄を閉じる</span>
                    </div>
                </td>
                <td v-for="(grade) in grades">
                    <div class="textlist" v-if="actions[action_key(line.date, grade.id)]">
@if (\Login::is_city())
                        <p v-for="(act, idx) in actions[action_key(line.date, grade.id)]">
                            <span class="rec_name">@{{ act.action }}<small class="brk">@{{ event_name(act.event_id) }}</small></span>
                        </p>
@else
                        <p v-for="(act, idx) in actions[action_key(line.date, grade.id)]" v-bind:id="n_action_name_id(act.id)" v-bind:class="n_action_rec(line.date, grade.id)">
                            <a class="rec_name" @click="show_action_edit(act.id, line.date, grade.id)">@{{ act.action }}<small class="brk">@{{ event_name(act.event_id) }}</small></a>
                            <span class="rec_input">
                                <textarea v-model="input_actions[action_key(line.date, grade.id)]"></textarea>
                                <br>
                                <select v-model="select_action_event_ids[action_key(line.date, grade.id)]" v-if="in_events[line.date]">
                                    <option value="">イベントなし</option>
                                    <option v-for="(event, idx) in in_events[line.date]" v-bind:value="event.id">
                                        @{{ event.name }}
                                    </option>
                                </select>
                                <button class="btn-sm btn-outline-dark city_hidden" @click="update_action(line.date, grade.id, act.id)">更新</button>
                                <button class="btn-sm btn-outline-dark city_hidden" @click="remove_action(line.date, grade.id, act.id)">削除</button>
                                <span class="btn-light float-right txt_close" @click="close_action_edit(line.date, grade.id)">入力欄を閉じる</span>
                            </span>
                        </p>
@endif
                    </div>
                    <div v-bind:id="n_act_btn(line.date, grade.id)">
                        <button class="btn-sm btn-outline-dark city_hidden" @click="show_action_add(line.date, grade.id)">活動及び配慮事項追加</button>
                    </div>
                    <div v-bind:id="n_act_input(line.date, grade.id)" class="hide">
                        <textarea v-model="input_actions[action_key(line.date, grade.id)]" placeholder="追加する活動及び配慮事項を入力"></textarea>
                        <br>
                        <select v-model="select_action_event_ids[action_key(line.date, grade.id)]" v-if="in_events[line.date]">
                            <option value="">イベントなし</option>
                            <option v-for="(event, idx) in in_events[line.date]" v-bind:value="event.id">
                                @{{ event.name }}
                            </option>
                        </select>
                        <button class="btn-sm btn-outline-dark" @click="add_action(line.date, grade.id)">追加</button>
                        <span class="btn-light float-right txt_close" @click="close_input_action(line.date, grade.id)">入力欄を閉じる</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection()
