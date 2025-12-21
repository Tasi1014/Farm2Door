let allOrders = [];
let currentFilter = "dispatched";

document.addEventListener("DOMContentLoaded", () => {
  fetchOrders();
});

function fetchOrders() {
  fetch("../../Backend/get_admin_orders.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        allOrders = data.orders;
        renderOrders();
      } else {
        console.error("Failed to fetch orders:", data.message);
      }
    });
}

function setFilter(filter) {
  currentFilter = filter;
  document.querySelectorAll(".tab-btn").forEach((btn) => {
    const text = btn.innerText.toLowerCase();
    if (filter === "dispatched")
      btn.classList.toggle("active", text.includes("farmers"));
    else if (filter === "pickup")
      btn.classList.toggle("active", text.includes("pickup"));
    else if (filter === "collected")
      btn.classList.toggle("active", text.includes("collected"));
    else if (filter === "cancelled")
      btn.classList.toggle("active", text.includes("cancelled"));
  });
  renderOrders();
}

function renderOrders() {
  const container = document.getElementById("orders-container");
  container.innerHTML = "";

  const filtered = allOrders.filter((order) => {
    const status = order.order_status.toLowerCase();
    if (currentFilter === "dispatched") return status === "dispatched";
    if (currentFilter === "pickup")
      return status === "received" || status === "ready for pickup";
    if (currentFilter === "collected") return status === "fulfilled";
    if (currentFilter === "cancelled")
      return status === "cancelled" || status === "rejected";
    return true;
  });

  if (filtered.length === 0) {
    container.innerHTML = `<div class="empty-state">
            <i class="fa-solid fa-clipboard-check"></i>
            <h3>No orders found in this category.</h3>
        </div>`;
    return;
  }

  filtered.forEach((order) => {
    const card = document.createElement("div");
    card.className = `order-card ${order.order_status
      .toLowerCase()
      .replace(/ /g, "-")}`;

    let actionsHtml = "";
    if (order.order_status === "Dispatched") {
      actionsHtml = `<button class="btn-detail" onclick="updateStatus(event, ${order.order_id}, 'Received')">Mark as Received</button>`;
    } else if (order.order_status === "Received") {
      actionsHtml = `<button class="btn-detail" onclick="updateStatus(event, ${order.order_id}, 'Ready for Pickup')">Ready for Pickup</button>`;
    } else if (order.order_status === "Ready for Pickup") {
      const fulfillBtnText =
        order.payment_method === "COD"
          ? "Received Cash & Mark Collected"
          : "Mark Collected";
      actionsHtml = `
                <button class="btn-detail" onclick="sendNotification(${order.order_id})">Resend Email</button>
                <button class="btn-detail" onclick="updateStatus(event, ${order.order_id}, 'Fulfilled')">${fulfillBtnText}</button>
            `;
    } else if (
      (order.order_status === "Cancelled" ||
        order.order_status === "Rejected") &&
      order.payment_method === "ONLINE" &&
      order.payment_status === "Paid"
    ) {
      actionsHtml = `<button class="btn-cancel" onclick="openRefundModal(${order.order_id})">Process Refund</button>`;
    }

    actionsHtml += `<button class="btn-view-items" onclick="viewDetails(${order.order_id})">View Details</button>`;

    card.innerHTML = `
            <div class="order-top">
                <div>
                    <span class="order-id">#${order.order_id}</span>
                    <div class="farmer-tag">Farmer: ${
                      order.items[0]?.farmer_name || "Multiple"
                    }</div>
                </div>
                <span class="status-badge status-${order.order_status
                  .toLowerCase()
                  .replace(/ /g, "-")}">${order.order_status}</span>
            </div>
            <div class="order-body">
                <div class="order-info-row">
                    <strong>Customer:</strong> ${order.shipping_name} (${
      order.shipping_phone
    })
                </div>
                <div class="order-info-row">
                    <strong>Total Amount:</strong> Rs. ${order.total_amount}
                </div>
                <div class="order-info-row">
                    <strong>Payment:</strong> <span class="pay-method">${
                      order.payment_method
                    }</span> - ${order.payment_status}
                </div>
            </div>
            <div class="order-actions">
                ${actionsHtml}
            </div>
        `;
    container.appendChild(card);
  });
}

function updateStatus(e, orderId, status) {
  const btn = e.target;
  const originalText = btn.innerText;

  // Visual feedback: Disable and show loading
  btn.disabled = true;
  btn.innerText = "Processing...";

  fetch("../../Backend/update_admin_order_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ order_id: orderId, status: status }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        if (status === "Ready for Pickup") {
          // Change text specifically for the email notification phase
          btn.innerText = "Sending notification...";
          sendNotification(orderId, btn);
        } else {
          fetchOrders();
        }
      } else {
        alert(data.message);
        btn.disabled = false;
        btn.innerText = originalText;
      }
    })
    .catch((err) => {
      console.error("Status update error:", err);
      btn.disabled = false;
      btn.innerText = originalText;
    });
}

