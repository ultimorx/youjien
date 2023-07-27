'use strict';

const dateFormat = require('../dateformat');

module.exports = () => {
    return new Vue({
        el: '#week-area',
        data: {
            dateObj: new Date(),
            bizyear: null,
            bizweek: null,
            viewdays: null
        },
        methods: {
            changePrevWeek() {
                let sub = 7;

                // 年度切替
                let month = this.dateObj.format('M');
                let day = this.dateObj.format('D');
                // 4月1日から前年度の最後の月曜日に変更
                if(month == 4 && day == 1) {
                    let week_key = this.dateObj.getDay();
                    sub = week_key - 1;
                    // 日曜日の場合
                    if(week_key == 0) sub = 6;
                    // 月曜日の場合
                    if(week_key == 1) sub = 7;
                }
                // 4月2日〜7日の場合、4月1日に変更
                if (month == 4 && (day >= 2 && day <= 7)) {
                    sub = day - 1;
                }

                this.dateObj = this.dateObj.addDate(sub * -1).clone();
            },
            changeNextWeek() {
                let add = 7;

                // 年度切替
                let month = this.dateObj.format('M');
                let day = this.dateObj.format('D');
                // 前年度の最後の月曜日から4月1日に変更
                if(month == 3 && day >= 26) {
                    add = 32 - day;
                }
                // 4月1日から次の月曜日に変更
                if(month == 4 && day == 1) {
                    let week_key = this.dateObj.getDay();
                    add = 8 - week_key;
                    // 日曜日の場合
                    if(week_key == 0) add = 1;
                }

                this.dateObj = this.dateObj.addDate(add).clone();
            },
            changeDate(date) {
                this.dateObj = new Date(date);
            },
            listen () {
                $('#date-select.datepicker').on('changeDate', (event) => {
                    this.changeDate(event.dates.pop());
                });
            }
        },
        computed: {
            date: function () {
                $(this.$el).trigger('change');
                return dateFormat(this.dateObj, 'M月D日'); // 'YYYY年MM月DD日〜'
            }
        }
    });
};
