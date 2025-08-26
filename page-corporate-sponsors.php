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
    <section class="sponsors-hero" style="background: var(--light-gray); color: var(--dark-green); padding: 120px 0 80px; text-align: center; border-bottom: 4px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; color: var(--dark-green); font-weight: 700;">
                <?php _e('Corporate Sponsorship', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; color: var(--text-secondary); line-height: 1.6;">
                <?php _e('Partner with Kili Smile to enhance your corporate social responsibility while making a meaningful impact on health education in Tanzania.', 'kilismile'); ?>
            </p>
            <div class="sponsor-stats" style="display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; margin-top: 40px;">
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">25+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Corporate Sponsors', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">$500K+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Sponsorship Value', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">75K+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Lives Impacted', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsorship Packages -->
    <section class="sponsorship-packages" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Sponsorship Packages', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Choose a sponsorship level that aligns with your corporate goals and budget while maximizing your social impact.', 'kilismile'); ?>
            </p>

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

    <!-- Why Sponsor Us -->
    <section class="why-sponsor" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Why Sponsor Kili Smile?', 'kilismile'); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
                <!-- CSR Impact -->
                <div class="benefit-card" style="background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2.2rem;">
                        <i class="fas fa-heart" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.5rem;">
                        <?php _e('Meaningful CSR Impact', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7;">
                        <?php _e('Demonstrate genuine corporate social responsibility with measurable impact on health education and community development in Tanzania.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Brand Visibility -->
                <div class="benefit-card" style="background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 80px; height: 80px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2.2rem;">
                        <i class="fas fa-bullhorn" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.5rem;">
                        <?php _e('Enhanced Brand Visibility', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7;">
                        <?php _e('Increase brand awareness and positive association through strategic co-branding opportunities and community engagement initiatives.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Employee Engagement -->
                <div class="benefit-card" style="background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 80px; height: 80px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2.2rem;">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 20px; font-size: 1.5rem;">
                        <?php _e('Employee Engagement', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.7;">
                        <?php _e('Boost employee morale and retention through meaningful volunteer opportunities and purpose-driven corporate initiatives.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Sponsors -->
    <section class="current-sponsors" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Our Valued Corporate Sponsors', 'kilismile'); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; align-items: center; text-align: center;">
                <!-- Sponsor Logos/Cards -->
                <div class="sponsor-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 60px; height: 60px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 1.5rem;">
                        <i class="fas fa-building" aria-hidden="true"></i>
                    </div>
                    <h4 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1.1rem;"><?php _e('TechCorp Solutions', 'kilismile'); ?></h4>
                    <span style="background: #FFD700; color: var(--dark-green); padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                        <?php _e('Gold Sponsor', 'kilismile'); ?>
                    </span>
                </div>

                <div class="sponsor-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 60px; height: 60px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 1.5rem;">
                        <i class="fas fa-heartbeat" aria-hidden="true"></i>
                    </div>
                    <h4 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1.1rem;"><?php _e('MediHealth Group', 'kilismile'); ?></h4>
                    <span style="background: #E5E4E2; color: var(--dark-green); padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                        <?php _e('Platinum Sponsor', 'kilismile'); ?>
                    </span>
                </div>

                <div class="sponsor-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 60px; height: 60px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 1.5rem;">
                        <i class="fas fa-leaf" aria-hidden="true"></i>
                    </div>
                    <h4 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1.1rem;"><?php _e('EcoSustain Ltd', 'kilismile'); ?></h4>
                    <span style="background: #C0C0C0; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                        <?php _e('Silver Sponsor', 'kilismile'); ?>
                    </span>
                </div>

                <div class="sponsor-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="width: 60px; height: 60px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 1.5rem;">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    </div>
                    <h4 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1.1rem;"><?php _e('EduTech Innovations', 'kilismile'); ?></h4>
                    <span style="background: #CD7F32; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                        <?php _e('Bronze Sponsor', 'kilismile'); ?>
                    </span>
                </div>
            </div>
        </div>
    </section>

    <!-- Sponsorship Application -->
    <section class="sponsorship-application" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Become a Corporate Sponsor', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1.2rem; margin-bottom: 40px; line-height: 1.6;">
                    <?php _e('Join our community of corporate partners and make a lasting impact on health education while achieving your CSR goals.', 'kilismile'); ?>
                </p>

                <form id="sponsorship-form" style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: left;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Company Name', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Industry', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <select required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                                <option value=""><?php _e('Select Industry', 'kilismile'); ?></option>
                                <option value="healthcare"><?php _e('Healthcare', 'kilismile'); ?></option>
                                <option value="technology"><?php _e('Technology', 'kilismile'); ?></option>
                                <option value="finance"><?php _e('Finance', 'kilismile'); ?></option>
                                <option value="education"><?php _e('Education', 'kilismile'); ?></option>
                                <option value="manufacturing"><?php _e('Manufacturing', 'kilismile'); ?></option>
                                <option value="retail"><?php _e('Retail', 'kilismile'); ?></option>
                                <option value="other"><?php _e('Other', 'kilismile'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Contact Person', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Email Address', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="email" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('Preferred Sponsorship Level', 'kilismile'); ?> <span style="color: red;">*</span>
                        </label>
                        <select id="sponsorship-level" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                            <option value=""><?php _e('Select Sponsorship Level', 'kilismile'); ?></option>
                            <option value="bronze"><?php _e('Bronze Sponsor ($5,000+)', 'kilismile'); ?></option>
                            <option value="silver"><?php _e('Silver Sponsor ($15,000+)', 'kilismile'); ?></option>
                            <option value="gold"><?php _e('Gold Sponsor ($50,000+)', 'kilismile'); ?></option>
                            <option value="platinum"><?php _e('Platinum Sponsor ($100,000+)', 'kilismile'); ?></option>
                            <option value="custom"><?php _e('Custom Package', 'kilismile'); ?></option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('CSR Objectives', 'kilismile'); ?>
                        </label>
                        <textarea rows="4" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical;" placeholder="<?php _e('Describe your corporate social responsibility objectives and goals...', 'kilismile'); ?>"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('Specific Interests', 'kilismile'); ?>
                        </label>
                        <textarea rows="4" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical;" placeholder="<?php _e('Any specific programs or initiatives you are particularly interested in supporting?', 'kilismile'); ?>"></textarea>
                    </div>

                    <button type="submit" style="width: 100%; padding: 15px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <?php _e('Submit Sponsorship Application', 'kilismile'); ?>
                        <i class="fas fa-paper-plane" style="margin-left: 10px;" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
function selectPackage(packageType) {
    // Scroll to application form
    document.querySelector('.sponsorship-application').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
    
    // Pre-select the sponsorship level
    const levelSelect = document.getElementById('sponsorship-level');
    if (levelSelect) {
        levelSelect.value = packageType;
    }
    
    // Show notification
    showNotification('<?php _e('Sponsorship application form ready! Please fill out the details below.', 'kilismile'); ?>');
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--primary-green);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Form submission
document.getElementById('sponsorship-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php _e('Submitting...', 'kilismile'); ?>';
    submitBtn.disabled = true;
    
    // Simulate submission
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        alert('<?php _e('Thank you for your sponsorship application! Our corporate partnerships team will contact you within 2-3 business days.', 'kilismile'); ?>');
        this.reset();
    }, 2000);
});
</script>

<style>
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

@media (max-width: 768px) {
    .sponsorship-packages .container > div,
    .current-sponsors .container > div {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 25px;
    }
    
    .why-sponsor .container > div {
        grid-template-columns: 1fr !important;
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
    
    .sponsorship-application form > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .sponsorship-packages .container > div,
    .current-sponsors .container > div {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .package-card {
        min-height: 450px !important;
    }
}
</style>

<?php get_footer(); ?>
