document.addEventListener("DOMContentLoaded", () => {
  // 1. Initial Cart Count Load
  updateCartCount();

  // 2. Setup Cart Icon Interactions
  setupCartIcon();

  // 3. Inject Modal & Toast HTML (Lazy load UI)
  injectCartUI();
});

// --- UI Injection ---
function injectCartUI() {
  // Cart Badge (if not present)
  // Usually handled in setupCartIcon but we ensure styles are ready

  // Modal HTML
  if (!document.getElementById("cartModal")) {
    const modalHTML = `
      <div id="cartModal" class="cart-overlay">
        <div class="cart-modal-content">
          <div class="cart-header">
            <span class="close-cart">&times;</span>
            <h2>Your Cart</h2>
          </div>
          
          <div id="cart-items">
            <!-- Items injected here -->
            <p style="text-align:center; padding: 20px; color:#666;">Loading cart...</p>
          </div>
          
          <div class="cart-footer">
            <div class="cart-total">
              <h3>Total:</h3>
              <span id="cart-total-amount" class="cart-total-amount">Rs 0</span>
            </div>
            <button id="checkout-btn" class="checkout-btn">Proceed to Checkout</button>
          </div>
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML("beforeend", modalHTML);

    // Event Listeners for Modal
    const modal = document.getElementById("cartModal");
    const closeBtn = document.querySelector(".close-cart");
    const checkoutBtn = document.getElementById("checkout-btn");

    if (closeBtn) {
      closeBtn.addEventListener("click", () => (modal.style.display = "none"));
    }

    // Close on click outside
    window.addEventListener("click", (event) => {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    });

    // Checkout Logic
    if (checkoutBtn) {
      checkoutBtn.addEventListener("click", () => {
        window.location.href = "../Cart/checkout.html";
      });
    }
  }

  // Toast HTML
  if (!document.getElementById("toast")) {
    const toastHTML = `<div id="toast">Item added to cart successfully!</div>`;
    document.body.insertAdjacentHTML("beforeend", toastHTML);
  }
}

function setupCartIcon() {
  const cartIcon = document.getElementById("cart");

  if (cartIcon) {
    cartIcon.style.cursor = "pointer";
    cartIcon.parentElement.style.position = "relative";

    // Create Badge if missing
    if (!document.getElementById("cart-count")) {
      const badge = document.createElement("span");
      badge.id = "cart-count";
      cartIcon.parentElement.appendChild(badge);
    }

    // Click Handler - Remove old listeners if any by cloning (optional but safe) or just add new one
    // We'll just add one.
    cartIcon.onclick = function (e) {
      e.preventDefault();
      openCartModal();
    };
  } else {
    // console.log("Cart Icon (id='cart') NOT FOUND - skipping setup.");
  }
}

// --- Core Actions ---

// Exposed Function for "Add to Cart" buttons
window.addToCart = function (event, quantity = 1) {
  console.log("addToCart called. Quantity:", quantity); // DEBUG LOG
  if (event) event.stopPropagation();

  let btn = event.currentTarget || event.target;
  if (!btn.classList.contains("add-cart-btn")) {
    btn = btn.closest(".add-cart-btn");
  }
  if (!btn) return;

  const id = btn.dataset.id;
  if (!id) return;

  // Optimistic UI Feedback
  const originalText = btn.innerHTML;
  btn.innerText = "Adding...";
  btn.disabled = true;

  fetch("../../Backend/add_to_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ product_id: id, quantity: parseInt(quantity) }), // Ensure INT
  })
    .then((res) => res.json())
    .then((data) => {
      console.log("Add to Cart Response:", data); // DEBUG LOG
      btn.innerHTML = originalText;
      btn.disabled = false;
      // ... rest of function

      if (data.success) {
        showToast("Added to Cart Successfully!");
        updateCartBadge(data.cart_count); // Use count from response if available
        // Fallback update if count missing
        if (data.cart_count === undefined) updateCartCount();
      } else {
        handleAuthError(data);
      }
    })
    .catch((err) => {
      console.error("Cart Error:", err);
      btn.innerHTML = originalText;
      btn.disabled = false;
      alert("Something went wrong. Please try again.");
    });
};

function openCartModal() {
  const modal = document.getElementById("cartModal");
  const container = document.getElementById("cart-items");
  const totalSpan = document.getElementById("cart-total-amount");

  if (!modal) {
    return;
  }

  // 1. Check Login & Fetch Data
  fetch("../../Backend/get_cart.php")
    .then((res) => {
      return res.json();
    })
    .then((data) => {
      // Handle Not Logged In
      if (
        data.success === false &&
        (data.message === "Not logged in" || data.not_logged_in)
      ) {
        if (
          confirm(
            "You must be logged in to view your cart. \n\nClick OK to Login."
          )
        ) {
          window.location.href = "../Login/login.html";
        }
        return;
      }

      // If success
      if (data.success) {
        modal.style.display = "block"; // Show modal only if logged in
        renderCartItems(data.items, container, totalSpan, data.total_price);
      } else {
        container.innerHTML = `<p style="text-align:center; padding:20px;">${
          data.message || "Error loading cart."
        }</p>`;
        modal.style.display = "block"; // Show modal with error
      }
    })
    .catch((err) => {
      alert("Failed to load cart.");
    });
}

function renderCartItems(items, container, totalSpan, totalPrice) {
  if (items.length === 0) {
    container.innerHTML = `<div style="text-align:center; padding:40px; color:#888;">
      <i class="fa fa-shopping-basket" style="font-size:3rem; margin-bottom:15px; display:block;"></i>
      <p>Your cart is empty.</p>
    </div>`;
    totalSpan.textContent = "Rs 0";
    return;
  }

  console.log("Rendering Cart Items:", items); // DEBUG LOG
  let html = "";
  items.forEach((item) => {
    console.log(`Render Item: ${item.name}, Qty: ${item.quantity}`); // DEBUG LOG
    html += `
      <div class="cart-item">
        <div class="cart-item-details">
          <img src="../../Images/products/${item.image}" alt="${item.name}" class="cart-item-img">
          <div class="cart-item-info">
            <h4>${item.name}</h4>
            <p>Rs ${item.price} x ${item.quantity}</p>
          </div>
        </div>
        <div class="cart-item-actions">
          <span class="cart-item-total">Rs ${item.subtotal}</span>
          <button class="remove-btn" onclick="removeFromCart(${item.cart_id})">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>
    `;
  });

  container.innerHTML = html;
  totalSpan.textContent = `Rs ${totalPrice}`;
}

window.removeFromCart = function (cartId) {
  if (!confirm("Are you sure you want to remove this item?")) return;

  fetch("../../Backend/remove_from_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cart_id: cartId }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        // Refresh Modal Content
        openCartModal();
        // Refresh Badge
        updateCartCount();
      } else {
        alert(data.message || "Failed to remove item.");
      }
    })
    .catch((err) => console.error(err));
};

function updateCartCount() {
  fetch("../../Backend/get_cart.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        updateCartBadge(data.total_items);
      } else {
        // likely not logged in, hide badge
        updateCartBadge(0);
      }
    })
    .catch(() => updateCartBadge(0));
}

function updateCartBadge(count) {
  const badge = document.getElementById("cart-count");
  if (!badge) return;

  if (count > 0) {
    badge.textContent = count;
    badge.style.display = "block";
    // Trigger pop animation
    badge.style.animation = "none";
    badge.offsetHeight; /* trigger reflow */
    badge.style.animation =
      "popIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)";
  } else {
    badge.style.display = "none";
  }
}

// Helper for Login Redirects
function handleAuthError(data) {
  if (
    data.not_logged_in ||
    (data.message && data.message.toLowerCase().includes("login"))
  ) {
    if (
      confirm("You need to login to perform this action.\n\nGo to Login page?")
    ) {
      window.location.href = "../Login/login.html";
    }
  } else {
    alert(data.message || "Operation failed.");
  }
}

function showToast(message) {
  const x = document.getElementById("toast");
  if (x) {
    x.innerText = message;
    x.className = "show";
    setTimeout(() => {
      x.className = x.className.replace("show", "");
    }, 3000);
  }
}
