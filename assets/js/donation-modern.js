/**
 * Modern Donation Form JavaScript
 * 
 * Handles the frontend interactions for the enhanced donation form
 * with multi-step wizard, real-time validation, and AJAX processing.
 *
 * @package KiliSmile
 * @version 2.0.0
 */

(function($) {
    'use strict';
    
    // Main donation form handler
    class KiliSmileDonationForm {
        constructor(formElement) {
            this.form = $(formElement);
            this.currentStep = 1;
            this.totalSteps = 3;
            this.selectedCurrency = 'TZS';
            this.selectedAmount = 0;
            this.validationErrors = {};
            this.paymentMethods = {};
            this.submitting = false;
            
            this.init();
        }
        
        init() {
            this.bindEvents();
            this.updateStepDisplay();
            this.loadPaymentMethods();
            this.setupValidation();
        }
        
        bindEvents() {
            // Currency selection
            this.form.find('.currency-btn').on('click', (e) => {
                e.preventDefault();
                this.handleCurrencyChange($(e.currentTarget));
            });
            
            // Amount selection
            this.form.find('.preset-amount').on('click', (e) => {
                e.preventDefault();
                this.handleAmountSelection($(e.currentTarget));
            });
            
            // Custom amount input
            this.form.find('#custom-amount').on('input', (e) => {
                this.handleCustomAmount($(e.currentTarget));
            });
            
            // Navigation buttons
            this.form.find('.next-step-btn').on('click', (e) => {
                e.preventDefault();
                this.nextStep();
            });
            
            this.form.find('.prev-step-btn').on('click', (e) => {
                e.preventDefault();
                this.prevStep();
            });
            
            // Form submission
            this.form.on('submit', (e) => {
                e.preventDefault();
                this.submitDonation();
            });
            
            // Real-time validation
            this.form.find('input[required]').on('blur', (e) => {
                this.validateField($(e.currentTarget));
            });
            
            // Payment method selection
            this.form.on('change', 'input[name="payment_method"]', (e) => {
                this.handlePaymentMethodChange($(e.currentTarget));
            });
        }
        
        handleCurrencyChange(button) {
            const newCurrency = button.data('currency');
            
            if (newCurrency === this.selectedCurrency) return;
            
            this.selectedCurrency = newCurrency;
            this.updateCurrencyDisplay();
            this.loadPaymentMethods();
            this.convertCurrentAmount();
            this.validateAmount();
        }
        
        updateCurrencyDisplay() {
            // Update button states
            this.form.find('.currency-btn').removeClass('active').css({
                background: 'transparent',
                color: '#6c757d'
            });
            
            this.form.find(`.currency-btn[data-currency="${this.selectedCurrency}"]`)
                .addClass('active').css({
                    background: '#28a745',
                    color: 'white'
                });
            
            // Update amount options visibility
            this.form.find('.currency-amounts').hide().removeClass('active');
            this.form.find(`.currency-amounts[data-currency="${this.selectedCurrency}"]`)
                .show().addClass('active');
            
            // Update currency symbol
            const symbol = this.selectedCurrency === 'USD' ? '$' : 'TSh';
            this.form.find('.currency-symbol').text(symbol);
            
            // Reset amount selection
            this.selectedAmount = 0;
            this.form.find('#custom-amount').val('');
            this.updateAmountSelection();
        }
        
        handleAmountSelection(button) {
            const amount = parseFloat(button.data('amount'));
            const currency = button.data('currency');
            
            if (currency !== this.selectedCurrency) return;
            
            this.selectedAmount = amount;
            this.form.find('#custom-amount').val(amount);
            this.updateAmountSelection();
            this.showConversion();
            this.validateAmount();
        }
        
        handleCustomAmount(input) {
            this.selectedAmount = parseFloat(input.val()) || 0;
            this.updateAmountSelection();
            this.showConversion();
            this.validateAmount();
        }
        
        updateAmountSelection() {
            // Update preset button states
            this.form.find('.preset-amount').removeClass('selected').css({
                borderColor: '#e9ecef',
                background: 'white',
                color: '#495057'
            });
            
            if (this.selectedAmount > 0) {
                this.form.find(`.preset-amount[data-currency="${this.selectedCurrency}"][data-amount="${this.selectedAmount}"]`)
                    .addClass('selected').css({
                        borderColor: '#28a745',
                        background: '#28a745',
                        color: 'white'
                    });
            }
        }
        
        showConversion() {
            const conversionDisplay = this.form.find('.conversion-display');
            
            if (this.selectedAmount <= 0) {
                conversionDisplay.text('');
                return;
            }
            
            const rates = kilismileDonation.currency_rates;
            let convertedAmount, convertedCurrency;
            
            if (this.selectedCurrency === 'USD') {
                convertedAmount = (this.selectedAmount * rates.USD_to_TZS).toLocaleString();
                convertedCurrency = 'TSh ';
            } else {
                convertedAmount = (this.selectedAmount * rates.TZS_to_USD).toFixed(2);
                convertedCurrency = '$';
            }
            
            conversionDisplay.text(`≈ ${convertedCurrency}${convertedAmount}`);
        }
        
        async convertCurrentAmount() {
            if (this.selectedAmount <= 0) return;
            
            try {
                const response = await this.makeAjaxRequest('convert_currency', {
                    amount: this.selectedAmount,
                    from: this.selectedCurrency === 'USD' ? 'TZS' : 'USD',
                    to: this.selectedCurrency
                });
                
                if (response.success) {
                    this.selectedAmount = response.data.converted_amount;
                    this.form.find('#custom-amount').val(this.selectedAmount);
                    this.updateAmountSelection();
                }
            } catch (error) {
                console.error('Currency conversion failed:', error);
            }
        }
        
        validateAmount() {
            const nextBtn = this.form.find('.form-step[data-step="1"] .next-step-btn');
            const isValid = this.selectedAmount > 0;
            
            nextBtn.prop('disabled', !isValid).css({
                background: isValid ? '#28a745' : '#6c757d',
                cursor: isValid ? 'pointer' : 'not-allowed'
            });
            
            return isValid;
        }
        
        async loadPaymentMethods() {
            try {
                const response = await this.makeAjaxRequest('get_payment_methods', {
                    currency: this.selectedCurrency
                });
                
                if (response.success) {
                    this.paymentMethods = response.data.payment_methods;
                }
            } catch (error) {
                console.error('Failed to load payment methods:', error);
            }
        }
        
        nextStep() {
            if (!this.validateCurrentStep()) {
                this.showValidationErrors();
                return;
            }
            
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
                this.updateStepDisplay();
                
                if (this.currentStep === 3) {
                    this.updateDonationSummary();
                    this.renderPaymentMethods();
                }
            }
        }
        
        prevStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateStepDisplay();
            }
        }
        
        updateStepDisplay() {
            // Update progress indicators
            this.form.find('.step-indicator').each((index, indicator) => {
                const $indicator = $(indicator);
                const stepNumber = index + 1;
                const $circle = $indicator.find('.step-circle');
                
                if (stepNumber <= this.currentStep) {
                    $indicator.addClass('active');
                    $circle.css({
                        background: 'white',
                        color: '#28a745'
                    });
                } else {
                    $indicator.removeClass('active');
                    $circle.css({
                        background: 'rgba(255,255,255,0.3)',
                        color: 'white'
                    });
                }
            });
            
            // Update step visibility
            this.form.find('.form-step').each((index, step) => {
                const $step = $(step);
                const stepNumber = index + 1;
                
                if (stepNumber === this.currentStep) {
                    $step.show().addClass('active');
                } else {
                    $step.hide().removeClass('active');
                }
            });
        }
        
        validateCurrentStep() {
            this.validationErrors = {};
            
            switch (this.currentStep) {
                case 1:
                    return this.validateStep1();
                case 2:
                    return this.validateStep2();
                case 3:
                    return this.validateStep3();
                default:
                    return true;
            }
        }
        
        validateStep1() {
            if (this.selectedAmount <= 0) {
                this.validationErrors.amount = kilismileDonation.strings.invalid_amount;
                return false;
            }
            
            return true;
        }
        
        validateStep2() {
            const firstName = this.form.find('input[name="first_name"]').val().trim();
            const lastName = this.form.find('input[name="last_name"]').val().trim();
            const email = this.form.find('input[name="email"]').val().trim();
            
            if (!firstName) {
                this.validationErrors.first_name = kilismileDonation.strings.required_field;
            }
            
            if (!lastName) {
                this.validationErrors.last_name = kilismileDonation.strings.required_field;
            }
            
            if (!email) {
                this.validationErrors.email = kilismileDonation.strings.required_field;
            } else if (!this.isValidEmail(email)) {
                this.validationErrors.email = kilismileDonation.strings.invalid_email;
            }
            
            return Object.keys(this.validationErrors).length === 0;
        }
        
        validateStep3() {
            const selectedMethod = this.form.find('input[name="payment_method"]:checked').val();
            
            if (!selectedMethod) {
                this.validationErrors.payment_method = 'Please select a payment method.';
                return false;
            }
            
            return true;
        }
        
        showValidationErrors() {
            // Clear previous errors
            this.form.find('.error-message').remove();
            this.form.find('.field-error').removeClass('field-error');
            
            // Show new errors
            Object.keys(this.validationErrors).forEach(field => {
                const $field = this.form.find(`[name="${field}"]`);
                const errorMessage = this.validationErrors[field];
                
                $field.addClass('field-error');
                $field.after(`<div class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px;">${errorMessage}</div>`);
            });
        }
        
        validateField($field) {
            const fieldName = $field.attr('name');
            const value = $field.val().trim();
            let isValid = true;
            let errorMessage = '';
            
            // Clear previous error
            $field.removeClass('field-error').next('.error-message').remove();
            
            if ($field.prop('required') && !value) {
                isValid = false;
                errorMessage = kilismileDonation.strings.required_field;
            } else if (fieldName === 'email' && value && !this.isValidEmail(value)) {
                isValid = false;
                errorMessage = kilismileDonation.strings.invalid_email;
            }
            
            if (!isValid) {
                $field.addClass('field-error');
                $field.after(`<div class="error-message" style="color: #dc3545; font-size: 0.85rem; margin-top: 5px;">${errorMessage}</div>`);
            }
            
            return isValid;
        }
        
        updateDonationSummary() {
            const currencySymbol = this.selectedCurrency === 'USD' ? '$' : 'TSh ';
            const formattedAmount = this.selectedCurrency === 'USD' ? 
                this.selectedAmount.toFixed(2) : 
                this.selectedAmount.toLocaleString();
            
            this.form.find('.summary-amount').text(currencySymbol + formattedAmount);
            this.form.find('.summary-total').text(currencySymbol + formattedAmount);
            
            // Show/hide recurring summary
            const isRecurring = this.form.find('input[name="recurring"]').is(':checked');
            const recurringSummary = this.form.find('.recurring-summary');
            
            if (isRecurring && recurringSummary.length) {
                recurringSummary.show();
            } else if (recurringSummary.length) {
                recurringSummary.hide();
            }
        }
        
        renderPaymentMethods() {
            const container = this.form.find('.payment-methods-container');
            
            if (!this.paymentMethods || this.paymentMethods.length === 0) {
                container.html('<p>No payment methods available for this currency.</p>');
                return;
            }
            
            let html = '<div class="payment-method-selection">';
            html += '<h4 style="margin-bottom: 20px; color: #495057;">Choose Payment Method</h4>';
            html += '<div style="display: grid; gap: 15px;">';
            
            this.paymentMethods.forEach(method => {
                html += `
                    <label class="payment-option" style="display: flex; align-items: center; padding: 15px; border: 2px solid #e9ecef; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">
                        <input type="radio" name="payment_method" value="${method.id}" style="margin-right: 15px; transform: scale(1.3);">
                        <i class="${method.icon}" style="font-size: 1.5rem; margin-right: 15px; color: #28a745;"></i>
                        <div>
                            <div style="font-weight: 600;">${method.name}</div>
                            <div style="font-size: 0.9rem; color: #6c757d;">${method.description}</div>
                        </div>
                    </label>
                `;
            });
            
            html += '</div></div>';
            container.html(html);
            
            // Bind payment method selection events
            container.find('input[name="payment_method"]').on('change', (e) => {
                this.handlePaymentMethodChange($(e.currentTarget));
            });
        }
        
        handlePaymentMethodChange($input) {
            // Update payment option styling
            this.form.find('.payment-option').css({
                borderColor: '#e9ecef',
                background: 'white'
            });
            
            $input.closest('.payment-option').css({
                borderColor: '#28a745',
                background: '#f8fff8'
            });
            
            // Enable submit button
            const submitBtn = this.form.find('.donation-submit-btn');
            submitBtn.prop('disabled', false).css({
                background: '#28a745',
                cursor: 'pointer'
            });
        }
        
        async submitDonation() {
            if (this.submitting) return;
            
            if (!this.validateCurrentStep()) {
                this.showValidationErrors();
                return;
            }
            
            this.submitting = true;
            const submitBtn = this.form.find('.donation-submit-btn');
            const originalText = submitBtn.text();
            
            // Show loading state
            submitBtn.text(kilismileDonation.strings.processing).prop('disabled', true).css({
                background: '#6c757d',
                cursor: 'not-allowed'
            });
            
            try {
                const formData = this.collectFormData();
                const response = await this.makeAjaxRequest('process_donation', formData);
                
                if (response.success) {
                    this.handleSuccessfulSubmission(response.data);
                } else {
                    this.handleSubmissionError(response.data);
                }
                
            } catch (error) {
                console.error('Donation submission error:', error);
                this.handleSubmissionError({
                    message: kilismileDonation.strings.error
                });
            } finally {
                this.submitting = false;
                submitBtn.text(originalText).prop('disabled', false).css({
                    background: '#28a745',
                    cursor: 'pointer'
                });
            }
        }
        
        collectFormData() {
            return {
                amount: this.selectedAmount,
                currency: this.selectedCurrency,
                recurring: this.form.find('input[name="recurring"]').is(':checked'),
                first_name: this.form.find('input[name="first_name"]').val(),
                last_name: this.form.find('input[name="last_name"]').val(),
                email: this.form.find('input[name="email"]').val(),
                phone: this.form.find('input[name="phone"]').val(),
                anonymous: this.form.find('input[name="anonymous"]').is(':checked'),
                payment_method: this.form.find('input[name="payment_method"]:checked').val(),
                purpose: 'general',
                message: '',
                nonce: kilismileDonation.nonce
            };
        }
        
        handleSuccessfulSubmission(data) {
            if (data.redirect_url) {
                // Redirect to payment gateway
                window.location.href = data.redirect_url;
            } else {
                // Show success message
                this.showSuccessMessage(data);
            }
        }
        
        handleSubmissionError(data) {
            this.showErrorMessage(data.message || kilismileDonation.strings.error);
        }
        
        showSuccessMessage(data) {
            const message = `
                <div style="text-align: center; padding: 40px; color: #28a745;">
                    <i class="fas fa-check-circle" style="font-size: 4rem; margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 15px;">${kilismileDonation.strings.success}</h3>
                    <p>Donation ID: ${data.donation_id}</p>
                    <p>Thank you for your generous support!</p>
                </div>
            `;
            
            this.form.html(message);
        }
        
        showErrorMessage(message) {
            // Show error at top of current step
            const currentStep = this.form.find(`.form-step[data-step="${this.currentStep}"]`);
            currentStep.prepend(`
                <div class="donation-error" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                    <strong>Error:</strong> ${message}
                </div>
            `);
            
            // Remove error after 10 seconds
            setTimeout(() => {
                this.form.find('.donation-error').fadeOut();
            }, 10000);
        }
        
        async makeAjaxRequest(action, data = {}) {
            const requestData = {
                action: action,
                ...data
            };
            
            const response = await fetch(kilismileDonation.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(requestData)
            });
            
            return await response.json();
        }
        
        setupValidation() {
            // Add CSS for validation states
            const style = document.createElement('style');
            style.textContent = `
                .field-error {
                    border-color: #dc3545 !important;
                    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                }
                
                .payment-option:hover {
                    border-color: #28a745 !important;
                    background: #f8fff8 !important;
                }
                
                .preset-amount:hover {
                    border-color: #28a745 !important;
                    background: #f8fff8 !important;
                }
                
                .currency-btn:hover {
                    background: #20c997 !important;
                    color: white !important;
                }
            `;
            document.head.appendChild(style);
        }
        
        isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    }
    
    // Initialize donation forms when document is ready
    $(document).ready(function() {
        $('.kilismile-donation-form').each(function() {
            new KiliSmileDonationForm(this);
        });
    });
    
})(jQuery);


