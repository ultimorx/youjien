'use strict';

require('../../common');

$(document).ready(() => {
    const dateFormat = require('../../dateformat');
    let today = dateFormat(new Date(), 'YYYY-MM-DD');;

    const list = new Vue({
        el: '#list',
        data: {
            list: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/city/information',
                    // data: $form.serialize(),
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                });
            },
            isDisplay(display) {
                return (display != 1);
            },
            viewDisplay(display) {
                return (display == 1)? '': '非表示';
            },
            edit(information) {
                editForm.set(_.cloneDeep(information));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-area',
        data: {
            id: null,
            public_date: today,
            title: '',
            message: '',
            display: 1,
            validation_error: null,
            original: null,
            deletable: true
        },
        methods: {
            reset() {
                this.id = null;
                this.public_date = today;
                this.title = '';
                this.message = '';
                this.display = 1;
                this.validation_error = null;
                this.original = null;
            },
            set(information) {
                this.id = information.id;
                this.public_date = information.public_date;
                this.title = information.title;
                this.message = information.message;
                this.display = information.display;
                this.validation_error = null;
                this.original = information;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                let information_id = _.find(fd, {name:'id'}).value;
                let url = '/api/city/information/create';
                let type = 'post';
                if(information_id) {
                    url = '/api/city/information/' + information_id;
                    type = 'put';
                }
                console.log('information_id',information_id, url, type,(information_id));
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
                let information_id = _.find(fd, {name:'id'}).value;
                console.log('delete information_id',information_id);

                if(!information_id) {
                    return;
                }
                let url = '/api/city/information/' + information_id;
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
