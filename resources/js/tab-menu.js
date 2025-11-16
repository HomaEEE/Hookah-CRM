$(document).ready(function () {
    function showCategory($targetSection) {
        // Показываем блок категории
        $targetSection.fadeIn(300).addClass("active");

        // Анимируем появление позиций внутри
        $targetSection.find(".menu-item").each(function (index) {
            $(this)
                .css({ opacity: 0, transform: "translateY(20px)" })
                .delay(index * 50)
                .animate({ opacity: 1, top: 0 }, 200);
        });
    }

    // Обработчик клика по табам
    $(".tab-item").on("click", function (e) {
        e.preventDefault();

        const target = $(this).data("target");

        // Меняем активную вкладку
        $(".tab-item").removeClass("active");
        $(this).addClass("active");

        // Скрываем все секции
        $(".positions").removeClass("active").hide();

        // Показываем новую категорию
        const $targetSection = $("#" + target);
        showCategory($targetSection);

        var containerWidth = $('.tabs').width();
        var activeItemPosition = $(this).position().left;
        var activeItemWidth = $(this).outerWidth();
        var scrollOffset = activeItemPosition - containerWidth / 2 + activeItemWidth / 2;
        $('.tabs').animate({ scrollLeft: "+=" + scrollOffset }, 1000);

            // Плавный скролл к секции
            $("html, body").animate({
                scrollTop: $(".list").offset().top - $(".tabs").outerHeight() - 10
            }, 600);
        });

    // === Автооткрытие первой категории при загрузке ===
    const $firstTab = $(".tab-item").first();
    const firstTarget = $firstTab.data("target");
    const $firstSection = $("#" + firstTarget);

    $firstTab.addClass("active");
    showCategory($firstSection);
});
