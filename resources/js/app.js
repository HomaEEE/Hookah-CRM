import Lenis from 'lenis';
import { animate, stagger, createTimeline } from "animejs";

import $ from "jquery";
window.$ = $;
window.jQuery = $;



const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // custom easing
  direction: 'vertical', // vertical, horizontal
  gestureDirection: 'vertical', // vertical, horizontal, both
  smooth: true,
  mouseMultiplier: 1,
  smoothTouch: false,
  touchMultiplier: 2,
  infinite: false,
});

// Use the 'scroll' event to sync with other libraries like GSAP ScrollTrigger
lenis.on('scroll', (e) => {
  // console.log(e);
});

// The animation loop
function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

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

    function resetMenuState() {
        const isMobile = window.innerWidth <= 617;
        if (isMobile) {
            animate("#cross-col", { translateY: "65px", translateX: 0, opacity: 0, duration: 0 });
            animate(menuEl, { translateY: "100%", translateX: 0, opacity: 0, duration: 0 });
            animate(".reserve", { translateY: "100%", translateX: 0, opacity: 0, duration: 0 });
        } else {
            animate("#cross-col", { translateX: "-65px", translateY: 0, opacity: 0, duration: 0 });
            animate(menuEl, { translateX: "100%", translateY: 0, opacity: 0, duration: 0 });
            animate(".nav-menu-item", { opacity: 0, translateX: 30, duration: 0 });
            animate(".line-vertical", { scaleY: 0, duration: 0 });
            animate(".line-horizontal", { scaleX: 0, duration: 0 });
            animate(".line-horizontal-short", { scaleX: 0, duration: 0 });
            animate(".content-fade", { opacity: 0, translateY: 20, duration: 0 });
            animate(".nav-form", { opacity: 0, translateY: 20, duration: 0 });
        }
    }

    // Первоначальный сброс состояния
    resetMenuState();

    function openMenu() {
        menuEl.classList.remove('hidden');
        resetMenuState();

        const isMobile = window.innerWidth <= 617;
        const tl = createTimeline();

        tl.add(menuEl, {
            ...(isMobile ? { translateY: 0, opacity: 1 } : { translateX: 0, opacity: 1 }),
            duration: 300,
            ease: "outExpo"
        });

        tl.add(".nav-menu-item", {
            opacity: 1,
            translateX: 0,
            duration: 400,
            ease: "outExpo",
            delay: stagger(100),
        }, "<<+=300");

        tl.add(".line-vertical", { scaleY: 1, duration: 600, ease: "outExpo" }, "<<");
        tl.add(".line-horizontal", { scaleX: 1, duration: 600, ease: "outExpo" }, "<<");
        tl.add(".line-horizontal-short", { scaleX: 1, duration: 500, ease: "outExpo" }, "<<+=100");

        // 3. Форма и блоки (Контакты и Адрес)
        tl.add(".content-fade", {
            opacity: 1,
            translateY: 0,
            duration: 400,
            delay: stagger(100),
            ease: "outExpo"
        }, "-=200");
        tl.add("form", { opacity: 1, translateY: 0, duration: 400, ease: "outExpo" }, "<<");

        tl.add(".reserve", {
            ...(isMobile ? { translateY: -40, opacity: 1 } : null ),
            duration: 300,
            ease: "outExpo"
        }, "<<");

        tl.add("#cross-col", {
            ...(isMobile ? { translateY: 0, opacity: 1 } : { translateX: "", opacity: 1 }),
            duration: 300,
            ease: "outExpo"
        }, "<<-500");
    }

    function closeMenu() {
        const isMobile = window.innerWidth <= 617;
        const tl = createTimeline();

        tl.add(".nav-menu-item", {
            opacity: 0,
            translateX: 30,
            duration: 200,
            ease: "outExpo",
            delay: stagger(100),
        }, "<<+=300");

        tl.add(".line-vertical", { scaleY: 0, duration: 200, ease: "outExpo" }, "<<-100");
        tl.add(".line-horizontal", { scaleX: 0, duration: 200, ease: "outExpo" }, "<<-100");
        tl.add(".line-horizontal-short", { scaleX: 0, duration: 200, ease: "outExpo" }, "<<-100");

        // 3. Форма и блоки (Контакты и Адрес)
        tl.add(".content-fade", {
            opacity: 0,
            translateY: 20,
            duration: 200,
            delay: stagger(100),
            ease: "outExpo"
        }, "-=200");
        tl.add(".nav-form", { opacity: 0, translateY: 20, duration: 200, ease: "outExpo" }, "<<-200");

        tl.add(".reserve", {
            ...(isMobile ? { translateY: 100, opacity: 0 } : null ),
            duration: 300,
            ease: "outExpo"
        }, "<<");

        tl.add("#cross-col", {
            ...(isMobile ? { translateY: "100%", opacity: 0 } : { translateX: "100%", opacity: 0 }),
            duration: 300
        });

        tl.add(menuEl, {
            ...(isMobile ? { translateY: "100%", opacity: 0 } : { translateX: "100%", opacity: 0 }),
            duration: 500,
            ease: "inExpo"
        }, "<<+=100");

        tl.then(() => {
            menuEl.classList.add('hidden');
        });
    }

    // Привязка событий
    if (openBtn) openBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
});