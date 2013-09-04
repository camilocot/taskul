$(document).ready(function(){
	new nicEditor({fullPanel : true}).panelInstance('message_body');

    $('button[type=submit]').click(function(event){
        event.preventDefault();
        ne = nicEditors.findEditor('message_body');
        $('#message_body').val(ne.getContent());
        $('#form_thread').submit();
    });
});