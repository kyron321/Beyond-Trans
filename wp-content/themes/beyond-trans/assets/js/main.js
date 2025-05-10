document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.main-nav__toggle');
    const menu = document.querySelector('.main-nav__menu');

    if (menuToggle && menu) {
        menuToggle.addEventListener('click', () => {
            const isActive = menuToggle.classList.toggle('active');
            menu.classList.toggle('active');
            menuToggle.setAttribute('aria-expanded', isActive);
        });

        const parentMenuItems = menu.querySelectorAll('.menu-item-has-children > a');
        parentMenuItems.forEach(item => {
            if (!item.hasAttribute('aria-expanded')) {
              item.setAttribute('aria-expanded', 'false');
            }

            item.addEventListener('click', (event) => {
                if (window.innerWidth <= 991 && menu.classList.contains('active')) {
                    event.preventDefault();
                    const parentLi = item.closest('.menu-item-has-children');
                    const isOpen = parentLi.classList.toggle('submenu-open');
                    item.setAttribute('aria-expanded', isOpen);
                }
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function() {
  // Get all elements with fade-in class
  const fadeElements = document.querySelectorAll('.fade-in');
  
  // Create intersection observer
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      // Add is-visible class when element enters viewport
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        // Optional: stop observing after animation
        // observer.unobserve(entry.target);
      }
    });
  }, {
    root: null, // viewport
    threshold: 0.1, // trigger when 10% of the element is visible
    rootMargin: '0px 0px -50px 0px' // adjust as needed
  });
  
  // Observe each fade element
  fadeElements.forEach(el => observer.observe(el));
});