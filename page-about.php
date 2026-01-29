<?php
/**
 * Template Name: About Us Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header();
$kilismile_start_year = 2024;
$kilismile_years_of_service = max(0, (int) date('Y') - $kilismile_start_year);
?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="about-hero" style="background: var(--light-gray); color: var(--dark-green); padding: 140px 0 80px; text-align: center; border-bottom: 2px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: clamp(2rem, 4vw, 2.8rem); margin-bottom: 20px; color: var(--dark-green); font-weight: 600;">
                <?php _e('About Kilismile Organization', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1rem; max-width: 800px; margin: 0 auto 30px; color: var(--text-secondary); line-height: 1.5;">
                <?php _e('Dedicated to improving health education and community well-being in Tanzania through innovative programs, compassionate care, and sustainable development initiatives.', 'kilismile'); ?>
            </p>
            <div class="hero-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 20px; max-width: 800px; margin: 20px auto 0;">
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">
                        <?php echo esc_html($kilismile_years_of_service . '+'); ?>
                    </div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Years of Service', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">1000+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Lives Impacted', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">25+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Programs', 'kilismile'); ?></div>
                </div>
                <div class="stat-item" style="text-align: center; background: white; padding: 15px; border-radius: 4px; box-shadow: 0 3px 10px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                    <div style="font-size: 1.8rem; font-weight: 600; margin-bottom: 5px; color: var(--primary-green);">0+</div>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;"><?php _e('Community Partners', 'kilismile'); ?></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Preamble, Mission, Vision, Objectives Section -->
    <section class="mission-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <div style="text-align: center; margin-bottom: 35px;">
                <h2 style="color: var(--dark-green); font-size: 1.75rem; margin-bottom: 10px; font-weight: 600;">Our Purpose & Direction</h2>
                <p style="color: var(--text-secondary); max-width: 760px; margin: 0 auto; line-height: 1.5; font-size: 0.95rem;">
                    The statements below guide every decision, outreach program, and partnership we build.
                </p>
            </div>

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

    <!-- Our Story Section -->
    <section class="story-section" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; align-items: center;">
                <div class="story-content">
                    <h2 style="color: var(--dark-green); font-size: 1.75rem; margin-bottom: 15px; position: relative; padding-bottom: 10px;">
                        <?php _e('About Us', 'kilismile'); ?>
                        <span style="display: block; width: 60px; height: 3px; background: var(--primary-green); position: absolute; bottom: 0; left: 0;"></span>
                    </h2>
                    <div style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem;">
                        <p style="margin-bottom: 15px;">
                            <?php _e('Kilismile is a Non-Governmental Organization based in Moshi, Kilimanjaro Region, officially registered under the Non-Governmental Organizations Act with NGO registration number 07NGO/R/6067. We are dedicated to improving oral health and addressing non-communicable diseases (NCDs) among children and the elderly, particularly in underserved and remote communities of Tanzania.', 'kilismile'); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <?php _e('Our journey began with a simple but powerful vision: to close the health education gap that leaves many vulnerable groups at risk of preventable conditions. We recognized the rising burden of poor oral hygiene, dental caries, and undetected NCDs such as diabetes and hypertension—conditions that not only affect quality of life but also place a strain on families and communities.', 'kilismile'); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <?php _e('At Kilismile, we strongly believe that by educating the young ones today, we will have a healthier elderly tomorrow. That is why our programs emphasize early prevention, awareness, and lifelong healthy habits. Through school-based oral health programs, community screenings, teacher and peer leader training, and advocacy initiatives, we empower people with the knowledge and tools to take charge of their health.', 'kilismile'); ?>
                        </p>
                        <p style="margin-bottom: 15px;">
                            <?php _e('What started as small-scale community outreach has grown into a structured organization that reaches children and elderly across Kilimanjaro and beyond. We combine community wisdom with modern healthcare knowledge, ensuring that our programs are not only impactful but also culturally relevant and sustainable.', 'kilismile'); ?>
                        </p>
                        <p>
                            <?php _e('Today, Kilismile continues to expand its reach through partnerships, innovative health education strategies, and community-driven initiatives—all while staying true to our mission of building healthier generations, one smile at a time.', 'kilismile'); ?>
                        </p>
                    </div>
                </div>
                <div class="story-image">
                    <div style="background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 600 400'><rect fill='%23f8f8f8' width='600' height='400'/><circle fill='%234CAF50' cx='300' cy='200' r='100'/><text x='300' y='210' text-anchor='middle' fill='white' font-size='20' font-family='Arial'>Our Journey</text></svg>" 
                             alt="<?php _e('Kilismile Organization Journey', 'kilismile'); ?>" 
                             style="width: 100%; display: block;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="timeline-section" style="padding: 60px 0; background: white;">
        <div class="container">
            <div class="milestones-section" style="text-align: center; background: white; padding: 30px 20px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid rgba(76, 175, 80, 0.1);">
                <h2 style="font-size: 1.75rem; margin-bottom: 30px; color: var(--dark-green); font-weight: 600; position: relative; padding-bottom: 10px; display: inline-block;">
                    <?php _e('Our Journey Since April 2024', 'kilismile'); ?>
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
    </section>

    <!-- Leadership & Team Section -->
    <section class="leadership-section" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 1.75rem; margin-bottom: 15px; position: relative; padding-bottom: 10px; display: inline-block;">
                <?php _e('Our Leadership & Team', 'kilismile'); ?>
                <span style="display: block; width: 60px; height: 3px; background: var(--primary-green); position: absolute; bottom: 0; left: 50%; transform: translateX(-50%);"></span>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 0.95rem; max-width: 700px; margin: 0 auto 30px; line-height: 1.5;">
                <?php _e('Add or remove leaders and team members from the Team posts. We highlight leadership and the wider team in separate carousels.', 'kilismile'); ?>
            </p>

            <div class="team-block">
                <div class="team-block__header">
                    <h3 class="team-block__title"><?php _e('Leadership', 'kilismile'); ?></h3>
                    <div class="team-block__controls" aria-label="Leadership carousel controls">
                        <button class="team-carousel-btn" type="button" data-target="leadership-carousel" data-direction="prev" aria-label="Previous">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button class="team-carousel-btn" type="button" data-target="leadership-carousel" data-direction="next" aria-label="Next">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div id="leadership-carousel" class="team-carousel" role="list">
                    <?php
                    $team_directory = get_option('kilismile_team_directory', array());
                    $leadership_members = array_filter($team_directory, function ($member) {
                        return isset($member['category']) && $member['category'] === 'leadership';
                    });

                    if (!empty($leadership_members)) :
                        foreach ($leadership_members as $member) :
                            $image_url = !empty($member['image_id']) ? wp_get_attachment_image_url($member['image_id'], 'medium_large') : '';
                            ?>
                            <article class="team-card" role="listitem">
                                <?php if ($image_url) : ?>
                                    <div class="team-card__photo">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($member['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php else : ?>
                                    <div class="team-card__photo team-card__photo--placeholder">
                                        <i class="fas fa-user" aria-hidden="true"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="team-card__body">
                                    <h4 class="team-card__name"><?php echo esc_html($member['name']); ?></h4>
                                    <?php if (!empty($member['role'])) : ?>
                                        <div class="team-card__role"><?php echo esc_html($member['role']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($member['bio'])) : ?>
                                        <p class="team-card__bio"><?php echo wp_trim_words(esc_html($member['bio']), 18, '...'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </article>
                            <?php
                        endforeach;
                    else : ?>
                        <article class="team-card" role="listitem">
                            <div class="team-card__photo team-card__photo--placeholder">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </div>
                            <div class="team-card__body">
                                <h4 class="team-card__name"><?php _e('Leadership Member', 'kilismile'); ?></h4>
                                <div class="team-card__role"><?php _e('Role Title', 'kilismile'); ?></div>
                                <p class="team-card__bio"><?php _e('Add leadership profiles from the Team posts to display them here.', 'kilismile'); ?></p>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </div>

            <div class="team-block">
                <div class="team-block__header">
                    <h3 class="team-block__title"><?php _e('Our Team', 'kilismile'); ?></h3>
                    <div class="team-block__controls" aria-label="Team carousel controls">
                        <button class="team-carousel-btn" type="button" data-target="team-carousel" data-direction="prev" aria-label="Previous">
                            <i class="fas fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <button class="team-carousel-btn" type="button" data-target="team-carousel" data-direction="next" aria-label="Next">
                            <i class="fas fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div id="team-carousel" class="team-carousel" role="list">
                    <?php
                    $team_directory = get_option('kilismile_team_directory', array());
                    $team_members = array_filter($team_directory, function ($member) {
                        return !isset($member['category']) || $member['category'] === 'team';
                    });

                    if (!empty($team_members)) :
                        foreach ($team_members as $member) :
                            $image_url = !empty($member['image_id']) ? wp_get_attachment_image_url($member['image_id'], 'medium_large') : '';
                            ?>
                            <article class="team-card" role="listitem">
                                <?php if ($image_url) : ?>
                                    <div class="team-card__photo">
                                        <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($member['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php else : ?>
                                    <div class="team-card__photo team-card__photo--placeholder">
                                        <i class="fas fa-user" aria-hidden="true"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="team-card__body">
                                    <h4 class="team-card__name"><?php echo esc_html($member['name']); ?></h4>
                                    <?php if (!empty($member['role'])) : ?>
                                        <div class="team-card__role"><?php echo esc_html($member['role']); ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($member['bio'])) : ?>
                                        <p class="team-card__bio"><?php echo wp_trim_words(esc_html($member['bio']), 18, '...'); ?></p>
                                    <?php endif; ?>
                                </div>
                            </article>
                            <?php
                        endforeach;
                    else : ?>
                        <article class="team-card" role="listitem">
                            <div class="team-card__photo team-card__photo--placeholder">
                                <i class="fas fa-user" aria-hidden="true"></i>
                            </div>
                            <div class="team-card__body">
                                <h4 class="team-card__name"><?php _e('Team Member', 'kilismile'); ?></h4>
                                <div class="team-card__role"><?php _e('Role Title', 'kilismile'); ?></div>
                                <p class="team-card__bio"><?php _e('Add team profiles from the Team posts to display them here.', 'kilismile'); ?></p>
                            </div>
                        </article>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section class="cta-section" style="padding: 50px 0; background: var(--primary-green); color: white; text-align: center; border-top: 2px solid var(--dark-green);">
        <div class="container">
            <h2 style="font-size: 1.8rem; margin-bottom: 15px; color: white; font-weight: 600;">
                <?php _e('Join Our Mission', 'kilismile'); ?>
            </h2>
            <p style="font-size: 0.95rem; max-width: 600px; margin: 0 auto 25px; color: rgba(255,255,255,0.95); line-height: 1.5;">
                <?php _e('Be part of the positive change in Tanzania. Whether through donations, volunteering, or partnerships, your support makes a difference.', 'kilismile'); ?>
            </p>
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo esc_url(home_url('/donate')); ?>" 
                   class="btn btn-secondary" 
                   style="display: inline-block; padding: 10px 20px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 4px; font-weight: 600; font-size: 0.9rem;">
                    <?php _e('Donate Now', 'kilismile'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/volunteer')); ?>" 
                   class="btn btn-outline" 
                   style="display: inline-block; padding: 10px 20px; background: transparent; color: white; text-decoration: none; border: 1px solid white; border-radius: 4px; font-weight: 600; font-size: 0.9rem;">
                    <?php _e('Volunteer', 'kilismile'); ?>
                </a>
                <a href="<?php echo esc_url(home_url('/contact')); ?>" 
                   class="btn btn-outline" 
                   style="display: inline-block; padding: 10px 20px; background: transparent; color: white; text-decoration: none; border: 1px solid white; border-radius: 4px; font-weight: 600; font-size: 0.9rem;">
                    <?php _e('Partner With Us', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<style>
    @media (max-width: 768px) {
        .story-section > .container > div {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        
        .timeline-item .timeline-content {
            width: calc(100% - 30px) !important;
            margin: 0 15px !important;
        }
        
        .timeline {
            padding: 0 10px;
        }
        
        .hero-stats {
            gap: 15px;
        }
        
        .hero-stats .stat-item {
            min-width: 120px;
        }
        
        .about-hero {
            padding-top: 120px !important; /* Ensure title is visible on mobile */
        }
    }

    .team-block {
        margin-top: 30px;
    }

    .team-block + .team-block {
        margin-top: 40px;
    }

    .team-block__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        gap: 12px;
    }

    .team-block__title {
        margin: 0;
        color: var(--dark-green);
        font-size: 1.2rem;
        font-weight: 600;
    }

    .team-block__controls {
        display: flex;
        gap: 8px;
    }

    .team-carousel-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid rgba(76, 175, 80, 0.3);
        background: #fff;
        color: var(--primary-green);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .team-carousel {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: calc((100% - 60px) / 4);
        gap: 20px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        padding-bottom: 10px;
        scrollbar-width: thin;
    }

    .team-carousel::-webkit-scrollbar {
        height: 8px;
    }

    .team-carousel::-webkit-scrollbar-thumb {
        background: rgba(76, 175, 80, 0.3);
        border-radius: 999px;
    }

    .team-card {
        background: #fff;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid rgba(76, 175, 80, 0.1);
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        scroll-snap-align: start;
        display: flex;
        flex-direction: column;
    }

    .team-card__photo {
        height: 200px;
        overflow: hidden;
    }

    .team-card__photo--placeholder {
        background: var(--light-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--medium-gray);
        font-size: 2.5rem;
    }

    .team-card__body {
        padding: 15px;
    }

    .team-card__name {
        margin: 0 0 6px;
        color: var(--dark-green);
        font-size: 1.05rem;
        font-weight: 600;
    }

    .team-card__role {
        color: var(--primary-green);
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .team-card__bio {
        color: var(--text-secondary);
        line-height: 1.5;
        font-size: 0.85rem;
        margin: 0;
    }

    @media (max-width: 1200px) {
        .team-carousel {
            grid-auto-columns: calc((100% - 40px) / 3);
        }
    }

    @media (max-width: 960px) {
        .team-carousel {
            grid-auto-columns: calc((100% - 20px) / 2);
        }
    }

    @media (max-width: 640px) {
        .team-carousel {
            grid-auto-columns: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.team-carousel-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = this.getAttribute('data-target');
                var direction = this.getAttribute('data-direction');
                var carousel = document.getElementById(targetId);
                if (!carousel) {
                    return;
                }
                var scrollAmount = carousel.clientWidth;
                carousel.scrollBy({
                    left: direction === 'next' ? scrollAmount : -scrollAmount,
                    behavior: 'smooth'
                });
            });
        });
    });
</script>

<?php get_footer(); ?>


