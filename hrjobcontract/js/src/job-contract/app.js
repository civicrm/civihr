/* eslint-env amd */

define([
  'common/angular',
  'common/ui-select',
  'common/services/dom-event-trigger',
  'common/services/angular-date/date-format',
  'common/modules/routers/compu-ng-route',
  'common/modules/directives',
  'common/directives/angular-date/date-input',
  'common/filters/time-unit-applier.filter',
  'leave-absences/shared/models/absence-type.model',
  'job-contract/modules/job-contract.controllers',
  'job-contract/controllers/contract-list.controller',
  'job-contract/controllers/contract.controller',
  'job-contract/controllers/revision-list.controller',
  'job-contract/controllers/modal/modal-change-reason.controller',
  'job-contract/controllers/modal/modal-contract.controller',
  'job-contract/controllers/modal/modal-contract-new.controller',
  'job-contract/controllers/modal/modal-dialog.controller',
  'job-contract/controllers/modal/modal-progress.controller',
  'job-contract/controllers/modal/modal-revision.controller',
  'job-contract/controllers/form/form-general.controller',
  'job-contract/controllers/form/form-hour.controller',
  'job-contract/controllers/form/form-health.controller',
  'job-contract/controllers/form/form-pay.controller',
  'job-contract/controllers/form/form-pension.controller',
  'job-contract/controllers/form/form-leave.controller',
  'job-contract/modules/job-contract.directives',
  'job-contract/directives/contact.directive',
  'job-contract/directives/loader.directive',
  'job-contract/directives/number.directive',
  'job-contract/directives/validate.directive',
  'job-contract/modules/job-contract.filters',
  'job-contract/filters/capitalize.filter',
  'job-contract/filters/get-obj-by-id.filter',
  'job-contract/filters/format-amount.filter',
  'job-contract/filters/format-period.filter',
  'job-contract/filters/parse-int.filter',
  'job-contract/modules/job-contract.services',
  'job-contract/services/contract.service',
  'job-contract/services/contract-revision-list.service',
  'job-contract/vendor/job-summary'
], function (angular) {
  'use strict';

  angular.module('job-contract', [
    'ngAnimate',
    'compuNgRoute',
    'ngResource',
    'angularFileUpload',
    'ui.bootstrap',
    'ui.select',
    'common.angularDate',
    'common.services',
    'common.directives',
    'common.filters',
    'leave-absences.models',
    'job-contract.controllers',
    'job-contract.directives',
    'job-contract.filters',
    'job-contract.services'
  ])
    .constant('settings', {
      classNamePrefix: 'hrjc-',
      contactId: CRM.jobContractTabApp.contactId,
      debug: +CRM.debug,
      pathApp: CRM.jobContractTabApp.path,
      pathFile: CRM.url('civicrm/hrjobcontract/file/'),
      pathReport: CRM.url('civicrm/report/hrjobcontract/summary'),
      pathRest: CRM.url('civicrm/ajax/rest'),
      pathTpl: CRM.jobContractTabApp.path + 'views/',
      CRM: {
        options: CRM.FieldOptions || {},
        defaultCurrency: CRM.jobContractTabApp.defaultCurrency,
        apiTsFmt: 'YYYY-MM-DD HH:mm:ss',
        fields: CRM.jobContractTabApp.fields,
        maxFileSize: CRM.jobContractTabApp.maxFileSize
      }
    })
    .config(['settings', '$routeProvider', '$resourceProvider', '$logProvider', '$httpProvider', 'uibDatepickerConfig', 'uiSelectConfig',
      function (settings, $routeProvider, $resourceProvider, $logProvider, $httpProvider, datepickerConfig, uiSelectConfig) {
        $logProvider.debugEnabled(settings.debug);

        $routeProvider
          .resolveForAll({
            format: ['DateFormat', function (DateFormat) {
              return DateFormat.getDateFormat();
            }]
          })
          .when('/', {
            controller: 'ContractListCtrl',
            templateUrl: settings.pathApp + 'views/contractList.html',
            resolve: {
              contractList: ['ContractService', function (ContractService) {
                return ContractService.get();
              }]
            }
          }
          )
          .otherwise({ redirectTo: '/' });

        $resourceProvider.defaults.stripTrailingSlashes = false;
        $httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        uiSelectConfig.theme = 'bootstrap';
        datepickerConfig.showWeeks = false;
      }
    ])
    .run(['settings', '$rootScope', '$q', '$log', 'ContractService', 'ContractDetailsService', 'ContractHourService',
      'ContractPayService', 'ContractLeaveService', 'ContractHealthService', 'ContractPensionService',
      function (settings, $rootScope, $q, $log, ContractService, ContractDetailsService, ContractHourService, ContractPayService,
        ContractLeaveService, ContractHealthService, ContractPensionService) {
        $log.debug('app.run');

        $rootScope.pathTpl = settings.pathTpl;
        $rootScope.prefix = settings.classNamePrefix;

        $q.all({
          contract: ContractService.getRevisionOptions(),
          details: ContractDetailsService.getOptions(),
          hour: ContractHourService.getOptions(),
          pay: ContractPayService.getOptions(),
          leave: ContractLeaveService.getOptions(),
          health: ContractHealthService.getOptions(),
          pension: ContractPensionService.getOptions()
        }).then(function (results) {
          results.pay.pay_is_auto_est = ['No', 'Yes'];
          results.pension.is_enrolled = ['No', 'Yes', 'Opted out'];

          $log.debug('OPTIONS:');
          $log.debug(results);
          $rootScope.options = results;
        });
      }
    ]);
});
