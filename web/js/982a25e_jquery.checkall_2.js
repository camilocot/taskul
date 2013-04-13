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