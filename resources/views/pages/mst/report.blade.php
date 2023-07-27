@php
use App\Models\Bizyear;
use App\Models\Calendar;
use App\Models\Classroom;
use App\Util\Date;
@endphp
@extends('layouts.default-wide')

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
.table thead th {  position: sticky; top: 0; z-index:1; background-color: #343a40; }

#aim td:nth-child(1){ width: auto; }
#aim td:nth-child(2){ width: 400px; }
.aim_text { min-height: 70px; text-align: left; }

#list td:nth-child(1){ width: 60px; }
#list td:nth-child(2){ width: 15%; }
#list td:nth-child(3){ width: auto;}
#list td:nth-child(4){ width: 20%; min-width: 300px; }
#list td:nth-child(5){ width: 20%; min-width: 300px; }

#list td{ vertical-align: top; } /* td内のtextareaの高さを100%するには、height: 200px; を指定する */
textarea{ width: 100%; min-height: 200px; height: 300px; }  /* height: 100%; にすると日付が見えなくなる場合あり */
</style>

@endsection

@section('scripts')
<script>
@php
$param_date = (isset($_GET['date']) && Calendar::row($_GET['date']))? $_GET['date']: date('Y-m-d');
$bizyear = Date::bizyear($param_date);
$param_classroom_id = (isset($_GET['classroom_id']) && Classroom::row_bizyear($_GET['classroom_id'], $bizyear))? $_GET['classroom_id']: "''";
echo "var PARAM_DATE = '{$param_date}';",PHP_EOL;
echo "var PARAM_CLASSROOM_ID = {$param_classroom_id};",PHP_EOL;
@endphp
</script>
<script src="{{asset('js/mst/report.js')}}?{{filemtime('js/mst/report.js')}}"></script>
@endsection

@include('components/header')

@section('content')
<h4>幼児園日誌</h4>

<div class="row d-print-none alert _alert-warning small" role="note">
    <ul>
        <li>「今日の反省」「子どもの姿・保健・健康」の入力欄は<strong>自動保存</strong>されます。</li>
        <li>4月以降は年度が変わるため、3月末の週は4月が表示されません。</li>
        <li>クラスは年度ごとに登録されています。クラスの年度と日付の年度が一致しないと日付一覧は表示されません。<br>
            日付一覧が表示されない場合は以下をご確認ください。<br>
            1. <a href="/mst/bizyears">年度設定</a>で年度が稼働中になっているか。
            2.<a href="/mst/classrooms">クラス設定</a>で年度のクラスが登録されているか。
        </li>
    </ul>
</div>

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/classroom-select-form')
    </div>
</div>

<div class="row mb-2 d-print-block">
    <div class="col-md-8 pl-0">
        @include('components/week-select')
    </div>
    <div class="col-md-4 text-right">
        <a href="{{ url('mst/print/report') }}" org_href="{{ url('mst/print/report') }}" class="btn btn-info" id="a_btn_print_page">印刷画面</a>
    </div>
</div>

<div class="row" id="aim" v-cloak>
    <table class="table">
        <thead>
            <tr>
                <th class="left">週のねらい</th>
                <td class="left" rowspan="2">
                    ①健康な心と体　②自立心　③協調性<br>
                    ④道徳性・規範意識の芽生え<br>
                    ⑤社会生活との関わり　⑥思考力の芽生え<br>
                    ⑦自然との関わり・生命尊重<br>
                    ⑧数量・図形・文字等への関心・感覚<br>
                    ⑨言葉による伝え合い　⑩豊かな感性と表現
                </td>
            </tr>
            <tr>
                <td>
                    <div class="aim_text">
                        <!-- ・身の回りのことは自分で行う。<br>・身の回りの素材を工夫する -->
                        <p>@{{ aim1 }}</p>
                        <p>@{{ aim2 }}</p>
                    </div>
                </td>
            </tr>
        </thead>
    </table>
</div>

<div class="_row table-responsive scroll" id="list" v-cloak>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>園内行事</th>
                <th>〇子どもの活動及び・配慮事項</th>
                <th>今日の反省</th>
                <th>子どもの姿・保健・健康</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(line) in sheet" v-bind:class="{ active: isToday(line) }"><!-- @{{ line.bizweek }} @{{ line.bizyear }}-->
                <td class="text-center">
                    @{{ viewDate(line.date) }}
                    <p class="pt-1 sub small">@{{ viewWeek(line.date) }}</p>
                </td>
                <td>
                    <div class="textlist" v-if="in_events[line.date]">
                        <p v-for="(event, idx) in in_events[line.date]">
                            @{{ event.name }}
                        </p>
                    </div>
                </td>
                <td>
                    <div class="textlist" v-if="actions[line.date]">
                        <p v-for="(act, idx) in actions[line.date]" class="rec_name">@{{ act.action }}<small class="hide brk">@{{ event_name(act.event_id) }}</small></p>
                    </div>
                </td>
@if (\Login::is_city())
                <td class="pre_wrap">@{{ line.life }}</td>
                <td class="pre_wrap">@{{ line.health }}</td>
@else
                <td><textarea @blur="update(line)" v-model="inputs[line.id]['life']">@{{ line.life }}</textarea></td>
                <td><textarea @blur="update(line)" v-model="inputs[line.id]['health']">@{{ line.health }}</textarea></td>
@endif
            </tr>
        </tbody>
    </table>
</div>



@endsection()
