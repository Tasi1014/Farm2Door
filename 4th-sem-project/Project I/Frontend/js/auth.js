document.addEventListener("DOMContentLoaded", () => {
  console.log("Auth script loaded");
  checkAuth();

  // Use event delegation for logout button as it might be replaced dynamically
  document.addEventListener("click", (e) => {
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

      if (data.loggedIn) {
        // User is logged in
        if (loginLink) loginLink.style.display = "none";
        if (signupLink) signupLink.style.display = "none";

        if (profileLink) {
          profileLink.style.display = "block";
          const profileBtn = profileLink.querySelector("#profile-btn");
          const profileDropdown =
            profileLink.querySelector("#profile-dropdown");

          if (profileBtn) {
            profileBtn.innerHTML = `<i class="fa-solid fa-user"></i> <span>${data.name}</span>`;
          }
          if (profileDropdown) {
            profileDropdown.innerHTML = `
                    <a href="../Orders/orders.html">My Orders</a>
                    <a href="#" id="logout-btn">Logout</a>
                `;
          } else {
            // Fallback if the dropdown structure isn't present, use original userNameSpan
            if (userNameSpan) {
              userNameSpan.textContent = data.name;
            }
          }
        }
      } else {
        // User is not logged in
        if (loginLink) loginLink.style.display = "block";
        if (signupLink) signupLink.style.display = "block";
        if (profileLink) profileLink.style.display = "none";
      }
    })
    .catch((error) => console.error("Error checking auth:", error));
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
