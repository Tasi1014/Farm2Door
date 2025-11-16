const hamburger = document.querySelector('.hamburger');
const navLinks = document.querySelector('.nav-links');

// Toggle mobile menu
hamburger.addEventListener('click', () => {
  navLinks.classList.toggle('mobile-active');
});

// Close mobile menu when a link is clicked
const navLinksItems = document.querySelectorAll('.nav-links li a');
navLinksItems.forEach(link => {
  link.addEventListener('click', () => {
    navLinks.classList.remove('mobile-active');
  });
});
