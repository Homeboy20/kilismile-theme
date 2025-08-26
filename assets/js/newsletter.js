/**
 * Newsletter Page JavaScript - Modern Enhanced Version
 */
(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Multi-step form navigation
        let currentStep = 1;
        const totalSteps = 3;
        
        // Preview tabs functionality
        $('.preview-tab').on('click', function() {
            const tab = $(this).data('tab');
            
            $('.preview-tab').removeClass('active');
            $(this).addClass('active');
            
            $('.preview-item').removeClass('active');
            $('[data-content="' + tab + '"]').addClass('active');
        });
        
        // Next step button
        $('.next-step').on('click', function() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });
        
        // Previous step button
        $('.prev-step').on('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
        
        // Show specific step
        function showStep(step) {
            $('.form-step').removeClass('active');
            $('[data-step="' + step + '"]').addClass('active');
            
            // Add animation
            $('[data-step="' + step + '"]').css({
                opacity: 0,
                transform: 'translateX(20px)'
            }).animate({
                opacity: 1
            }, 300, function() {
                $(this).css('transform', 'translateX(0)');
            });
            
            // Update progress if you want to add a progress bar
            updateProgress(step);
        }
        
        // Update progress indicator
        function updateProgress(step) {
            const progress = (step / totalSteps) * 100;
            $('.form-progress-bar').css('width', progress + '%');
        }
        
        // Validate current step
        function validateCurrentStep() {
            const currentStepEl = $('[data-step="' + currentStep + '"]');
            let isValid = true;
            
            // Check required fields in current step
            currentStepEl.find('input[required]').each(function() {
                const $input = $(this);
                const $group = $input.closest('.form-group');
                
                if (!$input.val().trim()) {
                    $group.addClass('error');
                    isValid = false;
                    
                    if (!$group.find('.error-message').length) {
                        $group.append('<span class="error-message">This field is required</span>');
                    }
                } else {
                    $group.removeClass('error');
                    $group.find('.error-message').remove();
                    
                    // Email validation
                    if ($input.attr('type') === 'email') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test($input.val())) {
                            $group.addClass('error');
                            isValid = false;
                            if (!$group.find('.error-message').length) {
                                $group.append('<span class="error-message">Please enter a valid email address</span>');
                            }
                        }
                    }
                }
            });
            
            // Check for at least one interest selected in step 2
            if (currentStep === 2) {
                const checkedInterests = currentStepEl.find('input[name="interests[]"]:checked');
                if (checkedInterests.length === 0) {
                    alert('Please select at least one area of interest.');
                    isValid = false;
                }
            }
            
            // Check consent in step 3
            if (currentStep === 3) {
                const consent = currentStepEl.find('input[name="consent"]:checked');
                if (consent.length === 0) {
                    alert('Please agree to receive our newsletter to continue.');
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        // Interest card selection animation
        $('.interest-card').on('click', function() {
            const checkbox = $(this).find('input[type="checkbox"]');
            const isChecked = checkbox.prop('checked');
            
            // Toggle checkbox
            checkbox.prop('checked', !isChecked);
            
            // Animate card
            $(this).addClass('selecting');
            setTimeout(() => {
                $(this).removeClass('selecting');
            }, 300);
        });
        
        // Frequency card selection
        $('.frequency-card').on('click', function() {
            const radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);
            
            // Visual feedback
            $('.frequency-card').removeClass('selected');
            $(this).addClass('selected');
        });
        
        // Newsletter subscription form
        $('#newsletter-subscription-form').on('submit', function(e) {
            e.preventDefault();
            
            if (!validateCurrentStep()) {
                return false;
            }
            
            var $form = $(this);
            var $message = $('#newsletter-message');
            var $submitBtn = $form.find('.subscribe-btn');
            
            // Add loading state
            $form.addClass('loading');
            $submitBtn.prop('disabled', true);
            
            // Store original button text
            const originalText = $submitBtn.html();
            $submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Subscribing...');
            
            // Collect form data
            var formData = {
                action: 'newsletter_subscription',
                newsletter_nonce: $form.find('input[name="newsletter_nonce"]').val(),
                email: $form.find('input[name="email"]').val(),
                first_name: $form.find('input[name="first_name"]').val(),
                last_name: $form.find('input[name="last_name"]').val(),
                location: $form.find('input[name="location"]').val(),
                interests: $form.find('input[name="interests[]"]:checked').map(function() {
                    return this.value;
                }).get(),
                frequency: $form.find('input[name="frequency"]:checked').val(),
                consent: $form.find('input[name="consent"]:checked').val()
            };
            
            // Send AJAX request
            $.ajax({
                url: kilismile_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Show success message with animation
                        $message.removeClass('error').addClass('success').html(
                            '<i class="fas fa-check-circle"></i> ' + response.data
                        ).show();
                        
                        // Show success step
                        showSuccessStep();
                        
                        // Scroll to message
                        $('html, body').animate({
                            scrollTop: $message.offset().top - 100
                        }, 500);
                        
                        // Track conversion (if you have analytics)
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'newsletter_subscription', {
                                'event_category': 'engagement',
                                'event_label': 'newsletter_form'
                            });
                        }
                        
                    } else {
                        $message.removeClass('success').addClass('error').html(
                            '<i class="fas fa-exclamation-circle"></i> ' + response.data
                        ).show();
                    }
                },
                error: function() {
                    $message.removeClass('success').addClass('error').html(
                        '<i class="fas fa-exclamation-triangle"></i> An error occurred. Please try again later.'
                    ).show();
                },
                complete: function() {
                    $form.removeClass('loading');
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
        
        // Show success step
        function showSuccessStep() {
            const successHtml = `
                <div class="success-step">
                    <div class="success-animation">
                        <div class="checkmark-circle">
                            <div class="checkmark"></div>
                        </div>
                    </div>
                    <h3>Welcome to Our Community! 🎉</h3>
                    <p>Thank you for subscribing! You'll receive a confirmation email shortly.</p>
                    <div class="next-steps">
                        <h4>What happens next?</h4>
                        <ul>
                            <li><i class="fas fa-envelope"></i> Check your email for a confirmation message</li>
                            <li><i class="fas fa-heart"></i> You'll receive your first newsletter within 24 hours</li>
                            <li><i class="fas fa-users"></i> Join our community of changemakers</li>
                        </ul>
                    </div>
                    <div class="success-actions">
                        <a href="${window.location.origin}/donate" class="btn btn-primary">
                            <i class="fas fa-heart"></i> Make a Donation
                        </a>
                        <a href="${window.location.origin}/volunteer" class="btn btn-outline">
                            <i class="fas fa-hands-helping"></i> Volunteer
                        </a>
                    </div>
                </div>
            `;
            
            $('.modern-form').html(successHtml);
            
            // Animate success elements
            $('.success-animation .checkmark-circle').addClass('animate');
            setTimeout(() => {
                $('.success-step h3, .success-step p').addClass('fade-in');
            }, 500);
            setTimeout(() => {
                $('.next-steps, .success-actions').addClass('fade-in');
            }, 1000);
        }
        
        // Load more newsletters
        $('.load-more-newsletters').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var page = parseInt($btn.data('page'));
            
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
            
            $.ajax({
                url: kilismile_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'load_more_newsletters',
                    page: page
                },
                success: function(response) {
                    if (response.success) {
                        $('.newsletter-grid').append(response.data.html);
                        
                        if (response.data.has_more) {
                            $btn.data('page', page + 1).html('<i class="fas fa-plus"></i> Load More Newsletters').prop('disabled', false);
                        } else {
                            $btn.hide();
                        }
                        
                        // Animate new items
                        $('.newsletter-grid .newsletter-item').slice(-6).each(function(index) {
                            $(this).css({
                                opacity: 0,
                                transform: 'translateY(20px)'
                            }).delay(index * 100).animate({
                                opacity: 1
                            }, 500, function() {
                                $(this).css('transform', 'translateY(0)');
                            });
                        });
                    } else {
                        $btn.html('<i class="fas fa-exclamation"></i> No More Newsletters').prop('disabled', true);
                    }
                },
                error: function() {
                    $btn.html('<i class="fas fa-exclamation-triangle"></i> Error Loading').prop('disabled', true);
                }
            });
        });
        
        // Animated counter for stats
        function animateCounter() {
            $('.stat-number').each(function() {
                var $this = $(this);
                var target = parseInt($this.data('count'));
                var current = 0;
                var increment = target / 60;
                var timer = setInterval(function() {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Format numbers with commas
                    const formatted = Math.floor(current).toLocaleString();
                    $this.text(formatted + ($this.parent().find('.stat-label').text().includes('%') ? '%' : ''));
                }, 25);
            });
        }
        
        // Trigger counter animation when stats section is in view
        function checkStatsInView() {
            var statsSection = $('.newsletter-stats');
            if (statsSection.length) {
                var sectionTop = statsSection.offset().top;
                var sectionHeight = statsSection.outerHeight();
                var windowTop = $(window).scrollTop();
                var windowHeight = $(window).height();
                
                if (windowTop + windowHeight > sectionTop + 100 && !statsSection.hasClass('animated')) {
                    statsSection.addClass('animated');
                    animateCounter();
                }
            }
        }
        
        // Check on scroll with throttling
        let ticking = false;
        $(window).on('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() {
                    checkStatsInView();
                    ticking = false;
                });
                ticking = true;
            }
        });
        
        // Check on load
        checkStatsInView();
        
        // Smooth scrolling for anchor links
        $('.smooth-scroll').on('click', function(e) {
            e.preventDefault();
            var target = $($(this).attr('href'));
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 800, 'easeInOutCubic');
            }
        });
        
        // Enhanced form validation
        $('.modern-form input[required]').on('blur', function() {
            const $input = $(this);
            const $group = $input.closest('.form-group');
            
            validateField($input, $group);
        });
        
        // Real-time validation
        $('.modern-form input[type="email"]').on('input', function() {
            const $input = $(this);
            const $group = $input.closest('.form-group');
            
            if ($input.val().length > 0) {
                validateField($input, $group);
            }
        });
        
        function validateField($input, $group) {
            if (!$input.val().trim()) {
                $group.addClass('error');
                if (!$group.find('.error-message').length) {
                    $group.append('<span class="error-message">This field is required</span>');
                }
            } else {
                $group.removeClass('error');
                $group.find('.error-message').remove();
                
                // Email validation
                if ($input.attr('type') === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test($input.val())) {
                        $group.addClass('error');
                        if (!$group.find('.error-message').length) {
                            $group.append('<span class="error-message">Please enter a valid email address</span>');
                        }
                    } else {
                        $group.addClass('success');
                    }
                } else {
                    $group.addClass('success');
                }
            }
        }
        
        // Show messages from URL parameters
        var urlParams = new URLSearchParams(window.location.search);
        var message = '';
        var messageType = '';
        
        if (urlParams.get('confirmed')) {
            message = 'Your newsletter subscription has been confirmed! Welcome to our community. 🎉';
            messageType = 'success';
        } else if (urlParams.get('unsubscribed')) {
            message = 'You have been successfully unsubscribed from our newsletter.';
            messageType = 'success';
        } else if (urlParams.get('error') === 'invalid_token') {
            message = 'Invalid confirmation link. Please try subscribing again.';
            messageType = 'error';
        } else if (urlParams.get('newsletter_message')) {
            message = decodeURIComponent(urlParams.get('newsletter_message'));
            messageType = 'success';
        }
        
        if (message) {
            $('#newsletter-message').removeClass('success error').addClass(messageType).html(
                '<i class="fas fa-' + (messageType === 'success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + message
            ).show();
            
            // Clean URL
            if (window.history && window.history.replaceState) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
        
        // Auto-hide messages after 8 seconds
        setTimeout(function() {
            $('#newsletter-message').fadeOut(500);
        }, 8000);
        
        // Newsletter item hover effects with better performance
        $('.newsletter-item').hover(
            function() {
                $(this).addClass('hovered');
            },
            function() {
                $(this).removeClass('hovered');
            }
        );
        
        // Accessibility improvements
        $('.modern-form input, .modern-form button').on('focus', function() {
            $(this).closest('.form-group, .form-navigation').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.form-group, .form-navigation').removeClass('focused');
        });
        
        // Keyboard navigation for newsletter items
        $('.newsletter-item').attr('tabindex', '0').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                var link = $(this).find('.newsletter-title a').attr('href');
                if (link) {
                    window.location.href = link;
                }
            }
        });
        
        // Add particles animation on hero section
        function createParticle() {
            const particle = $('<div class="particle"></div>');
            const size = Math.random() * 4 + 2;
            const duration = Math.random() * 3 + 2;
            const delay = Math.random() * 2;
            
            particle.css({
                width: size + 'px',
                height: size + 'px',
                left: Math.random() * 100 + '%',
                top: Math.random() * 100 + '%',
                animationDuration: duration + 's',
                animationDelay: delay + 's'
            });
            
            $('.hero-particles').append(particle);
            
            // Remove particle after animation
            setTimeout(() => {
                particle.remove();
            }, (duration + delay) * 1000);
        }
        
        // Create particles periodically
        setInterval(createParticle, 1000);
        
        // Initialize with some particles
        for (let i = 0; i < 5; i++) {
            setTimeout(createParticle, i * 200);
        }
        
    });
    
})(jQuery);

