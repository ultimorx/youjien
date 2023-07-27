'use strict';

require('../../../common');

$(document).ready(() => {
    const weekData = require('../../../components/week-area')();
    weekData.listen();

    const dateFormat = require('../../../dateformat');

    // 以下の変数はjs読み込み元HTMLで定義する
    // var PARAM_DATE
    // var PARAM_CLASSROOM_ID

    var bizweekFirstDate = '';
    var selectedGradeID = '';

    function setBizweekFirstDate() {
        bizweekFirstDate = weekData.dateObj.format();
    }

    function setSelectedGradeID() {
        let $classroom = $('#classroom-select-list option:selected');
        selectedGradeID = $classroom.attr('data-grade-id');
    }

    function setData() {
        setBizweekFirstDate();
        setSelectedGradeID();
        list.fetch();
    }

    function setIniData() {
        setBizweekFirstDate();
        setSelectedGradeID();
        list.fetch();
    }

    function resetList() {
        list.reset_list();
    }

    function resetMeta() {
        list.reset_meta();
    }

    const list = new Vue({
        el: '#list',
        data: {
            sheet: [],
            inputs: [],
            in_events: [],
            events: {},
            actions: {},
            aim1: '',
            aim2: '',
            gradename: '',
            classname: '',
            tearchername: '',
            firstdate: '',
            lastdate: '',
        },
        methods: {
            reset_list() {
                this.sheet = [];
                this.inputs = [];
                this.in_events = [];
                this.events = {};
                this.actions = {};
                // this.aim1 = '';
                // this.aim2 = '';
                // this.gradename = '';
                // this.classname = '';
                // this.tearchername = '';
                // this.firstdate = '';
                // this.lastdate = '';
            },
            reset_meta() {
                this.aim1 = '';
                this.aim2 = '';
                this.gradename = '';
                this.classname = '';
                this.tearchername = '';
                this.firstdate = '';
                this.lastdate = '';
            },
            fetch() {
                this.set_meta();
                this.fetch_event();
                this.fetch_actions();
                this.fetch_sheet();
                this.fetch_aim();
            },
            fetch_sheet() {
                let classroom_id = $('#classroom-select-list').val();
                $.ajax({
                    url: '/api/mst/report/days',
                    data: {
                        'date': bizweekFirstDate,
                        'classroom_id': classroom_id, // クラスは年度毎に登録している。2020年度たんぽぽ、2021年度たんぽぽ
                    },
                    type: 'get'
                }).done((res) => {
                    this.sheet = res;
                    weekData.bizweek = null;
                    weekData.viewdays = null;
                    if(res[0]) {
                        $('#year-txt').show();
                        let week_first_day = res[0];
                        let week_last_day = res[res.length-1];
                        weekData.changeDate(week_first_day['date']);
                        weekData.bizyear = week_first_day['bizyear'];
                        weekData.bizweek = week_first_day['bizweek'];
                        weekData.viewdays = res.length;
                        this.setData(res);
                        this.firstdate = this.viewDateJp(week_first_day.date);
                        this.lastdate = this.viewDateJp(week_last_day.date);
                        this.set_param_print_page_btn(bizweekFirstDate, classroom_id);

                        console.log('fetch_sheet()', res[0].date, res[res.length-1].date);
                        console.log('this.actions : ',this.actions.length,this.actions);
                    } else {
                        $('#year-txt').hide();
                    }
                });
            },
            fetch_event() {
                $.ajax({
                    url: '/api/mst/event/bizweeklist',
                    data: {
                        'date': bizweekFirstDate,
                        'grade_id': selectedGradeID,
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_events(res);
                    console.log('fetch_event()');
                });
            },
            fetch_actions() {
                $.ajax({
                    url: '/api/mst/action/bizweeklist',
                    data: {
                        'date': bizweekFirstDate,
                        'grade_id': selectedGradeID,
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_actions(res);
                    console.log('fetch_actions()');
                });
            },
            viewDateJp(d){
                return dateFormat(new Date(d), 'YYYY年 M月 D日');
            },
            viewDate(d){
                return dateFormat(new Date(d), 'M/D');
            },
            viewWeek(d){
                return dateFormat(new Date(d), 'ddd');
            },
            isToday(line){
                let today = dateFormat(new Date(), 'YYYY-MM-DD');
                return (line.date == today);
            },
            setData(reports){
                for(let k in reports) {
                    let key = reports[k].id;
                    this.inputs[key] = {
                        'life' : reports[k].life,
                        'health' : reports[k].health
                    }
                }
            },
            set_events(events) {
                let TYPE_IN = 10;
                let TYPE_OUT = 20;
                for(let k in events) {
                    let date = events[k].date;
                    let type = events[k].type;
                    if(type == TYPE_IN) {
                        this.set_in_event(date, events[k].id, events[k].name);
                    }
                    this.events[events[k].id] = events[k].name;
                }
            },
            set_in_event(date, event_id, event_name) {
                if( ! this.in_events[date] ) this.$set(this.in_events, date, {});
                this.$set(this.in_events[date], event_id, { id: event_id, name: event_name});
            },
            set_actions(acts) {
                for(let k in acts) {
                    this.set_action(k, acts[k].date, acts[k].grade_id, acts[k].id, acts[k].action, acts[k].event_id);
                }
            },
            set_action(idx, date, grade_id, action_id, action, event_id) {
                this.$set(this.actions, idx, { id: action_id, action: action, event_id: event_id, date: date} );
            },
            event_name(event_id) {
                return (this.events[event_id]) ? this.events[event_id] : '';
            },
            fetch_aim() {
                $.ajax({
                    url: '/api/mst/aim/week',
                    data: {
                        'date': bizweekFirstDate,
                        'grade_id': selectedGradeID,
                    },
                    type: 'get'
                }).done((res) => {
                    this.set_aims(res);
                    console.log('fetch_aim()', res);
                });
            },
            set_aims(aim){
                let prefix = {
                    'life' : '【生活】',
                    'play' : '【遊び】'
                }
                if( ! aim.life) aim.life = '';
                if( ! aim.play) aim.play = '';
                if(this.isGradeTypeIjyouji()) {
                    this.aim1 = (aim.life == '')? '': prefix.life + aim.life;
                    this.aim2 = (aim.play == '')? '': prefix.play + aim.play;
                } else {
                    this.aim1 = (aim.play == '')? '': prefix.play + aim.play;
                    this.aim2 = (aim.life == '')? '': prefix.life + aim.life;
                }
            },
            isGradeTypeIjyouji() {
                let ijyou = '以上児';
                let $classroom = $('#classroom-select-list option:selected');
                return $classroom.attr('data-grade-age-type') == ijyou;
            },
            set_meta() {
                let $classroom = $('#classroom-select-list option:selected');
                this.gradename = $classroom.attr('data-grade-name');
                this.classname = $classroom.attr('data-classroom-name');
                this.tearchername = $classroom.attr('data-teacher');
            },
            set_param_print_page_btn(date, classroom_id) {
                let $a_btn_print_page = $('#a_btn_print_page');
                let org_href = $a_btn_print_page.attr('org_href');
                $a_btn_print_page.attr('href', org_href + '?date=' + date + '&classroom_id=' + classroom_id);
            }
        }
    });

    // 前週、翌週ボタン押下時
    $(document).on('click', '#week-area .btn', () => {
        resetList();
        setData();
    });
    // カレンダー日付選択時
    $('#date-select.datepicker').on('changeDate', (event) => {
        resetList();
        setData();
    });

    // クラス選択時
    $(document).on('change', '#classroom-select-list', () => {
        resetMeta();
        resetList();
        setData();
    });
    // 再読込
    $(document).on('click', '#classroom-search', () => {
        resetMeta();
        resetList();
        setData();
    });

    const init = () => {
        if(PARAM_DATE) {
            weekData.changeDate(PARAM_DATE);
        }
        if(PARAM_CLASSROOM_ID) {
            $('#classroom-select-list').val(PARAM_CLASSROOM_ID);
        }
        setData();
    }
    init();
});
