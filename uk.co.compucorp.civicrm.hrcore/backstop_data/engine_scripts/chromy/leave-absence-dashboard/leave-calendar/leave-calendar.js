'use strict';

var page = require('../../../../page-objects/leave-absence-dashboard');

module.exports = function (chromy) {
  page.init(chromy).openTab('leave-calendar');
};