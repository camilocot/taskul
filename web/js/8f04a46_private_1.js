$(document).ready(function(){
  var generateNotification = [];


  $('.notiftoggle').click(function() {
        // Only call notifications when opening the dropdown
        $this = $(this);
        context = $this.data('context');
        if( (typeof generateNotification[context] === 'undefined' || generateNotification[context] ) && !$(this).parent('li').hasClass('open')) {
          route = Routing.generate('get_notifications', { "context": context });
           $.ajax({
              type: "GET",
              url: route,
              async: false,
              dataType: "json",
              success: function(data) {
                $this.siblings('ul:first').mustache('notification-html', data);
                generateNotification[context] = false;
              }
           });
        }
  });

  $.Mustache.add('notification-html', $('#notification-html').html());

  $('li.home > a').removeClass('ajaxy');

  /* Numero de notificaciones */
  $('.notifnumber').bind('notificationUpdate',function() {
    $this = $(this);
    context = $this.parent().data('context');
    route = Routing.generate('notification', { "context": context });
    $.ajax({
      method:'get',
      url: route,
      success:function(data){
        console.log($this.data('id'));
        if(data.success) {
          total = $this.text();
          if(total != data.total) {
            $this.text(data.total);
            generateNotification = true;
          }
        }
      }
    });
  });
  $('.notifnumber').trigger('notificationUpdate');
  setInterval(function(){
      $('.notifnumber').trigger('notificationUpdate');
  },50000);
});