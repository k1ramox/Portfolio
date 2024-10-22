const hamburger = document.querySelector(".hamburger");
const navMenu = document.querySelector(".nav-menu");

    hamburger.addEventListener('click', function(event){
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });

    navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(links) {
        links.addEventListener('click', function(event) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        });
    });

    const feedbackMessage = document.getElementById('feedback-below-header');
    if (feedbackMessage) {
        setTimeout(() => {
            feedbackMessage.classList.add('hidden');
        }, 2000); // Fade out after 3 seconds
    }
