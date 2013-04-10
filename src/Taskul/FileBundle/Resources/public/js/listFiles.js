$(document).ready(function() {


    $('.datatable').dataTable({
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
        "sPaginationType": "bootstrap",
        "bAutoWidth": false,
       "aoColumns" : [
        { sWidth: '90%' },
        { sWidth: '10%', "bSortable": false  }
        ],

        "oLanguage": {
            "sLengthMenu": "_MENU_ records per page"
        }
    } );

    $('#fileupload').uniform();
    $('#fileupload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            if(data.result.success == true)
                $.each(data.result.files, function (index, file) {
                    route = Routing.generate('api_download_file', { "id": file.id });
                    $('#list').dataTable().fnAddData( [
                        file.name, '<a title="show" class="btn btn-info" href="'+route+'"><i class="icon-zoom-in icon-white"></i></a><button class="btn btn-danger delete-modal-btn" type="button" data-task-id="'+ file.taskid +'" data-document-id="'+ file.id +'" data-target="#deleteModal" data-toggle="modal"><i class="icon-trash icon-white"></i></button> '
                        ] );
                    refereshQuota();
                });
            else {
                var n =noty({text: data.result.message, type: 'error', layout: 'top'});
            }


        },
        fail: function (e,data) {
            obj = jQuery.parseJSON(data.jqXHR.responseText);
            var n =noty({text: obj.message, type: 'error', layout: 'top'});
        }
    });

$(".orangeCircle").knob({
    'min':0,
    'max':100,
    'readOnly': true,
    'width': 120,
    'height': 120,
    'fgColor': '#FA5833',
    'dynamicDraw': true,
    'thickness': 0.2,
    'tickColorizeValues': true,
    'skin':'tron'
});

});

