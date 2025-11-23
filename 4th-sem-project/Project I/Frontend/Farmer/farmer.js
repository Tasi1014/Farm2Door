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
      if(name === lowStock){
        name.style.color = "red";
      }

      name.style.color = "green"
    }
  }, 100);
}

updateAnimation(product, 30);
updateAnimation(earnings, 5345, 5300);
updateAnimation(orders, 33);
updateAnimation(lowStock, 10);
