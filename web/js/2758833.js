$(document).ready(function() {

    $('#fileupload').uniform();
    $('#fileupload').fileupload({
        dataType: 'json',
        done: function (e, data) {
            if(data.result.success === true)
                $.each(data.result.files, function (index, file) {

                    route = Routing.generate('api_download_file', { "id": file.id });
                    var view = {
                      route: route,
                      file: file
                    };
                    $('#list > tbody:last').append($.Mustache.render('add-file-html', view));
                    $('#no-files').hide();
                    if($('#list').is(':hidden'))
                        $('#list').show();
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

    $.Mustache.add('add-file-html', $('#add-file-html').html());
    if ($('#no-files').length > 0)
        $('#list').hide();

    $('body').on('delete-submit', function(event) {
        if($('#list > tbody > tr').length == 1)
        {
            $('#list').hide();
            $('#no-files').show();
        }
    });

    $remove = null;
    $('.footable').footable();
});

