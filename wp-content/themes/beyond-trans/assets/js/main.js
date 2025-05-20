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

    // Fade-in animation with Intersection Observer
    const fadeElements = document.querySelectorAll('.fade-in');
  
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, {
        root: null, 
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px' 
    });

    fadeElements.forEach(el => observer.observe(el));

    // Adding scroll class to navigation
    const navContainer = document.querySelector('.main-nav');
    const adminBarHeight = 32;
    const topNavHeight = 54;
    let isScrolled = false; 
    let isWPAdminBar = false; 
  
    if (document.body.classList.contains('admin-bar')) {
        isWPAdminBar = true;
    }
  
    function checkScroll() {
        const threshold = isWPAdminBar ? topNavHeight + adminBarHeight : topNavHeight;
    
        if (window.scrollY >= threshold && !isScrolled) {
            navContainer.classList.add('scrolled');
            isScrolled = true;
        } else if (window.scrollY < threshold && isScrolled) {
            navContainer.classList.remove('scrolled');
            isScrolled = false;
        }
    }

    checkScroll();
    window.addEventListener('scroll', checkScroll);
});