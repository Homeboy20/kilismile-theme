<?php
/**
 * The main template file - Homepage Redesign
 *
 * @package KiliSmile
 * @version 2.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section with Clean Background -->
    <section class="hero-section-redesign" style="position: relative; min-height: 75vh; display: flex; align-items: center; justify-content: center; background: var(--dark-green); color: white; text-align: center; overflow: hidden; padding-top: 80px; padding-bottom: 30px;">
        <!-- Simple Subtle Pattern -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05; background-image: 
            linear-gradient(45deg, rgba(255,255,255,0.1) 25%, transparent 25%, transparent 50%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.1) 75%, transparent 75%, transparent);
            background-size: 10px 10px; 
            z-index: 1;"></div>
        
        <div class="container" style="position: relative; z-index: 2; padding-top: 10px;">
            <div class="hero-content-redesign" style="max-width: 850px; margin: 0 auto; padding: 10px 15px;">
                <!-- Organization Details -->
                <?php if (get_theme_mod('kilismile_show_hero_badge', true)) : ?>
                <div class="registration-badge" style="display: inline-block; background: rgba(0, 0, 0, 0.2); padding: 8px 15px; border-radius: 4px; margin-bottom: 20px;">
                    <span style="font-size: 0.85rem; font-weight: 500; color: white;">Est. 25/04/2024 | NGO Reg. No: 07NGO/R/6067</span>
                </div>
                <?php endif; ?>

                <h1 class="hero-title-redesign" style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 20px; font-weight: 700; line-height: 1.2; color: white;">
                    Transforming Lives Through 
                    <span style="color: var(--light-green); display: inline; margin-top: 5px;">Health Education</span>
                </h1>
                
                <p class="hero-subtitle-redesign" style="font-size: clamp(1rem, 2vw, 1.2rem); margin-bottom: 25px; line-height: 1.5; max-width: 700px; margin-left: auto; margin-right: auto; color: rgba(255, 255, 255, 0.9);">
                    Empowering communities in Tanzania with essential oral health knowledge, teacher training, and comprehensive health screening programs.
                </p>

                <!-- CTA Buttons with Simple Design -->
                <div class="cta-buttons-redesign" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 35px;">
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_primary_btn_url', '#donate')); ?>" 
                       class="btn-primary-redesign" 
                       style="background: var(--primary-green); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-heart" style="font-size: 1rem;"></i>
                        <?php echo esc_html(get_theme_mod('kilismile_primary_btn_text', 'Donate Now')); ?>
                    </a>
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_secondary_btn_url', '/about')); ?>" 
                       class="btn-secondary-redesign" 
                       style="background: rgba(255, 255, 255, 0.1); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 500; font-size: 1rem; transition: all 0.3s ease; border: 1px solid rgba(255,255,255,0.2); display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-info-circle" style="font-size: 1rem;"></i>
                        <?php echo esc_html(get_theme_mod('kilismile_secondary_btn_text', 'Our Story')); ?>
                    </a>
                </div>

                <!-- Simplified Quick Stats -->
                <?php if (get_theme_mod('kilismile_show_hero_stats', true)) : ?>
            <div class="hero-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; max-width: 800px; margin: 0 auto;">
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_children', '500')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Children Reached</div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_elderly', '200')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Elderly Served</div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_teachers', '50')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Teachers Trained</div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);"><?php echo esc_html(get_theme_mod('kilismile_stat_areas', '10')); ?>+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Remote Areas</div>
                </div>
            </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Simple Scroll Indicator -->
        <?php if (get_theme_mod('kilismile_show_scroll_indicator', true)) : ?>
        <div class="scroll-indicator" style="position: absolute; bottom: 20px; left: 50%; transform: translateX(-50%); z-index: 2;">
            <i class="fas fa-chevron-down" style="font-size: 1.2rem; color: white; opacity: 0.6;"></i>
        </div>
        <?php endif; ?>
    </section>

    <!-- Mission Section with Card Layout -->
    <section class="mission-section-redesign" style="padding: 60px 0; background: var(--light-gray); position: relative;">
        <div class="container">
            <!-- Section Header -->
            <div class="section-header" style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Mission, Vision & Objectives
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 760px; margin: 0 auto; line-height: 1.5;">
                    Our guiding purpose, long-term vision, and the objectives that shape every program we deliver.
                </p>
            </div>

            <!-- Preamble, Mission, Vision, Objectives -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px;">
                <div style="background: #f2fbf2; padding: 28px; border-radius: 10px; box-shadow: 0 10px 24px rgba(0,0,0,0.06); border: 1px solid rgba(76, 175, 80, 0.18);">
                    <span style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: var(--primary-green); background: rgba(76, 175, 80, 0.12); padding: 6px 10px; border-radius: 999px; margin-bottom: 12px;">
                        <i class="fas fa-seedling" aria-hidden="true"></i>
                        Preamble
                    </span>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; margin: 0 0 12px;">
                        We, the members of KiliSmile Organization, believe that every person deserves the right to a healthy and dignified life. We are committed to improving community well-being by focusing on two key areas: promoting oral health among school-aged children and supporting elderly people living with non-communicable diseases.
                    </p>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; margin: 0 0 12px;">
                        For children, we work to ensure healthy smiles, confidence, and knowledge that last a lifetime. For the elderly, we provide care, education, and support that protect dignity, enhance quality of life, and encourage active aging.
                    </p>
                    <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; margin: 0;">
                        Guided by compassion, equity, prevention, and community empowerment, we strive to create lasting change through education, advocacy, and accessible health services—building a healthier and happier future for all generations.
                    </p>
                </div>

                <div style="display: grid; gap: 22px;">
                    <div style="background: #eef7ff; padding: 24px; border-radius: 10px; box-shadow: 0 10px 24px rgba(0,0,0,0.06); border: 1px solid rgba(33, 150, 243, 0.15);">
                        <span style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: #2196F3; background: rgba(33, 150, 243, 0.12); padding: 6px 10px; border-radius: 999px; margin-bottom: 10px;">
                            <i class="fas fa-bullseye" aria-hidden="true"></i>
                            Mission
                        </span>
                        <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; margin: 0;">
                            To improve the health and quality of life of children and elderly people through preventive healthcare, treatment services, health education, community outreach, advocacy, and the establishment and operation of sustainable health facilities, with special focus on oral health and non-communicable diseases among the elderly.
                        </p>
                    </div>
                    <div style="background: #fff5ea; padding: 24px; border-radius: 10px; box-shadow: 0 10px 24px rgba(0,0,0,0.06); border: 1px solid rgba(255, 152, 0, 0.2);">
                        <span style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 700; color: #ff9800; background: rgba(255, 152, 0, 0.15); padding: 6px 10px; border-radius: 999px; margin-bottom: 10px;">
                            <i class="fas fa-eye" aria-hidden="true"></i>
                            Vision
                        </span>
                        <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; margin: 0;">
                            A society where children and elderly people, including those living with non-communicable diseases, enjoy good health, dignity, and access to quality and affordable healthcare.
                        </p>
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; padding: 28px; border-radius: 10px; box-shadow: 0 10px 24px rgba(0,0,0,0.06); border: 1px solid rgba(76, 175, 80, 0.18); margin-top: 24px;">
                <h3 style="margin: 0 0 15px; color: var(--dark-green); font-size: 1.2rem; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                    <span style="width: 32px; height: 32px; border-radius: 8px; background: rgba(76, 175, 80, 0.12); display: inline-flex; align-items: center; justify-content: center; color: var(--primary-green);">
                        <i class="fas fa-list-check" aria-hidden="true"></i>
                    </span>
                    Objectives
                </h3>
                <ol style="margin: 0; padding-left: 20px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; column-count: 2; column-gap: 30px;">
                    <li>Promote access to quality and affordable healthcare for children and elderly persons.</li>
                    <li>Conduct community health, dental, and non-communicable disease (NCD) screenings, including hypertension, diabetes, and cardiovascular conditions.</li>
                    <li>Promote preventive healthcare, oral health lifestyles, and NCD prevention and management.</li>
                    <li>Improve nutrition, growth, and development among children.</li>
                    <li>Prevent, manage, and support elderly people living with non-communicable diseases including diabetes, hypertension, heart diseases, cancers, and other chronic conditions.</li>
                    <li>Provide mental health and psychosocial support.</li>
                    <li>Advocate for the rights, welfare, and protection of children and elderly people.</li>
                    <li>Strengthen community health systems through partnerships and capacity building.</li>
                    <li>Establish, own, manage, and operate health facilities including KiliSmile Dental Clinic (established April 2025) to provide treatment services and receive referrals from community screenings, school programs, and outreach activities.</li>
                    <li>Train primary school teachers on basic oral and general health so they can transfer knowledge and preventive practices to children in schools.</li>
                    <li>Facilitate referrals, treatment, follow-up, and continuity of care for patients identified during community health and NCD activities.</li>
                </ol>
            </div>
        </div>
    </section>

    <!-- Programs Overview Section -->
    <section class="programs-overview-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 45px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Our Core Programs
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 760px; margin: 0 auto; line-height: 1.5;">
                    Three focused programs deliver measurable health outcomes for children, educators, and elders across underserved communities.
                </p>
            </div>

            <div class="programs-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 20px;">
                <div style="background: var(--light-gray); padding: 25px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.2rem; font-weight: 600;">School Oral Health</h3>
                    <p style="color: var(--text-secondary); margin: 0 0 12px; line-height: 1.5; font-size: 0.95rem;">Interactive lessons, practical demonstrations, and supplies that build lifelong hygiene habits for pupils.</p>
                    <ul style="margin: 0; padding-left: 18px; color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">
                        <li>Brushing technique workshops</li>
                        <li>Teacher-supported take-home routines</li>
                        <li>Monitoring and follow-up visits</li>
                    </ul>
                </div>

                <div style="background: var(--light-gray); padding: 25px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.2rem; font-weight: 600;">Teacher Capacity Building</h3>
                    <p style="color: var(--text-secondary); margin: 0 0 12px; line-height: 1.5; font-size: 0.95rem;">Training teachers to serve as health champions who reinforce positive behavior daily.</p>
                    <ul style="margin: 0; padding-left: 18px; color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">
                        <li>Health lesson toolkits</li>
                        <li>Referral guidance and care pathways</li>
                        <li>Community engagement strategies</li>
                    </ul>
                </div>

                <div style="background: var(--light-gray); padding: 25px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.2rem; font-weight: 600;">Community Health Screening</h3>
                    <p style="color: var(--text-secondary); margin: 0 0 12px; line-height: 1.5; font-size: 0.95rem;">Mobile screening services for elders and vulnerable groups to detect health issues early.</p>
                    <ul style="margin: 0; padding-left: 18px; color: var(--text-secondary); font-size: 0.9rem; line-height: 1.5;">
                        <li>Blood pressure and glucose checks</li>
                        <li>Oral health assessments</li>
                        <li>On-site counseling and referrals</li>
                    </ul>
                </div>
            </div>

            <div style="text-align: center; margin-top: 25px;">
                <a href="<?php echo esc_url(home_url('/programs')); ?>" style="display: inline-flex; align-items: center; gap: 8px; background: var(--primary-green); color: white; padding: 12px 22px; border-radius: 4px; text-decoration: none; font-weight: 600;">
                    Explore All Programs
                    <i class="fas fa-arrow-right" style="font-size: 0.9rem;"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- How We Work Section -->
    <section class="how-we-work-section" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 45px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    How We Deliver Impact
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 760px; margin: 0 auto; line-height: 1.5;">
                    A clear, repeatable approach keeps every outreach consistent, accountable, and community-led.
                </p>
            </div>

            <div class="process-steps" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
                <div style="background: white; padding: 22px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <div style="font-size: 0.85rem; color: var(--primary-green); font-weight: 700; margin-bottom: 8px;">Step 01</div>
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Needs Assessment</h3>
                    <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">We coordinate with schools, clinics, and local leaders to identify priority needs and locations.</p>
                </div>
                <div style="background: white; padding: 22px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <div style="font-size: 0.85rem; color: var(--primary-green); font-weight: 700; margin-bottom: 8px;">Step 02</div>
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Program Delivery</h3>
                    <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">Our teams conduct lessons, screenings, and training using standardized tools and reporting.</p>
                </div>
                <div style="background: white; padding: 22px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <div style="font-size: 0.85rem; color: var(--primary-green); font-weight: 700; margin-bottom: 8px;">Step 03</div>
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Follow-Up & Reporting</h3>
                    <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">We track outcomes, record referrals, and share insights with partners and donors.</p>
                </div>
                <div style="background: white; padding: 22px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <div style="font-size: 0.85rem; color: var(--primary-green); font-weight: 700; margin-bottom: 8px;">Step 04</div>
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Community Ownership</h3>
                    <p style="margin: 0; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">Local champions continue health education between visits, ensuring sustainable impact.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Get Involved Section -->
    <section class="get-involved-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Ways to Support KiliSmile
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 760px; margin: 0 auto; line-height: 1.5;">
                    Whether you give, volunteer, or partner with us, your support directly strengthens community health services.
                </p>
            </div>

            <div class="support-options" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
                <div style="background: var(--light-gray); padding: 24px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Donate</h3>
                    <p style="margin: 0 0 12px; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">Fund essential outreach supplies, transport, and screening kits for field teams.</p>
                    <a href="<?php echo esc_url(home_url('/donations')); ?>" style="color: var(--primary-green); font-weight: 600; text-decoration: none;">Give now →</a>
                </div>
                <div style="background: var(--light-gray); padding: 24px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Volunteer</h3>
                    <p style="margin: 0 0 12px; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">Join our field activities, training sessions, or administrative support efforts.</p>
                    <a href="<?php echo esc_url(home_url('/contact')); ?>" style="color: var(--primary-green); font-weight: 600; text-decoration: none;">Get involved →</a>
                </div>
                <div style="background: var(--light-gray); padding: 24px; border-radius: 6px; border: 1px solid rgba(76, 175, 80, 0.12);">
                    <h3 style="margin: 0 0 10px; color: var(--dark-green); font-size: 1.1rem; font-weight: 600;">Partner</h3>
                    <p style="margin: 0 0 12px; color: var(--text-secondary); line-height: 1.5; font-size: 0.95rem;">Work with us on school programs, community outreach, or funding initiatives.</p>
                    <a href="<?php echo esc_url(home_url('/about')); ?>" style="color: var(--primary-green); font-weight: 600; text-decoration: none;">Learn more →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Counter Section -->
    <section class="impact-counter-section" style="padding: 60px 0; background: var(--light-gray); color: var(--dark-green); position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin-bottom: 15px; font-weight: 600; color: var(--dark-green);">
                    Real Impact, Real Change
                </h2>
                <p style="font-size: 1rem; max-width: 700px; margin: 0 auto; line-height: 1.5; color: var(--text-secondary);">
                    Every number represents a life touched and a community strengthened through our health initiatives.
                </p>
            </div>

            <div class="impact-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; max-width: 900px; margin: 0 auto;">
                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="780">780</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Children Educated</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Comprehensive health awareness</div>
                </div>

                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="420">420</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Elderly Supported</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Health screening services</div>
                </div>

                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="95">95</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Teachers Trained</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Health education advocates</div>
                </div>

                <div class="impact-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);" data-count="18">18</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Remote Communities</div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); opacity: 0.8; margin-top: 5px;">Areas receiving care</div>
                </div>
            </div>

            <!-- Milestones Timeline -->
            <div class="milestones-section" style="margin-top: 50px; text-align: center; background: white; padding: 30px 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                <h2 style="font-size: 1.5rem; margin-bottom: 25px; color: var(--dark-green); font-weight: 600; position: relative; padding-bottom: 10px; display: inline-block;">
                    Our Journey Since April 2024
                    <span style="display: block; width: 60px; height: 3px; background: var(--primary-green); position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
                </h2>
                
                <!-- Timeline -->
                <div class="timeline" style="position: relative; max-width: 900px; margin: 30px auto 0;">
                    <!-- Timeline line -->
                    <div style="position: absolute; left: 50%; top: 0; bottom: 0; width: 2px; background: var(--light-green); transform: translateX(-50%);"></div>
                    
                    <!-- Timeline items -->
                    <div class="timeline-item" style="position: relative; margin-bottom: 30px; display: flex; align-items: center;">
                        <div class="timeline-content" style="background: white; padding: 20px; border-radius: 4px; width: 45%; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-right: auto; border-left: 3px solid var(--primary-green);">
                            <div class="timeline-year" style="color: var(--primary-green); font-weight: bold; font-size: 1rem; margin-bottom: 5px;">April 2024</div>
                            <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.1rem;">Launch</h3>
                            <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.9rem;">Kilismile Organization was officially registered with NGO number 07NGO/R/6067.</p>
                        </div>
                        <div class="timeline-marker" style="position: absolute; left: 50%; width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; transform: translateX(-50%); border: 2px solid white; z-index: 1;"></div>
                    </div>

                    <div class="timeline-item" style="position: relative; margin-bottom: 30px; display: flex; align-items: center;">
                        <div class="timeline-content" style="background: white; padding: 20px; border-radius: 4px; width: 45%; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-left: auto; border-left: 3px solid var(--primary-green);">
                            <div class="timeline-year" style="color: var(--primary-green); font-weight: bold; font-size: 1rem; margin-bottom: 5px;">May 2024</div>
                            <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.1rem;">First Outreach</h3>
                            <p style="color: var(--text-secondary); line-height: 1.5; font-size: 0.9rem;">Conducted our first community health education program in Same District.</p>
                        </div>
                        <div class="timeline-marker" style="position: absolute; left: 50%; width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; transform: translateX(-50%); border: 2px solid white; z-index: 1;"></div>
                    </div>

                    <div class="timeline-item" style="position: relative; display: flex; align-items: center;">
                        <div class="timeline-content" style="background: var(--primary-green); color: white; padding: 20px; border-radius: 4px; width: 45%; box-shadow: 0 3px 10px rgba(0,0,0,0.05); margin-right: auto;">
                            <div class="timeline-year" style="color: white; font-weight: bold; font-size: 1rem; margin-bottom: 5px;">August 2025</div>
                            <h3 style="color: white; margin-bottom: 8px; font-size: 1.1rem;">Major Milestone</h3>
                            <p style="color: white; opacity: 0.9; line-height: 1.5; font-size: 0.9rem;">Reaching over 500 lives through our comprehensive health education programs.</p>
                        </div>
                        <div class="timeline-marker" style="position: absolute; left: 50%; width: 12px; height: 12px; background: var(--primary-green); border-radius: 50%; transform: translateX(-50%); border: 2px solid white; z-index: 1;"></div>
                    </div>
                </div>
                
                <!-- Future Goals Banner -->
                <div style="margin-top: 40px; background: var(--light-gray); padding: 25px; border-radius: 4px; max-width: 600px; margin-left: auto; margin-right: auto; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border-left: 3px solid var(--primary-green);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 10px;">
                        <div style="width: 40px; height: 40px; background: var(--primary-green); border-radius: 4px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-bullseye" style="color: white; font-size: 1.1rem;"></i>
                        </div>
                        <h4 style="font-size: 1.2rem; font-weight: 600; margin: 0; color: var(--dark-green);">Future Goals: 2026</h4>
                    </div>
                    <p style="margin: 10px 0 0; line-height: 1.5; color: var(--text-secondary); font-size: 0.95rem; text-align: left; padding-left: 55px;">Expanding to 5 more remote communities and reaching an additional 1,000 children through our enhanced health education curriculum.</p>
                </div>
            </div>
        </div>
        
        <!-- Simplified animation keyframes and responsive styles -->
        <style>
            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-5px); }
            }
            
            /* Responsive styles for milestones timeline */
            @media (max-width: 768px) {
                .timeline-item .timeline-content {
                    width: calc(100% - 30px) !important;
                    margin: 0 15px !important;
                }
                
                .timeline {
                    padding: 0 10px;
                }
            }
        </style>
    </section>

    <!-- Programs Showcase -->
    <section class="programs-showcase" style="padding: 80px 0; background: linear-gradient(135deg, #f8fdf9 0%, #ffffff 100%);">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 50px;">
                <span style="display: inline-block; padding: 6px 16px; background: rgba(76, 175, 80, 0.1); color: var(--primary-green); border-radius: 20px; font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;">
                    Our Programs
                </span>
                <h2 style="font-size: clamp(2rem, 4vw, 2.5rem); margin: 0 0 18px; color: var(--dark-green); font-weight: 700; line-height: 1.2;">
                    Transforming Lives Through Health
                </h2>
                <p style="font-size: 1.05rem; color: var(--text-secondary); max-width: 650px; margin: 0 auto; line-height: 1.7;">
                    Empowering communities with accessible health education, preventive care, and sustainable wellness programs.
                </p>
            </div>

            <div class="programs-carousel-wrapper" style="position: relative; max-width: 1400px; margin: 0 auto;">
                <button class="carousel-nav prev-btn" style="position: absolute; left: -60px; top: 50%; transform: translateY(-50%); background: white; border: none; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.1); z-index: 10; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='var(--primary-green)'; this.querySelector('i').style.color='white';" onmouseout="this.style.background='white'; this.querySelector('i').style.color='var(--primary-green)';">
                    <i class="fas fa-chevron-left" style="font-size: 1.2rem; color: var(--primary-green); transition: color 0.3s ease;"></i>
                </button>
                
                <div class="programs-carousel" style="overflow: hidden; padding: 0 10px;">
                    <div class="programs-track" style="display: flex; gap: 25px; transition: transform 0.5s ease;">
                        <?php
                        $programs = new WP_Query(array(
                            'post_type' => 'programs',
                            'posts_per_page' => -1,
                            'post_status' => 'publish'
                        ));
                
                if ($programs->have_posts()) :
                    $program_icons = ['fas fa-tooth', 'fas fa-chalkboard-teacher', 'fas fa-hands-helping', 'fas fa-heartbeat'];
                    $program_colors = ['#4CAF50', '#66BB6A', '#81C784', '#A5D6A7'];
                    $icon_index = 0;
                    
                    while ($programs->have_posts()) : $programs->the_post();
                        $target_audience = get_post_meta(get_the_ID(), '_program_target_audience', true);
                        $status = get_post_meta(get_the_ID(), '_program_status', true);
                        $beneficiaries = get_post_meta(get_the_ID(), '_program_beneficiaries', true);
                        $current_icon = $program_icons[$icon_index % 4];
                        $current_color = $program_colors[$icon_index % 4];
                ?>
                <div class="program-card-modern" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); position: relative; transition: all 0.3s ease; border: 1px solid rgba(0,0,0,0.04); flex: 0 0 calc(25% - 19px); min-width: 300px;">
                    <style>
                        .program-card-modern:hover {
                            transform: translateY(-8px);
                            box-shadow: 0 12px 35px rgba(76, 175, 80, 0.15);
                        }
                        .program-card-modern .program-link:hover h3 {
                            color: <?php echo $current_color; ?>;
                        }
                        .program-card-modern .learn-more-btn:hover {
                            background: <?php echo $current_color; ?>;
                            transform: translateX(5px);
                        }
                    </style>
                    
                    <div class="program-image-wrapper" style="position: relative; height: 220px; overflow: hidden; background: linear-gradient(135deg, <?php echo $current_color; ?>22 0%, <?php echo $current_color; ?>11 100%);">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;')); ?>
                            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.4) 100%);"></div>
                        <?php else : ?>
                            <div style="height: 100%; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 12px;">
                                <div style="width: 80px; height: 80px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    <i class="<?php echo $current_icon; ?>" style="font-size: 2.2rem; color: <?php echo $current_color; ?>;"></i>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($status) : ?>
                            <div style="position: absolute; top: 16px; left: 16px; background: <?php echo $current_color; ?>; color: white; padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                <?php echo esc_html($status); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="position: absolute; bottom: 16px; right: 16px; width: 50px; height: 50px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 12px rgba(0,0,0,0.15);">
                            <i class="<?php echo $current_icon; ?>" style="font-size: 1.3rem; color: <?php echo $current_color; ?>;"></i>
                        </div>
                    </div>

                    <div class="program-content" style="padding: 28px 24px;">
                        <h3 style="font-size: 1.35rem; margin: 0 0 12px; font-weight: 700; color: var(--dark-green); line-height: 1.3; transition: color 0.3s ease;">
                            <a href="<?php the_permalink(); ?>" class="program-link" style="text-decoration: none; color: inherit;">
                                <?php the_title(); ?>
                            </a>
                        </h3>
                        
                        <div class="program-excerpt" style="color: #666; margin-bottom: 20px; line-height: 1.6; font-size: 0.95rem;">
                            <?php echo wp_trim_words(get_the_excerpt(), 18); ?>
                        </div>
                        
                        <?php if ($target_audience || $beneficiaries) : ?>
                        <div class="program-meta" style="display: flex; gap: 20px; margin-bottom: 20px; padding: 16px; background: rgba(76, 175, 80, 0.04); border-radius: 10px; border-left: 3px solid <?php echo $current_color; ?>;">
                            <?php if ($target_audience) : ?>
                            <div class="meta-item" style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <i class="fas fa-users" style="color: <?php echo $current_color; ?>; font-size: 0.9rem;"></i>
                                    <div style="font-size: 0.75rem; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Target</div>
                                </div>
                                <div style="font-weight: 600; color: var(--dark-green); font-size: 0.9rem;"><?php echo esc_html($target_audience); ?></div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($beneficiaries) : ?>
                            <div class="meta-item" style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <i class="fas fa-heart" style="color: <?php echo $current_color; ?>; font-size: 0.9rem;"></i>
                                    <div style="font-size: 0.75rem; color: #888; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px;">Impact</div>
                                </div>
                                <div style="font-weight: 600; color: var(--dark-green); font-size: 0.9rem;"><?php echo esc_html($beneficiaries); ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        
                        <a href="<?php the_permalink(); ?>" class="learn-more-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: transparent; color: <?php echo $current_color; ?>; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; border: 2px solid <?php echo $current_color; ?>;">
                            Learn More
                            <i class="fas fa-arrow-right" style="font-size: 0.8rem; transition: transform 0.3s ease;"></i>
                        </a>
                    </div>
                </div>
                <?php
                        $icon_index++;
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <div class="no-programs" style="flex: 0 0 100%; text-align: center; padding: 60px 30px; background: white; border-radius: 16px; border: 2px dashed rgba(76, 175, 80, 0.2);">
                    <div style="width: 100px; height: 100px; background: rgba(76, 175, 80, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-seedling" style="font-size: 3rem; color: var(--primary-green);"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 12px; font-size: 1.5rem; font-weight: 700;">Programs Coming Soon</h3>
                    <p style="color: var(--text-secondary); font-size: 1rem; max-width: 500px; margin: 0 auto;">We're developing comprehensive health programs to serve our communities better. Stay tuned!</p>
                </div>
                <?php endif; ?>
                    </div>
                </div>
                
                <button class="carousel-nav next-btn" style="position: absolute; right: -60px; top: 50%; transform: translateY(-50%); background: white; border: none; width: 50px; height: 50px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.1); z-index: 10; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center;" onmouseover="this.style.background='var(--primary-green)'; this.querySelector('i').style.color='white';" onmouseout="this.style.background='white'; this.querySelector('i').style.color='var(--primary-green)';">
                    <i class="fas fa-chevron-right" style="font-size: 1.2rem; color: var(--primary-green); transition: color 0.3s ease;"></i>
                </button>
            </div>
            
            <div class="carousel-dots" style="display: flex; justify-content: center; gap: 8px; margin-top: 40px;">
                <!-- Dots will be dynamically generated by JavaScript -->
            </div>

            <div style="text-align: center; margin-top: 50px;">
                <a href="<?php echo esc_url(home_url('/programs')); ?>" style="display: inline-flex; align-items: center; gap: 10px; background: var(--primary-green); color: white; padding: 14px 32px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                    View All Programs
                    <i class="fas fa-arrow-right" style="font-size: 0.9rem;"></i>
                </a>
            </div>
        </div>
        
        <style>
            .program-card-modern:hover {
                transform: translateY(-8px);
                box-shadow: 0 12px 35px rgba(76, 175, 80, 0.15);
            }
            .program-card-modern .program-link:hover h3 {
                color: var(--primary-green);
            }
            .program-card-modern .learn-more-btn:hover {
                background: var(--primary-green);
                transform: translateX(5px);
            }
            .program-card-modern:hover .program-image-wrapper img {
                transform: scale(1.05);
            }
            
            @media (max-width: 1200px) {
                .programs-showcase {
                    padding: 50px 0 !important;
                }
                .carousel-nav {
                    display: none !important;
                }
                .program-card-modern {
                    flex: 0 0 calc(33.333% - 17px) !important;
                }
            }
            
            @media (max-width: 768px) {
                .program-card-modern {
                    flex: 0 0 calc(100% - 20px) !important;
                    min-width: 280px !important;
                }
                .programs-carousel {
                    padding: 0 5px !important;
                }
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const track = document.querySelector('.programs-track');
                const prevBtn = document.querySelector('.prev-btn');
                const nextBtn = document.querySelector('.next-btn');
                const dotsContainer = document.querySelector('.carousel-dots');
                
                if (!track) return;
                
                const cards = track.querySelectorAll('.program-card-modern');
                const totalCards = cards.length;
                
                if (totalCards === 0) return;
                
                let currentIndex = 0;
                const cardsToShow = window.innerWidth > 1200 ? 4 : window.innerWidth > 768 ? 3 : 1;
                const totalSlides = Math.max(1, Math.ceil(totalCards - cardsToShow + 1));
                
                // Create dots
                for (let i = 0; i < totalSlides; i++) {
                    const dot = document.createElement('button');
                    dot.style.cssText = 'width: 10px; height: 10px; border-radius: 50%; border: none; background: rgba(76, 175, 80, 0.3); cursor: pointer; transition: all 0.3s ease; padding: 0;';
                    dot.onclick = () => goToSlide(i);
                    dotsContainer.appendChild(dot);
                }
                
                function updateCarousel() {
                    const cardWidth = cards[0].offsetWidth;
                    const gap = 25;
                    const offset = currentIndex * (cardWidth + gap);
                    track.style.transform = `translateX(-${offset}px)`;
                    
                    // Update dots
                    const dots = dotsContainer.querySelectorAll('button');
                    dots.forEach((dot, index) => {
                        if (index === currentIndex) {
                            dot.style.background = 'var(--primary-green)';
                            dot.style.width = '30px';
                        } else {
                            dot.style.background = 'rgba(76, 175, 80, 0.3)';
                            dot.style.width = '10px';
                        }
                    });
                    
                    // Update button states
                    if (prevBtn && nextBtn) {
                        prevBtn.style.opacity = currentIndex === 0 ? '0.3' : '1';
                        prevBtn.style.cursor = currentIndex === 0 ? 'not-allowed' : 'pointer';
                        nextBtn.style.opacity = currentIndex >= totalSlides - 1 ? '0.3' : '1';
                        nextBtn.style.cursor = currentIndex >= totalSlides - 1 ? 'not-allowed' : 'pointer';
                    }
                }
                
                function goToSlide(index) {
                    currentIndex = Math.max(0, Math.min(index, totalSlides - 1));
                    updateCarousel();
                }
                
                if (prevBtn) {
                    prevBtn.addEventListener('click', () => {
                        if (currentIndex > 0) {
                            currentIndex--;
                            updateCarousel();
                        }
                    });
                }
                
                if (nextBtn) {
                    nextBtn.addEventListener('click', () => {
                        if (currentIndex < totalSlides - 1) {
                            currentIndex++;
                            updateCarousel();
                        }
                    });
                }
                
                // Touch support for mobile
                let touchStartX = 0;
                let touchEndX = 0;
                
                track.addEventListener('touchstart', (e) => {
                    touchStartX = e.changedTouches[0].screenX;
                });
                
                track.addEventListener('touchend', (e) => {
                    touchEndX = e.changedTouches[0].screenX;
                    handleSwipe();
                });
                
                function handleSwipe() {
                    if (touchEndX < touchStartX - 50 && currentIndex < totalSlides - 1) {
                        currentIndex++;
                        updateCarousel();
                    }
                    if (touchEndX > touchStartX + 50 && currentIndex > 0) {
                        currentIndex--;
                        updateCarousel();
                    }
                }
                
                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft' && currentIndex > 0) {
                        currentIndex--;
                        updateCarousel();
                    }
                    if (e.key === 'ArrowRight' && currentIndex < totalSlides - 1) {
                        currentIndex++;
                        updateCarousel();
                    }
                });
                
                // Initialize
                updateCarousel();
                
                // Handle window resize
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        location.reload();
                    }, 500);
                });
            });
        </script>
    </section>

    <!-- News & Updates Section -->
    <section class="news-section-redesign" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    Latest News & Updates
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    Stay connected with our latest community outreach, success stories, and upcoming initiatives.
                </p>
            </div>

            <div class="news-grid-redesign" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; max-width: 1200px; margin: 0 auto;">
                <style>
                    @media (min-width: 992px) {
                        .news-grid-redesign {
                            grid-template-columns: repeat(3, 1fr) !important;
                        }
                    }
                    @media (max-width: 991px) and (min-width: 768px) {
                        .news-grid-redesign {
                            grid-template-columns: repeat(2, 1fr) !important;
                        }
                    }
                </style>
                <?php
                $news_query = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'post_status' => 'publish'
                ));
                
                if ($news_query->have_posts()) :
                    while ($news_query->have_posts()) : $news_query->the_post();
                        $reading_time = ceil(str_word_count(get_the_content()) / 200);
                ?>
                <article class="news-card-redesign" style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; border: 1px solid rgba(76, 175, 80, 0.1); height: 100%; display: flex; flex-direction: column;">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="news-image-container" style="height: 180px; position: relative; overflow: hidden;">
                            <?php the_post_thumbnail('medium', array('style' => 'width: 100%; height: 100%; object-fit: cover;')); ?>
                            <div style="position: absolute; top: 10px; left: 10px; background: var(--primary-green); color: white; padding: 4px 10px; border-radius: 2px; font-size: 0.75rem; font-weight: 500;">
                                <?php echo get_the_date('M j, Y'); ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div style="height: 180px; background: rgba(76, 175, 80, 0.05); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-newspaper" style="font-size: 3rem; color: var(--primary-green); opacity: 0.5;"></i>
                        </div>
                    <?php endif; ?>

                    <div class="news-content" style="padding: 20px; display: flex; flex-direction: column; flex-grow: 1;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 0.8rem; color: var(--text-secondary);">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="fas fa-user" style="font-size: 0.8rem;"></i>
                                <?php echo get_the_author(); ?>
                            </div>
                            <?php 
                            $categories = get_the_category();
                            if (!empty($categories)) : 
                            ?>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="fas fa-folder" style="font-size: 0.8rem;"></i>
                                <?php echo esc_html($categories[0]->name); ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <h3 style="font-size: 1.1rem; margin: 0 0 10px; font-weight: 600; line-height: 1.4; flex-grow: 0;">
                            <a href="<?php the_permalink(); ?>" style="text-decoration: none; color: var(--text-primary);">
                                <?php the_title(); ?>
                            </a>
                        </h3>

                        <div style="color: var(--text-secondary); margin-bottom: 15px; line-height: 1.5; font-size: 0.9rem; flex-grow: 1;">
                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                        </div>

                        <a href="<?php the_permalink(); ?>" style="display: inline-flex; align-items: center; font-size: 0.85rem; color: var(--primary-green); font-weight: 600; text-decoration: none; gap: 5px; margin-top: auto;">
                            Read More
                            <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </div>
                </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                ?>
                <div class="no-news" style="grid-column: 1 / -1; text-align: center; padding: 30px 20px; background: white; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <i class="fas fa-newspaper" style="font-size: 2rem; color: var(--primary-green); margin-bottom: 15px; opacity: 0.7;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 8px; font-size: 1.2rem;">News Coming Soon</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">We're working on bringing you the latest updates from our field work.</p>
                </div>
                <?php endif; ?>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <a href="<?php echo esc_url(home_url('/news')); ?>" class="view-all-news-btn" style="display: inline-flex; align-items: center; gap: 8px; background: var(--accent-green); color: white; padding: 10px 20px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease; box-shadow: 0 3px 10px rgba(102, 187, 106, 0.2);">
                    <i class="fas fa-newspaper" style="font-size: 0.9rem;"></i>
                    View All News
                </a>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <?php if (function_exists('display_homepage_partner_logos')) : ?>
        <?php display_homepage_partner_logos(); ?>
    <?php endif; ?>

    <!-- Health Quotes Section -->
    <?php if (get_theme_mod('kilismile_show_health_quotes', true)) : ?>
    <section class="health-quotes-section" style="padding: 60px 0; background: white; position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <div class="section-header" style="text-align: center; margin-bottom: 40px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; color: var(--dark-green); font-weight: 600; line-height: 1.3;">
                    <?php echo esc_html(get_theme_mod('kilismile_health_quotes_title', 'Health Wisdom')); ?>
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    <?php echo esc_html(get_theme_mod('kilismile_health_quotes_subtitle', 'Wisdom that guides our mission and inspires healthier communities.')); ?>
                </p>
            </div>

            <div class="health-quotes-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <!-- Quote 1 -->
                <div class="quote-card" style="background: white; padding: 25px 20px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; border-left: 4px solid var(--primary-green);">
                    <div style="display: flex; margin-bottom: 15px; align-items: center;">
                        <i class="fas fa-quote-left" style="color: var(--primary-green); font-size: 1.2rem; margin-right: 10px;"></i>
                        <div style="height: 1px; flex-grow: 1; background-color: rgba(76, 175, 80, 0.2);"></div>
                    </div>

                    <blockquote style="margin-bottom: 15px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; position: relative;">
                        "<?php echo esc_html(get_theme_mod('kilismile_quote1_text', 'A smile is a curve that sets everything straight. Oral health is not just about healthy teeth; it\'s about maintaining dignity and quality of life.')); ?>"
                    </blockquote>

                    <div class="quote-source" style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 4px; background: rgba(76, 175, 80, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-tooth" style="color: var(--primary-green);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--text-primary); margin: 0 0 2px; font-size: 0.95rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote1_author', 'World Health Organization')); ?>
                            </h4>
                            <div style="color: var(--text-secondary); font-size: 0.8rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote1_source', 'Global Health')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote 2 -->
                <div class="quote-card" style="background: white; padding: 25px 20px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; border-left: 4px solid var(--accent-green);">
                    <div style="display: flex; margin-bottom: 15px; align-items: center;">
                        <i class="fas fa-quote-left" style="color: var(--accent-green); font-size: 1.2rem; margin-right: 10px;"></i>
                        <div style="height: 1px; flex-grow: 1; background-color: rgba(102, 187, 106, 0.2);"></div>
                    </div>

                    <blockquote style="margin-bottom: 15px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; position: relative;">
                        "<?php echo esc_html(get_theme_mod('kilismile_quote2_text', 'Education is the most powerful weapon which you can use to change the world. Health education empowers communities to take control of their wellbeing.')); ?>"
                    </blockquote>

                    <div class="quote-source" style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 4px; background: rgba(102, 187, 106, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-heartbeat" style="color: var(--accent-green);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--text-primary); margin: 0 0 2px; font-size: 0.95rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote2_author', 'Nelson Mandela')); ?>
                            </h4>
                            <div style="color: var(--text-secondary); font-size: 0.8rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote2_source', 'Humanitarian Leader')); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quote 3 -->
                <div class="quote-card" style="background: white; padding: 25px 20px; border-radius: 4px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); text-align: left; position: relative; border-left: 4px solid var(--light-green);">
                    <div style="display: flex; margin-bottom: 15px; align-items: center;">
                        <i class="fas fa-quote-left" style="color: var(--light-green); font-size: 1.2rem; margin-right: 10px;"></i>
                        <div style="height: 1px; flex-grow: 1; background-color: rgba(129, 199, 132, 0.2);"></div>
                    </div>

                    <blockquote style="margin-bottom: 15px; color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem; position: relative;">
                        "<?php echo esc_html(get_theme_mod('kilismile_quote3_text', 'The greatest wealth is health. When you invest in community health education, you\'re investing in the future of humanity.')); ?>"
                    </blockquote>

                    <div class="quote-source" style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 40px; height: 40px; border-radius: 4px; background: rgba(129, 199, 132, 0.1); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-hand-holding-heart" style="color: var(--light-green);"></i>
                        </div>
                        <div>
                            <h4 style="font-weight: 600; color: var(--text-primary); margin: 0 0 2px; font-size: 0.95rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote3_author', 'Virgil')); ?>
                            </h4>
                            <div style="color: var(--text-secondary); font-size: 0.8rem;">
                                <?php echo esc_html(get_theme_mod('kilismile_quote3_source', 'Ancient Wisdom')); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Featured Quote -->
            <div style="margin-top: 40px; padding: 20px; background: rgba(76, 175, 80, 0.05); border-radius: 4px; max-width: 800px; margin-left: auto; margin-right: auto;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                    <i class="fas fa-lightbulb" style="font-size: 1rem; color: var(--primary-green);"></i>
                    <div style="font-weight: 600; color: var(--dark-green); font-size: 1rem;">Featured Quote</div>
                </div>
                <blockquote style="color: var(--text-secondary); line-height: 1.6; font-size: 1rem; margin-bottom: 10px;">
                    "<?php echo esc_html(get_theme_mod('kilismile_featured_quote_text', 'Health is not valued until sickness comes. Prevention through education is the foundation of public health.')); ?>"
                </blockquote>
                <div style="font-weight: 600; color: var(--dark-green); font-size: 0.9rem; text-align: right;">
                    — <?php echo esc_html(get_theme_mod('kilismile_featured_quote_author', 'Dr. Thomas Fuller')); ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Call to Action Section -->
    <section class="cta-section-redesign" style="padding: 60px 0; background: var(--dark-green); color: white; text-align: center; position: relative;">
        <div class="container" style="position: relative; z-index: 2;">
            <div style="max-width: 800px; margin: 0 auto;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin: 0 0 15px; font-weight: 600; line-height: 1.3;">
                    Be Part of the Health Revolution
                </h2>

                <p style="font-size: 1rem; margin-bottom: 30px; line-height: 1.5; max-width: 700px; margin-left: auto; margin-right: auto; color: rgba(255, 255, 255, 0.9);">
                    Your support enables us to reach more remote communities with life-saving health education. 
                    Together, we can create a healthier Tanzania, one community at a time.
                </p>

                <!-- Impact Preview -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">🏥</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Health Screenings</div>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">👨‍🏫</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Teacher Training</div>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">🦷</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Oral Health</div>
                    </div>
                    <div style="text-align: center; background: rgba(0, 0, 0, 0.15); padding: 15px 10px; border-radius: 4px;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">🏘️</div>
                        <div style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.9);">Community Outreach</div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 25px;">
                    <a href="<?php echo esc_url(get_theme_mod('kilismile_donation_url', '#donate')); ?>" 
                       style="background: white; color: var(--primary-green); padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-heart" style="font-size: 0.9rem; color: #e74c3c;"></i>
                        Make a Donation
                    </a>
                    <a href="<?php echo esc_url(home_url('/volunteer')); ?>" 
                       style="background: rgba(255, 255, 255, 0.15); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: 500; font-size: 1rem; transition: all 0.3s ease; border: 1px solid rgba(255, 255, 255, 0.3); display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-hands-helping" style="font-size: 0.9rem;"></i>
                        Volunteer With Us
                    </a>
                </div>

                <!-- Quick Contact -->
                <div style="font-size: 0.9rem; color: rgba(255, 255, 255, 0.8);">
                    Questions? <a href="<?php echo esc_url(home_url('/contact')); ?>" style="color: var(--light-green); text-decoration: underline; font-weight: 500;">Get in touch</a> 
                    or call us at <a href="tel:+255763495575" style="color: white; text-decoration: none; font-weight: 500;">+255763495575/+255735495575</a>
                </div>
            </div>
        </div>
    </section>
