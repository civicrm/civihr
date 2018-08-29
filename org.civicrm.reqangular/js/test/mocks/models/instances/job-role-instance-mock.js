/* eslint-env amd */

define([
  'common/lodash',
  'common/mocks/module'
], function (_, mocks) {
  'use strict';

  mocks.factory('JobRoleInstanceMock', ['$q', 'JobRoleInstance', function ($q, instance) {
    return _.assign(Object.create(instance), {

      /**
       * Checks if the given object is a modal instance
       *
       * @param {object} object
       * @return {boolean}
       */
      isInstance: function (object) {
        return _.isEqual(_.functionsIn(object), _.functionsIn(instance));
      },

      /**
      * Returns a promise that will resolve with the given value
      *
      * @param {any} value
      * @return {Promise}
      */
      promiseResolvedWith: function (value) {
        var deferred = $q.defer();

        deferred.resolve(value);

        return deferred.promise;
      }
    });
  }]);
});
