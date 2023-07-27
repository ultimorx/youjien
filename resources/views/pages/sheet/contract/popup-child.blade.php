@component('components.popup-area')

@slot('title')
園児契約詳細
@endslot

<h4>@{{ title }}　<small>@{{ subtitle }}</small></h4>

<div class="row mb-2 d-print-none">
    <div class="col-md-6 pl-0">
        @{{ date }}
    </div>
    <div class="col-md-6 text-right">
        <!-- <button class="btn btn-info ml-auto" onclick="print();">この画面を印刷</button> -->
        <button class="btn btn-success ml-auto" id="popup-download">エクセル出力</button>
    </div>
</div>

<div class="row" id="popup-list" v-cloak>
    <table class="table">
        <tbody>
            <tr v-for="(line, line_idx) in sheet">
                <td v-for="(cell, cell_idx) in line">@{{ cell }}</td>
            </tr>
        </tbody>
    </table>
</div>

@endcomponent
