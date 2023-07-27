'use strict';

let dateFormat = require('./dateformat');

let getFormatDate = (date) => {
    return dateFormat(new Date(date), { date: 'YYYY-MM-DD' });
};

let getFormatJpDate = (date) => {
    return dateFormat(new Date(date), { date: 'YYYY年MM月DD日' });
};

let getToday = () => {
    let d = new Date();
    return getFormatDate(d);
};

let getNextDate = (date) => {
    let d = new Date(date);
    d.setDate(d.getDate() + 1);
    return getFormatDate(d);
};

let getPrevDate = (date) => {
    let d = new Date(date);
    d.setDate(d.getDate() - 1);
    return getFormatDate(d);
};

let getMonth = (date) => {
    return dateFormat(new Date(date), { date: 'M' });
};

module.exports.getToday = getToday;
module.exports.getNextDate = getNextDate;
module.exports.getPrevDate = getPrevDate;
module.exports.getMonth = getMonth;
module.exports.getFormatJpDate = getFormatJpDate;