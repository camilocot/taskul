var app = app || {};

app.Period = Backbone.Model.extend({

    url : function() {
      var base = window.Api.period_url;
      if (this.isNew()) return base;
      return base + (base.charAt(base.length - 1) == '/' ? '' : '/') + this.id;
    },
    defaults: {
        begin: moment().format(),
        end: moment().format(),
        note: ''
    },
    initialize: function (){
    },
    validate: function (attrs) {
        var errors = [], parseEnd, parseBegin;
        if (!attrs.begin) {
            errors.push({name: 'begin', message:'Please fill begin field.'});
        }
        else {
            parseBegin = moment(app.utils.parseDate(attrs.begin)).format("X");
        }

        if (!attrs.end) {
            errors.push({name: 'end', message: 'Please fill end field.'});
        }
        else {
            parseEnd = moment(app.utils.parseDate(attrs.end)).format("X");
        }
        if(parseBegin && parseEnd && parseBegin > parseEnd){
            errors.push({name: 'end', message: 'La fecha de finalización debe ser mayor o igual que la de inicio'});
        }
        if (!attrs.note) {
            errors.push({name: 'note', message: 'Please fill note field.'});
        }
        return errors.length > 0 ? errors : false;
    },
    toJSON: function() {
        return app.utils.parseDates(this.attributes);
    },
    parse: function (data) {
        return app.utils.parseDates(data);
    },
    save: function(attrs, options) {
        options || (options = {});
        options.success = function (model, response) {
                alert('Thanks for the period!');
            };
        options.error = function (model, error) {
                alert('Error en el guardado del perido');
            };

        Backbone.Model.prototype.save.call(this, attrs, options);
        return this;
    }

},{parse: true});
