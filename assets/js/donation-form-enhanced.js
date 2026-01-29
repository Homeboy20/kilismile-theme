/**
 * Enhanced Donation Form JavaScript
 * 
 * Provides advanced features like real-time validation,
 * currency conversion, auto-save, and analytics tracking.
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

(function($) {
    'use strict';

    // Enhanced Payment System Object
    window.KiliSmilePaymentsEnhanced = {
        
        // Configuration
        config: {
            currencies: {
                TZS: { symbol: 'TZS ', precision: 0 },
                USD: { symbol: '$', precision: 2 }
            },
            autoSaveInterval: 30000, // 30 seconds
            validationDelay: 500, // 0.5 seconds
            conversionApiUrl: '/wp-json/kilismile/v1/currency-convert',
            analyticsEnabled: true
        },
        
        // State management
        state: {
            currentStep: 1,
            totalSteps: 3,
            formData: {},
            selectedCurrency: 'TZS',
            selectedAmount: 0,
            isConverting: false,
            autoSaveTimer: null,
            validationTimer: null
        },
        
        // Initialize enhanced payment system
        init: function() {
            this.bindEvents();
            this.initializeFormPersistence();
            this.initializeCurrencyConverter();
            this.initializeAnalytics();
            this.initializeAccessibility();
            this.setupProgressIndicator();
            this.loadSavedFormData();
        },
        
        // Bind enhanced event handlers
        bindEvents: function() {
            // Amount selection with real-time conversion
            $(document).on('click', '.amount-card', this.handleAmountSelection.bind(this));
            $(document).on('input', '.amount-input', this.debounce(this.handleCustomAmount.bind(this), this.config.validationDelay));
            
            // Currency switching with conversion
            $(document).on('click', '.currency-btn', this.handleCurrencySwitch.bind(this));
            
            // Step navigation with validation
            $(document).on('click', '.btn-next-step', this.handleNextStep.bind(this));
            $(document).on('click', '.btn-prev-step', this.handlePrevStep.bind(this));
            
            // Enhanced form validation
            $(document).on('blur', 'input[required]', this.validateField.bind(this));
            $(document).on('input', 'input, select', this.handleFieldChange.bind(this));
            
            // Payment method selection with smart filtering
            $(document).on('change', '.payment-method-radio', this.handlePaymentMethodChange.bind(this));
            
            // Form submission with analytics
            $(document).on('submit', '.kilismile-donation-form', this.handleFormSubmit.bind(this));
            
            // Auto-save functionality
            $(document).on('input change', 'input, select, textarea', this.scheduleAutoSave.bind(this));
            
            // Accessibility enhancements
            $(document).on('keydown', '.amount-card, .payment-option', this.handleKeyboardNavigation.bind(this));
            
            // Window events
            $(window).on('beforeunload', this.handlePageUnload.bind(this));
            $(window).on('online', this.handleOnline.bind(this));
            $(window).on('offline', this.handleOffline.bind(this));
        },
        
        // Enhanced amount selection with conversion
        handleAmountSelection: function(e) {
            e.preventDefault();
            
            const $card = $(e.currentTarget);
            const amount = parseFloat($card.data('amount'));
            
            // Visual feedback
            $('.amount-card').removeClass('selected');
            $card.addClass('selected');
            
            // Update state
            this.state.selectedAmount = amount;
            this.state.formData.amount = amount;
            
            // Hide custom input
            $('.custom-amount-input').slideUp(200);
            
            // Convert and display amount in both currencies
            this.displayAmountConversion(amount);
            
            // Update payment methods availability
            this.updatePaymentMethodsForAmount(amount);
            
            // Track analytics
            this.trackEvent('amount_selected', {
                amount: amount,
                currency: this.state.selectedCurrency,
                method: 'preset'
            });
            
            // Auto-save
            this.scheduleAutoSave();
        },
        
        // Handle custom amount input with real-time validation
        handleCustomAmount: function(e) {
            const amount = parseFloat($(e.target).val()) || 0;
            
            // Clear preset selection
            $('.amount-card').removeClass('selected');
            
            // Update state
            this.state.selectedAmount = amount;
            this.state.formData.amount = amount;
            
            // Validate amount
            this.validateAmount($(e.target), amount);
            
            if (amount > 0) {
                // Convert and display
                this.displayAmountConversion(amount);
                this.updatePaymentMethodsForAmount(amount);
                
                // Track analytics
                this.trackEvent('amount_entered', {
                    amount: amount,
                    currency: this.state.selectedCurrency,
                    method: 'custom'
                });
            }
            
            this.scheduleAutoSave();
        },
        
        // Currency switching with conversion
        handleCurrencySwitch: function(e) {
            e.preventDefault();
            
            const $btn = $(e.currentTarget);
            const newCurrency = $btn.data('currency');
            const currentAmount = this.state.selectedAmount;
            
            if (newCurrency === this.state.selectedCurrency) return;
            
            // Visual feedback
            $('.currency-btn').removeClass('active');
            $btn.addClass('active');
            
            // Show loading state
            $btn.html('<i class="fas fa-spinner fa-spin"></i> ' + newCurrency);
            
            // Convert current amount if exists
            if (currentAmount > 0) {
                this.convertAmount(currentAmount, this.state.selectedCurrency, newCurrency)
                    .then(convertedAmount => {
                        this.state.selectedAmount = convertedAmount;
                        this.state.formData.amount = convertedAmount;
                        
                        // Update amount input if visible
                        const $amountInput = $('.amount-input:visible');
                        if ($amountInput.length) {
                            $amountInput.val(convertedAmount);
                        }
                        
                        // Update preset amounts
                        this.updatePresetAmounts(newCurrency);
                        
                        // Update display
                        this.displayAmountConversion(convertedAmount);
                    })
                    .finally(() => {
                        $btn.html(newCurrency);
                    });
            } else {
                // Just update preset amounts
                this.updatePresetAmounts(newCurrency);
                $btn.html(newCurrency);
            }
            
            // Update state
            this.state.selectedCurrency = newCurrency;
            this.state.formData.currency = newCurrency;
            
            // Track analytics
            this.trackEvent('currency_changed', {
                from: this.state.selectedCurrency,
                to: newCurrency,
                amount: currentAmount
            });
            
            this.scheduleAutoSave();
        },
        
        // Real-time currency conversion
        convertAmount: function(amount, fromCurrency, toCurrency) {
            if (fromCurrency === toCurrency) {
                return Promise.resolve(amount);
            }
            
            this.state.isConverting = true;
            
            return fetch(this.config.conversionApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': kilismile_payment_ajax.nonce
                },
                body: JSON.stringify({
                    amount: amount,
                    from: fromCurrency,
                    to: toCurrency
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    return data.converted_amount;
                }
                throw new Error(data.message || 'Conversion failed');
            })
            .catch(error => {
                console.error('Currency conversion error:', error);
                this.showNotification('Currency conversion temporarily unavailable', 'warning');
                return amount; // Return original amount as fallback
            })
            .finally(() => {
                this.state.isConverting = false;
            });
        },
        
        // Display amount in both currencies
        displayAmountConversion: function(amount) {
            const currentCurrency = this.state.selectedCurrency;
            const otherCurrency = currentCurrency === 'TZS' ? 'USD' : 'TZS';
            
            // Update primary display
            const primaryFormatted = this.formatCurrency(amount, currentCurrency);
            $('.selected-amount-primary').text(primaryFormatted);
            
            // Convert and show secondary amount
            this.convertAmount(amount, currentCurrency, otherCurrency)
                .then(convertedAmount => {
                    const secondaryFormatted = this.formatCurrency(convertedAmount, otherCurrency);
                    $('.selected-amount-secondary').text(`≈ ${secondaryFormatted}`).show();
                });
        },
        
        // Update preset amounts for currency
        updatePresetAmounts: function(currency) {
            const presets = {
                TZS: [10000, 25000, 50000, 100000, 250000],
                USD: [5, 10, 25, 50, 100]
            };
            
            const amounts = presets[currency] || presets.TZS;
            
            $('.amount-card[data-amount!="custom"]').each((index, element) => {
                if (amounts[index]) {
                    const $card = $(element);
                    $card.attr('data-amount', amounts[index]);
                    $card.find('.amount-value').text(this.formatCurrency(amounts[index], currency));
                }
            });
        },
        
        // Enhanced step navigation with validation
        handleNextStep: function(e) {
            e.preventDefault();
            
            const currentStep = this.state.currentStep;
            
            // Validate current step
            if (!this.validateCurrentStep()) {
                this.trackEvent('step_validation_failed', { step: currentStep });
                return;
            }
            
            // Show loading state
            const $btn = $(e.currentTarget);
            const originalText = $btn.html();
            $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...').prop('disabled', true);
            
            // Simulate processing delay for better UX
            setTimeout(() => {
                if (currentStep < this.state.totalSteps) {
                    this.state.currentStep++;
                    this.showStep(this.state.currentStep);
                    this.updateProgressIndicator();
                    
                    // Track analytics
                    this.trackEvent('step_completed', { 
                        step: currentStep,
                        next_step: this.state.currentStep 
                    });
                }
                
                $btn.html(originalText).prop('disabled', false);
            }, 500);
        },
        
        // Enhanced field validation with visual feedback
        validateField: function(e) {
            const $field = $(e.target);
            const value = $field.val().trim();
            const fieldName = $field.attr('name');
            
            let isValid = true;
            let message = '';
            
            // Required field validation
            if ($field.prop('required') && !value) {
                isValid = false;
                message = 'This field is required';
            }
            // Email validation
            else if (fieldName === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    message = 'Please enter a valid email address';
                }
            }
            // Phone validation
            else if (fieldName === 'phone' && value) {
                const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
                if (!phoneRegex.test(value)) {
                    isValid = false;
                    message = 'Please enter a valid phone number';
                }
            }
            
            this.showFieldValidation($field, isValid, message);
            return isValid;
        },
        
        // Enhanced form persistence
        initializeFormPersistence: function() {
            // Check if browser supports localStorage
            if (typeof Storage !== 'undefined') {
                this.loadSavedFormData();
            }
        },
        
        // Auto-save form data
        scheduleAutoSave: function() {
            // Clear existing timer
            if (this.state.autoSaveTimer) {
                clearTimeout(this.state.autoSaveTimer);
            }
            
            // Schedule new auto-save
            this.state.autoSaveTimer = setTimeout(() => {
                this.saveFormData();
            }, this.config.autoSaveInterval);
        },
        
        // Save form data to localStorage
        saveFormData: function() {
            if (typeof Storage === 'undefined') return;
            
            try {
                const formData = this.gatherFormData();
                localStorage.setItem('kilismile_donation_form', JSON.stringify({
                    data: formData,
                    timestamp: Date.now(),
                    step: this.state.currentStep
                }));
                
                // Show subtle save indicator
                this.showSaveIndicator();
                
            } catch (error) {
                console.error('Failed to save form data:', error);
            }
        },
        
        // Load saved form data
        loadSavedFormData: function() {
            if (typeof Storage === 'undefined') return;
            
            try {
                const saved = localStorage.getItem('kilismile_donation_form');
                if (saved) {
                    const data = JSON.parse(saved);
                    
                    // Check if data is recent (within 24 hours)
                    const age = Date.now() - data.timestamp;
                    if (age < 24 * 60 * 60 * 1000) {
                        this.restoreFormData(data);
                        this.showRestoredDataNotification();
                    } else {
                        // Clear old data
                        localStorage.removeItem('kilismile_donation_form');
                    }
                }
            } catch (error) {
                console.error('Failed to load saved form data:', error);
            }
        },
        
        // Analytics tracking
        initializeAnalytics: function() {
            if (!this.config.analyticsEnabled) return;
            
            // Track form view
            this.trackEvent('donation_form_viewed', {
                timestamp: Date.now(),
                user_agent: navigator.userAgent,
                screen_resolution: `${screen.width}x${screen.height}`
            });
        },
        
        // Track custom events
        trackEvent: function(eventName, properties = {}) {
            if (!this.config.analyticsEnabled) return;
            
            const eventData = {
                event: eventName,
                properties: {
                    ...properties,
                    timestamp: Date.now(),
                    session_id: this.getSessionId(),
                    form_version: '2.0.0'
                }
            };
            
            // Send to Google Analytics if available
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, properties);
            }
            
            // Send to custom analytics endpoint
            this.sendAnalytics(eventData);
        },
        
        // Enhanced accessibility features
        initializeAccessibility: function() {
            // Add ARIA labels
            $('.amount-card').attr('role', 'button').attr('tabindex', '0');
            $('.payment-option').attr('role', 'button').attr('tabindex', '0');
            
            // Add keyboard navigation
            this.setupKeyboardNavigation();
            
            // Add screen reader announcements
            this.setupScreenReaderAnnouncements();
        },
        
        // Keyboard navigation support
        handleKeyboardNavigation: function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                $(e.currentTarget).click();
            }
        },
        
        // Enhanced error handling and recovery
        handleError: function(error, context = {}) {
            console.error('Payment form error:', error);
            
            // Track error
            this.trackEvent('form_error', {
                error: error.message || error,
                context: context,
                stack: error.stack
            });
            
            // Show user-friendly error message
            this.showNotification('An error occurred. Please try again or contact support.', 'error');
            
            // Attempt recovery
            this.attemptErrorRecovery(error, context);
        },
        
        // Network status handling
        handleOffline: function() {
            this.showNotification('You appear to be offline. Your progress will be saved locally.', 'warning');
            $('.online-only').prop('disabled', true);
        },
        
        handleOnline: function() {
            this.showNotification('Connection restored.', 'success');
            $('.online-only').prop('disabled', false);
            
            // Sync any pending data
            this.syncPendingData();
        },
        
        // Utility functions
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },
        
        formatCurrency: function(amount, currency) {
            const config = this.config.currencies[currency] || this.config.currencies.TZS;
            return config.symbol + parseFloat(amount).toLocaleString('en-US', {
                minimumFractionDigits: config.precision,
                maximumFractionDigits: config.precision
            });
        },
        
        getSessionId: function() {
            let sessionId = sessionStorage.getItem('kilismile_session_id');
            if (!sessionId) {
                sessionId = 'ks_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('kilismile_session_id', sessionId);
            }
            return sessionId;
        },
        
        // Show notification with enhanced styling
        showNotification: function(message, type = 'info', duration = 5000) {
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            
            const notification = $(`
                <div class="enhanced-notification ${type}" style="
                    position: fixed; 
                    top: 20px; 
                    right: 20px; 
                    z-index: 10000;
                    background: white;
                    border-left: 4px solid var(--${type}-color, #007bff);
                    border-radius: 8px;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
                    padding: 16px 20px;
                    max-width: 400px;
                    transform: translateX(100%);
                    transition: transform 0.3s ease;
                ">
                    <div style="display: flex; align-items: center;">
                        <i class="fas ${icons[type]}" style="margin-right: 12px; color: var(--${type}-color, #007bff);"></i>
                        <span>${message}</span>
                        <button type="button" class="close-notification" style="
                            background: none; 
                            border: none; 
                            margin-left: auto; 
                            padding: 0 0 0 10px;
                            color: #999;
                            cursor: pointer;
                        ">&times;</button>
                    </div>
                </div>
            `);
            
            $('body').append(notification);
            
            // Slide in
            setTimeout(() => notification.css('transform', 'translateX(0)'), 100);
            
            // Auto hide
            const hideTimeout = setTimeout(() => {
                notification.css('transform', 'translateX(100%)');
                setTimeout(() => notification.remove(), 300);
            }, duration);
            
            // Manual close
            notification.find('.close-notification').on('click', () => {
                clearTimeout(hideTimeout);
                notification.css('transform', 'translateX(100%)');
                setTimeout(() => notification.remove(), 300);
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        if ($('.kilismile-donation-form').length) {
            KiliSmilePaymentsEnhanced.init();
        }
    });

})(jQuery);

// CSS Variables for theming
document.documentElement.style.setProperty('--success-color', '#28a745');
document.documentElement.style.setProperty('--error-color', '#dc3545');
document.documentElement.style.setProperty('--warning-color', '#ffc107');
document.documentElement.style.setProperty('--info-color', '#17a2b8');


