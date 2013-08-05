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
    // $('.top-right').notify({
    //                 message: { text: message },
    //                 type: status,
    //                 fadeOut: { enabled: true, delay: 3000 }
    // }).show();
    notif = 'jNotify';
    switch (status)
    {
        case 'error':
            notif='jError';
            break;
        case 'success':
            notif='jSuccess';
            break;
    }
    window[notif](
        message,
        {
          autoHide : true, // added in v2.0
          clickOverlay : false, // added in v2.0
          MinWidth : 250,
          TimeShown : 3000,
          ShowTimeEffect : 200,
          HideTimeEffect : 200,
          LongTrip :20,
          HorizontalPosition : 'center',
          VerticalPosition : 'top',
          ShowOverlay : true,
          ColorOverlay : '#000',
          OpacityOverlay : 0.3,
          onClosed : function(){ // added in v2.0
          },
          onCompleted : function(){ // added in v2.0
          }
        });
        //launchNotifications();
}

$(document).ready(function(){
    var option = { resGetPath: 'messages/__lng__/__ns__.json' };
    $.i18n.init(option);
});

function checkMobile()
{
    return (( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent))?true:false);
}

var smartphone    = window.matchMedia( "(min-width: 320px)" ) && window.matchMedia( "(max-width: 1024px)" ),
      sp_landscape  = window.matchMedia( "(min-width: 321px)" ),
      sp_portrait   = window.matchMedia( "(max-width: 320px)" ),
      tablet        = window.matchMedia( "(min-width: 768px)" ) && window.matchMedia( "(max-width: 1024px)" ),
      tab_landscape = tablet && (window.orientation == 'landscape'),
      tab_portrait  = tablet && (window.orientation == 'portrait'),
      desktop       = window.matchMedia( "(min-width: 1224px)" ),
      large         = window.matchMedia( "(min-width: 1824px)" );
