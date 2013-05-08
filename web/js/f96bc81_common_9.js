var $remove;

$(document).ready(function() {

    $(document).on( 'click', '.modal-button', function (e) {
        $remove = $(this);
        id = $remove.data('id');
        target = $remove.data('target');
        $form = $(target).next('form');
        inputId = $form.data('input-id');
        formAction = $form.data('action');
        redirect = $remove.data('redirect');
        formAction = Routing.generate(formAction, { "id": id });

        $form.removeData('redirect');
        $(inputId).val(id);
        $form.attr('action', formAction);
        $form.data('redirect',redirect);
    });


    $(document).on('click','.btn-confirm-modal', function (e) {
        e.preventDefault();
        $this = $(this);
        $form = $this.closest('div.modal').next('form');
        $form.submit();
    });


});
