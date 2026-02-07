document
  .getElementById("forgot-password-form")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const e1 = document.getElementById("e1");
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const submitBtn = this.querySelector("button");
    const statusMsg = document.getElementById("status-message");
    let isFormValid = true;

    // Reset field error
    e1.innerText = "";

    if (email === "") {
      e1.innerText = "Email is required";
      isFormValid = false;
    } else if (!emailRegex.test(email)) {
      e1.innerText = "Invalid Email Format";
      isFormValid = false;
    }

    // Reset status
    statusMsg.className = "message";
    statusMsg.style.display = "none";

    // Disable button
    submitBtn.disabled = true;
    submitBtn.innerText = "Sending...";

    if (isFormValid) {
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
          statusMsg.classList.add(data.status === "success" ? "success" : "error");
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
    } else {
      submitBtn.disabled = false;
      submitBtn.innerText = "Send Reset Link";
    }
  });
