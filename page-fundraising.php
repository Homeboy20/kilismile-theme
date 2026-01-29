<?php
/**
 * Template Name: Fundraising Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="fundraising-hero" style="background: var(--light-gray); color: var(--dark-green); padding: 120px 0 80px; text-align: center; border-bottom: 4px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; color: var(--dark-green); font-weight: 700;">
                <?php _e('Fundraising With Kilismile', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; color: var(--text-secondary); line-height: 1.6;">
                <?php _e('Join us in creating sustainable change through innovative fundraising initiatives that directly impact health education in Tanzania communities.', 'kilismile'); ?>
            </p>
            <div class="fundraising-stats" style="display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; margin-top: 40px;">
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">$250K+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Funds Raised', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">50+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Active Campaigns', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">1,200+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Donors', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fundraising Opportunities -->
    <section class="fundraising-opportunities" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Fundraising Opportunities', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Discover various ways to support our mission and make a lasting impact in Tanzania communities.', 'kilismile'); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; align-items: stretch;">
                <!-- Individual Campaigns -->
                <div class="fundraising-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 400px;">
                    <div class="card-header" style="background: var(--primary-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-user-heart" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Individual Campaigns', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Start your own personal fundraising campaign to support specific health education programs in your community or beyond.', 'kilismile'); ?>
                        </p>
                        
                        <div class="features" style="margin-bottom: 20px;">
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Custom fundraising page', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Social media toolkit', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Progress tracking', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="startCampaign('individual')" 
                                style="width: 100%; padding: 12px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Start Campaign', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Corporate Challenges -->
                <div class="fundraising-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 400px;">
                    <div class="card-header" style="background: var(--accent-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-building" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Corporate Challenges', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Organize team-building fundraising challenges that engage employees while supporting our health education mission.', 'kilismile'); ?>
                        </p>
                        
                        <div class="features" style="margin-bottom: 20px;">
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Team leaderboards', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Employee engagement', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('CSR reporting', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="startCampaign('corporate')" 
                                style="width: 100%; padding: 12px; background: var(--accent-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Learn More', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Community Events -->
                <div class="fundraising-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 400px;">
                    <div class="card-header" style="background: var(--dark-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-calendar-check" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Community Events', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Host community fundraising events like health fairs, awareness walks, and educational workshops.', 'kilismile'); ?>
                        </p>
                        
                        <div class="features" style="margin-bottom: 20px;">
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Event planning support', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Marketing materials', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Local partnerships', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="startCampaign('community')" 
                                style="width: 100%; padding: 12px; background: var(--dark-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Plan Event', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Online Campaigns -->
                <div class="fundraising-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 400px;">
                    <div class="card-header" style="background: var(--primary-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-globe" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Online Campaigns', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Launch digital fundraising campaigns through social media, crowdfunding platforms, and online communities.', 'kilismile'); ?>
                        </p>
                        
                        <div class="features" style="margin-bottom: 20px;">
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Digital marketing', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Global reach', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 10px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 10px;" aria-hidden="true"></i>
                                <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php _e('Real-time tracking', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="startCampaign('online')" 
                                style="width: 100%; padding: 12px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Go Digital', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Campaigns -->
    <section class="current-campaigns" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 30px;">
                <?php _e('Current Fundraising Campaigns', 'kilismile'); ?>
            </h2>
            
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
                <i class="fas fa-calendar-check" style="font-size: 4rem; color: var(--primary-green); margin-bottom: 20px;" aria-hidden="true"></i>
                <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.8rem;">
                    <?php _e('Campaigns Coming Soon', 'kilismile'); ?>
                </h3>
                <p style="color: var(--text-secondary); line-height: 1.8; max-width: 600px; margin: 0 auto 30px; font-size: 1.1rem;">
                    <?php _e('We are currently planning several fundraising campaigns to support our programs and community initiatives. These campaigns will be announced soon. Stay connected with us through our social media channels and newsletter for updates.', 'kilismile'); ?>
                </p>
                <a href="<?php echo esc_url(home_url('/donate')); ?>" 
                   style="display: inline-block; padding: 15px 40px; background: var(--primary-green); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Support Our Work - Donate Now', 'kilismile'); ?>
                </a>
            </div>

            <!-- Commented out campaigns for future use -->
            <div style="display: none;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
                <!-- Campaign 1 -->
                <div class="campaign-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="height: 200px; background: var(--primary-green); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="fas fa-stethoscope" aria-hidden="true"></i>
                    </div>
                    <div style="padding: 30px;">
                        <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;"><?php _e('Medical Equipment Drive', 'kilismile'); ?></h3>
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                            <?php _e('Help us purchase essential medical equipment for rural health education centers.', 'kilismile'); ?>
                        </p>
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="font-weight: 600; color: var(--dark-green);"><?php _e('Progress:', 'kilismile'); ?></span>
                                <span style="color: var(--primary-green); font-weight: bold;">65%</span>
                            </div>
                            <div style="width: 100%; height: 10px; background: #e0e0e0; border-radius: 5px; overflow: hidden;">
                                <div style="width: 65%; height: 100%; background: var(--primary-green);"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);">
                                <span>$16,250 raised</span>
                                <span>Goal: $25,000</span>
                            </div>
                        </div>
                        <button style="width: 100%; padding: 12px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <?php _e('Donate Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Campaign 2 -->
                <div class="campaign-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="height: 200px; background: var(--accent-green); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    </div>
                    <div style="padding: 30px;">
                        <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;"><?php _e('Education Material Fund', 'kilismile'); ?></h3>
                        <p style="color: var(--text-secondary); line-line: 1.6; margin-bottom: 20px;">
                            <?php _e('Support the creation and distribution of health education materials in local languages.', 'kilismile'); ?>
                        </p>
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="font-weight: 600; color: var(--dark-green);"><?php _e('Progress:', 'kilismile'); ?></span>
                                <span style="color: var(--primary-green); font-weight: bold;">42%</span>
                            </div>
                            <div style="width: 100%; height: 10px; background: #e0e0e0; border-radius: 5px; overflow: hidden;">
                                <div style="width: 42%; height: 100%; background: var(--accent-green);"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);">
                                <span>$8,400 raised</span>
                                <span>Goal: $20,000</span>
                            </div>
                        </div>
                        <button style="width: 100%; padding: 12px; background: var(--accent-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <?php _e('Donate Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Campaign 3 -->
                <div class="campaign-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="height: 200px; background: var(--dark-green); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                        <i class="fas fa-hands-helping" aria-hidden="true"></i>
                    </div>
                    <div style="padding: 30px;">
                        <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;"><?php _e('Community Outreach Program', 'kilismile'); ?></h3>
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px;">
                            <?php _e('Expand our reach to remote communities through mobile health education units.', 'kilismile'); ?>
                        </p>
                        <div style="margin-bottom: 20px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                <span style="font-weight: 600; color: var(--dark-green);"><?php _e('Progress:', 'kilismile'); ?></span>
                                <span style="color: var(--primary-green); font-weight: bold;">78%</span>
                            </div>
                            <div style="width: 100%; height: 10px; background: #e0e0e0; border-radius: 5px; overflow: hidden;">
                                <div style="width: 78%; height: 100%; background: var(--dark-green);"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.9rem; color: var(--text-secondary);">
                                <span>$23,400 raised</span>
                                <span>Goal: $30,000</span>
                            </div>
                        </div>
                        <button style="width: 100%; padding: 12px; background: var(--dark-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                            <?php _e('Donate Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How Funds Are Used -->
    <section class="fund-usage" style="padding: 100px 0;">
        <div class="container">
            <div style="max-width: 1000px; margin: 0 auto;">
                <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('How Your Donations Make an Impact', 'kilismile'); ?>
                </h2>
                <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                    <?php _e('Every dollar donated goes directly toward improving health education and community well-being in Tanzania.', 'kilismile'); ?>
                </p>

                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 60px; align-items: center;">
                    <div class="fund-breakdown">
                        <h3 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 30px;"><?php _e('Fund Allocation', 'kilismile'); ?></h3>
                        
                        <div style="margin-bottom: 25px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 600; color: var(--dark-green);"><?php _e('Direct Programs', 'kilismile'); ?></span>
                                <span style="color: var(--primary-green); font-weight: bold;">75%</span>
                            </div>
                            <div style="width: 100%; height: 12px; background: #e0e0e0; border-radius: 6px; overflow: hidden;">
                                <div style="width: 75%; height: 100%; background: var(--primary-green);"></div>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 600; color: var(--dark-green);"><?php _e('Administration', 'kilismile'); ?></span>
                                <span style="color: var(--accent-green); font-weight: bold;">15%</span>
                            </div>
                            <div style="width: 100%; height: 12px; background: #e0e0e0; border-radius: 6px; overflow: hidden;">
                                <div style="width: 15%; height: 100%; background: var(--accent-green);"></div>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-weight: 600; color: var(--dark-green);"><?php _e('Fundraising', 'kilismile'); ?></span>
                                <span style="color: var(--dark-green); font-weight: bold;">10%</span>
                            </div>
                            <div style="width: 100%; height: 12px; background: #e0e0e0; border-radius: 6px; overflow: hidden;">
                                <div style="width: 10%; height: 100%; background: var(--dark-green);"></div>
                            </div>
                        </div>
                    </div>

                    <div class="impact-examples">
                        <h3 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 30px;"><?php _e('Your Impact', 'kilismile'); ?></h3>
                        
                        <div style="background: var(--light-gray); padding: 25px; border-radius: 15px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 15px;">
                                    <span style="font-weight: bold;">$50</span>
                                </div>
                                <div>
                                    <h4 style="color: var(--dark-green); margin: 0; font-size: 1.1rem;"><?php _e('Health Education Kit', 'kilismile'); ?></h4>
                                </div>
                            </div>
                            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem; line-height: 1.5;">
                                <?php _e('Provides educational materials for one community health workshop', 'kilismile'); ?>
                            </p>
                        </div>

                        <div style="background: var(--light-gray); padding: 25px; border-radius: 15px; margin-bottom: 20px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div style="width: 50px; height: 50px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 15px;">
                                    <span style="font-weight: bold;">$200</span>
                                </div>
                                <div>
                                    <h4 style="color: var(--dark-green); margin: 0; font-size: 1.1rem;"><?php _e('Training Program', 'kilismile'); ?></h4>
                                </div>
                            </div>
                            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem; line-height: 1.5;">
                                <?php _e('Trains one community health educator for one month', 'kilismile'); ?>
                            </p>
                        </div>

                        <div style="background: var(--light-gray); padding: 25px; border-radius: 15px;">
                            <div style="display: flex; align-items: center; margin-bottom: 15px;">
                                <div style="width: 50px; height: 50px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; margin-right: 15px;">
                                    <span style="font-weight: bold;">$500</span>
                                </div>
                                <div>
                                    <h4 style="color: var(--dark-green); margin: 0; font-size: 1.1rem;"><?php _e('Mobile Clinic', 'kilismile'); ?></h4>
                                </div>
                            </div>
                            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem; line-height: 1.5;">
                                <?php _e('Supports one mobile health clinic visit to remote communities', 'kilismile'); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Get Started Section -->
    <section class="get-started" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Ready to Start Fundraising?', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1.2rem; margin-bottom: 40px; line-height: 1.6;">
                    <?php _e('Join our community of fundraisers and make a lasting impact on health education in Tanzania.', 'kilismile'); ?>
                </p>

                <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                    <button onclick="startFundraising()" 
                            style="padding: 15px 30px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <?php _e('Start Fundraising', 'kilismile'); ?>
                        <i class="fas fa-arrow-right" style="margin-left: 10px;" aria-hidden="true"></i>
                    </button>
                    <button onclick="learnMore()" 
                            style="padding: 15px 30px; background: transparent; color: var(--dark-green); border: 2px solid var(--dark-green); border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <?php _e('Learn More', 'kilismile'); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
function startCampaign(type) {
    let message = '';
    switch(type) {
        case 'individual':
            message = '<?php _e('Individual campaign form will open here', 'kilismile'); ?>';
            break;
        case 'corporate':
            message = '<?php _e('Corporate challenge information will be displayed', 'kilismile'); ?>';
            break;
        case 'community':
            message = '<?php _e('Community event planning tools will be available', 'kilismile'); ?>';
            break;
        case 'online':
            message = '<?php _e('Online campaign setup will begin', 'kilismile'); ?>';
            break;
    }
    alert(message);
}

function startFundraising() {
    alert('<?php _e('Fundraising application form will open here', 'kilismile'); ?>');
}

function learnMore() {
    alert('<?php _e('More detailed information about fundraising will be displayed', 'kilismile'); ?>');
}
</script>

<style>
.fundraising-card:hover,
.campaign-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.fundraising-card button:hover,
.campaign-card button:hover,
.get-started button:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

@media (max-width: 768px) {
    .fundraising-opportunities .container > div,
    .current-campaigns .container > div {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 25px;
    }
    
    .fund-usage .container > div > div {
        grid-template-columns: 1fr !important;
        gap: 40px;
    }
    
    .fundraising-card,
    .campaign-card {
        min-height: 350px !important;
    }
    
    .card-header,
    .campaign-card > div:first-child {
        padding: 20px !important;
    }
    
    .card-content,
    .campaign-card > div:last-child {
        padding: 20px !important;
    }
}

@media (max-width: 480px) {
    .fundraising-opportunities .container > div,
    .current-campaigns .container > div {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .fundraising-card,
    .campaign-card {
        min-height: 320px !important;
    }
    
    .get-started .container > div > div {
        flex-direction: column;
        align-items: center;
    }
    
    .get-started button {
        width: 100%;
        max-width: 300px;
    }
}
</style>

<?php get_footer(); ?>


