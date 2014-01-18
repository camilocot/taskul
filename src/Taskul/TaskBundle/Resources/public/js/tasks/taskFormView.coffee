BBTaskul.module "TasksApp.FormView" , (FormView, App, Backbone, Marionette, $, _) ->
    class FormView.Task extends App.Libs.FormView
        template: '#task-form-template'
        className: 'task-form'
        ui:
            name: '[name="name"]'
            description: '[name="description"]'
            btnGroupStatus: '.btn-group > button'
            status: '[name="status"]'
            activityIndicator: '.loading'
        events:
            "click @ui.btnGroupStatus": "changeStatus"
        createModel: ->
            @model = new App.TasksApp.Tasks.Task if !@model
            @model
        updateModel: ->
            @model.set
                name: @ui.name.val()
                description: @ui.description.val()
                status: @ui.status.val()
        onSuccess: (model) ->
            App.vent.trigger 'task:create', model
            App.taskRouter.navigate('', {trigger: true})
        changeStatus: (ev)->
            @.ui.status.val $(ev.currentTarget).val()
        onRender: ->
            status = @model.get 'status'
            @ui.btnGroupStatus.filter('.btn-'+status).addClass('active')
