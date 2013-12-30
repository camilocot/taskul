BBTaskul.module "TasksApp.FormView" , (FormView, App, Backbone, Marionette, $, _) ->
    class FormView.Task extends App.Libs.FormView
        template: '#task-form-template'
        className: 'task-form'
        ui:
            name: '[name="name"]'
            activityIndicator: '.loading'
        createModel: -> new App.TasksApp.Tasks.Task
        updateModel: ->
            @model.set
                name: @ui.name.val()
            console.log @model
        onSuccess: (model) ->
            Backbone.trigger 'task:create', model
