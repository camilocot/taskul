$(document).ready(function(){

    $('#deleteModalWrap').appendTo(document.body);
    if ($(document.body).children('#deleteModalWrap').length>1) $(document.body).children('#deleteModalWrap:gt(0)').remove();

    if ($.fn.datepicker) {
        $('#btn-trash').click(function(e){
            $dp.datepicker('hide');
            $('input#task_dateEnd').val('');
        });
        $dp = $('#datepick').datepicker()
        .on('changeDate', function(ev){
            $('input#task_dateEnd').val($('#datepick').data('date'));
            $('#datepick').datepicker('hide');
        });
        $('input#task_dateEnd').datepicker();
    }

    if ($.fn.select2) {
        $("select#task_members").select2();
        route = Routing.generate('api_get_tags');
        $.getJSON(route, function(data) {
            if(data.length === 0)
                data = null;

            $("input#task_tags").select2({
                tags: data,
                tokenSeparators: [","]
            });
        });

    }

    // Botones de los estados de las tareas
    $('div.btn-group').each(function(){
        group   = $(this);
        form    = group.parents('form').eq(0);
        nameGroup    = group.data('toggle-name');
        var hidden  = $('input[name="' + nameGroup + '"]', form);
        $('button', group).each(function(){
            var button = $(this);
            button.on('click', function(){
                /* Cambiamos el slider */
                processSlide($(this).val());
                hidden.val($(this).val());
            });
            if(button.val() == hidden.val()) {
                button.addClass('active');
            }
        });
    });

    $("#task_percent").simpleSlider({'range': [1,100], 'step': 1});



    $('#minimize-comments').trigger('click');
    new nicEditor({fullPanel : true}).panelInstance('task_description');

    /* Percent Slider */
    $("#task_percent").bind("slider:changed", function (event, data) {
    // The currently selected value of the slider
        $(this).val(data.value);
        $('#text-percent').text('('+data.value+'%)');
        /* Activamos el boton adecuado */
        activateStatusButton(data.value);

    });

    $('label[for=task_percent]').append(' <span id="text-percent">('+$("#task_percent").val()+'%)</span>');

});

function processSlide(val)
{

    switch(val)
    {
        case 'inprogress':
            $("#task_percent").simpleSlider("setValue", 50);
            $("#task_percent").val(50);
            break;
        case 'todo':
            $("#task_percent").simpleSlider("setValue", 1);
            $("#task_percent").val(1);
            break;
        case 'done':
            $("#task_percent").simpleSlider("setValue", 100);
            $("#task_percent").val(100);
            break;
    }
    return true;
}

function activateStatusButton(percent)
{
            /* Activamos el boton del estado cuando sea necesario */
            group   = $('div.btn-group:first');
            nameGroup    = group.data('toggle-name');
            form    = group.parents('form').eq(0);
            hidden  = $('input[name="' + nameGroup + '"]', form);
            val = hidden.val();
            $('button',group).removeClass('active');
            if(percent == 1 && val != 'todo'){
                $('button[value=todo]',group).addClass('active');
                hidden.val('todo');
            }
            else if(percent == 100 && val != 'done'){
                $('button[value=done]',group).addClass('active');
                hidden.val('done');
            }
            else {
                $('button[value=inprogress]',group).addClass('active');
                hidden.val('inprogress');
            }

        return true;
}
