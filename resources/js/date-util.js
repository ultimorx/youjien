'use strict';

const dateFormat = require('./dateformat');

Date.prototype.addDate = function (date) {
    this.setDate(this.getDate() + date);
    return this;
};
Date.prototype.addWeek = function (week) {
    this.setDate(this.getDate() + 7 * week);
    return this;
};
Date.prototype.addMonth = function (month) {
    this.setMonth(this.getMonth() + month);
    return this;
};
Date.prototype.addYear = function (year) {
    this.setFullYear(this.getFullYear() + year);
    return this;
};

Date.prototype.clone = function () {
    return new Date(this);
};

Date.prototype.separate = function () {
    return {
        year: this.getFullYear(),
        month: this.getMonth() + 1,
        date: this.getDate(),
        day: this.getDay(),
    };
};

Date.prototype.format = function (format) {
    return dateFormat(this, format);
};

Date.format = function (date, format) {
    if (date == null) {
        date = new Date();
    }
    return dateFormat(new Date(date), format);
};;

//module.exports = Date;
// Dateオブジェクトはグローバル