BBTaskul.module "TasksApp.Tasks", (Tasks, App, Backbone, Marionette, $, _) ->
    class Tasks.Task extends Backbone.Model
    class Tasks.TaskCollection extends Backbone.Collection
        model: Tasks.Task
        url: window.Api.task_url
        initialize: ->
