/**
 * KiliSmile Payment Form JavaScript
 * Handles currency-based payment gateway selection and form interactions
 */

(function($) {
    'use strict';

    class KiliSmilePaymentForm {
        constructor() {
            this.form = $('#donation-form');
            this.currencyInputs = $('input[name="currency"]');
            this.amountInput = $('#amount');
            this.currencySymbol = $('#currency-symbol');
            this.presetAmounts = $('#preset-amounts');
            this.phoneField = $('#phone-field');
            this.paypalMethod = $('#paypal-method');
            this.mobileMoneyMethod = $('#mobile-money-method');
            this.submitBtn = $('#submit-payment');
            this.messages = $('#payment-messages');
            
            this.currentCurrency = 'USD';
            this.paymentData = window.kilismilePayment || {};
            
            this.init();
        }

        init() {
            this.bindEvents();
            this.updatePaymentMethod();
            this.generatePresetAmounts();
            this.validateForm();
        }

        bindEvents() {
            // Currency selection
            this.currencyInputs.on('change', (e) => {
                this.currentCurrency = e.target.value;
                this.updatePaymentMethod();
                this.generatePresetAmounts();
                this.validateForm();
            });

            // Amount input
            this.amountInput.on('input', () => {
                this.validateForm();
                this.updatePresetSelection();
            });

            // Preset amounts
            $(document).on('click', '.preset-amount', (e) => {
                const amount = $(e.target).data('amount');
                this.amountInput.val(amount);
                this.updatePresetSelection();
                this.validateForm();
            });

            // Mobile network selection
            $('input[name="mobile_network"]').on('change', () => {
                this.validateForm();
            });

            // Form fields
            $('input[name="donor_name"], input[name="donor_email"], input[name="donor_phone"]').on('input', () => {
                this.validateForm();
            });

            // Form submission
            this.form.on('submit', (e) => {
                e.preventDefault();
                this.processPayment();
            });
        }

        updatePaymentMethod() {
            this.updateCurrencyDisplay();
            
            if (this.currentCurrency === 'USD') {
                this.showPayPalMethod();
            } else if (this.currentCurrency === 'TZS') {
                this.showMobileMoneyMethod();
            }
        }

        updateCurrencyDisplay() {
            const symbols = {
                'USD': '$',
                'TZS': 'TSh'
            };
            this.currencySymbol.text(symbols[this.currentCurrency] || '$');
        }

        showPayPalMethod() {
            this.paypalMethod.show();
            this.mobileMoneyMethod.hide();
            this.phoneField.hide();
            
            // Clear mobile network selection
            $('input[name="mobile_network"]').prop('checked', false);
        }

        showMobileMoneyMethod() {
            this.paypalMethod.hide();
            this.mobileMoneyMethod.show();
            this.phoneField.show();
            
            // Set required attribute for phone
            $('#donor_phone').prop('required', true);
        }

        generatePresetAmounts() {
            const presets = {
                'USD': [10, 25, 50, 100, 250, 500],
                'TZS': [10000, 25000, 50000, 100000, 250000, 500000]
            };

            const amounts = presets[this.currentCurrency] || presets['USD'];
            const symbol = this.currentCurrency === 'USD' ? '$' : 'TSh ';
            
            let html = '';
            amounts.forEach(amount => {
                const formattedAmount = this.formatAmount(amount);
                html += `<div class="preset-amount" data-amount="${amount}">${symbol}${formattedAmount}</div>`;
            });
            
            this.presetAmounts.html(html);
        }

        formatAmount(amount) {
            if (this.currentCurrency === 'TZS') {
                return amount.toLocaleString();
            }
            return amount.toString();
        }

        updatePresetSelection() {
            const currentAmount = parseFloat(this.amountInput.val());
            $('.preset-amount').removeClass('selected');
            
            $('.preset-amount').each(function() {
                if (parseFloat($(this).data('amount')) === currentAmount) {
                    $(this).addClass('selected');
                }
            });
        }

        validateForm() {
            const amount = parseFloat(this.amountInput.val());
            const name = $('input[name="donor_name"]').val().trim();
            const email = $('input[name="donor_email"]').val().trim();
            const currency = this.currentCurrency;
            
            let isValid = amount > 0 && name && email;
            
            // Check minimum amounts
            const minimums = {
                'USD': 1,
                'TZS': 1000
            };
            
            if (amount < minimums[currency]) {
                isValid = false;
            }
            
            // Additional validation for mobile money
            if (currency === 'TZS') {
                const phone = $('input[name="donor_phone"]').val().trim();
                const network = $('input[name="mobile_network"]:checked').val();
                
                if (!phone || !network) {
                    isValid = false;
                }
                
                // Validate phone format (Tanzanian numbers)
                const phoneRegex = /^[67]\d{8}$/;
                if (phone && !phoneRegex.test(phone)) {
                    isValid = false;
                }
            }
            
            this.submitBtn.prop('disabled', !isValid);
            
            if (isValid) {
                this.updateSubmitButtonText();
            }
        }

        updateSubmitButtonText() {
            const amount = parseFloat(this.amountInput.val());
            const currency = this.currentCurrency;
            const symbol = currency === 'USD' ? '$' : 'TSh ';
            const formattedAmount = this.formatAmount(amount);
            
            let gateway = '';
            if (currency === 'USD') {
                gateway = 'via PayPal';
            } else {
                const network = $('input[name="mobile_network"]:checked').next().find('span').text();
                gateway = `via ${network}`;
            }
            
            this.submitBtn.find('.btn-text').text(`Donate ${symbol}${formattedAmount} ${gateway}`);
        }

        async processPayment() {
            this.setLoading(true);
            this.clearMessages();
            
            const formData = this.getFormData();
            
            try {
                if (formData.currency === 'USD') {
                    await this.processPayPalPayment(formData);
                } else {
                    await this.processMobileMoneyPayment(formData);
                }
            } catch (error) {
                this.showMessage('An error occurred while processing your payment. Please try again.', 'error');
                console.error('Payment error:', error);
            } finally {
                this.setLoading(false);
            }
        }

        getFormData() {
            return {
                currency: this.currentCurrency,
                amount: parseFloat(this.amountInput.val()),
                donor_name: $('input[name="donor_name"]').val(),
                donor_email: $('input[name="donor_email"]').val(),
                donor_phone: $('input[name="donor_phone"]').val(),
                mobile_network: $('input[name="mobile_network"]:checked').val(),
                nonce: this.paymentData.nonce
            };
        }

        async processPayPalPayment(formData) {
            this.showMessage('Redirecting to PayPal...', 'info');
            
            const response = await $.ajax({
                url: this.paymentData.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'kilismile_process_payment',
                    ...formData
                }
            });

            if (response.success) {
                // Redirect to PayPal
                window.location.href = response.redirect_url;
            } else {
                throw new Error(response.message || 'PayPal payment failed');
            }
        }

        async processMobileMoneyPayment(formData) {
            this.showMessage('Initiating mobile money payment...', 'info');
            
            const response = await $.ajax({
                url: this.paymentData.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'kilismile_process_payment',
                    ...formData
                }
            });

            if (response.success) {
                this.showMessage(
                    `Payment request sent to your phone (${formData.donor_phone}). Please check your mobile money app and enter your PIN to complete the payment.`,
                    'success'
                );
                
                // Poll for payment status
                this.pollPaymentStatus(response.transaction_id, response.donation_id);
            } else {
                throw new Error(response.message || 'Mobile money payment failed');
            }
        }

        async pollPaymentStatus(transactionId, donationId) {
            const maxAttempts = 30; // 5 minutes (10 seconds * 30)
            let attempts = 0;
            
            const poll = setInterval(async () => {
                attempts++;
                
                try {
                    const response = await $.ajax({
                        url: this.paymentData.ajaxUrl,
                        method: 'POST',
                        data: {
                            action: 'kilismile_check_payment_status',
                            donation_id: donationId,
                            nonce: this.paymentData.nonce
                        }
                    });
                    
                    if (response.success) {
                        if (response.status === 'completed') {
                            clearInterval(poll);
                            this.showMessage('Payment completed successfully! Thank you for your donation.', 'success');
                            this.form[0].reset();
                            this.validateForm();
                        } else if (response.status === 'failed') {
                            clearInterval(poll);
                            this.showMessage('Payment was not completed. Please try again.', 'error');
                        } else {
                            this.showMessage(`Payment status: ${response.message}`, 'info');
                        }
                    }
                } catch (error) {
                    console.error('Status check error:', error);
                }
                
                if (attempts >= maxAttempts) {
                    clearInterval(poll);
                    this.showMessage('Payment status check timed out. Please contact us if you completed the payment.', 'info');
                }
            }, 10000); // Check every 10 seconds
        }

        setLoading(loading) {
            if (loading) {
                this.submitBtn.prop('disabled', true);
                this.submitBtn.find('.btn-text').hide();
                this.submitBtn.find('.btn-loader').show();
            } else {
                this.submitBtn.find('.btn-text').show();
                this.submitBtn.find('.btn-loader').hide();
                this.validateForm(); // Re-enable if form is valid
            }
        }

        showMessage(message, type) {
            const messageHtml = `
                <div class="payment-message ${type}">
                    ${message}
                </div>
            `;
            this.messages.html(messageHtml);
        }

        clearMessages() {
            this.messages.empty();
        }
    }

    // Initialize when DOM is ready
    $(document).ready(function() {
        new KiliSmilePaymentForm();
    });

})(jQuery);

