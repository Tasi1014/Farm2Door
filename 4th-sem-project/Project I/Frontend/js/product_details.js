document.addEventListener("DOMContentLoaded", () => {
  // 1. Get Product ID from URL
  const params = new URLSearchParams(window.location.search);
  const productId = params.get("id");

  if (!productId) {
    alert("No product specified.");
    window.location.href = "product.html";
    return;
  }

  // 2. Fetch Product Details
  fetchProductDetails(productId);
});

function fetchProductDetails(id) {
  const container = document.getElementById("details-container");

  fetch(`../../Backend/get_all_products.php?id=${id}`)
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.product) {
        renderProductDetails(data.product);
      } else {
        container.innerHTML = "<p>Product not found.</p>";
      }
    })
    .catch((err) => {
      console.error(err);
      container.innerHTML = "<p>Error loading product details.</p>";
    });
}

function renderProductDetails(product) {
  const container = document.getElementById("details-container");

  // Stock Logic
  const stock = parseInt(product.stock_quantity) || 0;
  let stockStatusHTML = "";
  let isOutOfStock = false;

  if (stock <= 0) {
    stockStatusHTML =
      '<span style="color:red; font-weight:bold;"><i class="fa fa-times-circle"></i> Out of Stock</span>';
    isOutOfStock = true;
  } else if (stock < 5) {
    stockStatusHTML =
      '<span style="color:orange; font-weight:bold;"><i class="fa fa-exclamation-triangle"></i> Low Stock</span>';
  } else {
    stockStatusHTML =
      '<span style="color:green; font-weight:bold;"><i class="fa fa-check-circle"></i> In Stock</span>';
  }

  // Dynamic HTML Injection
  container.innerHTML = `
        <div class="product-image">
            <img src="../../Images/products/${product.image}" alt="${
    product.name
  }">
        </div>
        <div class="product-info">
            <span class="product-category">${
              product.category || "Fresh Vegetable"
            }</span>
            <h1 class="product-title">${product.name}</h1>
            <p class="product-description">${
              product.description ||
              "No description available for this product."
            }</p>
            
            <div class="product-price">
                 Rs ${product.price} <span>/kg</span>
            </div>
            
            <div class="stock-status">
                ${stockStatusHTML} 
                ${product.unit ? `(${product.unit})` : ""}
            </div>

            <!-- Quantity Control -->
            <div class="quantity-control" style="margin: 20px 0; display: flex; align-items: center; gap: 10px;">
                <label style="font-weight: 600;">Quantity:</label>
                <div style="display: flex; align-items: center; border: 1px solid #ccc; border-radius: 5px; overflow: hidden;">
                    <button id="qty-minus" style="padding: 5px 15px; background: #e0e0e0; border: none; cursor: pointer; font-size: 1.2rem;" ${
                      isOutOfStock ? "disabled" : ""
                    }>-</button>
                    <input type="number" id="qty-input" value="1" min="1" max="${
                      stock > 0 ? stock : 1
                    }" style="width: 50px; text-align: center; border: none; font-size: 1rem; -moz-appearance: textfield;" readonly>
                    <button id="qty-plus" style="padding: 5px 15px; background: #e0e0e0; border: none; cursor: pointer; font-size: 1.2rem;" ${
                      isOutOfStock ? "disabled" : ""
                    }>+</button>
                </div>
            </div>

            <div class="action-buttons">
                <!-- Data-id attribute is CRITICAL for cart.js -->
                <button class="btn-large add-cart-btn" 
                        data-id="${product.product_id}" 
                        id="add-to-cart-action"
                        ${
                          isOutOfStock
                            ? 'disabled style="background:#ccc; cursor:not-allowed;"'
                            : ""
                        }>
                    ${isOutOfStock ? "Out of Stock" : "Add to Cart"}
                </button>
                <a href="product.html" class="back-btn">Continue Shopping</a>
            </div>
        </div>
    `;

  // Attach Event Listeners
  if (!isOutOfStock) {
    const minusBtn = document.getElementById("qty-minus");
    const plusBtn = document.getElementById("qty-plus");
    const qtyInput = document.getElementById("qty-input");
    const addBtn = document.getElementById("add-to-cart-action");

    minusBtn.addEventListener("click", () => {
      let val = parseInt(qtyInput.value);
      if (val > 1) qtyInput.value = val - 1;
    });

    plusBtn.addEventListener("click", () => {
      let val = parseInt(qtyInput.value);
      if (val < stock) qtyInput.value = val + 1;
    });

    addBtn.addEventListener("click", (e) => {
      const qty = parseInt(qtyInput.value);
      // Call global window.addToCart with Quantity
      if (window.addToCart) {
        window.addToCart(e, qty);
      } else {
        console.error("Cart function missing");
      }
    });
  }
}
