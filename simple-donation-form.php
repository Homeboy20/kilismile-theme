<?php
/**
 * Simple Direct Donation Form
 * 
 * A minimal, always-visible donation form for testing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add shortcode for simple form display
function kilismile_simple_donation_form($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Make a Donation',
        'show_test' => 'true'
    ), $atts);
    
    ob_start();
    ?>
    
    <div id="kilismile-simple-donation-form" style="
        max-width: 600px; 
        margin: 20px auto; 
        padding: 30px; 
        background: white; 
        border-radius: 10px; 
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        border: 2px solid #28a745;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    ">
        
        <!-- Form Header -->
        <div style="text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e9ecef;">
            <h2 style="color: #28a745; margin: 0 0 10px 0; font-size: 1.8rem;">
                <i class="fas fa-heart" style="color: #ff6b6b; margin-right: 10px;"></i>
                <?php echo esc_html($atts['title']); ?>
            </h2>
            <p style="color: #6c757d; margin: 0; font-size: 1rem;">
                Help us make a difference in communities across Tanzania
            </p>
        </div>
        
        <!-- Donation Form -->
        <form id="simple-donation-form" method="post" style="display: block !important;">
            
            <!-- Amount Selection -->
            <div class="form-section" style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 10px; font-size: 1rem;">
                    💰 Choose Donation Amount
                </label>
                
                <div class="amount-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 10px; margin-bottom: 15px;">
                    <button type="button" class="amount-btn" data-amount="10000" style="
                        padding: 12px; 
                        border: 2px solid #ddd; 
                        background: white; 
                        border-radius: 6px; 
                        cursor: pointer; 
                        font-weight: 600;
                        transition: all 0.3s ease;
                    " onclick="selectAmount(this, 10000)">
                        TZS 10,000
                    </button>
                    <button type="button" class="amount-btn" data-amount="25000" style="
                        padding: 12px; 
                        border: 2px solid #ddd; 
                        background: white; 
                        border-radius: 6px; 
                        cursor: pointer; 
                        font-weight: 600;
                        transition: all 0.3s ease;
                    " onclick="selectAmount(this, 25000)">
                        TZS 25,000
                    </button>
                    <button type="button" class="amount-btn" data-amount="50000" style="
                        padding: 12px; 
                        border: 2px solid #ddd; 
                        background: white; 
                        border-radius: 6px; 
                        cursor: pointer; 
                        font-weight: 600;
                        transition: all 0.3s ease;
                    " onclick="selectAmount(this, 50000)">
                        TZS 50,000
                    </button>
                </div>
                
                <input type="number" name="custom_amount" id="custom_amount" placeholder="Enter custom amount" style="
                    width: 100%; 
                    padding: 12px; 
                    border: 2px solid #ddd; 
                    border-radius: 6px; 
                    font-size: 16px;
                    box-sizing: border-box;
                " min="1000" step="1000">
            </div>
            
            <!-- Donor Information -->
            <div class="form-section" style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 10px; font-size: 1rem;">
                    👤 Your Information
                </label>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <input type="text" name="first_name" placeholder="First Name" required style="
                        padding: 12px; 
                        border: 2px solid #ddd; 
                        border-radius: 6px; 
                        font-size: 16px;
                    ">
                    <input type="text" name="last_name" placeholder="Last Name" required style="
                        padding: 12px; 
                        border: 2px solid #ddd; 
                        border-radius: 6px; 
                        font-size: 16px;
                    ">
                </div>
                
                <input type="email" name="email" placeholder="Email Address" required style="
                    width: 100%; 
                    padding: 12px; 
                    border: 2px solid #ddd; 
                    border-radius: 6px; 
                    font-size: 16px;
                    box-sizing: border-box;
                    margin-bottom: 15px;
                ">
                
                <input type="tel" name="phone" placeholder="Phone Number (+255...)" style="
                    width: 100%; 
                    padding: 12px; 
                    border: 2px solid #ddd; 
                    border-radius: 6px; 
                    font-size: 16px;
                    box-sizing: border-box;
                ">
            </div>
            
            <!-- Payment Method -->
            <div class="form-section" style="margin-bottom: 25px;">
                <label style="display: block; font-weight: 600; color: #333; margin-bottom: 10px; font-size: 1rem;">
                    💳 Payment Method
                </label>
                
                <div style="display: grid; gap: 10px;">
                    <label style="
                        display: flex; 
                        align-items: center; 
                        padding: 15px; 
                        border: 2px solid #ddd; 
                        border-radius: 6px; 
                        cursor: pointer;
                    ">
                        <input type="radio" name="payment_method" value="mobile_money" checked style="margin-right: 10px;">
                        <i class="fas fa-mobile-alt" style="color: #28a745; margin-right: 10px; font-size: 1.2rem;"></i>
                        <div>
                            <div style="font-weight: 600;">Mobile Money</div>
                            <div style="font-size: 0.9rem; color: #6c757d;">M-Pesa, Tigo Pesa, Airtel Money</div>
                        </div>
                    </label>
                    
                    <label style="
                        display: flex; 
                        align-items: center; 
                        padding: 15px; 
                        border: 2px solid #ddd; 
                        border-radius: 6px; 
                        cursor: pointer;
                    ">
                        <input type="radio" name="payment_method" value="paypal" style="margin-right: 10px;">
                        <i class="fab fa-paypal" style="color: #0070ba; margin-right: 10px; font-size: 1.2rem;"></i>
                        <div>
                            <div style="font-weight: 600;">PayPal / Credit Card</div>
                            <div style="font-size: 0.9rem; color: #6c757d;">International payments</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" id="donate-btn" style="
                width: 100%; 
                padding: 18px; 
                background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                color: white; 
                border: none; 
                border-radius: 8px; 
                font-size: 18px; 
                font-weight: 700; 
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 10px;
            " onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                <i class="fas fa-heart"></i>
                Donate Now
            </button>
            
        </form>
        
        <?php if ($atts['show_test'] === 'true'): ?>
        <!-- Test Information -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid #007cba;">
            <h4 style="margin: 0 0 10px 0; color: #007cba;">🔧 Test Information</h4>
            <p style="margin: 0; font-size: 0.9rem; color: #6c757d;">
                This is a test donation form. Form visibility: <strong>WORKING</strong><br>
                Current time: <?php echo current_time('mysql'); ?><br>
                WordPress loaded: <strong>YES</strong><br>
                User agent: <?php echo substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 50); ?>...
            </p>
        </div>
        <?php endif; ?>
        
    </div>
    
    <script>
    // Simple JavaScript for form functionality
    function selectAmount(button, amount) {
        // Remove active class from all buttons
        document.querySelectorAll('.amount-btn').forEach(btn => {
            btn.style.background = 'white';
            btn.style.borderColor = '#ddd';
            btn.style.color = '#333';
        });
        
        // Activate selected button
        button.style.background = '#28a745';
        button.style.borderColor = '#28a745';
        button.style.color = 'white';
        
        // Clear custom amount
        document.getElementById('custom_amount').value = amount;
    }
    
    // Form submission
    document.getElementById('simple-donation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const amount = formData.get('custom_amount') || '0';
        const firstName = formData.get('first_name');
        const lastName = formData.get('last_name');
        const email = formData.get('email');
        const paymentMethod = formData.get('payment_method');
        
        if (!amount || amount < 1000) {
            alert('Please select a donation amount of at least TZS 1,000');
            return;
        }
        
        if (!firstName || !lastName || !email) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Show processing state
        const btn = document.getElementById('donate-btn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        btn.disabled = true;
        
        // Simulate processing
        setTimeout(() => {
            alert(`Test Donation Processed!\n\nAmount: TZS ${parseInt(amount).toLocaleString()}\nDonor: ${firstName} ${lastName}\nEmail: ${email}\nMethod: ${paymentMethod}\n\nThis is a test form - no actual payment was processed.`);
            
            // Reset button
            btn.innerHTML = '<i class="fas fa-heart"></i> Donate Now';
            btn.disabled = false;
        }, 2000);
    });
    
    // Console log for debugging
    console.log('KiliSmile Simple Donation Form Loaded Successfully');
    console.log('Form element:', document.getElementById('kilismile-simple-donation-form'));
    </script>
    
    <style>
    /* Form styling */
    #kilismile-simple-donation-form input:focus,
    #kilismile-simple-donation-form button:focus {
        outline: none;
        border-color: #28a745 !important;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }
    
    #kilismile-simple-donation-form .amount-btn:hover {
        background: #f8f9fa !important;
        border-color: #28a745 !important;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        #kilismile-simple-donation-form {
            margin: 10px;
            padding: 20px;
        }
        
        #kilismile-simple-donation-form .amount-options {
            grid-template-columns: repeat(2, 1fr);
        }
        
        #kilismile-simple-donation-form div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('kilismile_simple_form', 'kilismile_simple_donation_form');

// Also create an admin notice if this file is loaded
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        echo '<div class="notice notice-info"><p><strong>KiliSmile:</strong> Simple donation form shortcode loaded. Use <code>[kilismile_simple_form]</code> to display it.</p></div>';
    }
});

?>