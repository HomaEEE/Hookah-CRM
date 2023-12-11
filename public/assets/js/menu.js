$(".tab-item").click(function (e) {
	e.preventDefault();
	var target = $(this).data("target");
	$(this).removeClass("active");
	$(".tab-item").removeClass("active");
	$(this).addClass("active");
	$(".positions").removeClass("active").slideUp('fast');
	$(".positions#" + target).addClass("active").slideDown('fast');

	var containerWidth = $('.tabs').width();
    var activeItemPosition = $(this).position().left;
    var activeItemWidth = $(this).outerWidth();
    var scrollOffset = activeItemPosition - containerWidth / 2 + activeItemWidth / 2;
	$('.tabs').animate({ scrollLeft: "+=" + scrollOffset }, 500);

	$('html, body').animate({
        scrollTop: $(".list").offset().top - 70
    }, 1000);
});

$(".menu-item").accordion({collapsible: true, active: false});

Fancybox.bind("[data-fancybox]", {
    hideScrollbar: false,
    closeButton: "auto",
    contentClick: "close",
    Toolbar: {
        display: {
            left: [],
            middle: [],
            right: ["close"],
        },
    },
});
