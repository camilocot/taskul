$(document).ready(function(){

    clearMenuActive('task_ops');

    // Borrado de tareas y ficheros asociados a estas, ponemos los valores correctos dependiendo del boton pulsado
    $(document).on( 'click', '.delete-modal-btn', function (e) {
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
        $remove = $(this); //Para eliminar fila del datatables

        $('#form_id').val(taskId);
        $('#delete-task').attr('action', route);

    });

    // Borrado de tareas y ficheros asociados desde el listado
    var options = {
        dataType: 'json',
        success:    function(e) {
            $('#deleteModal').modal('hide');
            status = ( e.success ) ? 'success' : 'error';

            $('.top-right').notify({
                    message: { text: e.message },
                    type: status,
                    fadeOut: { enabled: true, delay: 3000 }
            }).show();
            console.log($remove);
            if(status == 'success' && typeof $remove !== 'undefined'){
                $remove.deleteTableRow();
                refereshQuota();

            }
            /* Por si estamos en una ficha */
            $('a.btn-success').remove();
            $('a.btn-back-list').trigger('click');

            launchNotifications();
            activateNotifications();

        },
        error: function(e) {
            $('#deleteModal').modal('hide');
            obj = jQuery.parseJSON(e.responseText);
            status = ( obj[0].success ) ? 'success' : 'info';
                        $('.top-right').notify({
                    message: { text: obj[0].message },
                    type: status,
                    fadeOut: { enabled: true, delay: 3000 }
            }).show();
        }
    };

    $('#delete-task').ajaxForm(options);

    $('#modal-form-submit').click( function (e) {
        e.preventDefault();
        $('#delete-task').submit();

    });

});