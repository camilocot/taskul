// Avoid `console` errors in browsers that lack a console.
(function() {
    var method;
    var noop = function () {};
    var methods = [
        'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error',
        'exception', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log',
        'markTimeline', 'profile', 'profileEnd', 'table', 'time', 'timeEnd',
        'timeStamp', 'trace', 'warn'
    ];
    var length = methods.length;
    var console = (window.console = window.console || {});

    while (length--) {
        method = methods[length];

        // Only stub undefined methods.
        if (!console[method]) {
            console[method] = noop;
        }
    }
}());


function notificacion(message, status)
{
    $('.top-right').notify({
                    message: { text: message },
                    type: status,
                    fadeOut: { enabled: true, delay: 3000 }
    }).show();
}

function menuColor(selector) {
    $liactive = $(selector);
    $parent = $liactive.parent();
    $parent.find('a').css('color','');
    $liactive.find('a').css('color','#0088CC');
    $liactive.addClass('active');
}

function refereshQuota()
{
    $.getJSON(Routing.generate('api_get_quota'), function(data) {
        actualValue = $('#dial-quota').val();
        if(data.success == true && actualValue != data.current_quota)
            $('#dial-quota').val(data.current_quota).trigger('change');
    });
}

function clearMenuActive(ulid)
{
	$('ul#'+ulid).show();
    $('ul#'+ulid).find('a').addClass('ajaxy');

    $liactive = $('ul#'+ulid).children('li.active');
    $liactive.find('a').css('color','');
    $liactive.removeClass('active');
}

$(document).ready(function(){
	// Almacena el boton pulsado para el envio
    $("form button[type=submit]").on('click',function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $('.boostrap-tp').tooltip({
        "trigger":"click"
    }).click(function(e){
        e.preventDefault();
    });


        /* ---------- Login Box Styles ---------- */
    if($(".login-box")) {

        $("#username").focus(function() {

            $(this).parent(".input-prepend").addClass("input-prepend-focus");

        });

        $("#username").focusout(function() {

            $(this).parent(".input-prepend").removeClass("input-prepend-focus");

        });

        $("#password").focus(function() {

            $(this).parent(".input-prepend").addClass("input-prepend-focus");

        });

        $("#password").focusout(function() {

            $(this).parent(".input-prepend").removeClass("input-prepend-focus");

        });

    }

    /* Desplegamos los ul del menu */
    $("li.active").parent('ul').css('display','block');
    $("li.active").children().css('color','#0088cc');
    template_functions();
    /*init_masonry();
    sparkline_charts();
    charts();
    calendars();
    growlLikeNotifications();
    widthFunctions();
    circle_progess();*/


});

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


    /* ---------- Submenu  ---------- */

    $('.dropmenu').click(function(e){

        e.preventDefault();

        $(this).parent().find('ul').slideToggle();
        $(this).find('i').toggleClass('icon-chevron-down').toggleClass('icon-chevron-up');

    });
    /* ---------- Disable moving to top ---------- */
    $('a[href="#"][data-top!=true]').click(function(e){
        e.preventDefault();
    });
    /* ---------- Uniform ---------- */
    $("input:checkbox, input:radio, input:file").not('[data-no-uniform="true"],#uniform-is-ajax').uniform();

    /* ---------- Tooltip ---------- */
    $('[rel="tooltip"],[data-rel="tooltip"]').tooltip({"placement":"bottom",delay: { show: 400, hide: 200 }});

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