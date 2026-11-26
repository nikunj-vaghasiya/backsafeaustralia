define(['jquery','swiperjs','domReady!'], function($, Swiper){
"use strict";
    return function(config, formElement) {
      var swiper = new Swiper(".bannerSwiper", {
            slidesPerView: "auto",
            spaceBetween: 0,
            pagination: {
              el: ".swiper-pagination",
              clickable: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            }
        });
    };
});