<?php
/**
 * Template Name: Corporate Sponsors Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="corporate-sponsors-hero" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 50%, #27ae60 100%); color: white; padding: 120px 0 80px; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cdefs%3E%3Cpattern id='corporate-pattern' width='50' height='50' patternUnits='userSpaceOnUse'%3E%3Crect width='50' height='50' fill='none'/%3E%3Cpath d='M25 5 L45 25 L25 45 L5 25 Z' fill='rgba(255,255,255,0.08)'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23corporate-pattern)'/%3E%3C/svg%3E&quot;); opacity: 0.7;"></div>
        <div class="container" style="position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="max-width: 900px; margin: 0 auto;">
                <h1 style="font-size: 3.8rem; margin-bottom: 25px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); line-height: 1.1;">
                    <?php _e('Corporate Sponsors', 'kilismile'); ?>
                </h1>
                <p style="font-size: 1.4rem; margin-bottom: 30px; line-height: 1.6; opacity: 0.95;">
                    <?php _e('Partner with KiliSmile to create meaningful impact while achieving your corporate social responsibility goals and enhancing your brand reputation.', 'kilismile'); ?>
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
                    <a href="#sponsorship-packages" class="cta-primary" style="background: rgba(255,255,255,0.9); color: #2c3e50; padding: 15px 35px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.2); backdrop-filter: blur(10px);">
                        <i class="fas fa-award" style="margin-right: 10px;"></i>
                        <?php _e('View Packages', 'kilismile'); ?>
                    </a>
                    <a href="#current-sponsors" class="cta-secondary" style="background: rgba(255,255,255,0.1); color: white; padding: 15px 35px; border: 2px solid rgba(255,255,255,0.4); border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                        <i class="fas fa-users" style="margin-right: 10px;"></i>
                        <?php _e('Our Sponsors', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Impact Statistics -->
        <div style="position: absolute; bottom: -40px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 1000px; z-index: 3;">
            <div style="background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); padding: 40px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px;">
                    <div class="stat-item" style="text-align: center; padding: 15px;">
                        <div style="background: linear-gradient(135deg, #3498db, #2980b9); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 25px rgba(52,152,219,0.3);">
                            <i class="fas fa-building" style="color: white; font-size: 1.8rem;"></i>
                        </div>
                        <div style="color: #3498db; font-size: 2.2rem; font-weight: 700; margin-bottom: 8px;">35+</div>
                        <div style="color: #7f8c8d; font-weight: 500; font-size: 0.9rem;"><?php _e('Corporate Partners', 'kilismile'); ?></div>
                    </div>

                    <div class="stat-item" style="text-align: center; padding: 15px;">
                        <div style="background: linear-gradient(135deg, #27ae60, #2ecc71); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 25px rgba(39,174,96,0.3);">
                            <i class="fas fa-heart" style="color: white; font-size: 1.8rem;"></i>
                        </div>
                        <div style="color: #27ae60; font-size: 2.2rem; font-weight: 700; margin-bottom: 8px;">$2.8M+</div>
                        <div style="color: #7f8c8d; font-weight: 500; font-size: 0.9rem;"><?php _e('Corporate Investment', 'kilismile'); ?></div>
                    </div>

                    <div class="stat-item" style="text-align: center; padding: 15px;">
                        <div style="background: linear-gradient(135deg, #e74c3c, #c0392b); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 25px rgba(231,76,60,0.3);">
                            <i class="fas fa-users" style="color: white; font-size: 1.8rem;"></i>
                        </div>
                        <div style="color: #e74c3c; font-size: 2.2rem; font-weight: 700; margin-bottom: 8px;">85,000+</div>
                        <div style="color: #7f8c8d; font-weight: 500; font-size: 0.9rem;"><?php _e('Lives Impacted', 'kilismile'); ?></div>
                    </div>

                    <div class="stat-item" style="text-align: center; padding: 15px;">
                        <div style="background: linear-gradient(135deg, #f39c12, #e67e22); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; box-shadow: 0 8px 25px rgba(243,156,18,0.3);">
                            <i class="fas fa-globe-africa" style="color: white; font-size: 1.8rem;"></i>
                        </div>
                        <div style="color: #f39c12; font-size: 2.2rem; font-weight: 700; margin-bottom: 8px;">18</div>
                        <div style="color: #7f8c8d; font-weight: 500; font-size: 0.9rem;"><?php _e('Regions Covered', 'kilismile'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsorship Packages -->
    <section id="sponsorship-packages" class="sponsorship-packages" style="padding: 120px 0 100px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); margin-top: 60px;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 3rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Sponsorship Packages', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Choose the sponsorship level that aligns with your corporate goals and budget. All packages include comprehensive reporting and recognition.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; align-items: stretch;">
                <!-- Bronze Package -->
                <div class="package-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 500px; position: relative;">
                    <div class="package-header" style="background: #CD7F32; color: white; padding: 25px; text-align: center;">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-award" style="font-size: 1.8rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.4rem; color: white;"><?php _e('Bronze Sponsor', 'kilismile'); ?></h3>
                        <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">$5,000+</div>
                    </div>
                    <div class="package-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <div class="benefits" style="margin-bottom: 25px; flex-grow: 1;">
                            <h4 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.1rem;"><?php _e('Benefits Include:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Logo on website & materials', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Social media recognition', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Quarterly impact reports', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Tax deduction certificate', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('CSR documentation', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="selectPackage('bronze')" 
                                style="width: 100%; padding: 12px; background: #CD7F32; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Choose Bronze', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Silver Package -->
                <div class="package-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 500px; position: relative;">
                    <div class="package-header" style="background: #C0C0C0; color: white; padding: 25px; text-align: center;">
                        <div style="background: rgba(255,255,255,0.2); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-medal" style="font-size: 1.8rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.4rem; color: white;"><?php _e('Silver Sponsor', 'kilismile'); ?></h3>
                        <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">$15,000+</div>
                    </div>
                    <div class="package-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <div class="benefits" style="margin-bottom: 25px; flex-grow: 1;">
                            <h4 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.1rem;"><?php _e('Everything in Bronze, plus:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Event co-branding opportunities', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Employee volunteer opportunities', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Newsletter inclusion', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Press release inclusion', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Dedicated relationship manager', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="selectPackage('silver')" 
                                style="width: 100%; padding: 12px; background: #C0C0C0; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Choose Silver', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Gold Package -->
                <div class="package-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 500px; position: relative; border: 3px solid #FFD700;">
                    <div class="popular-badge" style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #FFD700; color: var(--dark-green); padding: 8px 20px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; z-index: 3;">
                        <?php _e('MOST POPULAR', 'kilismile'); ?>
                    </div>
                    <div class="package-header" style="background: #FFD700; color: var(--dark-green); padding: 25px; text-align: center;">
                        <div style="background: rgba(255,255,255,0.3); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-trophy" style="font-size: 1.8rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.4rem;"><?php _e('Gold Sponsor', 'kilismile'); ?></h3>
                        <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">$50,000+</div>
                    </div>
                    <div class="package-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <div class="benefits" style="margin-bottom: 25px; flex-grow: 1;">
                            <h4 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.1rem;"><?php _e('Everything in Silver, plus:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Named program sponsorship', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Executive site visits', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Custom impact measurement', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Board meeting presentations', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Priority event invitations', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="selectPackage('gold')" 
                                style="width: 100%; padding: 12px; background: #FFD700; color: var(--dark-green); border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Choose Gold', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Platinum Package -->
                <div class="package-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 500px; position: relative;">
                    <div class="package-header" style="background: #E5E4E2; color: var(--dark-green); padding: 25px; text-align: center;">
                        <div style="background: rgba(255,255,255,0.3); border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-crown" style="font-size: 1.8rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.4rem;"><?php _e('Platinum Sponsor', 'kilismile'); ?></h3>
                        <div style="font-size: 2rem; font-weight: bold; margin-top: 10px;">$100,000+</div>
                    </div>
                    <div class="package-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <div class="benefits" style="margin-bottom: 25px; flex-grow: 1;">
                            <h4 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.1rem;"><?php _e('Everything in Gold, plus:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Strategic partnership status', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Joint research projects', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Advisory board seat', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Exclusive networking events', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px; font-size: 0.9rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Custom program development', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="selectPackage('platinum')" 
                                style="width: 100%; padding: 12px; background: #E5E4E2; color: var(--dark-green); border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Choose Platinum', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits of Sponsorship -->
    <section class="sponsorship-benefits" style="padding: 100px 0; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: white; font-size: 3rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Why Sponsor KiliSmile?', 'kilismile'); ?>
                </h2>
                <p style="color: rgba(255,255,255,0.8); font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Discover the comprehensive benefits of corporate sponsorship that go beyond traditional marketing.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                <div class="benefit-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="background: rgba(52,152,219,0.2); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-chart-line" style="color: #3498db; font-size: 1.8rem;"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Measurable Impact', 'kilismile'); ?>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        <?php _e('Receive detailed reports showing exactly how your sponsorship translates into real-world health improvements and community development.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="background: rgba(39,174,96,0.2); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-award" style="color: #27ae60; font-size: 1.8rem;"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Brand Recognition', 'kilismile'); ?>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        <?php _e('Enhance your corporate reputation through association with meaningful healthcare initiatives and community development programs.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="background: rgba(243,156,18,0.2); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-users" style="color: #f39c12; font-size: 1.8rem;"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Employee Engagement', 'kilismile'); ?>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        <?php _e('Boost team morale and retention through meaningful volunteer opportunities and corporate social responsibility programs.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="background: rgba(155,89,182,0.2); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-handshake" style="color: #9b59b6; font-size: 1.8rem;"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Strategic Partnerships', 'kilismile'); ?>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        <?php _e('Access exclusive networking opportunities and collaborate with other forward-thinking organizations in our sponsor network.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="background: rgba(231,76,60,0.2); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-globe-africa" style="color: #e74c3c; font-size: 1.8rem;"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Global Impact', 'kilismile'); ?>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        <?php _e('Contribute to UN Sustainable Development Goals while making a tangible difference in underserved communities across East Africa.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border-radius: 20px; padding: 30px; text-align: center; border: 1px solid rgba(255,255,255,0.2);">
                    <div style="background: rgba(26,188,156,0.2); width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-file-invoice-dollar" style="color: #1abc9c; font-size: 1.8rem;"></i>
                    </div>
                    <h3 style="color: white; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Tax Benefits', 'kilismile'); ?>
                    </h3>
                    <p style="color: rgba(255,255,255,0.8); line-height: 1.6;">
                        <?php _e('Maximize your CSR investment with tax-deductible contributions while fulfilling corporate social responsibility objectives.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Sponsors -->
    <section id="current-sponsors" class="current-sponsors" style="padding: 100px 0; background: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 3rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Our Valued Sponsors', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Meet the forward-thinking organizations that support our mission and make our impact possible.', 'kilismile'); ?>
                </p>
            </div>

            <!-- Platinum Sponsors -->
            <div style="margin-bottom: 60px;">
                <h3 style="text-align: center; color: #2c3e50; font-size: 1.8rem; margin-bottom: 40px; font-weight: 600;">
                    <i class="fas fa-crown" style="color: #f1c40f; margin-right: 10px;"></i>
                    <?php _e('Platinum Sponsors', 'kilismile'); ?>
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <!-- Example Platinum Sponsor -->
                    <div class="sponsor-card" style="background: linear-gradient(135deg, #f8f9fa, #e9ecef); border: 2px solid #f1c40f; border-radius: 15px; padding: 30px; text-align: center; transition: transform 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                        <div style="background: #f1c40f; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; box-shadow: 0 10px 20px rgba(241,196,15,0.3);">
                            <i class="fas fa-building" style="color: white; font-size: 2rem;"></i>
                        </div>
                        <h4 style="color: #2c3e50; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                            <?php _e('TechCorp International', 'kilismile'); ?>
                        </h4>
                        <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 20px;">
                            <?php _e('Leading technology solutions provider supporting digital health initiatives across Tanzania.', 'kilismile'); ?>
                        </p>
                        <div style="background: rgba(241,196,15,0.1); padding: 10px; border-radius: 10px; color: #f1c40f; font-weight: 600; font-size: 0.9rem;">
                            <?php _e('Partnership since 2023', 'kilismile'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gold Sponsors -->
            <div style="margin-bottom: 60px;">
                <h3 style="text-align: center; color: #2c3e50; font-size: 1.8rem; margin-bottom: 40px; font-weight: 600;">
                    <i class="fas fa-medal" style="color: #f39c12; margin-right: 10px;"></i>
                    <?php _e('Gold Sponsors', 'kilismile'); ?>
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
                    <!-- Example Gold Sponsors -->
                    <div class="sponsor-card" style="background: white; border: 2px solid #f39c12; border-radius: 15px; padding: 25px; text-align: center; transition: transform 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <div style="background: #f39c12; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-heart" style="color: white; font-size: 1.5rem;"></i>
                        </div>
                        <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 10px; font-weight: 600;">
                            <?php _e('HealthPlus Foundation', 'kilismile'); ?>
                        </h4>
                        <p style="color: #7f8c8d; line-height: 1.5; font-size: 0.9rem;">
                            <?php _e('Healthcare advocacy and community wellness programs.', 'kilismile'); ?>
                        </p>
                    </div>

                    <div class="sponsor-card" style="background: white; border: 2px solid #f39c12; border-radius: 15px; padding: 25px; text-align: center; transition: transform 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <div style="background: #f39c12; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-leaf" style="color: white; font-size: 1.5rem;"></i>
                        </div>
                        <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 10px; font-weight: 600;">
                            <?php _e('EcoVision Ltd', 'kilismile'); ?>
                        </h4>
                        <p style="color: #7f8c8d; line-height: 1.5; font-size: 0.9rem;">
                            <?php _e('Sustainable development and environmental health solutions.', 'kilismile'); ?>
                        </p>
                    </div>

                    <div class="sponsor-card" style="background: white; border: 2px solid #f39c12; border-radius: 15px; padding: 25px; text-align: center; transition: transform 0.3s ease; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <div style="background: #f39c12; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                            <i class="fas fa-graduation-cap" style="color: white; font-size: 1.5rem;"></i>
                        </div>
                        <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 10px; font-weight: 600;">
                            <?php _e('EduTech Solutions', 'kilismile'); ?>
                        </h4>
                        <p style="color: #7f8c8d; line-height: 1.5; font-size: 0.9rem;">
                            <?php _e('Educational technology and digital learning platforms.', 'kilismile'); ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Silver Sponsors -->
            <div>
                <h3 style="text-align: center; color: #2c3e50; font-size: 1.8rem; margin-bottom: 40px; font-weight: 600;">
                    <i class="fas fa-trophy" style="color: #95a5a6; margin-right: 10px;"></i>
                    <?php _e('Silver Sponsors', 'kilismile'); ?>
                </h3>
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; align-items: center;">
                    <!-- Silver sponsor logos/names in a more compact format -->
                    <div class="silver-sponsor" style="background: white; border: 1px solid #95a5a6; border-radius: 10px; padding: 20px; text-align: center; min-width: 180px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                        <div style="background: #95a5a6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                            <i class="fas fa-store" style="color: white; font-size: 1rem;"></i>
                        </div>
                        <div style="color: #2c3e50; font-weight: 600; font-size: 0.9rem;"><?php _e('Local Business Co-op', 'kilismile'); ?></div>
                    </div>

                    <div class="silver-sponsor" style="background: white; border: 1px solid #95a5a6; border-radius: 10px; padding: 20px; text-align: center; min-width: 180px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                        <div style="background: #95a5a6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                            <i class="fas fa-handshake" style="color: white; font-size: 1rem;"></i>
                        </div>
                        <div style="color: #2c3e50; font-weight: 600; font-size: 0.9rem;"><?php _e('Community Partners', 'kilismile'); ?></div>
                    </div>

                    <div class="silver-sponsor" style="background: white; border: 1px solid #95a5a6; border-radius: 10px; padding: 20px; text-align: center; min-width: 180px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                        <div style="background: #95a5a6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                            <i class="fas fa-utensils" style="color: white; font-size: 1rem;"></i>
                        </div>
                        <div style="color: #2c3e50; font-weight: 600; font-size: 0.9rem;"><?php _e('Restaurant Alliance', 'kilismile'); ?></div>
                    </div>

                    <div class="silver-sponsor" style="background: white; border: 1px solid #95a5a6; border-radius: 10px; padding: 20px; text-align: center; min-width: 180px; box-shadow: 0 3px 15px rgba(0,0,0,0.1);">
                        <div style="background: #95a5a6; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                            <i class="fas fa-car" style="color: white; font-size: 1rem;"></i>
                        </div>
                        <div style="color: #2c3e50; font-weight: 600; font-size: 0.9rem;"><?php _e('Transport Services', 'kilismile'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="sponsor-contact" style="padding: 100px 0; background: white;">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <h2 style="color: #2c3e50; font-size: 2.8rem; margin-bottom: 20px; font-weight: 700;">
                <?php _e('Ready to Make a Difference?', 'kilismile'); ?>
            </h2>
            <p style="color: #7f8c8d; font-size: 1.3rem; margin-bottom: 40px; line-height: 1.6;">
                <?php _e('Join our community of corporate sponsors and help us create lasting change in healthcare accessibility and community wellness.', 'kilismile'); ?>
            </p>
            
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-bottom: 50px;">
                <a href="<?php echo home_url('/become-partner'); ?>" class="cta-button" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 18px 35px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(39,174,96,0.3);">
                    <i class="fas fa-handshake" style="margin-right: 10px;"></i>
                    <?php _e('Become a Sponsor', 'kilismile'); ?>
                </a>
                <a href="<?php echo home_url('/contact'); ?>" class="cta-button" style="background: rgba(52,152,219,0.1); color: #3498db; padding: 18px 35px; border: 2px solid #3498db; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                    <i class="fas fa-phone" style="margin-right: 10px;"></i>
                    <?php _e('Schedule Meeting', 'kilismile'); ?>
                </a>
            </div>

            <div style="background: #f8f9fa; padding: 40px; border-radius: 20px; margin-top: 40px;">
                <h3 style="color: #2c3e50; font-size: 1.5rem; margin-bottom: 20px; font-weight: 600;">
                    <?php _e('Contact Our Sponsorship Team', 'kilismile'); ?>
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: center;">
                    <div>
                        <i class="fas fa-envelope" style="color: #3498db; font-size: 1.5rem; margin-bottom: 10px;"></i>
                        <div style="color: #2c3e50; font-weight: 600;">Email</div>
                        <div style="color: #7f8c8d;">sponsors@kilismile.org</div>
                    </div>
                    <div>
                        <i class="fas fa-phone" style="color: #27ae60; font-size: 1.5rem; margin-bottom: 10px;"></i>
                        <div style="color: #2c3e50; font-weight: 600;">Phone</div>
                        <div style="color: #7f8c8d;">+255763495575/+255735495575</div>
                    </div>
                    <div>
                        <i class="fas fa-calendar" style="color: #f39c12; font-size: 1.5rem; margin-bottom: 10px;"></i>
                        <div style="color: #2c3e50; font-weight: 600;">Schedule</div>
                        <div style="color: #7f8c8d;">Mon-Fri 9AM-5PM</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for sponsor CTA buttons
    document.querySelectorAll('.sponsor-cta').forEach(button => {
        button.addEventListener('click', function() {
            const packageType = this.textContent.trim();
            
            // You can integrate with your contact form or partnership application
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'sponsorship',
                    'event_label': packageType
                });
            }
            
            // Redirect to partnership application with package type
            const partnershipUrl = '<?php echo home_url("/become-partner"); ?>?package=' + 
                                 encodeURIComponent(packageType.toLowerCase().replace(/\s+/g, '-'));
            window.location.href = partnershipUrl;
        });
    });
    
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
    
    // Animate statistics on scroll
    const observerOptions = {
        threshold: 0.5,
        triggerOnce: true
    };
    
    if ('IntersectionObserver' in window) {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statNumbers = entry.target.querySelectorAll('.stat-item div:nth-child(2)');
                    statNumbers.forEach(stat => {
                        const finalValue = stat.textContent;
                        animateNumber(stat, finalValue);
                    });
                }
            });
        }, observerOptions);
        
        const statsSection = document.querySelector('.corporate-sponsors-hero');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }
    }
    
    function animateNumber(element, finalValue) {
        const isMonetary = finalValue.includes('$');
        const hasPlus = finalValue.includes('+');
        const isPercentage = finalValue.includes('%');
        
        let numericValue = parseFloat(finalValue.replace(/[^\d.]/g, ''));
        let current = 0;
        const increment = numericValue / 50;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= numericValue) {
                current = numericValue;
                clearInterval(timer);
            }
            
            let displayValue = current;
            if (isMonetary) {
                displayValue = '$' + (current >= 1000000 ? 
                                   (current / 1000000).toFixed(1) + 'M' : 
                                   current.toLocaleString());
            } else if (current >= 1000) {
                displayValue = (current / 1000).toFixed(0) + 'K';
            } else {
                displayValue = Math.floor(current).toLocaleString();
            }
            
            if (hasPlus) displayValue += '+';
            if (isPercentage) displayValue += '%';
            
            element.textContent = displayValue;
        }, 20);
    }

    // Add hover effects for interactive elements
    const interactiveElements = document.querySelectorAll('.sponsor-card, .benefit-card, .silver-sponsor, .stat-item');
    interactiveElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Show notification for CTA clicks
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#27ae60' : '#3498db'};
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            z-index: 1000;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Animate out
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Add click tracking for CTA buttons
    document.querySelectorAll('.cta-button').forEach(button => {
        button.addEventListener('click', function(e) {
            const buttonText = this.textContent.trim();
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'corporate_sponsors',
                    'event_label': buttonText
                });
            }
            
            showNotification('<?php _e('Redirecting to partnership application...', 'kilismile'); ?>', 'info');
        });
    });
});

function selectPackage(packageType) {
    // Redirect to partnership application with package type
    const partnershipUrl = '<?php echo home_url("/become-partner"); ?>?package=' + 
                         encodeURIComponent(packageType.toLowerCase().replace(/\s+/g, '-'));
    
    // Track the package selection
    if (typeof gtag !== 'undefined') {
        gtag('event', 'select_sponsorship_package', {
            'event_category': 'sponsorship',
            'event_label': packageType
        });
    }
    
    window.location.href = partnershipUrl;
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 10);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            if (document.body.contains(notification)) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}
</script>

<style>
/* Corporate Sponsors Page Styles */
.sponsor-package:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.2);
}

