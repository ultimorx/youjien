'use strict';

require('../../common');

$(document).ready(() => {
    const accesslog = new Vue({
        el: '#accesslog',
        data: {
            count: 100
        }
    });

    const list = new Vue({
        el: '#list',
        data: {
            list: null
        },
        methods: {
            fetch() {
                $.ajax({
                    url: '/api/city/user/accesslogs',
                    data: {
                        'count': accesslog.count,
                    },
                    type: 'get'
                }).done((res) => {
                    this.list = res;
                });
            }
        }
    });

    list.fetch();
});
