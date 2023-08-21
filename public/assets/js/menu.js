var menuContainer = $(".tabs");
var menuItems = $(".menu-item");


    menuItems.on("click", function (event) {
        event.preventDefault();
        menuItems.removeClass("active");
        $(this).addClass("active");
        target = $(this).find("a").attr("href");
        var containerWidth = menuContainer.width();
        var activeItemPosition = $(this).position().left;
        var activeItemWidth = $(this).outerWidth();
        var scrollOffset =
            activeItemPosition - containerWidth / 2 + activeItemWidth / 2;
        $("html, body").animate(
            {
                scrollTop: $(target).offset().top - 50,
            },
            500
        );

        menuContainer.animate(
            {
                scrollLeft: "+=" + scrollOffset,
            },
            500
        );
    });



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
