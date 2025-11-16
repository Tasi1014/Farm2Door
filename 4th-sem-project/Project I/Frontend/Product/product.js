// Scroll to product grid from hero button
function scrollToProducts() {
  document.getElementById("product").scrollIntoView({ behavior: "smooth" });
}

// ----------- PRODUCT FILTERING (Simplified) ------------

const searchInput = document.getElementById("searchInput");
const priceFilter = document.getElementById("priceFilter");
const categoryFilter = document.getElementById("categoryFilter");
const clearButton = document.getElementById("clearFilters");
const resultsCount = document.getElementById("resultsCount");
const productCards = document.querySelectorAll(".card");

// Filter function
function filterProducts() {
  const searchTerm = searchInput.value.toLowerCase();
  const maxPrice = parseFloat(priceFilter.value) || Infinity;
  const selectedCategory = categoryFilter.value;
  let visibleCount = 0;

  productCards.forEach(card => {
    const name = card.querySelector("h3").innerText.toLowerCase();
    const priceText = card.querySelector(".para").innerText;
    const price = parseFloat(priceText.replace(/[^\d.]/g, '')) || 0;
    const category = card.getAttribute("data-category") || "vegetables";

    const matchesSearch = name.includes(searchTerm);
    const matchesPrice = price <= maxPrice;
    const matchesCategory = !selectedCategory || category === selectedCategory;

    if (matchesSearch && matchesPrice && matchesCategory) {
      card.style.display = "block";
      visibleCount++;
    } else {
      card.style.display = "none";
    }
  });

  updateResultsCount(visibleCount);
}

// Clear all filters
function clearFilters() {
  searchInput.value = "";
  priceFilter.value = "";
  categoryFilter.value = "";
  filterProducts();
}

// Update result count
function updateResultsCount(visibleCount) {
  const total = productCards.length;
  if (visibleCount === total) {
    resultsCount.textContent = `Showing all ${total} products`;
  } else {
    resultsCount.textContent = `Showing ${visibleCount} of ${total} products`;
  }
}

// Event listeners
searchInput.addEventListener("input", filterProducts);
priceFilter.addEventListener("input", filterProducts);
categoryFilter.addEventListener("change", filterProducts);
clearButton.addEventListener("click", clearFilters);

// Initialize
filterProducts();

const hero = document.querySelector('.hero');
const heroImages = [
  '../../Images/Product-hero.jpg',
  '../../Images/Product-hero2.jpg',
  '../../Images/Product-hero3.jpg',
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
document.getElementById('hero-prev').addEventListener('click', () => {
  heroIndex = (heroIndex - 1 + heroImages.length) % heroImages.length;
  updateHeroBackground();
});

const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

hamburger.addEventListener('click', () => {
  navLinks.classList.toggle('mobile-active');
});