document.addEventListener("DOMContentLoaded", () => {
  // 1. Initial Cart Count
  updateCartCount();

  // 2. Attach Listener to Cart Icon
  const cartIcon = document.getElementById("cart");
  if (cartIcon) {
    cartIcon.addEventListener("click", openCartModal);

    // Add cursor pointer style
    cartIcon.style.cursor = "pointer";

    // Add Badge Element if not exists
    if (!document.getElementById("cart-count")) {
      const badge = document.createElement("span");
      badge.id = "cart-count";
      badge.style.cssText = `
                position: absolute;
                top: -8px;
                right: -8px;
                background-color: #e74c3c;
                color: white;
                border-radius: 50%;
                padding: 2px 6px;
                font-size: 10px;
                display: none;
            `;
      // Parent needs relative position
      cartIcon.parentElement.style.position = "relative";
      cartIcon.parentElement.appendChild(badge);
    }
  }

  // 3. Inject Modal HTML if not exists
  if (!document.getElementById("cartModal")) {
    const modalHTML = `
            <div id="cartModal" class="modal" style="display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.5);">
                <div class="modal-content" style="background-color:#fefefe; margin:10% auto; padding:20px; border:1px solid #888; width:90%; max-width:600px; border-radius:10px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd; padding-bottom:10px;">
                        <h2>Your Cart</h2>
                        <span class="close-cart" style="font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
                    </div>
                    <div id="cart-items" style="margin-top:20px;">
                        <p>Loading cart...</p>
                    </div>
                    <div id="cart-footer" style="margin-top:20px; border-top:1px solid #ddd; padding-top:15px; text-align:right;">
                        <h3>Total: Rs <span id="cart-total">0</span></h3>
                        <button id="checkout-btn" style="background-color:#2ecc71; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer; font-size:16px; margin-top:10px;">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        `;
    document.body.insertAdjacentHTML("beforeend", modalHTML);

    // Modal Close Logic
    document.querySelector(".close-cart").addEventListener("click", () => {
      document.getElementById("cartModal").style.display = "none";
    });

    window.onclick = function (event) {
      const modal = document.getElementById("cartModal");
      if (event.target == modal) {
        modal.style.display = "none";
      }
    };

    // Checkout Placeholder
    document.getElementById("checkout-btn").addEventListener("click", () => {
      alert("Proceeding to checkout (Phase 3)...");
      // Implement actual checkout logic later
    });
  }
});

// Global Function to Add to Cart (can be called from other scripts)
function addToCart(event) {
  if (event) event.stopPropagation(); // prevent card click

  // Determine ID
  let btn = event.currentTarget || event.target;
  let id = btn.dataset.id;

  if (!id) return;

  // Optimistic UI update (optional)? No, let's wait for server to be sure.
  fetch("../../Backend/add_to_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ product_id: id, quantity: 1 }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        updateCartCount();
        alert("Product added to cart!");
      } else {
        if (data.not_logged_in) {
          // Try to open login modal if exists, else redirect
          if (typeof openLoginModal === "function") {
            openLoginModal();
          } else {
            window.location.href = "../Login/login.html";
          }
        } else {
          alert(data.message);
        }
      }
    })
    .catch((err) => console.error(err));
}

function updateCartCount() {
  // We can fetch just the count, or get full cart and count it.
  // add_to_cart.php returns count too, but let's have a dedicated fetch for page load
  fetch("../../Backend/get_cart.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        const badge = document.getElementById("cart-count");
        if (badge) {
          if (data.total_items > 0) {
            badge.textContent = data.total_items;
            badge.style.display = "block";
          } else {
            badge.style.display = "none";
          }
        }
      }
    })
    .catch((err) => console.error(err));
}

function openCartModal() {
  const modal = document.getElementById("cartModal");
  const container = document.getElementById("cart-items");
  const totalSpan = document.getElementById("cart-total");

  modal.style.display = "block";
  container.innerHTML = "<p>Loading...</p>";

  fetch("../../Backend/get_cart.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        if (data.items.length === 0) {
          container.innerHTML = "<p>Your cart is empty.</p>";
          totalSpan.textContent = "0";
        } else {
          let html = '<ul style="list-style:none; padding:0;">';
          data.items.forEach((item) => {
            html += `
                        <li style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <img src="../../Images/products/${item.image}" style="width:50px; height:50px; object-fit:cover; border-radius:5px;">
                                <div>
                                    <div style="font-weight:bold;">${item.name}</div>
                                    <div style="font-size:12px; color:#666;">Rs ${item.price} x ${item.quantity}</div>
                                </div>
                            </div>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="font-weight:bold;">Rs ${item.subtotal}</span>
                                <button onclick="removeFromCart(${item.cart_id})" style="background:none; border:none; color:red; cursor:pointer;"><i class="fa fa-trash"></i></button>
                            </div>
                        </li>
                    `;
          });
          html += "</ul>";
          container.innerHTML = html;
          totalSpan.textContent = data.total_price;
        }
      } else {
        container.innerHTML = `<p>${data.message}</p>`;
      }
    })
    .catch((err) => {
      container.innerHTML = "<p>Error loading cart.</p>";
      console.error(err);
    });
}

function removeFromCart(cartId) {
  if (!confirm("Remove this item?")) return;

  fetch("../../Backend/remove_from_cart.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ cart_id: cartId }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        openCartModal(); // Refresh list
        updateCartCount(); // Refresh badge
      } else {
        alert(data.message);
      }
    })
    .catch((err) => console.error(err));
}
