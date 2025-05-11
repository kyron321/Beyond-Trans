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
});

// adding on scroll class
document.addEventListener('DOMContentLoaded', function() {
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

// create a function which finds a file upload field from ninja forms and gets the class file_upload-wrap
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
});

// adding on scroll class
document.addEventListener('DOMContentLoaded', function() {
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

// File upload handler for Ninja Forms
document.addEventListener('DOMContentLoaded', function() {
  // Track if we've already set up listeners to prevent duplication
  let listenersInitialized = false;
  
  const findAndSetupFileUpload = function() {
    // First try to get the element directly
    let fileUploadWrap = document.querySelector('.file_upload-wrap');
    
    if (fileUploadWrap && !listenersInitialized) {
      console.log('File upload wrap found:', fileUploadWrap);
      setupFileUploadListener(fileUploadWrap);
      listenersInitialized = true;
      return;
    }
    
    // If not found, set up a MutationObserver to watch for changes
    const observer = new MutationObserver(function(mutations) {
      if (listenersInitialized) {
        observer.disconnect();
        return;
      }
      
      fileUploadWrap = document.querySelector('.file_upload-wrap');
      if (fileUploadWrap) {
        console.log('File upload wrap found after DOM changes:', fileUploadWrap);
        observer.disconnect(); // Stop observing once found
        setupFileUploadListener(fileUploadWrap);
        listenersInitialized = true;
      }
    });
    
    // Start observing the document with the configured parameters
    observer.observe(document.body, { childList: true, subtree: true });
  };

  // Set up the listener for file input changes
  const setupFileUploadListener = function(wrapElement) {
    const fileInput = wrapElement.querySelector('input[type="file"]');
    
    if (fileInput) {
      // Remove any existing listeners first (to prevent duplicates)
      const newFileInput = fileInput.cloneNode(true);
      fileInput.parentNode.replaceChild(newFileInput, fileInput);
      
      newFileInput.addEventListener('change', function(event) {
        const files = event.target.files;
        
        if (files && files.length > 0) {
          // Remove any existing info containers first
          const existingContainers = wrapElement.querySelectorAll('.file-upload-info');
          existingContainers.forEach(container => container.remove());
          
          // Create a new container to display file info
          const fileInfoContainer = document.createElement('div');
          fileInfoContainer.className = 'file-upload-info';
          wrapElement.appendChild(fileInfoContainer);
          
          // Display file information
          const file = files[0];
          const fileInfo = document.createElement('p');
          fileInfo.innerHTML = `File uploaded: <strong>${file.name}</strong> (${(file.size / 1024).toFixed(2)} KB)`;
          fileInfoContainer.appendChild(fileInfo);
          
          // Optional: Add a thumbnail preview if it's an image
          if (file.type.match('image.*')) {
            const reader = new FileReader();
            reader.onload = function(e) {
              const img = document.createElement('img');
              img.src = e.target.result;
              fileInfoContainer.appendChild(img);
            };
            reader.readAsDataURL(file);
          }
        }
      });
    } else {
      console.log('File input element not found inside wrapper');
    }
  };

  // Start the process
  findAndSetupFileUpload();
});