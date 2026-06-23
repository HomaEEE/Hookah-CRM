import { animate, stagger } from "motion";

$(document).ready(function () {
  $('.menu-toggle').click(function () {
    $('.menu-toggle').toggleClass('nav-active');
    $('.tabs-links').toggleClass('open');
  });

   $('.tabs-links > a').click(function () {
    console.log('link click');
    $('.menu-toggle').toggleClass('nav-active');
    $('.tabs-links').toggleClass('open');
  });
});

document.addEventListener('DOMContentLoaded', () => {
    const menuEl = document.getElementById('full-menu');
    const closeBtn = document.getElementById('cross-col');
    const openBtn = document.getElementById('menu-btn');

    if (!menuEl) return;

    // 2. Обновленный сброс состояния (в Motion опции передаются третьим аргументом)
    function resetMenuState() {
        const isMobile = window.innerWidth <= 617;
        if (isMobile) {
            animate("#cross-col", { y: "65px", x: 0, opacity: 0 }, { duration: 0 });
            animate(menuEl, { y: "100%", x: 0, opacity: 0 }, { duration: 0 });
            animate(".reserve", { y: "100%", x: 0, opacity: 0 }, { duration: 0 });
        } else {
            animate("#cross-col", { x: "-65px", y: 0, opacity: 0 }, { duration: 0 });
            animate(menuEl, { x: "100%", y: 0, opacity: 0 }, { duration: 0 });
            animate(".nav-menu-item", { opacity: 0, x: 30 }, { duration: 0 });
            animate(".line-vertical", { scaleY: 0 }, { duration: 0 });
            animate(".line-horizontal", { scaleX: 0 }, { duration: 0 });
            animate(".line-horizontal-short", { scaleX: 0 }, { duration: 0 });
            animate(".content-fade", { opacity: 0, y: 20 }, { duration: 0 });
            animate(".nav-form", { opacity: 0, y: 20 }, { duration: 0 });
        }
    }

    resetMenuState();

    function openMenu() {
        menuEl.classList.remove('hidden');
        resetMenuState();

        const isMobile = window.innerWidth <= 617;

        // 3. Создаем таймлайн (секвенцию) через массив
        // Параметр 'at' управляет временем: "<" означает "вместе с предыдущим"
        const sequence = [
            // Появление меню
            [menuEl, isMobile ? { y: 0, opacity: 1 } : { x: 0, opacity: 1 }, { duration: 0.3, ease: "easeOut" }],

            // Пункты меню
            [".nav-menu-item", { opacity: 1, x: 0 }, { duration: 0.4, ease: "easeOut", delay: stagger(0.1) }],

            // Линии (стартуют вместе с пунктами меню благодаря at: "<")
            [".line-vertical", { scaleY: 1 }, { duration: 0.6, ease: "easeOut", at: "<" }],
            [".line-horizontal", { scaleX: 1 }, { duration: 0.6, ease: "easeOut", at: "<" }],
            [".line-horizontal-short", { scaleX: 1 }, { duration: 0.5, ease: "easeOut", at: "<0.1" }],

            // Контент и форма
            [".content-fade", { opacity: 1, y: 0 }, { duration: 0.4, delay: stagger(0.1), ease: "easeOut", at: "-0.2" }],
            ["form", { opacity: 1, y: 0 }, { duration: 0.4, ease: "easeOut", at: "<" }]
        ];

        // Добавляем крестик и резерв в зависимости от устройства
        if (isMobile) {
            sequence.push([".reserve", { y: -40, opacity: 1 }, { duration: 0.3, ease: "easeOut", at: "<" }]);
            sequence.push(["#cross-col", { y: 0, opacity: 1 }, { duration: 0.3, ease: "easeOut", at: "-0.5" }]);
        } else {
            sequence.push(["#cross-col", { x: 0, opacity: 1 }, { duration: 0.3, ease: "easeOut", at: "-0.5" }]);
        }

        // Запускаем
        animate(sequence);
    }

    function closeMenu() {
        const isMobile = window.innerWidth <= 617;

        const sequence = [
            // Скрываем пункты меню
            [".nav-menu-item", { opacity: 0, x: 30 }, { duration: 0.2, ease: "easeOut", delay: stagger(0.1) }],

            // Линии
            [".line-vertical", { scaleY: 0 }, { duration: 0.2, ease: "easeOut", at: "<" }],
            [".line-horizontal", { scaleX: 0 }, { duration: 0.2, ease: "easeOut", at: "<" }],
            [".line-horizontal-short", { scaleX: 0 }, { duration: 0.2, ease: "easeOut", at: "<" }],

            // Контент
            [".content-fade", { opacity: 0, y: 20 }, { duration: 0.2, delay: stagger(0.1), ease: "easeOut", at: "<" }],
            [".nav-form", { opacity: 0, y: 20 }, { duration: 0.2, ease: "easeOut", at: "<" }]
        ];

        if (isMobile) {
            sequence.push([".reserve", { y: "100%", opacity: 0 }, { duration: 0.3, ease: "easeOut", at: "<" }]);
            sequence.push(["#cross-col", { y: "100%", opacity: 0 }, { duration: 0.3, at: "<" }]);
            // Само меню (уезжает вниз)
            sequence.push([menuEl, { y: "100%", opacity: 0 }, { duration: 0.5, ease: "easeIn", at: "+0.1" }]);
        } else {
            sequence.push(["#cross-col", { x: "100%", opacity: 0 }, { duration: 0.3, at: "<" }]);
            // Само меню (уезжает вправо)
            sequence.push([menuEl, { x: "100%", opacity: 1 }, { duration: 0.5, ease: "easeIn", at: "+0.1" }]);
        }

        // Запускаем закрытие и вешаем then() для скрытия блока после анимации
        animate(sequence).then(() => {
            menuEl.classList.add('hidden');
        });
    }

    if (openBtn) openBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
});