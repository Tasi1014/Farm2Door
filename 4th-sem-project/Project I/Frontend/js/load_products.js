document.addEventListener("DOMContentLoaded", () => {
  const productsContainer = document.querySelector(".product-cards");
  const searchInput = document.getElementById("searchInput");
  const priceFilter = document.getElementById("priceFilter");
  const categoryFilter = document.getElementById("categoryFilter");
  const clearButton = document.getElementById("clearFilters");
  const resultsCount = document.getElementById("resultsCount");

  if (!productsContainer) return;

  // 1. Initial Load from URL Parameters
  const urlParams = new URLSearchParams(window.location.search);
  const initialSearch = urlParams.get("search") || "";
  const initialCategory = urlParams.get("category") || "";

  if (searchInput) {
    searchInput.value = initialSearch;
  }
  if (categoryFilter) categoryFilter.value = initialCategory;

  // 2. Scroll to products if searching
  if (initialSearch) {
    const prodSection = document.getElementById("scroll-section");
    if (prodSection) {
      setTimeout(() => {
        prodSection.scrollIntoView({ behavior: "smooth" });
      }, 5); // Small delay to ensure rendering starts
    }
  }

  // Determine Limit (for Home Page)
  let limit = 0;
  const path = window.location.pathname;
  if (path.includes("Home") || path.endsWith("index.html") || path === "/") {
    limit = 8;
  }

  // Fetch products initially
  fetchProducts();

  function fetchProducts() {
    const searchTerm = searchInput ? searchInput.value.trim() : "";
    const category = categoryFilter ? categoryFilter.value : "";

    productsContainer.innerHTML =
      "<p style='text-align: center; width: 100%'>Loading products...</p>";

    let url = `../../Backend/get_all_products.php?search=${encodeURIComponent(
      searchTerm
    )}&category=${encodeURIComponent(category)}`;
    if (limit > 0) url += `&limit=${limit}`;

    fetch(url)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          if (
            !categoryFilter.options.length ||
            categoryFilter.options.length <= 1
          ) {
            populateCategoryFilter(data.products);
          }
          renderProducts(data.products);
        } else {
          productsContainer.innerHTML =
            "<p>No products found matching your search.</p>";
          if (resultsCount) resultsCount.textContent = "Showing 0 products";
        }
      })
      .catch((err) => {
        console.error(err);
        productsContainer.innerHTML = "<p>Unable to load products.</p>";
      });
  }

  function renderProducts(products) {
    // Client-side Price Filtering (since it's a range slider/input often done locally for speed)
    // But for consistency let's filter the list we got from server by price
    const maxPrice =
      priceFilter && priceFilter.value
        ? parseFloat(priceFilter.value)
        : Infinity;

    const filteredProducts = products.filter(
      (p) => parseFloat(p.price) <= maxPrice
    );

    productsContainer.innerHTML = "";

    if (filteredProducts.length === 0) {
      productsContainer.innerHTML =
        "<p style='text-align: center; width: 100%'>No products found matching your criteria.</p>";
      if (resultsCount) resultsCount.textContent = "Showing 0 products";
      return;
    }

    filteredProducts.forEach((product) => {
      const card = document.createElement("div");
      card.className = "card";
      const imgSrc = `../../Images/products/${product.image}`;

      card.innerHTML = `
            <div class="card-badge in-stock">In Stock</div>
            <img src="${imgSrc}" alt="${product.name}" onerror="this.src='../../Images/logo.png'">
            <h3>${product.name}</h3>
            <p class="para">Rs ${product.price}<span class="unit">/kg</span></p>
            <div class="card-actions">
                 <button class="card-btn add-cart-btn" data-id="${product.product_id}">Add to Cart</button>
            </div>
      `;

      card.addEventListener("click", (e) => {
        if (!e.target.classList.contains("add-cart-btn")) {
          window.location.href = `../Product/product_details.html?id=${product.product_id}`;
        }
      });

      productsContainer.appendChild(card);
    });

    attachCartListeners();
    if (resultsCount) {
      resultsCount.textContent = `Showing ${filteredProducts.length} products`;
    }
  }

  function attachCartListeners() {
    const newBtns = document.querySelectorAll(".add-cart-btn");
    newBtns.forEach((btn) => {
      btn.addEventListener("click", (e) => {
        if (window.addToCart) {
          window.addToCart(e);
        } else {
          console.warn("Cart script not loaded");
        }
      });
    });
  }

  function populateCategoryFilter(allProductsForRef) {
    if (!categoryFilter || categoryFilter.options.length > 1) return;
    const categories = new Set();
    allProductsForRef.forEach((p) => {
      if (p.category) categories.add(p.category.trim());
    });
    categories.forEach((cat) => {
      const option = document.createElement("option");
      option.value = cat.toLowerCase();
      option.textContent = cat;
      categoryFilter.appendChild(option);
    });
  }

  // Event Listeners
  if (searchInput) {
    searchInput.addEventListener("keypress", (e) => {
      fetchProducts();
    });
  }

  if (categoryFilter) categoryFilter.addEventListener("change", fetchProducts);
  if (priceFilter)
    priceFilter.addEventListener("input", () => {
      // Re-render current products with price filter
      // We fetch again to be safe with server state, or just re-render local.
      // Let's fetch for server-side search/cat consistency.
      fetchProducts();
    });

  if (clearButton) {
    clearButton.addEventListener("click", () => {
      if (searchInput) searchInput.value = "";
      if (priceFilter) priceFilter.value = "";
      if (categoryFilter) categoryFilter.value = "";
      fetchProducts();
    });
  }
});