</main>

<style>
/* Enhanced animations and hover effects */
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateX(-50%) translateY(0); }
    40% { transform: translateX(-50%) translateY(-8px); }
    60% { transform: translateX(-50%) translateY(-4px); }
}

@keyframes wave {
    0% { transform: translateX(0px); }
    100% { transform: translateX(-200px); }
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hero section enhancements */
.hero-section-redesign {
    /* Ensure content doesn't get hidden behind header */
    box-sizing: border-box;
}

.hero-content-redesign {
    animation: fadeInUp 1s ease-out;
    /* Ensure content has proper spacing */
    margin-top: auto;
    margin-bottom: auto;
}

.impact-badge {
    animation: fadeInUp 1s ease-out 0.2s both;
}

.hero-title-redesign {
    animation: fadeInUp 1s ease-out 0.4s both;
}

.hero-subtitle-redesign {
    animation: fadeInUp 1s ease-out 0.6s both;
}

.hero-description-redesign {
    animation: fadeInUp 1s ease-out 0.8s both;
}

.cta-buttons-redesign {
    animation: fadeInUp 1s ease-out 0.8s both;
}

.hero-stats {
    animation: fadeInUp 1s ease-out 1s both;
}

.stat-item:hover {
    transform: translateY(-5px) scale(1.02);
    background: rgba(255, 255, 255, 0.18) !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2) !important;
}

