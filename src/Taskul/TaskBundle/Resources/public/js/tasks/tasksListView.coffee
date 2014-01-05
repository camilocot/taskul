BBTaskul.module "TasksApp.TasksList" , (TasksList, App, Backbone, Marionette, $, _) ->
    class TasksList.TaskPreview extends Marionette.ItemView
        template: '#task-preview-template'
        tagName: 'tr'
        className: ''
        ui:
            checkbox: "input[type=checkbox]"
        events:
            "click @ui.checkbox": "checkTask"
        checkTask: (ev) ->
            @trigger "task:check", {target: $(ev.currentTarget)}
    class TasksList.TasksListView extends Marionette.CompositeView
        itemView: TasksList.TaskPreview
        itemViewContainer: "tbody"
        template: '#tasks-list-template'
        initialize: (options) ->
            @listenTo @collection, 'add delete reset', @render
            @listenTo @, 'itemview:task:check', (itemView, data) -> @checkTask itemView, data
            @selectedTasks = new App.TasksApp.Tasks.TaskCollection
        ui:
            delete: "[name='delete-tasks']"
        events:
            "click @ui.delete": "deleteTasks"
        deleteTasks: ->
            callbacks =
                success: => @trigger 'delete:model:success'
                error: => @trigger 'delete:model:failure'
            while model = @selectedTasks.first()
                model.destroy callbacks
                @collection.remove model
        checkTask: (itemView, data) ->
            $check = data.target
            if $check.is(':checked')
                @selectedTasks.add @collection.get $check.val()
            else
                @selectedTasks.remove @collection.get $check.val()
