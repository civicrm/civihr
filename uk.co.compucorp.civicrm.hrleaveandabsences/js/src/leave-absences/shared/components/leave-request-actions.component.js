/* eslint-env amd */

define([
  'common/lodash',
  'common/moment',
  'leave-absences/shared/modules/components',
  'common/models/contact',
  'common/models/session.model',
  'common/services/hr-settings',
  'common/services/notification.service',
  'common/services/pub-sub',
  'leave-absences/shared/services/leave-request.service'
], function (_, moment, components) {
  components.component('leaveRequestActions', {
    bindings: {
      leaveRequest: '<',
      leaveRequestStatuses: '<',
      absenceTypes: '<',
      /**
       * Role is not a permission level in this case.
       * For example Manager can act as Staff
       * and Admin can act as either Manager or Staff.
       */
      role: '<'
    },
    templateUrl: ['shared-settings', function (sharedSettings) {
      return sharedSettings.sharedPathTpl + 'components/leave-request-actions.html';
    }],
    controllerAs: 'actions',
    controller: LeaveRequestActionsController
  });

  LeaveRequestActionsController.$inject = ['$log', '$q', '$rootScope', 'Contact',
    'dialog', 'LeavePopup', 'LeaveRequestService', 'pubSub', 'shared-settings',
    'notificationService', 'Session'];

  function LeaveRequestActionsController ($log, $q, $rootScope, Contact, dialog,
    LeavePopup, LeaveRequestService, pubSub, sharedSettings, notification, Session) {
    $log.debug('Component: leave-request-action-dropdown');

    var currentlyLoggedInContactId;
    var vm = this;
    var statusIdBeforeAction;
    var statusNames = sharedSettings.statusNames;
    var actions = {
      edit: {
        label: 'Edit',
        allowedStatuses: [statusNames.awaitingApproval]
      },
      respond: {
        label: 'Respond',
        allowedStatuses: [statusNames.moreInformationRequired]
      },
      view: {
        label: 'View',
        allowedStatuses: [
          statusNames.approved,
          statusNames.rejected,
          statusNames.cancelled
        ]
      },
      approve: {
        label: 'Approve',
        isDirectAction: true,
        allowedStatuses: [statusNames.awaitingApproval],
        dialog: {
          title: 'Approval',
          btnClass: 'success',
          btnLabel: 'Approve',
          msg: 'Please confirm approval'
        }
      },
      reject: {
        label: 'Reject',
        isDirectAction: true,
        allowedStatuses: [statusNames.awaitingApproval],
        dialog: {
          title: 'Rejection',
          btnClass: 'warning',
          btnLabel: 'Reject',
          msg: 'Please confirm rejection'
        }
      },
      cancel: {
        label: 'Cancel',
        isDirectAction: true,
        allowedStatuses: [
          statusNames.awaitingApproval,
          statusNames.approved,
          statusNames.rejected,
          statusNames.moreInformationRequired
        ],
        dialog: {
          title: 'Cancellation',
          btnClass: 'danger',
          btnLabel: 'Confirm',
          msg: 'Please confirm cancellation'
        }
      },
      delete: {
        label: 'Delete',
        isDirectAction: true,
        allowedStatuses: [
          statusNames.awaitingApproval,
          statusNames.moreInformationRequired,
          statusNames.approved,
          statusNames.rejected,
          statusNames.cancelled
        ],
        dialog: {
          title: 'Deletion',
          btnClass: 'danger',
          btnLabel: 'Confirm',
          msg: 'This cannot be undone'
        }
      }
    };
    var actionsToStatusesMap = {
      'approve': 'approved'
    };

    vm.allowedActions = [];
    vm.loading = { component: true };

    vm.action = action;
    vm.openLeavePopup = openLeavePopup;

    (function init () {
      $q.resolve()
        .then(indexSupportData)
        .then(loadCurrentlyLoggedInContactId)
        .then(function () {
          return checkIfOwnLeaveRequest() && setRoleToAdminIfSelfLeaveApprover();
        })
        .then(setAllowedActions)
        .finally(function () {
          vm.loading.component = false;
        });
    }());

    /**
     * Performs an action on a given leave request
     *
     * @param {String} action
     */
    function action (action) {
      statusIdBeforeAction = vm.leaveRequest.status_id;

      if (!_.includes(['cancel', 'reject', 'delete'], action) &&
        vm.leaveRequest.request_type !== 'toil') {
        checkBalanceChangeAndPromptForAnAction(action);
      } else {
        dialog.open(getConfirmationDialogOptions(action));
      }
    }

    /**
     * Returns true if the user is an admin and the leave request is a public
     * holiday. This allows admins to delete public holiday leave requests which
     * are automatically generated by the system.
     *
     * @return {Boolean}
     */
    function canDeletePublicHolidayLeaveRequests () {
      return vm.role === 'admin' && vm.leaveRequest.request_type === 'public_holiday';
    }

    /**
     * Checks if the given leave request can be cancelled
     *
     * @TODO This function utilises external resource
     * vm.absenceTypes - this sould be refactored
     *
     * @return {Boolean}
     */
    function canLeaveRequestBeCancelled (leaveRequestStatus) {
      var allowCancellationValue = vm.absenceTypes[vm.leaveRequest.type_id].allow_request_cancelation;

      // Admin can always cancel
      if (vm.role === 'admin') {
        return true;
      }

      // Manager can cancel regardless of the allow_request_cancelation value,
      // but only if the request is either in "Awaiting for Approval" or "More Information Required"
      if (vm.role === 'manager') {
        return _.includes([statusNames.awaitingApproval, statusNames.moreInformationRequired],
          leaveRequestStatus);
      }

      // If request can only be cancelled in advance of start date
      if (allowCancellationValue === '3') {
        return moment().isBefore(vm.leaveRequest.from_date);
      }

      // If request can always be cancelled
      return allowCancellationValue === '2';
    }

    /**
     * Opens dialog and immediately starts checking balance change
     * If balance changed, prompts to recalculate the balance change,
     * if not - simply asks for the action confirmation
     *
     * @param {String} action
     */
    function checkBalanceChangeAndPromptForAnAction (action) {
      dialog.open({
        title: 'Verifying balance...',
        loading: true,
        optionsPromise: function () {
          return vm.leaveRequest.checkIfBalanceChangeNeedsRecalculation()
            .then(function (balanceChangeNeedsRecalculation) {
              if (balanceChangeNeedsRecalculation) {
                return _.assign(
                  LeaveRequestService.getBalanceChangeRecalculationPromptOptions(),
                  {
                    onCloseAfterConfirm: function () {
                      openLeavePopupForAction(action);
                    }
                  }
                );
              } else {
                return getConfirmationDialogOptions(action);
              }
            });
        }
      });
    }

    /**
     * Checks if the currently logged in user is a leave approver
     *
     * @return {Promise}
     */
    function checkIfContactIsSelfLeaveApprover () {
      return Contact.find(currentlyLoggedInContactId)
        .then(function (currentlyLoggedInContact) {
          return currentlyLoggedInContact.checkIfSelfLeaveApprover();
        });
    }

    /**
     * Checks if the leave request is own
     *
     * @return {Boolean}
     */
    function checkIfOwnLeaveRequest () {
      return currentlyLoggedInContactId === vm.leaveRequest.contact_id;
    }

    /**
     * Indexes leave request statuses and absence types
     * if they are passed as arrays to the component
     */
    function indexSupportData () {
      if (_.isArray(vm.leaveRequestStatuses)) {
        vm.leaveRequestStatuses = _.indexBy(vm.leaveRequestStatuses, 'value');
      }

      if (_.isArray(vm.absenceTypes)) {
        vm.absenceTypes = _.indexBy(vm.absenceTypes, 'id');
      }
    }

    /**
     * Returns options for an action confirmation dialog
     *
     * @param  {String} action
     * @return {Object}
     */
    function getConfirmationDialogOptions (action) {
      var dialogParams = actions[action].dialog;

      return {
        title: 'Confirm ' + dialogParams.title + '?',
        copyCancel: 'Cancel',
        copyConfirm: dialogParams.btnLabel,
        classConfirm: 'btn-' + dialogParams.btnClass,
        msg: dialogParams.msg,
        onConfirm: function () {
          return vm.leaveRequest[action]()
            .then(function () {
              publishEvents(action);
            })
            .catch(function (error) {
              notification.error('Error:', error);
            });
        }
      };
    }

    /**
     * Loads the ID of the currently logged in contact
     *
     * @return {Promise}
     */
    function loadCurrentlyLoggedInContactId () {
      return Session.get()
        .then(function (session) {
          currentlyLoggedInContactId = session.contactId;
        });
    }

    /**
     * Opens the leave request popup
     *
     * When the component appears inside other elements
     * which also having click events, event.stopPropagation() is necessary
     * to prevent the click events of parent elements from being called
     *
     * @param {Object} params
     * @see LeavePopup.openModal for the reference to the `params` argument
     */
    function openLeavePopup (event, params) {
      event.stopPropagation();
      LeavePopup.openModal(params);
    }

    /**
     * Opens a leave popup for a specific action
     *
     * @param {String} action
     */
    function openLeavePopupForAction (action) {
      LeavePopup.openModal({
        leaveRequest: vm.leaveRequest,
        leaveType: vm.leaveRequest.request_type,
        selectedContactId: vm.leaveRequest.contact_id,
        forceRecalculateBalanceChange: true,
        defaultStatus: sharedSettings.statusNames[actionsToStatusesMap[action]]
      });
    }

    /**
     * Publish events
     *
     * @param {String} action
     */
    function publishEvents (action) {
      var awaitingApprovalStatusValue = _.find(vm.leaveRequestStatuses, function (status) {
        return status.name === sharedSettings.statusNames.awaitingApproval;
      }).value;

      // Check if the status was "Awaiting Approval" before the action
      if (statusIdBeforeAction === awaitingApprovalStatusValue) {
        pubSub.publish('ManagerBadge:: Update Count');
      }

      pubSub.publish('LeaveRequest::statusUpdate', {
        status: action,
        leaveRequest: vm.leaveRequest
      });
    }

    /**
     * @TODO This function utilises external resources:
     * vm.leaveRequestStatuses - this sould be refactored
     *
     * Sets actions that can be performed within the
     * leave request basing on its status and user role
     */
    function setAllowedActions () {
      var leaveRequestStatus = vm.leaveRequestStatuses[vm.leaveRequest.status_id].name;
      var allowedActions = _.compact(_.map(actions, function (action, actionKey) {
        return _.includes(action.allowedStatuses, leaveRequestStatus) ? actionKey : null;
      }));

      (!canLeaveRequestBeCancelled(leaveRequestStatus)) && _.pull(allowedActions, 'cancel');
      (vm.role !== 'admin') && _.pull(allowedActions, 'delete');
      (vm.role === 'staff') && _.pull(allowedActions, 'approve', 'reject');
      (vm.role !== 'staff') && swapViewEditAndRespondActions(allowedActions);
      canDeletePublicHolidayLeaveRequests() && allowedActions.push('delete');

      vm.allowedActions = _.map(allowedActions, function (action) {
        return {
          key: action,
          label: actions[action].label,
          isDirectAction: actions[action].isDirectAction
        };
      });
    }

    /**
     * Checks if the contact is a self leave approver and, if true,
     * sets the role to "admin"
     *
     * @return {Promise}
     */
    function setRoleToAdminIfSelfLeaveApprover () {
      return checkIfContactIsSelfLeaveApprover()
        .then(function (isSelfLeaveApprover) {
          if (isSelfLeaveApprover) {
            vm.role = 'admin';
          }
        });
    }

    /**
     * Swaps Edit and Respond actions in allowed actions list
     *
     * @param {Array} actions
     */
    function swapViewEditAndRespondActions (actions) {
      _.each(actions, function (action, actionKey) {
        (action === 'edit') && (actions[actionKey] = 'respond');
        (_.includes(['respond', 'view'], action)) && (actions[actionKey] = 'edit');
      });
    }
  }
});
