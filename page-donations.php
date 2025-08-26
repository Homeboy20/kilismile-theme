<?php
/**
 * Template Name: Donations Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="donations-hero" style="background: var(--light-gray); color: var(--text-primary); padding: 100px 0 60px; text-align: center; border-bottom: 3px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 20px; color: var(--dark-green);">
                <?php _e('Make a Difference Today', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto 40px; line-height: 1.6; color: var(--text-secondary);">
                <?php _e('Your donation helps us provide essential healthcare services, education, and hope to communities across Tanzania.', 'kilismile'); ?>
            </p>
            
            <!-- Quick Donation Amounts -->
            <div class="quick-donations" style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-bottom: 40px;">
                <button onclick="setDonationAmount(25)" class="quick-amount" style="padding: 12px 20px; background: var(--primary-green); border: 2px solid var(--primary-green); color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    $25
                </button>
                <button onclick="setDonationAmount(50)" class="quick-amount" style="padding: 12px 20px; background: var(--primary-green); border: 2px solid var(--primary-green); color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    $50
                </button>
                <button onclick="setDonationAmount(100)" class="quick-amount" style="padding: 12px 20px; background: var(--primary-green); border: 2px solid var(--primary-green); color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    $100
                </button>
                <button onclick="setDonationAmount(250)" class="quick-amount" style="padding: 12px 20px; background: var(--primary-green); border: 2px solid var(--primary-green); color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    $250
                </button>
            </div>
            
            <!-- Enhanced Donation Progress Bar -->
            <?php echo kilismile_donation_progress_bar(); ?>
        </div>
    </section>

    <!-- Current Campaign Impact -->
    <section class="current-campaign" style="padding: 60px 0; background: white;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.2rem; margin-bottom: 50px;">
                <?php _e('Current Campaign: Mobile Health Clinics', 'kilismile'); ?>
            </h2>
            
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 50px; align-items: center;">
                <!-- Campaign Info -->
                <div>
                    <h3 style="color: var(--primary-green); font-size: 1.6rem; margin-bottom: 20px;">
                        <?php _e('Bringing Healthcare to Remote Villages', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7; margin-bottom: 25px;">
                        <?php _e('Our mobile health clinics are essential for reaching communities in remote areas of Tanzania. Each clinic visit provides medical checkups, vaccinations, health education, and essential medications to families who otherwise have limited access to healthcare.', 'kilismile'); ?>
                    </p>
                    
                    <div style="background: var(--light-gray); padding: 20px; border-radius: 15px; margin-bottom: 25px;">
                        <h4 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem;">
                            <?php _e('Campaign Goals:', 'kilismile'); ?>
                        </h4>
                        <ul style="color: var(--text-secondary); line-height: 1.6; margin: 0; padding-left: 20px;">
                            <li><?php _e('Fund 50 mobile clinic visits', 'kilismile'); ?></li>
                            <li><?php _e('Serve 2,500 patients', 'kilismile'); ?></li>
                            <li><?php _e('Provide 10,000 vaccinations', 'kilismile'); ?></li>
                            <li><?php _e('Train 100 community health workers', 'kilismile'); ?></li>
                        </ul>
                    </div>
                    
                    <a href="#donation-form" style="display: inline-block; background: var(--primary-green); color: white; padding: 15px 30px; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                        <?php _e('Support This Campaign', 'kilismile'); ?> →
                    </a>
                </div>
                
                <!-- Campaign Image -->
                <div style="text-align: center;">
                    <div style="background: var(--light-gray); height: 300px; border-radius: 20px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <div style="text-align: center; color: var(--text-secondary);">
                            <i class="fas fa-truck-medical" style="font-size: 4rem; margin-bottom: 15px; color: var(--primary-green);" aria-hidden="true"></i>
                            <p style="font-size: 1.1rem; margin: 0;">
                                <?php _e('Mobile Health Clinic in Action', 'kilismile'); ?>
                            </p>
                        </div>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--text-secondary); font-style: italic; margin: 0;">
                        <?php _e('Our mobile clinics bring hope and healing directly to remote communities', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Form Section -->
    <section class="donation-form-section" style="padding: 80px 0; background: var(--light-gray);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
                <!-- Enhanced Donation Form with Currency Selection -->
                <div class="donation-form-container" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <h2 style="color: var(--dark-green); margin-bottom: 30px; font-size: 2rem; text-align: center;">
                        <?php _e('Choose Your Donation', 'kilismile'); ?>
                    </h2>
                    
                    <?php echo kilismile_donation_form(); ?>
                </div>

                <!-- Enhanced Impact Information with Currency Support -->
                <div class="impact-info">
                    <h2 style="color: var(--dark-green); margin-bottom: 30px; font-size: 2rem;">
                        <?php _e('Your Impact', 'kilismile'); ?>
                    </h2>
                    
                    <!-- Currency Toggle for Impact Display -->
                    <div class="impact-currency-toggle" style="display: flex; justify-content: center; margin-bottom: 30px;">
                        <div style="background: var(--light-gray); border-radius: 25px; padding: 5px; display: flex;">
                            <button type="button" onclick="toggleImpactCurrency('USD')" 
                                    class="impact-currency-btn active" data-currency="USD"
                                    style="padding: 10px 20px; border: none; background: var(--primary-green); color: white; border-radius: 20px; cursor: pointer; transition: all 0.3s ease; margin-right: 5px;">
                                USD ($)
                            </button>
                            <button type="button" onclick="toggleImpactCurrency('TZS')" 
                                    class="impact-currency-btn" data-currency="TZS"
                                    style="padding: 10px 20px; border: none; background: transparent; color: var(--text-secondary); border-radius: 20px; cursor: pointer; transition: all 0.3s ease;">
                                TZS (TSh)
                            </button>
                        </div>
                    </div>
                    
                    <div class="impact-cards" style="display: flex; flex-direction: column; gap: 20px;">
                        <!-- USD Impact Cards -->
                        <div class="impact-currency-content" data-currency="USD">
                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-stethoscope" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">$25 USD</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Provides basic medical supplies for one family for a month.', 'kilismile'); ?>
                                </p>
                            </div>

                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-graduation-cap" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">$50 USD</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Funds health education workshops for 20 students.', 'kilismile'); ?>
                                </p>
                            </div>

                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-user-md" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">$100 USD</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Sponsors a mobile clinic visit to a remote community.', 'kilismile'); ?>
                                </p>
                            </div>

                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-hand-holding-heart" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">$250 USD</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Provides comprehensive healthcare for a family for three months.', 'kilismile'); ?>
                                </p>
                            </div>
                        </div>

                        <!-- TZS Impact Cards -->
                        <div class="impact-currency-content" data-currency="TZS" style="display: none;">
                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-stethoscope" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">TSh 62,500</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Provides basic medical supplies for one family for a month.', 'kilismile'); ?>
                                </p>
                            </div>

                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-graduation-cap" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">TSh 125,000</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Funds health education workshops for 20 students.', 'kilismile'); ?>
                                </p>
                            </div>

                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-user-md" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">TSh 250,000</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Sponsors a mobile clinic visit to a remote community.', 'kilismile'); ?>
                                </p>
                            </div>

                            <div class="impact-card" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                                <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                    <div style="width: 50px; height: 50px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; margin-right: 15px;">
                                        <i class="fas fa-hand-holding-heart" style="font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <h3 style="color: var(--dark-green); margin: 0; font-size: 1.2rem;">TSh 625,000</h3>
                                </div>
                                <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                                    <?php _e('Provides comprehensive healthcare for a family for three months.', 'kilismile'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Trust Indicators with Gateway Information -->
                    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 30px;">
                        <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.3rem; text-align: center;">
                            <?php _e('Secure Payment Options', 'kilismile'); ?>
                        </h3>
                        
                        <!-- International Payment Methods -->
                        <div class="payment-methods" style="margin-bottom: 25px;">
                            <h4 style="color: var(--text-primary); margin-bottom: 15px; font-size: 1rem; text-align: center;">
                                <?php _e('International Payments (USD)', 'kilismile'); ?>
                            </h4>
                            <div style="display: flex; justify-content: center; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fab fa-paypal" style="font-size: 2rem; color: #0070ba;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">PayPal</p>
                                </div>
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fab fa-stripe" style="font-size: 2rem; color: #6772e5;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">Stripe</p>
                                </div>
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fas fa-university" style="font-size: 2rem; color: var(--primary-green);" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">Wire Transfer</p>
                                </div>
                            </div>
                        </div>

                        <!-- Local Payment Methods -->
                        <div class="payment-methods">
                            <h4 style="color: var(--text-primary); margin-bottom: 15px; font-size: 1rem; text-align: center;">
                                <?php _e('Local Payments (TZS)', 'kilismile'); ?>
                            </h4>
                            <div style="display: flex; justify-content: center; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fas fa-mobile-alt" style="font-size: 2rem; color: #00a651;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">M-Pesa</p>
                                </div>
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fas fa-mobile-alt" style="font-size: 2rem; color: #e31e24;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">Airtel Money</p>
                                </div>
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fas fa-mobile-alt" style="font-size: 2rem; color: #1e90ff;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">Tigo Pesa</p>
                                </div>
                                <div style="text-align: center; padding: 10px;">
                                    <i class="fas fa-building" style="font-size: 2rem; color: var(--primary-green);" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 5px 0 0 0;">Local Bank</p>
                                </div>
                            </div>
                        </div>

                        <!-- Security Features -->
                        <div style="border-top: 1px solid #e0e0e0; padding-top: 20px;">
                            <div style="display: flex; justify-content: center; gap: 20px; align-items: center; flex-wrap: wrap;">
                                <div style="text-align: center;">
                                    <i class="fas fa-shield-alt" style="font-size: 2rem; color: var(--primary-green); margin-bottom: 5px;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;"><?php _e('SSL Encrypted', 'kilismile'); ?></p>
                                </div>
                                <div style="text-align: center;">
                                    <i class="fas fa-lock" style="font-size: 2rem; color: var(--primary-green); margin-bottom: 5px;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;"><?php _e('Secure Payment', 'kilismile'); ?></p>
                                </div>
                                <div style="text-align: center;">
                                    <i class="fas fa-certificate" style="font-size: 2rem; color: var(--primary-green); margin-bottom: 5px;" aria-hidden="true"></i>
                                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin: 0;"><?php _e('Tax Deductible', 'kilismile'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Other Ways to Give -->
    <section class="other-ways" style="padding: 80px 0; background: white;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Other Ways to Give', 'kilismile'); ?>
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                <!-- Corporate Partnerships -->
                <div class="giving-option" style="background: var(--light-gray); padding: 40px; border-radius: 20px; text-align: center; transition: all 0.3s ease;">
                    <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white;">
                        <i class="fas fa-building" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;">
                        <?php _e('Corporate Partnerships', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 25px;">
                        <?php _e('Partner with us to make a larger impact through corporate social responsibility programs.', 'kilismile'); ?>
                    </p>
                    <a href="mailto:corporate@kilismile.org" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">
                        <?php _e('Learn More', 'kilismile'); ?> →
                    </a>
                </div>

                <!-- In-Kind Donations -->
                <div class="giving-option" style="background: var(--light-gray); padding: 40px; border-radius: 20px; text-align: center; transition: all 0.3s ease;">
                    <div style="width: 80px; height: 80px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white;">
                        <i class="fas fa-gift" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;">
                        <?php _e('In-Kind Donations', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 25px;">
                        <?php _e('Donate medical supplies, equipment, or other needed items directly to our programs.', 'kilismile'); ?>
                    </p>
                    <a href="mailto:donations@kilismile.org" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">
                        <?php _e('Contact Us', 'kilismile'); ?> →
                    </a>
                </div>

                <!-- Legacy Giving -->
                <div class="giving-option" style="background: var(--light-gray); padding: 40px; border-radius: 20px; text-align: center; transition: all 0.3s ease;">
                    <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white;">
                        <i class="fas fa-heart" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;">
                        <?php _e('Legacy Giving', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 25px;">
                        <?php _e('Leave a lasting impact through planned giving and estate planning.', 'kilismile'); ?>
                    </p>
                    <a href="mailto:legacy@kilismile.org" style="color: var(--primary-green); text-decoration: none; font-weight: 600;">
                        <?php _e('Get Information', 'kilismile'); ?> →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Donor Recognition -->
    <section class="donor-recognition" style="padding: 80px 0; background: var(--primary-green); color: white;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 60px; font-size: 2.5rem;">
                <?php _e('Thank You to Our Donors', 'kilismile'); ?>
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
                <!-- Major Donors -->
                <div style="text-align: center;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 20px; opacity: 0.9;">
                        <?php _e('Platinum Supporters', 'kilismile'); ?>
                    </h3>
                    <div style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; min-height: 150px; display: flex; align-items: center; justify-content: center;">
                        <p style="opacity: 0.7; font-style: italic;">
                            <?php _e('Recognition for major donors $10,000+', 'kilismile'); ?>
                        </p>
                    </div>
                </div>

                <div style="text-align: center;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 20px; opacity: 0.9;">
                        <?php _e('Gold Supporters', 'kilismile'); ?>
                    </h3>
                    <div style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; min-height: 150px; display: flex; align-items: center; justify-content: center;">
                        <p style="opacity: 0.7; font-style: italic;">
                            <?php _e('Recognition for donors $5,000+', 'kilismile'); ?>
                        </p>
                    </div>
                </div>

                <div style="text-align: center;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 20px; opacity: 0.9;">
                        <?php _e('Silver Supporters', 'kilismile'); ?>
                    </h3>
                    <div style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; min-height: 150px; display: flex; align-items: center; justify-content: center;">
                        <p style="opacity: 0.7; font-style: italic;">
                            <?php _e('Recognition for donors $1,000+', 'kilismile'); ?>
                        </p>
                    </div>
                </div>

                <div style="text-align: center;">
                    <h3 style="font-size: 1.5rem; margin-bottom: 20px; opacity: 0.9;">
                        <?php _e('Community Champions', 'kilismile'); ?>
                    </h3>
                    <div style="background: rgba(255,255,255,0.1); padding: 30px; border-radius: 15px; min-height: 150px; display: flex; align-items: center; justify-content: center;">
                        <p style="opacity: 0.7; font-style: italic;">
                            <?php _e('All our valued donors making a difference', 'kilismile'); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Enhanced donation page JavaScript with currency support

// Toggle impact currency display
function toggleImpactCurrency(currency) {
    // Update button states
    document.querySelectorAll('.impact-currency-btn').forEach(btn => {
        if (btn.dataset.currency === currency) {
            btn.style.background = 'var(--primary-green)';
            btn.style.color = 'white';
            btn.classList.add('active');
        } else {
            btn.style.background = 'transparent';
            btn.style.color = 'var(--text-secondary)';
            btn.classList.remove('active');
        }
    });
    
    // Toggle content visibility
    document.querySelectorAll('.impact-currency-content').forEach(content => {
        if (content.dataset.currency === currency) {
            content.style.display = 'block';
        } else {
            content.style.display = 'none';
        }
    });
}

// Quick amount setting function (legacy support)
function setDonationAmount(amount) {
    // Check if the donation form exists and use the enhanced function
    if (typeof window.kilismileSetDonationAmount === 'function') {
        window.kilismileSetDonationAmount(amount);
    } else {
        // Fallback for basic functionality
        const amountInput = document.querySelector('input[name="amount"]');
        if (amountInput) {
            amountInput.value = amount;
            amountInput.focus();
        }
    }
}

// Enhanced form initialization
document.addEventListener('DOMContentLoaded', function() {
    console.log('Enhanced donation page loaded');
    
    // Initialize currency toggle for impact display
    toggleImpactCurrency('USD');
    
    // Enhanced hover effects for giving options
    document.querySelectorAll('.giving-option').forEach(option => {
        option.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
        });
        
        option.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
        });
    });
    
    // Enhanced hover effects for impact cards
    document.querySelectorAll('.impact-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
    });
    
    // Quick amount buttons in hero section
    document.querySelectorAll('.quick-amount').forEach(btn => {
        btn.addEventListener('click', function() {
            // Smooth scroll to donation form
            const formSection = document.querySelector('.donation-form-section');
            if (formSection) {
                formSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                
                // Wait for scroll to complete, then focus on amount input
                setTimeout(() => {
                    const amountInput = document.querySelector('input[name="amount"]');
                    if (amountInput) {
                        amountInput.focus();
                    }
                }, 1000);
            }
        });
        
        // Enhanced hover effects
        btn.addEventListener('mouseenter', function() {
            this.style.background = 'white';
            this.style.color = 'var(--primary-green)';
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.style.background = 'var(--primary-green)';
            this.style.color = 'white';
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Payment method info display
    const paymentMethods = document.querySelectorAll('.payment-methods div[style*="text-align: center"]');
    paymentMethods.forEach(method => {
        method.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1)';
            this.style.background = 'var(--light-gray)';
            this.style.borderRadius = '10px';
            this.style.transition = 'all 0.3s ease';
        });
        
        method.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.background = 'transparent';
        });
    });
    
    // Accessibility improvements
    document.querySelectorAll('button, .impact-currency-btn').forEach(btn => {
        btn.addEventListener('focus', function() {
            this.style.outline = '3px solid var(--primary-green)';
            this.style.outlineOffset = '2px';
        });
        
        btn.addEventListener('blur', function() {
            this.style.outline = 'none';
            this.style.outlineOffset = '0';
        });
    });
    
    // Monitor for donation form load
    const checkForDonationForm = setInterval(() => {
        const donationForm = document.getElementById('kilismile-donation-form');
        if (donationForm) {
            console.log('Enhanced donation form detected');
            clearInterval(checkForDonationForm);
            
            // Add success message handler
            const form = donationForm;
            form.addEventListener('submit', function(e) {
                // Let the form handle its own submission
                console.log('Donation form submitted');
            });
        }
    }, 500);
    
    // Stop checking after 10 seconds
    setTimeout(() => {
        clearInterval(checkForDonationForm);
    }, 10000);
});

// Smooth scroll enhancement for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Window scroll effects
let lastScrollTop = 0;
window.addEventListener('scroll', function() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    // Add scroll effects here if needed
    // For example, changing header appearance, etc.
    
    lastScrollTop = scrollTop;
});
</script>

<style>
    /* Enhanced donation page styles with currency support */
    .giving-option:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    
    .impact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    
    .quick-amount:hover,
    .preset-amount:hover {
        background: white !important;
        color: var(--primary-green) !important;
        transform: translateY(-2px);
    }
    
    /* Currency toggle styles */
    .impact-currency-toggle {
        margin-bottom: 30px;
    }
    
    .impact-currency-btn {
        transition: all 0.3s ease;
        font-weight: 600;
        min-width: 100px;
    }
    
    .impact-currency-btn:hover {
        background: var(--accent-green) !important;
        color: white !important;
        transform: translateY(-1px);
    }
    
    .impact-currency-btn.active {
        background: var(--primary-green) !important;
        color: white !important;
    }
    
    /* Payment methods styling */
    .payment-methods {
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 20px;
    }
    
    .payment-methods:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .payment-methods h4 {
        font-weight: 600;
        color: var(--text-primary);
    }
    
    /* Enhanced donation form integration */
    .kilismile-donation-form {
        background: transparent !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    
    /* Currency selection in form */
    .currency-selection {
        margin-bottom: 25px;
    }
    
    .currency-btn {
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0;
        background: white;
        color: var(--text-primary);
        padding: 12px 20px;
        border-radius: 10px;
        cursor: pointer;
        font-weight: 600;
        flex: 1;
        text-align: center;
    }
    
    .currency-btn:hover {
        border-color: var(--primary-green);
        background: var(--light-gray);
    }
    
    .currency-btn.active {
        border-color: var(--primary-green);
        background: var(--primary-green);
        color: white;
    }
    
    /* Gateway visibility animations */
    .gateway-section {
        transition: all 0.5s ease;
        overflow: hidden;
    }
    
    .gateway-section.hidden {
        max-height: 0;
        opacity: 0;
        margin: 0;
        padding: 0;
    }
    
    .gateway-section.visible {
        max-height: 1000px;
        opacity: 1;
    }
    
    /* Amount input enhancements */
    .amount-input-container {
        position: relative;
    }
    
    .currency-symbol {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 600;
        color: var(--text-secondary);
        z-index: 1;
    }
    
    .amount-input-with-symbol {
        padding-left: 45px !important;
    }
    
    /* Conversion display */
    .conversion-display {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-top: 5px;
        font-style: italic;
    }
    
    /* Enhanced submit button */
    .donation-submit-btn {
        background: linear-gradient(135deg, var(--primary-green), var(--accent-green));
        border: none;
        color: white;
        padding: 18px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .donation-submit-btn:hover {
        background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
    }
    
    .donation-submit-btn:active {
        transform: translateY(0);
    }
    
    /* Loading state */
    .donation-submit-btn.loading {
        background: var(--text-secondary);
        cursor: not-allowed;
        transform: none;
    }
    
    /* Success message styling */
    .donation-message {
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
        text-align: center;
    }
    
    .donation-message.success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
    }
    
    .donation-message.error {
        background: #f8d7da;
        border: 1px solid #f5c6cb;
        color: #721c24;
    }
    
    /* Gateway instructions */
    .gateway-instructions {
        background: var(--light-gray);
        padding: 15px;
        border-radius: 8px;
        margin-top: 15px;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    
    .gateway-instructions h4 {
        margin: 0 0 10px 0;
        color: var(--dark-green);
        font-size: 1rem;
    }
    
    /* Responsive design enhancements */
    @media (max-width: 768px) {
        .donation-form-section > .container > div {
            grid-template-columns: 1fr;
            gap: 40px;
        }
        
        .quick-donations {
            flex-direction: column;
            align-items: center;
        }
        
        .quick-amount {
            width: 120px;
        }
        
        .preset-amount {
            font-size: 0.9rem;
            padding: 8px;
        }
        
        .impact-cards {
            margin-bottom: 30px;
        }
        
        .impact-currency-toggle {
            margin-bottom: 20px;
        }
        
        .impact-currency-btn {
            min-width: 80px;
            font-size: 0.9rem;
        }
        
        .payment-methods > div {
            gap: 10px !important;
        }
        
        .payment-methods > div > div {
            padding: 5px !important;
        }
        
        .payment-methods i {
            font-size: 1.5rem !important;
        }
        
        .currency-btn {
            padding: 10px 15px;
            font-size: 0.9rem;
        }
        
        .amount-input-with-symbol {
            padding-left: 40px !important;
        }
        
        .currency-symbol {
            left: 12px;
            font-size: 0.9rem;
        }
        
        /* Current campaign responsive */
        .current-campaign .container > div {
            grid-template-columns: 1fr !important;
            gap: 30px !important;
        }
        
        .current-campaign h2 {
            font-size: 1.8rem !important;
        }
        
        .current-campaign h3 {
            font-size: 1.4rem !important;
        }
        
        /* Donation progress responsive */
        .donation-progress-container {
            margin: 30px 0;
        }
    }
    
    @media (max-width: 480px) {
        .impact-currency-toggle > div {
            padding: 3px;
        }
        
        .impact-currency-btn {
            padding: 8px 15px;
            font-size: 0.85rem;
        }
        
        .payment-methods h4 {
            font-size: 0.9rem;
        }
        
        .donation-form-container {
            padding: 30px 20px !important;
        }
        
        .gateway-section {
            margin-bottom: 20px;
        }
    }
    
    /* Print styles */
    @media print {
        .quick-donations,
        .donation-form-container,
        .impact-currency-toggle {
            display: none !important;
        }
        
        .impact-info {
            page-break-inside: avoid;
        }
    }
    
    /* High contrast mode support */
    @media (prefers-contrast: high) {
        .currency-btn,
        .impact-currency-btn {
            border-width: 3px;
        }
        
        .donation-submit-btn {
            border: 2px solid currentColor;
        }
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        .giving-option,
        .impact-card,
        .quick-amount,
        .preset-amount,
        .currency-btn,
        .impact-currency-btn,
        .donation-submit-btn {
            transition: none;
        }
        
        .giving-option:hover,
        .impact-card:hover,
        .quick-amount:hover,
        .preset-amount:hover,
        .donation-submit-btn:hover {
            transform: none;
        }
    }
</style>

<?php get_footer(); ?>
