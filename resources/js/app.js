import Lenis from 'lenis';

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

  $('.menu-icon').click(function () {
    $('.menu-icon').toggleClass('nav-active');
    $('.menu-header').toggleClass('active');
  });

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