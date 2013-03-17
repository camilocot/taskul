
function sendRequestViaMultiFriendSelector() {
  FB.ui({method: 'apprequests',
    message: 'My Great Request'
  }, requestCallback);
}

function requestCallback(response) {
        // Handle callback here
        if(response != null){
          $('input#form_sended').val(response.request);
          $('form#form').submit();
        }
      }

      $(document).ready(function(){
        $('#sendinvfb').click(function(){
          var selectedItems = new Array();
          $("input[type=checkbox].friends:checked").each(function(){
            selectedItems.push($(this).val());
          });
          msg = $('#form_message').val();
          $('#form_message').val($('<div/>').text(msg).html());

          if(0 == selectedItems.length || msg == '')
           alert('selecciona alguno');
         else
         {
          FB.ui({method: 'apprequests',
            message: msg,
            to: selectedItems,
          }, requestCallback);
        }
      });
        $('input.pretty').prettyCheckable();
      });
