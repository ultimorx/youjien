@php
use App\Models\Bizyear;
use App\Models\Calendar;
use App\Models\Classroom;
use App\Util\Date;
@endphp
@extends('layouts.print')

@section('title')
幼児園日誌
@endsection

@section('styles')
<link href="{{asset('css/app.css')}}?{{filemtime('css/app.css')}}" rel="stylesheet" type="text/css">
<style>
.aim_text { min-height: 70px; text-align: left; }
#list .table td:nth-child(1){ width: 60px; }
#list .table td:nth-child(2){ width: 15%; }
#list .table td:nth-child(3){ width: auto; }
#list .table td:nth-child(4){ width: 25%; }
#list .table td:nth-child(5){ width: 25%; }
#list .table td{ vertical-align: top; }
.stampwrap{ width:200px; }
.stampbox {
    display: inline-block;
    width: 90px;
    height: 90px;
    border: 1px #000 solid;
    text-align: center;
}
.items2,.items3 { display:flex; justify-content: space-between; flex-wrap: wrap;}
.items2 .item { width: 49% !important; }
.items3 .item { width: 32% !important; }
hr { width: 100%; }
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
<script src="{{asset('js/mst/print/report.js')}}?{{filemtime('js/mst/print/report.js')}}"></script>
@endsection

@include('components/header')

@section('content')

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/classroom-select-form')
    </div>
</div>

<div class="row mb-2 d-print-none">
    <div class="col-md-8 pl-0">
        @include('components/week-select')
    </div>
    <div class="col-md-4 text-right">
        <button class="btn btn-info" onclick="print();">この画面を印刷</button>
        <a href="{{ url('mst/reports') }}" org_href="{{ url('mst/reports') }}" class="btn btn-outline-dark" id="a_btn_print_page">入力画面に戻る</a>
    </div>
    <hr>
</div>


<div id="list" v-cloak>

    <table class="meta mb-2" style="width: 100%;">
        <tr>
            <td class="items2">
                <h4 class="item">幼児園日誌</h4>
                <p class="item text-right mt-2">@{{ firstdate }}　〜　@{{ lastdate }}</p>
            </td>
            <td rowspan="2" class="stampwrap text-right">
                <span class="stampbox">園長印</span>
                <span class="stampbox">副園長印</span>
            </td>
        </tr>
        <tr>
            <td class="items3">
                <div class="item">
                    学年：@{{ gradename }}
                </div>
                <div class="item">
                    クラス：@{{ classname }}
                </div>
                <div class="item">
                    担任：@{{ tearchername }}
                </div>
            </td>
        </tr>
    </table>

    <table class="table">
        <tbody>
            <tr>
                <td colspan="3" class="text-center">週のねらい</td>
                <td colspan="2" rowspan="2">
①健康な心と体　②自立心　③協調性<br>
④道徳性・規範意識の芽生え<br>
⑤社会生活との関わり　⑥思考力の芽生え<br>
⑦自然との関わり・生命尊重<br>
⑧数量・図形・文字等への関心・感覚<br>
⑨言葉による伝え合い　⑩豊かな感性と表現
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="aim_text">
                        <p>@{{ aim1 }}</p>
                        <p>@{{ aim2 }}</p>
                    </div>
                </td>
            </tr>
            <tr class="text-center">
                <td></td>
                <td>園内行事</td>
                <td>〇子どもの活動及び・配慮事項</td>
                <td>今日の反省</td>
                <td>子どもの姿・保健・健康</td>
            </tr>
            <tr v-for="(line, line_idx) in sheet" _v-bind:class="{ active: isToday(line) }" v-bind:idx="line_idx">
                <td class="text-center">
                    @{{ viewDate(line.date) }}
                    <p class="pt-1 sub small">@{{ viewWeek(line.date) }}</p>
                </td>
                <td>
                    <div class="textlist" v-if="in_events[line.date]">
                        <p v-for="(event) in in_events[line.date]">
                            @{{ event.name }}
                        </p>
                    </div>
                </td>
                <td v-bind:rowspan="sheet.length" v-if="(line_idx == 0)">
                    <div class="textlist">
                        <p v-for="(act, k) in actions" v-bind:date="act.date" v-bind:action_id="act.id">
                            <span class="rec_name">@{{ act.action }}<small class="hide brk">@{{ event_name(act.event_id) }}</small>
                        </span>
                        </p>
                    </div>
                </td>
                <td class="pre_wrap">@{{ line.life }}</td>
                <td class="pre_wrap">@{{ line.health }}</td>
            </tr>
        </tbody>
    </table>
</div>



@endsection()
