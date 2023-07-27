<span id="date-area" v-cloak>
    <button class="btn btn-light d-print-none" @click="changePrevDate()">＜前日</button>
    <h5 class="d-inline date">@{{ date }}</h5>
    <button class="btn btn-light d-print-none" @click="changeNextDate()">翌日＞</button>
</span>
<button id="date-select" type="button" class="btn btn-light datepicker d-print-none">カレンダーより選択</button>
