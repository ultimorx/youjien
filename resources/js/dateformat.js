'use strict';

let LAST_TWO_DIGITS = -2;

let defaultFormat = {
    date: 'YYYY-MM-DD',
    weekdays: ['日', '月', '火', '水', '木', '金', '土']
};

let main = function (d, format) {
    if (format == null) {
        format = defaultFormat;
    }
    if (typeof format === 'string') {
        format = {date: format};
    }
    let year = d.getFullYear();
    let month = d.getMonth() + 1;
    let date = d.getDate();
    let hour = d.getHours();
    let minute = d.getMinutes();
    let second = d.getSeconds();
    let weekday = (format.weekdays) ? format.weekdays[d.getDay()] : defaultFormat.weekdays[d.getDay()];
    return format.date
            .replace('YYYY', year)
            .replace('MM', ('0' + month).slice(LAST_TWO_DIGITS))
            .replace('M', month)
            .replace('DD', ('0' + date).slice(LAST_TWO_DIGITS))
            .replace('D', date)
            .replace('HH', ('0' + hour).slice(LAST_TWO_DIGITS))
            .replace('H', hour)
            .replace('II', ('0' + minute).slice(LAST_TWO_DIGITS))
            .replace('I', minute)
            .replace('SS', ('0' + second).slice(LAST_TWO_DIGITS))
            .replace('S', second)
            .replace('ddd', weekday)
        ;
};

let DateFormat = function (options) {
    if (options == null) {
        options = defaultFormat;
    }
    this.options = options;
};

DateFormat.prototype.format = function (date) {
    return main(date, this.options);
};

module.exports = main;
module.exports.constructor = DateFormat;
