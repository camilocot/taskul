$(document).ready(function(){
    var nTr = null; //Para eliminar fila del datatables
    $('ul#task_ops').show();
    $('ul#task_ops').find('a').addClass('ajaxy');

    $liactive = $('ul#task_ops').children('li.active');
    $liactive.find('a').css('color','');
    $liactive.removeClass('active');


    // Alamacena el boton pulsado para el envio
    $("form button[type=submit]").live('click',function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    // Borrado de tareas y ficheros asociados a estas, ponemos los valores correctos dependiendo del boton pulsado
    $('.delete-modal-btn').live( 'click', function (e) {
        taskId = $(this).data('id');
        if (typeof taskId === 'undefined') // Borra ficheros
        {
            taskId = $(this).data('task-id');
            documentId = $(this).data('document-id');
            route = Routing.generate('api_delete_task_file', { "idTask": taskId, 'idDocument': documentId,'_format':'json' });
        }
        else // Borra tareas
        {
            route = Routing.generate('api_delete_task', { "id": taskId });
        }
        nTr = this.parentNode.parentNode; //Para eliminar fila del datatables
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
    // Borrado de tareas y ficheros asociados desde el listado
    var options = {
        dataType: 'json',
        success:    function(e) {
            $('#deleteModal').modal('hide');
            status = ( e.success ) ? 'success' : 'error';
            oTable = $('#list').dataTable();
            var n = noty({text: e.message, type: status, layout: 'top'});
            oTable.fnDeleteRow( oTable.fnGetPosition( nTr ) ) ;
            refereshQuota();


        },
        error: function(e) {
            $('#deleteModal').modal('hide');
            obj = jQuery.parseJSON(e.responseText);
            status = ( obj[0].success ) ? 'success' : 'error';
            n = noty({text: obj[0].message, type: 'error', layout: 'top'});
        }
    };
    $('#delete-task').ajaxForm(options);

    $('#modal-form-submit').click( function (e) {
        e.preventDefault();
        $('#delete-task').submit();
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

function menuColor(selector) {
    $liactive = $(selector);
    $parent = $liactive.parent();
    $parent.find('a').css('color','');
    $liactive.find('a').css('color','#0088CC');
    $liactive.addClass('active');
}
// Prepare our Variables
var History = window.History;

var n = null; //noty
function refereshQuota()
{
    $.getJSON(Routing.generate('api_get_quota'), function(data) {
        actualValue = $('#dial-quota').val();
        if(data.success == true && actualValue != data.current_quota)
            $('#dial-quota').val(data.current_quota).trigger('change');
    });
}