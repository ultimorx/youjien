'use strict';

require('../../common');

$(document).ready(() => {
    const search = new Vue({
        el: '#search',
        data: {
            count : ''
        },
    });

    const list = new Vue({
        el: '#list',
        data: {
            list: null,
            classrooms: null,
            child_id_classroom_ids: null,
            classroom_child_counts: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/mst/children/search',
                    // data: $form.serialize(),
                    data: {
                        'bizyear': $('#select-bizyear').val(),
                        'grade': $('#select-grade').val()
                    },
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                    search.count = res.length;
                });
                this.fetch_classrooms();
                this.fetch_child_id_classroom_ids();
            },
            fetch_classrooms() {
                $.ajax({
                    url: '/api/mst/rosters/classroom/search',
                    data: {
                        'bizyear': $('#select-bizyear').val(),
                        'grade': $('#select-grade').val()
                    },
                    type: 'get'
                }).done((res) => {
                    this.classrooms = res;
                    // console.log(res);
                });
            },
            fetch_child_id_classroom_ids() {
                $.ajax({
                    url: '/api/mst/rosters/classroom/child_ids',
                    data: {
                        'bizyear': $('#select-bizyear').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.child_id_classroom_ids = res['child_ids'];
                    this.classroom_child_counts = res['classroom_child_counts'];
                });
            },
            count_classroom_child(classroom_id) {
                if( ! this.classroom_child_counts[classroom_id]) {
                    return 0;
                }
                return this.classroom_child_counts[classroom_id];
            },
            match_child_classroom(child_id, classroom_id) {
                return this.child_id_classroom_ids[child_id] == classroom_id;
            },
            save(child_id, classroom_id) {
                if(child_id == '' || classroom_id == '') return;
                let bizyear = $('#select-bizyear').val();
                $.ajax({
                    url: '/api/mst/rosters/classroom/child/save',
                    data: {'bizyear': bizyear, 'classroom_id': classroom_id, 'child_id': child_id},
                    type: 'put'
                }).done((res) => {
                    this.child_id_classroom_ids[child_id] = classroom_id;
                    this.fetch_child_id_classroom_ids();
                });
            },
            edit(children) {
                editForm.set(_.cloneDeep(children));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-area',
        data: {
            id: null,
            name: null,
            kana: null,
            birthday: null,
            gender: 1,
            remarks: null,
            move_in_date: null,
            move_out_date: null,
            validation_error: null,
            original: null,
            deletable: false
        },
        methods: {
            reset() {
                this.id = null;
                this.name = null;
                this.kana = null;
                this.birthday = null;
                this.gender = 1;
                this.remarks = null;
                this.move_in_date = null;
                this.move_out_date = null;
                this.validation_error = null;
                this.original = null;
            },
            set(children) {
                this.children = children.children;
                this.id = children.id;
                this.name = children.name;
                this.kana = children.kana;
                this.birthday = children.birthday;
                this.gender = children.gender;
                this.remarks = children.remarks;
                this.move_in_date = children.move_in_date;
                this.move_out_date = children.move_out_date;
                this.validation_error = null;
                this.original = children;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                // let children_id = _.find(fd, {name:'id'}).value;

                let url = '/api/mst/children/save';
                let type = 'post';
                // if(children_id) {
                //     url = '/api/mst/children/' + children_id;
                //     type = 'put';
                // }
                // console.log('children_id',children_id, url, type,(children_id));
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

    $(document).on('click', '#create', () => {
        editForm.reset();
    });

    $(document).on('click', '#csv-download', () => {
        let bizyear = $('#select-bizyear').val();
        let grade = $('#select-grade').val();
        window.open('/api/mst/children/download?bizyear=' + bizyear + '&grade=' + grade);
    });

    $(document).on('click', '#edit-area .btn-save', () => {
        editForm.save();
    });

    $(document).on('click', '#edit-area .btn-close', () => {
        editForm.reset();
    });

    // 年度 or 学年選択
    $(document).on('change', '#select-bizyear, #select-grade', () => {
        list.fetch();
    });

    list.fetch();
});
