BBTaskul.module "TasksApp.Tags", (Tags, App, Backbone, Marionette, $, _) ->
    class Tags.Tag extends Backbone.Model
        urlRoot: window.Api.tag_url
        default:
            name: ''
    class Tags.TagCollection extends Backbone.Collection
        model: Tags.Tag
        url: window.Api.tag_url
        comparator: 'id'

