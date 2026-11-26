define(['jquery','swiperjs','domReady!'], function($, Swiper){
"use strict";
    return function(config, formElement) {
        var swiper = new Swiper('.projects-main-block.swiper-container', {
            slidesPerView: 3,
            spaceBetween: 30,
            slidesPerColumn: 3,
            pagination: {
              el: ".swiper-pagination",
              clickable: true
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
              1024: {
                slidesPerView: 3,
                spaceBetween: 30,
                slidesPerColumn: 3,
              },
              768: {
                slidesPerView: 2,
                spaceBetween: 20,
                slidesPerColumn: 2,
              },
              640: {
                slidesPerView: 1,
                spaceBetween: 10,
                slidesPerColumn: 1,
              },
              320: {
                slidesPerView: 1,
                spaceBetween: 10,
                slidesPerColumn: 1,
              }
            },
        });
    }
});