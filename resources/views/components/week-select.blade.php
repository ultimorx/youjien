<span id="week-area" v-cloak>
    <strong id="year-txt" class="caption-year-info"><span class="bizyear">@{{ bizyear }}</span>　<span class="bizweek">@{{ bizweek }}</span></strong>
    <button class="btn btn-light d-print-none" @click="changePrevWeek()">＜前週</button>
    <h5 class="date caption-date-info center"> @{{ date }}〜</h5>
    <button class="btn btn-light d-print-none" @click="changeNextWeek()">翌週＞</button>
</span>
<button id="date-select" type="button" class="btn btn-light datepicker d-print-none">カレンダーより選択</button>
