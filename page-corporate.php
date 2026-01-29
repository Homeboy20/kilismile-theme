<?php
/**
 * Template Name: Corporate Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="corporate-hero" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 120px 0 80px; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"grid\" width=\"10\" height=\"10\" patternUnits=\"userSpaceOnUse\"><path d=\"M 10 0 L 0 0 0 10\" fill=\"none\" stroke=\"rgba(255,255,255,0.1)\" stroke-width=\"0.5\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23grid)\"/></svg>'); opacity: 0.3;"></div>
        <div class="container" style="position: relative; z-index: 2;">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                <?php _e('Corporate Partnerships', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; line-height: 1.6; opacity: 0.9;">
                <?php _e('Transform your business impact through strategic partnerships with Kilismile. Join leading companies making a difference in global health education.', 'kilismile'); ?>
            </p>
            <div class="hero-buttons" style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
                <a href="#partnership-options" style="background: #27ae60; color: white; text-decoration: none; padding: 15px 30px; border-radius: 30px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);">
                    <?php _e('Explore Partnerships', 'kilismile'); ?>
                </a>
                <a href="#contact-corporate" style="background: transparent; color: white; text-decoration: none; padding: 15px 30px; border-radius: 30px; font-weight: 600; border: 2px solid rgba(255,255,255,0.3); transition: all 0.3s ease;">
                    <?php _e('Schedule Consultation', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Corporate Stats -->
    <section class="corporate-stats" style="padding: 80px 0; background: #f8f9fa;">
        <div class="container">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 40px; text-align: center;">
                <?php 
                $corporate_stats = get_option('kilismile_corporate_stats', "45|Corporate Partners\n$2.5M+|Corporate Investment\n150K+|Lives Impacted\n98%|Partner Satisfaction");
                $stats_array = array_filter(array_map('trim', explode("\n", $corporate_stats)));
                $colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12'];
                $descriptions = [
                    'Global companies trust our mission',
                    'Total corporate funding received',
                    'Through corporate partnerships',
                    'Would recommend our partnership'
                ];
                
                foreach ($stats_array as $index => $stat) {
                    $parts = explode('|', $stat, 2);
                    if (count($parts) == 2) {
                        $value = trim($parts[0]);
                        $label = trim($parts[1]);
                        $color = $colors[$index % count($colors)];
                        $description = isset($descriptions[$index]) ? $descriptions[$index] : 'Measuring our corporate impact';
                        
                        echo '<div class="stat-card" style="background: white; padding: 40px 20px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: transform 0.3s ease;" onmouseover="this.style.transform=\'translateY(-10px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                            <div style="font-size: 3rem; font-weight: bold; color: ' . $color . '; margin-bottom: 10px;">' . esc_html($value) . '</div>
                            <h3 style="color: #2c3e50; margin-bottom: 10px; font-size: 1.2rem;">' . esc_html($label) . '</h3>
                            <p style="color: #7f8c8d; font-size: 0.9rem;">' . esc_html($description) . '</p>
                        </div>';
                    }
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Partnership Options -->
    <section id="partnership-options" class="partnership-options" style="padding: 100px 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Corporate Partnership Opportunities', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Choose from multiple partnership models designed to align with your corporate objectives and CSR goals.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px;">
                <!-- Financial Sponsorship -->
                <div class="partnership-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 25px 50px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)'">
                    <div style="background: linear-gradient(135deg, #27ae60, #2ecc71); padding: 40px; text-align: center; color: white;">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-hand-holding-usd" style="font-size: 2rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.5rem;"><?php _e('Financial Sponsorship', 'kilismile'); ?></h3>
                    </div>
                    <div style="padding: 40px;">
                        <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 25px;">
                            <?php _e('Direct financial support for our health education programs with flexible sponsorship tiers and measurable impact metrics.', 'kilismile'); ?>
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 30px;">
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Tiered sponsorship levels', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Impact measurement & reporting', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Brand visibility opportunities', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Tax deductible donations', 'kilismile'); ?></span>
                            </li>
                        </ul>
                        <a href="/corporate-sponsors" style="display: inline-block; background: #27ae60; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
                            <?php _e('View Sponsorship Tiers', 'kilismile'); ?>
                        </a>
                    </div>
                </div>

                <!-- Strategic Partnerships -->
                <div class="partnership-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 25px 50px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)'">
                    <div style="background: linear-gradient(135deg, #3498db, #5dade2); padding: 40px; text-align: center; color: white;">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-handshake" style="font-size: 2rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.5rem;"><?php _e('Strategic Partnerships', 'kilismile'); ?></h3>
                    </div>
                    <div style="padding: 40px;">
                        <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 25px;">
                            <?php _e('Long-term collaborative partnerships that leverage your expertise and resources for mutual benefit and greater impact.', 'kilismile'); ?>
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 30px;">
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #3498db; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Joint program development', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #3498db; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Shared expertise & resources', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #3498db; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Co-branding opportunities', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center;">
                                <i class="fas fa-check-circle" style="color: #3498db; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Advisory board participation', 'kilismile'); ?></span>
                            </li>
                        </ul>
                        <a href="#contact-corporate" style="display: inline-block; background: #3498db; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
                            <?php _e('Explore Partnership', 'kilismile'); ?>
                        </a>
                    </div>
                </div>

                <!-- Employee Engagement -->
                <div class="partnership-card" style="background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.1); transition: all 0.3s ease; position: relative;" onmouseover="this.style.transform='translateY(-10px)'; this.style.boxShadow='0 25px 50px rgba(0,0,0,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 15px 35px rgba(0,0,0,0.1)'">
                    <div style="background: linear-gradient(135deg, #e74c3c, #ec7063); padding: 40px; text-align: center; color: white;">
                        <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                            <i class="fas fa-users" style="font-size: 2rem;" aria-hidden="true"></i>
                        </div>
                        <h3 style="margin: 0; font-size: 1.5rem;"><?php _e('Employee Engagement', 'kilismile'); ?></h3>
                    </div>
                    <div style="padding: 40px;">
                        <p style="color: #7f8c8d; line-height: 1.6; margin-bottom: 25px;">
                            <?php _e('Meaningful volunteer opportunities and team-building activities that boost employee engagement and satisfaction.', 'kilismile'); ?>
                        </p>
                        <ul style="list-style: none; padding: 0; margin-bottom: 30px;">
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #e74c3c; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Corporate volunteer programs', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #e74c3c; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Team building activities', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center; margin-bottom: 10px;">
                                <i class="fas fa-check-circle" style="color: #e74c3c; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Skills-based volunteering', 'kilismile'); ?></span>
                            </li>
                            <li style="display: flex; align-items: center;">
                                <i class="fas fa-check-circle" style="color: #e74c3c; margin-right: 10px;" aria-hidden="true"></i>
                                <span style="color: #2c3e50;"><?php _e('Employee recognition programs', 'kilismile'); ?></span>
                            </li>
                        </ul>
                        <a href="/volunteer" style="display: inline-block; background: #e74c3c; color: white; text-decoration: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; transition: all 0.3s ease;">
                            <?php _e('Learn About Volunteering', 'kilismile'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Corporate Partners Showcase -->
    <section class="corporate-partners-showcase" style="padding: 100px 0; background: #fff;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Our Valued Corporate Partners', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Meet the forward-thinking companies that share our vision for global health equity and social impact.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; align-items: center;">
                <?php 
                $corporate_partners = get_option('kilismile_corporate_partners', "TechCorp Solutions|Gold Sponsor|fas fa-building\nMediHealth Group|Platinum Sponsor|fas fa-heartbeat\nEcoSustain Ltd|Silver Sponsor|fas fa-leaf\nEduTech Innovations|Bronze Sponsor|fas fa-graduation-cap");
                $partners_array = array_filter(array_map('trim', explode("\n", $corporate_partners)));
                $sponsor_colors = [
                    'Gold Sponsor' => '#FFD700',
                    'Platinum Sponsor' => '#E5E4E2',
                    'Silver Sponsor' => '#C0C0C0',
                    'Bronze Sponsor' => '#CD7F32'
                ];
                $icon_colors = ['#27ae60', '#3498db', '#e74c3c', '#f39c12'];
                
                foreach ($partners_array as $index => $partner) {
                    $parts = explode('|', $partner, 3);
                    if (count($parts) == 3) {
                        $company = trim($parts[0]);
                        $level = trim($parts[1]);
                        $icon = trim($parts[2]);
                        $sponsor_color = isset($sponsor_colors[$level]) ? $sponsor_colors[$level] : '#27ae60';
                        $icon_color = $icon_colors[$index % count($icon_colors)];
                        
                        echo '<div class="partner-card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s ease; text-align: center;" onmouseover="this.style.transform=\'translateY(-5px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                            <div style="width: 60px; height: 60px; background: ' . $icon_color . '; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white;">
                                <i class="' . esc_attr($icon) . '" style="font-size: 1.5rem;" aria-hidden="true"></i>
                            </div>
                            <h4 style="color: #2c3e50; margin-bottom: 10px; font-size: 1.1rem;">' . esc_html($company) . '</h4>
                            <span style="background: ' . $sponsor_color . '; color: ' . ($level === 'Silver Sponsor' || $level === 'Platinum Sponsor' ? '#2c3e50' : 'white') . '; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: bold;">
                                ' . esc_html($level) . '
                            </span>
                        </div>';
                    }
                }
                
                // If no partners, show placeholder
                if (empty($partners_array)) {
                    echo '<div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                        <div style="color: #7f8c8d; font-style: italic;">
                            <span class="dashicons dashicons-groups" style="font-size: 3em; color: #dee2e6; margin-bottom: 12px; display: block;"></span>
                            <p style="margin: 0;">No corporate partners listed. Add partners from the Theme Dashboard.</p>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- CSR Benefits -->
    <section class="csr-benefits" style="padding: 100px 0; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Corporate Social Responsibility Benefits', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Partnering with Kilismile delivers measurable CSR outcomes that align with your corporate values and business objectives.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 60px; align-items: center;">
                <div>
                    <div class="benefit-item" style="display: flex; align-items: flex-start; margin-bottom: 40px;">
                        <div style="width: 60px; height: 60px; background: #27ae60; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-chart-line" style="color: white; font-size: 1.5rem;" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 style="color: #2c3e50; margin-bottom: 10px; font-size: 1.3rem;"><?php _e('Measurable Impact', 'kilismile'); ?></h3>
                            <p style="color: #7f8c8d; line-height: 1.6; margin: 0;"><?php _e('Track and report concrete outcomes with detailed impact metrics and regular progress updates for stakeholder communications.', 'kilismile'); ?></p>
                        </div>
                    </div>

                    <div class="benefit-item" style="display: flex; align-items: flex-start; margin-bottom: 40px;">
                        <div style="width: 60px; height: 60px; background: #3498db; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-award" style="color: white; font-size: 1.5rem;" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 style="color: #2c3e50; margin-bottom: 10px; font-size: 1.3rem;"><?php _e('Brand Enhancement', 'kilismile'); ?></h3>
                            <p style="color: #7f8c8d; line-height: 1.6; margin: 0;"><?php _e('Strengthen brand reputation through association with meaningful social impact and demonstrate commitment to global health equity.', 'kilismile'); ?></p>
                        </div>
                    </div>

                    <div class="benefit-item" style="display: flex; align-items: flex-start;">
                        <div style="width: 60px; height: 60px; background: #e74c3c; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px; flex-shrink: 0;">
                            <i class="fas fa-heart" style="color: white; font-size: 1.5rem;" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 style="color: #2c3e50; margin-bottom: 10px; font-size: 1.3rem;"><?php _e('Employee Satisfaction', 'kilismile'); ?></h3>
                            <p style="color: #7f8c8d; line-height: 1.6; margin: 0;"><?php _e('Boost employee morale and retention through meaningful corporate purpose and volunteer opportunities that align with personal values.', 'kilismile'); ?></p>
                        </div>
                    </div>
                </div>

                <div style="text-align: center;">
                    <div style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                        <h3 style="color: #2c3e50; font-size: 1.8rem; margin-bottom: 30px;"><?php _e('Partnership Impact Dashboard', 'kilismile'); ?></h3>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-bottom: 30px;">
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: bold; color: #27ae60; margin-bottom: 5px;">89%</div>
                                <div style="color: #7f8c8d; font-size: 0.9rem;"><?php _e('Employee Engagement Increase', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: bold; color: #3498db; margin-bottom: 5px;">5.2x</div>
                                <div style="color: #7f8c8d; font-size: 0.9rem;"><?php _e('ROI on CSR Investment', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: bold; color: #e74c3c; margin-bottom: 5px;">94%</div>
                                <div style="color: #7f8c8d; font-size: 0.9rem;"><?php _e('Stakeholder Approval Rating', 'kilismile'); ?></div>
                            </div>
                            <div style="text-align: center;">
                                <div style="font-size: 2.5rem; font-weight: bold; color: #f39c12; margin-bottom: 5px;">73%</div>
                                <div style="color: #7f8c8d; font-size: 0.9rem;"><?php _e('Brand Perception Improvement', 'kilismile'); ?></div>
                            </div>
                        </div>
                        <p style="color: #7f8c8d; font-size: 0.9rem; font-style: italic;"><?php _e('*Based on average results from corporate partner surveys', 'kilismile'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories -->
    <section class="success-stories" style="padding: 100px 0;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px;">
                    <?php _e('Corporate Partnership Success Stories', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Hear from our corporate partners about their experience and the impact we\'ve achieved together.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 50px;">
                <?php 
                $success_stories = get_option('kilismile_success_stories', "Our partnership with Kilismile has transformed our CSR program. We've seen unprecedented employee engagement and meaningful impact metrics that resonate with our stakeholders.|Sarah Johnson|CSR Director, TechCorp Global\n\nThe employee volunteer program through Kilismile has been a game-changer. Our team building and corporate culture have never been stronger.|Michael Chen|HR Director, HealthForward Inc.");
                $stories_array = array_filter(array_map('trim', explode("\n\n", $success_stories)));
                $colors = ['#27ae60', '#e74c3c', '#3498db', '#f39c12'];
                
                foreach ($stories_array as $index => $story) {
                    $parts = explode('|', $story, 3);
                    if (count($parts) == 3) {
                        $quote = trim($parts[0]);
                        $author = trim($parts[1]);
                        $title = trim($parts[2]);
                        $color = $colors[$index % count($colors)];
                        
                        echo '<div class="story-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); position: relative;">
                            <div style="position: absolute; top: -20px; left: 40px; width: 40px; height: 40px; background: ' . $color . '; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-quote-left" style="color: white; font-size: 1rem;" aria-hidden="true"></i>
                            </div>
                            <div style="margin-top: 20px;">
                                <p style="color: #2c3e50; font-size: 1.1rem; line-height: 1.6; margin-bottom: 30px; font-style: italic;">
                                    "' . esc_html($quote) . '"
                                </p>
                                <div style="display: flex; align-items: center;">
                                    <div style="width: 50px; height: 50px; background: ' . $color . '; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                        <i class="fas fa-user" style="color: white; font-size: 1.2rem;" aria-hidden="true"></i>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: #2c3e50; margin-bottom: 5px;">' . esc_html($author) . '</div>
                                        <div style="color: #7f8c8d; font-size: 0.9rem;">' . esc_html($title) . '</div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }
                
                // If no stories, show default
                if (empty($stories_array)) {
                    echo '<div class="story-card" style="background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); position: relative; grid-column: 1 / -1; text-align: center;">
                        <div style="color: #7f8c8d; font-style: italic;">
                            <span class="dashicons dashicons-format-quote" style="font-size: 3em; color: #dee2e6; margin-bottom: 12px; display: block;"></span>
                            <p style="margin: 0;">No success stories available. Add testimonials from the Theme Dashboard.</p>
                        </div>
                    </div>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact-corporate" class="contact-corporate" style="padding: 100px 0; background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white;">
        <div class="container">
            <div style="max-width: 1000px; margin: 0 auto;">
                <div style="text-align: center; margin-bottom: 60px;">
                    <h2 style="font-size: 2.5rem; margin-bottom: 20px;"><?php _e('Start Your Corporate Partnership', 'kilismile'); ?></h2>
                    <p style="font-size: 1.2rem; opacity: 0.9; line-height: 1.6;">
                        <?php _e('Let\'s discuss how we can create a customized partnership that delivers meaningful impact and measurable results for your organization.', 'kilismile'); ?>
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px;">
                    <!-- Contact Information -->
                    <div>
                        <h3 style="font-size: 1.5rem; margin-bottom: 30px; color: #ecf0f1;"><?php _e('Get in Touch', 'kilismile'); ?></h3>
                        
                        <div style="margin-bottom: 25px; display: flex; align-items: center;">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                                <i class="fas fa-phone" style="font-size: 1.2rem;" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; margin-bottom: 5px;"><?php _e('Corporate Partnerships', 'kilismile'); ?></div>
                                <div style="opacity: 0.8;">+255763495575/+255735495575</div>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px; display: flex; align-items: center;">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                                <i class="fas fa-envelope" style="font-size: 1.2rem;" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; margin-bottom: 5px;"><?php _e('Email Us', 'kilismile'); ?></div>
                                <div style="opacity: 0.8;">corporate@kilismile.org</div>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px; display: flex; align-items: center;">
                            <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 20px;">
                                <i class="fas fa-calendar" style="font-size: 1.2rem;" aria-hidden="true"></i>
                            </div>
                            <div>
                                <div style="font-weight: 600; margin-bottom: 5px;"><?php _e('Schedule a Meeting', 'kilismile'); ?></div>
                                <div style="opacity: 0.8;"><?php _e('Book a consultation call', 'kilismile'); ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 40px;">
                            <h4 style="font-size: 1.2rem; margin-bottom: 20px; color: #ecf0f1;"><?php _e('Partnership Response Time', 'kilismile'); ?></h4>
                            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span><?php _e('Initial Response:', 'kilismile'); ?></span>
                                    <span style="font-weight: 600;"><?php _e('24 hours', 'kilismile'); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                    <span><?php _e('Proposal Development:', 'kilismile'); ?></span>
                                    <span style="font-weight: 600;"><?php _e('5-7 days', 'kilismile'); ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span><?php _e('Partnership Launch:', 'kilismile'); ?></span>
                                    <span style="font-weight: 600;"><?php _e('2-4 weeks', 'kilismile'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div>
                        <form id="corporate-contact-form" style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 15px; backdrop-filter: blur(10px);">
                            <h3 style="font-size: 1.5rem; margin-bottom: 30px; color: #ecf0f1;"><?php _e('Partnership Inquiry', 'kilismile'); ?></h3>
                            
                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                    <?php _e('Company Name', 'kilismile'); ?> <span style="color: #e74c3c;">*</span>
                                </label>
                                <input type="text" required style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #2c3e50;">
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                        <?php _e('Contact Name', 'kilismile'); ?> <span style="color: #e74c3c;">*</span>
                                    </label>
                                    <input type="text" required style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #2c3e50;">
                                </div>
                                <div>
                                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                        <?php _e('Title/Position', 'kilismile'); ?>
                                    </label>
                                    <input type="text" style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #2c3e50;">
                                </div>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                    <?php _e('Email Address', 'kilismile'); ?> <span style="color: #e74c3c;">*</span>
                                </label>
                                <input type="email" required style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #2c3e50;">
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                    <?php _e('Partnership Interest', 'kilismile'); ?> <span style="color: #e74c3c;">*</span>
                                </label>
                                <select required style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #2c3e50;">
                                    <option value=""><?php _e('Select Partnership Type', 'kilismile'); ?></option>
                                    <option value="sponsorship"><?php _e('Financial Sponsorship', 'kilismile'); ?></option>
                                    <option value="strategic"><?php _e('Strategic Partnership', 'kilismile'); ?></option>
                                    <option value="employee"><?php _e('Employee Engagement', 'kilismile'); ?></option>
                                    <option value="custom"><?php _e('Custom Partnership', 'kilismile'); ?></option>
                                </select>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <label style="display: block; margin-bottom: 8px; font-weight: 600;">
                                    <?php _e('Partnership Goals', 'kilismile'); ?>
                                </label>
                                <textarea rows="4" style="width: 100%; padding: 12px; border: none; border-radius: 8px; background: rgba(255,255,255,0.9); color: #2c3e50; resize: vertical;" placeholder="<?php _e('Describe your corporate social responsibility goals and what you hope to achieve through partnership...', 'kilismile'); ?>"></textarea>
                            </div>

                            <button type="submit" style="width: 100%; padding: 15px; background: #27ae60; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                                <?php _e('Send Partnership Inquiry', 'kilismile'); ?>
                                <i class="fas fa-paper-plane" style="margin-left: 10px;" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
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

// Form submission
document.getElementById('corporate-contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php _e('Sending...', 'kilismile'); ?>';
    submitBtn.disabled = true;
    
    // Simulate form submission
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        // Show success message
        showNotification('<?php _e('Thank you for your partnership inquiry! Our corporate relations team will contact you within 24 hours to discuss opportunities.', 'kilismile'); ?>', 'success');
        this.reset();
    }, 2000);
});

// Notification function
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
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        max-width: 400px;
        animation: slideInRight 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 5000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);
</script>

<style>
/* Responsive Design */
@media (max-width: 1024px) {
    .corporate-stats .container > div,
    .partnership-options .container > div:last-child {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 30px;
    }
    
    .csr-benefits .container > div:last-child {
        grid-template-columns: 1fr !important;
        gap: 40px;
    }
    
    .success-stories .container > div:last-child {
        grid-template-columns: 1fr !important;
        gap: 30px;
    }
}

