var app = app || {};

$(function() {

    Backbone.Model.prototype.sync = function(method, model, options) {
        if (model.methodUrl && model.methodUrl(method.toLowerCase())) {
            options = options || {};
            options.url = model.methodUrl(method.toLowerCase());
        }
        Backbone.sync(method, model, options);
    };

    app.config = { dateFormats: {datepicker:'dd/mm/yyyy', moment:'DD/MM/YYYY', regEx: /[0-9]{2}\/[0-9]{2}\/[0-9]{4}/ }};
    app.utils = {
        parseDates: function (data){
            var attributes = data;
            var splitDate;
            // go through each attribute
            $.each(attributes, function(key, value) {
                attributes[key] = app.utils.parseDate(value);
            });
            return attributes;
        },
        parseDate: function (date) {
                if (typeof date === 'string' && app.config.dateFormats.regEx.test(date)){
                    // execute toJSON and overwrite the date in attributes
                    splitDate = date.split('/');
                    return moment(splitDate[2]+'-'+splitDate[1]+'-'+splitDate[0]).format();
                }
                return date;
        }

    };

    $.fn.renderDatePicker = function () {
        $this = $(this);
        if(!checkMobile()){
            var nowTemp = new Date();
            var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
            var $end = $this.find('#end');
            var $begin = $this.find('#begin');

            var begin = $begin.datepicker({
                format: app.config.dateFormats.datepicker
            }).on('changeDate', function(ev) {
              if (ev.date.valueOf() > end.date.valueOf()) {
                var newDate = new Date(ev.date);
                newDate.setDate(newDate.getDate());
                end.setValue(newDate);
              }
              begin.hide();
              $end[0].focus();
              $end.trigger('change');
              $begin.trigger('change');
            }).data('datepicker');

            var end = $end.datepicker({
              onRender: function(date) {
                return date.valueOf() <= begin.date.valueOf() ? 'disabled' : '';
              },
              format: app.config.dateFormats.datepicker
            }).on('changeDate', function(ev) {
              end.hide();
              $end.trigger('change');
            }).data('datepicker');

        }else {
            $this.find('.datepicker').prop('type','date');
        }
        return this;
    };

    var mainPeriodView = new app.PeriodMainView( window.Data.periods );
    $('.wrapper').append( mainPeriodView.render().$el);


});