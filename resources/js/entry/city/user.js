'use strict';

require('../../common');

$(document).ready(() => {
    // const date_area = require('../../components/date-area')();
    // let month = date_area.dateObj.separate().month;
    // let $form = $('#user-select-form');

    const list = new Vue({
        el: '#list',
        data: {
            list: null
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/city/user',
                    // data: $form.serialize(),
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                });
            },
            isActive(active) {
                return (active != 1);
            },
            viewActive(active) {
                return (active == 1)? '': '無効';
            },
            edit(user) {
                editForm.set(_.cloneDeep(user));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-area',
        data: {
            id: null,
            kindergarten_id: 0,
            active: 1,
            name: '',
            pass: '',
            order: 0,
            validation_error: null,
            original: null,
            deletable: true
        },
        methods: {
            reset() {
                this.id = null;
                this.kindergarten_id = 0;
                this.active = 1;
                this.name = '';
                this.pass = '';
                this.order = 1;
                this.validation_error = null;
                this.original = null;
            },
            set(user) {
                this.id = user.id;
                this.active = user.active;
                this.kindergarten_id = user.kindergarten_id;
                this.name = user.name;
                // this.pass = user.pass;
                this.order = user.order;
                this.validation_error = null;
                this.original = user;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                let user_id = _.find(fd, {name:'id'}).value;
                let url = '/api/city/user/create';
                let type = 'post';
                if(user_id) {
                    url = '/api/city/user/' + user_id;
                    type = 'put';
                }
                console.log('user_id',user_id, url, type,(user_id));
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
            },
            delete() {
                let fd = $('#edit-form').serializeArray();
                let user_id = _.find(fd, {name:'id'}).value;
                console.log('delete user_id',user_id);

                if(!user_id) {
                    return;
                }
                let url = '/api/city/user/' + user_id;
                let type = 'delete';

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
        editForm.kindergarten_id = $("#select-kindergarten option:first").val();
    });

    $(document).on('click', '#edit-area .btn-save', () => {
        editForm.save();
    });

    $(document).on('click', '#edit-area .btn-close', () => {
        editForm.reset();
    });

    $(document).on('click', '#edit-area .btn-delete', () => {
        if( confirm('削除してもよろしいですか?') ) {
            editForm.delete();
        }
    });

    list.fetch();
});
