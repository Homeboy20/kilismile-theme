/**
 * Kilismile Organization Theme JavaScript
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // DOM Ready
    $(document).ready(function() {
        
        // Initialize all functions
        initMobileMenu();
        initStickyHeader();
        initSmoothScrolling();
        initBackToTop();
        initStatsCounter();
        initCookieConsent();
        initNewsletterForm();
        initDonationForm();
        initAccessibilityFeatures();
        initPerformanceOptimizations();
        
    });

    /**
     * Mobile Menu Functionality
     */
    function initMobileMenu() {
        const $mobileToggle = $('.mobile-menu-toggle');
        const $navigation = $('.main-navigation');
        const $body = $('body');

        $mobileToggle.on('click', function(e) {
            e.preventDefault();
            
            const isExpanded = $(this).attr('aria-expanded') === 'true';
            
            // Toggle aria-expanded
            $(this).attr('aria-expanded', !isExpanded);
            
            // Toggle navigation
            $navigation.toggleClass('active');
            $body.toggleClass('menu-open');
            
            // Toggle icon
            const $icon = $(this).find('i');
            if ($navigation.hasClass('active')) {
                $icon.removeClass('fa-bars').addClass('fa-times');
            } else {
                $icon.removeClass('fa-times').addClass('fa-bars');
            }
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation, .mobile-menu-toggle').length) {
                $navigation.removeClass('active');
                $body.removeClass('menu-open');
                $mobileToggle.attr('aria-expanded', 'false');
                $mobileToggle.find('i').removeClass('fa-times').addClass('fa-bars');
            }
        });

        // Close menu on escape key
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $navigation.hasClass('active')) {
                $navigation.removeClass('active');
                $body.removeClass('menu-open');
                $mobileToggle.attr('aria-expanded', 'false').focus();
                $mobileToggle.find('i').removeClass('fa-times').addClass('fa-bars');
            }
        });
    }

    /**
     * Sticky Header Functionality
     */
    function initStickyHeader() {
        const $header = $('.site-header');
        
        if (!$header.length) {
            console.log('Header not found for sticky functionality');
            return;
        }
        
        // Add sticky class immediately
        $header.addClass('header-sticky');
        
        // Check if header is transparent
        const isTransparent = $header.hasClass('transparent-header') || 
                             $header.css('position') === 'absolute';
        
        // For transparent headers, we need different handling
        if (isTransparent) {
            $header.css('position', 'absolute');
            console.log('Transparent header detected');
        } else {
            $header.css('position', 'sticky');
        }
        
        console.log('Sticky header initialized successfully');
        
        // Force an initial scroll check
        setTimeout(function() {
            handleScroll();
        }, 100);
    }

    /**
     * Smooth Scrolling for Anchor Links
     */
    function initSmoothScrolling() {
        $('a[href^="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                
                const offset = $('.site-header').outerHeight() + 20;
                
                $('html, body').animate({
                    scrollTop: target.offset().top - offset
                }, 800, 'easeInOutCubic');
            }
        });
    }

    /**
     * Back to Top Button
     */
    function initBackToTop() {
        const $backToTop = $('#back-to-top');
        
        if (!$backToTop.length) {
            // Create back to top button if it doesn't exist
            $('body').append('<button id="back-to-top" class="back-to-top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></button>');
        }
        
        const $btn = $('#back-to-top');
        
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                $btn.fadeIn().addClass('visible');
            } else {
                $btn.fadeOut().removeClass('visible');
            }
        });
        
        $btn.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800, 'easeInOutCubic');
        });
    }

    /**
     * Animated Stats Counter
     */
    function initStatsCounter() {
        const $statsNumbers = $('.stat-number[data-count]');
        
        if ($statsNumbers.length && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting && !$(entry.target).hasClass('counted')) {
                        animateCounter($(entry.target));
                        $(entry.target).addClass('counted');
                    }
                });
            }, {
                threshold: 0.5,
                rootMargin: '0px 0px -50px 0px'
            });
            
            $statsNumbers.each(function() {
                observer.observe(this);
            });
        }
    }

    /**
     * Animate Counter Numbers
     */
    function animateCounter($element) {
        const target = parseInt($element.data('count'));
        const duration = 2000;
        const increment = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                $element.text(target.toLocaleString());
                clearInterval(timer);
            } else {
                $element.text(Math.floor(current).toLocaleString());
            }
        }, 16);
    }

    /**
     * Cookie Consent Management
     */
    function initCookieConsent() {
        const $cookieNotice = $('#cookie-notice');
        const $acceptBtn = $('#accept-cookies');
        const $declineBtn = $('#decline-cookies');
        
        // Show notice if no consent given
        if (!localStorage.getItem('cookieConsent')) {
            $cookieNotice.slideDown();
        }
        
        $acceptBtn.on('click', function() {
            localStorage.setItem('cookieConsent', 'accepted');
            $cookieNotice.slideUp();
            
            // Enable tracking scripts here
            enableAnalytics();
        });
        
        $declineBtn.on('click', function() {
            localStorage.setItem('cookieConsent', 'declined');
            $cookieNotice.slideUp();
        });
    }

    /**
     * Newsletter Subscription Form
     */
    function initNewsletterForm() {
        $('.newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $email = $form.find('input[name="newsletter_email"]');
            const $button = $form.find('button[type="submit"]');
            const email = $email.val().trim();
            
            if (!isValidEmail(email)) {
                showMessage($form, 'Please enter a valid email address.', 'error');
                return;
            }
            
            // Disable button and show loading
            $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            // AJAX request to handle newsletter subscription
            $.ajax({
                url: kilismile_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_newsletter_subscribe',
                    email: email,
                    nonce: kilismile_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        showMessage($form, 'Thank you for subscribing!', 'success');
                        $email.val('');
                    } else {
                        showMessage($form, response.data || 'Subscription failed. Please try again.', 'error');
                    }
                },
                error: function() {
                    showMessage($form, 'Network error. Please try again.', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).html('<i class="fas fa-paper-plane"></i>');
                }
            });
        });
    }

    /**
     * Donation Form Enhancements
     */
    function initDonationForm() {
        $('.donation-form').on('submit', function(e) {
            const $form = $(this);
            const amount = $form.find('input[name="donation_amount"]').val();
            
            if (!amount || parseFloat(amount) < 1) {
                e.preventDefault();
                showMessage($form, 'Please enter a valid donation amount.', 'error');
                return;
            }
            
            // Show loading state
            $form.find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        });
        
        // Donation amount buttons
        $('.donation-amounts button').on('click', function(e) {
            e.preventDefault();
            const amount = $(this).data('amount');
            $('.donation-form input[name="donation_amount"]').val(amount);
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');
        });
    }

    /**
     * Accessibility Enhancements
     */
    function initAccessibilityFeatures() {
        // Skip to content functionality
        $('.skip-link').on('click', function(e) {
            e.preventDefault();
            const target = $($(this).attr('href'));
            if (target.length) {
                target.focus();
                $('html, body').animate({
                    scrollTop: target.offset().top
                }, 300);
            }
        });
        
        // Keyboard navigation for mobile menu
        $('.main-navigation a').on('keydown', function(e) {
            if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
                $(this)[0].click();
            }
        });
        
        // Focus management for modals and dropdowns
        $('[role="dialog"], .dropdown-menu').on('keydown', function(e) {
            if (e.keyCode === 27) { // Escape key
                $(this).find('.close, [data-dismiss]').click();
            }
        });
    }

    /**
     * Performance Optimizations
     */
    function initPerformanceOptimizations() {
        // Lazy load images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;
                        if (src) {
                            img.src = src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        }
        
        // Preload critical resources
        preloadCriticalResources();
        
        // Optimize scroll events
        let ticking = false;
        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    /**
     * Handle Optimized Scroll Events
     */
    function handleScroll() {
        const scrollTop = $(window).scrollTop();
        const $header = $('.site-header');
        
        // Check if header is transparent
        const isTransparent = $header.hasClass('transparent-header') || 
                             $header.css('position') === 'absolute';
        
        // Sticky Header functionality with enhanced effects
        if (scrollTop > 50) {
            if (!$header.hasClass('is-sticky')) {
                $header.addClass('is-sticky');
                
                if (isTransparent) {
                    // For transparent headers, switch to fixed position when sticky
                    $header.css({
                        'position': 'fixed',
                        'top': '0',
                        'left': '0',
                        'right': '0',
                        'width': '100%',
                        'z-index': '1000'
                    });
                } else {
                    // For regular headers, just add background effects
                    $header.css({
                        'background': 'rgba(255, 255, 255, 0.95)',
                        'backdrop-filter': 'blur(10px)',
                        'box-shadow': '0 4px 20px rgba(0, 0, 0, 0.15)'
                    });
                }
            }
        } else {
            if ($header.hasClass('is-sticky')) {
                $header.removeClass('is-sticky');
                
                if (isTransparent) {
                    // For transparent headers, return to absolute position
                    $header.css({
                        'position': 'absolute',
                        'top': '0',
                        'left': '0',
                        'right': '0',
                        'width': '100%'
                    });
                } else {
                    // For regular headers, remove background effects
                    $header.css({
                        'background': '',
                        'backdrop-filter': '',
                        'box-shadow': '0 2px 10px rgba(0, 0, 0, 0.1)'
                    });
                }
            }
        }
        
        // Legacy header scroll effect
        if (scrollTop > 100) {
            $header.addClass('scrolled');
        } else {
            $header.removeClass('scrolled');
        }
        
        // Parallax effects for hero section
        if ($('.hero-section').length) {
            const parallaxSpeed = 0.5;
            $('.hero-section').css('transform', `translateY(${scrollTop * parallaxSpeed}px)`);
        }
    }

    /**
     * Preload Critical Resources
     */
    function preloadCriticalResources() {
        // Only preload images that actually exist
        const criticalImages = [
            '/wp-content/themes/kilismile/assets/images/logo.svg',
            '/wp-content/themes/kilismile/assets/images/hero-background.svg'
        ];
        
        criticalImages.forEach(function(src) {
            // Test if image exists before preloading
            const img = new Image();
            img.onload = function() {
                const link = document.createElement('link');
                link.rel = 'preload';
                link.as = 'image';
                link.href = src;
                document.head.appendChild(link);
            };
            img.onerror = function() {
                console.log('Image not found, skipping preload:', src);
            };
            img.src = src;
        });
    }

    /**
     * Enable Analytics (called after cookie consent)
     */
    function enableAnalytics() {
        // Google Analytics 4
        if (typeof gtag !== 'undefined') {
            gtag('consent', 'update', {
                'analytics_storage': 'granted'
            });
        }
        
        // Facebook Pixel
        if (typeof fbq !== 'undefined') {
            fbq('consent', 'grant');
        }
    }

    /**
     * Utility Functions
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showMessage($container, message, type) {
        const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
        const $alert = $(`<div class="alert ${alertClass}" role="alert">${message}</div>`);
        
        $container.find('.alert').remove();
        $container.prepend($alert);
        
        setTimeout(function() {
            $alert.fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Custom easing function
     */
    $.easing.easeInOutCubic = function(x) {
        return x < 0.5 ? 4 * x * x * x : 1 - Math.pow(-2 * x + 2, 3) / 2;
    };

    /**
     * Window resize handler
     */
    $(window).on('resize', debounce(function() {
        // Recalculate layouts on resize
        handleResize();
    }, 250));

    function handleResize() {
        // Close mobile menu on desktop resize
        if ($(window).width() > 768) {
            $('.main-navigation').removeClass('active');
            $('.mobile-menu-toggle').attr('aria-expanded', 'false');
            $('body').removeClass('menu-open');
        }
    }

    /**
     * Debounce function for performance
     */
    function debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    /**
     * Form validation helper
     */
    function validateForm($form) {
        let isValid = true;
        
        $form.find('[required]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            
            if (!value) {
                showFieldError($field, 'This field is required.');
                isValid = false;
            } else if ($field.attr('type') === 'email' && !isValidEmail(value)) {
                showFieldError($field, 'Please enter a valid email address.');
                isValid = false;
            } else {
                clearFieldError($field);
            }
        });
        
        return isValid;
    }

    function showFieldError($field, message) {
        const $error = $field.siblings('.field-error');
        if ($error.length) {
            $error.text(message);
        } else {
            $field.after(`<span class="field-error" style="color: #d32f2f; font-size: 0.8rem; display: block; margin-top: 5px;">${message}</span>`);
        }
        $field.addClass('error');
    }

    function clearFieldError($field) {
        $field.removeClass('error').siblings('.field-error').remove();
    }

})(jQuery);

/**
 * Vanilla JavaScript for critical functionality
 * (Loaded even if jQuery fails)
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // Critical accessibility features
    const skipLink = document.querySelector('.skip-link');
    if (skipLink) {
        skipLink.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.focus();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
    
    // Critical mobile menu fallback
    const mobileToggle = document.querySelector('.mobile-menu-toggle');
    const navigation = document.querySelector('.main-navigation');
    
    if (mobileToggle && navigation) {
        mobileToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            navigation.classList.toggle('active');
        });
    }
    
    // Service Worker Registration disabled for now
    // TODO: Create sw.js file if PWA functionality is needed
    /*
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('SW registered: ', registration);
                })
                .catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }
    */
    
});


