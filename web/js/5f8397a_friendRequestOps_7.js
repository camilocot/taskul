$(document).ready(function() {
    clearMenuActive('friendreq_ops');

    $('#modal-delete-form-submit').click( function (e) {
     e.preventDefault();
     $('#delete-frequest').submit();
    });

    $('.activate-modal-btn').click( function (e) {
        e.preventDefault();
        frquestId = $(this).data('id');
        $('#form_activate_id').val(frquestId);
        route = Routing.generate('frequest_activate', { "id": frquestId });
        $('#activate-frequest').attr('action', route);

    });

    $('#modal-activate-form-submit').click( function (e) {
     e.preventDefault();
     $('#activate-frequest').submit();
    });


    var nTr = null; //Para eliminar fila del datatables
});