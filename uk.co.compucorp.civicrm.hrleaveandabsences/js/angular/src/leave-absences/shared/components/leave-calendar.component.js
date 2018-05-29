/* eslint-env amd */

define([
  'common/lodash',
  'common/moment',
  'leave-absences/shared/modules/components',
  'leave-absences/shared/controllers/sub-controllers/leave-calendar-admin.controller',
  'leave-absences/shared/controllers/sub-controllers/leave-calendar-manager.controller',
  'leave-absences/shared/controllers/sub-controllers/leave-calendar-staff.controller'
], function (_, moment, components) {
  components.component('leaveCalendar', {
    bindings: {
      contactId: '<',
      roleOverride: '@?'
    },
    templateUrl: ['shared-settings', function (sharedSettings) {
      return sharedSettings.sharedPathTpl + 'components/leave-calendar.html';
    }],
    controllerAs: 'calendar',
    controller: ['$controller', '$q', '$log', '$rootScope',
      'shared-settings', 'AbsencePeriod', 'AbsenceType',
      'PublicHoliday', 'OptionGroup', 'checkPermissions',
      controller]
  });

  function controller ($controller, $q, $log, $rootScope, sharedSettings, AbsencePeriod, AbsenceType, PublicHoliday, OptionGroup, checkPermissions) {
    $log.debug('Component: leave-calendar');

    var subController, userRole;
    var vm = this;

    vm.absencePeriods = [];
    vm.contacts = [];
    vm.contactIdsToReduceTo = null;
    vm.injectMonth = false;
    vm.months = [];
    vm.selectedMonth = {};
    vm.selectedMonthIndex = '';
    vm.selectedPeriod = null;
    vm.showContactName = false;
    vm.showFilters = false;
    vm.supportData = {};
    vm.loading = { calendar: true, page: true };
    vm.filters = {
      optionValues: {},
      userSettings: {
        contact: null,
        contacts_with_leaves: true,
        department: null,
        level_type: null,
        location: null,
        region: null
      }
    };
    vm.monthPaginatorsAvailability = {
      previous: true,
      next: true
    };

    vm.labelPeriod = labelPeriod;
    vm.paginateMonth = paginateMonth;
    vm.refresh = refresh;
    vm.selectCurrentMonth = selectCurrentMonth;

    (function init () {
      setUserRole()
        .then(initWatchers)
        .then(injectSubController)
        .then(makeSureMonthIsNotInjected)
        .then(loadAbsencePeriods)
        .then(function () {
          return $q.all([
            loadContacts(),
            loadSupportData()
          ]);
        })
        .then(function () {
          return vm.showFilters ? loadFiltersOptionValues() : _.noop;
        })
        .then(function () {
          injectAndShowMonth();
          setMonthPaginatorsAvailability();
        })
        .then(function () {
          vm.loading.page = false;
        });
    }());

    /**
     * Creates a list of all the months in the currently selected period
     */
    function buildPeriodMonthsList () {
      var months = [];
      var pointerDate = moment(vm.selectedPeriod.start_date).clone().startOf('month');
      var limitDate = moment(vm.selectedPeriod.end_date).clone().endOf('month');

      while (pointerDate.isBefore(limitDate)) {
        months.push(monthStructure(pointerDate));
        pointerDate.add(1, 'month');
      }

      vm.months = months;
    }

    /**
     * Returns a month index in the format "YYYY-<month_number>"
     *
     * @param  {Moment} dateMoment
     * @return {String}
     */
    function getMonthIndex (dateMoment) {
      return dateMoment.year() + '-' + dateMoment.month();
    }

    /**
     * Initializes the scope properties' watchers
     */
    function initWatchers () {
      $rootScope.$new().$watch(function () {
        return vm.selectedMonthIndex;
      }, function (newValue, oldValue) {
        if (oldValue !== null && newValue !== oldValue) {
          setSelectedMonth();
          setMonthPaginatorsAvailability();
          sendShowMonthSignal();
        }
      });
    }

    /**
     * Injects the leave-calendar-month component and sends the "show month" signal
     *
     * @param {Boolean} forceDataReload whether the month needs a force data reload
     */
    function injectAndShowMonth (forceDataReload) {
      vm.injectMonth = true;

      waitUntilMonthIs('injected').then(function () {
        sendShowMonthSignal(forceDataReload);
      }).then(function () {
        vm.loading.calendar = false;
      });
    }

    /**
     * Injects the calendar sub controller specific for the role of the current user
     */
    function injectSubController () {
      subController = $controller('LeaveCalendar' + _.capitalize(userRole) + 'Controller').init(vm);
    }

    /**
     * Labels the given period according to whether it's current or not
     *
     * @param  {AbsencePeriodInstance} period
     * @return {string}
     */
    function labelPeriod (period) {
      return period.current ? 'Current Period (' + period.title + ')' : period.title;
    }

    /**
     * Loads the absence periods
     *
     * @return {Promise}
     */
    function loadAbsencePeriods () {
      return AbsencePeriod.all()
        .then(function (absencePeriods) {
          vm.absencePeriods = _.sortBy(absencePeriods, 'start_date');
          vm.selectedPeriod = _.find(vm.absencePeriods, function (period) {
            return !!period.current;
          });
        })
        .then(buildPeriodMonthsList)
        .then(setCurrentMonth);
    }

    /**
     * Loads the active absence types
     *
     * @return {Promise}
     */
    function loadAbsenceTypes () {
      return AbsenceType.all()
        .then(AbsenceType.loadCalculationUnits);
    }

    /**
     * Loads the OptionValues necessary for basic functioning of the controller
     *
     * @return {Promise}
     */
    function loadBasicOptionValues () {
      return OptionGroup.valuesOf([
        'hrleaveandabsences_absence_type_calculation_unit',
        'hrleaveandabsences_leave_request_day_type',
        'hrleaveandabsences_leave_request_status',
        'hrleaveandabsences_toil_amounts'
      ]);
    }

    /**
     * Loads the contacts by using the `loadContacts` method in the sub-controller
     *
     * @return {Promise}
     */
    function loadContacts () {
      return subController.loadContacts().then(function (contacts) {
        vm.contacts = contacts;
      });
    }

    /**
     * Loads the OptionValues necessary for the filters
     *
     * @return {Promise}
     */
    function loadFiltersOptionValues () {
      return OptionGroup.valuesOf([
        'hrjc_region',
        'hrjc_location',
        'hrjc_level_type',
        'hrjc_department'
      ]).then(function (data) {
        vm.filters.optionValues.regions = data.hrjc_region;
        vm.filters.optionValues.locations = data.hrjc_location;
        vm.filters.optionValues.levelTypes = data.hrjc_level_type;
        vm.filters.optionValues.departments = data.hrjc_department;
      });
    }

    /**
     * Loads all the public holidays
     *
     * @return {Promise}
     */
    function loadPublicHolidays () {
      return PublicHoliday.all();
    }

    /**
     * Loads all the data needed for the child components
     *
     * @return {Promise}
     */
    function loadSupportData () {
      return $q.all([
        loadAbsenceTypes(),
        loadPublicHolidays(),
        loadBasicOptionValues()
      ]).then(function (results) {
        vm.supportData.absenceTypes = results[0];
        vm.supportData.publicHolidays = results[1];
        vm.supportData.calculationUnits = results[2].hrleaveandabsences_absence_type_calculation_unit;
        vm.supportData.dayTypes = results[2].hrleaveandabsences_leave_request_day_type;
        vm.supportData.leaveRequestStatuses = results[2].hrleaveandabsences_leave_request_status;
        vm.supportData.toilAmounts = _.indexBy(results[2].hrleaveandabsences_toil_amounts, 'value');
      });
    }

    /**
     * If a month is already injected, it removes it and then waits
     * for its component to confirm that it is destroyed
     *
     * @return {Promise}
     */
    function makeSureMonthIsNotInjected () {
      var promise = $q.resolve();

      if (vm.injectMonth) {
        promise = waitUntilMonthIs('destroyed');
        vm.injectMonth = false;
      }

      return promise;
    }

    /**
     * Returns the structure of the month of the given date
     *
     * @param  {Moment} dateMoment
     * @return {Object}
     */
    function monthStructure (dateMoment) {
      return {
        index: getMonthIndex(dateMoment),
        month: dateMoment.month(),
        year: dateMoment.year(),
        name: dateMoment.format('MMMM'),
        moment: moment().year(dateMoment.year()).month(dateMoment.month())
      };
    }

    /**
     * Paginates the currently selected month in a specified direction
     *
     * @param {String} direction previous|next
     */
    function paginateMonth (direction) {
      var monthAction = direction === 'previous' ? 'subtract' : 'add';
      var dateFromMonth = vm.selectedMonth.moment[monthAction](1, 'month');

      vm.selectedMonthIndex = getMonthIndex(dateFromMonth);

      setSelectedMonth();
      setMonthPaginatorsAvailability();
      refresh('month');
    }

    /**
     * Reloads the selected month's data
     *
     * If the source of the refresh is a period change, then
     * it rebuilds the months list as well
     * If the source of the refresh is a change in contacts filters, then
     * it reloads the contacts as well
     *
     * @param {String} source The source of the refresh (period or contacts change)
     */
    function refresh (source) {
      source = _.includes(['contacts', 'period', 'month'], source) ? source : 'period';

      $q.resolve()
        .then(makeSureMonthIsNotInjected)
        .then(source === 'period' ? buildPeriodMonthsList : _.noop)
        .then(source === 'period' ? setFirstPeriodMonth : _.noop)
        .then(source === 'contacts' ? loadContacts : _.noop)
        .then(source === 'month' ? setMonthPaginatorsAvailability : _.noop)
        .then(function () {
          injectAndShowMonth((source === 'contacts'));
        });
    }

    function selectCurrentMonth () {
      var selectedPeriodId = vm.selectedPeriod.id;

      vm.selectedPeriod = _.find(vm.absencePeriods, function (period) {
        return !!period.current;
      });

      (selectedPeriodId !== vm.selectedPeriod.id) && buildPeriodMonthsList();
      setCurrentMonth();
      setMonthPaginatorsAvailability();
      refresh('month');
    }

    /**
     * Sends the "show" signal to the leave-calendar-month components
     *
     * @param {Boolean} forceDataReload if true, then a month will load its data
     *   regardless if it had already loaded it
     */
    function sendShowMonthSignal (forceDataReload) {
      $rootScope.$emit('LeaveCalendar::showMonth', !!forceDataReload);
    }

    /**
     * Sets the month that is to be selected by default
     */
    function setCurrentMonth () {
      vm.selectedMonthIndex = moment().year() + '-' + moment().month();

      setSelectedMonth();
    }

    /**
     * Sets the first month from the currently selected period as the selected month
     */
    function setFirstPeriodMonth () {
      vm.selectedMonthIndex = vm.months[0].index;

      setSelectedMonth();
    }

    /**
     * Enables or disables the month paginator of a specified direction.
     * It disables the paginator if there are no months to paginate to.
     *
     * @param {String} direction previous|next
     */
    function setMonthPaginatorAvailability (direction) {
      var edgeMonthSelector = direction === 'previous' ? 'first' : 'last';
      var edgeMonth = _[edgeMonthSelector](vm.months);
      var edgeMonthMoment = moment().year(edgeMonth.year).month(edgeMonth.month);

      vm.monthPaginatorsAvailability[direction] =
        !vm.selectedMonth.moment.isSame(edgeMonthMoment, 'month');
    }

    /**
     * Enables or disables the month paginators
     */
    function setMonthPaginatorsAvailability () {
      setMonthPaginatorAvailability('previous');
      setMonthPaginatorAvailability('next');
    }

    /**
     * Sets the month that is was selected
     */
    function setSelectedMonth () {
      vm.selectedMonth = _.find(vm.months, { index: vm.selectedMonthIndex });
    }

    /**
     * Sets the user's role based on his permissions
     *
     * @return {Promise}
     */
    function setUserRole () {
      if (vm.roleOverride) {
        return $q.resolve().then(function () {
          userRole = vm.roleOverride;
        });
      } else {
        return $q.all([
          checkPermissions(sharedSettings.permissions.admin.administer),
          checkPermissions(sharedSettings.permissions.ssp.manage)
        ]).then(function (results) {
          userRole = results[0] ? 'admin' : (results[1] ? 'manager' : 'staff');
        });
      }
    }

    /**
     * Waits until all leave-calendar-month components are <some status>
     *
     * @return {Promise}
     */
    function waitUntilMonthIs (status) {
      return $q(function (resolve) {
        var removeListener = $rootScope.$on('LeaveCalendar::month' + _.capitalize(status), function () {
          removeListener();
          resolve();
        });
      });
    }
  }
});
