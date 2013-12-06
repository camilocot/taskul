var app = app || {};

app.AddPeriodView = Backbone.View.extend({
    events: {
        'click .save-action': 'save',
        'click .close,.close-action': 'close',
        'change input,textarea': 'modify',
        'keyup textarea': 'modify'
    },

    initialize: function() {
        this.template = _.template($('#add-period-dialog').html());
    },

    render: function() {
        this.model = new app.Period( { 'begin': moment().format(), 'end': moment().format() });
        this.$el.html(this.template(this.model.toJSON()));
        return this;
    },
    /*
     * Displaying the dialog
     */
    show: function() {
        $(document.body).append(this.render().el);
        this.renderDatePicker();
    },
    /*
     * Removing the dialog
     */
    close: function() {
        this.remove();
    },
    /*
     * Fires when we click save on the form
     */
    save: function() {

        this.collection.create(this.model, {wait: true} );
        if(!this.checkErrors()) {
            this.collection.add( this.model );
            this.remove();
        }
    },

    renderDatePicker: function ()
    {
        this.$el.renderDatePicker();
    },
    checkErrors: function ()
    {
        if(!this.model.validationError) {
            this.hideErrors();
            return false;
        }
        else
            this.showErrors(this.model.validationError);
        return true;
    },
    showErrors: function(errors) {
        _.each(errors, function (error) {
            var controlGroup = this.$('.' + error.name);
            controlGroup.addClass('error');
            controlGroup.find('.help-inline').text(error.message);
        }, this);
    },

    hideErrors: function () {
        this.$('.control-group').removeClass('error');
        this.$('.help-inline').text('');
    },
    /*
     * We listen to every change on forms input elements and as they have the same name as the model attribute we can easily update our model
     */
    modify: function(e) {
        var attribute = {};
        attribute[e.currentTarget.id] = e.currentTarget.value;
        this.model.set(attribute);
    }
});