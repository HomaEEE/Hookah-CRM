import Swiper from 'swiper';
import { Pagination, Mousewheel, Keyboard, Autoplay } from 'swiper/modules';
import 'swiper/css';

function initSwiper() {

    const swiper = new Swiper('.swiper-feedbacks', {
        direction: 'horizontal',
        loop: true,
        slidesPerView: 4,
        spaceBetween: 20,
        parallax: true,

    breakpoints: {
    320: {
      slidesPerView: 1.3,
      spaceBetween: 20,
      slidesOffsetBefore:20,

    },
    667: {
      slidesPerView: 2,
      slidesOffsetBefore: 50,
    },
    1024: {
      slidesPerView: 2,
      slidesOffsetBefore: 70,
    },
    1280: {
      slidesPerView: 4,
      slidesOffsetBefore: 100,
    },
    1440: {
      slidesPerView: 5,
      slidesOffsetBefore: 120,
    },
    1920: {
      slidesPerView: 6,
      slidesOffsetBefore: 150,
    }
  },

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