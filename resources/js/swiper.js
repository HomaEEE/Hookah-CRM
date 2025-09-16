import Swup from 'swup';
import SwupHeadPlugin from '@swup/head-plugin';
import LocomotiveScroll from 'locomotive-scroll';

import Swiper from 'swiper';
import { Pagination, Mousewheel, Keyboard, Autoplay } from 'swiper/modules';
import 'swiper/css';

function initSwiper() {

    swiper = new Swiper('.swiper', {
        direction: 'vertical',
        loop: true,
        slidesPerView: 1,
        parallax: true,
        keyboard: {
            enabled: true,
        },
        mousewheel: {
            enabled: true,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        autoplay: {
            delay: 3000, // Time between slides (in milliseconds)
            disableOnInteraction: false, // Continue autoplay after user interaction
        },
        modules: [Pagination, Mousewheel, Keyboard, Autoplay], // Include Autoplay module
    });
}

initSwiper();