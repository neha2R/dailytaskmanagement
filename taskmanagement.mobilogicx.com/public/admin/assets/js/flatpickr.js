// npm package: flatpickr
// github link: https://github.com/flatpickr/flatpickr

const { now } = require("lodash");

$(function() {
  'use strict';

  // date picker 
  if($('#flatpickr-date').length) {
    flatpickr("#flatpickr-date", {
      wrap: true,
      dateFormat: "Y-m-d",
    });
  }

  // time picker
  if($('#flatpickr-time').length) {
    flatpickr("#flatpickr-time", {
      wrap: true,
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
    });
  }

});