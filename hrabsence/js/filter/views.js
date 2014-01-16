// Copyright CiviCRM LLC 2013. See http://civicrm.org/licensing
CRM.HRAbsenceApp.module('Filter', function(Filter, HRAbsenceApp, Backbone, Marionette, $, _) {
  Filter.FilterView = Marionette.ItemView.extend({
    template: '#hrabsence-filters-template',
    templateHelpers: function() {
      return {
        // 'RenderUtil': CRM.HRAbsenceApp.RenderUtil,
        'FieldOptions': {
          'activity_type_id': CRM.absenceApp.activityTypes,
          'period_id': CRM.absenceApp.periods
        }
      };
    },
    events: {
      "change [name=activity_type_id]": function(e) {
        // TODO: allow multiple values
        if ($(e.currentTarget).val()) {
          this.model.set('activity_type_id', $(e.currentTarget).val())
        } else {
          this.model.unset('activity_type_id');
        }
      },
      "change [name=period_id]": function(e) {
        // TODO: allow multiple values
        if ($(e.currentTarget).val()) {
          this.model.set('period_id', $(e.currentTarget).val())
        } else {
          this.model.unset('period_id');
        }
      }

    },
    onRender: function() {
      // TODO: allow multiple values
      this.$('[name=activity_type_id]').val(this.model.get('activity_type_id'));

      // TODO: allow multiple values
      this.$('[name=period_id]').val(this.model.get('period_id'));
    }
  });
});