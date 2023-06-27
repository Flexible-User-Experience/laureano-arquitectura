// require('bootstrap');

console.log('Bootstrap Carousel!');
document.addEventListener( 'DOMContentLoaded', function() {
    console.log('addEventListener Bootstrap Carousel!!');
    const myCarouselElement = document.querySelector('#carousel');
    new bootstrap.Carousel(myCarouselElement, {
        interval: 2000,
        touch: false
    });
});
