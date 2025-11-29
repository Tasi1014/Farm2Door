document.addEventListener("DOMContentLoaded", () => {
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
  fetch("../../Backend/get_user_info.php")
    .then((response) => response.json())
    .then((data) => {
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
