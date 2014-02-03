// Copyright CiviCRM LLC 2013. See http://civicrm.org/licensing
CRM.HRAbsenceApp.module('Models', function(Models, HRAbsenceApp, Backbone, Marionette, $, _) {
  Models.Absence = Backbone.Model.extend({
    initialize: function(options) {
      this.listenTo(this, 'change:activity_date_time', this.calculatePeriodId);
    },
    isInPeriod: function(period) {
      var actdate = CRM.HRAbsenceApp.moment(this.get('activity_date_time'));
      if (actdate.isBefore(CRM.HRAbsenceApp.moment(period.start_date), 'day')) return false;
      if (actdate.isAfter(CRM.HRAbsenceApp.moment(period.end_date), 'day')) return false;
      return true;
    },
    calculatePeriodId: function() {
      for (period in CRM.absenceApp.periods) {
        if (this.isInPeriod(CRM.absenceApp.periods[period])) {
          this._periodId = CRM.absenceApp.periods[period].id;
          return;
        }
      }
      this._periodId = null;
      if (console.log) console.log("Failed to determine period: " + this.get('activity_date_time'));
      throw "Failed to determine period: " + this.get('activity_date_time');
    },
    getPeriodId: function() {
      if (!this._periodId) this.calculatePeriodId();
      return this._periodId;
    },
    getFormattedDuration: function() {
      if (this.get('absence_range')) {
        // FIXME: if activity_type_id is credit, +; if debit, -
        return '+/- ' + (this.get('absence_range').duration / CRM.absenceApp.standardDay).toFixed(2);
      } else {
        return '';
      }
    }
  });
  CRM.Backbone.extendModel(Models.Absence, 'Activity');

  Models.AbsenceCollection = Backbone.Collection.extend({
    model: Models.Absence,

    /**
     * Create a listing of absennce-requests, sorted by the actual dates on which
     * the absence was claimed. Note that a given absence may appear multiple times.
     *
     * @return Object keys are dates; each item is an array of AbsenceModel
     */
    createDateIndex: function() {
      var idx = {};
      this.each(function(activity) {
        _.each(activity.get('absence_range').items, function(absenceItem){
          var date = CRM.HRAbsenceApp.moment(absenceItem.activity_date_time).format('YYYY-MM-DD');
          if (!idx[date]) {
            idx[date] = [];
          }
          idx[date].push(activity);
        });
      });
      return idx;
    },

    /** @return array of type-ids (int) */
    findActiveActivityTypes: function() {
      return _.uniq(
        this.map(function(model) {
          return model.get('activity_type_id');
        })
      );
    },

    /** @return array of period-ids (int) */
    findActivePeriods: function() {
      var coll = this;
      var periodIds = [];
      _.each(CRM.absenceApp.periods, function(period) {
        for (key in coll.models) {
          if (coll.models[key].isInPeriod(period)) {
            periodIds.push(period.id);
            return;
          }
        }
      });
      return periodIds;
    },

    /** @return array of statistics */
    createStatistics: function() {
      var stats = {};
      this.each(function(model) {
        var statsKey = model.getPeriodId() + '-' + model.get('activity_type_id');
	if (!stats[statsKey]) {
	  stats[statsKey] = {
	    period_id: model.getPeriodId(),
	    activity_type_id: model.get('activity_type_id'),
	    entitlement: 0,
	    requested: 0,
	    approved: 0,
	    balance: 0
	  };
        }       
        if (model.get('status_id') == 2) {
            stats[statsKey].approved =  (parseInt(stats[statsKey].approved) + parseInt(model.get('absence_range').duration));
        } else if (model.get('status_id') == 1) {
	    var s1 = stats[statsKey].requested;
	    var s2 = model.get('absence_range').duration;
          stats[statsKey].requested =  (parseInt(stats[statsKey].requested) + parseInt(model.get('absence_range').duration));
        }
      });
      return stats;
    }
  });
  CRM.Backbone.extendCollection(Models.AbsenceCollection);

  Models.AbsenceType = Backbone.Model.extend({});
  CRM.Backbone.extendModel(Models.AbsenceType, 'HRAbsenceType');
  Models.AbsenceTypeCollection = Backbone.Collection.extend({ 
    model: Models.AbsenceType,
    getAbsenceTypes: function() {
      var idx = {};
	var i = 1;
      this.each(function(model) {
        if (model.get('allow_debits') == 1) {
	  idx[model.get('debit_activity_type_id')] = model.get('id');
	} 
	if (model.get('allow_credits') == 1) {
	  idx[model.get('credit_activity_type_id')] = model.get('id');
	}
      });
      return idx;
    }
  });
  CRM.Backbone.extendCollection(Models.AbsenceTypeCollection);

  Models.Entitlement = Backbone.Model.extend({});
  CRM.Backbone.extendModel(Models.Entitlement, 'HRAbsenceEntitlement');
  Models.EntitlementCollection = Backbone.Collection.extend({ 
    model: Models.Entitlement,

    getEntitlements: function() {
      var idx = {};
      this.each(function(model) {
        var activity = model.get('amount');
        var actType = model.get('type_id');
	var pid = model.get('period_id');
        if (!idx[actType]) {
          idx[actType] = [];
        }
        if (!idx[actType][pid]) {
          idx[actType][pid] = [];
        }
        idx[actType][pid] = activity;
      });
      return idx;
    }
  });
  CRM.Backbone.extendCollection(Models.EntitlementCollection);

  /**
   * A set of modifiable/displayable filter criteria which is
   * used to create a collection. The collection's crmCriteria
   * are kept in sync with the filter criteria.
   *
   * @type {*}
   */
  Models.AbsenceCriteria = Backbone.Model.extend({
    defaults: {
      //activity_type_id: int or array(int); optional
      //activity_type_id: 3,

      // period_id: int or array(int); optional
      period_id: _.last(_.keys(CRM.absenceApp.periods)),

      target_contact_id: CRM.absenceApp.contactId,

      // What's a good upper-limit? Typical year probably has 1-20 activities,
      // so 10-year history might have 200 records. Double and add a little
      // more.
      options: {
        limit: 500
      }
    }
  });

  /**
   * A set of modifiable/displayable filter criteria which is
   * used to create a collection. The collection's crmCriteria
   * are kept in sync with the filter criteria.
   *
   * @type {*}
   */
  Models.EntitlementCriteria = Backbone.Model.extend({
    defaults: {
      contact_id: CRM.absenceApp.contactId,
      options: {
        limit: 500
      }
    }
  });

});