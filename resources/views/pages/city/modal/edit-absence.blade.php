@component('components.edit-delete-area')
@slot('title')
病欠理由
@endslot
@slot('size')
none
@endslot
@php
use App\Models\Disease;
$absence_types = Disease::ABSENCE_TYPES;
@endphp
<input type="hidden" name="id" v-model="id">

<div class="form-group row">
    <label for="" class="col-4 col-form-label required">種類</label>
    <div class="">
        <select id="select-absence_type" class="form-control" name="absence_type" v-model="absence_type">
            @foreach($absence_types as $absence_id => $absence_type_name)
            <option value="{{ $absence_id }}">
                 {{ $absence_type_name }}
            </option>
            @endforeach()
        </select>
    </div>
</div>


<div class="form-group row">
    <label for="" class="col-4 col-form-label required">名称</label>
    <div class="">
        <input type="text" class="form-control" name="name" v-model="name">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">表示順</label>
    <div class="">
        <input type="number" class="form-control" maxlength="4" min=0 max=1000 name="order" v-model="order">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">状態</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="active" id="active_1" value="1" v-model="active">
        <label class="form-check-label" for="active_1">使用</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="active" id="active_0" value="0" v-model="active">
        <label class="form-check-label" for="active_0">未使用</label>
    </div>
</div>

@endcomponent
