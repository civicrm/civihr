/* eslint-env amd */

define([
  'common/lodash',
  'leave-absences/manager-notification-badge/modules/components'
], function (_, components) {
  components.component('managerNotificationBadge', {
    templateUrl: ['settings', function (settings) {
      return settings.pathTpl + 'components/manager-notification-badge.html';
    }],
    controllerAs: 'managerNotificationBadge',
    controller: ManagerNotificationBadgeController
  });

  ManagerNotificationBadgeController.$inject = ['$log', '$q', 'Session', 'OptionGroup', 'shared-settings'];

  function ManagerNotificationBadgeController ($log, $q, Session, OptionGroup, sharedSettings) {
    $log.debug('Component: manager-notification-badge');

    var vm = this;
    var leaveRequestFilters = {
      apiName: 'LeaveRequest',
      params: {}
    };

    vm.refreshCountEventName = 'ManagerBadge:: Update Count';

    (function init () {
      $q.all([
        getManagerId(),
        getStatusId()
      ]).then(function () {
        vm.filters = [leaveRequestFilters];
      });
    })();

    /**
     * Get the logged in contact id and save it as manager id
     *
     * @returns {Promise}
     */
    function getManagerId () {
      return Session.get()
        .then(function (session) {
          leaveRequestFilters.params.managed_by = session.contactId;
        });
    }

    /**
     * Get the status id for awaiting approval status
     *
     * @return {Promise}
     */
    function getStatusId () {
      return loadStatuses()
        .then(function (leaveRequestStatuses) {
          leaveRequestFilters.params.status_id = _.find(leaveRequestStatuses, function (status) {
            return status.name === sharedSettings.statusNames.awaitingApproval;
          }).value;
        });
    }

    /**
     * Loads all the leave request statuses
     *
     * @return {Promise}
     */
    function loadStatuses () {
      return OptionGroup.valuesOf('hrleaveandabsences_leave_request_status');
    }
  }
});
