$(document).ready(function() {
    menuColor('li#freq_ops_recibed');
    $('#deleteModalWrap').appendTo(document.body);
    if ($(document.body).children('#deleteModalWrap').length>1) $(document.body).children('#deleteModalWrap:gt(0)').remove();
    $('#activateModalWrap').appendTo(document.body);
    if ($(document.body).children('#activateModalWrap').length>1) $(document.body).children('#activateModalWrap:gt(0)').remove();
});