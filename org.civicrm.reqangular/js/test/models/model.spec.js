/* eslint-env amd, jasmine */

define([
  'common/lodash',
  'common/angularMocks',
  'common/models/model'
], function (_) {
  'use strict';

  describe('Model', function () {
    var Model;
    var modelInterface = ['compactFilters', 'extend', 'processFilters'];

    beforeEach(module('common.models'));
    beforeEach(inject(['Model', function (_Model_) {
      Model = _Model_;
    }]));

    it('has the expected interface', function () {
      expect(_.functions(Model)).toEqual(modelInterface);
    });

    describe('extend()', function () {
      var NewModel;

      beforeEach(function () {
        NewModel = Model.extend({
          foo: function () {},
          bar: 'bar',
          baz: function () {}
        });
      });

      it('creates a new object', function () {
        expect(NewModel).not.toBe(Model);
      });

      it('retains the same basic interface', function () {
        expect(_.functionsIn(NewModel)).toEqual(jasmine.arrayContaining(modelInterface));
      });

      it('contains the new properties', function () {
        expect(NewModel.foo).toBeDefined();
        expect(NewModel.bar).toBeDefined();
        expect(NewModel.baz).toBeDefined();
      });
    });

    describe('compactFilters()', function () {
      var filters;

      beforeEach(function () {
        filters = Model.processFilters({
          filter_1: 'a non-empty string',
          filter_2: '',
          filter_3: 456,
          filter_4: 0,
          filter_5: undefined,
          filter_6: { foo: 'foo' },
          filter_7: null,
          filter_8: {},
          filter_9: false
        });
      });

      it('removes falsy values, except for 0 and false', function () {
        expect(filters).toEqual({
          filter_1: 'a non-empty string',
          filter_3: 456,
          filter_4: 0,
          filter_6: { foo: 'foo' },
          filter_8: {},
          filter_9: false
        });
      });
    });

    describe('processFilters()', function () {
      var filters;

      describe('filter names', function () {
        describe('standard filters', function () {
          beforeEach(function () {
            filters = Model.processFilters({
              filter_1: 'foo',
              filter_2: 'bar'
            });
          });

          it('keep the names unchanged', function () {
            expect(Object.keys(filters)).toEqual(['filter_1', 'filter_2']);
          });
        });
      });

      describe('operators', function () {
        describe('standard filters', function () {
          beforeEach(function () {
            filters = Model.processFilters({
              filter_1: 'foo',
              filter_2: 'bar'
            });
          });

          it('use simple key-value pairs, without an operator', function () {
            expect(filters).toEqual({
              filter_1: 'foo',
              filter_2: 'bar'
            });
          });
        });

        describe('multiple values filters', function () {
          beforeEach(function () {
            filters = Model.processFilters({
              filter_1: { in: ['1', '2', '3', '4', '5'] },
              filter_2: { nin: ['a', 'b', 'c', 'd', 'e'] }
            });
          });

          it('uses the correct operator depending on the filter name', function () {
            expect(filters).toEqual({
              filter_1: { 'IN': ['1', '2', '3', '4', '5'] },
              filter_2: { 'NOT IN': ['a', 'b', 'c', 'd', 'e'] }
            });
          });
        });

        describe('period-related filters', function () {
          describe('when the dates are passed', function () {
            beforeEach(function () {
              filters = Model.processFilters({
                filter_1: { from: jasmine.any(String), to: jasmine.any(String) },
                filter_2: { from: jasmine.any(String) },
                filter_3: { to: jasmine.any(String) }
              });
            });

            it('build the correct filter structure', function () {
              expect(filters).toEqual({
                filter_1: { 'BETWEEN': [jasmine.any(String), jasmine.any(String)] },
                filter_2: { '>=': jasmine.any(String) },
                filter_3: { '<=': jasmine.any(String) }
              });
            });
          });

          describe('when the dates are passed in DD/MM/YYYY format', function () {
            beforeEach(function () {
              filters = Model.processFilters({
                filter_1: { from: '20/01/2006', to: '16/02/2006' },
                filter_2: { from: '31/12/2016' },
                filter_3: { to: '02/07/2016' }
              });
            });

            it('converts the dates to the expected standard format', function () {
              expect(filters).toEqual({
                filter_1: { 'BETWEEN': ['2006-01-20', '2006-02-16'] },
                filter_2: { '>=': '2016-12-31' },
                filter_3: { '<=': '2016-07-02' }
              });
            });
          });

          describe('when the dates are passed in "YYYY-MM-DD[ HH:mm[:ss]]" formats', function () {
            beforeEach(function () {
              filters = Model.processFilters({
                filter_1: { from: '2018-01-02', to: '2019-02-03' },
                filter_2: { from: '2020-03-04' },
                filter_3: { to: '2021-04-05' },
                filter_4: { from: '2018-01-02 00:00', to: '2019-02-03 01:00' },
                filter_5: { from: '2020-03-04 23:59' },
                filter_6: { to: '2021-04-05 15:18' },
                filter_7: { from: '2018-01-02 00:00:00', to: '2019-02-03 01:00:01' },
                filter_8: { from: '2020-03-04 23:59:59' },
                filter_9: { to: '2021-04-05 15:18:00' }
              });
            });

            it('converts the dates to the expected standard format', function () {
              expect(filters).toEqual({
                filter_1: { 'BETWEEN': ['2018-01-02', '2019-02-03'] },
                filter_2: { '>=': '2020-03-04' },
                filter_3: { '<=': '2021-04-05' },
                filter_4: { 'BETWEEN': ['2018-01-02 00:00', '2019-02-03 01:00'] },
                filter_5: { '>=': '2020-03-04 23:59' },
                filter_6: { '<=': '2021-04-05 15:18' },
                filter_7: { 'BETWEEN': ['2018-01-02 00:00:00', '2019-02-03 01:00:01'] },
                filter_8: { '>=': '2020-03-04 23:59:59' },
                filter_9: { '<=': '2021-04-05 15:18:00' }
              });
            });
          });
        });
      });
    });
  });
});
