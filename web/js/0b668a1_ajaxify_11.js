// Ajaxify
// https://github.com/browserstate/ajaxify
(function(window,undefined){
	// Prepare our Variables
	var
	History = window.History,
	$ = window.jQuery,
	document = window.document;

	// Check to see if History.js is enabled for our Browser
	if ( !History.enabled ) {
		return false;
	}

	// Ajaxify Helper
    $.fn.ajaxify = function(){

        // Prepare
        var $this = $(this);

            $('a.ajaxy').on('click',function(event){
            // Prepare
            var
            $this = $(this),
            url = $this.attr('href'),
            title = $this.attr('title')||null;

            // Continue as normal for cmd clicks etc
            if ( event.which == 2 || event.metaKey ) { return true; }

            // Ajaxify this link
            History.pushState(null,title,url);
            event.preventDefault();
            return false;
            });

        // Chain
        return $this;
    };
	// Wait for Document
	$(function(){

    $window = $(window);
    $body = $(document.body),
    rootUrl = History.getRootUrl();

    // Ajaxify our Internal Links
    $body.ajaxify();

    $window.bind('statechange',function(){
            // Prepare Variables
            var
            State = History.getState(),
            url = State.url,
            relativeUrl = url.replace(rootUrl,'');
            loadPage(url);
    });

    }); // end onDomLoad


})(window); // end closure

function loadPage(url)
{
    //@TODO los redirect hay que controlarlos, pej si no se esta autenticado pero
    //  si se detecta un 30X redirigir la pagina
    $("#overlay").show();
    $.ajax({
        url: url,
        success: function(data, textStatus, jqXHR){
            $("#content").filter(':first').html(data).ajaxify().fadeIn();
            $("#overlay").fadeOut(500);
        },
        error: function(jqXHR, textStatus, errorThrown){
            document.location.href = url;
            return false;
        }
    }); // end ajax
}