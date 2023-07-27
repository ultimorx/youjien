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
                    url: '/api/mst/bizyear',
                    // data: $form.serialize(),
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                    console.log(res);
                });
            },
            edit(bizyear) {
                editForm.set(_.cloneDeep(bizyear));
            }
        }
    });

    const editForm = new Vue({
        el: '#edit-form',
        data: {
            bizyear: null,
            run: 1,
            validation_error: null,
            original: null
        },
        methods: {
            reset() {
                this.bizyear = null;
                this.run = 1;
                this.validation_error = null;
                this.original = null;
            },
            set(bizyear) {
                this.bizyear = bizyear.bizyear;
                this.run = bizyear.run;
                this.validation_error = null;
                this.original = bizyear;
            },
            save() {
                let fd = $('#edit-form').serializeArray();
                // let bizyear_id = _.find(fd, {name:'id'}).value;
                let url = '/api/mst/bizyear/save';
                let type = 'post';
                // if(bizyear_id) {
                //     url = '/api/mst/bizyear/' + bizyear_id;
                //     type = 'put';
                // }
                // console.log('bizyear_id',bizyear_id, url, type,(bizyear_id));
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

    list.fetch();
});
