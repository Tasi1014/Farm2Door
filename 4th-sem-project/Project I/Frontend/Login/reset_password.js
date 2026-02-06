document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get("token");

  if (!token) {
    showStatus("Invalid access. Token is missing.", "error");
    document.getElementById("reset-password-form").style.display = "none";
    return;
  }

  document.getElementById("token").value = token;
});

function showStatus(message, type) {
  const statusMsg = document.getElementById("status-message");
  statusMsg.innerText = message;
  statusMsg.className = "message " + type;
  statusMsg.style.display = "block";
}

document
  .getElementById("reset-password-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const token = document.getElementById("token").value;
    const submitBtn = this.querySelector("button");

    // Client-side quick check
    if (password !== confirmPassword) {
      showStatus("Passwords do not match!", "error");
      return;
    }

    if (password.length < 8) {
      showStatus("Password must be at least 8 characters long.", "error");
      return;
    }

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerText = "Updating...";

    fetch("../../Backend/reset_password_process.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `token=${encodeURIComponent(token)}&password=${encodeURIComponent(password)}&confirm_password=${encodeURIComponent(confirmPassword)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          showStatus(data.message, "success");
          document.getElementById("reset-password-form").style.display = "none";
          document.getElementById("success-actions").style.display = "block";
        } else {
          showStatus(data.message, "error");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        showStatus("Something went wrong. Please try again.", "error");
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = "Update Password";
      });
  });
