'use strict';

require('../../common');

$(document).ready(() => {

    const list = new Vue({
        el: '#list',
        data: {
            list: null,
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/city/event',
                    data: {
                        'bizyear': $('#year-select-list').val(),
                    },
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                });
            },
            edit(event) {
                editForm.set(_.cloneDeep(event));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-area',
        data: {
            id: null,
            name: '',
            date: '',
            validation_error: null,
            original: null,
            deletable: true
        },
        methods: {
            reset() {
                this.id = null;
                this.name = '';
                this.date = '';
                this.validation_error = null;
                this.original = null;
            },
            set(event) {
                this.id = event.id;
                this.name = event.name;
                this.date = event.date;
                this.validation_error = null;
                this.original = event;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                let event_id = _.find(fd, {name:'id'}).value;
                let url = '/api/city/event/create';
                let type = 'post';
                if(event_id) {
                    url = '/api/city/event/' + event_id;
                    type = 'put';
                }
                console.log('event_id',event_id, url, type,(event_id));
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
                let event_id = _.find(fd, {name:'id'}).value;
                console.log('delete event_id',event_id);

                if(!event_id) {
                    return;
                }
                let url = '/api/city/event/' + event_id;
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

    $(document).on('change', '#year-select-list', () => {
        list.fetch();
    });

    $(document).on('click', '#page_reload', () => {
        location.reload();
    });


    list.fetch();
});
