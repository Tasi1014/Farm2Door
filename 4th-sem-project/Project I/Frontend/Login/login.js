// Mobile menu toggle
const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

const consumer_success = document.getElementById("consumer-success");
const farmer_success = document.getElementById("farmer-success");
const admin_success = document.getElementById("admin-success");

hamburger.addEventListener('click', () => {
    navLinks.classList.toggle('mobile-active');
});

// Login form functionality
function showLoginForm(type) {
  // Get all forms
  const forms = {
    farmer: document.getElementById('farmer-form'),
    consumer: document.getElementById('consumer-form'),
    admin: document.getElementById('admin-form')
  };

  // Get all cards
  const cards = {
    farmer: document.querySelector('.farmer-login'),
    consumer: document.querySelector('.consumer-login'),
    admin: document.querySelector('.admin-login')
  };

  // Check if the clicked form is already visible
  const isAlreadyVisible = forms[type].style.display === 'block';

  // Hide all forms
  Object.values(forms).forEach(form => form.style.display = 'none');

  // Remove active class from all cards
  Object.values(cards).forEach(card => card.classList.remove('active'));

  // If the clicked form was already visible, just hide all (toggle off)
  if (isAlreadyVisible) return;

  // Otherwise, show the selected form and activate its card
  forms[type].style.display = 'block';
  cards[type].classList.add('active');

  // Smooth scroll to the form
  setTimeout(() => {
    const formElement = forms[type];
    const offset = 98; // small offset from top
    const topPos = formElement.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top: topPos, behavior: 'smooth' });
  }, 50);
}
