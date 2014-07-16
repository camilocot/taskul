BBTaskul.module "TasksApp.Tasks", (Tasks, App, Backbone, Marionette, $, _) ->
    #Join table
    class Tasks.TaskTagJoin extends Backbone.RelationalModel

    class Tasks.Task extends Backbone.RelationalModel
        urlRoot: window.Api.task_url
        validation:
            name:
                required: true
                msg: 'Please enter a valid name'
            description:
                required: false
        defaults:
            name: ''
            status: 'inprogress'
            members: []
            description: ''
            tags: []
        toJSON: ->
            json = Backbone.Model.prototype.toJSON.apply @, arguments
            delete json.owner
            delete json.periods
            delete json.created
            delete json.updated
            json
        relations: [
            type: 'HasMany',
            key: 'tags'
            relatedModel: Tasks.TaskTagJoin
            reverseRelation:
                key: 'task'
        ]

    class Tasks.TaskCollection extends Backbone.Collection
        model: Tasks.Task
        url: window.Api.task_url
        comparator: 'id'

