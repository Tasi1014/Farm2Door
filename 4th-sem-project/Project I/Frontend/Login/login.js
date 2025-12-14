// Mobile menu toggle
const hamburger = document.querySelector(".hamburger");

// Check if user is already logged in
fetch("../../Backend/get_user_info.php")
  .then((response) => response.json())
  .then((data) => {
    if (data.loggedIn) {
      if (data.userType === "consumer") {
        window.location.href = "../Home/index.html";
      } else if (data.userType === "farmer") {
        window.location.href = "../Farmer/dashboard.html";
      }
    }
  })
  .catch((error) => console.error("Error checking auth:", error));

const navLinks = document.querySelector(".nav-links");

const consumer_success = document.getElementById("consumer-success");
const farmer_success = document.getElementById("farmer-success");
const admin_success = document.getElementById("admin-success");

hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("mobile-active");
});

// Helper to get cookie
function getCookie(name) {
  const value = `; ${document.cookie}`;
  const parts = value.split(`; ${name}=`);
  if (parts.length === 2) return parts.pop().split(";").shift();
  return "";
}

// Pre-fill forms from cookies
window.addEventListener("DOMContentLoaded", () => {
  // Farmer
  const fEmail = getCookie("f_email");
  const fPass = getCookie("f_pass");
  if (fEmail) {
    document.getElementById("farmer-email").value = decodeURIComponent(fEmail);
    document.getElementById("f-chk").checked = true;
  }
  if (fPass) {
    document.getElementById("farmer-password").value =
      decodeURIComponent(fPass);
  }

  // Consumer
  const cEmail = getCookie("c_email");
  const cPass = getCookie("c_pass");
  if (cEmail) {
    document.getElementById("consumer-email").value =
      decodeURIComponent(cEmail);
    document.getElementById("c-chk").checked = true;
  }
  if (cPass) {
    document.getElementById("consumer-password").value =
      decodeURIComponent(cPass);
  }
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

// Consumer Login Logic
const consumerForm = document
  .getElementById("consumer-form")
  .querySelector("form");
if (consumerForm) {
  consumerForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const formData = new FormData(consumerForm);
    const emailError = document.getElementById("e3");
    const passwordError = document.getElementById("e4");
    const generalError = document.getElementById("consumer-general-error");
    const successElement = document.getElementById("consumer-success");

    // Clear previous errors
    emailError.textContent = "";
    passwordError.textContent = "";
    generalError.textContent = "";
    successElement.textContent = "";

    fetch("../../Backend/consumer_login.php", {
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
            window.location.href = "../Home/index.html";
          }, 1000);
        } else {
          if (data.errors) {
            if (data.errors.email) emailError.textContent = data.errors.email;
            if (data.errors.password)
              passwordError.textContent = data.errors.password;
            if (data.errors.result) {
              generalError.textContent = data.errors.result;
            }
          }
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        generalError.textContent = "An error occurred. Please try again.";
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
