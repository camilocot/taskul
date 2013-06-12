$(document).ready(function() {
    $('body').on('delete-submit', function(event) {
        $('a.btn-success').remove();
        $('a.btn-back-list').trigger('click');
    });
	$(document).on('click','.submit-comment', function(event){
		$form = $(this).parents('form:first');
        textareaid = $form.data('textarea-id');
        ne = nicEditors.findEditor(textareaid);
        content = ne.getContent().replace(/^(<p\>(\&nbsp\;|(\s)*)<\/p\>|<br(\s\/)?\>)$/g,'');
        if(content === '')
        {
			notificacion($.t('msg.comment.empty') ,'error');
			event.preventDefault();
			return false;
        }
        else
        {
			notificacion($.t('msg.comment.success_send'),'success');
			$('#'+textareaid).val(content);
			return true;
        }
    });
});