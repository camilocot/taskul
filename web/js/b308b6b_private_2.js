$(document).ready(function(){

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
                activateProgessBar();
              }
           });
        }
  });

  $.Mustache.add('get_notifications-html', $('#get_notifications-html').html());
  $.Mustache.add('api_list_task_status-html', $('#api_list_task_status-html').html());

  $(window).bind('statechange',function(){
    $('li.home > a').removeClass('ajaxy');
  });

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


  launchNotifications();
  setInterval('launchNotifications',60000);

  /* Para eliminar las filas de las tablas al pulsar sobre el boton */
  $.fn.deleteTableRow = function ()
  {
    $current = $(this).parents('tr:first');

    if ($current.hasClass('footable-row-detail')){
        //get the previous row and add it with the current row to be removed later
        $remove = $current.add($current.prev());
    } else {
        //get the next row after the current row and check if it's a detail row
        var $next = $current.next();
        //if the next row is a detail row or not
        if ($next.hasClass('footable-row-detail')){
            //get the next row and add it with the current row to be removed later
            $remove = $current.add($next);
        } else {
            //we can't find a detail row so just remove the current row later
            $remove = $current;
        }
    }
    $remove.css("background", "red");
    $remove.fadeOut(function() {
        $remove.remove();
    });
  }

});

var generateNotification = [];
var $remove; // Elemento a eliminar de las tablas

function launchNotifications()
{
  $.each($('.notifnumber'), function(){
    $(this).trigger('notificationUpdate');
  });
}

function activateNotifications()
{
  for (var i = generateNotification.length - 1; i >= 0; i--) {
    generateNotification[i] = true;
  }
}

function activateProgessBar(){
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