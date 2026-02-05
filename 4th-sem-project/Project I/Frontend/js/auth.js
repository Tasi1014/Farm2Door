document.addEventListener("DOMContentLoaded", () => {
  console.log("Auth script loaded");
  checkAuth();

  // Handle sidebar and hamburger clicks
  document.addEventListener("click", (e) => {
    const hamburger =
      e.target.id === "hamburger" || e.target.closest("#hamburger");
    const overlay = e.target.classList.contains("sidebar-overlay");

    if (hamburger || overlay) {
      toggleSidebar();
    }

    if (e.target.id === "logout-btn" || e.target.closest("#logout-btn")) {
      e.preventDefault();
      logout();
    }
  });
});

// Prepare page revalidation for back button
window.addEventListener("pageshow", function (event) {
  // If page is loaded from back/forward cache, force reload to re-run auth check
  if (event.persisted) {
    window.location.reload();
  }
});

function checkAuth() {
  // Path relative to the HTML file executing this script
  // All main pages (Home, Product, About Us, Contact Us) are 2 levels deep from Project Root
  // e.g. Frontend/Home/index.html -> ../../Backend/get_user_info.php
  const apiUrl = "../../Backend/get_user_info.php";

  console.log("Checking auth at:", apiUrl);

  fetch(apiUrl)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      console.log("Auth data:", data);
      const loginLink = document.getElementById("login-link");
      const signupLink = document.getElementById("signup-link");
      const profileLink = document.getElementById("profile-link");
      const userNameSpan = document.getElementById("user-name");
      const sidebarLinks = document.querySelector(".sidebar-links");

      if (data.loggedIn) {
        // Desktop Navbar Updates
        if (loginLink) loginLink.style.display = "none";
        if (signupLink) signupLink.style.display = "none";
        if (profileLink) {
          profileLink.style.display = "block";
          const profileBtn = profileLink.querySelector("#profile-btn");
          const profileDropdown =
            profileLink.querySelector("#profile-dropdown");

          if (profileBtn) {
            profileBtn.innerHTML = `<i class="fa-solid fa-user-circle"></i> <span>${data.name}</span>`;
          }
          if (profileDropdown) {
            profileDropdown.innerHTML = `
                <a href="../Orders/orders.html"><i class="fa-solid fa-box"></i> My Orders</a>
                <a href="#" id="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            `;
          }
        }

        // Mobile Sidebar Updates
        if (sidebarLinks) {
          // Keep existing main links (Home, Products, etc.)
          const existingLinks =
            sidebarLinks.querySelectorAll("li:not(.auth-link)");
          let html = "";
          existingLinks.forEach((li) => (html += li.outerHTML));

          // Prepend User Profile Info
          const profileHeader = `
            <li class="auth-link user-info-item" style="border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; margin-bottom: 15px;">
              <span style="display: flex; align-items: center; gap: 15px; padding: 12px 20px; font-size: 1.2rem; font-weight: 600;">
                <i class="fa-solid fa-user-circle"></i> ${data.name}
              </span>
            </li>
          `;

          html =
            profileHeader +
            html +
            `
            <li class="auth-link"><a href="../Orders/orders.html"><i class="fa-solid fa-box"></i> My Orders</a></li>
            <li class="auth-link"><a href="#" id="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
          `;
          sidebarLinks.innerHTML = html;
        }
      } else {
        // Desktop Navbar Updates
        if (loginLink) loginLink.style.display = "block";
        if (signupLink) signupLink.style.display = "block";
        if (profileLink) profileLink.style.display = "none";

        // Mobile Sidebar Updates
        if (sidebarLinks) {
          const existingLinks =
            sidebarLinks.querySelectorAll("li:not(.auth-link)");
          let html = "";
          existingLinks.forEach((li) => (html += li.outerHTML));
          html += `
            <li class="auth-link"><a href="../Login/login.html"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
            <li class="auth-link"><a href="../Registration/index.html"><i class="fa-solid fa-user-plus"></i> Sign Up</a></li>
          `;
          sidebarLinks.innerHTML = html;
        }
      }
    })
    .catch((error) => console.error("Error checking auth:", error));
}

// Global Toggle Sidebar
function toggleSidebar() {
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");
  if (sidebar && overlay) {
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
  }
}

function logout() {
  fetch("../../Backend/logout.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Reload the page to reset state
        window.location.reload();
      }
    })
    .catch((error) => console.error("Error logging out:", error));
}
