<?php
/**
 * Template Name: Donation Success
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Success Hero Section -->
    <section class="success-hero" style="background: linear-gradient(135deg, var(--primary-green), var(--accent-green)); color: white; padding: 140px 0 80px; text-align: center;">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto;">
                <div style="width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    <i class="fas fa-check" style="color: var(--primary-green); font-size: 3rem;"></i>
                </div>
                
                <h1 style="font-size: clamp(2rem, 4vw, 2.8rem); margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Thank You for Your Generous Donation!', 'kilismile'); ?>
                </h1>
                
                <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6; opacity: 0.95;">
                    <?php _e('Your support makes a real difference in the lives of people in Tanzania. We are grateful for your commitment to improving health education and community well-being.', 'kilismile'); ?>
                </p>
                
                <?php
                // Display donation details if available from AzamPay plugin
                $donation_id = isset($_GET['donation_id']) ? sanitize_text_field($_GET['donation_id']) : '';
                $amount = isset($_GET['amount']) ? sanitize_text_field($_GET['amount']) : '';
                $currency = isset($_GET['currency']) ? sanitize_text_field($_GET['currency']) : 'TZS';
                $donor_name = isset($_GET['donor']) ? sanitize_text_field($_GET['donor']) : '';
                $verify_mode = isset($_GET['verify']) && $_GET['verify'] === '1';
                $donation = null;
                $receipt_reference = '';
                $receipt_url = '';
                $receipt_submitted_at = '';
                $receipt_verified_at = '';
                $verification_status = '';
                $verification_message = '';
                $show_verification_section = false;
                $verification_url = '';
                $is_manual_transfer = false;

                if ($donation_id && class_exists('KiliSmile_Donation_Database')) {
                    $db = new KiliSmile_Donation_Database();
                    $donation = $db->get_donation($donation_id);
                    if (!empty($donation)) {
                        $receipt_reference = $db->get_donation_meta($donation_id, 'manual_receipt_reference');
                        $receipt_url = $db->get_donation_meta($donation_id, 'manual_receipt_file_url');
                        $receipt_submitted_at = $db->get_donation_meta($donation_id, 'manual_receipt_submitted_at');
                        $receipt_verified_at = $db->get_donation_meta($donation_id, 'manual_receipt_verified_at');

                        $is_manual_transfer = in_array($donation['payment_method'] ?? '', array('manual_transfer', 'bank_transfer'), true);
                        $show_verification_section = $is_manual_transfer || $verify_mode;

                        if (!empty($receipt_verified_at) || ($donation['status'] ?? '') === 'completed') {
                            $verification_status = __('Verified', 'kilismile');
                            $verification_message = __('Your transfer has been verified. Thank you!', 'kilismile');
                        } elseif (!empty($receipt_reference) || !empty($receipt_submitted_at) || !empty($receipt_url)) {
                            $verification_status = __('Under Review', 'kilismile');
                            $verification_message = __('We received your receipt and are verifying it now.', 'kilismile');
                        } elseif (($donation['status'] ?? '') === 'pending_verification' || ($donation['status'] ?? '') === 'pending') {
                            $verification_status = __('Awaiting Receipt', 'kilismile');
                            $verification_message = __('Please submit your transfer reference or receipt to complete verification.', 'kilismile');
                        } else {
                            $verification_status = __('Processing', 'kilismile');
                            $verification_message = __('We are processing your donation details.', 'kilismile');
                        }
                    } else {
                        $show_verification_section = $verify_mode;
                        $verification_status = __('Not Found', 'kilismile');
                        $verification_message = __('We could not find this donation. Please check your Donation ID.', 'kilismile');
                    }
                } elseif ($verify_mode) {
                    $show_verification_section = true;
                    $verification_status = __('Missing Donation ID', 'kilismile');
                    $verification_message = __('Please open the link with your Donation ID so we can verify your transfer.', 'kilismile');
                }

                if ($donation_id && $is_manual_transfer) {
                    $verification_url = add_query_arg(
                        array('donation_id' => $donation_id, 'verify' => '1'),
                        get_permalink()
                    ) . '#manual-verification';
                }
                
                if ($donation_id || $amount) :
                ?>
                <div style="background: rgba(255,255,255,0.15); padding: 25px; border-radius: 12px; backdrop-filter: blur(10px); margin-bottom: 30px;">
                    <?php if ($amount) : ?>
                        <div style="font-size: 2rem; font-weight: 700; margin-bottom: 10px;">
                            <?php echo esc_html($currency . ' ' . number_format((float)$amount)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($donor_name) : ?>
                        <div style="font-size: 1.1rem; margin-bottom: 10px; opacity: 0.9;">
                            <?php echo sprintf(__('Thank you, %s!', 'kilismile'), esc_html($donor_name)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($donation_id) : ?>
                        <div style="font-size: 0.9rem; opacity: 0.8;">
                            <?php echo sprintf(__('Donation ID: %s', 'kilismile'), esc_html($donation_id)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; backdrop-filter: blur(10px); margin-bottom: 30px;">
                    <p style="margin: 0; font-size: 1rem; opacity: 0.9;">
                        <?php _e('A confirmation email has been sent to your email address with your donation receipt and transaction details.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php if ($show_verification_section) : ?>
    <section id="manual-verification" class="verification-section" style="padding: 60px 0; background: #f5f7fb;">
        <div class="container" style="max-width: 1100px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 35px;">
                <h2 style="color: var(--dark-green); font-size: 1.9rem; margin-bottom: 10px; font-weight: 600;">
                    <?php _e('Verify Your Transfer', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1rem; margin: 0 auto; max-width: 700px; line-height: 1.6;">
                    <?php _e('If you completed a manual bank/mobile transfer, submit your receipt to speed up verification. You can scan the QR code with your phone if you paid on a PC.', 'kilismile'); ?>
                </p>
            </div>

            <div class="verification-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px; align-items: stretch;">
                <div class="verification-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); border: 1px solid #e6ebf5;">
                    <h3 style="margin: 0 0 12px; color: var(--dark-green); font-size: 1.2rem;">
                        <?php _e('Verification Status', 'kilismile'); ?>
                    </h3>
                    <div class="verification-status" style="display: inline-flex; align-items: center; gap: 8px; background: #eef6ff; color: #1d4ed8; padding: 6px 12px; border-radius: 999px; font-weight: 600; font-size: 0.9rem; margin-bottom: 12px;">
                        <i class="fas fa-shield-check"></i>
                        <?php echo esc_html($verification_status); ?>
                    </div>
                    <p style="margin: 0 0 15px; color: var(--text-secondary); line-height: 1.6;">
                        <?php echo esc_html($verification_message); ?>
                    </p>

                    <?php if ($donation_id) : ?>
                        <div style="background: #f7f9fc; padding: 12px; border-radius: 8px; border: 1px dashed #c9d6ee; font-size: 0.95rem;">
                            <strong><?php _e('Donation ID:', 'kilismile'); ?></strong>
                            <span><?php echo esc_html($donation_id); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($receipt_reference)) : ?>
                        <div style="margin-top: 12px; font-size: 0.9rem; color: var(--text-secondary);">
                            <strong><?php _e('Receipt Reference:', 'kilismile'); ?></strong>
                            <?php echo esc_html($receipt_reference); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($receipt_url)) : ?>
                        <div style="margin-top: 8px;">
                            <a href="<?php echo esc_url($receipt_url); ?>" target="_blank" style="color: var(--primary-green); font-weight: 600;">
                                <?php _e('View submitted receipt', 'kilismile'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="verification-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); border: 1px solid #e6ebf5;">
                    <h3 style="margin: 0 0 12px; color: var(--dark-green); font-size: 1.2rem;">
                        <?php _e('Scan to Upload Receipt', 'kilismile'); ?>
                    </h3>
                    <?php if (!empty($verification_url)) : ?>
                        <div class="qr-box" style="display: flex; flex-direction: column; align-items: center; gap: 12px;">
                            <img src="<?php echo esc_url('https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' . rawurlencode($verification_url)); ?>" alt="<?php esc_attr_e('Verification QR Code', 'kilismile'); ?>" style="width: 220px; height: 220px; border-radius: 12px; border: 1px solid #e6ebf5;">
                            <a href="<?php echo esc_url($verification_url); ?>" class="btn btn-primary" style="padding: 10px 18px; background: var(--primary-green); color: white; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                <?php _e('Open Verification Form', 'kilismile'); ?>
                            </a>
                            <p style="margin: 0; font-size: 0.85rem; color: var(--text-secondary); text-align: center;">
                                <?php _e('Scan with your phone camera to upload the receipt quickly.', 'kilismile'); ?>
                            </p>
                        </div>
                    <?php elseif ($donation_id && !$is_manual_transfer) : ?>
                        <p style="color: var(--text-secondary); margin: 0;">
                            <?php _e('This payment method does not require manual verification.', 'kilismile'); ?>
                        </p>
                    <?php else : ?>
                        <p style="color: var(--text-secondary); margin: 0;">
                            <?php _e('Verification link will appear once your Donation ID is available.', 'kilismile'); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="verification-card" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); border: 1px solid #e6ebf5;">
                    <h3 style="margin: 0 0 12px; color: var(--dark-green); font-size: 1.2rem;">
                        <?php _e('Submit Receipt', 'kilismile'); ?>
                    </h3>
                    <?php if ($donation_id && $is_manual_transfer) : ?>
                        <form id="manual-receipt-form" enctype="multipart/form-data">
                            <input type="hidden" name="donation_id" value="<?php echo esc_attr($donation_id); ?>">
                            <div style="display: grid; gap: 12px;">
                                <input type="text" name="receipt_reference" placeholder="<?php esc_attr_e('Transaction reference / receipt ID', 'kilismile'); ?>" required style="padding: 10px 12px; border-radius: 8px; border: 1px solid #cfe0f2; font-size: 0.95rem;">
                                <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf" style="padding: 6px;">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 16px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                                    <?php _e('Submit Receipt', 'kilismile'); ?>
                                </button>
                                <div class="receipt-upload-status" style="font-size: 0.9rem; color: #1f5f99;"></div>
                            </div>
                        </form>
                        <p style="margin-top: 12px; font-size: 0.85rem; color: var(--text-secondary);">
                            <?php _e('We accept JPG, PNG, or PDF files up to 5MB.', 'kilismile'); ?>
                        </p>
                    <?php elseif ($donation_id) : ?>
                        <p style="margin: 0; color: var(--text-secondary);">
                            <?php _e('Receipt submission is only needed for manual transfers.', 'kilismile'); ?>
                        </p>
                    <?php else : ?>
                        <p style="margin: 0; color: var(--text-secondary);">
                            <?php _e('We need your Donation ID to accept a receipt. Please open this page using the QR link from your confirmation page.', 'kilismile'); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- What Happens Next Section -->
    <section class="next-steps" style="padding: 80px 0; background: white;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="color: var(--dark-green); font-size: 2rem; margin-bottom: 15px; font-weight: 600;">
                    <?php _e('What Happens Next', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Here\'s how your donation will be put to work in our mission to improve health education in Tanzania.', 'kilismile'); ?>
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <!-- Step 1 -->
                <div class="step-card" style="background: #f8f9fa; padding: 30px; border-radius: 12px; text-align: center; border-top: 4px solid var(--primary-green);">
                    <div style="width: 60px; height: 60px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="color: white; font-size: 1.5rem; font-weight: bold;">1</span>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; font-weight: 600;">
                        <?php _e('Immediate Processing', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        <?php _e('Your donation is immediately allocated to our most urgent health education programs and community outreach initiatives.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="step-card" style="background: #f8f9fa; padding: 30px; border-radius: 12px; text-align: center; border-top: 4px solid var(--accent-green);">
                    <div style="width: 60px; height: 60px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="color: white; font-size: 1.5rem; font-weight: bold;">2</span>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; font-weight: 600;">
                        <?php _e('Program Implementation', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        <?php _e('Within 30 days, your contribution will be actively supporting health screenings, educational workshops, and community programs.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="step-card" style="background: #f8f9fa; padding: 30px; border-radius: 12px; text-align: center; border-top: 4px solid var(--light-green);">
                    <div style="width: 60px; height: 60px; background: var(--light-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="color: white; font-size: 1.5rem; font-weight: bold;">3</span>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; font-weight: 600;">
                        <?php _e('Impact Updates', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        <?php _e('You\'ll receive quarterly updates showing exactly how your donation has helped improve lives in Tanzanian communities.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Share Success Section -->
    <section class="share-success" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <h2 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 20px; font-weight: 600;">
                <?php _e('Help Us Spread the Word', 'kilismile'); ?>
            </h2>
            <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 30px; line-height: 1.6;">
                <?php _e('Share your support and inspire others to join our mission of improving health education in Tanzania.', 'kilismile'); ?>
            </p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 30px;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(home_url('/donate')); ?>" 
                   target="_blank"
                   class="social-share-btn" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #3b5998; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-facebook-f"></i>
                    <?php _e('Share on Facebook', 'kilismile'); ?>
                </a>
                
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(__('I just donated to @KiliSmile to support health education in Tanzania! Join me: ', 'kilismile')); ?>&url=<?php echo urlencode(home_url('/donate')); ?>" 
                   target="_blank"
                   class="social-share-btn" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #1da1f2; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-twitter"></i>
                    <?php _e('Share on Twitter', 'kilismile'); ?>
                </a>
                
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(home_url('/donate')); ?>" 
                   target="_blank"
                   class="social-share-btn" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #0077b5; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-linkedin-in"></i>
                    <?php _e('Share on LinkedIn', 'kilismile'); ?>
                </a>
            </div>
            
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid var(--primary-green);">
                <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem;">
                    <?php _e('Invite Friends to Donate', 'kilismile'); ?>
                </h3>
                <p style="color: var(--text-secondary); margin-bottom: 15px; line-height: 1.5;">
                    <?php _e('Copy this link to share with friends and family:', 'kilismile'); ?>
                </p>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" 
                           value="<?php echo home_url('/donate'); ?>" 
                           readonly
                           id="share-link"
                           style="flex: 1; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; background: #f8f9fa;">
                    <button onclick="copyShareLink()" 
                            class="btn btn-primary" 
                            style="padding: 10px 20px; background: var(--primary-green); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-copy"></i>
                        <?php _e('Copy', 'kilismile'); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Continue Engagement Section -->
    <section class="continue-engagement" style="padding: 60px 0; background: white;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 15px; font-weight: 600;">
                    <?php _e('Stay Connected with Our Mission', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('There are many ways to continue supporting our work beyond financial donations.', 'kilismile'); ?>
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
                <!-- Newsletter -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--primary-green);">
                    <i class="fas fa-envelope" style="font-size: 2.5rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Newsletter Updates', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Get monthly updates on our programs and see your impact in action.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo home_url('/newsletter'); ?>" 
                       class="btn btn-outline" 
                       style="display: inline-block; padding: 10px 20px; border: 2px solid var(--primary-green); color: var(--primary-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <?php _e('Subscribe', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Volunteer -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--accent-green);">
                    <i class="fas fa-hands-helping" style="font-size: 2.5rem; color: var(--accent-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Volunteer Opportunities', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Donate your time and skills to directly help our programs.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo home_url('/volunteer'); ?>" 
                       class="btn btn-outline" 
                       style="display: inline-block; padding: 10px 20px; border: 2px solid var(--accent-green); color: var(--accent-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <?php _e('Learn More', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Follow Us -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--light-green);">
                    <i class="fas fa-share-alt" style="font-size: 2.5rem; color: var(--light-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Follow Our Journey', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Stay updated on social media for daily stories and updates.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="#" style="color: var(--light-green); font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--light-green); font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: var(--light-green); font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Corporate -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--dark-green);">
                    <i class="fas fa-building" style="font-size: 2.5rem; color: var(--dark-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Corporate Partnership', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Explore partnership opportunities for your organization.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo home_url('/partnerships'); ?>" 
                       class="btn btn-outline" 
                       style="display: inline-block; padding: 10px 20px; border: 2px solid var(--dark-green); color: var(--dark-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <?php _e('Partner With Us', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta" style="padding: 50px 0; background: var(--primary-green); color: white; text-align: center;">
        <div class="container">
            <h2 style="font-size: 1.5rem; margin-bottom: 15px; color: white; font-weight: 600;">
                <?php _e('Thank You for Being Part of the Solution', 'kilismile'); ?>
            </h2>
            <p style="margin-bottom: 25px; opacity: 0.95; line-height: 1.5; max-width: 600px; margin-left: auto; margin-right: auto;">
                <?php _e('Together, we\'re building healthier communities and brighter futures across Tanzania. Your generosity makes it all possible.', 'kilismile'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/')); ?>" 
               class="btn btn-secondary" 
               style="display: inline-block; padding: 12px 25px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                <?php _e('Return to Homepage', 'kilismile'); ?>
            </a>
        </div>
    </section>
</main>

<script>
function copyShareLink() {
    const shareInput = document.getElementById('share-link');
    shareInput.select();
    shareInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> <?php _e('Copied!', 'kilismile'); ?>';
        button.style.background = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = 'var(--primary-green)';
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.step-card, .engagement-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    cards.forEach((card) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    const receiptForm = document.getElementById('manual-receipt-form');
    if (receiptForm) {
        receiptForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const statusEl = receiptForm.querySelector('.receipt-upload-status');
            const submitBtn = receiptForm.querySelector('button[type="submit"]');
            const donationId = receiptForm.querySelector('input[name="donation_id"]').value.trim();
            const reference = receiptForm.querySelector('input[name="receipt_reference"]').value.trim();

            if (!donationId || !reference) {
                statusEl.style.color = '#dc3545';
                statusEl.textContent = '<?php echo esc_js(__('Donation ID and receipt reference are required.', 'kilismile')); ?>';
                return;
            }

            const formData = new FormData(receiptForm);
            formData.append('action', 'kilismile_submit_manual_receipt');
            formData.append('nonce', '<?php echo wp_create_nonce('kilismile_manual_receipt'); ?>');

            statusEl.style.color = '#1f5f99';
            statusEl.textContent = '<?php echo esc_js(__('Submitting receipt...', 'kilismile')); ?>';
            if (submitBtn) submitBtn.disabled = true;

            fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusEl.style.color = '#28a745';
                        statusEl.textContent = data.data.message || '<?php echo esc_js(__('Receipt submitted successfully.', 'kilismile')); ?>';
                    } else {
                        statusEl.style.color = '#dc3545';
                        statusEl.textContent = (data.data && data.data.message) ? data.data.message : '<?php echo esc_js(__('Submission failed. Please try again.', 'kilismile')); ?>';
                    }
                })
                .catch(() => {
                    statusEl.style.color = '#dc3545';
                    statusEl.textContent = '<?php echo esc_js(__('Something went wrong. Please try again.', 'kilismile')); ?>';
                })
                .finally(() => {
                    if (submitBtn) submitBtn.disabled = false;
                });
        });
    }
});
</script>

<style>
.social-share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.engagement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.step-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .success-hero {
        padding-top: 120px !important;
    }
    
    .success-hero h1 {
        font-size: 1.8rem !important;
    }
    
    .next-steps,
    .share-success,
    .continue-engagement {
        padding: 50px 0 !important;
    }
    
    .social-share-btn {
        font-size: 0.9rem !important;
        padding: 10px 15px !important;
    }

    .verification-section {
        padding: 45px 0 !important;
    }

    .verification-section .qr-box img {
        width: 180px !important;
        height: 180px !important;
    }
}
</style>

<?php get_footer(); ?>


