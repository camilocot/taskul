$(function () {
    $(window).scroll(function() {
        if ($(".navbar").offset().top>30) {
            $(".navbar-inner").addClass("sticky");
        }
        else {
            $(".navbar-inner").removeClass("sticky");
        }
    });

    // Flex
    if ($(".flexslider").length) {
        $('.flexslider').flexslider();
    }

    staticHeader.initialize();
    if(!checkMobile())
    $('#beta').text('Taskul.net está en fase BETA, por favor ayúdenos a mejorar cumplimentando el formulario de sugerencias y/o problemas. Gracias.');
});

var staticHeader = {
    initialize: function () {
        if ($(".navbar-static-top").length) {
            $("body").css("padding-top", 0);
        }
    }
};
