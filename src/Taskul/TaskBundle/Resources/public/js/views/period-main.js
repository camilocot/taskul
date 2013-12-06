var app = app || {};

app.PeriodMainView = Backbone.View.extend({
    template: _.template( $( '#main-period' ).html() ),
    tagName: 'div',
    events:{
        'click #add-period': 'addPeriod',
        'click #clear-filter': 'clearFilter',
        'keyup #filter': 'filter',
        'click #view-calendar': 'renderCalendarView',
        'click #view-list': 'renderListView'
    },
    filtered: false,
    view: 'listPeriodView',

    filter: function (e) {
        this.filteredCollection.reset( this.filterList( this.$filter.val() ) );
    },

    addPeriod: function (e) {
        new app.AddPeriodView({collection: this.collection}).show();
    },

    initialize: function( initialPeriods ) {
        this.collection =  new app.Periods( initialPeriods );
        this.filteredCollection = new app.Periods( initialPeriods );
        this.listPeriodView = new app.PeriodsView( {collection: this.filteredCollection} );
        this.calendarView = new app.CalendarView( {collection: this.filteredCollection} );

        this.listenTo( this.collection, 'add', this.filter );

    },

    clearFilter: function()
    {
      this.render(initialPeriods);
    },

    render: function() {
        this.$el.html(this.template());

        if(this.view == 'listCalendarView'){
            this.renderCalendarView();
        } else {
            this.renderListView();
        }

        return this;
    },

    filterList: function(filterValue){
        filterValue = filterValue.toLowerCase();
        if (filterValue === "") {
            this.filtered = false;
            return this.collection.models;
        }
        this.filtered = true;
        return this.collection.filter(function(data) {
          return  _.some(_.values(data.toJSON()), function(value) {
            value = (!isNaN(value) ? value.toString() : value);
            if(typeof value !== 'string')
                return false;
            else{
                return value.toLowerCase().indexOf(filterValue) >= 0;
            }
          });
        });
    },

    renderCalendarView: function ( ) {
        this.$('.list-periods').hide();
        this.$('.calendar').show();
        this.$('#view-calendar').hide();
        this.$('#view-list').show();
        this.$el.append(this.calendarView.render().$el);
        this.calendarView.activate();
        this.view = 'listCalendarView';
    },

    renderListView: function()
    {
        this.$('.calendar').hide();
        this.$filter = this.$('#filter');
        this.$('#view-calendar').show();
        this.$('#view-list').hide();
        this.$el.append(this.listPeriodView.render().$el);
        this.view = 'listPeriodView';
        this.$('.list-periods').show();
    }
});

