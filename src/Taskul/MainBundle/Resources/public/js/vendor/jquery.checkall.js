$(document).ready(function(){

$('#checkInvert').on('switch-change', function (e, data) {
	var $ckPretty = $('div.prettycheckbox a');
	$ckPretty.trigger('click');
});

$('#checkAll').on('switch-change', function (e, data) {
    var $ckPretty = $('div.prettycheckbox a');
	var $ckb = $('div.prettycheckbox input[type=checkbox]');

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