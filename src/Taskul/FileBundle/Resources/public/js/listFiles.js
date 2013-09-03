$(document).ready(function() {

    $('#fileupload').uniform();
    $('#fileupload').fileupload({
        dataType: 'json',
        add: function (e, data) {
            $(".progress-indicator").fadeIn(500);
            data.submit();
        },
        done: function (e, data) {
            if(data.result.success === true)
                $.each(data.result.files, function (index, file) {

                    route = Routing.generate('api_download_file', { "id": file.id });
                    deleteroute = Routing.generate('api_delete_task_file', {'idDocument': file.id , 'idTask': file.taskid } );
                    var view = {

                      route: route,
                      deleteroute: deleteroute,
                      file: file
                    };
                    $('#list > tbody:last').append($.Mustache.render('add-file-html', view));
                    if($('#list').is(':hidden'))
                        $('#list').show();
                    toggleWarning();
                    refreshQuota();
                });
            else {
                var n =notificacion(data.result.message, 'error');
            }
            $(".progress-indicator").fadeOut(500);
        },
        fail: function (e,data) {
            try {
                obj = jQuery.parseJSON(data.jqXHR.responseText);
                msg = obj.message;
            } catch (ex) {
                msg = $.t('msg.error.maxupload');
            }
            $(".progress-indicator").fadeOut(500);
            var n = notificacion(msg, 'error', 5000 );
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

    $.Mustache.add('add-file-html', $('#add-file-html').html());

    $remove = null;
    $('.footable').footable();

    $('span.filename').data('i18n','msg.uniform.fileButtonHtml').i18n();
    $('span.action').data('i18n','msg.uniform.fileDefaultHtml').i18n();

    $('#dial-quota').bind('deleterow',function(e){
        refreshQuota();
    });
});

