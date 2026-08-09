/* ==========================================
   Shree Shyam Hotel and Restorent - JS
   Interactive jQuery Animations & Functionality
========================================== */

$(document).ready(function() {

    // --- Force Page Scroll to Top on Refresh ---
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);
    $(window).on('beforeunload', function() {
        window.scrollTo(0, 0);
    });

    // --- Preloader Fade Out ---
    function dismissPreloader() {
        if ($('.preloader').length) {
            $('.preloader').fadeOut('slow', function() {
                $('body').removeClass('loading-active');
                $('.hero-content').addClass('hero-ready'); // Trigger staggered hero entrance animations
                $(this).remove();
            });
        } else {
            $('.hero-content').addClass('hero-ready');
        }
    }

    // Dismiss preloader when page resources are fully loaded
    $(window).on('load', function() {
        dismissPreloader();
    });

    // Failsafe: dismiss preloader after 3.5 seconds anyway in case of slow resources
    setTimeout(dismissPreloader, 3500);

    // --- AOS (Animate On Scroll) Initialization ---
    AOS.init({
        duration: 900,
        once: false,
        offset: 80, // Reduced offset so animations trigger earlier on smaller mobile viewports
        easing: 'ease-out-cubic'
    });

    // --- Dynamic Navbar Background on Scroll ---
    const $navbar = $('#mainNavbar');
    const $backToTop = $('#backToTopBtn');

    function checkScroll() {
        if ($(window).scrollTop() > 50) {
            $navbar.addClass('scrolled');
            $backToTop.addClass('show');
        } else {
            $navbar.removeClass('scrolled');
            $backToTop.removeClass('show');
        }
    }

    // Initial check on load
    checkScroll();
    
    // Bind scroll listener
    $(window).on('scroll', function() {
        checkScroll();
        highlightNavOnScroll();
    });

    // --- Smooth Scroll for Anchor Links ---
    $('a[href^="#"]').on('click', function(event) {
        const target = this.hash;
        if (target) {
            event.preventDefault();
            
            // Auto close mobile menu if open
            const $navbarCollapse = $('.navbar-collapse');
            if ($navbarCollapse.hasClass('show')) {
                $('.navbar-toggler').trigger('click');
            }

            const offset = 80; // height of sticky header
            $('html, body').animate({
                scrollTop: $(target).offset().top - offset
            }, 800);
        }
    });

    // --- Active Link Highlight on Scroll (ScrollSpy) ---
    const $navLinks = $('.navbar-nav .nav-link');
    const $sections = $('section');

    function highlightNavOnScroll() {
        const scrollPos = $(window).scrollTop() + 100; // offset for detection

        $sections.each(function() {
            const sectionTop = $(this).offset().top;
            const sectionHeight = $(this).outerHeight();
            const id = $(this).attr('id');

            if (scrollPos >= sectionTop && scrollPos < (sectionTop + sectionHeight)) {
                $navLinks.removeClass('active');
                $(`.navbar-nav a[href="#${id}"]`).addClass('active');
            }
        });
    }

    // --- Room Card Button Interactive Booking Prefill ---
    $('.room-card .btn').on('click', function(e) {
        // e.preventDefault() is not called so it naturally scrolls to #contact
        const roomName = $(this).siblings('.room-title').text();
        const $selectType = $('#bookingType');
        const $messageField = $('#bookMessage');

        // Set select value to 'room'
        $selectType.val('room').trigger('change');
        
        // Prefill message field with requested room name
        $messageField.val(`Hello, I would like to check availability for the "${roomName}" lodging. Please let me know the reservation procedure.`);
        
        // Add subtle animation focus to message box
        setTimeout(() => {
            $messageField.focus();
        }, 850);
    });

    // --- Reservation Form Interactive Submission ---
    const $form = $('#reservationForm');
    const $submitBtn = $('#submitBtn');
    const $btnText = $submitBtn.find('.btn-text');
    const $spinner = $submitBtn.find('.spinner-border');
    const $statusAlert = $('#formStatus');

    $form.on('submit', function(event) {
        event.preventDefault();

        // Basic Custom Validation Check
        if ($form[0].checkValidity() === false) {
            event.stopPropagation();
            $form.addClass('was-validated');
            return;
        }

        $form.removeClass('was-validated');
        
        // Show Loading Animation
        $submitBtn.prop('disabled', true);
        $btnText.text('Opening WhatsApp...');
        $spinner.removeClass('d-none');
        $statusAlert.addClass('d-none');

        // Retrieve values for message
        const guestName = $('#bookName').val();
        const guestPhone = $('#bookPhone').val();
        const bookingTypeVal = $('#bookingType').val();
        const bookingDate = $('#bookDate').val();
        const guestMessage = $('#bookMessage').val() || "None";

        // Map request type to readable text
        let bookingTypeLabel = "Inquiry";
        if (bookingTypeVal === "room") bookingTypeLabel = "Book a Room (Lodging)";
        else if (bookingTypeVal === "table") bookingTypeLabel = "Book a Table (Dining)";
        else if (bookingTypeVal === "event") bookingTypeLabel = "Party Hall / Event Booking";
        else if (bookingTypeVal === "inquiry") bookingTypeLabel = "General Inquiry";

        // Construct message text
        const messageText = `Hello Shree Shyam Hotel and Restorent,%0A%0AI would like to make a reservation request.%0A%0A*Name:* ${encodeURIComponent(guestName)}%0A*Phone:* ${encodeURIComponent(guestPhone)}%0A*Request Type:* ${encodeURIComponent(bookingTypeLabel)}%0A*Preferred Date:* ${encodeURIComponent(bookingDate)}%0A*Special Message/Requirements:* ${encodeURIComponent(guestMessage)}`;

        // WhatsApp number (from the page details)
        const whatsappNumber = "919912561234";

        // WhatsApp URL
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${messageText}`;

        // Open WhatsApp in a new tab after a tiny delay
        setTimeout(function() {
            window.open(whatsappUrl, '_blank');

            // Reset loading state and form
            $spinner.addClass('d-none');
            $submitBtn.prop('disabled', false);
            $btnText.text('Submit Reservation Request');
            $form[0].reset();
            
            // Show status feedback alert on the page as well
            $('#statusName').text(guestName);
            $('#statusPhone').text(guestPhone);
            $statusAlert.removeClass('d-none');
            
            // Scroll to the status alert smoothly
            $('html, body').animate({
                scrollTop: $statusAlert.offset().top - 150
            }, 500);

        }, 1000);
    });

    // --- Newsletter Form Submission ---
    $('.newsletter-form').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $input = $form.find('input[type="email"]');
        const $errorMsg = $('.newsletter-error');
        const emailVal = $input.val().trim();
        
        // Custom Email Regex Validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (emailVal === "" || !emailRegex.test(emailVal)) {
            // Show custom error feedback
            $input.addClass('is-invalid');
            $errorMsg.removeClass('d-none');
            
            // Clear validation error when user begins typing again
            $input.off('input').on('input', function() {
                $input.removeClass('is-invalid');
                $errorMsg.addClass('d-none');
            });
            return;
        }

        $input.removeClass('is-invalid');
        $errorMsg.addClass('d-none');
        
        // Disable input and button
        $form.find('input, button').prop('disabled', true);
        
        // Fade out and display gold success message
        $form.fadeOut('fast', function() {
            $form.html('<p class="text-gold mb-0 small" style="animation: slideUpIn 0.4s ease;"><i class="fa-solid fa-circle-check me-2"></i>Thank you! Subscribed successfully.</p>').fadeIn('slow');
        });
    });

    // --- Dynamic Copyright Year ---
    $('#currentYear').text(new Date().getFullYear());

    // --- Custom Scroll Reveal Observer for Headings ---
    if ('IntersectionObserver' in window) {
        const revealCallback = (entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    $(entry.target).addClass('active');
                    observer.unobserve(entry.target); // Animate only once
                }
            });
        };

        const revealObserver = new IntersectionObserver(revealCallback, {
            root: null,
            threshold: 0.15,
            rootMargin: "0px 0px -40px 0px"
        });

        // Register observer on our animate classes
        $('.reveal-fade-up, .reveal-letter-stretch, .title-divider-grow').each(function() {
            revealObserver.observe(this);
        });
    } else {
        // Fallback for older browsers
        $('.reveal-fade-up, .reveal-letter-stretch, .title-divider-grow').addClass('active');
    }

    function initGoldDust() {
        $('section, footer').each(function() {
            const $section = $(this);
            if ($section.css('position') === 'static') {
                $section.css('position', 'relative');
            }
            const $container = $('<div class="gold-dust-container"></div>');
            $section.append($container);

            const sectionHeight = $section.outerHeight() || 600;
            // Spawn ~1 particle per 50px height, capped at 25 per section
            const particleCount = Math.min(Math.round(sectionHeight / 50), 25); 

            for (let i = 0; i < particleCount; i++) {
                const size = Math.random() * 3.5 + 1.5; // size between 1.5px and 5px
                const left = Math.random() * 100;
                const duration = Math.random() * 8 + 8; // duration between 8s and 16s
                const delay = Math.random() * 12; // animation delay offset
                
                const $particle = $('<span class="gold-particle"></span>').css({
                    width: size + 'px',
                    height: size + 'px',
                    left: left + '%',
                    animationDuration: duration + 's',
                    animationDelay: '-' + delay + 's'
                });
                $container.append($particle);
            }
        });
    }
    
    initGoldDust();

});
