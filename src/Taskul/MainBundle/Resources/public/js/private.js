$(document).ready(function(){

  /* Si esstamos forzando a cambiar la clave se desactivan los a href */
  if(window.location.href.match(/change_password$/)){
    $('a').click(function(e){
      e.preventDefault();
      notificacion($.t('msg.change.password') ,'error');
      return false;
    });
  }
  /* Contenido del listado de las notificaciones */
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
      var $this = $(this);
      var context = $this.parent().data('context');
      dynroute = $this.parent().data('route');
      route = Routing.generate(dynroute, { "context": context });
      $.ajax({
        method:'get',
        url: route,
        async: true,
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
  var intervalID = setInterval('launchNotifications', 600000);

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

    $('#overlay').fadeOut(500);

    $(document).on('click','.btn-close',function(e){
        e.preventDefault();
        $(this).parent().parent().parent().fadeOut();
    });

    $(document).on('click','.btn-minimize', function(e){
        e.preventDefault();
        var $target = $(this).parent().parent().next('.box-content');
        if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
        else                       $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
        $target.slideToggle();
    });

        // Almacena el boton pulsado para el envio
    $("form button[type=submit]").on('click',function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    /* ---------- Submenu  ---------- */

    $(document).on('click','.dropmenu',function(e){

        e.preventDefault();
        $this = $(this);

        $this.parent().find('ul').slideToggle();
        $this.find('i').toggleClass('icon-chevron-down').toggleClass('icon-chevron-up');

        $siblings = $this.parent().siblings();
        $siblings.find('ul').slideUp().children('li.active').removeClass('active');
        $siblings.find('i').addClass('icon-chevron-down').removeClass('icon-chevron-up');

    });

    template_functions();
    widthFunctions();

    // Comments actions loading
    $( document ).ajaxSend(function( event, jqxhr, settings ) {
      var n=settings.url.indexOf("api/threads");
      if(settings.type === 'POST' && n > 0)
        if(!$(".progress-indicator").is(':visible'))
          $(".progress-indicator").fadeIn(500);
    }).ajaxComplete(function( event, xhr, settings ) {
      var n=settings.url.indexOf("api/threads");
      if(settings.type === 'POST' && n > 0)
        if($(".progress-indicator").is(':visible'))
          $(".progress-indicator").fadeOut(500);
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
      var endValue = parseInt($(this).html(),10);
      $(this).progressbar({
              value: endValue
      });
      $(this).parent().find(".percent").html(endValue + "%");
    });
  }
}

function refreshQuota()
{
    $.getJSON(Routing.generate('api_get_quota'), function(data) {
        actualValue = $('#dial-quota').val();
        if(data.success === true && actualValue != data.current_quota){
            $('#dial-quota').val(data.current_quota).trigger('change');
        }
    });
}


function launchNotifications()
{
  $.each($('.notifnumber'), function(){
    $(this).trigger('notificationUpdate');
  });
}

/* ---------- Numbers Sepparator ---------- */

function numberWithCommas(x) {
    x = x.toString();
    var pattern = /(-?\d+)(\d{3})/;
    while (pattern.test(x))
        x = x.replace(pattern, "$1.$2");
    return x;
}

/* ---------- Template Functions ---------- */

function template_functions(){

    /* ---------- Disable moving to top ---------- */
    $('a[href="#"][data-top!=true]').click(function(e){
        e.preventDefault();
    });
    /* ---------- Uniform ---------- */
    $("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

    /* ---------- Tooltip ---------- */
    if (!smartphone.matches && !checkMobile()) {
      $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({ "placement":"bottom" });
    }

}
/* ---------- Page width functions ---------- */

$(window).bind("resize", widthFunctions);

function widthFunctions( e ) {
    var winHeight = $(window).height();
    var winWidth = $(window).width();

    if (winHeight) {

        $("#content").css("min-height",winHeight);

    }

    if (winWidth < 980 && winWidth > 767) {

        if($(".main-menu-span").hasClass("span2")) {

            $(".main-menu-span").removeClass("span2");
            $(".main-menu-span").addClass("span1");

        }

        if($("#content").hasClass("span10")) {

            $("#content").removeClass("span10");
            $("#content").addClass("span11");

        }


        $("a").each(function(){

            if($(this).hasClass("quick-button-small span1")) {

                $(this).removeClass("quick-button-small span1");
                $(this).addClass("quick-button span2 changed");

            }

        });

        $(".circleStatsItem").each(function() {

            var getOnTablet = $(this).parent().attr('onTablet');
            var getOnDesktop = $(this).parent().attr('onDesktop');

            if (getOnTablet) {

                $(this).parent().removeClass(getOnDesktop);
                $(this).parent().addClass(getOnTablet);

            }

        });

        $(".box").each(function(){

            var getOnTablet = $(this).attr('onTablet');
            var getOnDesktop = $(this).attr('onDesktop');

            if (getOnTablet) {

                $(this).removeClass(getOnDesktop);
                $(this).addClass(getOnTablet);

            }

        });

    } else {

        if($(".main-menu-span").hasClass("span1")) {

            $(".main-menu-span").removeClass("span1");
            $(".main-menu-span").addClass("span2");

        }

        if($("#content").hasClass("span11")) {

            $("#content").removeClass("span11");
            $("#content").addClass("span10");

        }

        $("a").each(function(){

            if($(this).hasClass("quick-button span2 changed")) {

                $(this).removeClass("quick-button span2 changed");
                $(this).addClass("quick-button-small span1");

            }

        });

        $(".circleStatsItem").each(function() {

            var getOnTablet = $(this).parent().attr('onTablet');
            var getOnDesktop = $(this).parent().attr('onDesktop');

            if (getOnTablet) {

                $(this).parent().removeClass(getOnTablet);
                $(this).parent().addClass(getOnDesktop);

            }

        });

        $(".box").each(function(){

            var getOnTablet = $(this).attr('onTablet');
            var getOnDesktop = $(this).attr('onDesktop');

            if (getOnTablet) {

                $(this).removeClass(getOnTablet);
                $(this).addClass(getOnDesktop);

            }

        });

    }

}