/* Enhanced hover effects for hero buttons */
.btn-primary-redesign:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 35px rgba(76, 175, 80, 0.5) !important;
    background: linear-gradient(135deg, #66BB6A, #4CAF50) !important;
}

.btn-secondary-redesign:hover {
    background: rgba(255, 255, 255, 0.25) !important;
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 12px 35px rgba(255,255,255,0.2) !important;
}

/* Original hover effects for other elements */

.mission-card-redesign:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.12) !important;
}

.program-card-redesign:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.1) !important;
}

.program-card-redesign:hover img {
    transform: scale(1.03);
}

.news-card-redesign:hover {
    transform: translateY(-8px);
    box-shadow: 0 25px 60px rgba(0,0,0,0.1) !important;
}

.news-card-redesign:hover img {
    transform: scale(1.03);
}

.testimonial-card-redesign:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 50px rgba(0,0,0,0.1) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section-redesign {
        padding-top: 80px !important;
        padding-bottom: 30px !important;
        min-height: calc(90vh - 15px) !important;
    }
    
    .header-container {
        padding: 8px 15px !important;
        min-height: 60px !important;
    }
    
    .site-title {
        font-size: 1.1rem !important;
    }
    
    .site-tagline {
        font-size: 0.75rem !important;
    }
    
    .main-navigation {
        display: none !important;
    }
    
    .mobile-menu-toggle {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .cta-buttons-redesign {
        flex-direction: column;
        align-items: center;
    }
    
    .hero-stats {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
    
    .impact-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 20px !important;
    }
    
    .mission-grid-redesign {
        grid-template-columns: 1fr !important;
    }
    
    .programs-grid {
        grid-template-columns: 1fr !important;
    }
    
    .news-grid-redesign {
        grid-template-columns: 1fr !important;
    }
    
    .testimonials-grid-redesign {
        grid-template-columns: 1fr !important;
    }
}

