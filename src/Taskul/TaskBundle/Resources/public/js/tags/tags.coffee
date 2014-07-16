BBTaskul.module "TasksApp.Tags", (Tags, App, Backbone, Marionette, $, _) ->
    class Tags.Tag extends Backbone.Model
        urlRoot: window.Api.tag_url
        relations: [
            type: 'HasMany'
            key: 'tasks'
            relatedModel: App.TasksApp.Tasks.TaskTagJoin
            reverseRelation:
                key: 'tag'
        ]
        default:
            name: ''
            tasks: []
        push: (arg, val) ->
            arr = _.clone @get arg
            arr.push val
            @set arg, arr
    class Tags.TagCollection extends Backbone.Collection
        model: Tags.Tag
        url: window.Api.tag_url
        comparator: 'id'

