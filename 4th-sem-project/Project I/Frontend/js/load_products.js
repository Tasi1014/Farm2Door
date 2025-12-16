document.addEventListener("DOMContentLoaded", () => {
  const productsContainer = document.querySelector(".product-cards");
  // Filter Elements
  const searchInput = document.getElementById("searchInput");
  const priceFilter = document.getElementById("priceFilter");
  const categoryFilter = document.getElementById("categoryFilter");
  const clearButton = document.getElementById("clearFilters");
  const resultsCount = document.getElementById("resultsCount");

  if (!productsContainer) return;

  // Determine Limit
  let limit = 0;
  const path = window.location.pathname;
  // If we are on Home page (index.html), maybe limit. If on Product page, show all.
  if (path.includes("Home") || path.endsWith("index.html") || path === "/") {
    limit = 8;
  }

  // Fetch Products
  let url = "../../Backend/get_all_products.php";
  if (limit > 0) url += `?limit=${limit}`;

  fetch(url)
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        renderProducts(data.products);
      } else {
        productsContainer.innerHTML = "<p>No products found.</p>";
      }
    })
    .catch((err) => {
      console.error(err);
      productsContainer.innerHTML = "<p>Unable to load products.</p>";
    });

  function renderProducts(products) {
    productsContainer.innerHTML = "";

    if (!products || products.length === 0) {
      productsContainer.innerHTML = "<p>No products available right now.</p>";
      if (resultsCount) resultsCount.textContent = "Showing 0 products";
      return;
    }

    products.forEach((product) => {
      // Create Card
      const card = document.createElement("div");
      card.className = "card";
      // Store data for filtering (Attributes instead of dataset)
      card.setAttribute(
        "data-category",
        (product.category || "vegetables").toLowerCase()
      );
      card.setAttribute("data-name", (product.name || "").toLowerCase());
      card.setAttribute("data-price", product.price);

      const imgSrc = `../../Images/products/${product.image}`;

      const stars = `
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
            <i class="fa-solid fa-star"></i>
      `;

      card.innerHTML = `
            <div class="card-badge in-stock">In Stock</div>
            <img src="${imgSrc}" alt="${product.name}" onerror="this.src='../../Images/logo.png'">
            <h3>${product.name}</h3>
            <div class="rating">${stars}</div>
            <p class="para">Rs ${product.price}<span class="unit">/kg</span></p>
            <div class="card-actions">
                 <button class="card-btn add-cart-btn" data-id="${product.product_id}">Add to Cart</button>
            </div>
      `;

      // Click event for details
      card.addEventListener("click", (e) => {
        if (!e.target.classList.contains("add-cart-btn")) {
          window.location.href = `../Product/product_details.html?id=${product.product_id}`;
        }
      });

      productsContainer.appendChild(card);
    });

    // Attach Add to Cart Listeners
    attachCartListeners();

    // Initial Filter Run (to update counts)
    filterProducts();
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

  // --- MERGED FILTER LOGIC ---
  function filterProducts() {
    // If we are on Home page, we might not have filter inputs. Check first.
    if (!searchInput || !priceFilter || !categoryFilter) return;

    const searchTerm = searchInput.value.toLowerCase();
    const maxPrice = parseFloat(priceFilter.value) || Infinity;
    const selectedCategory = categoryFilter.value.toLowerCase();

    const cards = productsContainer.querySelectorAll(".card");
    let visibleCount = 0;

    cards.forEach((card) => {
      const name = card.getAttribute("data-name");
      const price = parseFloat(card.getAttribute("data-price"));
      const category = card.getAttribute("data-category");

      const matchesSearch = name.includes(searchTerm);
      const matchesPrice = price <= maxPrice;
      const matchesCategory =
        !selectedCategory || category === selectedCategory;

      if (matchesSearch && matchesPrice && matchesCategory) {
        card.style.display = "block";
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    if (resultsCount) {
      resultsCount.textContent = `Showing ${visibleCount} of ${cards.length} products`;
    }
  }

  // Attach Filter Event Listeners
  if (searchInput) searchInput.addEventListener("input", filterProducts);
  if (priceFilter) priceFilter.addEventListener("input", filterProducts);
  if (categoryFilter) categoryFilter.addEventListener("change", filterProducts);

  if (clearButton) {
    clearButton.addEventListener("click", () => {
      searchInput.value = "";
      priceFilter.value = "";
      categoryFilter.value = "";
      filterProducts();
    });
  }
});
