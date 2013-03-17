$(document).ready(function() {

    $('.datatable').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
        "sPaginationType": "bootstrap",
         "bAutoWidth": false,
         "aoColumns" : [
            { sWidth: '60%' },
            { sWidth: '30%' },
            { sWidth: '10%', "bSortable": false  }
        ],
        "oLanguage": {
            "sLengthMenu": "_MENU_ records per page"
        }
    } );


    $('.delete-modal-btn').click( function (e) {
        frquestId = $(this).data('id');
        $('#form_delete_id').val(frquestId);
        route = Routing.generate('frequest_delete', { "id": frquestId });
        $('#delete-frequest').attr('action', route);

    });
    $('#modal-delete-form-submit').click( function (e) {
     e.preventDefault();
     $('#delete-frequest').submit();
    });

    $('.activate-modal-btn').click( function (e) {
        e.preventDefault();
        frquestId = $(this).data('id');
        $('#form_activate_id').val(frquestId);
        route = Routing.generate('frequest_activate', { "id": frquestId });
        $('#delete-frequest').attr('action', route);

    });
    $('#modal-activate-form-submit').click( function (e) {
     e.preventDefault();
     $('#delete-frequest').submit();
    });
});