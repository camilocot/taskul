BBTaskul.module "TasksApp.Tasks", (Tasks, App, Backbone, Marionette, $, _) ->
    class Tasks.Task extends Backbone.Model
        urlRoot: window.Api.task_url
        validation:
            name:
                required: true
                msg: 'Please enter a valid name'
            description:
                required: false
        defaults:
            name: 'cxzcxzczx'
            status: 'inprogress'
            members: []
            description: 'cxzcxcxz'
            tags: []
        toJSON: ->
            json = Backbone.Model.prototype.toJSON.apply @, arguments
            delete json.owner
            delete json.periods
            delete json.created
            delete json.updated
            json
    class Tasks.TaskCollection extends Backbone.Collection
        model: Tasks.Task
        url: window.Api.task_url
        comparator: 'id'
