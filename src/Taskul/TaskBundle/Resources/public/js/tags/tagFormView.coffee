BBTaskul.module "TasksApp.FormView" , (FormView, App, Backbone, Marionette, $, _) ->
    class FormView.Tag extends App.Libs.FormView
        template: '#modal-view-form-tag'
        className: 'tag-form'
        ui:
            name: '[name="name"]'
            activityIndicator: '.loading'
        createModel: ->
            @model = new App.TasksApp.Tags.Tag if !@model
            @model
        updateModel: ->
            @model.set
                name: @ui.name.val()
        onSuccess: (model) ->
            @collection.add model

        changeStatus: (ev)->
            @.ui.status.val $(ev.currentTarget).val()
