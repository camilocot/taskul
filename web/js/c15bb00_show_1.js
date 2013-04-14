$(document).ready(function() {
    $('.delete-modal-btn').deleteModal('frequest_delete',$('#delete-frequest'), ['id'], Routing.generate('frequest'));
});