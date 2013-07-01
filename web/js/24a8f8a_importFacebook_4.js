function requestCallback(response) {
  // Handle callback here
  if(response !== null){
    $('input#form_sended').val(response.request);
    $('form#form').submit();
  }
}

$(document).ready(function(){
  $('#sendinvfb').click(function(){
    var selectedItems = [];
    $("input[type=checkbox].friends:checked").each(function(){
      selectedItems.push($(this).val());
    });
    msg = $('#form_message').val();
    $('#form_message').val($('<div/>').text(msg).html());

    if(0 === selectedItems.length)
     notificacion($.t('msg.friendrequest.facebook_empty_select'), 'error');
    else if (msg === '')
      notificacion($.t('msg.friendrequest.facebook_empty_msg'), 'error');
    else
    {
      FB.ui({method: 'apprequests',
        message: msg,
        to: selectedItems
      }, requestCallback);
    }
  });
  $('input.pretty').prettyCheckable();
  menuColor('li#freq_ops_import');

});
