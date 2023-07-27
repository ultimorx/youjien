@component('components.edit-area')
@slot('title')
降園時間修正
@endslot
@slot('size')
modal-lg
@endslot

<input type="hidden" name="id" v-model="id">
<div class="form-group row">
    <label for="" class="col-4 col-form-label">降園時間</label>
    <div class="">
        <input type="time" class="form-control" name="outtime" v-model="outtime">
    </div>
</div>
<div class="form-group row">
    <label for="" class="col-4 col-form-label">お迎え者</label>
    <div class="">
        <input type="text" class="form-control" name="pick_up" v-model="pick_up" list="pick_up">
        <datalist id="pick_up">
            @foreach(config('const.pick_up') as $pick_up)
            <option value="{{ $pick_up }}"></option>
            @endforeach
        </datalist>
    </div>
</div>

@endcomponent