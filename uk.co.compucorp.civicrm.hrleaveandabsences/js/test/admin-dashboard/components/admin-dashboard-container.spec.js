/* eslint-env amd, jasmine */

define([
  'common/angular',
  'leave-absences/admin-dashboard/app'
], function (angular) {
  'use strict';

  describe('adminDashboardContainer', function () {
    var $componentController, $log, $rootScope;

    beforeEach(module('admin-dashboard'));

    beforeEach(inject(function (_$componentController_, _$log_, _$rootScope_) {
      $componentController = _$componentController_;
      $log = _$log_;
      $rootScope = _$rootScope_;

      spyOn($log, 'debug');

      compileComponent();
    }));

    it('is initialized', function () {
      expect($log.debug).toHaveBeenCalled();
    });

    function compileComponent () {
      $componentController('adminDashboardContainer');
      $rootScope.$digest();
    }
  });
});
