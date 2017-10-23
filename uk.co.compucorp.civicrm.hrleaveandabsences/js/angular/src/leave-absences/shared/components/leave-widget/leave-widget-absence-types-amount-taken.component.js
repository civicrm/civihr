/* eslint-env amd */

define([
  'common/lodash',
  'common/moment',
  'leave-absences/shared/modules/components',
  'leave-absences/shared/components/leave-requests-heatmap.component',
  'leave-absences/shared/models/leave-request.model'
], function (_, moment, components) {
  components.component('leaveWidgetAbsenceTypesAmountTaken', {
    bindings: {
      title: '@',
      absenceTypes: '<',
      contactId: '<',
      absencePeriod: '<',
      leaveRequestStatuses: '<'
    },
    controller: absenceTypesTakenController,
    controllerAs: 'absenceTypesTaken',
    templateUrl: ['shared-settings', function (sharedSettings) {
      return sharedSettings.sharedPathTpl + 'components/leave-widget/leave-widget-absence-types-amount-taken.html';
    }]
  });

  absenceTypesTakenController.$include = ['$scope', 'LeaveRequest',
    'OptionGroup'];

  function absenceTypesTakenController ($scope, LeaveRequest, OptionGroup) {
    var childComponentName = 'leave-wdiget-absence-types-amount-taken';
    var vm = this;

    vm.leaveRequests = [];

    vm.$onChanges = $onChanges;

    /**
     * Initializes the controller by emiting a child is loading event.
     */
    (function init () {
      $scope.$emit('LeaveWidget::childIsLoading', childComponentName);
    })();

    /**
     * Implements the $onChanges method for angular controllers. When bindings
     * are ready for use, it loads all dependencies.
     */
    function $onChanges () {
      if (areBindingsReady()) {
        loadDependencies();
      }
    }

    /**
     * Returns true when the bindings are ready to be used.
     *
     * @return {Boolean}
     */
    function areBindingsReady () {
      return vm.absenceTypes && vm.contactId && vm.absencePeriod &&
        vm.leaveRequestStatuses && vm.leaveRequestStatuses.length;
    }

    /**
     * Loads the status ids for absence types and leave requests for the contact.
     * After loading the dependencies, it emits a child is ready event.
     *
     * @return {Promise}
     */
    function loadDependencies () {
      return loadLeaveRequests()
        .finally(function () {
          $scope.$emit('LeaveWidget::childIsReady', childComponentName);
        });
    }

    /**
     * Loads all leave requests for the contact, in the current period, of the
     * allowed statuses, and of the specific absence types.
     *
     * @return {Promise}
     */
    function loadLeaveRequests () {
      var absenceTypeIds = vm.absenceTypes.map(function (absenceType) {
        return absenceType.id;
      });
      var statusIds = vm.leaveRequestStatuses.map(function (status) {
        return status.value;
      });

      return LeaveRequest.all({
        contact_id: vm.contactId,
        from_date: { '>=': vm.absencePeriod.start_date },
        to_date: { '<=': vm.absencePeriod.end_date },
        status_id: { IN: statusIds },
        type_id: { IN: absenceTypeIds }
      })
      .then(function (response) {
        vm.leaveRequests = response.list;

        mapAbsenceTypesBalance();
      });
    }

    /**
     * Finds and stores the balance for each absence type.
     */
    function mapAbsenceTypesBalance () {
      vm.absenceTypes = vm.absenceTypes.map(function (absenceType) {
        var balance;

        balance = vm.leaveRequests.filter(function (request) {
          return +request.type_id === +absenceType.id;
        })
        .reduce(function (balance, request) {
          return balance + request.balance_change;
        }, 0);
        balance = Math.abs(balance);

        return _.assign({ balance: balance }, absenceType);
      });
    }
  }
});