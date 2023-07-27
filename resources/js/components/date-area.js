'use strict';

const dateFormat = require('../dateformat');

module.exports = () => {
    return new Vue({
        el: '#date-area',
        data: {
            dateObj: new Date()
        },
        methods: {
            changePrevDate() {
               this.dateObj = this.dateObj.addDate(-1).clone();
            },
            changeNextDate() {
               this.dateObj = this.dateObj.addDate(1).clone();
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
              return dateFormat(this.dateObj, 'YYYY年MM月DD日');
            }
          }
    });
};
