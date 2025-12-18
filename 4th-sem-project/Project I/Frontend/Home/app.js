const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".nav-links");
const shopNow = document.getElementById("shop");

// Function to scroll to products section
function goToProducts() {
  document.getElementById("product").scrollIntoView({ behavior: "smooth" });
}

shopNow.addEventListener("click", (e) => {
  e.preventDefault();
  goToProducts();
});

hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("mobile-active");
});

// Hero Background Slider
const hero = document.querySelector(".hero");
const heroImages = [
  "../../Images/hero.jpg",
  "../../Images/hero3.jpg",
  "../../Images/hero4.jpg",
];

let heroIndex = 0;

// Function to update hero background
function updateHeroBackground() {
  hero.style.backgroundImage = `url('${heroImages[heroIndex]}')`;
}

// Initial background
updateHeroBackground();

// Auto slide every 4 seconds
setInterval(() => {
  heroIndex = (heroIndex + 1) % heroImages.length;
  updateHeroBackground();
}, 4000);

// Manual navigation
document.getElementById("hero-prev").addEventListener("click", () => {
  heroIndex = (heroIndex - 1 + heroImages.length) % heroImages.length;
  updateHeroBackground();
});

document.getElementById("hero-next").addEventListener("click", () => {
  heroIndex = (heroIndex + 1) % heroImages.length;
  updateHeroBackground();
});
// Search Logic
const searchInput = document.getElementById("search");
const searchIcon = document.getElementById("icon");

function handleSearch() {
  const query = searchInput.value.trim();
  if (query) {
    // Redirect to Products page
    window.location.href = `../Product/product.html?search=${encodeURIComponent(
      query
    )}`;
  }
}

if (searchInput) {
  searchInput.addEventListener("keypress", (e) => {
    if (e.key === "Enter") handleSearch();
  });
}

if (searchIcon) {
  searchIcon.addEventListener("click", handleSearch);
}
