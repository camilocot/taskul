$(document).ready(function() {

	$warning = $('.warning:first');
    if ($('#list > tbody > tr').length > 0)
        $warning.hide();
    else {
        $warning.show();
        $('#filter-list').hide();
        $('#list').hide();
    }

    $remove = null;
    $('.footable').footable();
    $('.clear-filter').click(function (e) {
        e.preventDefault();
        $('table').trigger('footable_clear_filter');
    });

});
$(document).ready(function() {
    menuColor('li#freq_ops_sended');
});