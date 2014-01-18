BBTaskul.module "TasksApp" , (TasksApp, App, Backbone, Marionette, $, _) ->
    class TasksApp.TaskView extends Marionette.ItemView
        template: '#task-view-template'
        tagName: 'div'
        className: ''
        onBeforeRender: ->
            @getTags if ! @model.tags? or @model.tags.length == 0
        getTags: ->
            @model.tags = []
