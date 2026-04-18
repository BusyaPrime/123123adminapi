var swiper = new Swiper('.swiper-container', {
    slidesPerView: 4,
    pagination: {
      el: '.swiper-pagination',
    },
    loop: true,
    breakpoints: {
        768: {
          slidesPerView: 2,
          spaceBetween: 40,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 50,
        },
    }
  });