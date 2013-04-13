/* Set the defaults for DataTables initialisation */
$.extend( true, $.fn.dataTable.defaults, {
    "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
    "sPaginationType": "bootstrap",
    "oLanguage": {
        "sLengthMenu": "_MENU_ records per page"
    }
} );


/* Default class modification */
$.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
} );


/* API method to get paging information */
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
    return {
        "iStart":         oSettings._iDisplayStart,
        "iEnd":           oSettings.fnDisplayEnd(),
        "iLength":        oSettings._iDisplayLength,
        "iTotal":         oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage":          oSettings._iDisplayLength === -1 ?
        0 : Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
        "iTotalPages":    oSettings._iDisplayLength === -1 ?
        0 : Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
};


/* Bootstrap style pagination control */
$.extend( $.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function( oSettings, nPaging, fnDraw ) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function ( e ) {
                e.preventDefault();
                if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                    fnDraw( oSettings );
                }
            };

            $(nPaging).addClass('pagination').append(
                '<ul>'+
                '<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
                '<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
                '</ul>'
                );
            var els = $('a', nPaging);
            $(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
            $(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
        },

        "fnUpdate": function ( oSettings, fnDraw ) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, ien, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);

            if ( oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if ( oPaging.iPage <= iHalf ) {
                iStart = 1;
                iEnd = iListLength;
            } else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }

            for ( i=0, ien=an.length ; i<ien ; i++ ) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();

                // Add the new list items and their event handlers
                for ( j=iStart ; j<=iEnd ; j++ ) {
                    sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                    $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                    .insertBefore( $('li:last', an[i])[0] )
                    .bind('click', function (e) {
                        e.preventDefault();
                        oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                        fnDraw( oSettings );
                    } );
                }

                // Add / remove disabled classes from the static elements
                if ( oPaging.iPage === 0 ) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }

                if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
} );

    /*
 * TableTools Bootstrap compatibility
 * Required TableTools 2.1+
 */
 if ( $.fn.DataTable.TableTools ) {
    // Set the classes that TableTools uses to something suitable for Bootstrap
    $.extend( true, $.fn.DataTable.TableTools.classes, {
        "container": "DTTT btn-group",
        "buttons": {
            "normal": "btn",
            "disabled": "disabled"
        },
        "collection": {
            "container": "DTTT_dropdown dropdown-menu",
            "buttons": {
                "normal": "",
                "disabled": "disabled"
            }
        },
        "print": {
            "info": "DTTT_print_info modal"
        },
        "select": {
            "row": "active"
        }
    } );

    // Have the collection use a bootstrap compatible dropdown
    $.extend( true, $.fn.DataTable.TableTools.DEFAULTS.oTags, {
        "collection": {
            "container": "ul",
            "button": "li",
            "liner": "a"
        }
    } );
}

$(document).ready(function() {
    clearMenuActive('friendreq_ops');

    $('.delete-modal-btn').click( function (e) {
        frquestId = $(this).data('id');
        $('#form_delete_id').val(frquestId);
        route = Routing.generate('frequest_delete', { "id": frquestId });
        $('#delete-frequest').attr('action', route);

    });

    $('#modal-delete-form-submit').click( function (e) {
     e.preventDefault();
     $('#delete-frequest').submit();
    });

    $('.activate-modal-btn').click( function (e) {
        e.preventDefault();
        frquestId = $(this).data('id');
        $('#form_activate_id').val(frquestId);
        route = Routing.generate('frequest_activate', { "id": frquestId });
        $('#activate-frequest').attr('action', route);

    });

    $('#modal-activate-form-submit').click( function (e) {
     e.preventDefault();
     $('#activate-frequest').submit();
    });
});

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

/*
 *  Project: prettyCheckable
 *  Description: jQuery plugin to replace checkboxes and radios for custom images
 *  Author: Arthur Gouveia
 *  License: Licensed under the MIT License
 */