.sponsor-package.platinum:hover {
    box-shadow: 0 25px 60px rgba(241,196,15,0.3);
}

.sponsor-cta:hover {
    transform: scale(1.05);
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}

.sponsor-card:hover,
.silver-sponsor:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.benefit-card:hover {
    transform: translateY(-5px);
    background: rgba(255,255,255,0.15);
}

.stat-item:hover {
    transform: translateY(-5px);
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

.cta-primary:hover,
.cta-secondary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
}

.package-card:hover,
.benefit-card:hover,
.sponsor-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.package-card button:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

/* Responsive Design */
@media (max-width: 768px) {
    .corporate-sponsors-hero h1 {
        font-size: 2.8rem;
    }
    
    .corporate-sponsors-hero p {
        font-size: 1.2rem;
    }
    
    .corporate-sponsors-hero .cta-primary,
    .corporate-sponsors-hero .cta-secondary {
        padding: 12px 25px !important;
        font-size: 0.9rem;
    }
    
    .sponsorship-packages .container > div,
    .current-sponsors .container > div {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 25px;
    }
    
    .sponsorship-benefits .container > div {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 30px;
    }
    
    .package-card,
    .benefit-card,
    .sponsor-card {
        min-height: auto !important;
    }
    
    .package-header {
        padding: 20px !important;
    }
    
    .package-content {
        padding: 20px !important;
    }
}

@media (max-width: 480px) {
    .corporate-sponsors-hero {
        padding: 80px 0 60px;
    }
    
    .corporate-sponsors-hero h1 {
        font-size: 2.2rem;
    }
    
    .sponsorship-packages,
    .current-sponsors,
    .sponsorship-benefits,
    .sponsor-contact {
        padding: 60px 0;
    }
    
    .sponsorship-packages .container > div,
    .current-sponsors .container > div,
    .sponsorship-benefits .container > div {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .stat-item {
        padding: 15px;
    }
    
    .package-card {
        min-height: 450px !important;
        padding: 30px 20px;
    }
    
    .corporate-sponsors-hero > div:last-child {
        width: 95% !important;
        padding: 30px 20px !important;
    }
    
    .cta-button {
        padding: 15px 25px !important;
        font-size: 0.9rem;
    }
}
</style>

<?php get_footer(); ?>


