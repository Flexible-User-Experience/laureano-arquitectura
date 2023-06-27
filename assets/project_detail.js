document.addEventListener( 'DOMContentLoaded', function() {
    const myCarouselElement = document.querySelector('#carousel');
    new bootstrap.Carousel(myCarouselElement, {
        interval: 4000,
        touch: true
    });
    myCarouselElement.addEventListener('slide.bs.carousel', (event) => {
        let elementChildren = document.querySelector('#indicators').children;
        elementChildren.item(event.to).classList.remove('opacity-25');
        elementChildren.item(event.from).classList.add('opacity-25');
    });
});
