class Taskul.Routers.TasksRouter extends Backbone.Router
    initialize: (el) ->
        @el = el
        @tasksListView = new Taskul.Views.TasksView
        @taskulRegion = new Marionette.Region
            el: '#main-content'
    routes:
        "": "tasksList"
    switchView: (view) ->
        console.log view
        @taskulRegion.show view
    tasksList: ->
        this.switchView(@tasksListView);
