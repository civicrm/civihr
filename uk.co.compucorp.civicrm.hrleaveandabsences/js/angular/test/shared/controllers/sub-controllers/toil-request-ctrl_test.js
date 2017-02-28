(function (CRM) {
  define([
    'common/lodash',
    'mocks/data/option-group-mock-data',
    'mocks/data/absence-type-data',
    'mocks/data/leave-request-data',
    'common/angularMocks',
    'leave-absences/shared/config',
    'common/mocks/services/hr-settings-mock',
    'mocks/apis/absence-period-api-mock',
    'mocks/apis/absence-type-api-mock',
    'mocks/apis/entitlement-api-mock',
    'mocks/apis/work-pattern-api-mock',
    'mocks/apis/leave-request-api-mock',
    'mocks/apis/option-group-api-mock',
    'mocks/apis/public-holiday-api-mock',
    'common/mocks/services/api/contact-mock',
    'leave-absences/shared/controllers/sub-controllers/toil-request-ctrl',
    'leave-absences/shared/modules/shared-settings',
  ], function (_, optionGroupMock, absenceMockData, mockData) {
    'use strict';

    describe('ToilRequestCtrl', function () {
      var $log, $rootScope, $ctrl, modalInstanceSpy, $scope, $controller,
        $provide, AbsenceTypeAPI, TOILRequestInstance, sharedSettings,
        date2016 = '01/12/2016';

      beforeEach(module('leave-absences.templates', 'leave-absences.controllers',
        'leave-absences.mocks', 'common.mocks', 'leave-absences.settings',
        function (_$provide_) {
          $provide = _$provide_;
        }));

      beforeEach(inject(['shared-settings',
        function (_sharedSettings_) {
          sharedSettings = _sharedSettings_;
        }
      ]));

      beforeEach(inject(function (_AbsencePeriodAPIMock_, _HR_settingsMock_,
        _AbsenceTypeAPIMock_, _EntitlementAPIMock_, _WorkPatternAPIMock_,
        _LeaveRequestAPIMock_, _OptionGroupAPIMock_, _PublicHolidayAPIMock_) {
        $provide.value('AbsencePeriodAPI', _AbsencePeriodAPIMock_);
        $provide.value('AbsenceTypeAPI', _AbsenceTypeAPIMock_);
        $provide.value('EntitlementAPI', _EntitlementAPIMock_);
        $provide.value('WorkPatternAPI', _WorkPatternAPIMock_);
        $provide.value('HR_settings', _HR_settingsMock_);
        $provide.value('LeaveRequestAPI', _LeaveRequestAPIMock_);
        $provide.value('api.optionGroup', _OptionGroupAPIMock_);
        $provide.value('PublicHolidayAPI', _PublicHolidayAPIMock_);
      }));

      beforeEach(inject(function (_$log_, _$controller_, _$rootScope_,
        _AbsenceTypeAPI_, _TOILRequestInstance_) {
        $log = _$log_;
        $rootScope = _$rootScope_;
        $controller = _$controller_;
        AbsenceTypeAPI = _AbsenceTypeAPI_;
        TOILRequestInstance = _TOILRequestInstance_;
        modalInstanceSpy = jasmine.createSpyObj('modalInstanceSpy', ['dismiss', 'close']);

        spyOn($log, 'debug');
        spyOn(AbsenceTypeAPI, 'all').and.callThrough();
        spyOn(AbsenceTypeAPI, 'calculateToilExpiryDate').and.callThrough();
        spyOn(TOILRequestInstance, 'init').and.callThrough();
      }));

      describe('toil request', function () {
        var parentRequestCtrl;

        beforeEach(function () {
          var directiveOptions = {
            contactId: CRM.vars.leaveAndAbsences.contactId
          };

          initTestController(directiveOptions);
          parentRequestCtrl = $controller('RequestCtrl')
        });

        it('is called', function () {
          expect($log.debug).toHaveBeenCalled();
        });

        it('inherited from request controller', function () {
          expect($ctrl instanceof parentRequestCtrl.constructor).toBe(true);
        });

        it('has leave type set to toil', function () {
          expect($ctrl.isLeaveType('toil')).toBeTruthy();
        });

        it('calls init on toil request instance', function () {
          expect(TOILRequestInstance.init).toHaveBeenCalledWith({
            contact_id: CRM.vars.leaveAndAbsences.contactId
          });
        });

        it('loads toil amounts', function () {
          expect(Object.keys($ctrl.toilAmounts).length).toBeGreaterThan(0);
        });

        it('gets absence types with true allow_accruals_request param', function () {
          expect(AbsenceTypeAPI.all).toHaveBeenCalledWith({
            allow_accruals_request: true
          })
        });

        it('cannot submit request', function () {
          expect($ctrl.canSubmit()).toBe(false);
        });

        describe('with selected duration and dates', function () {
          beforeEach(function () {
            var toil_accrue = optionGroupMock.specificObject('hrleaveandabsences_toil_amounts', 'name', 'quarter_day');

            setTestDates(date2016, date2016);
            $ctrl.request.toilDurationHours = 1;
            $ctrl.request.updateDuration();
            $ctrl.request.toil_to_accrue = toil_accrue.value;
          });

          it('can submit request', function () {
            expect($ctrl.canSubmit()).toBe(true);
          });

          it('sets expiry date', function () {
            expect($ctrl.expiryDate).toEqual(absenceMockData.calculateToilExpiryDate().values.toil_expiry_date);
          });

          it('calls calculateToilExpiryDate on AbsenceType', function () {
            expect(AbsenceTypeAPI.calculateToilExpiryDate.calls.mostRecent().args[0]).toEqual($ctrl.request.type_id);
            expect(AbsenceTypeAPI.calculateToilExpiryDate.calls.mostRecent().args[1]).toEqual($ctrl.request.from_date);
          });

          describe('when user changes number of days selected', function () {
            beforeEach(function () {
              $ctrl.changeInNoOfDays();
            });

            it('resets toil attributes', function () {
              expect($ctrl.request.toilDurationHours).toEqual(0);
              expect($ctrl.request.toilDurationMinutes).toEqual(0);
              expect($ctrl.request.toil_to_accrue).toEqual("");
            });
          });

          describe('when submit with valid fields', function () {
            beforeEach(function () {
              spyOn($rootScope, '$emit');
              setTestDates(date2016, date2016);
              //entitlements are randomly generated so resetting them to positive here
              $ctrl.balance.closing = 1;
              $ctrl.submit();
              $scope.$digest();
            });

            it('has all required fields', function () {
              expect($ctrl.request.from_date).toBeDefined();
              expect($ctrl.request.to_date).toBeDefined();
              expect($ctrl.request.from_date_type).toBeDefined();
              expect($ctrl.request.to_date_type).toBeDefined();
              expect($ctrl.request.contact_id).toBeDefined();
              expect($ctrl.request.status_id).toBeDefined();
              expect($ctrl.request.type_id).toBeDefined();
              expect($ctrl.request.toil_duration).toBeDefined();
              expect($ctrl.request.toil_to_accrue).toBeDefined();
              expect($ctrl.request.toil_expiry_date).toBeDefined();
            });

            it('is successful', function () {
              expect($ctrl.error).toBeNull();
              expect($ctrl.request.id).toBeDefined();
            });

            it('allows user to submit', function () {
              expect($ctrl.canSubmit()).toBeTruthy();
            });

            it('sends event', function () {
              expect($rootScope.$emit).toHaveBeenCalledWith('LeaveRequest::new', $ctrl.request);
            });

            describe('and absence type allows overuse', function () {
              beforeEach(function () {
                $ctrl.updateBalance();
                $ctrl.submit();
                $scope.$digest();
              });

              it('saves without errors', function () {
                expect($ctrl.error).toBeNull();
              });
            });
          });
        });

        describe('in edit mode', function () {
          var toilRequest, absenceType;

          beforeEach(function () {
            toilRequest = TOILRequestInstance.init(mockData.findBy('request_type', 'toil'));
            toilRequest.contact_id = CRM.vars.leaveAndAbsences.contactId.toString();
            var directiveOptions = {
              contactId: toilRequest.contact_id, //owner's contact id
              leaveRequest: toilRequest
            };

            initTestController(directiveOptions);
            absenceType = _.find($ctrl.absenceTypes, function (absenceType) {
              return absenceType.id == $ctrl.request.type_id;
            });
          });

          it('sets balance', function () {
            expect($ctrl.balance.opening).toEqual(jasmine.any(Number));
          });

          it('sets absence types', function () {
            expect(absenceType.id).toEqual(toilRequest.type_id);
          });
        });
      });

      /**
       * Initialize the controller
       *
       * @param leave request
       */
      function initTestController(directiveOptions) {
        $scope = $rootScope.$new();

        $ctrl = $controller('ToilRequestCtrl', {
          $scope: $scope,
          $uibModalInstance: modalInstanceSpy,
          directiveOptions: directiveOptions
        });

        $scope.$digest();
      }

      /**
       * sets from and/or to dates
       * @param {String} from date set if passed
       * @param {String} to date set if passed
       */
      function setTestDates(from, to) {
        if (from) {
          $ctrl.uiOptions.fromDate = new Date(from);
          $ctrl.updateAbsencePeriodDatesTypes($ctrl.uiOptions.fromDate, 'from');
          $scope.$digest();
        }

        if (to) {
          $ctrl.uiOptions.toDate = new Date(to);
          $ctrl.updateAbsencePeriodDatesTypes($ctrl.uiOptions.toDate, 'to');
          $scope.$digest();
        }
      }
    });
  });
})(CRM);
