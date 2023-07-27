@component('components.edit-area')
@slot('title')
園児詳細
@endslot
@slot('size')
modal-lg
@endslot



@php
$first_half = [4,5,6,7,8,9];
$second_half = [10,11,12,1,2,3];
$month_pairs = [
[4,5],
[6,7],
[8,9],
[10,11],
[12,1],
[2,3]
];
@endphp
<input type="hidden" name="id" v-model="id">
<input type="hidden" name="classroom_id" v-model="classroom_id">
<div class="form-group row">
    <label for="" class="col-4 col-form-label">学年</label>
    <div class="">
        <input type="text" class="form-control" name="grade" v-model="classroom.grade.name" disabled>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">クラス</label>
    <div class="">
        <input type="text" class="form-control" name="classroom" v-model="classroom.name" disabled>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label" v-bind:class="{ required: number_change }">出席番号</label>
    <div class="">
        <input type="text" class="form-control" name="number" v-model="number" v-bind:readonly="!number_change">
    </div>
    <div class="form-check form-check-inline pl-3 small" v-show="id != null">
        <label class="form-check-label">
            <input class="form-check-input" type="checkbox" name="number_change" v-model="number_change">出席番号を変更する
        </label>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">園児名</label>
    <div class="">
        <input type="text" class="form-control" name="name" v-model="child.name">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">園児名かな</label>
    <div class="">
        <input type="text" class="form-control" name="kana" v-model="child.kana">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">生年月日</label>
    <div class="">
        <input type="date" class="form-control _datepicker" name="birthday" v-model="child.birthday">
    </div>
    <div class="d-none pl-3">
        <small>半角ハイフン区切り（2018-04-02）または、<br>区切りなしで半角数字（20180402）で入力できます</small>
    </div>
</div>
<div class="form-group row pb-1">
    <label for="" class="col-4 col-form-label required">性別</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender_m" value="1" v-model="child.gender">
        <label class="form-check-label" for="gender_m">男</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender_f" value="2" v-model="child.gender">
        <label class="form-check-label" for="gender_f">女</label>
    </div>
</div>

@foreach([$first_half, $second_half] as $months)
<div class="form-group row">
    <label for="" class="col-4 col-form-label">{{ $loop->first ? '登園バス' : '' }}</label>
    @foreach($months as $month)
    <div class="form-check form-check-inline">
        <label class="form-check-label">
            <input class="form-check-input" type="checkbox" name="contract_arrive_bus[]" value="{{ $month }}" v-model="contract_arrive_bus">{{ $month }}月
        </label>
    </div>
    @endforeach
</div>
@if($loop->last)
<div class="form-group row pb-3">
    <label for="" class="col-4 col-form-label"></label>
    <div class="form-check form-check-inline">
        <label class="form-check-label text-primary pl-4 pt-1 custom-control">
            <input type="checkbox" id="arrive-copy" checked><small>登園バスの4月を変更するとすべての月にコピーを使用する</small>
        </label>
    </div>　
</div>
@endif
@endforeach

@foreach([$first_half, $second_half] as $months)
<div class="form-group row">
    <label for="" class="col-4 col-form-label">{{ $loop->first ? '降園バス' : '' }}</label>
    @foreach($months as $month)
    <div class="form-check form-check-inline">
        <label class="form-check-label">
            <input class="form-check-input contract_depart_bus" type="checkbox" name="contract_depart_bus[]" value="{{ $month }}" v-model="contract_depart_bus">{{ $month }}月
        </label>
    </div>
    @endforeach
</div>

@if($loop->last)
<div class="form-group row">
    <label for="" class="col-4 col-form-label"></label>
    <div class="form-check form-check-inline">
        <label class="form-check-label text-primary pl-4 pb-2 pt-1 custom-control">
            <input type="checkbox" id="depart-copy" checked><small>降園バスの4月を変更するとすべての月にコピーを使用する</small>
        </label>
    </div>　
</div>
@endif
@endforeach
<div class="form-group row pb-3">
    <label for="" class="col-4 col-form-label" v-bind:class="{ required: !select_bus_disable }">降園で使用するバス</label>
    <div class="">
        <select class="custom-select" name="bus" v-model="bus" :disabled="select_bus_disable">
            <option value="0">使用しない</option>
            @foreach($buses as $bus)
            <option value="{{ $bus->id }}">{{ $bus->name }}</option>
            @endforeach
        </select>
    </div>
