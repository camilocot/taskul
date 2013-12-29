BBTaskul.module "TasksApp.TasksList" , (TasksList, App, Backbone, Marionette, $, _) ->
    class TasksList.TaskPreview extends Marionette.ItemView
        template: '#task-preview-template'
        tagName: 'tr'
        className: ''
    class TasksList.TasksListView extends Marionette.CollectionView
        tagName: "table",
        className: "tasks-list",
        itemViewEventPrefix: "task",
        itemView: TasksList.TaskPreview
        initialize: (options) ->
            @collection = new App.TasksApp.Tasks.TaskCollection
            @collection.fetch()
