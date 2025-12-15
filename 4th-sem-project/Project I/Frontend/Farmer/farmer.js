// Check authentication and protect page
function checkAuth() {
  fetch("../../Backend/get_user_info.php")
    .then((response) => response.json())
    .then((data) => {
      // If not logged in, OR if logged in but NOT a farmer, redirect to login
      if (!data.loggedIn || data.userType !== "farmer") {
        window.location.href = "../Login/login.html";
      } else {
        // Update Profile Info
        const nameEl = document.querySelector(".user-info .name");
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

function updateAnimation(name, maxCount, startCount = 0) {
  let counterNumber = startCount;
  const counter = setInterval(() => {
    counterNumber++;
    name.textContent = counterNumber;
    if (counterNumber == maxCount) {
      clearInterval(counter);
      if (name === earnings) {
        name.textContent = "Rs. " + counterNumber;
      }
      if (name === lowStock) {
        name.style.color = "red";
      }

      name.style.color = "green";
    }
  }, 100);
}

if (product) updateAnimation(product, 30);
if (earnings) updateAnimation(earnings, 5345, 5300);
if (orders) updateAnimation(orders, 33);
if (lowStock) updateAnimation(lowStock, 10);

// Logout Logic
document.addEventListener("DOMContentLoaded", () => {
  const logoutBtn = document.getElementById("logout-btn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
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
  }
});
