BBTaskul.module "TasksApp.Tasks", (Tasks, App, Backbone, Marionette, $, _) ->
    class Tasks.Task extends Backbone.Model
        url: window.Api.task_url
        initialize: ->
            console.log @
    class Tasks.TaskCollection extends Backbone.Collection
        model: Tasks.Task
        url: window.Api.task_url
        comparator: 'name'
        initialize: ->
                    console.log @
