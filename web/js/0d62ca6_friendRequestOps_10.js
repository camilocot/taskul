var $warning;
$(document).ready(function() {

    $(document).on( 'click', '.delete-modal-btn', function (e) {
        frquestId = $(this).data('id');
        $('#form_delete_id').val(frquestId);
        dynroute = $('#delete-frequest').data('route');
        route = Routing.generate(dynroute, { "id": frquestId });
        $('#delete-frequest').attr('action', route);
        $remove = $(this);

    });

    // Borrado de peticiones de amistad
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
            if(status == 'success' && typeof $remove !== 'undefined'){
                $remove.deleteTableRow();
            }
            if($('#list > tbody > tr').length == 1)
            {
                $('#list').hide();
                $('#filter-list').hide();
                $warning.show();
            }

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

    $('#delete-frequest').ajaxForm(options);

    $('#modal-delete-form-submit').click( function (e) {
     e.preventDefault();
     $('#delete-frequest').submit();
    });


    $(document).on('click','.activate-modal-btn', function (e) {
        frquestId = $(this).data('id');
        $('#form_activate_id').val(frquestId);
        route = Routing.generate('frequest_activate', { "id": frquestId });
        $('#activate-frequest').attr('action', route);
        $remove = $(this);
    });

    // Borrado de peticiones de amistad
    var options = {
        dataType: 'json',
        success:    function(e) {
            $('#activateModal').modal('hide');
            status = ( e.success ) ? 'success' : 'error';

            $('.top-right').notify({
                    message: { text: e.message },
                    type: status,
                    fadeOut: { enabled: true, delay: 3000 }
            }).show();
            if(status == 'success' && typeof $remove !== 'undefined'){
                $remove.deleteTableRow();
            }
            if($('#list > tbody > tr').length == 1)
            {
                $('#list').hide();
                $('#filter-list').hide();
                $warning.show();
            }

            launchNotifications();
            activateNotifications();

        },
        error: function(e) {
            $('#activateModal').modal('hide');
            obj = jQuery.parseJSON(e.responseText);
            status = ( obj[0].success ) ? 'success' : 'info';
                        $('.top-right').notify({
                    message: { text: obj[0].message },
                    type: status,
                    fadeOut: { enabled: true, delay: 3000 }
            }).show();
        }
    };

    $('#activate-frequest').ajaxForm(options);

    $('#modal-activate-form-submit').click( function (e) {
     e.preventDefault();
     $('#activate-frequest').submit();
    });

});