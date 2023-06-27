document.addEventListener( 'DOMContentLoaded', function() {
    const myCarouselElement = document.querySelector('#carousel');
    new bootstrap.Carousel(myCarouselElement, {
        interval: 2000,
        touch: false
    });
});
