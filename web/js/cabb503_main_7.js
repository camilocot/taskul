var n = null; //noty

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
    return true;
}

$(document).ready(function(){
	// Almacena el boton pulsado para el envio
    $("form button[type=submit]").live('click',function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $('.boostrap-tp').tooltip({
        "trigger":"click"
    }).click(function(e){
        e.preventDefault();
    });
});