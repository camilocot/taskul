var historyBool = true; // Para los formularios qeu sino carga la pagina 2 veces, una por la redireccion del form y otra por el statechange del history

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
        $window = $(window);
        $body = $(document.body);
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

                $('a').removeClass('active');
                $this.addClass('active');
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
        // Ajaxify our Internal Links
        $body.ajaxify();

        $window.bind('statechange',function(){
                // Prepare Variables
                var
                State = History.getState(),
                url = State.url;
                relativeUrl = url.replace(rootUrl,'');
                if(historyBool)
                    loadPage(url);
                else
                    historyBool = true;
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
    relativeUrl,
    $window,
    $body,
    rootUrl,
    completedEventName = 'statechangecomplete';

function loadPage(url)
{

    if(!$(".progress-indicator").is(':visible'))
        $(".progress-indicator").fadeIn(500);
    $.ajax({
        url: url,
        success: function(data, textStatus, jqXHR){
            var $menuChildren;
            // Aqui vamos a comprobar si es un página pública y si estamos en la parte pública
            if(data.private_page === false && $('#overlay').length == 1) { // Estamos con el frontend equivacado recargamos
                window.location.href = url;
            } else if(data.success === true && data.content)
                $("#content").filter(':first').html(data.content).ajaxify();
            else
                $("#content").filter(':first').html(data).ajaxify();
            if($('#overlay').length == 1) { // Cargamos funciones del backend
                loadAjaxModalForms();
                loadAjaxForms();
                showWarningNoRecords();
                template_functions(); //private.js
                widthFunctions(); //private.js
                launchNotifications(); // private.js
                // Update the menu
                $menuChildren = $menu.find(menuChildrenSelector);
                $menuChildren.filter(activeSelector).removeClass(activeClass);
                $menuChildren = $menuChildren.has('a[href^="'+relativeUrl+'"],a[href^="/'+relativeUrl+'"],a[href^="'+url+'"]');
                if ( $menuChildren.length === 1 ) { $menuChildren.addClass(activeClass); }
                $(".progress-indicator").fadeOut(500);
            }

            // document.title = title;
            // try {
            //         document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<','&lt;').replace('>','&gt;').replace(' & ',' &amp; ');
            //     }
            // catch ( Exception ) { }

            // Complete the change
            $body.scrollTo( 0, 800 );
            $window.trigger(completedEventName);

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
                $(".progress-indicator").fadeIn(500);
                $(form).ajaxSubmit({
                    success: function (data){
                        checkResponse(data);
                    },
                    error: function(jqXHR,textStatus,errorThrown){
                        $(".progress-indicator").fadeOut(500);
                        notificacion(jqXHR.responseText.message,'error');
                    }
                });
            }
        });
    });
}

function redirect(url){
    window.location.replace(url);
}

function checkResponse(data)
{
    if(data.success === true && data.url && data.forceredirect) {
        if(data.message){
            notificacion(data.message,'success');
            setTimeout("redirect('"+data.url+"')",3000);
        }else
            redirect(data.url);
    }else if(data.success === true && data.url) {
        title = data.title;
        loadPage(data.url);
        historyBool = false;
        if(data.message)
            notificacion(data.message,'success');
        History.pushState(null,data.title,data.url);
    }else if (data.success === true && data.content){
        $('#content').html(data.content);
        loadAjaxForms();
        if(data.message)
            notificacion(data.message,'success');
    } else if (data.success === true && data.message) {
        notificacion(data.message,'success');
    }else if(data.success === false && data.message){
        notificacion(data.message,'error');
    }
    $(".progress-indicator").fadeOut(500);
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

                if(e.success === true && typeof $remove !== 'undefined'){
                    $remove.deleteTableRow(); // private.js
                }

                checkResponse(e);

            },
            error: function(e) {
                $modal.modal('hide');
                obj = jQuery.parseJSON(e.responseText);
                var status = ( obj[0].success ) ? 'success' : 'info';
                notificacion(obj[0].message, status);
                $(".progress-indicator").fadeOut(500);

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
    if($('#list > tbody > tr').length === 0)
    {
        $('#list').hide();

        //$('.well').hide();
        //$('#filter-list').hide();
        $warning.show();
    }else
        $warning.hide();
}