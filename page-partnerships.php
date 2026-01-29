<?php
/**
 * Template Name: Partnerships Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="partnerships-hero" style="background: var(--light-gray); color: var(--dark-green); padding: 120px 0 80px; text-align: center; border-bottom: 4px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; color: var(--dark-green); font-weight: 700;">
                <?php _e('Strategic Partnerships', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; color: var(--text-secondary); line-height: 1.6;">
                <?php _e('Building collaborative relationships that amplify our impact in health education and community development across Tanzania.', 'kilismile'); ?>
            </p>
            <div class="partnership-stats" style="display: flex; justify-content: center; gap: 50px; flex-wrap: wrap; margin-top: 40px;">
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">35+</div>
                    <div style="color: var(--text-secondary);"><?php _e('Active Partners', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">12</div>
                    <div style="color: var(--text-secondary);"><?php _e('Countries', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: var(--white); padding: 20px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 5px; color: var(--primary-green);">100K+</div>
                    <div style="color: var(--text-secondary);"><?php _e('People Reached', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partnership Types -->
    <section class="partnership-types" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Partnership Opportunities', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Explore different ways to collaborate with Kilismile and make a meaningful impact together.', 'kilismile'); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; align-items: stretch;">
                <!-- Healthcare Partners -->
                <div class="partnership-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 420px;">
                    <div class="card-header" style="background: var(--primary-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-hospital" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Healthcare Organizations', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Partner with hospitals, clinics, and medical institutions to enhance health education delivery and training programs.', 'kilismile'); ?>
                        </p>
                        
                        <div class="benefits" style="margin-bottom: 20px;">
                            <h4 style="color: var(--dark-green); margin-bottom: 12px; font-size: 1rem;"><?php _e('Benefits:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Medical expertise sharing', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Resource optimization', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Professional development', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Joint research opportunities', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyPartnership('healthcare')" 
                                style="width: 100%; padding: 12px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Partner With Us', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Educational Partners -->
                <div class="partnership-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 420px;">
                    <div class="card-header" style="background: var(--accent-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-university" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Educational Institutions', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Collaborate with universities, schools, and training centers to develop comprehensive health education curricula.', 'kilismile'); ?>
                        </p>
                        
                        <div class="benefits" style="margin-bottom: 20px;">
                            <h4 style="color: var(--dark-green); margin-bottom: 12px; font-size: 1rem;"><?php _e('Benefits:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Curriculum development', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Student engagement programs', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Research collaborations', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Academic exchange', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyPartnership('educational')" 
                                style="width: 100%; padding: 12px; background: var(--accent-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Partner With Us', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- NGO Partners -->
                <div class="partnership-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 420px;">
                    <div class="card-header" style="background: var(--dark-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-hands-helping" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('NGOs & Nonprofits', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Join forces with like-minded organizations to maximize impact and create sustainable change in communities.', 'kilismile'); ?>
                        </p>
                        
                        <div class="benefits" style="margin-bottom: 20px;">
                            <h4 style="color: var(--dark-green); margin-bottom: 12px; font-size: 1rem;"><?php _e('Benefits:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Shared resources', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Joint fundraising', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Knowledge exchange', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Advocacy collaboration', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyPartnership('ngo')" 
                                style="width: 100%; padding: 12px; background: var(--dark-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Partner With Us', 'kilismile'); ?>
                        </button>
                    </div>
                </div>

                <!-- Government Partners -->
                <div class="partnership-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1); transition: all 0.3s ease; display: flex; flex-direction: column; min-height: 420px;">
                    <div class="card-header" style="background: var(--primary-green); color: white; padding: 25px; text-align: center;">
                        <i class="fas fa-landmark" style="font-size: 2.5rem; margin-bottom: 15px;" aria-hidden="true"></i>
                        <h3 style="margin: 0; font-size: 1.3rem; color: white;"><?php _e('Government Agencies', 'kilismile'); ?></h3>
                    </div>
                    <div class="card-content" style="padding: 25px; flex-grow: 1; display: flex; flex-direction: column;">
                        <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 20px; flex-grow: 1;">
                            <?php _e('Work with government health departments and agencies to align with national health policies and initiatives.', 'kilismile'); ?>
                        </p>
                        
                        <div class="benefits" style="margin-bottom: 20px;">
                            <h4 style="color: var(--dark-green); margin-bottom: 12px; font-size: 1rem;"><?php _e('Benefits:', 'kilismile'); ?></h4>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Policy alignment', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Wider reach & scale', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Sustainability support', 'kilismile'); ?></span>
                            </div>
                            <div style="margin-bottom: 8px; display: flex; align-items: center;">
                                <i class="fas fa-check" style="color: var(--primary-green); margin-right: 8px; font-size: 0.8rem;" aria-hidden="true"></i>
                                <span style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Official recognition', 'kilismile'); ?></span>
                            </div>
                        </div>
                        
                        <button onclick="applyPartnership('government')" 
                                style="width: 100%; padding: 12px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; margin-top: auto;">
                            <?php _e('Partner With Us', 'kilismile'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Partners -->
    <section class="current-partners" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 60px;">
                <?php _e('Our Valued Partners', 'kilismile'); ?>
            </h2>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
                <!-- Partner 1 -->
                <div class="partner-card" style="background: white; border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-hospital-alt" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;"><?php _e('Muhimbili University', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.6;">
                        <?php _e('Leading medical university providing expertise in health education curriculum development and training programs.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; justify-content: center; gap: 15px;">
                        <span style="background: var(--light-gray); padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; color: var(--dark-green);">
                            <?php _e('Education', 'kilismile'); ?>
                        </span>
                        <span style="background: var(--light-gray); padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; color: var(--dark-green);">
                            <?php _e('Research', 'kilismile'); ?>
                        </span>
                    </div>
                </div>

                <!-- Partner 2 -->
                <div class="partner-card" style="background: white; border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="width: 80px; height: 80px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-globe-africa" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;"><?php _e('World Health Organization', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.6;">
                        <?php _e('Global health leadership supporting our initiatives through technical guidance and resource mobilization.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; justify-content: center; gap: 15px;">
                        <span style="background: var(--light-gray); padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; color: var(--dark-green);">
                            <?php _e('Global Health', 'kilismile'); ?>
                        </span>
                        <span style="background: var(--light-gray); padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; color: var(--dark-green);">
                            <?php _e('Policy', 'kilismile'); ?>
                        </span>
                    </div>
                </div>

                <!-- Partner 3 -->
                <div class="partner-card" style="background: white; border-radius: 20px; padding: 40px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                    <div style="width: 80px; height: 80px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 2rem;">
                        <i class="fas fa-hands-helping" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.4rem;"><?php _e('Tanzania Red Cross', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.6;">
                        <?php _e('National humanitarian organization collaborating on community health education and emergency response training.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; justify-content: center; gap: 15px;">
                        <span style="background: var(--light-gray); padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; color: var(--dark-green);">
                            <?php _e('Community', 'kilismile'); ?>
                        </span>
                        <span style="background: var(--light-gray); padding: 5px 12px; border-radius: 15px; font-size: 0.8rem; color: var(--dark-green);">
                            <?php _e('Emergency', 'kilismile'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partnership Process -->
    <section class="partnership-process" style="padding: 100px 0;">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('How to Partner With Us', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.2rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Follow our simple process to establish a meaningful partnership that creates lasting impact.', 'kilismile'); ?>
            </p>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; position: relative;">
                <!-- Step 1 -->
                <div class="process-step" style="text-align: center; position: relative;">
                    <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 1.5rem; font-weight: bold; position: relative; z-index: 2;">
                        1
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;"><?php _e('Initial Contact', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.9rem;">
                        <?php _e('Reach out to us with your partnership proposal and organizational information.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="process-step" style="text-align: center; position: relative;">
                    <div style="width: 80px; height: 80px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 1.5rem; font-weight: bold; position: relative; z-index: 2;">
                        2
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;"><?php _e('Assessment', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.9rem;">
                        <?php _e('We evaluate partnership alignment with our mission, values, and strategic objectives.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="process-step" style="text-align: center; position: relative;">
                    <div style="width: 80px; height: 80px; background: var(--dark-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 1.5rem; font-weight: bold; position: relative; z-index: 2;">
                        3
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;"><?php _e('Planning', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.9rem;">
                        <?php _e('Develop detailed partnership agreement with clear objectives, roles, and responsibilities.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="process-step" style="text-align: center; position: relative;">
                    <div style="width: 80px; height: 80px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; color: white; font-size: 1.5rem; font-weight: bold; position: relative; z-index: 2;">
                        4
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem;"><?php _e('Implementation', 'kilismile'); ?></h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.9rem;">
                        <?php _e('Launch collaborative projects with regular monitoring and evaluation for continuous improvement.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Partnership Application -->
    <section class="partnership-application" style="padding: 100px 0; background: var(--light-gray);">
        <div class="container">
            <div style="max-width: 800px; margin: 0 auto; text-align: center;">
                <h2 style="color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Ready to Partner With Us?', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1.2rem; margin-bottom: 40px; line-height: 1.6;">
                    <?php _e('Join our network of partners and help us create sustainable change in health education across Tanzania.', 'kilismile'); ?>
                </p>

                <form id="partnership-form" style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); text-align: left;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Organization Name', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <input type="text" required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                        </div>

                        <div class="form-group">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                                <?php _e('Organization Type', 'kilismile'); ?> <span style="color: red;">*</span>
                            </label>
                            <select required style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem;">
                                <option value=""><?php _e('Select Type', 'kilismile'); ?></option>
                                <option value="healthcare"><?php _e('Healthcare Organization', 'kilismile'); ?></option>
                                <option value="educational"><?php _e('Educational Institution', 'kilismile'); ?></option>
                                <option value="ngo"><?php _e('NGO/Nonprofit', 'kilismile'); ?></option>
                                <option value="government"><?php _e('Government Agency', 'kilismile'); ?></option>
                                <option value="corporate"><?php _e('Corporate/Business', 'kilismile'); ?></option>
                                <option value="foundation"><?php _e('Foundation', 'kilismile'); ?></option>
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
                            <?php _e('Partnership Proposal', 'kilismile'); ?> <span style="color: red;">*</span>
                        </label>
                        <textarea required rows="6" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical;" placeholder="<?php _e('Describe your partnership proposal, objectives, and how it aligns with our mission...', 'kilismile'); ?>"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green);">
                            <?php _e('Resources & Contribution', 'kilismile'); ?>
                        </label>
                        <textarea rows="4" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; resize: vertical;" placeholder="<?php _e('What resources, expertise, or contribution can your organization provide?', 'kilismile'); ?>"></textarea>
                    </div>

                    <button type="submit" style="width: 100%; padding: 15px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <?php _e('Submit Partnership Application', 'kilismile'); ?>
                        <i class="fas fa-paper-plane" style="margin-left: 10px;" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<script>
function applyPartnership(type) {
    // Scroll to application form
    document.querySelector('.partnership-application').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
    
    // Pre-select the organization type
    const typeSelect = document.querySelector('select');
    if (typeSelect) {
        typeSelect.value = type;
    }
    
    // Show notification
    showNotification('<?php _e('Partnership application form ready! Please fill out the details below.', 'kilismile'); ?>');
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
document.getElementById('partnership-form').addEventListener('submit', function(e) {
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
        
        alert('<?php _e('Thank you for your partnership application! We will review it and contact you within 5-7 business days.', 'kilismile'); ?>');
        this.reset();
    }, 2000);
});
</script>

<style>
.partnership-card:hover,
.partner-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.partnership-card button:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

.process-step::after {
    content: '';
    position: absolute;
    top: 40px;
    right: -15px;
    width: 30px;
    height: 2px;
    background: var(--primary-green);
    z-index: 1;
}

.process-step:last-child::after {
    display: none;
}

@media (max-width: 768px) {
    .partnership-types .container > div,
    .current-partners .container > div,
    .partnership-process .container > div {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 25px;
    }
    
    .partnership-card,
    .partner-card {
        min-height: 380px !important;
    }
    
    .card-header {
        padding: 20px !important;
    }
    
    .card-content {
        padding: 20px !important;
    }
    
    .process-step::after {
        display: none;
    }
    
    .partnership-application form > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .partnership-types .container > div,
    .current-partners .container > div,
    .partnership-process .container > div {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .partnership-card,
    .partner-card {
        min-height: 350px !important;
    }
}
</style>

<?php get_footer(); ?>


