$(document).ready(function() {
    clearMenuActive('task_ops');
    menuColor('li#task_ops_list');
    $('#deleteModalWrap').appendTo(document.body);
    if ($(document.body).children('#deleteModalWrap').length>1) $(document.body).children('#deleteModalWrap:gt(0)').remove();
    $(document).on('click','.tags',function(e){
        e.preventDefault();
        $('#filter').val($(this).text()).trigger('keyup');
    });
});