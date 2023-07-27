@component('components.edit-delete-area')
@slot('title')
園児基本情報
@endslot
@slot('size')
none
@endslot
@php
use App\Models\Runstate;
@endphp

<input type="hidden" name="id" v-model="id">

<div class="form-group row">
    <label for="" class="col-4 col-form-label required">園児名</label>
    <div class="">
        <input type="text" class="form-control" name="name" v-model="name">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">園児名かな</label>
    <div class="">
        <input type="text" class="form-control" name="kana" v-model="kana">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">生年月日</label>
    <div class="">
        <input type="date" class="form-control _datepicker" name="birthday" v-model="birthday">
    </div>
    <div class="d-none pl-3">
        <small>半角ハイフン区切り（2018-04-02）または、<br>区切りなしで半角数字（20180402）で入力できます</small>
    </div>
</div>
<div class="form-group row pb-1">
    <label for="" class="col-4 col-form-label required">性別</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender_m" value="1" v-model="gender">
        <label class="form-check-label" for="gender_m">男</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="gender_f" value="2" v-model="gender">
        <label class="form-check-label" for="gender_f">女</label>
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-4 col-form-label">備考</label>
    <div class="w-50">
        <textarea class="form-control" name="remarks" v-model="remarks"></textarea>
    </div>
</div>
<div class="form-group row pt-2">
    <label for="" class="col-4 col-form-label">転入日</label>
    <div class="">
        <input type="date" class="form-control _datepicker" name="move_in_date" v-model="move_in_date">
    </div>
    <div class="d-none pl-3">
        <small>半角ハイフン区切り（2018-04-02）または、<br>区切りなしで半角数字（20180402）で入力できます</small>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">転出日</label>
    <div class="">
        <input type="date" class="form-control _datepicker" name="move_out_date" v-model="move_out_date">
    </div>
</div>

@endcomponent
