$(document).ready(function() {
    $('#unsubscribeModalWrap').appendTo(document.body);
    if ($(document.body).children('#unsubscribeModalWrap').length>1) $(document.body).children('#unsubscribeModalWrap:gt(0)').remove();
});