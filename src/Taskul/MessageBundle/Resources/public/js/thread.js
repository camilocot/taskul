$(document).ready(function(){
	new nicEditor({fullPanel : true}).panelInstance('message_body');
	 $('#form_thread').submit(function(event){
        $(this).ajaxSubmit({
            success: function (data){
                if(data.success === true){
                	route = Routing.generate('fos_message_thread_view', {threadId : data.threadid});
                    title = 'Sent';
                    loadPage(route);
                    $('#content').html(data);
                }
            },
            error: function(jqXHR,textStatus,errorThrown){
                alert(jqXHR.responseText.message);
            }
        });
        // return false to prevent normal browser submit and page navigation
        return false;
    });

    $('button[type=submit]').click(function(event){
    	event.preventDefault();
    	ne = nicEditors.findEditor('message_body');
        $('#message_body').val(ne.getContent());
        $('#form_thread').submit();
    });
});