@media (max-width: 768px) {
    .corporate-hero h1 {
        font-size: 2.5rem !important;
    }
    
    .corporate-hero p {
        font-size: 1.1rem !important;
    }
    
    .hero-buttons {
        flex-direction: column !important;
        align-items: center;
    }
    
    .hero-buttons a {
        width: 250px;
        text-align: center;
    }
    
    .corporate-stats .container > div,
    .partnership-options .container > div:last-child {
        grid-template-columns: 1fr !important;
        gap: 25px;
    }
    
    .stat-card,
    .partnership-card {
        padding: 30px 20px !important;
    }
    
    .contact-corporate .container > div:last-child > div {
        grid-template-columns: 1fr !important;
        gap: 40px;
    }
    
    .corporate-contact-form {
        padding: 30px 20px !important;
    }
    
    .corporate-contact-form > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .corporate-hero {
        padding: 80px 0 60px !important;
    }
    
    .corporate-hero h1 {
        font-size: 2rem !important;
    }
    
    .partnership-options,
    .csr-benefits,
    .success-stories,
    .contact-corporate {
        padding: 60px 0 !important;
    }
    
    .partnership-options h2,
    .csr-benefits h2,
    .success-stories h2,
    .contact-corporate h2 {
        font-size: 2rem !important;
    }
    
    .stat-card {
        padding: 25px 15px !important;
    }
    
    .stat-card > div:first-child {
        font-size: 2.5rem !important;
    }
    
    .partnership-card {
        padding: 25px 20px !important;
    }
    
    .partnership-card .partnership-card:first-child {
        padding: 30px 20px !important;
    }
}

/* Hover effects */
.partnership-card:hover,
.stat-card:hover,
.story-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15);
}

.partnership-card a:hover,
.hero-buttons a:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Loading animation */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}
</style>

<?php get_footer(); ?>


