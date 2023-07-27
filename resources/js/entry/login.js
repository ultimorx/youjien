'use strict';

require('../common');

$(document).ready(() => {
    const loginForm = new Vue({
        el: '#login-form',
        data: {
            name: '',
            pass: '',
            validation_error: null,
            original: null
        },
        methods: {
            login() {
                $.ajax({
                    url: '/api/login/user',
                    data: $('#login-form').serializeArray(),
                    type: 'post'
                }).done((res) => {
                    console.log('login res : ', res);
                    if (typeof res.validation_error === 'undefined') {
                        this.setCookie(LOGIN_NAME, this.name);
                        this.setCookie(LOGIN_PASS, this.pass);
                        // リダイレクト
                        location.href = '/';
                    }
                    this.validation_error = res.validation_error;
                });
            },
            getCookie(key) {
                return $cookies.get(key);
            },
            setCookie(key, value) {
                $cookies.config(LOGIN_COOKIE_EXPIRE, '');
                $cookies.set(key, value);
            }
        }
    });

    loginForm.name = loginForm.getCookie(LOGIN_NAME);
    loginForm.pass = loginForm.getCookie(LOGIN_PASS);

    $(document).on('click', '.btn-login', () => {
        loginForm.login();
    });

    $(window).keyup(function(e){
        if(e.which != 13){ // is EnterKey
            return;
        }
        $('.btn-login').trigger('click');
    });
});
