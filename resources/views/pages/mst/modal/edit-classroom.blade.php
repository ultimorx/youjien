@component('components.edit-area')
@slot('title')
クラス詳細
@endslot
@slot('size')
none
@endslot
@php
use App\Models\Bizyear;
use App\Models\Grade;
$bizyears = Bizyear::ready_and_actives();
$grades = Grade::list();
@endphp
<input type="hidden" name="id" v-model="id">
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">年度</label>
    <div class="">
        <select id="select-bizyear" class="form-control" name="bizyear" v-model="bizyear">
            <!-- <option value="" selected>未選択</option> -->
            @foreach($bizyears as $bizyear)
            <option value="{{ $bizyear->bizyear }}">
                 {{ $bizyear->bizyear }}年度
            </option>
            @endforeach()
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">学年</label>
    <div class="">
        <select id="select-grade" class="form-control" name="grade_id" v-model="grade_id">
            <!-- <option value="" selected>未選択</option> -->
            @foreach($grades as $grade)
            <option value="{{ $grade->id }}">
                 {{ $grade->name }}
            </option>
            @endforeach()
        </select>
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">クラス名</label>
    <div class="">
        <input type="text" class="form-control" name="name" v-model="name">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">担任名</label>
    <div class="">
        <input type="text" class="form-control" name="teacher" v-model="teacher">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">表示順</label>
    <div class="">
        <input type="number" class="form-control" maxlength="4" min=1 max=1000 name="order" v-model="order">
    </div>
</div>
@endcomponent
