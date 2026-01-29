/**
 * KiliSmile Payments - Frontend JavaScript
 */

(function($) {
    'use strict';

    // Main payment form handler
    const KiliSmilePayments = {
        
        // Configuration
        config: {
            form: '.kilismile-payment-form',
            gatewaySelector: 'input[name="gateway"]',
            amountField: 'input[name="amount"]',
            recurringCheckbox: 'input[name="recurring"]',
            submitButton: '.payment-submit button',
            messageContainer: '.payment-message'
        },

        // Initialize
        init: function() {
            this.bindEvents();
            this.initializeForm();
            this.loadStoredData();
        },

        // Bind all events
        bindEvents: function() {
            $(document).on('change', this.config.gatewaySelector, this.handleGatewayChange.bind(this));
            $(document).on('click', '.amount-preset', this.handleAmountPreset.bind(this));
            $(document).on('input', this.config.amountField, this.handleAmountChange.bind(this));
            $(document).on('change', this.config.recurringCheckbox, this.handleRecurringChange.bind(this));
            $(document).on('submit', this.config.form, this.handleFormSubmit.bind(this));
            $(document).on('input', 'input, select, textarea', this.clearFieldError.bind(this));
            $(document).on('click', '.currency-converter', this.showCurrencyConverter.bind(this));
        },

        // Initialize form
        initializeForm: function() {
            this.updatePaymentSummary();
            this.validateGatewayRequirements();
            this.setupProgressIndicator();
        },

        // Handle gateway selection change
        handleGatewayChange: function(e) {
            const selectedGateway = $(e.target).val();
            this.updateGatewayRequirements(selectedGateway);
            this.updatePaymentSummary();
            this.validateGatewayRequirements();
            
            // Store selection
            localStorage.setItem('kilismile_selected_gateway', selectedGateway);
        },

        // Handle amount preset selection
        handleAmountPreset: function(e) {
            e.preventDefault();
            const amount = $(e.target).data('amount');
            
            // Remove previous selection
            $('.amount-preset').removeClass('selected');
            $(e.target).addClass('selected');
            
            // Update amount field
            $(this.config.amountField).val(amount).trigger('input');
            
            // Convert currency if needed
            this.convertCurrency(amount);
        },

        // Handle amount change
        handleAmountChange: function(e) {
            const amount = parseFloat($(e.target).val()) || 0;
            
            // Remove preset selections if custom amount
            $('.amount-preset').removeClass('selected');
            
            // Update summary
            this.updatePaymentSummary();
            
            // Validate minimum amount
            this.validateAmount(amount);
        },

        // Handle recurring change
        handleRecurringChange: function(e) {
            const isRecurring = $(e.target).is(':checked');
            const frequencyContainer = $('.recurring-frequency');
            
            if (isRecurring) {
                frequencyContainer.addClass('show');
            } else {
                frequencyContainer.removeClass('show');
            }
            
            this.updatePaymentSummary();
        },

        // Handle form submission
        handleFormSubmit: function(e) {
            e.preventDefault();
            
            if (this.validateForm()) {
                this.processPayment();
            }
        },

        // Update gateway requirements
        updateGatewayRequirements: function(gateway) {
            const phoneField = $('.form-field.donor-phone');
            const addressField = $('.form-field.donor-address');
            
            if (gateway === 'azampay') {
                phoneField.show().find('input').prop('required', true);
                this.showPhoneHelp();
            } else {
                phoneField.hide().find('input').prop('required', false);
                this.hidePhoneHelp();
            }
            
            // Update gateway description
            this.updateGatewayDescription(gateway);
        },

        // Show phone number help for AzamPay
        showPhoneHelp: function() {
            const phoneField = $('.form-field.donor-phone');
            if (!phoneField.find('.phone-help').length) {
                phoneField.append('<small class="phone-help">Enter Tanzanian mobile number (e.g., +255712345678 or 0712345678)</small>');
            }
        },

        // Hide phone number help
        hidePhoneHelp: function() {
            $('.phone-help').remove();
        },

        // Update gateway description
        updateGatewayDescription: function(gateway) {
            const descContainer = $('.gateway-description-dynamic');
            const gateways = kilismile_payments.gateways || {};
            
            if (gateways[gateway] && descContainer.length) {
                descContainer.html(gateways[gateway].description);
            }
        },

        // Update payment summary
        updatePaymentSummary: function() {
            const amount = parseFloat($(this.config.amountField).val()) || 0;
            const currency = this.getSelectedCurrency();
            const isRecurring = $(this.config.recurringCheckbox).is(':checked');
            const frequency = $('select[name="recurring_interval"]').val();
            
            const summary = $('.payment-summary');
            if (summary.length && amount > 0) {
                let summaryHtml = '<h4>Payment Summary</h4>';
                summaryHtml += `<div class="summary-row"><span>Amount:</span><span>${this.formatCurrency(amount, currency)}</span></div>`;
                
                if (isRecurring && frequency) {
                    summaryHtml += `<div class="summary-row"><span>Frequency:</span><span>${this.capitalizeFirst(frequency)}</span></div>`;
                }
                
                summaryHtml += `<div class="summary-row total"><span>Total:</span><span>${this.formatCurrency(amount, currency)}</span></div>`;
                
                summary.html(summaryHtml);
            }
        },

        // Validate form
        validateForm: function() {
            let isValid = true;
            const errors = {};

            // Validate required fields
            const requiredFields = {
                'donor_name': 'Name is required',
                'donor_email': 'Email is required',
                'amount': 'Amount is required',
                'gateway': 'Please select a payment method'
            };

            Object.keys(requiredFields).forEach(field => {
                const value = $(`[name="${field}"]`).val();
                if (!value || value.trim() === '') {
                    errors[field] = requiredFields[field];
                    isValid = false;
                }
            });

            // Validate email
            const email = $('[name="donor_email"]').val();
            if (email && !this.isValidEmail(email)) {
                errors.donor_email = 'Please enter a valid email address';
                isValid = false;
            }

            // Validate amount
            const amount = parseFloat($('[name="amount"]').val());
            const gateway = $('[name="gateway"]:checked').val();
            
            if (amount <= 0) {
                errors.amount = 'Please enter a valid amount';
                isValid = false;
            } else if (!this.validateMinimumAmount(amount, gateway)) {
                errors.amount = this.getMinimumAmountError(gateway);
                isValid = false;
            }

            // Validate phone for AzamPay
            if (gateway === 'azampay') {
                const phone = $('[name="donor_phone"]').val();
                if (!phone || !this.isValidTanzanianPhone(phone)) {
                    errors.donor_phone = 'Please enter a valid Tanzanian phone number';
                    isValid = false;
                }
            }

            // Display errors
            this.displayValidationErrors(errors);

            return isValid;
        },

        // Display validation errors
        displayValidationErrors: function(errors) {
            // Clear previous errors
            $('.form-field').removeClass('error');
            $('.error-message').hide();

            // Show new errors
            Object.keys(errors).forEach(field => {
                const fieldContainer = $(`.form-field input[name="${field}"], .form-field select[name="${field}"]`).closest('.form-field');
                fieldContainer.addClass('error');
                
                let errorElement = fieldContainer.find('.error-message');
                if (!errorElement.length) {
                    errorElement = $('<div class="error-message"></div>').appendTo(fieldContainer);
                }
                errorElement.text(errors[field]).show();
            });

            // Show general error message
            if (Object.keys(errors).length > 0) {
                this.showMessage('Please correct the errors below and try again.', 'error');
                
                // Scroll to first error
                const firstError = $('.form-field.error').first();
                if (firstError.length) {
                    $('html, body').animate({
                        scrollTop: firstError.offset().top - 100
                    }, 300);
                }
            }
        },

        // Clear field error
        clearFieldError: function(e) {
            const field = $(e.target).closest('.form-field');
            field.removeClass('error').find('.error-message').hide();
        },

        // Process payment
        processPayment: function() {
            const form = $(this.config.form);
            const submitButton = $(this.config.submitButton);
            const formData = this.getFormData();

            // Show loading state
            this.setLoadingState(true);
            submitButton.prop('disabled', true).addClass('loading');

            // Make AJAX request
            $.ajax({
                url: kilismile_payments.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_process_payment',
                    nonce: kilismile_payments.nonce,
                    ...formData
                },
                success: (response) => {
                    if (response.success) {
                        this.handlePaymentSuccess(response.data);
                    } else {
                        this.handlePaymentError(response.data);
                    }
                },
                error: (xhr, status, error) => {
                    this.handlePaymentError({
                        message: kilismile_payments.strings.network_error
                    });
                },
                complete: () => {
                    this.setLoadingState(false);
                    submitButton.prop('disabled', false).removeClass('loading');
                }
            });
        },

        // Handle payment success
        handlePaymentSuccess: function(data) {
            if (data.redirect && data.redirect_url) {
                this.showMessage(kilismile_payments.strings.redirecting, 'info');
                
                // Store transaction ID for status checking
                if (data.transaction_id) {
                    localStorage.setItem('kilismile_transaction_id', data.transaction_id);
                }
                
                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 1500);
            } else {
                this.showMessage(data.message || kilismile_payments.strings.payment_successful, 'success');
                this.clearForm();
            }
        },

        // Handle payment error
        handlePaymentError: function(data) {
            let message = data.message || kilismile_payments.strings.payment_failed;
            
            if (data.validation_errors) {
                this.displayValidationErrors(data.validation_errors);
                message = kilismile_payments.strings.validation_error;
            }
            
            this.showMessage(message, 'error');
        },

        // Set loading state
        setLoadingState: function(loading) {
            const form = $(this.config.form);
            
            if (loading) {
                form.addClass('form-loading');
            } else {
                form.removeClass('form-loading');
            }
        },

        // Get form data
        getFormData: function() {
            const form = $(this.config.form);
            const data = {};
            
            form.find('input, select, textarea').each(function() {
                const element = $(this);
                const name = element.attr('name');
                const type = element.attr('type');
                
                if (name) {
                    if (type === 'checkbox' || type === 'radio') {
                        if (element.is(':checked')) {
                            data[name] = element.val();
                        }
                    } else {
                        data[name] = element.val();
                    }
                }
            });
            
            return data;
        },

        // Show message
        showMessage: function(message, type = 'info') {
            let container = $(this.config.messageContainer);
            
            if (!container.length) {
                container = $('<div class="payment-message"></div>').prependTo(this.config.form);
            }
            
            container.removeClass('success error info warning')
                    .addClass(type)
                    .html(message)
                    .show();
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    container.fadeOut();
                }, 5000);
            }
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: container.offset().top - 50
            }, 300);
        },

        // Clear form
        clearForm: function() {
            $(this.config.form)[0].reset();
            $('.amount-preset').removeClass('selected');
            $('.form-field').removeClass('error');
            $('.error-message').hide();
            this.updatePaymentSummary();
        },

        // Validation helpers
        isValidEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        isValidTanzanianPhone: function(phone) {
            const regex = /^(\+?255|0)?[67]\d{8}$/;
            return regex.test(phone.replace(/\s+/g, ''));
        },

        validateMinimumAmount: function(amount, gateway) {
            const gateways = kilismile_payments.gateways || {};
            if (gateways[gateway] && gateways[gateway].min_amount) {
                return amount >= gateways[gateway].min_amount;
            }
            return amount > 0;
        },

        getMinimumAmountError: function(gateway) {
            const gateways = kilismile_payments.gateways || {};
            if (gateways[gateway] && gateways[gateway].min_amount) {
                const minAmount = gateways[gateway].min_amount;
                const currency = this.getGatewayCurrency(gateway);
                return `Minimum amount is ${this.formatCurrency(minAmount, currency)}`;
            }
            return 'Please enter a valid amount';
        },

        // Currency helpers
        getSelectedCurrency: function() {
            const gateway = $('[name="gateway"]:checked').val();
            return this.getGatewayCurrency(gateway);
        },

        getGatewayCurrency: function(gateway) {
            const gateways = kilismile_payments.gateways || {};
            if (gateways[gateway] && gateways[gateway].currencies && gateways[gateway].currencies.length > 0) {
                return gateways[gateway].currencies[0];
            }
            return 'USD';
        },

        formatCurrency: function(amount, currency) {
            const symbols = kilismile_payments.currency_symbols || {};
            const symbol = symbols[currency] || currency;
            return `${symbol} ${amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        },

        // Currency conversion
        convertCurrency: function(amount) {
            const fromCurrency = 'USD';
            const toCurrency = 'TZS';
            
            if (fromCurrency === toCurrency) return;
            
            $.ajax({
                url: kilismile_payments.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_convert_currency',
                    nonce: kilismile_payments.nonce,
                    amount: amount,
                    from_currency: fromCurrency,
                    to_currency: toCurrency
                },
                success: (response) => {
                    if (response.success) {
                        this.displayConversionInfo(response.data);
                    }
                }
            });
        },

        // Display conversion info
        displayConversionInfo: function(data) {
            const conversionInfo = $('.currency-conversion-info');
            if (conversionInfo.length) {
                const html = `
                    <small>
                        ${data.original_amount} ${data.original_currency} = 
                        ${data.converted_amount} ${data.converted_currency}
                        (Rate: ${data.exchange_rate})
                    </small>
                `;
                conversionInfo.html(html);
            }
        },

        // Load stored data
        loadStoredData: function() {
            const storedGateway = localStorage.getItem('kilismile_selected_gateway');
            if (storedGateway) {
                $(`[name="gateway"][value="${storedGateway}"]`).prop('checked', true).trigger('change');
            }
        },

        // Utility functions
        capitalizeFirst: function(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },

        // Setup progress indicator
        setupProgressIndicator: function() {
            const progress = $('.payment-progress');
            if (progress.length) {
                this.updateProgressStep(1);
            }
        },

        // Update progress step
        updateProgressStep: function(step) {
            $('.progress-step').each(function(index) {
                const stepElement = $(this);
                if (index < step) {
                    stepElement.addClass('completed').removeClass('active');
                } else if (index === step - 1) {
                    stepElement.addClass('active').removeClass('completed');
                } else {
                    stepElement.removeClass('active completed');
                }
            });
        },

        // Validate gateway requirements
        validateGatewayRequirements: function() {
            const selectedGateway = $('[name="gateway"]:checked').val();
            const submitButton = $(this.config.submitButton);
            
            if (!selectedGateway) {
                submitButton.prop('disabled', true);
                return;
            }
            
            const gateways = kilismile_payments.gateways || {};
            if (!gateways[selectedGateway]) {
                submitButton.prop('disabled', true);
                this.showMessage('Selected payment method is not available.', 'error');
                return;
            }
            
            submitButton.prop('disabled', false);
        }
    };

    // Payment status checker for return pages
    const PaymentStatusChecker = {
        init: function() {
            this.checkUrlParams();
            this.checkStoredTransaction();
        },

        checkUrlParams: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const paymentStatus = urlParams.get('payment');
            const transactionId = urlParams.get('transaction_id');

            if (paymentStatus) {
                this.handlePaymentReturn(paymentStatus, transactionId);
            }
        },

        checkStoredTransaction: function() {
            const transactionId = localStorage.getItem('kilismile_transaction_id');
            if (transactionId) {
                this.checkTransactionStatus(transactionId);
            }
        },

        handlePaymentReturn: function(status, transactionId) {
            let message, type;

            switch (status) {
                case 'success':
                    message = kilismile_payments.strings.payment_successful;
                    type = 'success';
                    localStorage.removeItem('kilismile_transaction_id');
                    break;
                case 'error':
                case 'failed':
                    message = kilismile_payments.strings.payment_failed;
                    type = 'error';
                    break;
                case 'cancelled':
                    message = kilismile_payments.strings.payment_cancelled;
                    type = 'warning';
                    break;
                default:
                    return;
            }

            this.showReturnMessage(message, type);
        },

        checkTransactionStatus: function(transactionId) {
            $.ajax({
                url: kilismile_payments.ajax_url,
                type: 'POST',
                data: {
                    action: 'kilismile_check_payment_status',
                    nonce: kilismile_payments.nonce,
                    transaction_id: transactionId
                },
                success: (response) => {
                    if (response.success) {
                        this.handleStatusResponse(response.data);
                    }
                }
            });
        },

        handleStatusResponse: function(data) {
            if (data.status === 'completed') {
                this.showReturnMessage(kilismile_payments.strings.payment_successful, 'success');
                localStorage.removeItem('kilismile_transaction_id');
            } else if (data.status === 'failed') {
                this.showReturnMessage(kilismile_payments.strings.payment_failed, 'error');
            }
        },

        showReturnMessage: function(message, type) {
            const messageHtml = `<div class="payment-message ${type}">${message}</div>`;
            $('body').prepend(messageHtml);

            // Auto-scroll to message
            $('html, body').animate({scrollTop: 0}, 500);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        if ($(KiliSmilePayments.config.form).length) {
            KiliSmilePayments.init();
        }
        
        PaymentStatusChecker.init();
    });

    // Expose to global scope for theme integration
    window.KiliSmilePayments = KiliSmilePayments;

})(jQuery);