;(function ( $, window, undefined ) {

    var pluginName = 'prettyCheckable',
      document = window.document,
      defaults = {
        labelPosition: 'right',
        customClass: '',
        color: 'blue'
      };

    function Plugin( element, options ) {
      this.element = element;
      this.options = $.extend( {}, defaults, options) ;

      this._defaults = defaults;
      this._name = pluginName;

      this.init();
    }

    function addCheckableEvents(element){

      element.find('a, label').on('touchstart click', function(e){

        e.preventDefault();

        var clickedParent = $(this).closest('.clearfix');
        var input = clickedParent.find('input');
        var fakeCheckable = clickedParent.find('a');

        if (input.attr('type') == 'radio') {

          $('input[name="' + input.attr('name') + '"]').each(function(index, el){
            $(el).removeAttr('checked').parent().find('a').removeClass('checked');
          });

        }

        if (input.attr('checked') !== undefined) {

          input.removeAttr('checked').change();

        } else {

          input.attr('checked', 'checked').change();

        }

        fakeCheckable.toggleClass('checked');

      });

      element.find('a').on('keyup', function(e){

        if (e.keyCode === 32) {

          $(this).click();

        }

      });

    }

    Plugin.prototype.init = function () {

      var el = $(this.element);

      el.css('display', 'none');

      var classType = el.data('type') !== undefined ? el.data('type') : el.attr('type');

      var label = el.data('label') !== undefined ? el.data('label') : '';

      var labelPosition = el.data('labelposition') !== undefined ? 'label' + el.data('labelposition') : 'label' + this.options.labelPosition;

      var customClass = el.data('customclass') !== undefined ? el.data('customclass') : this.options.customClass;

      var color =  el.data('color') !== undefined ? el.data('color') : this.options.color;

      var containerClasses = ['pretty' + classType, labelPosition, customClass, color].join(' ');

      el.wrap('<div class="clearfix ' + containerClasses + '"></div>').parent().html();
      
      var dom = [];
      var isChecked = el.attr('checked') !== undefined ? 'checked' : '';

      if (labelPosition === 'labelright') {

        dom.push('<a href="#" class="' + isChecked + '"></a>');
        dom.push('<label for="' + el.attr('id') + '">' + label + '</label>');

      } else {

        dom.push('<label for="' + el.attr('id') + '">' + label + '</label>');
        dom.push('<a href="#" class="' + isChecked + '"></a>');

      }

      el.parent().append(dom.join('\n'));
      addCheckableEvents(el.parent());

    };

    $.fn[pluginName] = function ( options ) {
      this.each(function () {
        if (!$.data(this, 'plugin_' + pluginName)) {
          $.data(this, 'plugin_' + pluginName, new Plugin( this, options ));
        }
      });
      return this;
    };

}(jQuery, window));
$(document).ready(function(){

var $ckPretty = $('div.prettycheckbox a');// Para los pretty checkboxes
var $ckb = $('div.prettycheckbox input[type=checkbox]'); // Para los checkboxes en si

// Seleccionar inversamente
$('#checkInvert').on('switch-change', function (e, data) {
	$ckPretty.trigger('click');
});

// Seleccionar todo/nada
$('#checkAll').on('switch-change', function (e, data) {
	if($(this).bootstrapSwitch('status')){
		$ckPretty.removeClass('checked');
		$ckb.attr('checked', false);
	}else{
		$ckPretty.addClass('checked');
		$ckb.attr('checked', true);
	}
		$ckPretty.trigger('click');

});
});
/* ============================================================
 *  * bootstrapSwitch v1.2 by Larentis Mattia @spiritualGuru
 *   * http://www.larentis.eu/switch/
 *    * ============================================================
 *     * Licensed under the Apache License, Version 2.0
 *      * http://www.apache.org/licenses/LICENSE-2.0
 *       * ============================================================ */

