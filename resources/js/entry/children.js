'use strict';

require('../common');

$(document).ready(() => {
    const date_area = require('../components/date-area')();
    let month = date_area.dateObj.separate().month;
    let $form = $('#classroom-select-form');

    const list = new Vue({
        el: '#children-list',
        data: {
            rosters: null,
            eveningtime_mst: [],
            bus_mst: [],
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/roster',
                    data: $form.serialize(),
                    type: 'get'
                }).done((res) => {
                    this.rosters = res;
                    // console.log(res[0].contract_evenings);
                });
            },
            edit(roster) {
                editForm.set(_.cloneDeep(roster));
                CheckSetContractDepartBus();
            },
            setEveningTimeMst() {
                $.ajax({
                    url: '/api/eveningtime',
                    data: {},
                    type: 'get'
                }).done((res) => {
                    for(let k in res) this.eveningtime_mst[res[k].id] = res[k].time;
                });
            },
            setBusMst() {
                $.ajax({
                    url: '/api/bus',
                    data: {},
                    type: 'get'
                }).done((res) => {
                    for(let k in res) this.bus_mst[res[k].id] = res[k].name;
                });
            },
            getEveningTimeName(id) {
                return (this.eveningtime_mst[id]) ? this.eveningtime_mst[id]: 'None';
            },
            getBusName(id) {
                return (this.bus_mst[id]) ? this.bus_mst[id]: 'None';
            },
        }
    });

    const editForm = new Vue({
        el: '#edit-form',
        data: {
            id: null,
            classroom_id: null,
            number: null,
            number_change: false,
            bus: '',
            contract_arrive_bus: [],
            contract_depart_bus: [],
            contract_mornings: _.mapValues(_.mapKeys(_.range(1, 13), (value, key) => value ), () => {return ''}),
            contract_evenings: _.mapValues(_.mapKeys(_.range(1, 13), (value, key) => value ), () => {return ''}),
            child: {
                id: null,
                name: null,
                kana: null,
                birthday: null,
                gender: 1,
                remarks: null,
                move_in_date: null,
                move_out_date: null,
            },
            classroom: {
                name: null,
                grade: {
                    name: null,
                }
            },
            validation_error: null,
            original: null,
            select_bus_disable: false
        },
        methods: {
            reset() {
                this.id = null;
                this.classroom_id = null;
                this.number = null;
                this.number_change = false;
                this.bus = '';
                this.child = {
                    id: null,
                    name: null,
                    kana: null,
                    birthday: null,
                    gender: 1,
                    remarks: null,
                    move_in_date: null,
                    move_out_date: null,
                };
                this.classroom = {
                    name: null,
                    grade: {
                        name: null,
                    }
                };
                this.validation_error = null;
                this.original = null;

                // リレーション先のデータを初期化
                this.contract_arrive_bus = [];
                this.contract_depart_bus = [];
                this.contract_mornings = _.mapValues(_.mapKeys(_.range(1, 13), (value, key) => value ), () => {return ''});
                this.contract_evenings = _.mapValues(_.mapKeys(_.range(1, 13), (value, key) => value ), () => {return ''});
                this.select_bus_disable = false;
            },
            set(roster) {
                this.original = roster;
                this.id = roster.id;
                this.classroom_id = roster.classroom_id;
                this.number = roster.number;
                this.bus = roster.bus_id || '';
                this.contract_arrive_bus = _.map(roster.contract_arrive_bus, 'month'); // 契約月の配列
                this.contract_depart_bus = _.map(roster.contract_depart_bus, 'month'); // 契約月の配列
                this.contract_mornings = _.merge(this.contract_mornings, _.mapValues(_.keyBy(roster.contract_mornings, 'month'), 'morning_time_id')); // 契約月 => 契約時間帯 のオブジェクト
                this.contract_evenings = _.merge(this.contract_evenings, _.mapValues(_.keyBy(roster.contract_evenings, 'month'), 'evening_time_id')); // 契約月 => 契約時間帯 のオブジェクト
                this.child = roster.child;
                this.classroom = roster.classroom;
                this.validation_error = null;
                this.select_bus_disable = false;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                let roster_id = _.find(fd, {name:'id'}).value;
                let url = '/api/roster/create';
                let type = 'post';
                if(roster_id) {
                    url = '/api/roster/' + roster_id;
                    type = 'put';
                }
                console.log('roster_id',roster_id, url, type,(roster_id));
                $.ajax({
                    url: url,
                    data: fd,
                    type: type
                }).done((res) => {
                    if (typeof res.validation_error === 'undefined') {
                        list.fetch();
                        $('#edit-area').modal('hide');
                        this.reset();
                    }
                    this.validation_error = res.validation_error;
                    $('#edit-area').scrollTop(0);
                });
            }
        }
    });

    // クラス選択
    $(document).on('click', '#classroom-search', () => {
        list.fetch();
    });
    $(document).on('change', '#classroom-select-list', () => {
        list.fetch();
    });

    $(document).on('click', '#create', () => {
        console.log('#create', list.rosters.length);
        editForm.reset();
        let $classroom = $('#classroom-select-list option:selected');
        editForm.classroom.grade.name = $classroom.attr('data-grade-name');
        editForm.classroom.name = $classroom.attr('data-classroom-name');
        editForm.number = list.rosters.length + 1;
        editForm.classroom_id = $('#classroom-select-list').val();
        editForm.select_bus_disable = true;
        editForm.number_change = true;
    });

    $(document).on('click', '#csv-download', () => {
        let classroom_id = $('#classroom-select-list').val();
        window.open('api/roster/download/' + classroom_id);
    });

    $(document).on('click', '#edit-area .btn-save', () => {
        editForm.save();
    });

    $(document).on('click', '#edit-area .btn-close', () => {
        editForm.reset();
    });

    $(document).on('change', '#edit-area [name^="contract_arrive_bus"]:first', (event) => {
        if( ! $('input#arrive-copy').prop('checked') ) return;
        editForm.contract_arrive_bus = event.target.checked ? _.range(1, 13) : [];
    });
    $(document).on('change', '#edit-area [name^="contract_depart_bus"]:first', (event) => {
        if( ! $('input#depart-copy').prop('checked') ) return;
        editForm.contract_depart_bus = event.target.checked ? _.range(1, 13) : [];
        //CheckChangeContractDepartBus();
        editForm.select_bus_disable = !event.target.checked;
    });
    $(document).on('change', '#edit-area [name^="contract_mornings"]:first', (event) => {
        if( ! $('input#morning-copy').prop('checked') ) return;
        _.each(editForm.contract_mornings, (value, key) => {
            editForm.contract_mornings[key] = event.target.value;
        });
    });
    $(document).on('change', '#edit-area [name^="contract_evenings"]:first', (event) => {
        if( ! $('input#evening-copy').prop('checked') ) return;
        _.each(editForm.contract_evenings, (value, key) => {
            editForm.contract_evenings[key] = event.target.value;
        });
    });
    $(document).on('click', '#edit-area input[name^="contract_depart_bus"]', (event) => {
        CheckChangeContractDepartBus();
    });

    const CheckSetContractDepartBus = () => {
        console.log('CheckContractDepartBus', editForm.original.contract_depart_bus.length);
        if(editForm.original.contract_depart_bus.length == 0) {
            editForm.select_bus_disable = true;
            return;
        }
        editForm.select_bus_disable = false;
    }
    const CheckChangeContractDepartBus = () => {
        console.log('CheckContractDepartBus', $('input.contract_depart_bus:checked').length);
        if( $('input.contract_depart_bus:checked').length == 0) {
            editForm.select_bus_disable = true;
            return;
        }
        editForm.select_bus_disable = false;
    }

    list.fetch();
    list.setEveningTimeMst();
    list.setBusMst();
});