function sendNotification(orderId, manualBtn = null) {
  // If we have a button passed directly (automated flow) use it,
  // otherwise find it (manual "Resend" flow)
  let btn = manualBtn;

  if (!btn) {
    // Try to find the button if called from "Resend Email" click
    btn = window.event ? window.event.target : null;

    // If still no button, look for it in the UI (fallback)
    if (!btn || !btn.classList.contains("btn-detail")) {
      const cards = document.querySelectorAll(".order-card");
      cards.forEach((card) => {
        if (card.querySelector(".order-id")?.innerText.includes(orderId)) {
          btn = Array.from(card.querySelectorAll("button")).find((b) =>
            b.innerText.includes("Email")
          );
        }
      });
    }
  }

  const originalText = btn ? btn.innerText : "Resend Email";
  if (btn) {
    btn.innerText = "Sending notification...";
    btn.disabled = true;
  }

  fetch("../../Backend/send_pickup_notification.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ order_id: orderId }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert("Success: " + data.message);
        btn.disabled = false;
        btn.innerText = originalText;
      } else {
        alert("Error: " + data.message);
      }
    })
    .catch((err) => {
      console.error("Mail error:", err);
      alert("Failed to send notification.");
    })
    .finally(() => {
      btn.innerText = originalText;
      btn.disabled = false;
    });
}

let activeRefundOrderId = null;
let activeRefundPaymentId = null;

function openRefundModal(orderId) {
  const order = allOrders.find((o) => o.order_id == orderId);
  activeRefundOrderId = orderId;
  activeRefundPaymentId = order.payment_id;

  const total = parseFloat(order.total_amount);
  const fee = total * 0.1;
  const netRefund = total - fee;

  const info = document.getElementById("refund-info");
  info.innerHTML = `
        <div style="background: #f7f7f7; padding: 15px; border-radius: 8px; border: 1px solid #eee;">
            <p style="margin: 0; display: flex; justify-content: space-between;"><strong>Order Amount:</strong> <span>Rs. ${total.toFixed(
              2
            )}</span></p>
            <p style="margin: 5px 0; color: #d32f2f; display: flex; justify-content: space-between;"><strong>Service Fee (10%):</strong> <span>- Rs. ${fee.toFixed(
              2
            )}</span></p>
            <hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
            <p style="margin: 0; font-size: 1.1em; color: #4caf50; display: flex; justify-content: space-between;"><strong>Refund Amount:</strong> <span>Rs. ${netRefund.toFixed(
              2
            )}</span></p>
        </div>
        <p style="font-size: 0.85em; color: #666; margin-top: 10px;">Customer: ${
          order.shipping_name
        }</p>
    `;
  document.getElementById("refundModal").style.display = "flex";
}

function closeRefundModal() {
  document.getElementById("refundModal").style.display = "none";
}

function submitRefund() {
  const btn = document.getElementById("confirmRefundBtn");

  btn.disabled = true;
  btn.innerText = "Processing...";

  fetch("../../Backend/process_refund.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      order_id: activeRefundOrderId,
      payment_id: activeRefundPaymentId,
    }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        closeRefundModal();
        fetchOrders(); // Refresh list
      } else {
        alert("Refund Failed: " + data.message);
        btn.disabled = false;
        btn.innerText = "Confirm Refund";
      }
    })
    .catch((err) => {
      console.error("Refund error:", err);
      alert("An error occurred while processing the refund.");
      btn.disabled = false;
      btn.innerText = "Confirm Refund";
    });
}

function viewDetails(orderId) {
  console.log("Viewing details for order:", orderId);
  const order = allOrders.find((o) => o.order_id == orderId);
  if (!order) {
    console.error("Order not found in allOrders array:", orderId);
    return;
  }

  const modalDetails = document.getElementById("modal-details");
  modalDetails.innerHTML = `
        <h3><i class="fa-solid fa-receipt"></i> Order Details #${
          order.order_id
        }</h3>
        <div class="detail-grid">
            <div class="detail-section">
                <h4><i class="fa-solid fa-basket-shopping"></i> Items Summary</h4>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Farmer</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${order.items
                          .map(
                            (item) => `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>${item.farmer_name}</td>
                                <td>${item.quantity}</td>
                                <td>Rs. ${item.subtotal}</td>
                            </tr>
                        `
                          )
                          .join("")}
                    </tbody>
                </table>
            </div>
            <div class="detail-section">
                <h4><i class="fa-solid fa-truck-fast"></i> Shipping Information</h4>
                <p><strong>Recipient:</strong> ${order.shipping_name}</p>
                <p><strong>Contact:</strong> ${order.shipping_phone}</p>
                <p><strong>Address:</strong> ${order.shipping_address}</p>
                <p><strong>Notes:</strong> ${order.shipping_notes || "None"}</p>
            </div>
        </div>
        <div class="detail-grid" style="margin-top:20px;">
            <div class="detail-section">
                <h4><i class="fa-solid fa-credit-card"></i> Payment Details</h4>
                <p><strong>Total Amount:</strong> Rs. ${order.total_amount}</p>
                <p><strong>Method:</strong> ${order.payment_method}</p>
                <p><strong>Status:</strong> <span class="status-badge status-${order.payment_status.toLowerCase()}">${
    order.payment_status
  }</span></p>
            </div>
        </div>
    `;
  document.getElementById("orderModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("orderModal").style.display = "none";
}
