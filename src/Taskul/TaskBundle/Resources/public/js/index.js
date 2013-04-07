$(document).ready(function() {
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

    $('.datatable').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
        "sPaginationType": "bootstrap",
        "bAutoWidth": false,
        "aoColumns" : [
            { sWidth: '10%' },
            { sWidth: '30%' },
            { sWidth: '5%' },
            { sWidth: '40%' },
            { sWidth: '15%', "bSortable": false  }
        ],
        "oLanguage": {
            "sLengthMenu": "_MENU_ records per page"
        }
    } );

    menuColor('li#task_ops_list');

});