var app = app || {};

app.PeriodView = Backbone.View.extend({
    tagName: 'div',
    className: 'periodContainer',
    template: _.template( $( '#periodTemplate' ).html() ),
    events: {
        'click .delete': 'deletePeriod'
    },

    initialize: function(options) {
      this.parent = options.parent; // Or use this.options.parent everywhere.
    },

    deletePeriod: function() {

        this.parent.calendar().remove(this.model);
        //Delete model
        this.model.destroy();
        //Delete view
        this.remove();
    },

    render: function() {
        //this.el is what we defined in tagName. use $el to get access to jQuery html() function
        this.$el.html( this.template( this.model.toJSON() ) );
        return this;
    }
});