window.addEventListener("pageshow", function (event) {
  // If page is loaded from back/forward cache, force reload to re-run auth check
  if (event.persisted) {
    window.location.reload();
  }
  // Always check auth on load
  checkAuth();
});

// Toggle Sidebar (Mobile)
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  if (sidebar && overlay) {
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  }
}

// Sidebar Active State
const sidebarLinks = document.querySelectorAll(".sidebar ul li a");
sidebarLinks.forEach((link) => {
  if (link.href === window.location.href) {
    link.classList.add("active");
  }
});

// Toggle Profile Dropdown
function toggleDropdown(event) {
  if (event) event.stopPropagation();
  const dropdown = document.getElementById("userDropdown");
  if (dropdown) dropdown.classList.toggle("show");
}

window.addEventListener("click", function (event) {
  const dropdown = document.getElementById("userDropdown");
  if (
    dropdown &&
    !event.target.closest(".profile-icon") &&
    !event.target.closest(".dropdown-menu")
  ) {
    if (dropdown.classList.contains("show")) {
      dropdown.classList.remove("show");
    }
  }
});

// --- Dashboard Stats ---
function animateValue(id, start, end, duration) {
  const obj = document.getElementById(id);
  if (!obj) return;

  let startTimestamp = null;
  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    let value = progress * (end - start) + start;

    if (id === "revenue") {
      obj.innerHTML =
        "Rs. " +
        value.toLocaleString(undefined, {
          minimumFractionDigits: 0,
          maximumFractionDigits: 2,
        });
    } else {
      obj.innerHTML = Math.floor(value).toLocaleString();
    }
    if (progress < 1) window.requestAnimationFrame(step);
  };
  window.requestAnimationFrame(step);
}

async function fetchDashboardStats() {
  try {
    const res = await fetch("../../Backend/Admin/get_admin_stats.php");
    const data = await res.json();

    if (data.success) {
      animateValue("revenue", 0, data.stats.total_revenue, 1500);
      animateValue("orders", 0, data.stats.total_orders, 1500);
      animateValue("products", 0, data.stats.total_products, 1500);
      animateValue("farmers", 0, data.stats.total_farmers, 1500);
      animateValue("consumers", 0, data.stats.total_consumers, 1500);
    } else {
      console.error("Failed to load stats:", data.message);
    }
  } catch (err) {
    console.error("Fetch error for stats:", err);
  }
}

// --- Management Tables ---
let allData = [];
let currentPage = 1;
const itemsPerPage = 5;
let totalItems = 0;

async function fetchTableData(pageType, page = 1) {
  currentPage = page;
  let url = "";
  if (pageType === "farmers") url = "../../Backend/Admin/get_all_farmers.php";
  else if (pageType === "consumers")
    url = "../../Backend/Admin/get_all_consumers.php";
  else if (pageType === "products") url = "../../Backend/get_all_products.php";

  try {
    const res = await fetch(`${url}?page=${currentPage}&limit=${itemsPerPage}`);
    const data = await res.json();
    if (data.success) {
      allData = data.farmers || data.consumers || data.products;
      totalItems = data.total || allData.length;
      renderTable(pageType, allData);
      updatePaginationControls(pageType);
    }
  } catch (err) {
    console.error("Error fetching table data:", err);
  }
}

function renderTable(pageType, data) {
  const tbody = document.getElementById(`${pageType}TableBody`);
  if (!tbody) return;
  tbody.innerHTML = "";

  if (data.length === 0) {
    tbody.innerHTML = `<tr><td colspan="7" style="text-align:center">No data found</td></tr>`;
    return;
  }

  data.forEach((item) => {
    let row = "";
    if (pageType === "farmers" || pageType === "consumers") {
      const isBlocked = item.status === "blocked";
      const buttonText = isBlocked ? "Unblock" : "Block";
      const buttonClass = isBlocked ? "unblock" : "block";
      const statusClass = isBlocked ? "status-blocked" : "status-active";

      row = `
        <tr>
          <td>${item.id}</td>
          <td>${item.firstName} ${item.lastName}</td>
          <td>${item.email}</td>
          <td>${item.phone || "N/A"}</td>
          <td>${item.address || "N/A"}</td>
          <td>
            <span class="status-badge ${statusClass}">${(
              item.status || "active"
            ).toUpperCase()}</span>
          </td>
          <td>
            <button class="action-btn ${buttonClass}" onclick="toggleUserStatus('${pageType}', ${
              item.id
            }, '${isBlocked ? "active" : "blocked"}')">
              ${buttonText}
            </button>
          </td>
        </tr>
      `;
    } else if (pageType === "products") {
      row = `
        <tr>
          <td><img src="../../Images/products/${item.image}" alt="${item.name}" style="width:50px; border-radius:4px" onerror="this.src='../../Images/logo.png'"></td>
          <td>${item.name}</td>
          <td>${item.category}</td>
          <td>Rs. ${item.price}</td>
          <td>${item.firstName} ${item.lastName}</td>
          <td>
            <button class="action-btn delete" onclick="deleteProduct(${item.product_id}, this)">Remove</button>
          </td>
        </tr>
      `;
    }
    tbody.innerHTML += row;
  });
}

