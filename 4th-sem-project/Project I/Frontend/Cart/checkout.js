document.addEventListener("DOMContentLoaded", () => {
  // 1. Check Auth & Load User Data
  checkAuthAndLoadData();

  // 2. Load Cart Items
  loadCartSummary();

  // 3. Handle Payment Option UI
  setupPaymentOptions();

  // 4. Handle Place Order
  document
    .getElementById("placeOrderBtn")
    .addEventListener("click", placeOrder);
});

function setupPaymentOptions() {
  const options = document.querySelectorAll(".payment-option");
  options.forEach((option) => {
    option.addEventListener("click", function () {
      // Remove selected class from all
      options.forEach((opt) => opt.classList.remove("selected"));
      // Add to clicked
      this.classList.add("selected");
      // Check the radio inside
      this.querySelector('input[type="radio"]').checked = true;
    });
  });
}

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

  // Validation
  if (!name || !phone || !address) {
    alert("Please fill in all delivery details.");
    return;
  }

  if (phone.length < 10) {
    alert("Please enter a valid phone number.");
    return;
  }

  // UI Feedback
  btn.disabled = true;
  btn.innerText = "Processing Order...";

  const payload = {
    full_name: name,
    phone: phone,
    address: address,
    notes: notes,
    payment_method: payment,
  };

  // Step 1: Create the Order in the Backend
  fetch("../../Backend/place_order.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        if (payment === "ONLINE") {
          btn.innerText = "Redirecting to eSewa...";

          // Step 2: Initialize eSewa Payment Context
          fetch("../../Backend/payments/esewa_init.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ transaction_uuid: data.transaction_uuid }), // Optional: pass UUID for logging
          })
            .then((res) => res.json())
            .then((initData) => {
              if (initData.success) {
                // Step 3: Populate Hidden eSewa Form & Submit
                const form = document.getElementById("esewaForm");
                const d = initData.esewa_data;

                document.getElementById("esewa_amount").value = d.amount;
                document.getElementById("esewa_tax_amount").value =
                  d.tax_amount;
                document.getElementById("esewa_total_amount").value =
                  d.total_amount;
                document.getElementById("esewa_transaction_uuid").value =
                  d.transaction_uuid;
                document.getElementById("esewa_product_code").value =
                  d.product_code;
                document.getElementById("esewa_product_service_charge").value =
                  d.product_service_charge;
                document.getElementById("esewa_product_delivery_charge").value =
                  d.product_delivery_charge;
                document.getElementById("esewa_signed_field_names").value =
                  d.signed_field_names;
                document.getElementById("esewa_signature").value = d.signature;
                document.getElementById("esewa_success_url").value =
                  d.success_url;
                document.getElementById("esewa_failure_url").value =
                  d.failure_url;

                console.log("Submitting to eSewa Testing (RC/UAT)...");
                form.submit();
              } else {
                throw new Exception(initData.message || "Payment init failed");
              }
            })
            .catch((err) => {
              console.error(err);
              alert("Payment Error: " + err.message);
              btn.disabled = false;
              btn.innerText = "Confirm Order";
            });
        } else {
          // Success for COD
          window.location.href = "order_success.html";
        }
      } else {
        alert("Order Error: " + data.message);
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
