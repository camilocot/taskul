$(document).ready(function(){

  /* COntenido del listado de las notificaciones */
  $('.notiftoggle').click(function() {
        // Only call notifications when opening the dropdown
        $this = $(this);
        var context = $this.data('context');
        var dynroute = $this.data('route-content');
        /* Si el numero de notificaciones no ha cambioado no se actualiza */
        if( (typeof generateNotification[context] === 'undefined' || generateNotification[context] ) && !$(this).parent('li').hasClass('open')) {
          route = Routing.generate(dynroute, { "context": context });
           $.ajax({
              type: "GET",
              url: route,
              async: false,
              dataType: "json",
              success: function(data) {
                // Añadimos una funcion para limitar el tamano
                data.limitLength = function() {
                  return function(text, render) {
                    return render(text).substr(0,10) + '...';
                  };
                };
                $this.siblings('ul:first').mustache(dynroute+'-html', data);
                generateNotification[context] = false;
                activateProgessBar();
              }
           });
        }
  });

  $.Mustache.add('get_notifications-html', $('#get_notifications-html').html());
  $.Mustache.add('api_list_task_status-html', $('#api_list_task_status-html').html());
  $.Mustache.add('taskul_message_list_unread_messages-html', $('#taskul_message_list_unread_messages-html').html());

  /* Numero de notificaciones */
  $('.notifnumber').bind('notificationUpdate',function() {
    $this = $(this);
    var context = $this.parent().data('context');
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
  //setInterval('launchNotifications',60000);

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
        $('.helpinfo').remove();
        toggleWarning(); // ajaxyfy.js
    });
    $('body').trigger('delete-submit');
  };

  /* Quitamos la clase ajaxy del dashboard */
  $(window).bind('statechange',function(){
    $('li.home > a').removeClass('ajaxy');
  });

  /* Botones que activan los modales para la confirmacion de envio de formularios */
  $(document).on( 'click', '.modal-button', function (e) {
        $remove = $(this);
        id = $remove.data('id');
        target = $remove.data('target');
        $form = $(target).next('form');
        inputId = $form.data('input-id');
        formAction = $form.data('action');
        redirect = $remove.data('redirect');
        formAction = Routing.generate(formAction, { "id": id });
        $form.removeData('redirect');
        $(inputId).val(id);
        $form.attr('action', formAction);
        $form.data('redirect',redirect);
    });

    /* Boton de confirmacion del propio modal que envia el formulario*/
    $(document).on('click','.btn-confirm-modal', function (e) {
        e.preventDefault();
        $this = $(this);
        $form = $this.closest('div.modal').next('form');
        $form.submit();
    });

});

var generateNotification = [];
var $remove; // Elemento a eliminar de las tablas

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