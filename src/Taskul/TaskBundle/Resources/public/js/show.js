$(document).ready(function() {
    clearMenuActive('task_ops');

    $('#deleteModalWrap').appendTo(document.body);
    if ($(document.body).children('#deleteModalWrap').length>1) $(document.body).children('#deleteModalWrap:gt(0)').remove();

    $('body').on('delete-submit', function(event) {
        $('a.btn-success').remove();
        $('a.btn-back-list').trigger('click');
    });
});