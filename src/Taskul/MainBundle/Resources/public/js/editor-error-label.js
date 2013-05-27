        $(document).ready(function(){
            var editor = $('.wysihtml5').data("wysihtml5");

            if(typeof editor !== 'undefined'){
                  editor = editor.editor;
                  $(".validate").validate({
                       highlight: function(element) {
                            $(element).closest('.control-group').removeClass('success').addClass('error');
                      },
                      success: function(element) {
                        element
                        .text('OK!').addClass('valid')
                        .closest('.control-group').removeClass('error').addClass('success');
                  }
            });
                  $('#form_task').submit(function(){
                        val = $("button[type=submit][clicked=true]").val();
                        $('button[name=goto_upload]').val(val);

                        return onChangeEditor();
                  });
                  editor.on("change", onChangeEditor);
            }

            $("form button[type=submit]").click(function() {
            	$("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            	$(this).attr("clicked", "true");
            });

            $('#login-form').ajaxForm(function(e) {
            	status = ( e.success ) ? 'success' : 'error';
            	noty({text: e.message, type: status, layout: 'top'});
            	if(status == 'success')
            		setTimeout(function() {
            			window.location.href = "{{ path('api_get_tasks') }}";
            		}, 2000);
            });

      });

        function onChangeEditor()
        {
           label = $('label[for=task_description].error');
           emp = editor.isEmpty();
           if(label.length > 0 && !emp){
                label.hide();
                return true;
          }else if (label.length === 0 && emp){
                $('iframe.wysihtml5-sandbox').after('<label for="task_description" class="error">This field is required.</label>');
          }else{
                label.show();
          }
          return false;
    }