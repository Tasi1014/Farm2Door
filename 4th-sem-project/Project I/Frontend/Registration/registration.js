// Mobile menu toggle
const hamburger = document.querySelector(".hamburger");
const navLinks = document.querySelector(".nav-links");

hamburger.addEventListener("click", () => {
  navLinks.classList.toggle("mobile-active");
});

// Form validation functions
function validateName(name, errorElement) {
  const nameRegex = /^[a-zA-Z\s]{3,15}$/;
  if (name.trim() === "") {
    errorElement.textContent = "This field is required";
    return false;
  } else if (!nameRegex.test(name)) {
    errorElement.textContent =
      "Name should only contain letters and spaces (3-15 characters)";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateEmail(email, errorElement) {
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  if (email.trim() === "") {
    errorElement.textContent = "Email is required";
    return false;
  } else if (!emailRegex.test(email)) {
    errorElement.textContent = "Please enter a valid email address";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validatePhone(phone, errorElement) {
  const phoneRegex = /^\d{10}$/;
  if (phone.trim() === "") {
    errorElement.textContent = "Phone number is required";
    return false;
  } else if (!phoneRegex.test(phone.replace(/\s/g, ""))) {
    errorElement.textContent = "Please enter a valid phone number";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateAddress(address, errorElement) {
  if (address.trim() === "") {
    errorElement.textContent = "Address is required";
    return false;
  } else if (address.trim().length < 10) {
    errorElement.textContent =
      "Please enter a complete address (at least 10 characters)";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateCity(city, errorElement) {
  const cityRegex = /^[a-zA-Z\s]{2,30}$/;
  if (city.trim() === "") {
    errorElement.textContent = "City is required";
    return false;
  } else if (!cityRegex.test(city)) {
    errorElement.textContent = "Please enter a valid city name";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateProvince(province, errorElement) {
  const provinceRegex = /^[a-zA-Z\s]{2,30}$/;
  if (province.trim() === "") {
    errorElement.textContent = "Province is required";
    return false;
  } else if (!provinceRegex.test(province)) {
    errorElement.textContent = "Please enter a valid Province name";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validatePassword(password, errorElement) {
  const passwordRegex =
    /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,20}$/;
  if (password.trim() === "") {
    errorElement.textContent = "Password is required";
    return false;
  } else if (!passwordRegex.test(password)) {
    errorElement.textContent =
      "Password must contain at least one uppercase letter, one special character, and be 8-20 characters long";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateConfirmPassword(password, confirmPassword, errorElement) {
  if (confirmPassword.trim() === "") {
    errorElement.textContent = "Please confirm your password";
    return false;
  } else if (password !== confirmPassword) {
    errorElement.textContent = "Passwords do not match";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateCrops(crops, errorElement) {
  if (crops.trim() === "") {
    errorElement.textContent = "Please specify the types of crops you grow";
    return false;
  } else if (crops.trim().length < 3) {
    errorElement.textContent = "Please provide more details about your crops";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateExperience(experience, errorElement) {
  if (experience.trim() === "") {
    errorElement.textContent = "Please select your farming experience";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

function validateTerms(terms, errorElement) {
  if (!terms) {
    errorElement.textContent = "You must agree to the terms and conditions";
    return false;
  } else {
    errorElement.textContent = "";
    return true;
  }
}

// Consumer registration form validation
document.addEventListener("DOMContentLoaded", function () {
  const consumerForm = document.getElementById("consumer-registration-form");
  if (consumerForm) {
    consumerForm.addEventListener("submit", function (e) {
      e.preventDefault(); // Prevent default form submission

      // Get form elements
      const firstName = document.getElementById("consumer-first-name").value;
      const lastName = document.getElementById("consumer-last-name").value;
      const email = document.getElementById("consumer-email").value;
      const phone = document.getElementById("consumer-phone").value;
      const address = document.getElementById("consumer-address").value;
      const password = document.getElementById("consumer-password").value;
      const confirmPassword = document.getElementById(
        "consumer-confirm-password"
      ).value;
      const terms = document.getElementById("consumer-terms").checked;

      // Get error elements
      const firstNameError = document.getElementById("first-name-error");
      const lastNameError = document.getElementById("last-name-error");
      const emailError = document.getElementById("email-error");
      const phoneError = document.getElementById("phone-error");
      const addressError = document.getElementById("address-error");
      const passwordError = document.getElementById("password-error");
      const confirmPasswordError = document.getElementById(
        "confirm-password-error"
      );
      const termsError = document.getElementById("terms-error");
      const successElement = document.getElementById("registration-success");

      // Clear previous errors
      const errorElements = document.querySelectorAll(".error");
      errorElements.forEach((el) => (el.textContent = ""));
      successElement.textContent = "";

      let isFormValid = true;

      isFormValid = validateName(firstName, firstNameError) && isFormValid;
      isFormValid = validateName(lastName, lastNameError) && isFormValid;
      isFormValid = validateEmail(email, emailError) && isFormValid;
      isFormValid = validatePhone(phone, phoneError) && isFormValid;
      isFormValid = validateAddress(address, addressError) && isFormValid;
      isFormValid = validatePassword(password, passwordError) && isFormValid;
      isFormValid =
        validateConfirmPassword(
          password,
          confirmPassword,
          confirmPasswordError
        ) && isFormValid;
      isFormValid = validateTerms(terms, termsError) && isFormValid;

      if (isFormValid) {
        // Create FormData object
        const formData = new FormData(consumerForm);

        // Send data to backend using Fetch API
        fetch("../../Backend/consumer-registration.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              successElement.textContent = data.message;
              successElement.style.color = "green";
              consumerForm.reset();

              // Redirect after 2 seconds
              setTimeout(() => {
                window.location.href = "../Login/login.html";
              }, 2000);
            } else {
              // Display backend errors
              if (data.errors) {
                if (data.errors.firstName)
                  firstNameError.textContent = data.errors.firstName;
                if (data.errors.lastName)
                  lastNameError.textContent = data.errors.lastName;
                if (data.errors.email)
                  emailError.textContent = data.errors.email;
                if (data.errors.phone)
                  phoneError.textContent = data.errors.phone;
                if (data.errors.address)
                  addressError.textContent = data.errors.address;
                if (data.errors.password)
                  passwordError.textContent = data.errors.password;
                if (data.errors.confirmPassword)
                  confirmPasswordError.textContent =
                    data.errors.confirmPassword;
                if (data.errors.terms)
                  termsError.textContent = data.errors.terms;
                if (data.errors.database) {
                  successElement.textContent = data.errors.database;
                  successElement.style.color = "red";
                }

                // Scroll to the first error after displaying backend errors
                const allErrors = document.querySelectorAll(".error");
                for (let i = 0; i < allErrors.length; i++) {
                  if (allErrors[i].textContent !== "") {
                    allErrors[i].scrollIntoView({
                      behavior: "smooth",
                      block: "center",
                    });
                    break;
                  }
                }
              }
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            successElement.textContent = "An error occurred. Please try again.";
            successElement.style.color = "red";
          });
      } else {
        // Get all error elements
        const allErrors = document.querySelectorAll(".error");

        // Loop through to find the first one with text
        for (let i = 0; i < allErrors.length; i++) {
          if (allErrors[i].textContent !== "") {
            allErrors[i].scrollIntoView({
              behavior: "smooth",
              block: "center",
            });
            break; // Stop after finding the first error
          }
        }
      }
    });
  }

  // Farmer registration form validation
  const farmerForm = document.getElementById("farmer-registration-form");
  if (farmerForm) {
    farmerForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Get form elements
      const firstName = document.getElementById("farmer-first-name").value;
      const lastName = document.getElementById("farmer-last-name").value;
      const email = document.getElementById("farmer-email").value;
      const phone = document.getElementById("farmer-phone").value;
      const password = document.getElementById("farmer-password").value;
      const confirmPassword = document.getElementById(
        "farmer-confirm-password"
      ).value;
      const terms = document.getElementById("farmer-terms").checked;

      // Get error elements
      const firstNameError = document.getElementById("first-name-error");
      const lastNameError = document.getElementById("last-name-error");
      const emailError = document.getElementById("email-error");
      const phoneError = document.getElementById("phone-error");
      const passwordError = document.getElementById("password-error");
      const confirmPasswordError = document.getElementById(
        "confirm-password-error"
      );
      const termsError = document.getElementById("terms-error");
      const successElement = document.getElementById("registration-success");

      let isFormValid = true;

      isFormValid = validateName(firstName, firstNameError) && isFormValid;
      isFormValid = validateName(lastName, lastNameError) && isFormValid;
      isFormValid = validateEmail(email, emailError) && isFormValid;
      isFormValid = validatePhone(phone, phoneError) && isFormValid;
      isFormValid = validatePassword(password, passwordError) && isFormValid;
      isFormValid =
        validateConfirmPassword(
          password,
          confirmPassword,
          confirmPasswordError
        ) && isFormValid;
      isFormValid = validateTerms(terms, termsError) && isFormValid;

      if (isFormValid) {
        // Create FormData object
        const formData = new FormData(farmerForm);

        // Send data to backend using Fetch API
        fetch("../../Backend/farmer-registration.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              const alertBox = document.getElementById("alertBox");
              alertBox.textContent = data.message;
              alertBox.className = "success";
              alertBox.style.display = "block";

              farmerForm.reset();

              // Redirect after 2 seconds
              setTimeout(() => {
                window.location.href = "../Login/login.html";
              }, 2000);
            } else {
              // Display backend errors
              if (data.errors) {
                if (data.errors.firstName)
                  firstNameError.textContent = data.errors.firstName;
                if (data.errors.lastName)
                  lastNameError.textContent = data.errors.lastName;
                if (data.errors.email)
                  emailError.textContent = data.errors.email;
                if (data.errors.phone)
                  phoneError.textContent = data.errors.phone;
                if (data.errors.password)
                  passwordError.textContent = data.errors.password;
                if (data.errors.confirmPassword)
                  confirmPasswordError.textContent =
                    data.errors.confirmPassword;
                if (data.errors.terms)
                  termsError.textContent = data.errors.terms;
                if (data.errors.database) {
                  successElement.textContent = data.errors.database;
                  successElement.style.color = "red";
                }

                // Scroll to the first error after displaying backend errors
                const allErrors = document.querySelectorAll(".error");
                for (let i = 0; i < allErrors.length; i++) {
                  if (allErrors[i].textContent !== "") {
                    allErrors[i].scrollIntoView({
                      behavior: "smooth",
                      block: "center",
                    });
                    break;
                  }
                }
              }
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            successElement.textContent = "An error occurred. Please try again.";
            successElement.style.color = "red";
          });
      } else {
        // Find the first error and scroll to it
        const error = document.querySelector(".error:not(:empty)");
        if (error) {
          error.scrollIntoView();
        }
      }
    });
  }
});

// Addeddd smooth scrolling for better UX
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});
