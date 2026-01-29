/**
 * KiliSmile Email Forms JavaScript
 * 
 * Handles AJAX form submissions for contact, newsletter, and volunteer forms
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

(function($) {
    'use strict';
    
    // Form submission handler
    function handleFormSubmission(form, formType) {
        const $form = $(form);
        const $submitBtn = $form.find('button[type="submit"]');
        const $message = $form.find('.form-message');
        const originalText = $submitBtn.text();
        
        // Show loading state
        $submitBtn.prop('disabled', true).text(kilismileEmail.strings.sending);
        $message.removeClass('success error').empty();
        
        // Serialize form data
        const formData = $form.serialize();
        const actionMap = {
            'contact': 'kilismile_contact_form',
            'newsletter': 'kilismile_newsletter_signup',
            'volunteer': 'kilismile_volunteer_form'
        };
        
        // AJAX request
        $.ajax({
            url: kilismileEmail.ajaxUrl,
            type: 'POST',
            data: {
                action: actionMap[formType],
                nonce: kilismileEmail.nonce,
                ...Object.fromEntries(new URLSearchParams(formData))
            },
            success: function(response) {
                if (response.success) {
                    $message.addClass('success').html('<i class="fas fa-check-circle"></i> ' + response.data);
                    $form[0].reset();
                    
                    // Track successful submission
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'form_submit', {
                            'event_category': 'engagement',
                            'event_label': formType,
                            'value': 1
                        });
                    }
                } else {
                    $message.addClass('error').html('<i class="fas fa-exclamation-triangle"></i> ' + response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Form submission error:', error);
                $message.addClass('error').html('<i class="fas fa-exclamation-triangle"></i> ' + kilismileEmail.strings.error);
            },
            complete: function() {
                // Reset button state
                $submitBtn.prop('disabled', false).text(originalText);
                
                // Auto-hide success messages after 5 seconds
                setTimeout(function() {
                    $message.filter('.success').fadeOut();
                }, 5000);
            }
        });
    }
    
    // Form validation
    function validateForm($form, formType) {
        let isValid = true;
        const errors = [];
        
        // Clear previous validation styles
        $form.find('.form-group').removeClass('has-error');
        
        // Required fields validation
        $form.find('input[required], textarea[required], select[required]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            
            if (!value) {
                $field.closest('.form-group').addClass('has-error');
                errors.push($field.prev('label').text() + ' is required');
                isValid = false;
            }
        });
        
        // Email validation
        const $emailField = $form.find('input[type="email"]');
        if ($emailField.length && $emailField.val()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test($emailField.val())) {
                $emailField.closest('.form-group').addClass('has-error');
                errors.push('Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Phone validation (if present and filled)
        const $phoneField = $form.find('input[type="tel"]');
        if ($phoneField.length && $phoneField.val()) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (!phoneRegex.test($phoneField.val().replace(/[\s\-\(\)]/g, ''))) {
                $phoneField.closest('.form-group').addClass('has-error');
                errors.push('Please enter a valid phone number');
                isValid = false;
            }
        }
        
        // Show validation errors
        if (!isValid) {
            const $message = $form.find('.form-message');
            $message.removeClass('success').addClass('error')
                .html('<i class="fas fa-exclamation-triangle"></i> ' + errors.join('<br>'));
        }
        
        return isValid;
    }
    
    // Form enhancement functions
    function enhanceFormFields() {
        // Add floating labels effect
        $('.kilismile-contact-form input, .kilismile-newsletter-form input, .kilismile-volunteer-form input, .kilismile-volunteer-form textarea, .kilismile-volunteer-form select').on('focus blur', function() {
            const $field = $(this);
            const $group = $field.closest('.form-group');
            
            if ($field.val() || $field.is(':focus')) {
                $group.addClass('focused');
            } else {
                $group.removeClass('focused');
            }
        });
        
        // Character counter for textareas
        $('textarea[maxlength]').each(function() {
            const $textarea = $(this);
            const maxLength = $textarea.attr('maxlength');
            const $counter = $('<div class="char-counter"></div>');
            
            $textarea.after($counter);
            
            $textarea.on('input', function() {
                const remaining = maxLength - $(this).val().length;
                $counter.text(remaining + ' characters remaining');
                
                if (remaining < 50) {
                    $counter.addClass('warning');
                } else {
                    $counter.removeClass('warning');
                }
            }).trigger('input');
        });
        
        // Auto-resize textareas
        $('textarea').on('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
    
    // Real-time validation
    function setupRealTimeValidation() {
        $('input[type="email"]').on('blur', function() {
            const $field = $(this);
            const email = $field.val();
            
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                $field.closest('.form-group').addClass('has-error');
            } else {
                $field.closest('.form-group').removeClass('has-error');
            }
        });
        
        $('input[required], textarea[required]').on('blur', function() {
            const $field = $(this);
            
            if (!$field.val().trim()) {
                $field.closest('.form-group').addClass('has-error');
            } else {
                $field.closest('.form-group').removeClass('has-error');
            }
        });
    }
    
    // Form submission rate limiting
    const submissionTimes = {};
    
    function isSubmissionAllowed(formType) {
        const now = Date.now();
        const lastSubmission = submissionTimes[formType] || 0;
        const timeDiff = now - lastSubmission;
        
        // Allow submission if more than 30 seconds have passed
        if (timeDiff > 30000) {
            submissionTimes[formType] = now;
            return true;
        }
        
        return false;
    }
    
    // Initialize when document is ready
    $(document).ready(function() {
        
        // Enhance form fields
        enhanceFormFields();
        setupRealTimeValidation();
        
        // Handle form submissions
        $(document).on('submit', '[data-form-type]', function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const formType = $form.data('form-type');
            
            // Rate limiting check
            if (!isSubmissionAllowed(formType)) {
                const $message = $form.find('.form-message');
                $message.removeClass('success').addClass('error')
                    .html('<i class="fas fa-clock"></i> Please wait before submitting again.');
                return;
            }
            
            // Validate form
            if (validateForm($form, formType)) {
                handleFormSubmission(this, formType);
            }
        });
        
        // Newsletter popup functionality
        function showNewsletterPopup() {
            if (localStorage.getItem('kilismile_newsletter_dismissed')) {
                return;
            }

            if (typeof kilismile_newsletter_form !== 'function') {
                return;
            }
            
            const popup = `
                <div id="newsletter-popup" class="newsletter-popup">
                    <div class="popup-content">
                        <button class="popup-close">&times;</button>
                        <h3>Stay Updated!</h3>
                        <p>Get the latest updates on our health programs and community impact.</p>
                        ${kilismile_newsletter_form({class: 'popup-newsletter-form', submit_text: 'Subscribe Now'})}
                    </div>
                    <div class="popup-backdrop"></div>
                </div>
            `;
            
            $('body').append(popup);
            
            // Show popup after delay
            setTimeout(function() {
                $('#newsletter-popup').addClass('show');
            }, 100);
        }
        
        // Close newsletter popup
        $(document).on('click', '.popup-close, .popup-backdrop', function() {
            $('#newsletter-popup').removeClass('show');
            localStorage.setItem('kilismile_newsletter_dismissed', 'true');
            
            setTimeout(function() {
                $('#newsletter-popup').remove();
            }, 300);
        });
        
        // Show newsletter popup on page load (after 10 seconds)
        if (window.location.pathname === '/' || window.location.pathname.includes('home')) {
            setTimeout(showNewsletterPopup, 10000);
        }
        
        // Contact form modal functionality
        $('.open-contact-modal').on('click', function(e) {
            e.preventDefault();

            if (typeof kilismile_contact_form !== 'function') {
                return;
            }
            
            const modal = `
                <div id="contact-modal" class="contact-modal">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Contact Us</h3>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            ${kilismile_contact_form()}
                        </div>
                    </div>
                    <div class="modal-backdrop"></div>
                </div>
            `;
            
            $('body').append(modal);
            $('#contact-modal').addClass('show');
        });
        
        // Close contact modal
        $(document).on('click', '.modal-close, .modal-backdrop', function() {
            $('#contact-modal').removeClass('show');
            
            setTimeout(function() {
                $('#contact-modal').remove();
            }, 300);
        });
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 600);
            }
        });
        
        // Form analytics tracking
        function trackFormInteraction(formType, action) {
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    'event_category': 'form_interaction',
                    'event_label': formType
                });
            }
        }
        
        // Track form focus
        $('[data-form-type] input, [data-form-type] textarea').on('focus', function() {
            const formType = $(this).closest('[data-form-type]').data('form-type');
            trackFormInteraction(formType, 'form_start');
        });
        
        // Track form abandonment
        $(window).on('beforeunload', function() {
            $('[data-form-type]').each(function() {
                const $form = $(this);
                const formType = $form.data('form-type');
                const hasContent = $form.find('input, textarea').filter(function() {
                    return $(this).val().trim() !== '';
                }).length > 0;
                
                if (hasContent) {
                    trackFormInteraction(formType, 'form_abandon');
                }
            });
        });
    });
    
})(jQuery);

// CSS for enhanced form styling (to be included in main stylesheet)
const formStyles = `
    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }
    
    .form-group.has-error input,
    .form-group.has-error textarea,
    .form-group.has-error select {
        border-color: #e74c3c;
        box-shadow: 0 0 0 2px rgba(231, 76, 60, 0.2);
    }
    
    .form-group.focused label {
        color: #3498db;
        transform: translateY(-20px) scale(0.9);
    }
    
    .form-message {
        margin-top: 1rem;
        padding: 0.75rem;
        border-radius: 4px;
        display: none;
    }
    
    .form-message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        display: block;
    }
    
    .form-message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        display: block;
    }
    
    .char-counter {
        font-size: 0.8rem;
        color: #666;
        text-align: right;
        margin-top: 0.25rem;
    }
    
    .char-counter.warning {
        color: #e74c3c;
    }
    
    .newsletter-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .newsletter-popup.show {
        opacity: 1;
        visibility: visible;
    }
    
    .newsletter-popup .popup-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    
    .newsletter-popup .popup-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 2rem;
        border-radius: 8px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .popup-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }
    
    .contact-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .contact-modal.show {
        opacity: 1;
        visibility: visible;
    }
    
    .contact-modal .modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
    }
    
    .contact-modal .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-close {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
    }
`;

// Inject styles if not already present
if (!document.getElementById('kilismile-form-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'kilismile-form-styles';
    styleSheet.textContent = formStyles;
    document.head.appendChild(styleSheet);
}


