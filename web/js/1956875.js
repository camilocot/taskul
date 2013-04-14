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
    });
    menuColor('li#freq_ops_recibed');
    $('.delete-modal-btn').deleteModal('frequest_delete',$('#delete-frequest'), ['id'] );
});