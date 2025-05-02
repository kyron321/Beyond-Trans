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
