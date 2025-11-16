fetch("../../login-modal/login-modal.html")
  .then(res => res.text())
  .then(data => {
    document.getElementById("loginModal").innerHTML = data;

        document.querySelector(".close").addEventListener("click", closeModal);
  });


function showModal(){

    const navLinks = document.querySelector('.nav-links');
  if (navLinks.classList.contains('mobile-active')) {
    navLinks.classList.remove('mobile-active');
  }
    document.querySelector(".overlay").classList.add('showOverlay')
    document.querySelector(".modal").classList.add('showModal')
}

function closeModal(){
    document.querySelector(".overlay").classList.remove('showOverlay');
    document.querySelector(".modal").classList.remove('showModal');
}

