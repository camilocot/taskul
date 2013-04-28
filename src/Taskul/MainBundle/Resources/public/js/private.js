$(document).ready(function(){
  var generateNotification = [];

  $('.notiftoggle').click(function() {
        // Only call notifications when opening the dropdown
        $this = $(this);
        context = $this.data('context');
        var dynroute = $this.data('route-content');
        if( (typeof generateNotification[context] === 'undefined' || generateNotification[context] ) && !$(this).parent('li').hasClass('open')) {
          route = Routing.generate(dynroute, { "context": context });
           $.ajax({
              type: "GET",
              url: route,
              async: false,
              dataType: "json",
              success: function(data) {
                $this.siblings('ul:first').mustache(dynroute+'-html', data);
                generateNotification[context] = false;
                if($(".taskProgress")) {

                        $(".taskProgress").each(function(){

                                var endValue = parseInt($(this).html());

                                $(this).progressbar({
                                        value: endValue
                                });

                                $(this).parent().find(".percent").html(endValue + "%");

                        });

                }

              }
           });
        }
  });

  $.Mustache.add('get_notifications-html', $('#get_notifications-html').html());
  $.Mustache.add('api_list_task_status-html', $('#api_list_task_status-html').html());

  $('li.home > a').removeClass('ajaxy');

  /* Numero de notificaciones */
  $('.notifnumber').bind('notificationUpdate',function() {
    $this = $(this);
    context = $this.parent().data('context');
    dynroute = $this.parent().data('route');
    route = Routing.generate(dynroute, { "context": context });
    $.ajax({
      method:'get',
      url: route,
      async: false,
      success:function(data){
        if(data.success) {
          total = $this.text();
          if(total != data.total) {
            $this.text(data.total);
            generateNotification[context] = true;
          }
        }
      }
    });
  });

  $('.notifnumber').trigger('notificationUpdate');
  setInterval(function(){
       $('.notifnumber').trigger('notificationUpdate');
  },60000);

});