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
});

var staticHeader = {
    initialize: function () {
        if ($(".navbar-static-top").length) {
            $("body").css("padding-top", 0);
        }
    }
};
