var app = app || {};

app.PeriodsView = Backbone.View.extend({
    el: '#periodsapp',

    events:{
        'click #add':'addPeriod'
    },

    addPeriod: function( e ) {
        e.preventDefault();

        var formData = {};

        $( '#addPeriod div' ).children( 'input' ).each( function( i, el ) {
            if( $( el ).val() !== '' )
            {
                formData[ el.id ] = $( el ).val();
            }
        });

        this.collection.create( new app.Period( formData ) );
    },

    initialize: function( initialPeriods ) {
        this.$reportrange = this.$('#reportrange');
        this.$reportrangespan = this.$('#reportrange span');
        this.$periods = this.$('#periods');
        this.$begin = this.$('#begin');
        this.$end = this.$('#end');
        this.$calendar = this.$('#calendar');
        this.collection = new app.Periods( initialPeriods );

        this.rangeDatePicker(this);
        this.render();
        this.renderCalendar();

        this.listenTo( this.collection, 'add', this.renderPeriod );
        this.listenTo( this.collection, 'add reset', this.updateCalendar );
        this.listenTo( this.collection, 'reset', this.render );
    },

    render: function() {
        this.collection.each(function( item ) {
            this.renderPeriod( item );
        }, this );

    },

    renderPeriod: function( item ) {
        var periodView = new app.PeriodView({
            model: item,
            parent: this
        });
        this.$periods.append( periodView.render().el );
    },

    renderCalendar: function ( ) {
        this._calendarView = new app.CalendarView(this.collection.models);
        this.$calendar.append(this._calendarView.render().el );
        this._calendarView.activate();
    },

    updateCalendar: function ( item ) {
        this._calendarView.update(item);
    },

    calendar: function() { return this._calendarView; },

    rangeDatePicker: function (that)
    {
        this.$reportrange.daterangepicker(
        {
            startDate: moment(),
            endDate: moment(),
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
               'Last 7 Days': [moment().subtract('days', 6), moment()],
               'Last 30 Days': [moment().subtract('days', 29), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'DD/MM/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        },
        function(start, end) {
            that.$reportrangespan.html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            that.$begin.val(start.format());
            that.$end.val(end.format());
        }
        );
        //Set the initial state of the picker label
        that.$reportrangespan.html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
    }


});