<?php
/**
 * Donation Cancelled Page Template
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

get_header(); 

// Get transaction ID from URL
$transaction_id = isset($_GET['transaction_id']) ? sanitize_text_field($_GET['transaction_id']) : '';
?>

<main id="main" class="site-main">
    <div class="donation-cancelled-container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #fff8f8 0%, #fef2f2 100%); padding: 40px 20px;">
        <div style="max-width: 600px; width: 100%; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; text-align: center;">
            
            <!-- Cancelled Header -->
            <div style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; padding: 40px 30px;">
                <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                    <i class="fas fa-times" style="color: #dc3545; font-size: 2.5rem;"></i>
                </div>
                <h1 style="margin: 0 0 10px 0; font-size: 2.2rem; font-weight: 700;">
                    <?php _e('Payment Cancelled', 'kilismile'); ?>
                </h1>
                <p style="margin: 0; font-size: 1.1rem; opacity: 0.9;">
                    <?php _e('Your donation was not completed', 'kilismile'); ?>
                </p>
            </div>
            
            <!-- Content -->
            <div style="padding: 40px 30px;">
                <?php if ($transaction_id): ?>
                <div style="background: #f8f9fa; border-radius: 12px; padding: 25px; margin-bottom: 30px; border-left: 4px solid #6c757d;">
                    <h3 style="color: #495057; margin: 0 0 15px 0; font-size: 1.3rem;">
                        <?php _e('Transaction Details', 'kilismile'); ?>
                    </h3>
                    <p style="color: #6c757d; margin: 0;">
                        <strong><?php _e('Transaction ID:', 'kilismile'); ?></strong> <?php echo esc_html($transaction_id); ?>
                    </p>
                </div>
                <?php endif; ?>

                <!-- Explanation -->
                <div style="margin-bottom: 30px; padding: 25px; background: #fff3cd; border-radius: 12px; border-left: 4px solid #ffc107;">
                    <h4 style="color: #856404; margin: 0 0 15px 0; font-size: 1.2rem;">
                        <i class="fas fa-info-circle" style="margin-right: 8px;"></i>
                        <?php _e('What happened?', 'kilismile'); ?>
                    </h4>
                    <p style="color: #856404; margin: 0; line-height: 1.6;">
                        <?php _e('You cancelled the payment process or the payment was not completed. No charges have been made to your account.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Encouragement Message -->
                <div style="margin-bottom: 30px; padding: 25px; background: linear-gradient(135deg, #e8f5e8, #f0fff0); border-radius: 12px; border: 1px solid #c3e6cb;">
                    <h4 style="color: #155724; margin: 0 0 15px 0; font-size: 1.2rem;">
                        <i class="fas fa-heart" style="color: #dc3545; margin-right: 8px;"></i>
                        <?php _e('We still need your support', 'kilismile'); ?>
                    </h4>
                    <p style="color: #155724; margin: 0; line-height: 1.6;">
                        <?php _e('Your donation helps us provide essential healthcare education and support to underserved communities in Tanzania. Every contribution makes a difference.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center; margin-bottom: 30px;">
                    <a href="<?php echo home_url('/donate'); ?>" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 15px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; transition: all 0.3s ease;">
                        <i class="fas fa-heart" style="margin-right: 8px;"></i>
                        <?php _e('Try Again', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo home_url(); ?>" style="background: #6c757d; color: white; padding: 15px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; transition: all 0.3s ease;">
                        <i class="fas fa-home" style="margin-right: 8px;"></i>
                        <?php _e('Back to Home', 'kilismile'); ?>
                    </a>
                    
                    <a href="<?php echo home_url('/contact'); ?>" style="background: #17a2b8; color: white; padding: 15px 25px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; transition: all 0.3s ease;">
                        <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                        <?php _e('Contact Us', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Alternative Ways to Help -->
                <div style="background: #e7f3ff; border-radius: 12px; padding: 25px; border-left: 4px solid #007bff;">
                    <h4 style="color: #004085; margin: 0 0 15px 0; font-size: 1.2rem;">
                        <i class="fas fa-hands-helping" style="margin-right: 8px;"></i>
                        <?php _e('Other Ways to Help', 'kilismile'); ?>
                    </h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
                        <div style="text-align: left;">
                            <strong style="color: #004085; display: block; margin-bottom: 5px;">
                                <i class="fas fa-share-alt" style="margin-right: 5px;"></i>
                                <?php _e('Share Our Mission', 'kilismile'); ?>
                            </strong>
                            <p style="color: #004085; margin: 0; font-size: 0.9rem;">
                                <?php _e('Spread the word about our work on social media', 'kilismile'); ?>
                            </p>
                        </div>
                        <div style="text-align: left;">
                            <strong style="color: #004085; display: block; margin-bottom: 5px;">
                                <i class="fas fa-user-friends" style="margin-right: 5px;"></i>
                                <?php _e('Volunteer', 'kilismile'); ?>
                            </strong>
                            <p style="color: #004085; margin: 0; font-size: 0.9rem;">
                                <?php _e('Join our team of dedicated volunteers', 'kilismile'); ?>
                            </p>
                        </div>
                        <div style="text-align: left;">
                            <strong style="color: #004085; display: block; margin-bottom: 5px;">
                                <i class="fas fa-lightbulb" style="margin-right: 5px;"></i>
                                <?php _e('Partner With Us', 'kilismile'); ?>
                            </strong>
                            <p style="color: #004085; margin: 0; font-size: 0.9rem;">
                                <?php _e('Explore corporate partnership opportunities', 'kilismile'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>