$(document).ready(function() {
    menuColor('li#friends_index');
    $('#deleteModalWrap').appendTo(document.body);
    if ($(document.body).children('#deleteModalWrap').length>1) $(document.body).children('#deleteModalWrap:gt(0)').remove();
});