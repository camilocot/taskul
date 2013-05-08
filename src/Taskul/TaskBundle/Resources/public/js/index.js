$(document).ready(function() {

    $remove = null;
    $('.footable').footable();
    $('.clear-filter').click(function (e) {
        e.preventDefault();
        $('table').trigger('footable_clear_filter');
    });
    menuColor('li#task_ops_list');

});