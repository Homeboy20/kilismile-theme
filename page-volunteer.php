<?php
/**
 * Template Name: Volunteer Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="volunteer-hero" style="background: var(--light-gray); color: var(--dark-green); padding: 120px 0 80px; text-align: center; border-bottom: 4px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; color: var(--dark-green); font-weight: 700;">
                <?php _e('Join Our Volunteer Community', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; color: var(--text-secondary); line-height: 1.6;">
                <?php _e('Make a meaningful difference in Tanzania communities. Whether you have an hour or a year to give, your contribution matters.', 'kilismile'); ?>
            </p>
            <div class="volunteer-stats" style="display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; margin-top: 40px;">
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">500+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Active Volunteers', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">15</div>
                    <div style="color: var(--text-secondary);"><?php _e('Volunteer Programs', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px;">10,000+</div>
                    <div style="opacity: 0.9;"><?php _e('Hours Contributed', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Volunteer Section -->
    <section class="why-volunteer" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Why Volunteer With Us?', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Volunteering with Kilismile is more than giving back—it\'s about growing, learning, and creating lasting impact.', 'kilismile'); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; align-items: stretch;">
                <!-- Make Impact -->
                <div class="benefit-card" style="background: white; padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="icon" style="width: 70px; height: 70px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-hands-helping" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;">
                        <?php _e('Create Real Impact', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; flex-grow: 1; font-size: 0.95rem;">
                        <?php _e('See the direct results of your efforts as you help improve health education and community well-being across Tanzania.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Skills Development -->
                <div class="benefit-card" style="background: white; padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="icon" style="width: 70px; height: 70px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-user-graduate" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;">
                        <?php _e('Develop Skills', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; flex-grow: 1; font-size: 0.95rem;">
                        <?php _e('Gain valuable experience in healthcare, education, project management, and community development while making a difference.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Community -->
                <div class="benefit-card" style="background: white; padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="icon" style="width: 70px; height: 70px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-users" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;">
                        <?php _e('Join Community', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; flex-grow: 1; font-size: 0.95rem;">
                        <?php _e('Connect with like-minded individuals who share your passion for health education and community development.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Flexibility -->
                <div class="benefit-card" style="background: white; padding: 30px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="icon" style="width: 70px; height: 70px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-clock" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;">
                        <?php _e('Flexible Commitment', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; flex-grow: 1; font-size: 0.95rem;">
                        <?php _e('Choose from various time commitments and volunteer opportunities that fit your schedule and availability.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Volunteer Opportunities -->
    <section class="volunteer-opportunities" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Volunteer Opportunities', 'kilismile'); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px; margin-bottom: 60px;">
                <!-- Health Education -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-heartbeat" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Health Education', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Deliver health education workshops and training sessions to community members.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('4-8 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Communication', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Communities', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('health-education')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Community Outreach -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-bullhorn" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Community Outreach', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Engage with local communities to promote our programs and connect people with resources.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('6-10 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Social skills', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Rural areas', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('community-outreach')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Administrative Support -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-laptop" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Administrative Support', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Support operations with data entry, research, social media management and admin tasks.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('3-6 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Computer skills', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Office/Remote', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('administrative')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Event Support -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-calendar-alt" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Event Support', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Help organize and run health fairs, workshops, fundraising events and community gatherings.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('1-3 days/month', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Organization', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Event venues', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('event-support')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Opportunities Row -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 25px;">
                <!-- Fundraising -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-hand-holding-heart" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Fundraising', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Help organize fundraising campaigns, grant writing, and donor engagement activities.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('5-8 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Writing, Sales', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Office/Remote', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('fundraising')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-share-alt" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Social Media', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Manage our social media presence, create content, and engage with our online community.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('3-5 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Social media', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Remote', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('social-media')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Translation -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-language" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Translation', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Help translate educational materials and communications into local languages.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('2-4 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Multilingual', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Remote', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('translation')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Research -->
                <div class="opportunity-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 350px;">
                    <div class="opportunity-header" style="background: var(--primary-green); color: white; padding: 20px; text-align: center;">
                        <i class="fas fa-search" style="font-size: 2rem; margin-bottom: 10px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.1rem; color: white; line-height: 1.3;"><?php _e('Research', 'kilismile'); ?></h3>
                    </div>
                    <div class="opportunity-content" style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.9rem; flex-grow: 1;">
                            <?php _e('Conduct research on health topics, best practices, and program evaluation methods.', 'kilismile'); ?>
                        </p>
                        
                        <div class="opportunity-details" style="margin-bottom: 15px;">
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Time:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('4-6 hrs/week', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Skills:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Research skills', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; font-size: 0.85rem;">
                                <strong style="color: var(--dark-green);"><?php _e('Location:', 'kilismile'); ?></strong>
                                <span style="color: var(--text-secondary);"><?php _e('Remote', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyForOpportunity('research')" 
                                class="apply-btn" 
                                style="width: 100%; padding: 10px; background: var(--primary-green); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; font-size: 0.9rem; margin-top: auto;">
                            <?php _e('Apply Now', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Volunteer Application Form -->
    <section class="volunteer-application" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Ready to Get Started?', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1.2rem; margin-bottom: 40px; line-height: 1.6;">
                    <?php _e('Fill out our volunteer application form and we\'ll match you with the perfect opportunity based on your interests and availability.', 'kilismile'); ?>
                </p>

                <form id="volunteer-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: left;">
                    <?php wp_nonce_field('kilismile_volunteer_form', 'volunteer_nonce'); ?>
                    <input type="hidden" name="action" value="kilismile_volunteer_form">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="v_first_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('First Name', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="text" 
                                   id="v_first_name" 
                                   name="first_name" 
                                   required 
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>

                        <div class="form-group">
                            <label for="v_last_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Last Name', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="text" 
                                   id="v_last_name" 
                                   name="last_name" 
                                   required 
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="v_email" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Email Address', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="email" 
                                   id="v_email" 
                                   name="email" 
                                   required 
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>

                        <div class="form-group">
                            <label for="v_phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Phone Number', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="tel" 
                                   id="v_phone" 
                                   name="phone" 
                                   required 
                                   style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="v_interests" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('Areas of Interest', 'kilismile'); ?> <span style="color: red;">*</span>
                        </label>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="interests[]" value="health-education">
                                <?php _e('Health Education', 'kilismile'); ?>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="interests[]" value="community-outreach">
                                <?php _e('Community Outreach', 'kilismile'); ?>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="interests[]" value="administrative">
                                <?php _e('Administrative Support', 'kilismile'); ?>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="interests[]" value="event-support">
                                <?php _e('Event Support', 'kilismile'); ?>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="interests[]" value="fundraising">
                                <?php _e('Fundraising', 'kilismile'); ?>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="checkbox" name="interests[]" value="social-media">
                                <?php _e('Social Media', 'kilismile'); ?>
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label for="v_availability" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Availability', 'kilismile'); ?>
                            </label>
                            <select id="v_availability" 
                                    name="availability" 
                                    style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                                <option value="1-3-hours"><?php _e('1-3 hours per week', 'kilismile'); ?></option>
                                <option value="4-6-hours"><?php _e('4-6 hours per week', 'kilismile'); ?></option>
                                <option value="7-10-hours"><?php _e('7-10 hours per week', 'kilismile'); ?></option>
                                <option value="more-than-10"><?php _e('More than 10 hours per week', 'kilismile'); ?></option>
                                <option value="event-based"><?php _e('Event-based only', 'kilismile'); ?></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="v_experience" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Volunteer Experience', 'kilismile'); ?>
                            </label>
                            <select id="v_experience" 
                                    name="experience" 
                                    style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                                <option value="none"><?php _e('No previous experience', 'kilismile'); ?></option>
                                <option value="some"><?php _e('Some volunteer experience', 'kilismile'); ?></option>
                                <option value="extensive"><?php _e('Extensive volunteer experience', 'kilismile'); ?></option>
                                <option value="professional"><?php _e('Professional nonprofit experience', 'kilismile'); ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="v_skills" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('Skills & Qualifications', 'kilismile'); ?>
                        </label>
                        <textarea id="v_skills" 
                                  name="skills" 
                                  rows="4" 
                                  style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical;"
                                  placeholder="<?php _e('Tell us about your relevant skills, education, or experience...', 'kilismile'); ?>"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label for="v_motivation" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('Why do you want to volunteer with us?', 'kilismile'); ?>
                        </label>
                        <textarea id="v_motivation" 
                                  name="motivation" 
                                  rows="4" 
                                  style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical;"
                                  placeholder="<?php _e('Share what motivates you to volunteer with Kilismile...', 'kilismile'); ?>"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 30px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                            <input type="checkbox" 
                                   id="v_agreement" 
                                   name="agreement" 
                                   required 
                                   style="margin-top: 3px;">
                            <span style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">
                                <?php printf(__('I agree to the %s and %s, and I understand that a background check may be required for certain volunteer positions.', 'kilismile'), 
                                    '<a href="' . esc_url(home_url('/terms')) . '" style="color: var(--primary-green);">' . __('Terms of Service', 'kilismile') . '</a>',
                                    '<a href="' . esc_url(home_url('/privacy-policy')) . '" style="color: var(--primary-green);">' . __('Privacy Policy', 'kilismile') . '</a>'
                                ); ?>
                                <span style="color: red;">*</span>
                            </span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="volunteer-submit-btn" 
                            style="width: 100%; padding: 15px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <?php _e('Submit Application', 'kilismile'); ?>
                        <i class="fas fa-paper-plane" style="margin-left: 10px;" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Volunteer Testimonials -->
    <section class="volunteer-testimonials" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('What Our Volunteers Say', 'kilismile'); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px;">
                <!-- Testimonial 1 -->
                <div class="testimonial-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        <div style="width: 100%; height: 100%; background: var(--primary-green); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div style="padding-top: 40px;">
                        <p style="color: var(--text-secondary); line-height: 1.6; font-style: italic; margin-bottom: 20px;">
                            "<?php _e('Volunteering with Kilismile has been the most rewarding experience of my life. I\'ve learned so much while helping make a real difference in my community.', 'kilismile'); ?>"
                        </p>
                        <h4 style="color: var(--dark-green); margin-bottom: 5px;"><?php _e('Sarah Mwenda', 'kilismile'); ?></h4>
                        <p style="color: var(--medium-gray); font-size: 0.9rem;"><?php _e('Health Education Volunteer, 2 years', 'kilismile'); ?></p>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="testimonial-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        <div style="width: 100%; height: 100%; background: var(--accent-green); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div style="padding-top: 40px;">
                        <p style="color: var(--text-secondary); line-height: 1.6; font-style: italic; margin-bottom: 20px;">
                            "<?php _e('The flexibility and support from Kilismile made it easy to balance volunteering with my studies. I gained valuable experience for my healthcare career.', 'kilismile'); ?>"
                        </p>
                        <h4 style="color: var(--dark-green); margin-bottom: 5px;"><?php _e('John Kisanga', 'kilismile'); ?></h4>
                        <p style="color: var(--medium-gray); font-size: 0.9rem;"><?php _e('Community Outreach Volunteer, 1 year', 'kilismile'); ?></p>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="testimonial-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: center; position: relative;">
                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 5px solid white; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                        <div style="width: 100%; height: 100%; background: var(--dark-green); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-user" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div style="padding-top: 40px;">
                        <p style="color: var(--text-secondary); line-height: 1.6; font-style: italic; margin-bottom: 20px;">
                            "<?php _e('Working with Kilismile connected me with amazing people who share my passion for health education. The impact we create together is incredible.', 'kilismile'); ?>"
                        </p>
                        <h4 style="color: var(--dark-green); margin-bottom: 5px;"><?php _e('Grace Mahali', 'kilismile'); ?></h4>
                        <p style="color: var(--medium-gray); font-size: 0.9rem;"><?php _e('Event Support Volunteer, 3 years', 'kilismile'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Volunteer application functions
function applyForOpportunity(opportunity) {
    // Scroll to application form
    document.querySelector('.volunteer-application').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
    
    // Pre-select the relevant interest checkbox
    const checkboxes = document.querySelectorAll('input[name="interests[]"]');
    checkboxes.forEach(checkbox => {
        if (checkbox.value === opportunity) {
            checkbox.checked = true;
        }
    });
    
    // Show notification
    showNotification('<?php _e('Application form ready! Please fill out the details below.', 'kilismile'); ?>');
}

// Form validation
document.getElementById('volunteer-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const requiredFields = ['first_name', 'last_name', 'email', 'phone'];
    let isValid = true;
    
    requiredFields.forEach(field => {
        const input = document.getElementById('v_' + field);
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '#e0e0e0';
        }
    });
    
    // Validate interests
    const interests = document.querySelectorAll('input[name="interests[]"]:checked');
    if (interests.length === 0) {
        alert('<?php _e('Please select at least one area of interest.', 'kilismile'); ?>');
        isValid = false;
    }
    
    // Validate agreement
    const agreement = document.getElementById('v_agreement');
    if (!agreement.checked) {
        alert('<?php _e('Please agree to the terms and conditions.', 'kilismile'); ?>');
        isValid = false;
    }
    
    if (isValid) {
        // Show loading state
        const submitBtn = document.querySelector('.volunteer-submit-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php _e('Submitting...', 'kilismile'); ?>';
        submitBtn.disabled = true;
        
        // Simulate submission
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // Show success message
            alert('<?php _e('Thank you for your volunteer application! We will contact you within 3-5 business days.', 'kilismile'); ?>');
            
            // Reset form
            this.reset();
        }, 2000);
    }
});

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
</script>

<style>
    .benefit-card:hover,
    .opportunity-card:hover,
    .testimonial-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    
    .apply-btn:hover,
    .volunteer-submit-btn:hover:not(:disabled) {
        transform: translateY(-2px);
        opacity: 0.9;
    }
    
    @media (max-width: 768px) {
        .volunteer-form form > div[style*="grid-template-columns"] {
            grid-template-columns: 1fr;
        }
        
        .volunteer-stats {
            gap: 30px;
        }
        
        .opportunity-details {
            font-size: 0.85rem;
        }
        
        .volunteer-opportunities > .container > div {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 20px;
        }
        
        /* Why Volunteer section - 2 columns on tablet */
        .why-volunteer .container > div[style*="grid-template-columns"] {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 25px;
        }
        
        .benefit-card {
            min-height: 320px !important;
            padding: 25px !important;
        }
        
        .benefit-card .icon {
            width: 60px !important;
            height: 60px !important;
            font-size: 1.8rem !important;
            margin-bottom: 20px !important;
        }
        
        .benefit-card h3 {
            font-size: 1.2rem !important;
            margin-bottom: 12px !important;
        }
        
        .benefit-card p {
            font-size: 0.9rem !important;
        }
    }
    
    @media (max-width: 480px) {
        .volunteer-opportunities > .container > div {
            grid-template-columns: 1fr !important;
            gap: 15px;
        }
        
        .opportunity-card {
            min-height: 300px !important;
        }
        
        .opportunity-header {
            padding: 15px !important;
        }
        
        .opportunity-content {
            padding: 15px !important;
        }
        
        /* Why Volunteer section - 1 column on mobile */
        .why-volunteer .container > div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
            gap: 20px;
        }
        
        .benefit-card {
            min-height: 280px !important;
            padding: 20px !important;
        }
        
        .benefit-card .icon {
            width: 50px !important;
            height: 50px !important;
            font-size: 1.5rem !important;
            margin-bottom: 15px !important;
        }
        
        .benefit-card h3 {
            font-size: 1.1rem !important;
            margin-bottom: 10px !important;
        }
        
        .benefit-card p {
            font-size: 0.85rem !important;
        }
    }
</style>

<?php get_footer(); ?>


