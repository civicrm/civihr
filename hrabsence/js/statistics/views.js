// Copyright CiviCRM LLC 2013. See http://civicrm.org/licensing
CRM.HRAbsenceApp.module('Statistics', function(Statistics, HRAbsenceApp, Backbone, Marionette, $, _) {

  /**
   * A view which computes statistics about one's absences by period, type,
   * and status.
   *
   * Constructor arguments:
   *  - collection: AbsenceCollection
   *  - criteria: AbsenceCriteria
   *
   * This view is currently based on ItemView because it's just a placeholder.
   * For the final/real implementation, one might use ItemView, CompositeView,
   * CollectionView, or something else.
   *
   * @type {*}
   */
  Statistics.StatisticsView = Marionette.ItemView.extend({
    template: '#hrabsence-statistics-template',
    templateHelpers: function() {
      return {
        'FieldOptions': {
          'activity_type_id': CRM.absenceApp.activityTypes,
          'period_id': CRM.absenceApp.periods
        }
      };
    },
    initialize: function(options) {
      if (console.log) console.log('StatisticsView.initialize  with ' + options.collection.models.length + ' item(s)');
      this.listenTo(options.collection, 'reset', this.render);
    },
    onRender: function() {
      if (console.log) console.log('StatisticsView.onRender with ' + this.options.collection.models.length + ' item(s)');
      this.$('.activity-count').text(this.options.collection.models.length);
    }
  });
});