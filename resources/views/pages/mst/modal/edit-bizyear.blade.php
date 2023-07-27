@component('components.edit-area')
@slot('title')
年度詳細
@endslot
@slot('size')
none
@endslot
@php
use App\Models\Runstate;
@endphp
<div class="form-group row">
    <label for="" class="col-4 col-form-label required">年度</label>
    <div class="">
        <input type="number" maxlength="4" min="2020" max=2099 class="form-control" name="bizyear" v-model="bizyear">
    </div>
</div>

<div class="form-group row pb-1">
    <label for="" class="col-4 col-form-label required">状態</label>
@foreach(Runstate::list() as $run)
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="run" id="run_{{ $run['id'] }}" value="{{ $run['id'] }}" v-model="run">
        <label class="form-check-label" for="run_{{ $run['id'] }}">{{ $run['name'] }}</label>
    </div>
@endforeach
</div>

@endcomponent
