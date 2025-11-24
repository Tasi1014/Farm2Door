// Mobile menu toggle
const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".nav-links");

const consumer_success = document.getElementById("consumer-success");
const farmer_success = document.getElementById("farmer-success");
const admin_success = document.getElementById("admin-success");

hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("mobile-active");
});

// Login form functionality
function showLoginForm(type) {
  // Get all forms
  const forms = {
    farmer: document.getElementById("farmer-form"),
    consumer: document.getElementById("consumer-form"),
    admin: document.getElementById("admin-form"),
  };

  // Get all cards
  const cards = {
    farmer: document.querySelector(".farmer-login"),
    consumer: document.querySelector(".consumer-login"),
    admin: document.querySelector(".admin-login"),
  };

  // Check if the clicked form is already visible
  const isAlreadyVisible = forms[type].style.display === "block";

  // Hide all forms
  Object.values(forms).forEach((form) => (form.style.display = "none"));

  // Remove active class from all cards
  Object.values(cards).forEach((card) => card.classList.remove("active"));

  // If the clicked form was already visible, just hide all (toggle off)
  if (isAlreadyVisible) return;

  // Otherwise, show the selected form and activate its card
  forms[type].style.display = "block";
  cards[type].classList.add("active");

  // Smooth scroll to the form
  setTimeout(() => {
    const formElement = forms[type];
    const offset = 98; // small offset from top
    const topPos =
      formElement.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top: topPos, behavior: "smooth" });
  }, 50);
}

// Farmer Login Logic
const farmerForm = document.getElementById("farmer-form").querySelector("form");
if (farmerForm) {
  farmerForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(farmerForm);
    const emailError = document.getElementById("e1");
    const passwordError = document.getElementById("e2");
    const successElement = document.getElementById("farmer-success");

    // Clear previous errors
    emailError.textContent = "";
    passwordError.textContent = "";
    successElement.textContent = "";

    fetch("../../Backend/farmer_login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          successElement.textContent = data.message;
          successElement.style.color = "green";

          // Redirect after short delay
          setTimeout(() => {
            window.location.href = "../Farmer/dashboard.html";
          }, 1000);
        } else {
          if (data.errors) {
            if (data.errors.email) emailError.textContent = data.errors.email;
            if (data.errors.password)
              passwordError.textContent = data.errors.password;
            if (data.errors.result) {
              successElement.textContent = data.errors.result;
              successElement.style.color = "red";
            }
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        successElement.textContent = "An error occurred. Please try again.";
        successElement.style.color = "red";
      });
  });
}

// Admin Login Logic
const adminForm = document.getElementById("admin-form").querySelector("form");
if (adminForm) {
  adminForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(adminForm);
    const emailError = document.getElementById("e5");
    const passwordError = document.getElementById("e6");
    const successElement = document.getElementById("admin-success");

    // Clear previous errors
    emailError.textContent = "";
    passwordError.textContent = "";
    successElement.textContent = "";

    fetch("../../Backend/admin_login.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          successElement.textContent = data.message;
          successElement.style.color = "green";

          // Redirect after short delay
          setTimeout(() => {
            window.location.href = "../Admin/dashboard.html";
          }, 1000);
        } else {
          if (data.errors) {
            if (data.errors.email) emailError.textContent = data.errors.email;
            if (data.errors.password)
              passwordError.textContent = data.errors.password;
            if (data.errors.result) {
              successElement.textContent = data.errors.result;
              successElement.style.color = "red";
            }
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        successElement.textContent = "An error occurred. Please try again.";
        successElement.style.color = "red";
      });
  });
}