// Add CSS for modern form enhancements
document.addEventListener('DOMContentLoaded', function() {
    var style = document.createElement('style');
    style.textContent = `
        /* Enhanced Form Validation States */
        .form-group.error input {
            border-color: #dc3545;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.15);
            background: #fff5f5;
        }
        
        .form-group.success input {
            border-color: #28a745;
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.15);
            background: #f8fff8;
        }
        
        .form-group.focused input {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.15);
            background: white;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 5px;
            animation: slideIn 0.3s ease;
        }
        
        .error-message::before {
            content: "⚠️";
            font-size: 12px;
        }
        
        /* Newsletter Item Hover Enhancement */
        .newsletter-item:focus {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
            border-radius: 12px;
        }
        
        .newsletter-item.hovered .newsletter-thumbnail img {
            transform: scale(1.05);
        }
        
        /* Stats Animation */
        .newsletter-stats.animated .stat-item {
            animation: slideInUp 0.6s ease-out forwards;
        }
        
        .newsletter-stats.animated .stat-item:nth-child(2) {
            animation-delay: 0.1s;
        }
        
        .newsletter-stats.animated .stat-item:nth-child(3) {
            animation-delay: 0.2s;
        }
        
        .newsletter-stats.animated .stat-item:nth-child(4) {
            animation-delay: 0.3s;
        }
        
        /* Interest Card Selection Animation */
        .interest-card.selecting {
            transform: scale(1.05);
            box-shadow: 0 15px 35px rgba(76, 175, 80, 0.2);
        }
        
        .frequency-card.selected {
            border-color: var(--primary-color);
            background: #f8fff8;
            box-shadow: 0 8px 25px rgba(76, 175, 80, 0.15);
        }
        
        /* Success Step Styles */
        .success-step {
            text-align: center;
            padding: 60px 40px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .success-animation {
            margin-bottom: 30px;
        }
        
        .checkmark-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transform: scale(0);
            transition: transform 0.5s ease;
        }
        
        .checkmark-circle.animate {
            transform: scale(1);
        }
        
        .checkmark {
            width: 25px;
            height: 25px;
            border: 3px solid white;
            border-top: none;
            border-left: none;
            transform: rotate(45deg);
            opacity: 0;
            animation: checkmarkDraw 0.5s 0.3s ease forwards;
        }
        
        .success-step h3 {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 15px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .success-step p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .next-steps {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .next-steps h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
            font-size: 1.2rem;
        }
        
        .next-steps ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .next-steps li {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            font-size: 15px;
            color: #555;
        }
        
        .next-steps li i {
            color: var(--primary-color);
            width: 16px;
        }
        
        .success-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.5s ease;
        }
        
        .success-actions .btn {
            padding: 12px 25px;
            font-size: 15px;
            font-weight: 600;
            min-width: 140px;
        }
        
        .fade-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
        
        /* Smooth Scrolling Enhancement */
        html {
            scroll-behavior: smooth;
        }
        
        /* Loading States */
        .modern-form.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        
        .modern-form.loading .subscribe-btn {
            position: relative;
        }
        
        /* Enhanced Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes checkmarkDraw {
            from {
                opacity: 0;
                transform: scale(0) rotate(45deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(45deg);
            }
        }
        
        @keyframes pulse {
            0%, 100% { 
                transform: scale(1);
                opacity: 1; 
            }
            50% { 
                transform: scale(1.05);
                opacity: 0.8; 
            }
        }
        
        /* Mobile Responsive Enhancements */
        @media (max-width: 768px) {
            .success-step {
                padding: 40px 20px;
            }
            
            .checkmark-circle {
                width: 60px;
                height: 60px;
            }
            
            .checkmark {
                width: 18px;
                height: 18px;
            }
            
            .success-step h3 {
                font-size: 1.5rem;
            }
            
            .success-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .success-actions .btn {
                width: 100%;
                max-width: 280px;
            }
        }
        
        /* Accessibility Improvements */
        @media (prefers-reduced-motion: reduce) {
            .checkmark-circle,
            .success-step h3,
            .success-step p,
            .next-steps,
            .success-actions {
                transition: none;
                animation: none;
            }
            
            .particle {
                animation: none;
            }
        }
        
        /* Focus Styles for Better Accessibility */
        .preview-tab:focus,
        .interest-card:focus,
        .frequency-card:focus,
        .consent-checkbox:focus {
            outline: 3px solid var(--primary-color);
            outline-offset: 2px;
        }
        
        /* High Contrast Mode Support */
        @media (prefers-contrast: high) {
            .interest-card,
            .frequency-card {
                border-width: 3px;
            }
            
            .interest-card:hover,
            .frequency-card:hover {
                border-width: 4px;
            }
        }
    `;
    document.head.appendChild(style);
});
