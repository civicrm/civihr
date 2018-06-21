/* eslint-env amd, jasmine */

define([
  'common/angular',
  'common/lodash',
  'leave-absences/mocks/data/calendar-feed-config.data',
  'leave-absences/mocks/helpers/helper',
  './calendar-feeds.module'
], function (angular, _, calendarFeedConfigData, mockHelper) {
  'use strict';

  describe('CalendarFeedConfigAPI', function () {
    var $httpBackend, $rootScope, CalendarFeedConfigAPI, expectedResults;

    beforeEach(module('calendar-feeds'));
    beforeEach(inject(['$httpBackend', '$rootScope', 'CalendarFeedConfigAPI',
      function (_$httpBackend_, _$rootScope_, _CalendarFeedConfigAPI_) {
        $httpBackend = _$httpBackend_;
        $rootScope = _$rootScope_;
        CalendarFeedConfigAPI = _CalendarFeedConfigAPI_;

        interceptHTTP();
      }
    ]));

    describe('all()', function () {
      beforeEach(function () {
        spyOn(CalendarFeedConfigAPI, 'sendGET').and.callThrough();
        CalendarFeedConfigAPI
          .all()
          .then(function (results) {
            expectedResults = results;
          });
        $rootScope.$digest();
        $httpBackend.flush();
      });

      it('calls the "LeaveRequestCalendarFeedConfig.get" endpoint', function () {
        expect(CalendarFeedConfigAPI.sendGET.calls.mostRecent().args[0]).toBe('LeaveRequestCalendarFeedConfig');
        expect(CalendarFeedConfigAPI.sendGET.calls.mostRecent().args[1]).toBe('get');
      });

      it('returns expected data', function () {
        expect(expectedResults).toEqual(calendarFeedConfigData.all().values);
      });
    });

    describe('create()', function () {
      var params = { any: 'param' };

      beforeEach(function () {
        spyOn(CalendarFeedConfigAPI, 'sendPOST').and.callThrough();
        CalendarFeedConfigAPI
          .create(params)
          .then(function (results) {
            expectedResults = results;
          });
        $rootScope.$digest();
        $httpBackend.flush();
      });

      it('calls the "LeaveRequestCalendarFeedConfig.create" endpoint with specified params', function () {
        expect(CalendarFeedConfigAPI.sendPOST.calls.mostRecent().args).toEqual([
          'LeaveRequestCalendarFeedConfig',
          'create',
          params
        ]);
      });

      it('returns expected data', function () {
        expect(expectedResults).toEqual(calendarFeedConfigData.singleDataSuccess().values);
      });
    });

    /**
     * Intercept HTTP calls to be handled by httpBackend
     */
    function interceptHTTP () {
      // Intercept backend calls for GET CalendarFeedConfigAPI.all
      $httpBackend.whenGET(/action=get&entity=LeaveRequestCalendarFeedConfig/)
        .respond(calendarFeedConfigData.all());
      // Intercept backend calls for POST CalendarFeedConfigAPI.create
      $httpBackend.whenPOST(/\/civicrm\/ajax\/rest/)
        .respond(function (method, url, data) {
          if (mockHelper.isEntityActionInPost(data, 'LeaveRequestCalendarFeedConfig', 'create')) {
            return [201, calendarFeedConfigData.singleDataSuccess()];
          }
        });
    }
  });
});
