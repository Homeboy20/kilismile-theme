<?php
/**
 * Enhanced Donation Page Template
 * 
 * Modern, responsive donation page with enhanced payment system integration
 *
 * @package KiliSmile
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div class="enhanced-donation-page" style="min-height: 100vh; background: linear-gradient(135deg, #f8fffe 0%, #f0f9ff 50%, #f8fff8 100%); font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;">
    
    <!-- Hero Section with Impact Stats -->
    <section class="donation-hero" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 80px 20px; position: relative; overflow: hidden;">
        <!-- Animated Background Elements -->
        <div class="hero-bg-pattern" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.1; background-image: radial-gradient(circle at 20% 80%, white 2px, transparent 2px), radial-gradient(circle at 80% 20%, white 2px, transparent 2px), radial-gradient(circle at 40% 40%, white 2px, transparent 2px); background-size: 60px 60px; animation: float 20s ease-in-out infinite;"></div>
        
        <div class="container" style="max-width: 1200px; margin: 0 auto; text-align: center; position: relative; z-index: 2;">
            <!-- Trust Badge -->
            <div class="trust-badge" style="display: inline-flex; align-items: center; background: rgba(255,255,255,0.15); padding: 10px 20px; border-radius: 30px; margin-bottom: 25px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                <i class="fas fa-shield-alt" style="color: #fff; margin-right: 10px; font-size: 1.1rem;"></i>
                <span style="font-size: 0.95rem; font-weight: 600;"><?php _e('Secure & Trusted Donations', 'kilismile'); ?></span>
            </div>
            
            <!-- Main Heading -->
            <h1 class="hero-title" style="font-size: 3.5rem; font-weight: 800; margin: 0 0 20px 0; line-height: 1.2; text-shadow: 0 2px 4px rgba(0,0,0,0.1); animation: slideInUp 0.8s ease-out;">
                <?php _e('Transform Lives Through Giving', 'kilismile'); ?>
            </h1>
            
            <!-- Subtitle -->
            <p class="hero-subtitle" style="font-size: 1.3rem; margin: 0 0 40px 0; opacity: 0.95; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6; animation: slideInUp 0.8s ease-out 0.2s both;">
                <?php _e('Your donation provides essential healthcare services to underserved communities in Tanzania. Every contribution creates lasting impact and saves lives.', 'kilismile'); ?>
            </p>
            
            <!-- Impact Statistics -->
            <div class="impact-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; max-width: 800px; margin: 0 auto 40px; animation: slideInUp 0.8s ease-out 0.4s both;">
                <div class="stat-item" style="text-align: center; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 8px; color: #fff;">5,000+</div>
                    <div class="stat-label" style="font-size: 1rem; opacity: 0.9; font-weight: 600;"><?php _e('Lives Transformed', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 8px; color: #fff;">50+</div>
                    <div class="stat-label" style="font-size: 1rem; opacity: 0.9; font-weight: 600;"><?php _e('Communities Reached', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; padding: 20px; background: rgba(255,255,255,0.1); border-radius: 16px; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <div class="stat-number" style="font-size: 2.5rem; font-weight: 800; margin-bottom: 8px; color: #fff;">24/7</div>
                    <div class="stat-label" style="font-size: 1rem; opacity: 0.9; font-weight: 600;"><?php _e('Healthcare Support', 'kilismile'); ?></div>
                </div>
            </div>
            
            <!-- Quick Action Button -->
            <div class="hero-cta" style="animation: slideInUp 0.8s ease-out 0.6s both;">
                <a href="#donation-form" class="cta-button" style="display: inline-flex; align-items: center; background: #fff; color: #28a745; padding: 18px 35px; border-radius: 50px; font-weight: 700; font-size: 1.1rem; text-decoration: none; box-shadow: 0 8px 25px rgba(0,0,0,0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: none;">
                    <i class="fas fa-heart" style="margin-right: 10px; color: #ff6b6b;"></i>
                    <?php _e('Start Donating Now', 'kilismile'); ?>
                    <i class="fas fa-arrow-down" style="margin-left: 10px; animation: bounce 2s infinite;"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Trust Indicators Section -->
    <section class="trust-indicators" style="background: white; padding: 50px 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <div class="trust-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; align-items: center; text-align: center;">
                <div class="trust-item" style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 20px; border-radius: 12px; transition: transform 0.3s ease;">
                    <div class="trust-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-shield-alt" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <div class="trust-content" style="text-align: left;">
                        <div style="font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 4px;"><?php _e('100% Secure', 'kilismile'); ?></div>
                        <div style="font-size: 0.9rem; color: #6c757d;"><?php _e('SSL encrypted payments', 'kilismile'); ?></div>
                    </div>
                </div>
                
                <div class="trust-item" style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 20px; border-radius: 12px; transition: transform 0.3s ease;">
                    <div class="trust-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #17a2b8, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-certificate" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <div class="trust-content" style="text-align: left;">
                        <div style="font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 4px;"><?php _e('Tax Deductible', 'kilismile'); ?></div>
                        <div style="font-size: 0.9rem; color: #6c757d;"><?php _e('Instant tax receipts', 'kilismile'); ?></div>
                    </div>
                </div>
                
                <div class="trust-item" style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 20px; border-radius: 12px; transition: transform 0.3s ease;">
                    <div class="trust-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #6f42c1, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-users" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <div class="trust-content" style="text-align: left;">
                        <div style="font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 4px;"><?php _e('Trusted by 1000+', 'kilismile'); ?></div>
                        <div style="font-size: 0.9rem; color: #6c757d;"><?php _e('Happy donors worldwide', 'kilismile'); ?></div>
                    </div>
                </div>
                
                <div class="trust-item" style="display: flex; align-items: center; justify-content: center; gap: 15px; padding: 20px; border-radius: 12px; transition: transform 0.3s ease;">
                    <div class="trust-icon" style="width: 50px; height: 50px; background: linear-gradient(135deg, #fd7e14, #28a745); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-chart-line" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <div class="trust-content" style="text-align: left;">
                        <div style="font-weight: 700; color: #333; font-size: 1.1rem; margin-bottom: 4px;"><?php _e('Maximum Impact', 'kilismile'); ?></div>
                        <div style="font-size: 0.9rem; color: #6c757d;"><?php _e('95% to programs', 'kilismile'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Donation Form Section -->
    <section id="donation-form" class="donation-form-section" style="padding: 80px 20px; background: linear-gradient(135deg, #f8fff8 0%, #f0f9ff 100%); position: relative;">
        <!-- Background Decoration -->
        <div style="position: absolute; top: 0; right: 0; width: 300px; height: 300px; background: radial-gradient(circle, rgba(40,167,69,0.05) 0%, transparent 70%); border-radius: 50%; transform: translate(50%, -50%);"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 200px; height: 200px; background: radial-gradient(circle, rgba(32,201,151,0.05) 0%, transparent 70%); border-radius: 50%; transform: translate(-50%, 50%);"></div>
        
        <div class="container" style="max-width: 1200px; margin: 0 auto; position: relative; z-index: 1;">
            <!-- Section Header -->
            <div class="section-header" style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.5rem; font-weight: 800; color: #2c5530; margin: 0 0 20px 0; line-height: 1.3;">
                    <?php _e('Make Your Donation', 'kilismile'); ?>
                </h2>
                <p style="font-size: 1.2rem; color: #6c757d; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                    <?php _e('Choose your preferred donation method and amount. Your contribution will directly support our healthcare initiatives.', 'kilismile'); ?>
                </p>
            </div>

            <!-- Enhanced Donation Form -->
            <div class="donation-form-container" style="max-width: 900px; margin: 0 auto;">
                <?php 
                // Load the enhanced donation form template
                $template_path = get_template_directory() . '/templates/donation-form.php';
                if (file_exists($template_path)) {
                    include $template_path;
                } else {
                    // Fallback to shortcode
                    echo do_shortcode('[kilismile_donation_form class="enhanced-donation-form" show_recurring="true" show_anonymous="true"]');
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Impact Stories Section -->
    <section class="impact-stories" style="padding: 80px 20px; background: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <!-- Section Header -->
            <div class="section-header" style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.5rem; font-weight: 800; color: #2c5530; margin: 0 0 20px 0;">
                    <?php _e('Your Impact in Action', 'kilismile'); ?>
                </h2>
                <p style="font-size: 1.2rem; color: #6c757d; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <?php _e('See how your donations are creating real change in communities across Tanzania.', 'kilismile'); ?>
                </p>
            </div>

            <!-- Impact Grid -->
            <div class="impact-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
                
                <!-- Impact Story 1 -->
                <div class="impact-card" style="background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid rgba(40,167,69,0.1); transition: transform 0.3s ease;">
                    <div class="impact-image" style="height: 200px; background: linear-gradient(135deg, #28a745, #20c997); position: relative; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-heart-pulse" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <div style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 8px 16px; border-radius: 20px; font-weight: 700; color: #28a745; font-size: 0.9rem;">
                            $50 = 5 check-ups
                        </div>
                    </div>
                    <div class="impact-content" style="padding: 30px;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #2c5530; margin: 0 0 15px 0;">
                            <?php _e('Community Health Screenings', 'kilismile'); ?>
                        </h3>
                        <p style="color: #6c757d; margin: 0 0 20px 0; line-height: 1.6;">
                            <?php _e('Your donation funds comprehensive health screenings for children and adults in rural communities, helping detect and prevent diseases early.', 'kilismile'); ?>
                        </p>
                        <div class="impact-stats" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: rgba(40,167,69,0.1); border-radius: 12px;">
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #28a745; font-size: 1.3rem;">200+</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Screened', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #28a745; font-size: 1.3rem;">85%</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Early Detection', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #28a745; font-size: 1.3rem;">15</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Villages', 'kilismile'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impact Story 2 -->
                <div class="impact-card" style="background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%); border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid rgba(23,162,184,0.1); transition: transform 0.3s ease;">
                    <div class="impact-image" style="height: 200px; background: linear-gradient(135deg, #17a2b8, #138496); position: relative; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-graduation-cap" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <div style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 8px 16px; border-radius: 20px; font-weight: 700; color: #17a2b8; font-size: 0.9rem;">
                            $25 = 1 workshop
                        </div>
                    </div>
                    <div class="impact-content" style="padding: 30px;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #2c5530; margin: 0 0 15px 0;">
                            <?php _e('Health Education Programs', 'kilismile'); ?>
                        </h3>
                        <p style="color: #6c757d; margin: 0 0 20px 0; line-height: 1.6;">
                            <?php _e('Educational workshops teaching essential health practices, disease prevention, and wellness strategies to entire communities.', 'kilismile'); ?>
                        </p>
                        <div class="impact-stats" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: rgba(23,162,184,0.1); border-radius: 12px;">
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #17a2b8; font-size: 1.3rem;">45</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Workshops', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #17a2b8; font-size: 1.3rem;">1,200+</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Participants', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #17a2b8; font-size: 1.3rem;">92%</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Retention', 'kilismile'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impact Story 3 -->
                <div class="impact-card" style="background: linear-gradient(135deg, #fff8f0 0%, #ffe6cc 100%); border-radius: 20px; overflow: hidden; box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid rgba(253,126,20,0.1); transition: transform 0.3s ease;">
                    <div class="impact-image" style="height: 200px; background: linear-gradient(135deg, #fd7e14, #e8590c); position: relative; display: flex; align-items: center; justify-content: center;">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                            <i class="fas fa-stethoscope" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <div style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.9); padding: 8px 16px; border-radius: 20px; font-weight: 700; color: #fd7e14; font-size: 0.9rem;">
                            $100 = Equipment
                        </div>
                    </div>
                    <div class="impact-content" style="padding: 30px;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #2c5530; margin: 0 0 15px 0;">
                            <?php _e('Medical Equipment & Supplies', 'kilismile'); ?>
                        </h3>
                        <p style="color: #6c757d; margin: 0 0 20px 0; line-height: 1.6;">
                            <?php _e('Essential medical equipment and supplies that enable healthcare workers to provide quality care in underserved areas.', 'kilismile'); ?>
                        </p>
                        <div class="impact-stats" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: rgba(253,126,20,0.1); border-radius: 12px;">
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #fd7e14; font-size: 1.3rem;">25</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Clinics', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #fd7e14; font-size: 1.3rem;">500+</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Equipment', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-weight: 800; color: #fd7e14; font-size: 1.3rem;">100%</div>
                                <div style="font-size: 0.8rem; color: #6c757d;"><?php _e('Functional', 'kilismile'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Donation Progress & Transparency -->
    <section class="donation-transparency" style="padding: 80px 20px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container" style="max-width: 1200px; margin: 0 auto;">
            <!-- Section Header -->
            <div class="section-header" style="text-align: center; margin-bottom: 60px;">
                <h2 style="font-size: 2.5rem; font-weight: 800; color: #2c5530; margin: 0 0 20px 0;">
                    <?php _e('Transparency & Progress', 'kilismile'); ?>
                </h2>
                <p style="font-size: 1.2rem; color: #6c757d; margin: 0; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <?php _e('We believe in complete transparency. See exactly how your donations are being used to create impact.', 'kilismile'); ?>
                </p>
            </div>

            <div class="transparency-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                
                <!-- Current Campaign Progress -->
                <div class="progress-card" style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid rgba(40,167,69,0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-target" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #2c5530; margin: 0 0 10px 0;">
                            <?php _e('Current Campaign', 'kilismile'); ?>
                        </h3>
                        <p style="color: #6c757d; margin: 0; font-size: 1rem;">
                            <?php _e('Emergency Medical Equipment', 'kilismile'); ?>
                        </p>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div style="margin-bottom: 25px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <span style="font-weight: 600; color: #495057;">$75,000 raised</span>
                            <span style="font-weight: 600; color: #28a745;">75%</span>
                        </div>
                        <div style="height: 12px; background: #e9ecef; border-radius: 25px; overflow: hidden;">
                            <div style="height: 100%; width: 75%; background: linear-gradient(90deg, #28a745, #20c997); border-radius: 25px; position: relative;">
                                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent); animation: shimmer 2s infinite;"></div>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.9rem; color: #6c757d;">
                            <span><?php _e('Goal: $100,000', 'kilismile'); ?></span>
                            <span><?php _e('23 days left', 'kilismile'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: center;">
                        <div>
                            <div style="font-weight: 800; color: #28a745; font-size: 1.5rem;">342</div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Donors', 'kilismile'); ?></div>
                        </div>
                        <div>
                            <div style="font-weight: 800; color: #28a745; font-size: 1.5rem;">$219</div>
                            <div style="font-size: 0.85rem; color: #6c757d;"><?php _e('Avg. Donation', 'kilismile'); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="breakdown-card" style="background: white; border-radius: 20px; padding: 40px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); border: 1px solid rgba(23,162,184,0.1);">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #17a2b8, #138496); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-chart-pie" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <h3 style="font-size: 1.4rem; font-weight: 700; color: #2c5530; margin: 0 0 10px 0;">
                            <?php _e('Fund Allocation', 'kilismile'); ?>
                        </h3>
                        <p style="color: #6c757d; margin: 0; font-size: 1rem;">
                            <?php _e('How donations are used', 'kilismile'); ?>
                        </p>
                    </div>
                    
                    <!-- Breakdown Items -->
                    <div style="space-y: 15px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%); border-radius: 12px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 12px; height: 12px; background: #28a745; border-radius: 50%; margin-right: 12px;"></div>
                                <span style="font-weight: 600; color: #495057;"><?php _e('Direct Programs', 'kilismile'); ?></span>
                            </div>
                            <span style="font-weight: 700; color: #28a745;">85%</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%); border-radius: 12px; margin-bottom: 15px;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 12px; height: 12px; background: #17a2b8; border-radius: 50%; margin-right: 12px;"></div>
                                <span style="font-weight: 600; color: #495057;"><?php _e('Operations', 'kilismile'); ?></span>
                            </div>
                            <span style="font-weight: 700; color: #17a2b8;">10%</span>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: linear-gradient(135deg, #fff8f0 0%, #ffe6cc 100%); border-radius: 12px;">
                            <div style="display: flex; align-items: center;">
                                <div style="width: 12px; height: 12px; background: #fd7e14; border-radius: 50%; margin-right: 12px;"></div>
                                <span style="font-weight: 600; color: #495057;"><?php _e('Fundraising', 'kilismile'); ?></span>
                            </div>
                            <span style="font-weight: 700; color: #fd7e14;">5%</span>
                        </div>
                    </div>
                    
                    <!-- Trust Badge -->
                    <div style="text-align: center; margin-top: 25px; padding: 15px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 12px;">
                        <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 5px;"><?php _e('Verified by', 'kilismile'); ?></div>
                        <div style="font-weight: 700; color: #495057;"><?php _e('Independent Auditors', 'kilismile'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="final-cta" style="padding: 80px 20px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; text-align: center;">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-size: 2.5rem; font-weight: 800; margin: 0 0 20px 0; line-height: 1.3;">
                <?php _e('Ready to Make a Difference?', 'kilismile'); ?>
            </h2>
            <p style="font-size: 1.2rem; margin: 0 0 40px 0; opacity: 0.95; line-height: 1.6;">
                <?php _e('Join thousands of donors who are transforming lives through healthcare. Every donation, no matter the size, creates lasting impact.', 'kilismile'); ?>
            </p>
            
            <!-- Action Buttons -->
            <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px;">
                <a href="#donation-form" class="cta-button-primary" style="display: inline-flex; align-items: center; background: white; color: #28a745; padding: 18px 35px; border-radius: 50px; font-weight: 700; font-size: 1.1rem; text-decoration: none; box-shadow: 0 8px 25px rgba(0,0,0,0.2); transition: all 0.3s ease;">
                    <i class="fas fa-heart" style="margin-right: 10px; color: #ff6b6b;"></i>
                    <?php _e('Donate Now', 'kilismile'); ?>
                </a>
                
                <a href="<?php echo esc_url(get_permalink(get_page_by_path('about'))); ?>" class="cta-button-secondary" style="display: inline-flex; align-items: center; background: rgba(255,255,255,0.2); color: white; padding: 18px 35px; border-radius: 50px; font-weight: 700; font-size: 1.1rem; text-decoration: none; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s ease;">
                    <i class="fas fa-info-circle" style="margin-right: 10px;"></i>
                    <?php _e('Learn More', 'kilismile'); ?>
                </a>
            </div>
            
            <!-- Social Proof -->
            <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid rgba(255,255,255,0.2);">
                <p style="font-size: 0.95rem; opacity: 0.8; margin: 0 0 15px 0;">
                    <?php _e('Join 1000+ donors from around the world', 'kilismile'); ?>
                </p>
                <div style="display: flex; justify-content: center; gap: 30px; font-size: 0.9rem; opacity: 0.9;">
                    <span><i class="fas fa-star"></i> 4.9/5 Trust Rating</span>
                    <span><i class="fas fa-shield-alt"></i> 100% Secure</span>
                    <span><i class="fas fa-certificate"></i> Tax Deductible</span>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Enhanced Animations and Styles -->
<style>
    /* Hero Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-8px);
        }
        60% {
            transform: translateY(-4px);
        }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        50% {
            transform: translateY(-10px) rotate(2deg);
        }
    }
    
    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }
    
    /* Hover Effects */
    .cta-button:hover {
        transform: translateY(-4px) scale(1.05) !important;
        box-shadow: 0 15px 40px rgba(0,0,0,0.25) !important;
    }
    
    .cta-button-primary:hover {
        transform: translateY(-4px) scale(1.05) !important;
        box-shadow: 0 15px 40px rgba(40,167,69,0.4) !important;
    }
    
    .cta-button-secondary:hover {
        background: rgba(255,255,255,0.3) !important;
        transform: translateY(-4px) scale(1.05) !important;
    }
    
    .impact-card:hover {
        transform: translateY(-8px) scale(1.02) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }
    
    .trust-item:hover {
        transform: scale(1.05) !important;
    }
    
    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-title {
            font-size: 2.8rem !important;
        }
        
        .impact-grid,
        .transparency-grid {
            grid-template-columns: 1fr !important;
        }
        
        .trust-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 768px) {
        .donation-hero {
            padding: 60px 20px !important;
        }
        
        .hero-title {
            font-size: 2.2rem !important;
        }
        
        .hero-subtitle {
            font-size: 1.1rem !important;
        }
        
        .impact-stats {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        
        .trust-grid {
            grid-template-columns: 1fr !important;
        }
        
        .trust-item {
            flex-direction: column !important;
            text-align: center !important;
        }
        
        .trust-content {
            text-align: center !important;
        }
        
        .section-header h2 {
            font-size: 2rem !important;
        }
        
        .section-header p {
            font-size: 1rem !important;
        }
        
        .final-cta h2 {
            font-size: 2rem !important;
        }
        
        .final-cta p {
            font-size: 1rem !important;
        }
        
        .final-cta > div > div {
            flex-direction: column !important;
        }
        
        .final-cta > div > div:last-child {
            flex-direction: row !important;
            flex-wrap: wrap !important;
        }
    }
    
    @media (max-width: 480px) {
        .hero-title {
            font-size: 1.8rem !important;
        }
        
        .stat-number {
            font-size: 2rem !important;
        }
        
        .impact-card,
        .progress-card,
        .breakdown-card {
            padding: 25px !important;
        }
        
        .impact-image {
            height: 150px !important;
        }
        
        .cta-button,
        .cta-button-primary,
        .cta-button-secondary {
            padding: 15px 25px !important;
            font-size: 1rem !important;
        }
    }
    
    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        .impact-card,
        .progress-card,
        .breakdown-card {
            background: #1a1a1a !important;
            border-color: rgba(255,255,255,0.1) !important;
        }
        
        .trust-indicators {
            background: #1a1a1a !important;
        }
        
        .section-header h2 {
            color: #ffffff !important;
        }
        
        .section-header p {
            color: #cccccc !important;
        }
    }
    
    /* Accessibility improvements */
    .cta-button:focus,
    .cta-button-primary:focus,
    .cta-button-secondary:focus {
        outline: 3px solid #ffc107 !important;
        outline-offset: 2px !important;
    }
    
    /* Print styles */
    @media print {
        .donation-hero,
        .final-cta {
            background: white !important;
            color: black !important;
        }
        
        .cta-button,
        .cta-button-primary,
        .cta-button-secondary {
            border: 2px solid black !important;
            background: white !important;
            color: black !important;
        }
    }
    
    /* Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        *,
        *::before,
        *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>

<!-- Enhanced JavaScript for Interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scrolling for anchor links
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
    
    // Progressive enhancement for impact cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.impact-card, .progress-card, .breakdown-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
        observer.observe(card);
    });
    
    // Enhanced statistics counter animation
    function animateNumbers() {
        const numbers = document.querySelectorAll('.stat-number');
        numbers.forEach(number => {
            const finalValue = parseInt(number.textContent.replace(/[^\d]/g, ''));
            let currentValue = 0;
            const increment = finalValue / 60; // 60 frames animation
            
            const timer = setInterval(() => {
                currentValue += increment;
                if (currentValue >= finalValue) {
                    currentValue = finalValue;
                    clearInterval(timer);
                }
                
                const formatted = Math.floor(currentValue).toLocaleString();
                number.textContent = number.textContent.includes('+') ? 
                    formatted + '+' : formatted;
            }, 16); // ~60fps
        });
    }
    
    // Trigger animations when hero section is visible
    const heroObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateNumbers();
                heroObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    const heroSection = document.querySelector('.donation-hero');
    if (heroSection) {
        heroObserver.observe(heroSection);
    }
    
    // Dynamic progress bar animation
    const progressBars = document.querySelectorAll('[style*="width: 75%"]');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        bar.style.transition = 'width 2s ease-out';
        
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
    
    // Enhanced form integration detection
    const checkFormLoad = () => {
        const donationForm = document.querySelector('.kilismile-donation-form, .enhanced-donation-form');
        if (donationForm && window.KiliSmileDonationForm) {
            // Integration successful
            console.log('Enhanced donation form loaded successfully');
        }
    };
    
    // Check immediately and after a delay
    checkFormLoad();
    setTimeout(checkFormLoad, 1000);
    
    // Accessibility: Add proper focus management
    const focusableElements = document.querySelectorAll(
        'a[href], button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    focusableElements.forEach(element => {
        element.addEventListener('focus', function() {
            this.style.outline = '3px solid #ffc107';
            this.style.outlineOffset = '2px';
        });
        
        element.addEventListener('blur', function() {
            this.style.outline = '';
            this.style.outlineOffset = '';
        });
    });
    
    // Loading state management
    window.addEventListener('load', function() {
        document.body.classList.add('page-loaded');
        
        // Trigger any remaining animations
        const remainingCards = document.querySelectorAll('.impact-card[style*="opacity: 0"]');
        remainingCards.forEach(card => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        });
    });
});
</script>

<?php get_footer(); ?>


