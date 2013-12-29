class Taskul.Models.Task extends Backbone.Model
    defaults:
        name: null
    validate:
        name:
          required: true
