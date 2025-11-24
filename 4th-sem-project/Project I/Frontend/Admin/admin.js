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
    if (dropdown && dropdown.classList.contains("show")) {
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

// Mock Data for Dashboard Stats
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

    if (progress < 1) {
      window.requestAnimationFrame(step);
    }
  };
  window.requestAnimationFrame(step);
}

// Initialize Dashboard Stats if on dashboard page
if (document.getElementById("revenue")) {
  animateValue("revenue", 0, 150000, 2000);
  animateValue("orders", 0, 120, 2000);
  animateValue("products", 0, 45, 2000);
  animateValue("farmers", 0, 12, 2000);
  animateValue("consumers", 0, 85, 2000);
}

// Delete Item Function (Mock)
function deleteItem(btn) {
  if (confirm("Are you sure you want to delete this item?")) {
    const row = btn.closest("tr");
    row.style.opacity = "0";
    setTimeout(() => {
      row.remove();
    }, 300);
  }
}

// Generate Report Function
function generateReport() {
  const reportResult = document.getElementById("reportResult");
  const tableBody = document.getElementById("reportTableBody");
  const reportType = document.getElementById("reportType").value;

  if (!reportResult || !tableBody) return;

  // Clear existing data
  tableBody.innerHTML = "";

  // Show the table
  reportResult.style.display = "block";

  // Mock Data Generation based on type
  let data = [];
  if (reportType === "monthly") {
    document.getElementById("reportTitle").innerText = "Monthly Revenue Report";
    data = [
      { date: "2023-10-01", orders: 15, revenue: 5000 },
      { date: "2023-10-02", orders: 12, revenue: 4200 },
      { date: "2023-10-03", orders: 18, revenue: 6100 },
      { date: "2023-10-04", orders: 20, revenue: 7500 },
      { date: "2023-10-05", orders: 14, revenue: 4800 },
    ];
  } else if (reportType === "daily") {
    document.getElementById("reportTitle").innerText = "Daily Sales Report";
    data = [
      { date: "10:00 AM", orders: 2, revenue: 500 },
      { date: "11:00 AM", orders: 4, revenue: 1200 },
      { date: "12:00 PM", orders: 5, revenue: 1500 },
      { date: "01:00 PM", orders: 3, revenue: 800 },
    ];
  } else {
    document.getElementById("reportTitle").innerText = "Top Farmers Report";
    // Different structure for farmers
    tableBody.innerHTML = `
            <tr>
                <td>Ram Bahadur</td>
                <td>50 Orders</td>
                <td>Rs. 25,000</td>
                <td>Rs. 1,250</td>
            </tr>
            <tr>
                <td>Sita Devi</td>
                <td>45 Orders</td>
                <td>Rs. 22,000</td>
                <td>Rs. 1,100</td>
            </tr>
        `;
    return;
  }

  // Populate Table
  data.forEach((item) => {
    const commission = item.revenue * 0.05;
    const row = `
            <tr>
                <td>${item.date}</td>
                <td>${item.orders}</td>
                <td>Rs. ${item.revenue.toLocaleString()}</td>
                <td>Rs. ${commission.toLocaleString()}</td>
            </tr>
        `;
    tableBody.innerHTML += row;
  });
}