!function ($) {
  "use strict";

  $.fn['bootstrapSwitch'] = function (method) {
    var methods = {
      init: function () {
        return this.each(function () {
            var $element = $(this)
              , $div
              , $switchLeft
              , $switchRight
              , $label
              , myClasses = ""
              , classes = $element.attr('class')
              , color
              , moving
              , onLabel = "ON"
              , offLabel = "OFF";

            $.each(['switch-mini', 'switch-small', 'switch-large'], function (i, el) {
              if (classes.indexOf(el) >= 0)
                myClasses = el;
            });

            $element.addClass('has-switch');

            if ($element.data('on') !== undefined)
              color = "switch-" + $element.data('on');

            if ($element.data('on-label') !== undefined)
              onLabel = $element.data('on-label');

            if ($element.data('off-label') !== undefined)
              offLabel = $element.data('off-label');

            $switchLeft = $('<span>')
              .addClass("switch-left")
              .addClass(myClasses)
              .addClass(color)
              .html(onLabel);

            color = '';
            if ($element.data('off') !== undefined)
              color = "switch-" + $element.data('off');

            $switchRight = $('<span>')
              .addClass("switch-right")
              .addClass(myClasses)
              .addClass(color)
              .html(offLabel);

            $label = $('<label>')
              .html("&nbsp;")
              .addClass(myClasses)
              .attr('for', $element.find('input').attr('id'));

            $div = $element.find(':checkbox').wrap($('<div>')).parent().data('animated', false);

            if ($element.data('animated') !== false)
              $div.addClass('switch-animate').data('animated', true);

            $div
              .append($switchLeft)
              .append($label)
              .append($switchRight);

            $element.find('>div').addClass(
              $element.find('input').is(':checked') ? 'switch-on' : 'switch-off'
            );

            if ($element.find('input').is(':disabled'))
              $(this).addClass('deactivate');

            var changeStatus = function ($this) {
              $this.siblings('label').trigger('mousedown').trigger('mouseup').trigger('click');
            };

            $element.on('keydown', function (e) {
              if (e.keyCode === 32) {
                e.stopImmediatePropagation();
                e.preventDefault();
                changeStatus($(e.target).find('span:first'));
              }
            });

            $switchLeft.on('click', function (e) {
              changeStatus($(this));
            });

            $switchRight.on('click', function (e) {
              changeStatus($(this));
            });

            $element.find('input').on('change', function (e) {
              var $element = $(this).parent();

              e.preventDefault();
              e.stopImmediatePropagation();

              $element.css('left', '');

              if ($(this).is(':checked'))
                $element.removeClass('switch-off').addClass('switch-on');
              else $element.removeClass('switch-on').addClass('switch-off');

              if ($element.data('animated') !== false)
                $element.addClass("switch-animate");

              $element.parent().trigger('switch-change', {'el': $(this), 'value': $(this).is(':checked')})
            });

            $element.find('label').on('mousedown touchstart', function (e) {
              var $this = $(this);
              moving = false;

              e.preventDefault();
              e.stopImmediatePropagation();

              $this.closest('div').removeClass('switch-animate');

              if ($this.closest('.switch').is('.deactivate'))
                $this.unbind('click');
              else {
                $this.on('mousemove touchmove', function (e) {
                  var $element = $(this).closest('.switch')
                    , relativeX = (e.pageX || e.originalEvent.targetTouches[0].pageX) - $element.offset().left
                    , percent = (relativeX / $element.width()) * 100
                    , left = 25
                    , right = 75;

                  moving = true;

                  if (percent < left)
                    percent = left;
                  else if (percent > right)
                    percent = right;

                  $element.find('>div').css('left', (percent - right) + "%")
                });

                $this.on('click touchend', function (e) {
                  var $this = $(this)
                    , $target = $(e.target)
                    , $myCheckBox = $target.siblings('input');

                  e.stopImmediatePropagation();
                  e.preventDefault();

                  $this.unbind('mouseleave');

                  if (moving)
                    $myCheckBox.prop('checked', !(parseInt($this.parent().css('left')) < -25));
                  else $myCheckBox.prop("checked", !$myCheckBox.is(":checked"));

                  moving = false;
                  $myCheckBox.trigger('change');
                });

                $this.on('mouseleave', function (e) {
                  var $this = $(this)
                    , $myCheckBox = $this.siblings('input');

                  e.preventDefault();
                  e.stopImmediatePropagation();

                  $this.unbind('mouseleave');
                  $this.trigger('mouseup');

                  $myCheckBox.prop('checked', !(parseInt($this.parent().css('left')) < -25)).trigger('change');
                });

                $this.on('mouseup', function (e) {
                  e.stopImmediatePropagation();
                  e.preventDefault();

                  $(this).unbind('mousemove');
                });
              }
            });
          }
        );
      },
      toggleActivation: function () {
        $(this).toggleClass('deactivate');
      },
      isActive: function () {
        return !$(this).hasClass('deactivate');
      },
      setActive: function (active) {
        if (active)
          $(this).removeClass('deactivate');
        else $(this).addClass('deactivate');
      },
      toggleState: function (skipOnChange) {
        var $input = $(this).find('input:checkbox');
        $input.prop('checked', !$input.is(':checked')).trigger('change', skipOnChange);
      },
      setState: function (value, skipOnChange) {
        $(this).find('input:checkbox').prop('checked', value).trigger('change', skipOnChange);
      },
      status: function () {
        return $(this).find('input:checkbox').is(':checked');
      },
      destroy: function () {
        var $div = $(this).find('div')
          , $checkbox;

        $div.find(':not(input:checkbox)').remove();

        $checkbox = $div.children();
        $checkbox.unwrap().unwrap();

        $checkbox.unbind('change');

        return $checkbox;
      }
    };

    if (methods[method])
      return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
    else if (typeof method === 'object' || !method)
      return methods.init.apply(this, arguments);
    else
      $.error('Method ' + method + ' does not exist!');
  };
}(jQuery);

$(function () {
  $('.switch')['bootstrapSwitch']();
});

