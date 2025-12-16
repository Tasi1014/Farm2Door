// product.js - UI Interactions only (Hero Slider, Scroll, Hamburger)

document.addEventListener("DOMContentLoaded", () => {
  // 1. Hero Slider Logic
  const hero = document.querySelector(".hero");
  if (hero) {
    const heroImages = [
      "../../Images/Product-hero.jpg",
      "../../Images/Product-hero2.jpg",
      "../../Images/Product-hero3.jpg",
    ];
    let heroIndex = 0;

    function updateHeroBackground() {
      hero.style.backgroundImage = `url('${heroImages[heroIndex]}')`;
    }

    // Start
    updateHeroBackground();

    // Auto slide
    setInterval(() => {
      heroIndex = (heroIndex + 1) % heroImages.length;
      updateHeroBackground();
    }, 4000);

    // Buttons
    const prevBtn = document.getElementById("hero-prev");
    const nextBtn = document.getElementById("hero-next");

    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        heroIndex = (heroIndex - 1 + heroImages.length) % heroImages.length;
        updateHeroBackground();
      });
    }
    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        heroIndex = (heroIndex + 1) % heroImages.length;
        updateHeroBackground();
      });
    }
  }

  // 2. Scroll Logic
  window.scrollToProducts = function () {
    const prodSection = document.getElementById("product");
    if (prodSection) prodSection.scrollIntoView({ behavior: "smooth" });
  };

  // 3. Hamburger Menu (if not already handled by common script)
  const hamburger = document.querySelector(".hamburger");
  const navLinks = document.querySelector(".nav-links");
  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      navLinks.classList.toggle("mobile-active");
    });
  }
});
