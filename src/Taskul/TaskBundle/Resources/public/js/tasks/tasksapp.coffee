BBTaskul.module "TasksApp", (TasksApp, App, Backbone, Marionette, $, _) ->
    class Router extends Backbone.Router
        routes:
            "": "showTasksList"
            "new": "showTaskForm"
        showTasksList: ->
            taskList = new App.TasksApp.TasksList.TasksListView
            App.main.show taskList
        showTaskForm: ->
            taskForm = new App.TasksApp.FormView.Task
            App.main.show taskForm

    App.addInitializer ->
        router = new Router
        App.vent.trigger "routing:started"

