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

                $this.siblings('ul:first').text('').mustache(dynroute+'-html', data);
                generateNotification[context] = false;

                $('.notification-content').click( function (e){
                  $('.dropdown.open .dropdown-toggle').dropdown('toggle');
                  generateNotification[context] = true;
                });
                activateProgessBar();
              }
           });
        }
  });

  $.Mustache.add('get_notifications-html', $('#get_notifications-html').html());
  $.Mustache.add('api_list_task_status-html', $('#api_list_task_status-html').html());
  $.Mustache.add('taskul_message_list_unread_messages-html', $('#taskul_message_list_unread_messages-html').html());

  /* Numero de notificaciones */
  $('.notifnumber').each(function(){
    $(this).bind('notificationUpdate',function() {
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
    $('#dial-quota').trigger('deleterow');
  };

  /* Quitamos la clase ajaxy del dashboard */
  $(window).bind('statechange',function(){
    $('li.home > a').removeClass('ajaxy');
  });

  /* Botones que activan los modales para la confirmacion de envio de formularios */
  $(document).on( 'click', '.modal-button', function (e) {
        e.preventDefault();
        $remove = $(this);
        $form = $($remove.data('target')).next('form');
        $form.attr('action', $remove.data('href'));

        // Se le asocia la routa de redirección si tiene
        $form.children('input[name=redirect]').val($remove.data('redirect'));
    });

    /* Boton de confirmacion del propio modal que envia el formulario*/
    $(document).on('click','.btn-confirm-modal', function (e) {
        e.preventDefault();
        $this = $(this);
        $form = $this.closest('div.modal').next('form');
        $form.submit();
    });
    /* Para comprobar si el nicedit tiene contenido */
    $(document).on('click','.submit-niceditor', function(event){
      $form = $(this).parents('form:first');
        textareaid = $form.data('textarea-id');
        ne = nicEditors.findEditor(textareaid);
        content = ne.getContent().replace(/^(<p\>(\&nbsp\;|(\s)*)<\/p\>|<br(\s\/)?\>)$/g,'');
        if(content === '')
        {
          notificacion($.t('msg.comment.empty') ,'error');
          event.preventDefault();
          return false;
        }
        else
        {
          if(!$form.hasClass('ajaxform')) {
            notificacion($.t('msg.comment.success_send'),'success'); // Esto es para los comentarios de la tareas
          }
          $('#'+textareaid).val(content);
          $form.submit();
          ne.setContent('');
          return false;
        }
    });

    $(".progress-indicator").fadeOut(500);

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
      var endValue = parseInt($(this).html(),10);
      $(this).progressbar({
              value: endValue
      });
      $(this).parent().find(".percent").html(endValue + "%");
    });
  }
}