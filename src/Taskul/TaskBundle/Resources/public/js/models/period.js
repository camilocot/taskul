var app = app || {};

app.Period = Backbone.Model.extend({
    defaults: {
        begin: moment().format(),
        end: moment().format()
    }
});
