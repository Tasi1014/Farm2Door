let allOrders = [];
let currentFilter = "pending";

document.addEventListener("DOMContentLoaded", () => {
  fetchOrders();
});

function fetchOrders() {
  fetch("../../Backend/get_farmer_orders.php")
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
    btn.classList.toggle(
      "active",
      btn.innerText.toLowerCase().includes(filter),
    );
  });
  renderOrders();
}

function renderOrders() {
  const container = document.getElementById("orders-container");
  container.innerHTML = "";

  const filtered = allOrders.filter((order) => {
    const status = order.order_status.toLowerCase();
    if (currentFilter === "pending") return status === "pending";
    if (currentFilter === "processing")
      return (
        status === "processing" ||
        status === "dispatched" ||
        status === "received" ||
        status === "ready for pickup"
      );
    if (currentFilter === "fulfilled") return status === "fulfilled";
    if (currentFilter === "cancelled")
      return status === "cancelled" || status === "rejected";
    return true;
  });

  if (filtered.length === 0) {
    container.innerHTML = `<div class="empty-state">
            <i class="fa-solid fa-clipboard-list"></i>
            <h3>No orders found in this category.</h3>
        </div>`;
    return;
  }

  filtered.forEach((order) => {
    const card = document.createElement("div");
    card.className = `order-card ${order.order_status.toLowerCase()}`;

    let actionsHtml = ""; // Declare and reset actionsHtml inside the loop
    if (order.order_status === "Pending") {
      actionsHtml = `
                <button class="btn-detail" onclick="updateStatus(this, ${order.order_id}, 'Processing')">Accept</button>
                <button class="btn-cancel" onclick="openRejectionModal(${order.order_id})">Reject</button>
            `;
    } else if (order.order_status === "Processing") {
      actionsHtml = `
                <button class="btn-detail" onclick="updateStatus(this, ${order.order_id}, 'Dispatched')">Mark Dispatched</button>
            `;
    }

    actionsHtml += `<button class="btn-view-items" onclick="viewDetails(${order.order_id})">View Details</button>`;

    card.innerHTML = `
            <div class="order-top">
                <div>
                    <span class="order-id">#${order.order_id}</span>
                </div>
                <span class="status-badge status-${order.order_status.toLowerCase()}">${
                  order.order_status
                }</span>
            </div>
            <div class="order-body">
                <div class="order-info-row">
                    <strong>Customer:</strong> ${order.shipping_name}
                </div>
                <div class="order-info-row">
                    <strong>Products:</strong> ${order.farmer_items
                      .map((i) => i.product_name)
                      .join(", ")}
                </div>
                <div class="order-info-row">
                    <strong>Farmer Total:</strong> Rs. ${order.farmer_total}
                </div>
                <div class="order-info-row">
                    <strong>Payment:</strong> <span class="pay-method">${order.payment_method}</span> 
                    (${order.p_status || order.payment_status || "Pending"})
                </div>
            </div>
            <div class="order-actions">
                ${actionsHtml}
            </div>
        `;
    container.appendChild(card);
  });
}

function updateStatus(btn, orderId, status, reason = null) {
  const originalText = btn ? btn.innerText : "";
  if (btn) {
    btn.disabled = true;
    btn.innerText = status === "Processing" ? "Accepting..." : "Rejecting...";
    if (status === "Dispatched") btn.innerText = "Dispatching...";
  }

  fetch("../../Backend/update_farmer_order_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ order_id: orderId, status: status, reason: reason }),
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        fetchOrders();
        if (reason) closeRejectionModal();
      } else {
        alert(data.message);
        if (btn) {
          btn.disabled = false;
          btn.innerText = originalText;
        }
      }
    })
    .catch((err) => {
      console.error("Update status error:", err);
      if (btn) {
        btn.disabled = false;
        btn.innerText = originalText;
      }
    });
}

let activeRejectionId = null;
function openRejectionModal(orderId) {
  activeRejectionId = orderId;
  document.getElementById("rejectionModal").style.display = "flex";
}

function closeRejectionModal() {
  document.getElementById("rejectionModal").style.display = "none";
  document.getElementById("rejection-reason").value = "";
}

function submitRejection(btn) {
  const reason = document.getElementById("rejection-reason").value.trim();
  if (!reason) {
    alert("Please provide a reason for rejection.");
    return;
  }
  updateStatus(btn, activeRejectionId, "Rejected", reason);
}

function viewDetails(orderId) {
  const order = allOrders.find((o) => o.order_id === orderId);
  if (!order) return;

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
                        <tr><th>Product</th><th>Qty</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        ${order.farmer_items
                          .map(
                            (item) => `
                            <tr>
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>Rs. ${item.subtotal}</td>
                            </tr>
                        `,
                          )
                          .join("")}
                    </tbody>
                </table>
            </div>
            <div class="detail-section">
                <h4><i class="fa-solid fa-truck"></i> Shipping Information</h4>
                <p><strong>Name:</strong> ${order.shipping_name}</p>
                <p><strong>Phone:</strong> ${order.shipping_phone}</p>
                <p><strong>Address:</strong> ${order.shipping_address}</p>
                <p><strong>Notes:</strong> ${
                  order.shipping_notes || "No special instructions."
                }</p>
            </div>
        </div>
        <div class="detail-grid" style="margin-top:20px;">
            <div class="detail-section">
                <h4><i class="fa-solid fa-credit-card"></i> Payment Info</h4>
                <p><strong>Method:</strong> ${order.payment_method}</p>
                <p><strong>Status:</strong> <span class="status-badge status-${(order.p_status || order.payment_status || "Pending").toString().toLowerCase()}">${order.p_status || order.payment_status || "Pending"}</span></p>
                <p><strong>Farmer Share:</strong> Rs. ${order.farmer_total}</p>
            </div>
        </div>
    `;
  document.getElementById("orderModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("orderModal").style.display = "none";
}
