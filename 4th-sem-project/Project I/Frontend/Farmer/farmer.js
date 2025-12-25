// Check authentication and protect page
function checkAuth() {
  fetch("../../Backend/get_user_info.php")
    .then((response) => response.json())
    .then((data) => {
      // If not logged in, OR if logged in but NOT a farmer, redirect to login
      if (!data.loggedIn || data.userType !== "farmer") {
        console.log("Not logged in or not farmer. Redirecting...");
        window.location.href = "../Login/login.html";
      } else {
        console.log("Logged in as:", data.name);
        // Update Profile Info
        const nameEl = document.querySelector(".user-info .name");
        console.log("Target Element:", nameEl);

        const roleEl = document.querySelector(".user-info .role");
        if (nameEl) nameEl.textContent = data.name;
        if (roleEl) roleEl.textContent = "Farmer"; // Or specific role if we had it
      }
    })
    .catch((error) => console.error("Error checking auth:", error));
}

// Prepare page revalidation for back button
window.addEventListener("pageshow", function (event) {
  // If page is loaded from back/forward cache, force reload to re-run auth check
  if (event.persisted) {
    window.location.reload();
  }
  // Always check auth on load
  checkAuth();
});

// Toggle Sidebar
const sidebarLinks = document.querySelectorAll(".sidebar ul li a");
sidebarLinks.forEach((link) => {
  if (link.href === window.location.href) {
    link.classList.add("active");
  }
});

// Toggle Profile Dropdown
function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  dropdown.classList.toggle("show");
}

// Close dropdown when clicking outside
window.onclick = function (event) {
  const dropdown = document.getElementById("userDropdown");

  // Check if the click is OUTSIDE the profile icon AND OUTSIDE the dropdown menu
  if (
    !event.target.closest(".profile-icon") &&
    !event.target.closest(".dropdown-menu")
  ) {
    if (dropdown.classList.contains("show")) {
      dropdown.classList.remove("show");
    }
  }
};

// Toggle Sidebar (Mobile)
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  sidebar.classList.toggle("active");
  overlay.classList.toggle("active");
}

const product = document.getElementById("product");
const earnings = document.getElementById("earnings");
const orders = document.getElementById("orders");
const lowStock = document.getElementById("lowStock");

function fetchDashboardStats() {
  fetch("../../Backend/get_farmer_stats.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        const stats = data.stats;
        // Update DOM elements with real data
        if (product) product.textContent = stats.total_products;
        if (earnings)
          earnings.textContent = "Rs. " + stats.total_earnings.toLocaleString();
        if (orders) orders.textContent = stats.total_orders;

        // Update sidebar names
        const sidebarName = document.getElementById("sidebarFarmerName");
        if (sidebarName)
          sidebarName.textContent =
            document.querySelector(".user-info .name").textContent;
      } else {
        console.error("Failed to load stats:", data.message);
      }
    })
    .catch((err) => console.error("Error fetching stats:", err));
}

function fetchRecentOrders() {
  const tbody = document.getElementById("recentOrdersBody");
  if (!tbody) return;

  fetch("../../Backend/get_farmer_recent_orders.php")
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        tbody.innerHTML = "";
        if (data.recent_orders.length === 0) {
          tbody.innerHTML =
            '<tr><td colspan="4" style="text-align:center;">No recent orders found.</td></tr>';
          return;
        }

        data.recent_orders.forEach((order) => {
          const statusClass =
            "status-" + order.order_status.toLowerCase().replace(/ /g, "-");
          tbody.innerHTML += `
            <tr>
              <td>#${order.order_id}</td>
              <td>${order.customer_name}</td>
              <td>Rs. ${order.order_total.toLocaleString()}</td>
              <td><span class="status-badge ${statusClass}">${
            order.order_status
          }</span></td>
            </tr>
          `;
        });
      }
    })
    .catch((err) => console.error("Error fetching recent orders:", err));
}

// Call on load
document.addEventListener("DOMContentLoaded", () => {
  fetchDashboardStats();
  fetchRecentOrders();
});

// Logout Logic
document.addEventListener("DOMContentLoaded", () => {
  const logoutBtns = document.querySelectorAll(".logout-item, #logout-btn");

  logoutBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      fetch("../../Backend/logout.php")
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            window.location.href = "../Login/login.html";
          }
        })
        .catch((error) => console.error("Error logging out:", error));
    });
  });
});
