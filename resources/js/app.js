import Lenis from 'lenis';
import { animate, stagger } from "motion";

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
            animate("#cross-col", { y: "100%", opacity: 0 }, { duration: 0 });
            animate(menuEl, { y: "100%", opacity: 0 }, { duration: 0 });
        } else {
            animate("#cross-col", { x: "100%", opacity: 0 }, { duration: 0 });
            animate(menuEl, { x: "100%", opacity: 0 }, { duration: 0 });
            animate("li", { opacity: 0, x: 30 }, { duration: 0 });
            animate(".line-vertical", { scaleY: 0 }, { duration: 0 });
            animate(".line-horizontal", { scaleX: 0 }, { duration: 0 });
            animate(".line-horizontal-short", { scaleX: 0 }, { duration: 0 });
            animate(".content-fade", { opacity: 0, y: 20 }, { duration: 0 });
            animate("form", { opacity: 0, y: 20 }, { duration: 0 });
        }
    }

    function openMenu() {
        menuEl.classList.remove('hidden');
        resetMenuState();

        const sequence = [
            [menuEl,
                window.innerWidth <= 617 ? { y: 0, opacity: 1 } : { x: 0, opacity: 1 },
                { duration: 0.3, ease: [0.22, 1, 0.36, 1] }],

            ["li", { opacity: 1, x: 0 }, {
                duration: 0.4,
                delay: 0.3,
                ease: "easeOut",
                at: "<"
            }],
            [".line-vertical", { scaleY: 1 }, { duration: 0.6, ease: "easeOut", at: "<" }],
            [".line-horizontal", { scaleX: 1 }, { duration: 0.6, ease: "easeOut", at: "<" }],
            [".line-horizontal-short", { scaleX: 1 }, { duration: 0.5, ease: "easeOut", at: "<0.1" }],

            // 3. Форма и блоки (Контакты и Адрес)
            [".content-fade", { opacity: 1, y: 0 }, {
                duration: 0.4,
                delay: stagger(0.1),
                ease: "easeOut",
                at: "-0.2"
            }],
            ["form", { opacity: 1, y: 0 }, { duration: 0.4, ease: "easeOut", at: "<" }],

            // 4. В конце выезжает кнопка закрытия (колонка)
            ["#cross-col",
                window.innerWidth <= 617 ? { y: 0, opacity: 1 } : { x: 0, opacity: 1 },
                { duration: 0.5, ease: "easeOut", at: "<" }
            ]
        ];

        animate(sequence);
    }

    function closeMenu() {
        const isMobile = window.innerWidth <= 617;
        const closeSequence = [
            ["#cross-col", isMobile ? { y: "100%", opacity: 0 } : { x: "100%", opacity: 0 }, { duration: 0.3 }],
            [menuEl, { y: "100%", opacity: 0 }, { duration: 0.5, ease: "easeIn", at: "<0.1" }]
        ];

        animate(closeSequence).finished.then(() => {
            menuEl.classList.add('hidden');
        });
    }

    // Привязка событий
    if (openBtn) openBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
});