function updatePaginationControls(pageType) {
  const totalPages = Math.ceil(totalItems / itemsPerPage);
  const prevBtn = document.getElementById("prevPageBtn");
  const nextBtn = document.getElementById("nextPageBtn");
  const pageInfo = document.getElementById("pageInfo");

  if (pageInfo) {
    pageInfo.textContent = `Page ${currentPage} of ${totalPages || 1}`;
  }

  if (prevBtn) prevBtn.disabled = currentPage <= 1;
  if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
}

function setupPagination(pageType) {
  document.getElementById("prevPageBtn")?.addEventListener("click", () => {
    if (currentPage > 1) {
      fetchTableData(pageType, currentPage - 1);
    }
  });

  document.getElementById("nextPageBtn")?.addEventListener("click", () => {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    if (currentPage < totalPages) {
      fetchTableData(pageType, currentPage + 1);
    }
  });
}

// --- Real-time Search Logic ---
function setupSearch(pageType) {
  const searchInput = document.getElementById("adminSearch");
  if (!searchInput) return;

  searchInput.addEventListener("input", (e) => {
    const query = e.target.value.toLowerCase().trim();
    if (query === "") {
      fetchTableData(pageType, 1);
      return;
    }
    // For searches, we still filter locally for now, or we could update backend to handle search with pagination
    // Actually, backend now handles search for products, let's keep it simple for farmers/consumers for now
    const filtered = allData.filter((item) => {
      if (pageType === "products") {
        return (
          item.name.toLowerCase().includes(query) ||
          item.category.toLowerCase().includes(query)
        );
      } else {
        return (
          item.firstName.toLowerCase().includes(query) ||
          item.lastName.toLowerCase().includes(query) ||
          item.email.toLowerCase().includes(query)
        );
      }
    });
    renderTable(pageType, filtered);
  });
}

// --- Actions ---
async function toggleUserStatus(type, id, action) {
  const confirmMsg =
    action === "blocked"
      ? `Are you sure you want to block this ${type.slice(
          0,
          -1,
        )}? They will not be able to log in.`
      : `Are you sure you want to unblock this ${type.slice(0, -1)}?`;

  if (confirm(confirmMsg)) {
    try {
      const res = await fetch("../../Backend/Admin/toggle_user_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, type, action }),
      });
      const data = await res.json();
      if (data.success) {
        // Refresh current page
        fetchTableData(type, currentPage);
      } else {
        alert("Action failed: " + data.message);
      }
    } catch (err) {
      console.error("Status toggle error:", err);
      alert("An error occurred while updating status.");
    }
  }
}

async function deleteProduct(id, btn) {
  if (
    confirm("Are you sure you want to remove this product from the database?")
  ) {
    try {
      const res = await fetch("../../Backend/delete_products.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
      });
      const data = await res.json();
      if (data.success) {
        // Refresh current page
        fetchTableData("products", currentPage);
      } else {
        alert("Failed to delete product: " + data.message);
      }
    } catch (err) {
      console.error("Delete error:", err);
      alert("An error occurred while deleting.");
    }
  }
}

// --- Initialization ---
document.addEventListener("DOMContentLoaded", () => {
  // Stats
  if (document.getElementById("revenue")) fetchDashboardStats();

  // Tables
  if (document.getElementById("farmersTableBody")) {
    fetchTableData("farmers", 1);
    setupSearch("farmers");
    setupPagination("farmers");
  } else if (document.getElementById("consumersTableBody")) {
    fetchTableData("consumers", 1);
    setupSearch("consumers");
    setupPagination("consumers");
  } else if (document.getElementById("productsTableBody")) {
    fetchTableData("products", 1);
    setupSearch("products");
    setupPagination("products");
  }

  // Logout
  const logoutBtns = document.querySelectorAll(".logout-item, #logout-btn");
  logoutBtns.forEach((btn) => {
    btn.addEventListener("click", (e) => {
      e.preventDefault();
      fetch("../../Backend/logout.php")
        .then((res) => res.json())
        .then((data) => {
          if (data.success) window.location.href = "../Login/login.html";
        })
        .catch((err) => console.error("Logout failed:", err));
    });
  });
});
