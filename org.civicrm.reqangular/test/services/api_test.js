define([
  'common/angular',
  'common/angularMocks',
  'common/services/api'
], function () {
  'use strict';

  describe('api', function () {
    var api, $httpBackend, $httpParamSerializer, $rootScope,
      entity = 'entity',
      action = 'action';

    beforeEach(module('common.apis'));

    beforeEach(inject(function (_api_, _$httpBackend_, _$httpParamSerializer_, _$rootScope_) {
      api = _api_;
      $rootScope = _$rootScope_;
      $httpBackend = _$httpBackend_;
      $httpParamSerializer = _$httpParamSerializer_;
    }));

    afterEach(function () {
      $httpBackend.flush();
    });

    describe('sendGET', function () {
      var promise;

      describe('when the API does not return an error', function () {
        var returnValue = {
          is_error: 0,
          somekey: 'someval'
        };

        beforeEach(function () {
          promise = expectAndSendGET(returnValue)
        });

        it('returns values sent from API', function () {
          promise.then(function (response) {
            expect(response).toEqual(returnValue);
          });
        });
      });

      describe('when the API returns an error', function () {
        var returnValue = {
          is_error: 1,
          error_message: 'some error message'
        };

        beforeEach(function () {
          promise = expectAndSendGET(returnValue)
        });

        it('rejects the promise with the error message provided by the API', function () {
          promise.catch(function (response) {
            expect(response).toBe(returnValue.error_message);
          });
        });
      });

      describe('limit', function () {
        var returnValue = {
          is_error: 0,
          somekey: 'someval'
        };

        describe('when limit is sent as a parameter', function () {
          var limit = 5;

          beforeEach(function () {
            expectAndSendGET(returnValue, {options: {limit: limit}});
          });

          it('send a GET request with original limit value', function () {
            $httpBackend.expectGET('/civicrm/ajax/rest?' + $httpParamSerializer({
                json: {options: {limit: limit}},
                sequential: 1,
                action: action,
                entity: entity
              }));
          });
        });

        describe('when limit is not sent as a parameter', function () {
          beforeEach(function () {
            expectAndSendGET(returnValue);
          });

          it('send a GET request with 0 set as limit', function () {
            $httpBackend.expectGET('/civicrm/ajax/rest?' + $httpParamSerializer({
                json: {options: {limit: 0}},
                sequential: 1,
                action: action,
                entity: entity
              }));
          });
        });
      });

      function expectAndSendGET(returnValue, params) {
        $httpBackend
          .whenGET(new RegExp('action=' + action + '&entity=' + entity))
          .respond(returnValue);

        return api.sendGET(entity, action, params, true);
      }
    });

    describe('sendPOST', function () {
      var promise;

      describe('when the API doesnt return an error', function () {
        var returnValue = {
          is_error: 0,
          somekey: 'someval'
        };

        beforeEach(function () {
          promise = expectAndSendPOST(returnValue);
        });

        it('returns values sent from API', function () {
          promise.then(function (response) {
            expect(response).toEqual(returnValue);
          });
        });
      });

      describe('when the API returns an error', function () {
        var returnValue = {
          is_error: 1,
          error_message: 'some error message'
        };

        beforeEach(function () {
          promise = expectAndSendPOST(returnValue);
        });

        it('rejects the promise with the error message provided by the API', function () {
          promise.catch(function (response) {
            expect(response).toBe(returnValue.error_message);
          });
        });
      });

      describe('limit', function () {
        var returnValue = {
          is_error: 0,
          somekey: 'someval'
        };

        describe('when limit is sent as a parameter', function () {
          var limit = 5;

          beforeEach(function () {
            expectAndSendPOST(returnValue, {options: {limit: limit}});
          });

          it('send a POST request with original limit value', function () {
            $httpBackend.expectPOST('/civicrm/ajax/rest', $httpParamSerializer({
              json: { options: { limit: limit } },
              sequential: 1,
              action: action,
              entity: entity
            }));
          });
        });

        describe('when limit is not sent as a parameter', function () {
          beforeEach(function () {
            expectAndSendPOST(returnValue);
          });

          it('send a POST request with 0 set as limit', function () {
            $httpBackend.expectPOST('/civicrm/ajax/rest', $httpParamSerializer({
              json: { options: { limit: 0 } },
              sequential: 1,
              action: action,
              entity: entity
            }));
          });
        });
      });

      function expectAndSendPOST(returnValue, params) {
        $httpBackend.whenPOST('/civicrm/ajax/rest').respond(returnValue);

        return api.sendPOST(entity, action, params);
      }
    });
  });
});
