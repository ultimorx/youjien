@component('components.edit-delete-area')
@slot('title')
ユーザー情報
@endslot
@slot('size')
none
@endslot
@php
use App\Models\CityKindergarten;
$kindergartens = CityKindergarten::list();
@endphp
<input type="hidden" name="id" v-model="id">
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">所属</label>
    <div class="">
        <select id="select-kindergarten" class="form-control" name="kindergarten_id" v-model="kindergarten_id">
            <option value="0">{{ CityKindergarten::KINDERGARTEN_ID_ZERO_NAME }}</option>
            @foreach($kindergartens as $kindergarten)
            <option value="{{ $kindergarten->id }}">
                 {{ $kindergarten->name }}
            </option>
            @endforeach()
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">ユーザー名</label>
    <div class="">
        <input type="text" class="form-control" name="name" v-model="name">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">パスワード</label>
    <div class="">
        <input type="text" class="form-control" name="pass" v-model="pass">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">表示順</label>
    <div class="">
        <input type="number" class="form-control" maxlength="4" min=0 max=1000 name="order" v-model="order">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">ログイン</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="active" id="active_1" value="1" v-model="active">
        <label class="form-check-label" for="active_1">有効</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="active" id="active_0" value="0" v-model="active">
        <label class="form-check-label" for="active_0">無効</label>
    </div>
</div>
@endcomponent
