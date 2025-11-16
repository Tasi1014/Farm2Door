// Mobile menu toggle
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('mobile-active');
});

// Form validation functions
function validateName(name, errorElement) {
    const nameRegex = /^[a-zA-Z\s]{3,15}$/;
    if (name.trim() === "") {
        errorElement.textContent = "This field is required";
        return false;
    } else if (!nameRegex.test(name)) {
        errorElement.textContent = "Name should only contain letters and spaces (3-15 characters)";
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
    } else if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
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
        errorElement.textContent = "Please enter a complete address (at least 10 characters)";
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
    const passwordRegex = /^(?=.*[A-Z])(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,20}$/;
    if (password.trim() === "") {
        errorElement.textContent = "Password is required";
        return false;
    } else if (!passwordRegex.test(password)) {
        errorElement.textContent = "Password must contain at least one uppercase letter, one special character, and be 8-20 characters long";
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
document.addEventListener('DOMContentLoaded', function() {
    const consumerForm = document.getElementById('consumer-registration-form');
    if (consumerForm) {
        consumerForm.addEventListener('submit', function(e) {
            // Allow natural form submission to PHP handler
            
            // Get form elements
            const firstName = document.getElementById('consumer-first-name').value;
            const lastName = document.getElementById('consumer-last-name').value;
            const email = document.getElementById('consumer-email').value;
            const phone = document.getElementById('consumer-phone').value;
            const address = document.getElementById('consumer-address').value;
            const city = document.getElementById('consumer-city').value;
            const province = document.getElementById('consumer-province').value;
            const password = document.getElementById('consumer-password').value;
            const confirmPassword = document.getElementById('consumer-confirm-password').value;
            const terms = document.getElementById('consumer-terms').checked;

            // Get error elements
            const firstNameError = document.getElementById('first-name-error');
            const lastNameError = document.getElementById('last-name-error');
            const emailError = document.getElementById('email-error');
            const phoneError = document.getElementById('phone-error');
            const addressError = document.getElementById('address-error');
            const cityError = document.getElementById('city-error');
            const provinceError = document.getElementById('province-error');
            const passwordError = document.getElementById('password-error');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            const termsError = document.getElementById('terms-error');
            const successElement = document.getElementById('registration-success');

            let isFormValid = true;
            
            isFormValid = validateName(firstName, firstNameError) && isFormValid;
            isFormValid = validateName(lastName, lastNameError) && isFormValid;
            isFormValid = validateEmail(email, emailError) && isFormValid;
            isFormValid = validatePhone(phone, phoneError) && isFormValid;
            isFormValid = validateAddress(address, addressError) && isFormValid;
            isFormValid = validateCity(city, cityError) && isFormValid;
            isFormValid = validateProvince(province, provinceError) && isFormValid;
            isFormValid = validatePassword(password, passwordError) && isFormValid;
            isFormValid = validateConfirmPassword(password, confirmPassword, confirmPasswordError) && isFormValid;
            isFormValid = validateTerms(terms, termsError) && isFormValid;

            if (isFormValid) {
                successElement.textContent = "Registration successful! Welcome to Farm2Door!";
                successElement.style.color = "green";
                console.log("Consumer registration data:", {
                    firstName, lastName, email, phone, address, city, province, password
                });
                
            } else {
                // Scroll to first error
                const firstError = document.querySelector('.error:not(:empty)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // Farmer registration form validation
    const farmerForm = document.getElementById('farmer-registration-form');
    if (farmerForm) {
        farmerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form elements
            const firstName = document.getElementById('farmer-first-name').value;
            const lastName = document.getElementById('farmer-last-name').value;
            const email = document.getElementById('farmer-email').value;
            const phone = document.getElementById('farmer-phone').value;
            const city = document.getElementById('farmer-city').value;
            const province = document.getElementById('farmer-province').value;
            const experience = document.getElementById('farmer-experience').value;
            const crops = document.getElementById('farmer-crops').value;
            const password = document.getElementById('farmer-password').value;
            const confirmPassword = document.getElementById('farmer-confirm-password').value;
            const terms = document.getElementById('farmer-terms').checked;

            // Get error elements
            const firstNameError = document.getElementById('first-name-error');
            const lastNameError = document.getElementById('last-name-error');
            const emailError = document.getElementById('email-error');
            const phoneError = document.getElementById('phone-error');
            const cityError = document.getElementById('city-error');
            const provinceError = document.getElementById('province-error');
            const experienceError = document.getElementById('experience-error');
            const cropsError = document.getElementById('crops-error');
            const passwordError = document.getElementById('password-error');
            const confirmPasswordError = document.getElementById('confirm-password-error');
            const termsError = document.getElementById('terms-error');
            const successElement = document.getElementById('registration-success');

           
            let isFormValid = true;
            
            isFormValid = validateName(firstName, firstNameError) && isFormValid;
            isFormValid = validateName(lastName, lastNameError) && isFormValid;
            isFormValid = validateEmail(email, emailError) && isFormValid;
            isFormValid = validatePhone(phone, phoneError) && isFormValid;
            isFormValid = validateCity(city, cityError) && isFormValid;
            isFormValid = validateProvince(province, provinceError) && isFormValid;
            isFormValid = validateExperience(experience, experienceError) && isFormValid;
            isFormValid = validateCrops(crops, cropsError) && isFormValid;
            isFormValid = validatePassword(password, passwordError) && isFormValid;
            isFormValid = validateConfirmPassword(password, confirmPassword, confirmPasswordError) && isFormValid;
            isFormValid = validateTerms(terms, termsError) && isFormValid;

            if (isFormValid) {
                successElement.textContent = "Registration successful! Welcome to Farm2Door as a Farmer!";
                successElement.style.color = "green";
                console.log("Farmer registration data:", {
                    firstName, lastName, email, phone, city, province, experience, crops, password
                });
                
                
            } else {
                // Scroll to first error
                const firstError = document.querySelector('.error:not(:empty)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});

// Addeddd smooth scrolling for better UX
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});