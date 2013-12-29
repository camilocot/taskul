@BBTaskul = do (Backbone, Marionette) ->
    App = new Marionette.Application()

    App.addRegions
        header: '#header'
        main: '#main'

    App.on 'initialize:after', ->
        Backbone.history.start() if Backbone.history
    App

