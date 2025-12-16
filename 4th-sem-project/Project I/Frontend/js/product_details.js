document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("details-container");

  // Get ID from URL
  const urlParams = new URLSearchParams(window.location.search);
  const id = urlParams.get("id");

  if (!id) {
    container.innerHTML =
      "<p>Product not specified. <a href='product.html'>Go back</a></p>";
    return;
  }

  // Fetch details
  fetch(`../../Backend/get_all_products.php?id=${id}`)
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        renderProduct(data.product);
      } else {
        container.innerHTML = `<p>${data.message || "Product not found."}</p>`;
      }
    })
    .catch((err) => {
      console.error(err);
      container.innerHTML = "<p>Error loading product details.</p>";
    });

  function renderProduct(product) {
    const imgSrc = `../../Images/products/${product.image}`;

    container.innerHTML = `
            <div class="product-image">
                <a href="product.html" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Products</a>
                <img src="${imgSrc}" alt="${
      product.name
    }" onerror="this.src='../../Images/logo.png'">
            </div>
            <div class="product-info">
                <span class="product-category">${product.category}</span>
                <h1 class="product-title">${product.name}</h1>
                <div class="product-price">Rs ${
                  product.price
                } <span>/ kg</span></div>
                
                <div class="stock-status">
                    <i class="fa-solid fa-check-circle"></i> In Stock
                </div>

                <div class="farmer-info" style="margin-bottom: 20px; color: #555; font-style: italic;">
                    <i class="fa-solid fa-user-tag"></i> Sold by: <strong>${
                      product.first_name ||
                      product.firstName ||
                      "Farm2Door Farmer"
                    } ${product.last_name || product.lastName || ""}</strong>
                </div>

                <h3>Description</h3>
                <p class="product-description">
                    ${
                      product.description ||
                      "No description available for this product."
                    }
                </p>

                <div class="action-buttons">
                    <button class="btn-large add-cart-btn" data-id="${
                      product.product_id
                    }">
                        <i class="fa-solid fa-cart-plus"></i> Add to Cart
                    </button>
                    <!-- Maybe Quantity selector later -->
                </div>
            </div>
        `;

    // Attach listener for Add to Cart
    const btn = container.querySelector(".add-cart-btn");
    if (btn) {
      btn.addEventListener("click", (e) => {
        if (window.addToCart) {
          window.addToCart(e);
        } else {
          console.error("Cart script missing");
          alert("Cart script not loaded properly.");
        }
      });
    }
  }
});
