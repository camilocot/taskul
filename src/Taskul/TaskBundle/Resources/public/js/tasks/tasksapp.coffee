BBTaskul.module "TasksApp", (TasksApp, App, Backbone, Marionette, $, _) ->
    class TasksApp.Router extends Marionette.AppRouter
        appRoutes:
            "": "showTasksList"
            "new": "showTaskForm"
            "edit/:id": "showEditTaskForm"
            "view/:id": "showViewForm"

    class TasksApp.Layout extends Marionette.Layout
        template: '#tasks-layout-template'
        regions:
            labels: '#labels'
            content: '#main-content'

    class TasksApp.ModalRegion extends Marionette.Region
        constructor: ->
            Marionette.Region.prototype.constructor.apply @, arguments
            @ensureEl()
            @$el.on 'hidden', {region:this}, (event) ->
                event.data.region.close()
        onShow: ->
            @$el.modal 'show'
        onClose: ->
            @$el.modal 'hide'

    class TasksApp.Controller extends Marionette.Controller
        showTasksList: ->
            taskList = new App.TasksApp.TasksList.TasksListView
                collection: @collection
            @taskLayout.content.show taskList
        showTaskForm: ->
            taskForm = new App.TasksApp.FormView.Task
            @taskLayout.content.show taskForm
        showEditTaskForm: (id) ->
            task = @collection.get id
            taskForm = new App.TasksApp.FormView.Task
                model: task
            @taskLayout.content.show taskForm
        showViewForm: (id) ->
            task = @collection.get id
            taskView = new App.TasksApp.TaskView
                model: task
            @taskLayout.content.show taskView
        initialize: ->
            @collection = new App.TasksApp.Tasks.TaskCollection
            @tags = new App.TasksApp.Tags.TagCollection
            @tags.fetch
                async: false
            @collection.fetch
                async: false
            @taskLayout = new App.TasksApp.Layout
            @taskActions = new App.TasksApp.TasksList.TaskActions
                collection: @tags
            App.main.show @taskLayout
            @taskLayout.labels.show @taskActions
            App.vent.on 'task:create', (model) ->
                @collection.add model
            , @

            App.vent.on 'tag:create', (model) ->
                @tags.add model
            , @
    App.addInitializer ->
        @taskController = new TasksApp.Controller
        @taskRouter = new TasksApp.Router
            controller: @taskController
        App.vent.trigger "routing:started"
