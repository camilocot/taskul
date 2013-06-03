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

	// Wait for Document
	$(function(){
        $menu = $('#menu,#nav,nav:first,.nav:first').filter(':first');
        var
            $window = $(window),
            $body = $(document.body),
            rootUrl = History.getRootUrl();
        // Ajaxify Helper
        $.fn.ajaxify = function(){

            // Prepare
            var $this = $(this);
            $($this).on('click','a.ajaxy',function(event){
                // Prepare
                var
                $this = $(this),
                url = $this.attr('href'),
                title = $this.attr('title')||null;

                // Continue as normal for cmd clicks etc
                //if ( event.which == 2 || event.metaKey ) { return true; }

                // Ajaxify this link
                History.pushState(null,title,url);
                event.preventDefault();
                return false;
            });

            // Chain
            return $this;
        };
        // Ajaxify our Internal Links
        $body.ajaxify();

        $window.bind('statechange',function(){
                // Prepare Variables
                var
                State = History.getState(),
                url = State.url;
                relativeUrl = url.replace(rootUrl,'');
                loadPage(url);
        });
        loadAjaxModalForms();
        loadAjaxForms();
        showWarningNoRecords();
    }); // end onDomLoad

})(window); // end closure

var $menu,
    activeClass = 'active selected current youarehere',
    activeSelector = '.active,.selected,.current,.youarehere',
    menuChildrenSelector = '> li,> ul > li',
    relativeUrl;

function loadPage(url)
{
    //@TODO los redirect hay que controlarlos, pej si no se esta autenticado pero
    //  si se detecta un 30X redirigir la pagina
    $("#overlay").show();
    $.ajax({
        url: url,
        success: function(data, textStatus, jqXHR){
            var $menuChildren;

            if(data.success == true && data.content)
                $("#content").filter(':first').html(data.content).ajaxify().fadeIn();
            else
                $("#content").filter(':first').html(data).ajaxify().fadeIn();
            $("#overlay").fadeOut(500);
            loadAjaxModalForms();
            loadAjaxForms();
            showWarningNoRecords();
            template_functions(); //main.js
            widthFunctions(); //main.js

            // Update the menu
            $menuChildren = $menu.find(menuChildrenSelector);
            $menuChildren.filter(activeSelector).removeClass(activeClass);
            $menuChildren = $menuChildren.has('a[href^="'+relativeUrl+'"],a[href^="/'+relativeUrl+'"],a[href^="'+url+'"]');
            if ( $menuChildren.length === 1 ) { $menuChildren.addClass(activeClass); }

        },
        error: function(jqXHR, textStatus, errorThrown){
            document.location.href = url;
            return false;
        }
    }); // end ajax
}

function loadAjaxForms()
{
    $("form.ajaxform").each(function(index) {
        var $form = $(this);
        $form.validate({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function (data){
                        if(data.success == true && data.url && data.forceredirect) {
                            window.location.replace(data.url);
                        }else if(data.success == true && data.url) {
                            loadPage(data.url);
                            History.pushState(null,data.title,data.url);
                        }else if (data.success == true && data.content){
                            $('#content').html(data.content);
                            loadAjaxForms();
                        }
                    },
                    error: function(jqXHR,textStatus,errorThrown){
                        alert(jqXHR.responseText.message);
                    }
                });
            }
        });
    });
}
function loadAjaxModalForms()
{

    $('.ajaxmodalform').each(function(index){
        var $form = $(this);
        var $modal = $($form.data('modal-id'));

        var options = {
            dataType: 'json',
            success:    function(e) {
                /* Este dato se asocia al formulario dinamicamente desde el boton que abre el modal */
                var redirect = $form.data('redirect');

                $modal.modal('hide');
                status = ( e.success ) ? 'success' : 'error';
                notificacion(e.message,status);


                if(status == 'success' && typeof $remove !== 'undefined'){
                    $remove.deleteTableRow();
                }

                toggleWarning();

                if(typeof redirect !== 'undefined')
                {
                    url = Routing.generate(redirect);
                    $('.box-content').fadeOut();
                    loadPage(url);
                }

                launchNotifications();
                activateNotifications();

            },
            error: function(e) {
                $modal.modal('hide');
                obj = jQuery.parseJSON(e.responseText);
                status = ( obj[0].success ) ? 'success' : 'info';
                            $('.top-right').notify({
                        message: { text: obj[0].message },
                        type: status,
                        fadeOut: { enabled: true, delay: 3000 }
                }).show();
            }
        };
        $form.ajaxForm(options);
    });
}

function showWarningNoRecords()
{
   if($('.warning').length > 0) {
    /* Mostrar / ocultar la capa warning si no hay resultados en los listados */
    toggleWarning();
    /* Footable para los listados */
    $remove = null;
    $('.footable').footable();
    $('.clear-filter').click(function (e) {
        e.preventDefault();
        $('table').trigger('footable_clear_filter');
    });
  }
}

function toggleWarning()
{
    $warning = $('.warning:first');
    if($('#list > tbody > tr').length == 0)
    {
        $('#list').hide();
        $('#filter-list').hide();
        $warning.show();
    }else
        $warning.hide();
}