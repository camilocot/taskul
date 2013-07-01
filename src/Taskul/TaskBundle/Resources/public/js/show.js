$(document).ready(function() {
    $('body').on('delete-submit', function(event) {
        $('a.btn-success').remove();
        $('a.btn-back-list').trigger('click');
    });
});