@component('components.edit-delete-area')
@slot('title')
連絡
@endslot
@slot('size')
modal-lg
@endslot
@php
# use App\Models\CityKindergarten;
# $kindergartens = CityKindergarten::list();
@endphp
<input type="hidden" name="id" v-model="id">

<div class="form-group row">
    <label for="" class="col-2 col-form-label required">表示</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="display" id="display_1" value="1" v-model="display">
        <label class="form-check-label" for="display_1">表示</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="display" id="display_0" value="0" v-model="display">
        <label class="form-check-label" for="display_0">非表示</label>
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-2 col-form-label required">公開日</label>
    <div class="">
        <input type="date" class="form-control" name="public_date" v-model="public_date">
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-2 col-form-label required">件名</label>
    <div class="w-70">
        <input type="text" class="form-control" name="title" v-model="title">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-2 col-form-label required">内容</label>
    <div class="w-70">
        <textarea class="form-control" name="message" v-model="message" style="height: 100px;"></textarea>
    </div>
</div>

@endcomponent
