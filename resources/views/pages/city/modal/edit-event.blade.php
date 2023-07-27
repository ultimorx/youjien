@component('components.edit-delete-area')
@slot('title')
園外行事
@endslot
@slot('size')
none
@endslot
<input type="hidden" name="id" v-model="id">

<div class="form-group row">
    <label for="" class="col-4 col-form-label required">日付</label>
    <div class="">
        <input type="date" class="form-control" name="date" v-model="date">
    </div>
</div>

<div class="form-group row">
    <label for="" class="col-4 col-form-label required">園外行事</label>
    <div class="">
        <input type="text" class="form-control" name="name" v-model="name">
    </div>
</div>

@endcomponent
