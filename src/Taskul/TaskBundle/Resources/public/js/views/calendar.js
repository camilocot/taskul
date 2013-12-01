var app = app || {};

app.CalendarView = Backbone.View.extend({
    tagName: 'div',
    className: 'calendar',
    template: _.template( $( '#calendarTemplate' ).html() ),
    eventsCalendar: {},
    removeEvents: [],

    initialize: function ( periods ) {
        this.addPeriods(periods);
    },

    render: function() {
        this.$el.html( this.template( ) );
        return this;
    },

    activate: function() {
        this.$el.parent().responsiveCalendar({
            time: moment().format('YYYY-MM'),
            events: this.eventsCalendar,
            activateNonCurrentMonths: true
        });
    },

    update: function ( period )
    {
        this.addPeriods([period]);
    },

    remove: function ( period )
    {
        this.deletePeriods([period]);
        this.$el.responsiveCalendar('clear', this.removeEvents);
    },

    addPeriods: function ( periods )
    {
        return this._processPeriods(periods,'_addDay');
    },

    deletePeriods: function ( periods )
    {
        return this._processPeriods(periods,'_removeDay');
    },

    _processPeriods: function (periods, op)
    {
        var startDate, endDate, days, d;
        that = this;

        _.each (periods, function(period) {
            that._processPeriod(op, period);
        });

        this.$el.responsiveCalendar('edit', this.eventsCalendar);
    },

    _processPeriod: function ( op, period )
    {
        var d, days, day;
        startDate = new Date(period.attributes.begin);
        endDate = new Date(period.attributes.end);
        that = this;

        days = startDate.getDates(endDate);

        _.each ( days, function (day) {
            d = moment(day).format('YYYY-MM-DD');
            that[op](d);
        });
    },

    _removeDay: function(d)
    {
        var eventsCalendar = this.eventsCalendar;

        if(eventsCalendar[d].number === 1){
            delete eventsCalendar[d];
            this.removeEvents.push(d);
        }
        else if(eventsCalendar[d].number === 1)
            eventsCalendar[d] = { "number": 1, "class": "active cool" };
        else
            eventsCalendar[d] = { "number": --eventsCalendar[d].number, "badgeClass": "badge-warning" };

        this.eventsCalendar = eventsCalendar;
    },

    _addDay: function (d)
    {
        var eventsCalendar = this.eventsCalendar;

        if(typeof eventsCalendar[d] !== 'undefined')
            eventsCalendar[d] = { "number": ++eventsCalendar[d].number, "badgeClass": "badge-warning" };
        else
            eventsCalendar[d] = {"class": "active cool", "number": 1};

        this.eventsCalendar = eventsCalendar;
    }
});