BBTaskul.module "TasksApp", (TasksApp, App, Backbone, Marionette, $, _) ->
    class TasksApp.Router extends Marionette.AppRouter
        appRoutes:
            "": "showTasksList"
            "new": "showTaskForm"
            "edit/:id": "showEditTaskForm"
            "view/:id": "showViewForm"

    class TasksApp.Controller extends Marionette.Controller
        showTasksList: ->
            taskList = new App.TasksApp.TasksList.TasksListView
                collection: @collection
            App.main.show taskList
        showTaskForm: ->
            taskForm = new App.TasksApp.FormView.Task
            App.main.show taskForm
        showEditTaskForm: (id) ->
            task = @collection.get id
            taskForm = new App.TasksApp.FormView.Task
                model: task
            App.main.show taskForm
        showViewForm: (id) ->
            task = @collection.get id
            taskView = new App.TasksApp.TaskView
                model: task
            App.main.show taskView
        initialize: ->
            @collection = new App.TasksApp.Tasks.TaskCollection
            @collection.fetch
                async: false
            App.vent.on 'task:create', (model) ->
                @collection.add model
            , @
    App.addInitializer ->
        @taskController = new TasksApp.Controller
        @taskRouter = new TasksApp.Router
            controller: @taskController
        App.vent.trigger "routing:started"

