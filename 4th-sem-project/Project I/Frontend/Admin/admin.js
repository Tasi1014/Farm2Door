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
function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  if (dropdown) dropdown.classList.toggle("show");
}

window.onclick = function (event) {
  const dropdown = document.getElementById("userDropdown");
  if (
    !event.target.closest(".profile-icon") &&
    !event.target.closest(".dropdown-menu")
  ) {
    if (dropdown && dropdown.classList.contains("show")) {
      dropdown.classList.remove("show");
    }
  }
};

// --- Dashboard Stats ---
function animateValue(id, start, end, duration) {
  const obj = document.getElementById(id);
  if (!obj) return;

  let startTimestamp = null;
  const step = (timestamp) => {
    if (!startTimestamp) startTimestamp = timestamp;
    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
    let value = Math.floor(progress * (end - start) + start);

    if (id === "revenue") {
      obj.innerHTML = "Rs. " + value.toLocaleString();
    } else {
      obj.innerHTML = value.toLocaleString();
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
let allData = []; // Local cache for filtering

async function fetchTableData(pageType) {
  let url = "";
  if (pageType === "farmers") url = "../../Backend/Admin/get_all_farmers.php";
  else if (pageType === "consumers")
    url = "../../Backend/Admin/get_all_consumers.php";
  else if (pageType === "products") url = "../../Backend/get_all_products.php";

  try {
    const res = await fetch(url);
    const data = await res.json();
    if (data.success) {
      allData = data.farmers || data.consumers || data.products;
      renderTable(pageType, allData);
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
      row = `
        <tr>
          <td>${item.id}</td>
          <td>${item.firstName} ${item.lastName}</td>
          <td>${item.email}</td>
          <td>${item.phone || "N/A"}</td>
          <td>${item.address || "N/A"}</td>
          <td>
            <button class="action-btn delete" onclick="deleteUser('${pageType}', ${
        item.id
      }, this)">Delete</button>
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

// --- Real-time Search Logic ---
function setupSearch(pageType) {
  const searchInput = document.getElementById("adminSearch");
  if (!searchInput) return;

  searchInput.addEventListener("input", (e) => {
    const query = e.target.value.toLowerCase().trim();
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
async function deleteUser(type, id, btn) {
  if (
    confirm(
      `Are you sure you want to delete this ${type.slice(
        0,
        -1
      )}? This will remove them from the system.`
    )
  ) {
    try {
      const res = await fetch("../../Backend/Admin/delete_user.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, type }),
      });
      const data = await res.json();
      if (data.success) {
        btn.closest("tr").remove();
        // Update local cache
        allData = allData.filter((item) => item.id !== id);
      } else {
        alert("Failed to delete: " + data.message);
      }
    } catch (err) {
      console.error("Delete error:", err);
      alert("An error occurred while deleting.");
    }
  }
}

async function deleteProduct(id, btn) {
  if (
    confirm("Are you sure you want to remove this product from the database?")
  ) {
    try {
      const res = await fetch("../../Backend/Admin/delete_product.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id }),
      });
      const data = await res.json();
      if (data.success) {
        btn.closest("tr").remove();
        // Update local cache
        allData = allData.filter((item) => item.product_id !== id);
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
    fetchTableData("farmers");
    setupSearch("farmers");
  } else if (document.getElementById("consumersTableBody")) {
    fetchTableData("consumers");
    setupSearch("consumers");
  } else if (document.getElementById("productsTableBody")) {
    fetchTableData("products");
    setupSearch("products");
  }

  // Logout
  const logoutBtn = document.getElementById("logout-btn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      fetch("../../Backend/logout.php")
        .then((res) => res.json())
        .then((data) => {
          if (data.success) window.location.href = "../Login/login.html";
        });
    });
  }
});
