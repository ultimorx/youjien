 'use strict';

require('../../common');

$(document).ready(() => {
    let url_list = URL_LIST;
    const EVENT_TYPE_IN = 10;
    const EVENT_TYPE_OUT = 20;

    const date_area = require('../../components/date-area')();
    const dateFormat = require('../../dateformat');

    const list = new Vue({
        el: '#list',
        data: {
            sheet: [],
            type_ijuyouji: 'ijyouji',
            type_mimanji: 'mimanji',
            event_type_in: EVENT_TYPE_IN,
            event_type_out: EVENT_TYPE_OUT,
            in_events: [],
            out_events: [],
            city_out_events: [],
            bizmonth: '',
        },
        methods: {
            fetch() {
                this.bizmonth = $('#month-select-list option:selected').val();
                this.fetch_event();
                this.fetch_city_event();
            },
            fetch_event() {
                $.ajax({
                    url: '/api/mst/event/monthlist',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list option:selected').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_events(res);
                    this.fetch_sheet();
                    console.log('fetch_event()');
                });
            },
            fetch_sheet() {
                $.ajax({
                    url: url_list,
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list option:selected').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.sheet = res;
                    console.log('fetch_sheet()');
                });
            },
            fetch_city_event() {
                $.ajax({
                    url: '/api/city/event/monthlist',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list option:selected').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_city_out_events(res);
                    console.log('fetch_city_event()', res);
                });
            },
            viewDate(d){
                return dateFormat(new Date(d), 'M月D日');
            },
            viewEvents(events){
                if( ! events ) return;
                // console.log(events);
                let txt = '';
                let br = '\r\n';
                // let br = "<br>";
                let n = 0;
                for(let k in events) {
                    if(n >= 1) txt += br;
                    n++;
                    txt += events[k].name;
                }
                return txt + br;
            },
            isSat(week_idx) {
                return (week_idx == 6);
            },
            isSan(week_idx) {
                return (week_idx == 0);
            },
            set_city_out_events(events) {
                for(let k in events) {
                    let date = events[k].date;
                    let event_id = events[k].id;
                    let event_name = events[k].name;

                    if( ! this.city_out_events[date] ) this.$set(this.city_out_events, date, {});
                    this.$set(this.city_out_events[date], event_id, { id: event_id, name: event_name});
                }
            },
            set_events(events) {
                for(let k in events) {
                    let date = events[k].date;
                    let type = events[k].type;
                    if(type == EVENT_TYPE_IN) {
                        this.set_in_event(date, events[k].id, events[k].name);
                    } else if (type == EVENT_TYPE_OUT) {
                        this.set_out_event(date, events[k].id, events[k].name);
                    }
                }
            },
            set_in_event(date, event_id, event_name) {
                if( ! this.in_events[date] ) this.$set(this.in_events, date, {});
                this.$set(this.in_events[date], event_id, { id: event_id, name: event_name});
            },
            set_out_event(date, event_id, event_name) {
                if( ! this.out_events[date] ) this.$set(this.out_events, date, {});
                this.$set(this.out_events[date], event_id, { id: event_id, name: event_name});
            },
        }
    });

    const viewYearMonth = () => {
        let year = $('#year-select-list option:selected').text();
        let month = $('#month-select-list option:selected').text();
        $('#yearmonth').text(year + month);
    }

    const init = () => {
        viewYearMonth();
        list.fetch();
    }

    // 年、月の選択時
    $(document).on('change', '#year-select-list, #month-select-list', () => {
        viewYearMonth();
        list.fetch();
    });
    // 再読込
    $(document).on('click', '#yearmonth-search', () => {
        viewYearMonth();
        list.fetch();
    });


    init();
});
