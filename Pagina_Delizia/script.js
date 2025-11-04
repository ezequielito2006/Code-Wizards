let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-slide');
const totalSlides = slides.length;

// Mostrar solo la imagen activa
function updateSlidePosition() {
    slides.forEach((slide, index) => {
        if (index === currentSlide) {
            slide.classList.add('active');
        } else {
            slide.classList.remove('active');
        }
    });
}

// Cambiar la imagen del carrusel
function changeSlide(direction) {
    currentSlide = (currentSlide + direction + totalSlides) % totalSlides;
    updateSlidePosition();
}

// Cambiar automáticamente cada 5 segundos
setInterval(() => {
    changeSlide(1);
}, 5000);

// Inicializar el primer slide
updateSlidePosition();

// Para menú hamburguesa
function toggleMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('active');
}
