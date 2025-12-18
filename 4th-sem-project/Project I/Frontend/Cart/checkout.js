document.addEventListener("DOMContentLoaded", () => {
  // 1. Check Auth & Load User Data
  checkAuthAndLoadData();

  // 2. Load Cart Items
  loadCartSummary();

  // 3. Handle Place Order
  document
    .getElementById("placeOrderBtn")
    .addEventListener("click", placeOrder);
});

async function checkAuthAndLoadData() {
  try {
    const res = await fetch("../../Backend/get_user_info.php");
    const data = await res.json();

    if (!data.loggedIn) {
      alert("Please login to proceed.");
      window.location.href = "../Login/login.html";
      return;
    }

    // Auto-fill Form
    if (data.name) {
      // Try to split name if needed, or just set it
      // Backend returns 'name' (Full Name) ?
      document.getElementById("fullName").value = data.name || "";
    }

    // Backend 'get_user_info.php' might not return Phone/Address currently.
    // We should check that file. If it doesn't, we might need to update it
    // OR just let user fill it. For now, we try.
    if (data.phone) document.getElementById("phone").value = data.phone;
    if (data.address) document.getElementById("address").value = data.address;
  } catch (err) {
    console.error("Auth Check Error:", err);
  }
}

async function loadCartSummary() {
  try {
    const res = await fetch("../../Backend/get_cart.php");
    const data = await res.json();
    const container = document.getElementById("orderItems");
    const totalEl = document.getElementById("finalTotal");

    if (!data.success || !data.items || data.items.length === 0) {
      container.innerHTML = "<p>Your cart is empty.</p>";
      // Redirect back?
      return;
    }

    let html = "";
    data.items.forEach((item) => {
      html += `
                <div class="order-item">
                    <div style="display:flex; align-items:center;">
                        <img src="../../Images/products/${item.image}" alt="${item.name}">
                        <div class="item-info">
                            <h4 style="margin:0;">${item.name}</h4>
                            <small>Qty: ${item.quantity}</small>
                        </div>
                    </div>
                    <span style="font-weight:bold;">Rs ${item.subtotal}</span>
                </div>
            `;
    });

    container.innerHTML = html;
    totalEl.textContent = `Rs ${data.total_price}`;
  } catch (err) {
    console.error("Cart Load Error:", err);
  }
}

function placeOrder() {
  const btn = document.getElementById("placeOrderBtn");
  const name = document.getElementById("fullName").value.trim();
  const phone = document.getElementById("phone").value.trim();
  const address = document.getElementById("address").value.trim();
  const notes = document.getElementById("notes").value.trim();
  const payment = document.querySelector('input[name="payment"]:checked').value;

  if (!name || !phone || !address) {
    alert("Please fill in all delivery details.");
    return;
  }

  if (phone.length < 10) {
    alert("Please enter a valid phone number.");
    return;
  }

  // Disable button
  btn.disabled = true;
  btn.innerText = "Processing...";

  const payload = {
    full_name: name,
    phone: phone,
    address: address,
    notes: notes,
    payment_method: payment,
  };

  fetch("../../Backend/place_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        // Redirect to success page
        window.location.href = "order_success.html";
      } else {
        alert("Order Failed: " + data.message);
        btn.disabled = false;
        btn.innerText = "Confirm Order";
      }
    })
    .catch((err) => {
      console.error(err);
      alert("Network Error. Please try again.");
      btn.disabled = false;
      btn.innerText = "Confirm Order";
    });
}

