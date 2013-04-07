$(document).ready(function(){

    if ($.fn.datepicker) {
        $('#btn-trash').click(function(e){
            $dp.datepicker('hide');
            $('input#task_dateEnd').val('');
        });
        $dp = $('#datepick').datepicker()
        .on('changeDate', function(ev){
            $('input#task_dateEnd').val($('#datepick').data('date'));
            $('#datepick').datepicker('hide');
        });
    }

    if(typeof defaultTags === 'undefined')
        var defaultTags = null;

    if ($.fn.select2) {
        $("select#task_members").select2();
        $("input#task_tags").select2({
            tags: defaultTags,
            tokenSeparators: [","]
        });
    }
    $('.wysihtml5').wysihtml5({
        "html":true,
        "color":true
    });

    // Botones de los estados de las tareas
    $('div.btn-group[data-toggle-name=*]').each(function(){
        group   = $(this);
        form    = group.parents('form').eq(0);
        name    = group.attr('data-toggle-name');
        var hidden  = $('input[name="' + name + '"]', form);
        $('button', group).each(function(){
          var button = $(this);
          button.live('click', function(){
              hidden.val($(this).val());
          });
          if(button.val() == hidden.val()) {
            button.addClass('active');
        }
    });
    });
    $('ul#task_ops').show();

    // Crear nueva tarea, redirecciona a ver o a aañadir ficheros
    $('#form_task').submit(function(){
        var valSubmit = $("button[type=submit][clicked=true]").val();
        $(this).ajaxSubmit({
            success: function (data){
                console.log("Sample of data:", data);
                console.log(valSubmit);
                taskId = data.id;
                if(valSubmit == 1)
                {
                    route = Routing.generate('api_get_task_files', { "id": taskId, '_format':'html'});
                }
                else if(valSubmit == 0)
                {
                    route = Routing.generate('api_get_task', { "id": taskId , '_format':'html'});
                }
                console.log(route);
                loadPage(route);

            },
            error: function (data){
                console.log("Error of data:", data);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });

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

    // Borrado de tareas desde el listado
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
            var n = noty({text: obj[0].message, type: 'error', layout: 'top'});
        }
    };
    $('#delete-task').ajaxForm(options);

    $('#modal-form-submit').click( function (e) {
       e.preventDefault();
       $('#delete-task').submit();
   });

    $window = $(window);
    rootUrl = History.getRootUrl(),
    $window.bind('statechange',function(){
            // Prepare Variables
            var
            State = History.getState(),
            url = State.url,
            relativeUrl = url.replace(rootUrl,'');
            loadPage(url);
    });

});

function loadPage(url)
{

    $.ajax({
        url: url,
        success: function(data, textStatus, jqXHR){
                    // Prepare
                    var
                    $data = $(data),
                    $dataContent = $data.find('#content').filter(':first');
                    console.log($dataContent.length);
                    // Fetch the content
                    contentHtml = $dataContent.html();
                    if ( !contentHtml ) {
                        //document.location.href = url;
                        //return false;
                    }

                    // Update the content
                    //$content.stop(true,true);
                    title = 'sasaas';
                    $('#content').filter(':first').html(data).show(); /* you could fade in here if you'd like */
                    History.pushState(null,title,url);
                },
                error: function(jqXHR, textStatus, errorThrown){
                    //document.location.href = url;
                    //return false;
                }
            }); // end ajax
}

// Prepare our Variables
var History = window.History;