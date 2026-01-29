<?php
/**
 * Template Name: Become a Partner
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="become-partner-hero" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 50%, #3498db 100%); color: white; padding: 120px 0 80px; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cdefs%3E%3Cpattern id='partnership-pattern' width='40' height='40' patternUnits='userSpaceOnUse'%3E%3Ccircle cx='20' cy='20' r='3' fill='rgba(255,255,255,0.1)'/%3E%3Ccircle cx='10' cy='10' r='2' fill='rgba(255,255,255,0.05)'/%3E%3Ccircle cx='30' cy='30' r='2' fill='rgba(255,255,255,0.05)'/%3E%3C/pattern%3E%3C/defs%3E%3Crect width='100' height='100' fill='url(%23partnership-pattern)'/%3E%3C/svg%3E&quot;); opacity: 0.6;"></div>
        <div class="container" style="position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="max-width: 800px; margin: 0 auto;">
                <h1 style="font-size: 3.5rem; margin-bottom: 25px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); line-height: 1.2;">
                    <?php _e('Partner With Us', 'kilismile'); ?>
                </h1>
                <p style="font-size: 1.4rem; margin-bottom: 30px; line-height: 1.6; opacity: 0.95;">
                    <?php _e('Join us in transforming healthcare and creating lasting change in communities across Tanzania and beyond.', 'kilismile'); ?>
                </p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
                    <a href="#partnership-form" class="cta-primary" style="background: rgba(255,255,255,0.9); color: #27ae60; padding: 15px 35px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(0,0,0,0.2); backdrop-filter: blur(10px);">
                        <i class="fas fa-handshake" style="margin-right: 10px;"></i>
                        <?php _e('Become a Partner', 'kilismile'); ?>
                    </a>
                    <a href="#partnership-benefits" class="cta-secondary" style="background: rgba(255,255,255,0.1); color: white; padding: 15px 35px; border: 2px solid rgba(255,255,255,0.4); border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                        <i class="fas fa-star" style="margin-right: 10px;"></i>
                        <?php _e('View Benefits', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Partner With Us Section -->
    <section class="why-partner" style="padding: 100px 0; background: #f8f9fa;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.8rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Why Partner With KiliSmile?', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Discover the unique value and impact you can create by joining our mission to improve health outcomes in underserved communities.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 40px; margin-bottom: 60px;">
                <!-- Proven Impact -->
                <div class="impact-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease;">
                    <div style="background: linear-gradient(135deg, #e74c3c, #c0392b); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 10px 30px rgba(231,76,60,0.3);">
                        <i class="fas fa-chart-line" style="color: white; font-size: 2rem;"></i>
                    </div>
                    <h3 style="color: #2c3e50; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Proven Impact', 'kilismile'); ?>
                    </h3>
                    <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 20px;">
                        <?php _e('Our evidence-based programs have reached over 10,000 individuals, with measurable improvements in health outcomes and community wellness.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; gap: 20px; justify-content: center; font-weight: 600; color: #27ae60;">
                        <span>10K+ Lives Touched</span>
                        <span>95% Satisfaction</span>
                    </div>
                </div>

                <!-- Strategic Alignment -->
                <div class="impact-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease;">
                    <div style="background: linear-gradient(135deg, #3498db, #2980b9); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 10px 30px rgba(52,152,219,0.3);">
                        <i class="fas fa-bullseye" style="color: white; font-size: 2rem;"></i>
                    </div>
                    <h3 style="color: #2c3e50; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Strategic Alignment', 'kilismile'); ?>
                    </h3>
                    <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 20px;">
                        <?php _e('Align your corporate social responsibility goals with our mission to create sustainable health solutions for vulnerable populations.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; gap: 20px; justify-content: center; font-weight: 600; color: #3498db;">
                        <span>CSR Excellence</span>
                        <span>SDG Alignment</span>
                    </div>
                </div>

                <!-- Brand Visibility -->
                <div class="impact-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease;">
                    <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 10px 30px rgba(155,89,182,0.3);">
                        <i class="fas fa-eye" style="color: white; font-size: 2rem;"></i>
                    </div>
                    <h3 style="color: #2c3e50; font-size: 1.5rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Brand Visibility', 'kilismile'); ?>
                    </h3>
                    <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 20px;">
                        <?php _e('Gain meaningful exposure through our digital platforms, community events, and media coverage while supporting a worthy cause.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; gap: 20px; justify-content: center; font-weight: 600; color: #9b59b6;">
                        <span>50K+ Reach</span>
                        <span>Media Coverage</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partnership Types Section -->
    <section class="partnership-types" style="padding: 100px 0; background: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.8rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Partnership Opportunities', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Choose the partnership model that best aligns with your organization\'s goals and capacity for impact.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <!-- Corporate Partnership -->
                <div class="partnership-card" style="background: linear-gradient(135deg, #fff, #f8f9fa); border: 3px solid #e9ecef; border-radius: 20px; padding: 40px; text-align: center; transition: all 0.3s ease; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(39,174,96,0.1); border-radius: 50%;"></div>
                    <div style="background: #27ae60; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; position: relative; z-index: 2;">
                        <i class="fas fa-building" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="color: #2c3e50; font-size: 1.8rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Corporate Partnership', 'kilismile'); ?>
                    </h3>
                    <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 25px;">
                        <?php _e('Strategic collaboration for companies looking to make a significant impact while achieving CSR objectives.', 'kilismile'); ?>
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 30px; text-align: left;">
                        <li style="color: #27ae60; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Logo placement on materials', 'kilismile'); ?>
                        </li>
                        <li style="color: #27ae60; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Co-branded campaigns', 'kilismile'); ?>
                        </li>
                        <li style="color: #27ae60; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Employee engagement opportunities', 'kilismile'); ?>
                        </li>
                        <li style="color: #27ae60; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Quarterly impact reports', 'kilismile'); ?>
                        </li>
                    </ul>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <span style="color: #27ae60; font-weight: 600; font-size: 1.1rem;">
                            <?php _e('Starting from $5,000/year', 'kilismile'); ?>
                        </span>
                    </div>
                </div>

                <!-- Community Partnership -->
                <div class="partnership-card" style="background: linear-gradient(135deg, #fff, #f8f9fa); border: 3px solid #e9ecef; border-radius: 20px; padding: 40px; text-align: center; transition: all 0.3s ease; position: relative; overflow: hidden;">
                    <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(52,152,219,0.1); border-radius: 50%;"></div>
                    <div style="background: #3498db; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; position: relative; z-index: 2;">
                        <i class="fas fa-users" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="color: #2c3e50; font-size: 1.8rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Community Partnership', 'kilismile'); ?>
                    </h3>
                    <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 25px;">
                        <?php _e('Perfect for local businesses, community organizations, and smaller enterprises wanting to contribute meaningfully.', 'kilismile'); ?>
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 30px; text-align: left;">
                        <li style="color: #3498db; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Social media recognition', 'kilismile'); ?>
                        </li>
                        <li style="color: #3498db; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Community event participation', 'kilismile'); ?>
                        </li>
                        <li style="color: #3498db; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Volunteer opportunities', 'kilismile'); ?>
                        </li>
                        <li style="color: #3498db; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Bi-annual impact updates', 'kilismile'); ?>
                        </li>
                    </ul>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <span style="color: #3498db; font-weight: 600; font-size: 1.1rem;">
                            <?php _e('Starting from $500/year', 'kilismile'); ?>
                        </span>
                    </div>
                </div>

                <!-- Strategic Partnership -->
                <div class="partnership-card" style="background: linear-gradient(135deg, #fff, #f8f9fa); border: 3px solid #f39c12; border-radius: 20px; padding: 40px; text-align: center; transition: all 0.3s ease; position: relative; overflow: hidden; box-shadow: 0 15px 35px rgba(243,156,18,0.2);">
                    <div style="position: absolute; top: -50px; right: -50px; width: 100px; height: 100px; background: rgba(243,156,18,0.1); border-radius: 50%;"></div>
                    <div style="position: absolute; top: 20px; right: 20px; background: #f39c12; color: white; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                        <?php _e('FEATURED', 'kilismile'); ?>
                    </div>
                    <div style="background: #f39c12; width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; position: relative; z-index: 2;">
                        <i class="fas fa-star" style="color: white; font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="color: #2c3e50; font-size: 1.8rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Strategic Partnership', 'kilismile'); ?>
                    </h3>
                    <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 25px;">
                        <?php _e('Comprehensive partnership for organizations seeking maximum impact and co-innovation opportunities.', 'kilismile'); ?>
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 30px; text-align: left;">
                        <li style="color: #f39c12; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Co-development of programs', 'kilismile'); ?>
                        </li>
                        <li style="color: #f39c12; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Joint research initiatives', 'kilismile'); ?>
                        </li>
                        <li style="color: #f39c12; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Executive board representation', 'kilismile'); ?>
                        </li>
                        <li style="color: #f39c12; margin-bottom: 10px; display: flex; align-items: center;">
                            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
                            <?php _e('Custom impact measurement', 'kilismile'); ?>
                        </li>
                    </ul>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                        <span style="color: #f39c12; font-weight: 600; font-size: 1.1rem;">
                            <?php _e('Starting from $15,000/year', 'kilismile'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="partnership-benefits" class="partnership-benefits" style="padding: 100px 0; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.8rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Partnership Benefits', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Discover the comprehensive benefits and value proposition of partnering with KiliSmile Organization.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 50px; align-items: center; margin-bottom: 80px;">
                <div>
                    <h3 style="color: #2c3e50; font-size: 2.2rem; margin-bottom: 30px; font-weight: 600;">
                        <?php _e('Measurable Social Impact', 'kilismile'); ?>
                    </h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                        <div class="benefit-stat" style="text-align: center; padding: 25px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <div style="color: #27ae60; font-size: 2.5rem; font-weight: 700; margin-bottom: 10px;">
                                10,000+
                            </div>
                            <div style="color: #7f8c8d; font-weight: 500;">
                                <?php _e('Lives Impacted', 'kilismile'); ?>
                            </div>
                        </div>
                        <div class="benefit-stat" style="text-align: center; padding: 25px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <div style="color: #3498db; font-size: 2.5rem; font-weight: 700; margin-bottom: 10px;">
                                50+
                            </div>
                            <div style="color: #7f8c8d; font-weight: 500;">
                                <?php _e('Communities Served', 'kilismile'); ?>
                            </div>
                        </div>
                        <div class="benefit-stat" style="text-align: center; padding: 25px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <div style="color: #e74c3c; font-size: 2.5rem; font-weight: 700; margin-bottom: 10px;">
                                95%
                            </div>
                            <div style="color: #7f8c8d; font-weight: 500;">
                                <?php _e('Satisfaction Rate', 'kilismile'); ?>
                            </div>
                        </div>
                        <div class="benefit-stat" style="text-align: center; padding: 25px; background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                            <div style="color: #f39c12; font-size: 2.5rem; font-weight: 700; margin-bottom: 10px;">
                                25+
                            </div>
                            <div style="color: #7f8c8d; font-weight: 500;">
                                <?php _e('Active Partners', 'kilismile'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="text-align: center;">
                    <!-- Partnership Impact Illustration -->
                    <div style="width: 100%; max-width: 500px; margin: 0 auto;">
                        <svg viewBox="0 0 400 320" style="width: 100%; height: auto; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.2); background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                            <!-- Background -->
                            <defs>
                                <linearGradient id="bgGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#f8f9fa;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#e9ecef;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="primaryGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#27ae60;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#2ecc71;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="secondaryGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#3498db;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#5dade2;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="orangeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#f39c12;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#e67e22;stop-opacity:1" />
                                </linearGradient>
                                <linearGradient id="purpleGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#9b59b6;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#8e44ad;stop-opacity:1" />
                                </linearGradient>
                                <filter id="shadow" x="-50%" y="-50%" width="200%" height="200%">
                                    <feDropShadow dx="2" dy="3" stdDeviation="4" flood-opacity="0.25"/>
                                </filter>
                                <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
                                    <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                                    <feMerge> 
                                        <feMergeNode in="coloredBlur"/>
                                        <feMergeNode in="SourceGraphic"/>
                                    </feMerge>
                                </filter>
                            </defs>
                            
                            <!-- Background -->
                            <rect width="400" height="320" fill="url(#bgGradient)"/>
                            
                            <!-- Grid Guidelines (subtle) -->
                            <defs>
                                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="rgba(44,62,80,0.05)" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="400" height="320" fill="url(#grid)" opacity="0.3"/>
                            
                            <!-- Central Partnership Hub -->
                            <circle cx="200" cy="160" r="45" fill="url(#primaryGradient)" filter="url(#shadow)"/>
                            <circle cx="200" cy="160" r="37" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="2"/>
                            <text x="200" y="155" text-anchor="middle" dy="0.3em" fill="white" font-family="Arial, sans-serif" font-size="13" font-weight="bold">KiliSmile</text>
                            <text x="200" y="170" text-anchor="middle" dy="0.3em" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="8">HUB</text>
                            
                            <!-- Partner Organizations - Perfect Circle Alignment -->
                            <!-- Top Partners -->
                            <g id="top-partners">
                                <!-- Corporate Partners (Top Left) -->
                                <circle cx="130" cy="90" r="28" fill="url(#secondaryGradient)" filter="url(#shadow)"/>
                                <circle cx="130" cy="90" r="23" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                                <text x="130" y="85" text-anchor="middle" dy="0.3em" fill="white" font-family="Arial, sans-serif" font-size="9" font-weight="bold">Corporate</text>
                                <text x="130" y="97" text-anchor="middle" dy="0.3em" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="7">Partners</text>
                                
                                <!-- Community Partners (Top Right) -->
                                <circle cx="270" cy="90" r="28" fill="#e74c3c" filter="url(#shadow)"/>
                                <circle cx="270" cy="90" r="23" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                                <text x="270" y="85" text-anchor="middle" dy="0.3em" fill="white" font-family="Arial, sans-serif" font-size="9" font-weight="bold">Community</text>
                                <text x="270" y="97" text-anchor="middle" dy="0.3em" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="7">Partners</text>
                            </g>
                            
                            <!-- Bottom Partners -->
                            <g id="bottom-partners">
                                <!-- Strategic Partners (Bottom Left) -->
                                <circle cx="130" cy="230" r="28" fill="url(#orangeGradient)" filter="url(#shadow)"/>
                                <circle cx="130" cy="230" r="23" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                                <text x="130" y="225" text-anchor="middle" dy="0.3em" fill="white" font-family="Arial, sans-serif" font-size="9" font-weight="bold">Strategic</text>
                                <text x="130" y="237" text-anchor="middle" dy="0.3em" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="7">Partners</text>
                                
                                <!-- Healthcare Partners (Bottom Right) -->
                                <circle cx="270" cy="230" r="28" fill="url(#purpleGradient)" filter="url(#shadow)"/>
                                <circle cx="270" cy="230" r="23" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5"/>
                                <text x="270" y="225" text-anchor="middle" dy="0.3em" fill="white" font-family="Arial, sans-serif" font-size="9" font-weight="bold">Healthcare</text>
                                <text x="270" y="237" text-anchor="middle" dy="0.3em" fill="rgba(255,255,255,0.8)" font-family="Arial, sans-serif" font-size="7">Partners</text>
                            </g>
                            
                            <!-- Connection Lines - Perfectly Aligned -->
                            <g id="connection-lines">
                                <!-- From center to top partners -->
                                <line x1="172" y1="126" x2="158" y2="112" stroke="#27ae60" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
                                <line x1="228" y1="126" x2="242" y2="112" stroke="#27ae60" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
                                
                                <!-- From center to bottom partners -->
                                <line x1="172" y1="194" x2="158" y2="208" stroke="#27ae60" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
                                <line x1="228" y1="194" x2="242" y2="208" stroke="#27ae60" stroke-width="4" opacity="0.6" stroke-linecap="round"/>
                                
                                <!-- Data flow indicators (small animated dots) -->
                                <circle r="3" fill="#27ae60" opacity="0.8">
                                    <animateMotion dur="3s" repeatCount="indefinite">
                                        <path d="M172,126 L158,112"/>
                                    </animateMotion>
                                </circle>
                                <circle r="3" fill="#27ae60" opacity="0.8">
                                    <animateMotion dur="3.5s" repeatCount="indefinite">
                                        <path d="M228,126 L242,112"/>
                                    </animateMotion>
                                </circle>
                                <circle r="3" fill="#27ae60" opacity="0.8">
                                    <animateMotion dur="4s" repeatCount="indefinite">
                                        <path d="M172,194 L158,208"/>
                                    </animateMotion>
                                </circle>
                                <circle r="3" fill="#27ae60" opacity="0.8">
                                    <animateMotion dur="2.5s" repeatCount="indefinite">
                                        <path d="M228,194 L242,208"/>
                                    </animateMotion>
                                </circle>
                            </g>
                            
                            <!-- Impact Metrics - Perfectly Aligned Corners -->
                            <g id="impact-metrics">
                                <!-- Top Left: Lives Impacted -->
                                <g transform="translate(20, 20)">
                                    <rect x="0" y="0" width="90" height="35" rx="17.5" fill="rgba(39,174,96,0.15)" stroke="#27ae60" stroke-width="2"/>
                                    <text x="45" y="15" text-anchor="middle" dy="0.3em" fill="#27ae60" font-family="Arial, sans-serif" font-size="11" font-weight="bold">10,000+</text>
                                    <text x="45" y="27" text-anchor="middle" dy="0.3em" fill="#27ae60" font-family="Arial, sans-serif" font-size="7">Lives Impacted</text>
                                </g>
                                
                                <!-- Top Right: Communities Served -->
                                <g transform="translate(290, 20)">
                                    <rect x="0" y="0" width="90" height="35" rx="17.5" fill="rgba(52,152,219,0.15)" stroke="#3498db" stroke-width="2"/>
                                    <text x="45" y="15" text-anchor="middle" dy="0.3em" fill="#3498db" font-family="Arial, sans-serif" font-size="11" font-weight="bold">50+</text>
                                    <text x="45" y="27" text-anchor="middle" dy="0.3em" fill="#3498db" font-family="Arial, sans-serif" font-size="7">Communities</text>
                                </g>
                                
                                <!-- Bottom Left: Success Rate -->
                                <g transform="translate(20, 265)">
                                    <rect x="0" y="0" width="90" height="35" rx="17.5" fill="rgba(231,76,60,0.15)" stroke="#e74c3c" stroke-width="2"/>
                                    <text x="45" y="15" text-anchor="middle" dy="0.3em" fill="#e74c3c" font-family="Arial, sans-serif" font-size="11" font-weight="bold">95%</text>
                                    <text x="45" y="27" text-anchor="middle" dy="0.3em" fill="#e74c3c" font-family="Arial, sans-serif" font-size="7">Success Rate</text>
                                </g>
                                
                                <!-- Bottom Right: Active Partners -->
                                <g transform="translate(290, 265)">
                                    <rect x="0" y="0" width="90" height="35" rx="17.5" fill="rgba(243,156,18,0.15)" stroke="#f39c12" stroke-width="2"/>
                                    <text x="45" y="15" text-anchor="middle" dy="0.3em" fill="#f39c12" font-family="Arial, sans-serif" font-size="11" font-weight="bold">25+</text>
                                    <text x="45" y="27" text-anchor="middle" dy="0.3em" fill="#f39c12" font-family="Arial, sans-serif" font-size="7">Active Partners</text>
                                </g>
                            </g>
                            
                            <!-- Connection Pulse Animation - Perfectly Centered -->
                            <circle cx="200" cy="160" r="50" fill="none" stroke="#27ae60" stroke-width="2" opacity="0.4">
                                <animate attributeName="r" values="45;60;45" dur="4s" repeatCount="indefinite"/>
                                <animate attributeName="opacity" values="0.4;0.1;0.4" dur="4s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="200" cy="160" r="55" fill="none" stroke="#2ecc71" stroke-width="1" opacity="0.3">
                                <animate attributeName="r" values="50;70;50" dur="5s" repeatCount="indefinite"/>
                                <animate attributeName="opacity" values="0.3;0.05;0.3" dur="5s" repeatCount="indefinite"/>
                            </circle>
                            
                            <!-- Title -->
                            <text x="200" y="15" text-anchor="middle" dy="0.3em" fill="#2c3e50" font-family="Arial, sans-serif" font-size="16" font-weight="bold">Partnership Impact Network</text>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Additional Benefits -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;">
                <div class="benefit-item" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="background: #3498db; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-certificate" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Recognition & Awards', 'kilismile'); ?>
                    </h4>
                    <p style="color: #7f8c8d; line-height: 1.6;">
                        <?php _e('Annual partner recognition and co-branded award opportunities for outstanding contributions.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-item" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="background: #27ae60; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-network-wired" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Network Access', 'kilismile'); ?>
                    </h4>
                    <p style="color: #7f8c8d; line-height: 1.6;">
                        <?php _e('Connect with like-minded organizations and expand your network through our partner ecosystem.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-item" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="background: #e74c3c; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-chart-bar" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Impact Reporting', 'kilismile'); ?>
                    </h4>
                    <p style="color: #7f8c8d; line-height: 1.6;">
                        <?php _e('Regular detailed reports showing the direct impact of your partnership on community health.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-item" style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;">
                    <div style="background: #f39c12; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-handshake" style="color: white; font-size: 1.3rem;"></i>
                    </div>
                    <h4 style="color: #2c3e50; font-size: 1.3rem; margin-bottom: 15px; font-weight: 600;">
                        <?php _e('Employee Engagement', 'kilismile'); ?>
                    </h4>
                    <p style="color: #7f8c8d; line-height: 1.6;">
                        <?php _e('Volunteer opportunities and team-building activities that boost employee satisfaction and retention.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Partnership Form Section -->
    <section id="partnership-form" class="partnership-form-section" style="padding: 100px 0; background: white;">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="color: #2c3e50; font-size: 2.8rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Start Your Partnership Journey', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; line-height: 1.6;">
                    <?php _e('Ready to make a difference? Fill out the form below and our partnership team will contact you within 24 hours.', 'kilismile'); ?>
                </p>
            </div>

            <form id="partnership-application-form" class="partnership-form" style="background: #f8f9fa; padding: 50px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.1);">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div class="form-group">
                        <label for="organization_name" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Organization Name *', 'kilismile'); ?>
                        </label>
                        <input type="text" id="organization_name" name="organization_name" required style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                    </div>
                    <div class="form-group">
                        <label for="contact_person" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Contact Person *', 'kilismile'); ?>
                        </label>
                        <input type="text" id="contact_person" name="contact_person" required style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div class="form-group">
                        <label for="email" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Email Address *', 'kilismile'); ?>
                        </label>
                        <input type="email" id="email" name="email" required style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                    </div>
                    <div class="form-group">
                        <label for="phone" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Phone Number', 'kilismile'); ?>
                        </label>
                        <input type="tel" id="phone" name="phone" style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                    <div class="form-group">
                        <label for="organization_type" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Organization Type *', 'kilismile'); ?>
                        </label>
                        <select id="organization_type" name="organization_type" required style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                            <option value=""><?php _e('Select Type', 'kilismile'); ?></option>
                            <option value="corporation"><?php _e('Corporation', 'kilismile'); ?></option>
                            <option value="small_business"><?php _e('Small Business', 'kilismile'); ?></option>
                            <option value="nonprofit"><?php _e('Non-Profit', 'kilismile'); ?></option>
                            <option value="government"><?php _e('Government Agency', 'kilismile'); ?></option>
                            <option value="foundation"><?php _e('Foundation', 'kilismile'); ?></option>
                            <option value="educational"><?php _e('Educational Institution', 'kilismile'); ?></option>
                            <option value="other"><?php _e('Other', 'kilismile'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="partnership_type" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                            <?php _e('Preferred Partnership *', 'kilismile'); ?>
                        </label>
                        <select id="partnership_type" name="partnership_type" required style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                            <option value=""><?php _e('Select Partnership', 'kilismile'); ?></option>
                            <option value="corporate"><?php _e('Corporate Partnership', 'kilismile'); ?></option>
                            <option value="community"><?php _e('Community Partnership', 'kilismile'); ?></option>
                            <option value="strategic"><?php _e('Strategic Partnership', 'kilismile'); ?></option>
                            <option value="custom"><?php _e('Custom Partnership', 'kilismile'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label for="annual_budget" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                        <?php _e('Estimated Annual Partnership Budget', 'kilismile'); ?>
                    </label>
                    <select id="annual_budget" name="annual_budget" style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; transition: border-color 0.3s ease;">
                        <option value=""><?php _e('Select Budget Range', 'kilismile'); ?></option>
                        <option value="under_1000"><?php _e('Under $1,000', 'kilismile'); ?></option>
                        <option value="1000_5000"><?php _e('$1,000 - $5,000', 'kilismile'); ?></option>
                        <option value="5000_15000"><?php _e('$5,000 - $15,000', 'kilismile'); ?></option>
                        <option value="15000_50000"><?php _e('$15,000 - $50,000', 'kilismile'); ?></option>
                        <option value="over_50000"><?php _e('Over $50,000', 'kilismile'); ?></option>
                        <option value="in_kind"><?php _e('In-Kind Contributions', 'kilismile'); ?></option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label for="organization_goals" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                        <?php _e('Organization Goals & CSR Objectives', 'kilismile'); ?>
                    </label>
                    <textarea id="organization_goals" name="organization_goals" rows="4" style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; resize: vertical; transition: border-color 0.3s ease;" placeholder="<?php _e('Tell us about your organization\'s mission, values, and CSR goals...', 'kilismile'); ?>"></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 40px;">
                    <label for="partnership_interest" style="display: block; color: #2c3e50; font-weight: 600; margin-bottom: 8px;">
                        <?php _e('Why are you interested in partnering with KiliSmile? *', 'kilismile'); ?>
                    </label>
                    <textarea id="partnership_interest" name="partnership_interest" rows="4" required style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 1rem; resize: vertical; transition: border-color 0.3s ease;" placeholder="<?php _e('Share your motivation and how you envision our partnership creating impact...', 'kilismile'); ?>"></textarea>
                </div>

                <div style="display: flex; gap: 20px; justify-content: center;">
                    <button type="submit" style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 18px 40px; border: none; border-radius: 50px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(39,174,96,0.3);">
                        <i class="fas fa-paper-plane" style="margin-right: 10px;"></i>
                        <?php _e('Submit Application', 'kilismile'); ?>
                    </button>
                    <button type="button" onclick="window.location.href='<?php echo home_url('/contact'); ?>'" style="background: rgba(52,152,219,0.1); color: #3498db; padding: 18px 40px; border: 2px solid #3498db; border-radius: 50px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-comments" style="margin-right: 10px;"></i>
                        <?php _e('Schedule a Call', 'kilismile'); ?>
                    </button>
                </div>

                <!-- Success Message -->
                <div id="partnership-form-success" style="display: none; background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin-top: 30px; text-align: center;">
                    <i class="fas fa-check-circle" style="margin-right: 10px; font-size: 1.2rem;"></i>
                    <?php _e('Thank you for your interest! We\'ll contact you within 24 hours to discuss partnership opportunities.', 'kilismile'); ?>
                </div>

                <!-- Error Message -->
                <div id="partnership-form-error" style="display: none; background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin-top: 30px; text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 10px; font-size: 1.2rem;"></i>
                    <?php _e('There was an error submitting your application. Please try again or contact us directly.', 'kilismile'); ?>
                </div>
            </form>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="partnership-cta" style="padding: 100px 0; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; text-align: center;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <h2 style="font-size: 2.8rem; margin-bottom: 25px; font-weight: 700;">
                <?php _e('Ready to Transform Lives Together?', 'kilismile'); ?>
            </h2>
            <p style="font-size: 1.3rem; margin-bottom: 40px; line-height: 1.6; opacity: 0.9;">
                <?php _e('Join our community of forward-thinking partners who are making a real difference in healthcare and community development across Tanzania.', 'kilismile'); ?>
            </p>
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="#partnership-form" class="cta-button" style="background: #27ae60; color: white; padding: 18px 35px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 10px 30px rgba(39,174,96,0.3);">
                    <?php _e('Apply Now', 'kilismile'); ?>
                </a>
                <a href="<?php echo home_url('/partners'); ?>" class="cta-button" style="background: rgba(255,255,255,0.1); color: white; padding: 18px 35px; border: 2px solid rgba(255,255,255,0.3); border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('View Current Partners', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<style>
/* Partnership Form Styles */
.partnership-form input:focus,
.partnership-form select:focus,
.partnership-form textarea:focus {
    border-color: #27ae60;
    outline: none;
    box-shadow: 0 0 0 3px rgba(39,174,96,0.1);
}