@media (max-width: 480px) {
    .hero-section-redesign {
        padding-top: 70px !important;
        padding-bottom: 25px !important;
        min-height: calc(85vh - 10px) !important;
    }
    
    .hero-stats {
        grid-template-columns: 1fr !important;
    }
    
    .impact-grid {
        grid-template-columns: 1fr !important;
    }
    
    .site-logo img {
        width: 35px !important;
        height: 35px !important;
    }
    
    .site-title {
        font-size: 1rem !important;
    }
    
    .site-tagline {
        display: none;
    }
    
    .hero-section-redesign {
        min-height: 80vh !important;
    }
    
    .impact-badge, .registration-badge {
        padding: 8px 15px !important;
        font-size: 0.8rem !important;
        margin-bottom: 10px !important;
    }
}

/* Header fixes for better layout */
@media (min-width: 769px) {
    .mobile-menu-toggle {
        display: none !important;
    }
}

/* Ensure donation button stays visible */
.donate-btn {
    white-space: nowrap;
}

@media (max-width: 1024px) {
    .main-menu {
        gap: 2px !important;
    }
    
    .menu-link {
        padding: 8px 12px !important;
        font-size: 0.85rem !important;
    }
    
    .donate-btn {
        padding: 8px 16px !important;
        font-size: 0.85rem !important;
    }
}
</style>

<!-- Simplified Counter Animation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simple counter animation
    const counters = document.querySelectorAll('[data-count]');
    
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-count');
        let count = 0;
        const duration = 1500; // ms
        const increment = target / (duration / 16); // for 60fps
        
        const updateCount = () => {
            if (count < target) {
                count += increment;
                counter.innerText = Math.ceil(count);
                requestAnimationFrame(updateCount);
            } else {
                counter.innerText = target;
            }
        };
        
        // Only start animation when element is in viewport
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                updateCount();
                observer.disconnect();
            }
        });
        
        observer.observe(counter);
    });
});
</script>

<?php get_footer(); ?>


