var app = app || {};

app.Periods = Backbone.Collection.extend({
    model: app.Period,

    url: window.Api.get_periods,

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
    }
});