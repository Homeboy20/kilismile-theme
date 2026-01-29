/**
 * Enhanced Donation Form Validation
 * Real-time field validation and improved UX
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

(function($) {
    'use strict';
    
    const DonationValidator = {
        
        /**
         * Initialize validation
         */
        init: function() {
            this.setupFieldValidation();
            this.setupPhoneFormatting();
            this.setupAutoSave();
            this.setupConditionalFields();
            this.setupAddressValidation();
        },
        
        /**
         * Setup real-time field validation
         */
        setupFieldValidation: function() {
            const fields = {
                'email': this.validateEmail,
                'phone': this.validatePhone,
                'first_name': this.validateName,
                'last_name': this.validateName,
                'postal_code': this.validatePostalCode,
                'tribute_notification_email': this.validateEmail
            };
            
            Object.keys(fields).forEach(fieldName => {
                const $field = $(`#${fieldName}, [name="${fieldName}"]`);
                if ($field.length) {
                    $field.on('blur', function() {
                        const validator = fields[fieldName];
                        validator.call(DonationValidator, $(this));
                    });
                    
                    // Real-time validation for email
                    if (fieldName === 'email' || fieldName === 'tribute_notification_email') {
                        $field.on('input', debounce(function() {
                            const validator = fields[fieldName];
                            validator.call(DonationValidator, $(this));
                        }, 500));
                    }
                }
            });
        },
        
        /**
         * Validate email address
         */
        validateEmail: function($field) {
            const email = $field.val().trim();
            const $errorContainer = $field.closest('.form-group, .field-group').find('.field-error');
            
            if (!email) {
                this.clearError($field);
                return true;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.showFieldError($field, 'Please enter a valid email address');
                return false;
            }
            
            // Check for disposable emails (optional)
            const disposableDomains = ['tempmail.com', '10minutemail.com', 'guerrillamail.com'];
            const domain = email.split('@')[1];
            if (disposableDomains.includes(domain.toLowerCase())) {
                this.showFieldError($field, 'Please use a valid email address. Disposable emails are not allowed.');
                return false;
            }
            
            this.clearError($field);
            this.showFieldSuccess($field);
            return true;
        },
        
        /**
         * Validate phone number
         */
        validatePhone: function($field) {
            let phone = $field.val().trim();
            const $errorContainer = $field.closest('.form-group, .field-group').find('.field-error');
            
            if (!phone) {
                this.clearError($field);
                return true; // Phone is optional
            }
            
            // Remove spaces and dashes
            phone = phone.replace(/[\s\-]/g, '');
            
            // Tanzania phone validation
            let isValid = false;
            let errorMessage = '';
            
            if (phone.startsWith('+255')) {
                // International format: +255XXXXXXXXX (9 digits)
                if (/^\+255\d{9}$/.test(phone)) {
                    isValid = true;
                } else {
                    errorMessage = 'Tanzania phone must be: +255 followed by 9 digits';
                }
            } else if (phone.startsWith('0')) {
                // Local format: 0XXXXXXXXX (10 digits)
                if (/^0\d{9}$/.test(phone)) {
                    isValid = true;
                    // Auto-format to international
                    const formatted = '+255' + phone.substring(1);
                    $field.val(formatted);
                } else {
                    errorMessage = 'Local phone must be 10 digits starting with 0';
                }
            } else if (phone.startsWith('255')) {
                // Format without +: 255XXXXXXXXX
                if (/^255\d{9}$/.test(phone)) {
                    isValid = true;
                    $field.val('+' + phone);
                } else {
                    errorMessage = 'Invalid phone number format';
                }
            } else {
                errorMessage = 'Please include country code (+255) or use local format (0XXXXXXXXX)';
            }
            
            if (!isValid) {
                this.showFieldError($field, errorMessage);
                return false;
            }
            
            this.clearError($field);
            this.showFieldSuccess($field);
            return true;
        },
        
        /**
         * Validate name
         */
        validateName: function($field) {
            const name = $field.val().trim();
            
            if (!name) {
                this.clearError($field);
                return true; // Will be validated on submit
            }
            
            if (name.length < 2) {
                this.showFieldError($field, 'Name must be at least 2 characters');
                return false;
            }
            
            if (!/^[a-zA-Z\s\-'\.]+$/.test(name)) {
                this.showFieldError($field, 'Name can only contain letters, spaces, hyphens, and apostrophes');
                return false;
            }
            
            this.clearError($field);
            this.showFieldSuccess($field);
            return true;
        },
        
        /**
         * Validate postal code
         */
        validatePostalCode: function($field) {
            const postalCode = $field.val().trim();
            
            if (!postalCode) {
                this.clearError($field);
                return true; // Optional field
            }
            
            if (!/^[A-Z0-9\s\-]{3,20}$/i.test(postalCode)) {
                this.showFieldError($field, 'Invalid postal code format');
                return false;
            }
            
            this.clearError($field);
            return true;
        },
        
        /**
         * Setup phone number formatting
         */
        setupPhoneFormatting: function() {
            $('input[type="tel"], input[name="phone"]').on('input', function() {
                let value = $(this).val().replace(/\D/g, '');
                
                // Auto-add +255 if user starts typing 0
                if (value.startsWith('0') && value.length <= 10) {
                    // Keep as is for local format
                } else if (value.startsWith('255') && value.length === 12) {
                    $(this).val('+' + value);
                } else if (value.length > 0 && !value.startsWith('255') && !value.startsWith('0')) {
                    // Assume Tanzania if no country code
                    if (value.length <= 9) {
                        $(this).val('+255' + value);
                    }
                }
            });
        },
        
        /**
         * Setup auto-save draft
         */
        setupAutoSave: function() {
            let saveTimeout;
            const formFields = $('input, select, textarea').filter(function() {
                return $(this).closest('.kilismile-donation-form').length > 0;
            });
            
            formFields.on('change input', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(function() {
                    DonationValidator.saveDraft();
                }, 2000); // Save 2 seconds after last change
            });
        },
        
        /**
         * Save draft donation
         */
        saveDraft: function() {
            const formData = new FormData();
            const form = $('.kilismile-donation-form').first();
            
            // Collect all form data
            form.find('input, select, textarea').each(function() {
                const $field = $(this);
                const name = $field.attr('name');
                const type = $field.attr('type');
                
                if (name && type !== 'file') {
                    if (type === 'checkbox' || type === 'radio') {
                        if ($field.is(':checked')) {
                            formData.append(name, $field.val());
                        }
                    } else {
                        formData.append(name, $field.val());
                    }
                }
            });
            
            formData.append('action', 'kilismile_save_draft_donation');
            formData.append('nonce', kilismile_donation_js?.nonce || '');
            formData.append('session_id', this.getSessionId());
            
            $.ajax({
                url: kilismile_donation_js?.ajax_url || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show subtle save indicator
                        DonationValidator.showSaveIndicator();
                    }
                }
            });
        },
        
        /**
         * Get or create session ID
         */
        getSessionId: function() {
            let sessionId = sessionStorage.getItem('kilismile_donation_session');
            if (!sessionId) {
                sessionId = 'ks_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('kilismile_donation_session', sessionId);
            }
            return sessionId;
        },
        
        /**
         * Show save indicator
         */
        showSaveIndicator: function() {
            let $indicator = $('.draft-save-indicator');
            if (!$indicator.length) {
                $indicator = $('<div class="draft-save-indicator" style="position: fixed; bottom: 20px; right: 20px; background: #28a745; color: white; padding: 8px 15px; border-radius: 5px; font-size: 0.85rem; z-index: 9999; opacity: 0; transition: opacity 0.3s;"></div>');
                $('body').append($indicator);
            }
            
            $indicator.text('Draft saved').css('opacity', 1);
            setTimeout(function() {
                $indicator.css('opacity', 0);
            }, 2000);
        },
        
        /**
         * Setup conditional fields
         */
        setupConditionalFields: function() {
            // Tribute fields
            $('input[name="is_tribute"]').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.tribute-fields, [data-conditional="is_tribute"]').toggle(isChecked);
                
                if (!isChecked) {
                    $('input[name="tribute_name"], textarea[name="tribute_message"]').val('');
                }
            });
            
            // Tribute notification fields
            $('input[name="notify_tribute"]').on('change', function() {
                const isChecked = $(this).is(':checked');
                $('.tribute-notification-fields, [data-conditional="notify_tribute"]').toggle(isChecked);
            });
            
            // Address fields
            $('input[name="address_line1"]').on('input', function() {
                const hasAddress = $(this).val().trim().length > 0;
                $('.address-required-fields').toggle(hasAddress);
            });
        },
        
        /**
         * Setup address validation
         */
        setupAddressValidation: function() {
            $('input[name="address_line1"]').on('blur', function() {
                const hasAddress = $(this).val().trim().length > 0;
                const city = $('input[name="city"]').val().trim();
                
                if (hasAddress && !city) {
                    DonationValidator.showFieldError($('input[name="city"]'), 'City is required when providing an address');
                }
            });
        },
        
        /**
         * Show field error
         */
        showFieldError: function($field, message) {
            this.clearError($field);
            
            const $error = $('<span class="field-error" style="color: #dc3545; font-size: 0.875rem; margin-top: 5px; display: block;"></span>');
            $error.text(message);
            
            $field.closest('.form-group, .field-group, .form-field').append($error);
            $field.addClass('error').css('border-color', '#dc3545');
        },
        
        /**
         * Show field success
         */
        showFieldSuccess: function($field) {
            $field.removeClass('error').css('border-color', '#28a745');
        },
        
        /**
         * Clear field error
         */
        clearError: function($field) {
            $field.closest('.form-group, .field-group, .form-field').find('.field-error').remove();
            $field.removeClass('error');
        },
        
        /**
         * Validate entire form
         */
        validateForm: function() {
            let isValid = true;
            const form = $('.kilismile-donation-form').first();
            
            // Required fields
            form.find('[required]').each(function() {
                const $field = $(this);
                const value = $field.val().trim();
                
                if (!value) {
                    DonationValidator.showFieldError($field, 'This field is required');
                    isValid = false;
                }
            });
            
            // Validate email
            const $email = form.find('input[type="email"], input[name="email"]');
            if ($email.length && $email.val()) {
                if (!DonationValidator.validateEmail($email)) {
                    isValid = false;
                }
            }
            
            // Validate phone if provided
            const $phone = form.find('input[type="tel"], input[name="phone"]');
            if ($phone.length && $phone.val()) {
                if (!DonationValidator.validatePhone($phone)) {
                    isValid = false;
                }
            }
            
            // Validate tribute fields if tribute is checked
            if (form.find('input[name="is_tribute"]').is(':checked')) {
                const $tributeName = form.find('input[name="tribute_name"]');
                if (!$tributeName.val().trim()) {
                    DonationValidator.showFieldError($tributeName, 'Tribute name is required');
                    isValid = false;
                }
            }
            
            return isValid;
        }
    };
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Initialize on document ready
    $(document).ready(function() {
        DonationValidator.init();
        
        // Add validation to form submission
        $('form.kilismile-donation-form').on('submit', function(e) {
            if (!DonationValidator.validateForm()) {
                e.preventDefault();
                // Scroll to first error
                const $firstError = $(this).find('.field-error').first();
                if ($firstError.length) {
                    $('html, body').animate({
                        scrollTop: $firstError.offset().top - 100
                    }, 500);
                }
                return false;
            }
        });
    });
    
    // Export for global access
    window.KiliSmileDonationValidator = DonationValidator;
    
})(jQuery);
