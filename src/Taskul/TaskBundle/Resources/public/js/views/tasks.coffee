class Taskul.Views.TasksView extends Backbone.View
    el: '#main-content'
    initialize: (options) ->
        @collection = new Taskul.Collections.TasksCollection
        @collection.on 'reset', @render, @
        @collection.fetch
            success: =>
        @template = _.template $('#tasks-list-template').html()
    render: =>
        @$el.empty()
        @$el.html @template()
        for task in @collection.models
            do (task) =>
                @renderTask task
    renderTask: (task) ->
        v = new Taskul.Views.TaskView
            model: task
        @$('.wrapper').append v.render().el