.partnership-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
    border-color: #27ae60;
}

.impact-card:hover {
    transform: translateY(-5px);
}

.benefit-item:hover {
    transform: translateY(-5px);
}

.cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    background: white;
}

.cta-secondary:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-3px);
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
}

/* Responsive Design */
@media (max-width: 768px) {
    .become-partner-hero h1 {
        font-size: 2.5rem;
    }
    
    .become-partner-hero p {
        font-size: 1.1rem;
    }
    
    .partnership-types .container > div {
        grid-template-columns: 1fr;
    }
    
    .partnership-benefits .container > div:nth-child(2) {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .partnership-benefits .container > div:nth-child(2) > div:first-child h3 {
        font-size: 1.8rem;
    }
    
    .partnership-form {
        padding: 30px 20px;
    }
    
    .partnership-form > div {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

@media (max-width: 480px) {
    .become-partner-hero {
        padding: 80px 0 60px;
    }
    
    .become-partner-hero h1 {
        font-size: 2rem;
    }
    
    .why-partner,
    .partnership-types,
    .partnership-benefits,
    .partnership-form-section,
    .partnership-cta {
        padding: 60px 0;
    }
    
    .benefit-stat {
        padding: 20px 15px;
    }
    
    .benefit-stat div:first-child {
        font-size: 2rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Partnership form handling
    const form = document.getElementById('partnership-application-form');
    const successMsg = document.getElementById('partnership-form-success');
    const errorMsg = document.getElementById('partnership-form-error');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 10px;"></i><?php _e("Submitting...", "kilismile"); ?>';
            submitBtn.disabled = true;
            
            // Collect form data
            const formData = new FormData(form);
            formData.append('action', 'kilismile_handle_partnership_application');
            formData.append('nonce', '<?php echo wp_create_nonce("kilismile_partnership_nonce"); ?>');
            
            // Submit via AJAX
            fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successMsg.style.display = 'block';
                    errorMsg.style.display = 'none';
                    form.reset();
                    
                    // Scroll to success message
                    successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    errorMsg.style.display = 'block';
                    successMsg.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMsg.style.display = 'block';
                successMsg.style.display = 'none';
            })
            .finally(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
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
});
</script>

<?php get_footer(); ?>