document.addEventListener("DOMContentLoaded", () => {
  console.log("Auth script loaded");
  checkAuth();

  const logoutBtn = document.getElementById("logout-btn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", (e) => {
      e.preventDefault();
      logout();
    });
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
          userNameSpan.textContent = data.name;
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
