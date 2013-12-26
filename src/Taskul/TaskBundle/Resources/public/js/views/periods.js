var app = app || {};

app.PeriodsView = Backbone.View.extend({
    template: _.template( $( '#list-periods' ).html() ),
    tagName: 'div',
    className: 'list-periods',
    events:{
        'click .sort': 'sortClick',
    },
    // Make it easier to change later
    sortUpIcon: 'ui-icon-triangle-1-n',
    sortDnIcon: 'ui-icon-triangle-1-s',
    _periodRowViews: [],

    initialize: function( ) {
        this.listenTo( this.collection, 'reset', this.render );
        this.listenTo( this.collection, 'sort', this.updateList);
    },

    render: function() {
        this.$el.html(this.template());
        this.$('#periods .sort')
             .append($('<span>'))
             .closest('.row-fluid')
             .find('span')
               .addClass('ui-icon icon-none')
               .end()
             .find('[data-column="'+this.collection.sortAttribute+'"] span')
               .removeClass('icon-none').addClass(this.sortUpIcon);

        this.updateList();
        return this;

    },

    // Now the part that actually changes the sort order
    sortClick: function( e ) {
      var $el = $(e.currentTarget),
          ns = $el.data('column'),
          cs = this.collection.sortAttribute;

      // Toggle sort if the current column is sorted
      if (ns == cs) {
         this.collection.sortDirection *= -1;
      } else {
         this.collection.sortDirection = 1;
      }

      // Adjust the indicators.  Reset everything to hide the indicator
      $el.closest('.row-fluid').find('span').attr('class', 'ui-icon icon-none');

      // Now show the correct icon on the correct column
      if (this.collection.sortDirection == 1) {
         $el.find('span').removeClass('icon-none').addClass(this.sortUpIcon);
      } else {
         $el.find('span').removeClass('icon-none').addClass(this.sortDnIcon);
      }

      // Now sort the collection
      this.collection.sortPeriods(ns);
    },

    // This code has not changed from the example setup in the previous post.
    updateList: function () {

        var ref = this.collection, $periods, me = this;

        _.invoke(this._periodRowViews, 'remove');

        $periods = this.$('ul');

        this._periodRowViews = this.collection.map(
            function ( obj ) {
                  var v = new app.PeriodView({  model: ref.get(obj), collection: this.collection });

                  $periods.append( v.render().el );

                  return v;
        }, this);
    }

});
