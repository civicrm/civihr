/* eslint-env amd */

define([
  'common/angular'
], function (angular) {
  'use strict';

  angular.module('contactsummary.config', ['contactsummary.constants']).config(config);

  config.$inject = [
    'settings', '$routeProvider', '$resourceProvider', '$httpProvider',
    '$logProvider'
  ];

  function config (settings, $routeProvider, $resourceProvider, $httpProvider,
    $logProvider) {
    $logProvider.debugEnabled(settings.debug);

    $routeProvider
      .when('/', {
        controller: 'ContactSummaryController',
        controllerAs: 'ContactSummaryCtrl',
        templateUrl: settings.pathBaseUrl + settings.pathTpl + 'mainTemplate.html',
        resolve: {}
      })
      .when('/secondpath', {
        controller: 'ContactSummaryController',
        controllerAs: 'ContactSummaryCtrl',
        templateUrl: settings.pathBaseUrl + settings.pathTpl + 'mainTemplate.html',
        resolve: {}
      })
      .when('/:otherwise*', {
        template: '<script>window.location.hash = "/";</script>',
        resolve: {}
      });

    $resourceProvider.defaults.stripTrailingSlashes = false;

    $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  }
});
