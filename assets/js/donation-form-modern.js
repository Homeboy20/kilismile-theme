/**
 * Modern Donation Form JavaScript
 * 
 * @package KiliSmile
 * @version 2.0
 */

jQuery(document).ready(function($) {
    let currentStep = 1;
    const exchangeRate = 2500; // TZS to USD (update this value as needed)
    
    // Step navigation
    function goToStep(step) {
        $('.form-step').removeClass('active');
        $(`.form-step[data-step="${step}"]`).addClass('active');
        
        $('.progress-step').removeClass('active completed');
        for (let i = 1; i < step; i++) {
            $(`.progress-step[data-step="${i}"]`).addClass('completed');
        }
        $(`.progress-step[data-step="${step}"]`).addClass('active');
        
        // Update progress line
        const progress = ((step - 1) / 2) * 100;
        $('.progress-line').css('background', 
            `linear-gradient(to right, #10b981 ${progress}%, #e5e7eb ${progress}%)`);
        
        currentStep = step;
        updateForm();
    }
    
    // Currency toggle
    $('.currency-option').on('click', function() {
        const currency = $(this).data('currency');
        $('.currency-option').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true);
        
        // Toggle amount displays
        if (currency === 'TZS') {
            $('.tzs-amount').show();
            $('.usd-amount').hide();
            $('.currency-symbol').text('TZS');
            $('#donation-amount').attr('placeholder', '0');
        } else {
            $('.tzs-amount').hide();
            $('.usd-amount').show();
            $('.currency-symbol').text('$');
            $('#donation-amount').attr('placeholder', '0');
        }
        
        $('.amount-btn').removeClass('selected');
        $('#donation-amount').val('');
        updateConversion();
        updateSummary();
    });
    
    // Amount selection
    $('.amount-btn').on('click', function() {
        $('.amount-btn').removeClass('selected');
        $(this).addClass('selected');
        
        const amount = $(this).data('amount');
        const currency = $(this).data('currency');
        
        $('#donation-amount').val(amount);
        updateConversion();
        updateSummary();
    });
    
    // Custom amount input
    $('#donation-amount').on('input', function() {
        $('.amount-btn').removeClass('selected');
        updateConversion();
        updateSummary();
    });
    
    // Frequency selection
    $('.frequency-option').on('click', function() {
        $('.frequency-option').removeClass('active');
        $(this).addClass('active');
        $(this).find('input').prop('checked', true);
        updateSummary();
    });
    
    // Payment method selection
    $('.payment-method-card').on('click', function() {
        $('.payment-method-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input').prop('checked', true);
        updateSummary();
    });
    
    // Navigation buttons
    $('.btn-next').on('click', function() {
        const nextStep = parseInt($(this).data('next'));
        if (validateStep(currentStep)) {
            goToStep(nextStep);
        }
    });
    
    $('.btn-back').on('click', function() {
        const prevStep = parseInt($(this).data('back'));
        goToStep(prevStep);
    });
    
    // Form validation
    function validateStep(step) {
        switch (step) {
            case 1:
                const amount = parseFloat($('#donation-amount').val());
                if (!amount || amount < 100) {
                    alert(kilismile_donation_js.messages.invalid_amount || 'Please enter a valid donation amount.');
                    return false;
                }
                break;
            case 2:
                if (!$('input[name="payment_method"]:checked').length) {
                    alert(kilismile_donation_js.messages.select_payment || 'Please select a payment method.');
                    return false;
                }
                break;
            case 3:
                const requiredFields = ['donor_first_name', 'donor_last_name', 'donor_email'];
                for (let field of requiredFields) {
                    if (!$(`#${field}`).val().trim()) {
                        alert(kilismile_donation_js.messages.required_fields || 'Please fill in all required fields.');
                        $(`#${field}`).focus();
                        return false;
                    }
                }
                
                // Email validation
                const email = $('#donor_email').val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    alert(kilismile_donation_js.messages.invalid_email || 'Please enter a valid email address.');
                    $('#donor_email').focus();
                    return false;
                }
                break;
        }
        return true;
    }
    
    // Update conversion display
    function updateConversion() {
        const amount = parseFloat($('#donation-amount').val());
        const currency = $('input[name="currency"]:checked').val();
        
        if (amount && amount > 0) {
            if (currency === 'TZS') {
                const usdAmount = amount / exchangeRate;
                $('#conversion-text').text(`≈ $${usdAmount.toFixed(2)} USD`);
            } else {
                const tzsAmount = amount * exchangeRate;
                $('#conversion-text').text(`≈ TZS ${tzsAmount.toLocaleString()}`);
            }
        } else {
            $('#conversion-text').text('');
        }
    }
    
    // Update summary and submit button
    function updateSummary() {
        const amount = parseFloat($('#donation-amount').val()) || 0;
        const currency = $('input[name="currency"]:checked').val() || 'TZS';
        const frequency = $('input[name="frequency"]:checked').val() || 'once';
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        
        // Update summary
        const formattedAmount = currency === 'TZS' ? 
            `TZS ${amount.toLocaleString()}` : 
            `$${amount.toFixed(2)}`;
            
        $('#summary-amount').text(formattedAmount);
        $('#submit-amount-display').text(formattedAmount);
        
        const frequencyTexts = {
            'once': kilismile_donation_js.frequency.once || 'One-time',
            'monthly': kilismile_donation_js.frequency.monthly || 'Monthly',
            'quarterly': kilismile_donation_js.frequency.quarterly || 'Quarterly'
        };
        $('#summary-frequency').text(frequencyTexts[frequency] || 'One-time');
        
        if (paymentMethod) {
            const methodName = $(`.payment-method-card input[value="${paymentMethod}"]`)
                .closest('.payment-method-card')
                .find('.method-name').text();
            $('#summary-payment').text(methodName);
        } else {
            $('#summary-payment').text(kilismile_donation_js.messages.not_selected || 'Not selected');
        }
        
        // Enable/disable submit button
        const canSubmit = amount > 0 && 
                         paymentMethod && 
                         $('#donor_first_name').val() && 
                         $('#donor_last_name').val() && 
                         $('#donor_email').val();
                         
        $('.submit-button').prop('disabled', !canSubmit);
    }
    
    // Update form state
    function updateForm() {
        updateSummary();
        
        // Show/hide summary based on step
        if (currentStep >= 2) {
            $('.donation-summary').show();
        } else {
            $('.donation-summary').hide();
        }
        
        if (currentStep === 3) {
            $('.form-submit').show();
        } else {
            $('.form-submit').hide();
        }
    }
    
    // Animate impact counters
    function animateCounters() {
        $('.impact-number').each(function() {
            const target = parseInt($(this).data('count'));
            const $this = $(this);
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(function() {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                $this.text(Math.floor(current).toLocaleString());
            }, 50);
        });
    }
    
    // Form submission
    $('#modern-donation-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateStep(3)) {
            return;
        }
        
        // Show loading state
        const originalText = $('.submit-button').html();
        $('.submit-button').html('<i class="fas fa-spinner fa-spin"></i> ' + 
            (kilismile_donation_js.messages.processing || 'Processing...'));
        $('.submit-button').prop('disabled', true);
        
        // Prepare form data
        const formData = new FormData(this);
        
        // Submit via AJAX
        $.ajax({
            url: kilismile_donation_js.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(kilismile_donation_js.messages.success || 'Thank you for your donation!');
                    
                    // Redirect if needed
                    if (response.data && response.data.redirect_url) {
                        window.location.href = response.data.redirect_url;
                    } else {
                        // Reset form or redirect to thank you page
                        window.location.reload();
                    }
                } else {
                    // Show error message
                    alert(response.data.message || kilismile_donation_js.messages.error || 'An error occurred. Please try again.');
                    $('.submit-button').html(originalText);
                    $('.submit-button').prop('disabled', false);
                }
            },
            error: function() {
                alert(kilismile_donation_js.messages.error || 'An error occurred. Please try again.');
                $('.submit-button').html(originalText);
                $('.submit-button').prop('disabled', false);
            }
        });
    });
    
    // Monitor required fields
    $('#donor_first_name, #donor_last_name, #donor_email').on('input', updateSummary);
    
    // Initialize
    setTimeout(animateCounters, 500); // Delay animation slightly for better effect
    updateForm();
    
    // Update progress line on page load
    $('.progress-line').css('background', '#e5e7eb');
});


