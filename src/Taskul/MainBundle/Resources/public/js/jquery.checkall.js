$(document).ready(function(){


// Seleccionar inversamente
$(document).on('switch-change', '#checkInvert', function (e, data) {
	$ckPretty = $('div.prettycheckbox a');// Para los pretty checkboxes
	$ckPretty.trigger('click');
});

// Seleccionar todo/nada
$(document).on('switch-change', '#checkAll',  function (e, data) {
	$ckPretty = $('div.prettycheckbox a');// Para los pretty checkboxes
	$ckb = $('div.prettycheckbox input[type=checkbox]'); // Para los checkboxes en si
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