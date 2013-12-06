var app = app || {};

app.Periods = Backbone.Collection.extend({
    model: app.Period,
    sortAttribute: "begin",
    sortDirection: 1,
    url: window.Api.period_url,

    strategies: {
        begin: function (period) { return period.get("begin"); },
        end: function (period) { return period.get("end"); },
    },
    initialize: function()
    {
        this._meta = {};
    },
    meta: function(prop, value) {
        if (value === undefined) {
            return this._meta[prop];
        } else {
            this._meta[prop] = value;
        }
    },
    sortPeriods: function (attr) {
      this.sortAttribute = attr;
      this.sort();
    },

    comparator: function(a, b) {
      a = a.get(this.sortAttribute);
      b = b.get(this.sortAttribute);

      if (a == b) return 0;

      if (this.sortDirection == 1) {
         return a > b ? 1 : -1;
      } else {
         return a < b ? 1 : -1;
      }
    }

});