BBTaskul.module "TasksApp.Tasks", (Tasks, App, Backbone, Marionette, $, _) ->
    class Tasks.Task extends Backbone.Model
        urlRoot: window.Api.task_url
        validation:
            name:
                required: true
                msg: 'Please enter a valid name'
        defaults:
            name: ''
            status: 'inprogress'
            members: []
            description: ''
        toJSON: ->
            json = Backbone.Model.prototype.toJSON.apply @, arguments
            delete json.owner
            delete json.periods
            delete json.created
            delete json.updated
            delete json.percent
            json
    class Tasks.TaskCollection extends Backbone.Collection
        model: Tasks.Task
        url: window.Api.task_url
        comparator: 'id'
