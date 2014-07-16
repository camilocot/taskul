BBTaskul.module "TasksApp.TagsList" , (TagsList, App, Backbone, Marionette, $, _) ->
    class TagsList.TagPreview extends Marionette.ItemView
        template: '#tag-preview-template'
        tagName: 'tr'
        ui:
            checkbox: "input[type=checkbox]"
        events:
            "click @ui.checkbox": "checkTag"
        checkTag: (ev) ->
            @trigger "tag:check", {target: $(ev.currentTarget)}
    class TagsList.TagsListView extends Marionette.CompositeView
        itemView: TagsList.TagPreview
        itemViewContainer: "tbody"
        template: '#tags-list-template'
        initialize: (options) ->
            @listenTo @collection, 'add delete reset', @render
            console.log @model
            @selectedTags = new App.TasksApp.Tags.TagsCollection
        ui:
            addTags: "[name='add-tasks']"
        events:
            "click @ui.addTags": "addTags"
        addTags:
            while model = @selectedTags.first()
                console.log model
