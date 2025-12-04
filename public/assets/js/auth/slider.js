// Auth slider functionality
(function() {
  const slider = document.querySelector('.auth-slider');
  if (!slider) return;

  const slides = slider.querySelectorAll('.slide');
  const indicators = slider.querySelectorAll('.indicator');
  
  if (slides.length === 0) return;

  let currentIndex = 0;
  let autoplayInterval = null;
  const AUTOPLAY_DELAY = 4000; //

  function showSlide(index) {
    // Remove active class from all
    slides.forEach(slide => slide.classList.remove('active'));
    indicators.forEach(ind => ind.classList.remove('active'));

    // Add active to target
    currentIndex = index;
    slides[currentIndex].classList.add('active');
    indicators[currentIndex].classList.add('active');
  }

  function nextSlide() {
    const next = (currentIndex + 1) % slides.length;
    showSlide(next);
  }

  function startAutoplay() {
    stopAutoplay();
    autoplayInterval = setInterval(nextSlide, AUTOPLAY_DELAY);
  }

  function stopAutoplay() {
    if (autoplayInterval) {
      clearInterval(autoplayInterval);
      autoplayInterval = null;
    }
  }
 

  // Click indicators
  indicators.forEach((indicator, index) => {
    indicator.addEventListener('click', () => {
      showSlide(index);
      startAutoplay(); // Restart autoplay
    });
  });

  // Pause on hover
  slider.addEventListener('mouseenter', stopAutoplay);
  slider.addEventListener('mouseleave', startAutoplay);

  // Start autoplay
  startAutoplay();
})();
