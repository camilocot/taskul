BBTaskul.module "TasksApp", (TasksApp, App, Backbone, Marionette, $, _) ->
    class Router extends Backbone.Router
        routes:
            "": "showTasksList"
        showTasksList: ->
            taskList = new App.TasksApp.TasksList.TasksListView
            App.main.show taskList

    App.addInitializer ->
        router = new Router
        App.vent.trigger "routing:started"

