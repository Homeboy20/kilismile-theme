<?php
/**
 * Donation Page Template
 * Template Name: Donation Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="page-hero" style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%); color: white; padding: 120px 0 80px; text-align: center;">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 20px; color: white;">
                <?php _e('Support Our Mission', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 600px; margin: 0 auto; opacity: 0.9;">
                <?php _e('Help us bring essential health education and services to remote communities in Tanzania. Every donation makes a difference.', 'kilismile'); ?>
            </p>
        </div>
    </section>

    <div class="container" style="padding: 80px 20px;">
        <!-- Donation Impact Section -->
        <section class="donation-impact" style="margin-bottom: 80px;">
            <div class="row" style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap;">
                <div class="col-md-6" style="flex: 1; min-width: 300px;">
                    <h2 style="color: var(--dark-green); margin-bottom: 30px;">
                        <?php _e('Your Impact', 'kilismile'); ?>
                    </h2>
                    
                    <div class="impact-items">
                        <div class="impact-item" style="display: flex; align-items: center; margin-bottom: 25px; padding: 20px; background: var(--light-gray); border-radius: 10px;">
                            <div class="impact-amount" style="background: var(--primary-green); color: white; padding: 15px; border-radius: 50%; margin-right: 20px; font-weight: bold; min-width: 60px; text-align: center;">
                                $25
                            </div>
                            <div class="impact-description">
                                <strong><?php _e('Oral Health Kit', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Provides oral health supplies for 5 children', 'kilismile'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="impact-item" style="display: flex; align-items: center; margin-bottom: 25px; padding: 20px; background: var(--light-gray); border-radius: 10px;">
                            <div class="impact-amount" style="background: var(--primary-green); color: white; padding: 15px; border-radius: 50%; margin-right: 20px; font-weight: bold; min-width: 60px; text-align: center;">
                                $50
                            </div>
                            <div class="impact-description">
                                <strong><?php _e('Teacher Training', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Trains one teacher in basic health education', 'kilismile'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="impact-item" style="display: flex; align-items: center; margin-bottom: 25px; padding: 20px; background: var(--light-gray); border-radius: 10px;">
                            <div class="impact-amount" style="background: var(--primary-green); color: white; padding: 15px; border-radius: 50%; margin-right: 20px; font-weight: bold; min-width: 60px; text-align: center;">
                                $100
                            </div>
                            <div class="impact-description">
                                <strong><?php _e('Health Screening', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Provides health screening for 20 elderly people', 'kilismile'); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="impact-item" style="display: flex; align-items: center; margin-bottom: 25px; padding: 20px; background: var(--light-gray); border-radius: 10px;">
                            <div class="impact-amount" style="background: var(--primary-green); color: white; padding: 15px; border-radius: 50%; margin-right: 20px; font-weight: bold; min-width: 60px; text-align: center;">
                                $250
                            </div>
                            <div class="impact-description">
                                <strong><?php _e('Community Outreach', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Funds a complete health education program in one remote area', 'kilismile'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" style="flex: 1; min-width: 300px;">
                    <!-- Donation Form -->
                    <?php echo kilismile_donation_form(array(
                        'title' => __('Make Your Donation', 'kilismile'),
                        'show_amounts' => true,
                        'show_progress' => true
                    )); ?>
                </div>
            </div>
        </section>

        <!-- Donation Methods Section -->
        <section class="donation-methods-info" style="margin-bottom: 80px; background: var(--light-gray); padding: 60px 40px; border-radius: 15px;">
            <h2 style="text-align: center; color: var(--dark-green); margin-bottom: 40px;">
                <?php _e('How to Donate', 'kilismile'); ?>
            </h2>
            
            <div class="methods-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                <?php if (get_theme_mod('kilismile_paypal_email')) : ?>
                    <div class="method-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <i class="fab fa-paypal" style="font-size: 3rem; color: #0070ba; margin-bottom: 20px;"></i>
                        <h3 style="margin-bottom: 15px; color: var(--dark-green);"><?php _e('PayPal', 'kilismile'); ?></h3>
                        <p style="color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('Secure online donations using PayPal. Accepts credit cards and PayPal accounts worldwide.', 'kilismile'); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (get_theme_mod('kilismile_mpesa_number')) : ?>
                    <div class="method-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <i class="fas fa-mobile-alt" style="font-size: 3rem; color: #00a651; margin-bottom: 20px;"></i>
                        <h3 style="margin-bottom: 15px; color: var(--dark-green);"><?php _e('M-Pesa', 'kilismile'); ?></h3>
                        <p style="color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('For our Tanzanian supporters, donate easily using M-Pesa mobile money service.', 'kilismile'); ?>
                        </p>
                        <p style="font-weight: bold; color: var(--primary-green);">
                            <?php echo esc_html(get_theme_mod('kilismile_mpesa_number')); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <?php if (get_theme_mod('kilismile_bank_details')) : ?>
                    <div class="method-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <i class="fas fa-university" style="font-size: 3rem; color: #333; margin-bottom: 20px;"></i>
                        <h3 style="margin-bottom: 15px; color: var(--dark-green);"><?php _e('Bank Transfer', 'kilismile'); ?></h3>
                        <p style="color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('Direct bank transfer for larger donations. Contact us for detailed banking information.', 'kilismile'); ?>
                        </p>
                    </div>
                <?php endif; ?>
                
                <div class="method-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <i class="fas fa-handshake" style="font-size: 3rem; color: var(--primary-green); margin-bottom: 20px;"></i>
                    <h3 style="margin-bottom: 15px; color: var(--dark-green);"><?php _e('Corporate Sponsorship', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?php _e('Partner with us for larger impact. Contact us to discuss corporate sponsorship opportunities.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" class="btn btn-primary" style="margin-top: 15px; padding: 10px 20px;">
                        <?php _e('Contact Us', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- Transparency Section -->
        <section class="transparency-section" style="margin-bottom: 80px;">
            <h2 style="text-align: center; color: var(--dark-green); margin-bottom: 40px;">
                <?php _e('Transparency & Accountability', 'kilismile'); ?>
            </h2>
            
            <div class="row" style="display: flex; gap: 40px; align-items: center; flex-wrap: wrap;">
                <div class="col-md-6" style="flex: 1; min-width: 300px;">
                    <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                        <?php _e('How We Use Your Donations', 'kilismile'); ?>
                    </h3>
                    
                    <div class="expense-breakdown" style="background: var(--light-gray); padding: 30px; border-radius: 15px;">
                        <div class="expense-item" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                            <span><?php _e('Direct Program Services', 'kilismile'); ?></span>
                            <strong style="color: var(--primary-green);">75%</strong>
                        </div>
                        <div class="expense-item" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                            <span><?php _e('Medical Supplies & Equipment', 'kilismile'); ?></span>
                            <strong style="color: var(--primary-green);">15%</strong>
                        </div>
                        <div class="expense-item" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #ddd;">
                            <span><?php _e('Administrative Costs', 'kilismile'); ?></span>
                            <strong style="color: var(--primary-green);">7%</strong>
                        </div>
                        <div class="expense-item" style="display: flex; justify-content: space-between; align-items: center;">
                            <span><?php _e('Fundraising & Awareness', 'kilismile'); ?></span>
                            <strong style="color: var(--primary-green);">3%</strong>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6" style="flex: 1; min-width: 300px;">
                    <h3 style="color: var(--dark-green); margin-bottom: 20px;">
                        <?php _e('Our Commitment', 'kilismile'); ?>
                    </h3>
                    
                    <ul style="list-style: none; padding: 0;">
                        <li style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                            <i class="fas fa-check-circle" style="color: var(--primary-green); margin-right: 15px; margin-top: 5px; font-size: 1.2rem;"></i>
                            <div>
                                <strong><?php _e('Regular Financial Reports', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Quarterly financial reports available to all donors', 'kilismile'); ?>
                                </p>
                            </div>
                        </li>
                        <li style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                            <i class="fas fa-check-circle" style="color: var(--primary-green); margin-right: 15px; margin-top: 5px; font-size: 1.2rem;"></i>
                            <div>
                                <strong><?php _e('Impact Documentation', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Photo and video documentation of all programs', 'kilismile'); ?>
                                </p>
                            </div>
                        </li>
                        <li style="display: flex; align-items: flex-start; margin-bottom: 20px;">
                            <i class="fas fa-check-circle" style="color: var(--primary-green); margin-right: 15px; margin-top: 5px; font-size: 1.2rem;"></i>
                            <div>
                                <strong><?php _e('Donor Updates', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Regular updates on how your donation is making a difference', 'kilismile'); ?>
                                </p>
                            </div>
                        </li>
                        <li style="display: flex; align-items: flex-start;">
                            <i class="fas fa-check-circle" style="color: var(--primary-green); margin-right: 15px; margin-top: 5px; font-size: 1.2rem;"></i>
                            <div>
                                <strong><?php _e('External Audits', 'kilismile'); ?></strong>
                                <p style="margin: 5px 0 0; color: var(--text-secondary);">
                                    <?php _e('Annual independent financial audits for transparency', 'kilismile'); ?>
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section class="donor-testimonials" style="background: var(--light-gray); padding: 60px 40px; border-radius: 15px; margin-bottom: 80px;">
            <h2 style="text-align: center; color: var(--dark-green); margin-bottom: 40px;">
                <?php _e('What Our Supporters Say', 'kilismile'); ?>
            </h2>
            
            <div class="testimonials-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div class="testimonial-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
                    <blockquote style="font-style: italic; margin-bottom: 20px; color: var(--text-secondary);">
                        "Supporting Kili Smile has been incredibly rewarding. Seeing the impact of our donations on children's health in remote areas gives me hope for a better future."
                    </blockquote>
                    <cite style="font-weight: 600; color: var(--dark-green);">
                        - Sarah M., Regular Donor
                    </cite>
                </div>
                
                <div class="testimonial-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
                    <blockquote style="font-style: italic; margin-bottom: 20px; color: var(--text-secondary);">
                        "The transparency and regular updates from Kili Smile make me confident that my donations are truly making a difference in Tanzania."
                    </blockquote>
                    <cite style="font-weight: 600; color: var(--dark-green);">
                        - Dr. James K., Monthly Supporter
                    </cite>
                </div>
                
                <div class="testimonial-card" style="background: white; padding: 30px; border-radius: 15px; text-align: center;">
                    <blockquote style="font-style: italic; margin-bottom: 20px; color: var(--text-secondary);">
                        "As a healthcare professional, I appreciate the focus on oral health education. This organization is doing vital work in underserved communities."
                    </blockquote>
                    <cite style="font-weight: 600; color: var(--dark-green);">
                        - Dr. Maria L., One-time Donor
                    </cite>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="donation-faq" style="margin-bottom: 80px;">
            <h2 style="text-align: center; color: var(--dark-green); margin-bottom: 40px;">
                <?php _e('Frequently Asked Questions', 'kilismile'); ?>
            </h2>
            
            <div class="faq-list" style="max-width: 800px; margin: 0 auto;">
                <details class="faq-item" style="margin-bottom: 20px; background: white; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px;">
                    <summary style="font-weight: 600; color: var(--dark-green); cursor: pointer; margin-bottom: 15px;">
                        <?php _e('Is my donation tax-deductible?', 'kilismile'); ?>
                    </summary>
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?php _e('As we are a registered NGO in Tanzania, tax deductibility depends on your country of residence. Please consult with a tax professional in your jurisdiction.', 'kilismile'); ?>
                    </p>
                </details>
                
                <details class="faq-item" style="margin-bottom: 20px; background: white; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px;">
                    <summary style="font-weight: 600; color: var(--dark-green); cursor: pointer; margin-bottom: 15px;">
                        <?php _e('How often will I receive updates?', 'kilismile'); ?>
                    </summary>
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?php _e('We send quarterly impact reports to all donors, plus special updates when we reach significant milestones or launch new programs.', 'kilismile'); ?>
                    </p>
                </details>
                
                <details class="faq-item" style="margin-bottom: 20px; background: white; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px;">
                    <summary style="font-weight: 600; color: var(--dark-green); cursor: pointer; margin-bottom: 15px;">
                        <?php _e('Can I designate my donation for a specific program?', 'kilismile'); ?>
                    </summary>
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?php _e('Yes! When making your donation, you can specify if you\'d like it to go toward oral health education, teacher training, or health screening programs.', 'kilismile'); ?>
                    </p>
                </details>
                
                <details class="faq-item" style="margin-bottom: 20px; background: white; border: 1px solid #e0e0e0; border-radius: 10px; padding: 20px;">
                    <summary style="font-weight: 600; color: var(--dark-green); cursor: pointer; margin-bottom: 15px;">
                        <?php _e('Can I volunteer instead of donating money?', 'kilismile'); ?>
                    </summary>
                    <p style="color: var(--text-secondary); line-height: 1.6;">
                        <?php _e('Absolutely! We welcome volunteers with healthcare backgrounds, teaching experience, or general support skills. Visit our Get Involved page to learn more.', 'kilismile'); ?>
                    </p>
                </details>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="donation-contact" style="text-align: center; background: var(--primary-green); color: white; padding: 60px 40px; border-radius: 15px;">
            <h2 style="margin-bottom: 20px; color: white;">
                <?php _e('Have Questions?', 'kilismile'); ?>
            </h2>
            <p style="font-size: 1.1rem; margin-bottom: 30px; opacity: 0.9;">
                <?php _e('We\'re here to help! Contact us for any questions about donations or our programs.', 'kilismile'); ?>
            </p>
            <div class="contact-buttons" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="mailto:<?php echo esc_attr(get_theme_mod('kilismile_email', 'kilismile21@gmail.com')); ?>" 
                   class="btn btn-secondary" 
                   style="background: white; color: var(--primary-green); padding: 15px 25px;">
                    <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                    <?php _e('Email Us', 'kilismile'); ?>
                </a>
                <a href="tel:<?php echo esc_attr(str_replace(['/', ' '], '', get_theme_mod('kilismile_phone', '0763495575'))); ?>" 
                   class="btn btn-secondary" 
                   style="background: white; color: var(--primary-green); padding: 15px 25px;">
                    <i class="fas fa-phone" style="margin-right: 8px;"></i>
                    <?php _e('Call Us', 'kilismile'); ?>
                </a>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>
