var _ = require('lodash');
var Promise = require('es6-promise').Promise;
var customCasperJS = require('../utils/custom-casperjs');

/**
 * Closes any modal currently open
 *
 * @return {object}
 */
function closeAnyModal () {
  var casper = this.casper;
  var openModalSelector = '.modal.in';

  casper.then(function () {
    if (casper.exists(openModalSelector)) {
      casper.click(openModalSelector + ' .close[ng-click="cancel()"]');
      casper.wait(300);
    }
  });

  return this;
}

/**
 * Closes any notification currently open
 *
 * @return {object}
 */
function closeNotifications () {
  var casper = this.casper;
  var notificationSelector = 'a.ui-notify-cross.ui-notify-close';

  casper.then(function () {
    if (casper.exists(notificationSelector)) {
      casper.click(notificationSelector);
      casper.wait(500);
    }
  });

  return this;
}

module.exports = {

  /**
   * Initializes the page
   *
   * Stores a customized version of CasperJS and then wait for a
   * until a certain "ready" condition is met, if the page is set up to do so
   *
   * @param  {object} casper
   * @param  {boolean} clearDialogs if true it will close modals and notifications
   * @return {object}
   */
  init: function (casper, clearDialogs) {
    clearDialogs = typeof clearDialogs !== 'undefined' ? !!clearDialogs : true;

    this.casper = customCasperJS(casper);
    this.casper.options.waitTimeout = 60000;

    !!this.waitForReady && this.casper.then(function () {
      this.waitForReady();
    }.bind(this));

    if (clearDialogs) {
      closeAnyModal.call(this);
      closeNotifications.call(this);
    }

    return this;
  },

  /**
   * Used to extend the main page
   *
   * @param  {object} page
   *   a collection of methods and properties that will extend the main page
   * @return {object}
   */
  extend: function (page) {
    return _.assign(Object.create(this), page);
  },

  /**
   * Makes CasperJS wait until the modal is visible, then it returns the
   * specified modal object (if any)
   *
   * @param {string} name of the modal object
   * @return {object} the modal object
   */
  waitForModal: function (modalModule) {
    var casper = this.casper;

    return new Promise(function (resolve) {
      casper.then(function () {
        casper.waitUntilVisible('.modal', function () {
          casper.wait(300);

          if (modalModule) {
            resolve(require('./modals/' + modalModule).init(casper, false));
          } else {
            resolve();
          }
        });
      });
    });
  }
};
