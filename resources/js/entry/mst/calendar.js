'use strict';

require('../../common');

$(document).ready(() => {
    const EVENT_TYPE_IN = 10;
    const EVENT_TYPE_OUT = 20;

    const date_area = require('../../components/date-area')();
    const dateFormat = require('../../dateformat');

    const check = new Vue({
        el: '#check',
        data: {
            grades: [],
            checkeds: [],
        },
        methods: {
            set(grades) {
                this.grades = grades;
                for(let k in grades) {
                    this.checkeds[grades[k].id] = true;
                }
            },
            change(grade_id) {
                let cls = 'grade' + grade_id + 'hide'; // css名 .grade1hide .grade2hide
                let $table = $('#listtable');
                if( this.checkeds[grade_id] ) {
                    $table.removeClass(cls);
                } else {
                    $table.addClass(cls);
                }
            },
        }
    });

    const list = new Vue({
        el: '#list',
        data: {
            sheet: null,
            type_ijuyouji: 'ijyouji',
            type_mimanji: 'mimanji',
            dayoff_true: 1,
            dayoff_false: 0,
            input_notes: [],
            event_type_in: EVENT_TYPE_IN,
            event_type_out: EVENT_TYPE_OUT,
            in_events: [],
            out_events: [],
            city_out_events: [],
            event_names: [],
            input_in_events: [],
            input_out_events: [],
            input_in_event_dates: [],
            input_out_event_dates: [],
            grades: [],
            actions: [],
            input_actions: [],
            select_action_event_ids: [],
        },
        methods: {
            fetch() {
                this.fetch_event();
                this.fetch_city_event();
            },
            fetch_event() {
                $.ajax({
                    url: '/api/mst/event/monthlist',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_events(res);
                    this.fetch_actions();
                    console.log('fetch_event()');
                });
            },
            fetch_actions() {
                $.ajax({
                    url: '/api/mst/action/monthlist',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_actions(res);
                    console.log('fetch_actions()');
                    this.fetch_sheet();
                });
            },
            fetch_sheet() {
                $.ajax({
                    url: '/api/mst/calendar',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.sheet = res;
                    this.set_calendars(res);
                    console.log('fetch_sheet()');
                });
            },
            fetch_grades() {
                $.ajax({
                    url: '/api/grades/desc',
                    data: {},
                    type: 'get'
                }).done((res) => {
                    this.grades = res;
                    check.set(res);
                });
            },
            fetch_city_event() {
                $.ajax({
                    url: '/api/city/event/monthlist',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                        'month': $('#month-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_city_out_events(res);
                    console.log('fetch_city_event()', res);
                });
            },
            viewDate(d){
                return dateFormat(new Date(d), 'M/D');
            },
            isSat(week_idx) {
                return (week_idx == 6);
            },
            isSan(week_idx) {
                return (week_idx == 0);
            },
            isDayoff(off) {
                return (off == this.dayoff_true);
            },
            setDayoff(calendar, type) {
                if(type == this.type_ijuyouji) {
                    calendar.ijyouji = this.dayoff_true;
                }
                if(type == this.type_mimanji) {
                    calendar.mimanji = this.dayoff_true;
                }
                this.update_dayoff(calendar);
            },
            setDayon(calendar, type) {
                if(type == this.type_ijuyouji) {
                    calendar.ijyouji = this.dayoff_false;
                }
                if(type == this.type_mimanji) {
                    calendar.mimanji = this.dayoff_false;
                }
                this.update_dayoff(calendar);
            },
            update_dayoff(calendar) {
                $.ajax({
                    url: '/api/mst/calendar/dayoff/' + calendar.id,
                    data: calendar,
                    type: 'put'
                }).done((res) => {
                    // console.log(res);
                });
            },
            show_input_note(key) {
                $('#' + this.n_nt_btn(key)).addClass('hide');
                $('#' + this.n_nt_input(key)).removeClass('hide');
            },
            hide_input_note(key) {
                $('#' + this.n_nt_btn(key)).removeClass('hide');
                $('#' + this.n_nt_input(key)).addClass('hide');
            },
            n_nt_btn(key) {
                return 'ntb_' + key;
            },
            n_nt_input(key) {
                return 'nti_' + key;
            },
            save_note(calendar,sheet_idx) {
                $.ajax({
                    url: '/api/mst/calendar/save_note/' + calendar.id,
                    data: {'note' : this.input_notes[calendar.id]},
                    type: 'put'
                }).done((res) => {
                    this.sheet[sheet_idx].note = this.input_notes[calendar.id];
                    this.hide_input_note(calendar.id);
                    // console.log(res);
                });
            },
            set_calendars(calendars){
                for(let k in calendars) {
                    let key = calendars[k].id;
                    this.input_notes[key] = calendars[k].note;
                }
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
                    this.set_event_name(events[k].id, events[k].name);
                }
            },
            set_in_event(date, event_id, event_name) {
                if( ! this.in_events[date] ) this.$set(this.in_events, date, {});
                this.$set(this.in_events[date], event_id, { id: event_id, name: event_name});
            },
            del_in_event(date, event_id) {
                this.$delete(this.in_events[date], event_id);
            },
            set_out_event(date, event_id, event_name) {
                if( ! this.out_events[date] ) this.$set(this.out_events, date, {});
                this.$set(this.out_events[date], event_id, { id: event_id, name: event_name});
            },
            del_out_event(date, event_id) {
                this.$delete(this.out_events[date], event_id);
            },
            set_event_name(event_id, event_name) {
                this.$set(this.event_names, event_id, event_name);
            },
            event_name(event_id) {
                return (this.event_names[event_id]) ? this.event_names[event_id] : '';
            },
            show_event_edit(event_id, date, type) {
                if(type == EVENT_TYPE_IN) {
                    this.$set(this.input_in_events, date, this.in_events[date][event_id].name);
                    this.$set(this.input_in_event_dates, date, date);
                }
                else if(type == EVENT_TYPE_OUT) {
                    this.$set(this.input_out_events, date, this.out_events[date][event_id].name);
                    this.$set(this.input_out_event_dates, date, date);
                }
                this.hide_event_edit(date, type);
                $('#' + this.n_event_name_id(event_id)).addClass('active');
                this.hide_event_add(date, type);
                $('#' + this.n_ev_btn(date, type)).addClass('hide');
            },
            hide_event_edit(date, type) {
                $('.' + this.n_event_rec(date, type)).removeClass('active');
            },
            close_event_edit(date, type) {
                this.hide_event_edit(date, type);
                this.hide_event_add(date, type);
            },
            show_event_add(date, type) {
                if(type == EVENT_TYPE_IN) {
                    this.$set(this.input_in_events, date, '');
                }
                else if(type == EVENT_TYPE_OUT) {
                    this.$set(this.input_out_events, date, '');
                }
                this.hide_event_edit(date, type);
                $('#' + this.n_ev_btn(date, type)).addClass('hide');
                $('#' + this.n_ev_input(date, type)).removeClass('hide');
            },
            hide_event_add(date, type) {
                $('#' + this.n_ev_btn(date, type)).removeClass('hide');
                $('#' + this.n_ev_input(date, type)).addClass('hide');
            },
            close_input_event(date, type) {
                this.hide_event_add(date, type);
            },
            n_event_rec(date, type) {
                return 'ev_rec_' + date + '_' + type;
            },
            n_event_name_id(event_id) {
                return 'evn_' + event_id;
            },
            n_ev_btn(date, type) {
                return 'evb_' + date + '_' + type;
            },
            n_ev_input(date, type) {
                return 'evi_' + date + '_' + type;
            },
            add_event_in(date) {
                if( ! this.input_in_events[date] ) {
                    this.alert_empty_event();
                    return;
                }
                let event_name = this.input_in_events[date];
                if( event_name == '') {
                    this.alert_empty_event();
                    return;
                }
                $.ajax({
                    url: '/api/mst/event/create',
                    data: {
                        'date': date,
                        'event_name': event_name,
                        'event_type': EVENT_TYPE_IN,
                    },
                    type: 'put'
                }).done((event_id) => {
                    this.set_in_event(date, event_id, event_name);
                    this.set_event_name(event_id, event_name);
                    this.input_in_events[date] = '';
                });
            },
            add_event_out(date) {
                if( ! this.input_out_events[date] ) {
                    this.alert_empty_event();
                    return;
                }
                let event_name = this.input_out_events[date];
                if( event_name == '') {
                    this.alert_empty_event();
                    return;
                }
                $.ajax({
                    url: '/api/mst/event/create',
                    data: {
                        'date': date,
                        'event_name': event_name,
                        'event_type': EVENT_TYPE_OUT,
                    },
                    type: 'put'
                }).done((event_id) => {
                    this.set_out_event(date, event_id, event_name);
                    // this.set_event_name(event_id, event_name);
                    this.input_out_events[date] = '';
                });
            },
            update_event(date, event_id, type) {
                let input_name = (type == EVENT_TYPE_IN)? this.input_in_events[date]: this.input_out_events[date];
                let input_date = (type == EVENT_TYPE_IN)? this.input_in_event_dates[date]: this.input_out_event_dates[date];
                if( input_name == '') {
                    this.alert_empty_event();
                    return;
                }
                $.ajax({
                    url: '/api/mst/event/update/' + event_id,
                    data: {
                        'date': input_date,
                        'name': input_name,
                    },
                    type: 'put'
                }).done((res) => {
                    this.close_event_edit(date, type);

                    if(type == EVENT_TYPE_IN) {
                        this.set_in_event(res.event.date, event_id, res.event.name);
                        if(date != res.event.date) {
                            this.del_in_event(date, event_id);
                        }
                        this.update_eventdate_actions(date, res.actions);
                    }
                    else if(type == EVENT_TYPE_OUT) {
                        this.set_out_event(res.event.date, event_id, res.event.name);
                        if(date != res.event.date) {
                            this.del_out_event(date, event_id);
                        }
                    }
                    this.set_event_name(event_id, input_name);
                });
            },
            remove_event(date, event_id, type) {
                if( ! this.is_remove_confirm() ) {
                    return;
                }
                $.ajax({
                    url: '/api/mst/event/remove/' + event_id,
                    data: {},
                    type: 'put'
                }).done((res) => {
                    this.close_event_edit(date, type);
                    if(type == EVENT_TYPE_IN) {
                        this.del_in_event(date, event_id);
                    }
                    else if(type == EVENT_TYPE_OUT) {
                        this.del_out_event(date, event_id);
                    }
                    this.$delete(this.event_names, event_id);
                });
            },

            set_actions(actions) {
                this.actions = []; // 活動内容の初期化
                for(let k in actions) {
                    this.set_action(actions[k].date, actions[k].grade_id, actions[k].id, actions[k].action, actions[k].event_id);
                }
            },
            set_action(date, grade_id, action_id, action, event_id) {
                let key = this.action_key(date, grade_id);
                if( ! this.actions[key] ) this.$set(this.actions, key, {});
                this.$set(this.actions[key], action_id, { id: action_id, action: action, event_id: event_id} );
            },
            del_action(date, grade_id, action_id) {
                let key = this.action_key(date, grade_id);
                this.$delete(this.actions[key], action_id);
            },
            update_eventdate_actions(old_date, change_actions) {
                if(change_actions.length == 0) {
                    return;
                }
                let actions = change_actions;
                for(let k in actions) {
                    this.set_action(actions[k].date, actions[k].grade_id, actions[k].id, actions[k].action, actions[k].event_id);
                    this.del_action(old_date, actions[k].grade_id, actions[k].id);
                }
            },
            action_key(date, grade_id) {
                // return date + grade_id;
                return date + '_' + grade_id;
            },
            show_action_edit(action_id, date, grade_id) {
                let key = this.action_key(date, grade_id);
                this.$set(this.input_actions, key, this.actions[key][action_id].action);
                this.$set(this.select_action_event_ids, key, this.actions[key][action_id].event_id);

                this.hide_action_edit(date, grade_id);
                $('#' + this.n_action_name_id(action_id)).addClass('active');
                this.hide_action_add(date, grade_id);
                $('#' + this.n_act_btn(date, grade_id)).addClass('hide');
            },
            hide_action_edit(date, grade_id) {
                $('.' + this.n_action_rec(date, grade_id)).removeClass('active');
            },
            close_action_edit(date, grade_id) {
                this.hide_action_edit(date, grade_id);
                this.hide_action_add(date, grade_id);
            },
            show_action_add(date, grade_id) {
                let key = this.action_key(date, grade_id);
                this.$set(this.input_actions, key, '');
                this.hide_action_edit(date, grade_id);
                $('#' + this.n_act_btn(date, grade_id)).addClass('hide');
                $('#' + this.n_act_input(date, grade_id)).removeClass('hide');
            },
            hide_action_add(date, grade_id) {
                $('#' + this.n_act_btn(date, grade_id)).removeClass('hide');
                $('#' + this.n_act_input(date, grade_id)).addClass('hide');
            },
            close_input_action(date, grade_id) {
                this.hide_action_add(date, grade_id);
            },
            n_action_rec(date, grade_id) {
                return 'act_rec_' + date + '_' + grade_id;
            },
            n_action_name_id(action_id) {
                return 'act_name_' + action_id;
            },
            n_act_btn(date, grade_id) {
                return 'actb_' + date + '_' + grade_id;
            },
            n_act_input(date, grade_id) {
                return 'acti_' + date + '_' + grade_id;
            },
            add_action(date, grade_id) {
                let key = this.action_key(date, grade_id);
                let input_action = this.input_actions[key];
                let select_event_id = this.select_action_event_ids[key];
                if ( ! input_action || input_action == '') {
                    this.alert_empty_action();
                    return;
                }
                $.ajax({
                    url: '/api/mst/action/create',
                    data: {
                        'date': date,
                        'grade_id': grade_id,
                        'action': input_action,
                        'event_id': select_event_id,
                    },
                    type: 'put'
                }).done((action_id) => {
                    this.set_action(date, grade_id, action_id, input_action, select_event_id);
                    this.input_actions[key] = '';
                    this.select_action_event_ids[key] = '';
                });
            },
            update_action(date, grade_id, action_id) {
                let key = this.action_key(date, grade_id);
                let input_action = this.input_actions[key];
                let select_event_id = this.select_action_event_ids[key];
                if ( ! input_action || input_action == '') {
                    this.alert_empty_action();
                    return;
                }
                $.ajax({
                    url: '/api/mst/action/update/' + action_id,
                    data: {
                        'action': input_action,
                        'event_id': select_event_id,
                    },
                    type: 'put'
                }).done((res) => {
                    this.set_action(date, grade_id, action_id, res.action, res.event_id);
                    this.close_action_edit(date, grade_id);
                });
            },
            remove_action(date, grade_id, action_id) {
                if( ! this.is_remove_confirm() ) {
                    return;
                }
                $.ajax({
                    url: '/api/mst/action/remove/' + action_id,
                    data: {},
                    type: 'put'
                }).done((res) => {
                    this.close_action_edit(date, grade_id);
                    this.del_action(date, grade_id, action_id);
                });
            },

            alert_empty_action() {
                alert('活動及び配慮事項を入力してください。');
            },
            alert_empty_event() {
                alert('行事名を入力してください。');
            },
            is_remove_confirm() {
                return confirm('削除します。よろしいですか?');
            },
        }
    });

    const viewYearMonth = () => {
        let year = $('#year-select-list option:selected').text();
        let month = $('#month-select-list option:selected').text();
        $('#yearmonth').text(year + month);
    }

    const adjustListHeight = () => {
        // console.log('window resize', window.innerHeight);
        let listHeight = window.innerHeight - 250;
        let min = 400;
        if(listHeight < min) listHeight = min;
        $('#list.scroll').height(listHeight);
    }

    const init = () => {
        viewYearMonth();
        list.fetch_grades();
        list.fetch();
        adjustListHeight();
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

    $(window).resize(function() {
        adjustListHeight();
    });

    init();
});
