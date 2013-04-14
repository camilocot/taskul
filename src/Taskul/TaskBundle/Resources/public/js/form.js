$(document).ready(function(){

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
            if(data.length == 0)
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
        name    = group.data('toggle-name');
        var hidden  = $('input[name="' + name + '"]', form);
        $('button', group).each(function(){
            var button = $(this);
            button.on('click', function(){
              hidden.val($(this).val());
            });
            if(button.val() == hidden.val()) {
                button.addClass('active');
            }
        });
    });

    // Crear nueva tarea, redirecciona a ver o a aañadir ficheros
    $('#form_task').submit(function(event){
        var valSubmit = $("button[type=submit][clicked=true]").val();
        ne = nicEditors.findEditor('task_description');
        $('#task_description').val(ne.getContent());
        $(this).ajaxSubmit({
            success: function (data){
                if(data.success === true){
                    taskId = data.id;
                    title = null;
                    if(valSubmit == 1)
                    {
                        route = Routing.generate('api_get_task_files', { "id": taskId });
                        title = 'Asignar ficheros';
                    }
                    else
                    {
                        route = Routing.generate('api_get_task', { "id": taskId });
                        title = 'Mostrar tarea';
                    }
                    loadPage(route);
                    History.pushState(null,title,route);
                }else{
                    console.log(data);
                }
            },
            error: function(jqXHR,textStatus,errorThrown){
                alert(jqXHR.responseText.message);
                console.log("Error of data:", jqXHR);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });

    menuColor('li#task_ops_new');
    new nicEditor({fullPanel : true}).panelInstance('task_description');

});
