$(document).ready(function(){

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

	$('.btn-close').click(function(e){
		e.preventDefault();
		$(this).parent().parent().parent().fadeOut();
	});
	$('.btn-minimize').click(function(e){
		e.preventDefault();
		var $target = $(this).parent().parent().next('.box-content');
		if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
		else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
		$target.slideToggle();
	});
	$('.btn-setting').click(function(e){
		e.preventDefault();
		$('#myModal').modal('show');
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