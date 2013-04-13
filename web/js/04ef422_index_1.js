$(document).ready(function() {

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