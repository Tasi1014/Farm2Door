document
  .getElementById("forgot-password-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value;
    const submitBtn = this.querySelector("button");
    const statusMsg = document.getElementById("status-message");

    // Reset status
    statusMsg.className = "message";
    statusMsg.style.display = "none";

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerText = "Sending...";

    fetch("../../Backend/forgot_password.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `email=${encodeURIComponent(email)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        statusMsg.innerText = data.message;
        if (data.status === "success") {
          statusMsg.classList.add("success");
          // document.getElementById('forgot-password-form').reset();
        } else {
          statusMsg.classList.add("error");
        }
        statusMsg.style.display = "block";
      })
      .catch((error) => {
        console.error("Error:", error);
        statusMsg.innerText = "Something went wrong. Please try again.";
        statusMsg.classList.add("error");
        statusMsg.style.display = "block";
      })
      .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = "Send Reset Link";
      });
  });
