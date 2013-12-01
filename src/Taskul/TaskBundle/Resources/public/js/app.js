var app = app || {};

$(function() {

    Backbone.Model.prototype.sync = function(method, model, options) {
        if (model.methodUrl && model.methodUrl(method.toLowerCase())) {
            options = options || {};
            options.url = model.methodUrl(method.toLowerCase());
        }
        Backbone.sync(method, model, options);
    };


    new app.PeriodsView( window.Data.periods );
});