</div>

@foreach($month_pairs as $pair)
<div class="form-group row">
    <label for="" class="col-4 col-form-label"> {{ $loop->first ? '早朝契約' : '' }}</label>
    <div class="d-flex">
        <label class="col-form-label text-nowrap">
            <span class="contract_month_label">{{ $pair[0] }}月</span>
            <select class="custom-select" name="contract_mornings[{{ $pair[0] }}]" v-model="contract_mornings[{{ $pair[0] }}]">
                <option value="">契約なし</option>
                @foreach($morning_times as $morning_time)
                <option value="{{ $morning_time->id }}">{{ $morning_time->time }}</option>
                @endforeach
            </select>
        </label>
        <label class="col-form-label text-nowrap ml-5">
            <span class="contract_month_label">{{ $pair[1] }}月</span>
            <select class="custom-select" name="contract_mornings[{{ $pair[1] }}]" v-model="contract_mornings[{{ $pair[1] }}]">
                <option value="">契約なし</option>
                @foreach($morning_times as $morning_time)
                <option value="{{ $morning_time->id }}">{{ $morning_time->time }}</option>
                @endforeach
            </select>
        </label>
    </div>
</div>
@if($loop->last)
<div class="form-group row">
    <label for="" class="col-4 col-form-label"></label>
    <div class="form-check form-check-inline">
        <label class="form-check-label text-primary pl-4 pb-3 pt-0 custom-control">
            <input type="checkbox" id="morning-copy" checked><small>早朝契約の4月を変更するとすべての月にコピーを使用する</small>
        </label>
    </div>　
</div>
@endif
@endforeach

@foreach($month_pairs as $pair)
<div class="form-group row">
    <label for="" class="col-4 col-form-label"> {{ $loop->first ? '延長契約' : '' }}</label>
    <div class="d-flex">
        <label class="col-form-label text-nowrap">
            <span class="contract_month_label">{{ $pair[0] }}月</span>
            <select class="custom-select" name="contract_evenings[{{ $pair[0] }}]" v-model="contract_evenings[{{ $pair[0] }}]">
                <option value="">契約なし</option>
                @foreach($evening_times as $evening_time)
                <option value="{{ $evening_time->id }}">{{ $evening_time->time }}</option>
                @endforeach
            </select>
        </label>
        <label class="col-form-label text-nowrap ml-5">
            <span class="contract_month_label">{{ $pair[1] }}月</span>
            <select class="custom-select" name="contract_evenings[{{ $pair[1] }}]" v-model="contract_evenings[{{ $pair[1] }}]">
                <option value="">契約なし</option>
                @foreach($evening_times as $evening_time)
                <option value="{{ $evening_time->id }}">{{ $evening_time->time }}</option>
                @endforeach
            </select>
        </label>
    </div>
</div>
@if($loop->last)
<div class="form-group row">
    <label for="" class="col-4 col-form-label"></label>
    <div class="form-check form-check-inline">
        <label class="form-check-label text-primary pl-4 pb-3 pt-1 custom-control">
            <input type="checkbox" id="evening-copy" checked><small>延長契約の4月を変更するとすべての月にコピーを使用する</small>
        </label>
    </div>　
</div>
@endif
@endforeach
<div class="form-group row">
    <label for="" class="col-4 col-form-label">備考</label>
    <div class="w-50">
        <textarea class="form-control" name="remarks" v-model="child.remarks"></textarea>
    </div>
</div>
<div class="form-group row pt-2">
    <label for="" class="col-4 col-form-label">転入日</label>
    <div class="">
        <input type="date" class="form-control _datepicker" name="move_in_date" v-model="child.move_in_date">
    </div>
    <div class="d-none pl-3">
        <small>半角ハイフン区切り（2018-04-02）または、<br>区切りなしで半角数字（20180402）で入力できます</small>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">転出日</label>
    <div class="">
        <input type="date" class="form-control _datepicker" name="move_out_date" v-model="child.move_out_date">
    </div>
</div>
@endcomponent
