$(document).ready(function(){
    $('ul#task_ops').show();
    $('ul#task_ops').find('a').addClass('ajaxy');

    $liactive = $('ul#task_ops').children('li.active');
    $liactive.find('a').css('color','');
    $liactive.removeClass('active');


    // Alamacena el boton pulsado para el envio
    $("form button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    // Borrado de tareas y ficheros asociados a estas
    $('.delete-modal-btn').live( 'click', function (e) {
        taskId = $(this).data('id');
        if (typeof taskId === 'undefined') // Borra ficheros
        {
            taskId = $(this).data('task-id');
            documentId = $(this).data('document-id');
            route = Routing.generate('api_delete_task_file', { "idTask": taskId, 'idDocument': documentId,'_format':'json' });
            nTr = this.parentNode.parentNode; //Para eliminar fila del datatables
        }
        else // Borra tareas
        {
            route = Routing.generate('api_delete_task', { "id": taskId });
        }
        $('#form_id').val(taskId);
        $('#delete-task').attr('action', route);

    });

    $window = $(window);
    $body = $(document.body),
    rootUrl = History.getRootUrl();
    $window.bind('statechange',function(){
            // Prepare Variables
            var
            State = History.getState(),
            url = State.url,
            relativeUrl = url.replace(rootUrl,'');
            loadPage(url);
            //History.pushState(null,'Cambio',url);
    });

    $('.boostrap-tp').tooltip({
        "trigger":"click"
    }).click(function(e){
        e.preventDefault();
    });



    // Ajaxify Helper
    $.fn.ajaxify = function(){
            // Prepare
            var $this = $(this);

            // Ajaxify
            links = $this.find('a.ajaxy');
            if(links){
                links.click(function(event){
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
            }

            // Chain
            return $this;
        };

    // Ajaxify our Internal Links
    $body.ajaxify();
});

function loadPage(url)
{

    $.ajax({
        url: url,
        success: function(data, textStatus, jqXHR){
            $("#content").filter(':first').css('min-height','600px').fadeOut(function() {
            $(this).html(data).ajaxify().fadeIn();
            });
        },
        error: function(jqXHR, textStatus, errorThrown){
            document.location.href = url;
            return false;
        }
            }); // end ajax
}

function menuColor(selector) {
    $liactive = $(selector);
    $parent = $liactive.parent();
    $parent.find('a').css('color','');
    $liactive.find('a').css('color','#0088CC');
    $liactive.addClass('active');
}
// Prepare our Variables
var History = window.History;