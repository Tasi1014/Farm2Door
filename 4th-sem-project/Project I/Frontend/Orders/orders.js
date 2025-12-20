document.addEventListener("DOMContentLoaded", () => {
  const ordersContainer = document.getElementById("orders-container");
  const tabBtns = document.querySelectorAll(".tab-btn");
  const modal = document.getElementById("order-modal");
  const closeModal = document.querySelector(".close-modal");
  const detailsContent = document.getElementById("order-details-content");

  let allOrders = [];

  // Initial Fetch
  fetchOrders();

  // Tab Switching
  tabBtns.forEach((btn) => {
    btn.addEventListener("click", () => {
      tabBtns.forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      renderOrders(btn.dataset.tab);
    });
  });

  // Modal Closing
  closeModal.onclick = () => (modal.style.display = "none");
  window.onclick = (event) => {
    if (event.target == modal) modal.style.display = "none";
  };

  async function fetchOrders() {
    try {
      const response = await fetch("../../Backend/get_user_orders.php");
      const data = await response.json();

      if (data.success) {
        allOrders = data.orders;
        renderOrders("all");
      } else {
        ordersContainer.innerHTML = `<div class="empty-state">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <h3>${data.message}</h3>
                    <a href="../Login/login.html" class="btn">Login Now</a>
                </div>`;
      }
    } catch (error) {
      console.error("Fetch error:", error);
      ordersContainer.innerHTML = `<p style="text-align:center; padding: 50px;">Error connecting to server. Please try again later.</p>`;
    }
  }

  function renderOrders(filter) {
    let filtered = allOrders;

    if (filter === "pending") {
      filtered = allOrders.filter(
        (o) => o.order_status === "Pending" || o.payment_status === "Pending"
      );
    } else if (filter === "delivered") {
      // "To Receive" should show orders that are currently in progress (Processing or Shipped)
      filtered = allOrders.filter(
        (o) => o.order_status === "Processing" || o.order_status === "Shipped"
      );
    }

    if (filtered.length === 0) {
      if (allOrders.length === 0) {
        // Truly no orders at all
        ordersContainer.innerHTML = `<div class="empty-state">
                  <i class="fa-solid fa-box-open"></i>
                  <h3>You haven't placed any orders yet.</h3>
                  <p>Support your local farmers and fill your cart!</p>
                  <a href="../Product/product.html" class="btn">Start Shopping</a>
              </div>`;
      } else {
        // Has orders, but none in this category
        let message = "No orders found in this category.";
        if (filter === "pending")
          message = "You have no pending payments. Great!";
        if (filter === "delivered")
          message = "No orders are currently on their way.";

        ordersContainer.innerHTML = `<div class="empty-state">
                  <i class="fa-solid fa-clipboard-check"></i>
                  <h3>${message}</h3>
                  <p>Check the "All" tab to see your full order history.</p>
              </div>`;
      }
      return;
    }

    ordersContainer.innerHTML = filtered
      .map((order) => {
        const orderDate = new Date(order.order_date).toLocaleDateString(
          "en-US",
          {
            year: "numeric",
            month: "short",
            day: "numeric",
          }
        );

        const statusClass = order.order_status.toLowerCase();

        return `
            <div class="order-card ${statusClass}">
                <div class="order-top">
                    <div>
                        <p class="order-id">#${
                          order.transaction_uuid || order.order_id
                        }</p>
                        <p class="order-date">${orderDate}</p>
                    </div>
                    <span class="status-badge status-${statusClass}">${
          order.order_status
        }</span>
                </div>
                <div class="order-info">
                    <p class="order-total">Rs ${order.total_amount}</p>
                    <div class="order-actions">
                        <button class="btn-detail" onclick="viewDetails(${
                          order.order_id
                        })">View Items</button>
                        ${
                          order.order_status === "Processing" ||
                          order.order_status === "Pending"
                            ? `<button class="btn-cancel" onclick="cancelOrder(${order.order_id})">Cancel</button>`
                            : ""
                        }
                    </div>
                </div>
            </div>
        `;
      })
      .join("");
  }

  window.viewDetails = async (orderId) => {
    // Show modal with loading
    detailsContent.innerHTML = "<p>Loading details...</p>";
    modal.style.display = "flex";

    try {
      const response = await fetch(
        `../../Backend/get_order_items.php?order_id=${orderId}`
      );
      const data = await response.json();

      if (data.success) {
        const order = allOrders.find((o) => o.order_id == orderId);

        detailsContent.innerHTML = `
                <div class="detail-section">
                    <h3>Items in Order</h3>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.items
                              .map(
                                (item) => `
                                <tr>
                                    <td>
                                        <div style="display:flex; align-items:center; gap: 10px;">
                                            <img src="../../Images/products/${item.image}" class="item-img" alt="${item.product_name}">
                                            <span>${item.product_name}</span>
                                        </div>
                                    </td>
                                    <td>Rs ${item.price_per_unit}</td>
                                    <td>${item.quantity}</td>
                                    <td>Rs ${item.subtotal}</td>
                                </tr>
                            `
                              )
                              .join("")}
                        </tbody>
                    </table>
                </div>

                <div class="detail-section">
                    <h3>Shipping Information</h3>
                    <div class="shipping-grid">
                        <div class="info-group">
                            <label>Recipient Name</label>
                            <p>${order.shipping_name}</p>
                        </div>
                        <div class="info-group">
                            <label>Phone Number</label>
                            <p>${order.shipping_phone}</p>
                        </div>
                        <div class="info-group" style="grid-column: span 2;">
                            <label>Delivery Address</label>
                            <p>${order.shipping_address}</p>
                        </div>
                        ${
                          order.shipping_notes
                            ? `<div class="info-group" style="grid-column: span 2;">
                                <label>Notes</label>
                                <p>${order.shipping_notes}</p>
                            </div>`
                            : ""
                        }
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Payment Details</h3>
                    <div class="shipping-grid">
                        <div class="info-group">
                            <label>Payment Method</label>
                            <p>${order.payment_method}</p>
                        </div>
                        <div class="info-group">
                            <label>Transaction ID</label>
                            <p>${order.transaction_id || "N/A"}</p>
                        </div>
                        <div class="info-group">
                            <label>Payment Status</label>
                            <p>${order.payment_status}</p>
                        </div>
                        <div class="info-group">
                            <label>Amount Paid</label>
                            <p>Rs ${order.amount_paid}</p>
                        </div>
                    </div>
                </div>
            `;
      }
    } catch (error) {
      console.error("Error fetching items:", error);
      detailsContent.innerHTML = "<p>Error loading order items.</p>";
    }
  };

  window.cancelOrder = async (orderId) => {
    if (!confirm("Are you sure you want to cancel this order?")) return;

    try {
      const response = await fetch("../../Backend/cancel_order.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id: orderId }),
      });
      const data = await response.json();

      if (data.success) {
        alert("Order cancelled successfully.");
        fetchOrders(); // Refresh
      } else {
        alert("Error: " + data.message);
      }
    } catch (error) {
      console.error("Cancel error:", error);
      alert("Failed to connect to server.");
    }
  };
});
