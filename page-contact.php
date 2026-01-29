<?php
/**
 * Template Name: Contact Us Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header();

$email = (string) get_theme_mod('contact_details_email', get_theme_mod('kilismile_email', 'kilismile21@gmail.com'));
$phone = (string) get_theme_mod('contact_details_phone', get_theme_mod('kilismile_phone', '+255763495575'));
$phone_href = preg_replace('/[^0-9+]/', '', $phone);
$address = (string) get_theme_mod('contact_details_address', get_theme_mod('kilismile_address', 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania'));
?>

<main id="main" class="site-main contact-page">
    <section class="contact-hero">
        <div class="contact-container">
            <p class="contact-hero__eyebrow"><?php echo esc_html__('Contact', 'kilismile'); ?></p>
            <h1 class="contact-hero__title"><?php echo esc_html__('Get in Touch', 'kilismile'); ?></h1>
            <p class="contact-hero__desc"><?php echo esc_html__('We respond within 24 hours. Share your questions, ideas, or partnership requests and our team will help.', 'kilismile'); ?></p>
            <div class="contact-hero__actions">
                <a class="contact-btn contact-btn--primary" href="#contact-form">
                    <i class="fas fa-paper-plane" aria-hidden="true"></i>
                    <span><?php echo esc_html__('Send a Message', 'kilismile'); ?></span>
                </a>
                <?php if (!empty($phone_href)) : ?>
                    <a class="contact-btn contact-btn--ghost" href="tel:<?php echo esc_attr($phone_href); ?>">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                        <span><?php echo esc_html__('Call Us', 'kilismile'); ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="contact-details">
        <div class="contact-container">
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-card__icon"><i class="fas fa-envelope" aria-hidden="true"></i></div>
                    <h3 class="contact-card__title"><?php echo esc_html__('Email', 'kilismile'); ?></h3>
                    <p class="contact-card__text"><?php echo esc_html__('We reply within 24 hours.', 'kilismile'); ?></p>
                    <a class="contact-card__link" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                </div>
                <div class="contact-card">
                    <div class="contact-card__icon"><i class="fas fa-phone" aria-hidden="true"></i></div>
                    <h3 class="contact-card__title"><?php echo esc_html__('Phone', 'kilismile'); ?></h3>
                    <p class="contact-card__text"><?php echo esc_html__('Available weekdays, 8AM–5PM.', 'kilismile'); ?></p>
                    <?php if (!empty($phone_href)) : ?>
                        <a class="contact-card__link" href="tel:<?php echo esc_attr($phone_href); ?>"><?php echo esc_html($phone); ?></a>
                    <?php endif; ?>
                </div>
                <div class="contact-card">
                    <div class="contact-card__icon"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></div>
                    <h3 class="contact-card__title"><?php echo esc_html__('Office', 'kilismile'); ?></h3>
                    <p class="contact-card__text"><?php echo esc_html__('Visit or send mail.', 'kilismile'); ?></p>
                    <span class="contact-card__meta"><?php echo nl2br(esc_html($address)); ?></span>
                </div>
            </div>
        </div>
    </section>

    <section id="contact-form" class="contact-form">
        <div class="contact-container">
            <div class="contact-form__header">
                <h2><?php echo esc_html__('Send us a message', 'kilismile'); ?></h2>
                <p><?php echo esc_html__('Fill out the form below and we will respond as soon as possible.', 'kilismile'); ?></p>
            </div>
            <?php echo do_shortcode('[kilismile_contact_form columns="2" show_info="yes"]'); ?>
        </div>
    </section>
</main>

<style>
    .contact-page { background: #ffffff; }

    .contact-container {
        width: min(1200px, calc(100% - 40px));
        margin: 0 auto;
    }

    .contact-hero {
        background: var(--dark-green);
        color: #ffffff;
        padding: 96px 0 64px;
        text-align: center;
    }

    .contact-hero__eyebrow {
        font-size: 0.85rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        opacity: 0.8;
        margin-bottom: 12px;
    }

    .contact-hero__title {
        font-size: clamp(2.2rem, 4vw, 3.2rem);
        margin-bottom: 16px;
        font-weight: 700;
    }

    .contact-hero__desc {
        max-width: 640px;
        margin: 0 auto 28px;
        line-height: 1.7;
        color: rgba(255, 255, 255, 0.9);
    }

    .contact-hero__actions {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .contact-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
    }

    .contact-btn--primary {
        background: #ffffff;
        color: var(--dark-green);
    }

    .contact-btn--ghost {
        border-color: rgba(255, 255, 255, 0.4);
        color: #ffffff;
        background: transparent;
    }

    .contact-details {
        padding: 64px 0;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
    }

    .contact-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 24px;
        text-align: center;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
    }

    .contact-card__icon {
        width: 52px;
        height: 52px;
        margin: 0 auto 16px;
        border-radius: 12px;
        background: var(--primary-green);
        display: grid;
        place-items: center;
        color: #ffffff;
        font-size: 1.2rem;
    }

    .contact-card__title {
        font-size: 1.1rem;
        color: var(--dark-green);
        margin-bottom: 8px;
    }

    .contact-card__text {
        color: var(--text-secondary);
        margin-bottom: 12px;
        font-size: 0.95rem;
    }

    .contact-card__link,
    .contact-card__meta {
        color: var(--primary-green);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.95rem;
        display: inline-block;
        line-height: 1.4;
    }

    .contact-form {
        background: var(--light-gray);
        padding: 72px 0 80px;
    }

    .contact-form__header {
        text-align: center;
        margin-bottom: 32px;
    }

    .contact-form__header h2 {
        color: var(--dark-green);
        margin-bottom: 10px;
    }

    .contact-form__header p {
        color: var(--text-secondary);
        max-width: 640px;
        margin: 0 auto;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .contact-hero {
            padding: 72px 0 48px;
        }

        .contact-form {
            padding: 56px 0 64px;
        }
    }
</style>

<?php get_footer(); return; ?>
    <!-- Hero Section -->
        <section class="contact-hero" style="
        background: var(--dark-green);
        color: white;
        padding: 120px 0 70px;
        text-align: center;
        position: relative;
        overflow: hidden;
    ">
        <!-- Background Pattern -->
        <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.05; background-image: 
            radial-gradient(circle at 25% 25%, rgba(255,255,255,0.2) 1px, transparent 1px),
            radial-gradient(circle at 75% 75%, rgba(255,255,255,0.2) 1px, transparent 1px);
            background-size: 50px 50px; 
            z-index: 1;"></div>
            
        <div class="container" style="position: relative; z-index: 2;">
            <h1 style="font-size: clamp(2.4rem, 4.5vw, 3.2rem); margin-bottom: 16px; font-weight: 700; color: white; letter-spacing: -0.02em;">
                <?php echo esc_html(get_theme_mod('contact_hero_title', 'Get In Touch')); ?>
            </h1>
            <p style="font-size: clamp(1.05rem, 1.8vw, 1.2rem); max-width: 720px; margin: 0 auto 26px; color: rgba(255,255,255,0.92); line-height: 1.7;">
                <?php echo esc_html(get_theme_mod('contact_hero_description', 'Ready to make a difference? We\'d love to hear from you. Whether you want to volunteer, partner with us, or learn more about our programs, we\'re here to help.')); ?>
            </p>
            
            <!-- Quick Action Buttons -->
            <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; margin-top: 26px;">
                <a href="#contact-form" style="background: white; color: var(--dark-green); padding: 12px 22px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-envelope"></i>
                    <?php _e('Send Message', 'kilismile'); ?>
                </a>
                <a href="tel:+<?php echo esc_attr(str_replace(array(' ', '-', '(', ')'), '', get_theme_mod('quick_contact_phone', '+255763495575'))); ?>" style="background: transparent; color: white; padding: 12px 22px; border-radius: 10px; text-decoration: none; font-weight: 600; border: 1px solid rgba(255,255,255,0.35); transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-phone"></i>
                    <?php _e('Call Now', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Information Cards -->
    <section class="contact-info" style="padding: 72px 0; background: white;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 50px;">
                <h2 style="font-size: clamp(1.8rem, 3vw, 2.2rem); margin-bottom: 15px; color: var(--dark-green); font-weight: 600;">
                    <?php _e('How to Reach Us', 'kilismile'); ?>
                </h2>
                <p style="font-size: 1rem; color: var(--text-secondary); max-width: 700px; margin: 0 auto; line-height: 1.5;">
                    <?php _e('Multiple ways to connect with our team. Choose the method that works best for you.', 'kilismile'); ?>
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 48px; align-items: stretch;">
                <!-- Main Office -->
                <div class="contact-card" style="background: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 6px 16px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid var(--border-color); position: relative; overflow: hidden; display: flex; flex-direction: column; min-height: 210px;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--primary-green);"></div>
                    <div class="icon" style="width: 52px; height: 52px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 1.2rem;">
                        <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1rem; font-weight: 600;">
                        <?php _e(get_theme_mod('contact_office_title', 'Visit Office'), 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.85rem; flex-grow: 1;">
                        <?php echo wp_kses_post(get_theme_mod('contact_office_content', 'Moshi, Kilimanjaro<br>Mon-Fri, 8AM-5PM')); ?>
                    </p>
                    <a href="<?php echo esc_url(get_theme_mod('contact_office_link', '#map-section')); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: all 0.3s ease; font-size: 0.85rem; margin-top: auto;">
                        <?php _e(get_theme_mod('contact_office_link_text', 'Location'), 'kilismile'); ?> 
                        <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i>
                    </a>
                </div>

                <!-- Phone -->
                <div class="contact-card" style="background: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 6px 16px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid var(--border-color); position: relative; overflow: hidden; display: flex; flex-direction: column; min-height: 210px;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--primary-green);"></div>
                    <div class="icon" style="width: 52px; height: 52px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 1.2rem;">
                        <i class="fas fa-phone" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1rem; font-weight: 600;">
                        <?php _e(get_theme_mod('contact_phone_title', 'Call Us'), 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.85rem; flex-grow: 1;">
                        <?php echo wp_kses_post(get_theme_mod('contact_phone_content', '+255 763 495 575<br>24/7 for emergencies')); ?>
                    </p>
                    <a href="<?php echo esc_url(get_theme_mod('contact_phone_link', 'tel:+255763495575')); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: all 0.3s ease; font-size: 0.85rem; margin-top: auto;">
                        <?php _e(get_theme_mod('contact_phone_link_text', 'Call Now'), 'kilismile'); ?> 
                        <i class="fas fa-phone" style="font-size: 0.7rem;"></i>
                    </a>
                </div>

                <!-- Email -->
                <div class="contact-card" style="background: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 6px 16px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid var(--border-color); position: relative; overflow: hidden; display: flex; flex-direction: column; min-height: 210px;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--primary-green);"></div>
                    <div class="icon" style="width: 52px; height: 52px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 1.2rem;">
                        <i class="fas fa-envelope" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1rem; font-weight: 600;">
                        <?php _e(get_theme_mod('contact_email_title', 'Email Us'), 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.85rem; flex-grow: 1;">
                        <?php echo wp_kses_post(get_theme_mod('contact_email_content', 'kilismile21@gmail.com<br>Response within 24hrs')); ?>
                    </p>
                    <a href="<?php echo esc_url(get_theme_mod('contact_email_link', 'mailto:kilismile21@gmail.com')); ?>" style="color: var(--primary-green); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 5px; transition: all 0.3s ease; font-size: 0.85rem; margin-top: auto;">
                        <?php _e(get_theme_mod('contact_email_link_text', 'Send Email'), 'kilismile'); ?> 
                        <i class="fas fa-envelope" style="font-size: 0.7rem;"></i>
                    </a>
                </div>

                <!-- Social Media -->
                <div class="contact-card" style="background: white; padding: 24px; border-radius: 14px; text-align: center; box-shadow: 0 6px 16px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 1px solid var(--border-color); position: relative; overflow: hidden; display: flex; flex-direction: column; min-height: 210px;">
                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: var(--primary-green);"></div>
                    <div class="icon" style="width: 52px; height: 52px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: white; font-size: 1.2rem;">
                        <i class="fas fa-share-alt" aria-hidden="true"></i>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 10px; font-size: 1rem; font-weight: 600;">
                        <?php _e(get_theme_mod('contact_social_title', 'Follow Us'), 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.5; margin-bottom: 15px; font-size: 0.85rem; flex-grow: 1;">
                        <?php echo wp_kses_post(get_theme_mod('contact_social_content', 'Latest news & updates<br>on social media')); ?>
                    </p>
                    <div style="display: flex; justify-content: center; gap: 8px; margin-top: auto;">
                        <?php if (get_theme_mod('contact_facebook_url')): ?>
                            <a href="<?php echo esc_url(get_theme_mod('contact_facebook_url')); ?>" target="_blank" style="color: var(--primary-green); font-size: 1rem; transition: all 0.3s ease;">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (get_theme_mod('contact_twitter_url')): ?>
                            <a href="<?php echo esc_url(get_theme_mod('contact_twitter_url')); ?>" target="_blank" style="color: var(--primary-green); font-size: 1rem; transition: all 0.3s ease;">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (get_theme_mod('contact_instagram_url')): ?>
                            <a href="<?php echo esc_url(get_theme_mod('contact_instagram_url')); ?>" target="_blank" style="color: var(--primary-green); font-size: 1rem; transition: all 0.3s ease;">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (get_theme_mod('contact_linkedin_url')): ?>
                            <a href="<?php echo esc_url(get_theme_mod('contact_linkedin_url')); ?>" target="_blank" style="color: var(--primary-green); font-size: 1rem; transition: all 0.3s ease;">
                                <i class="fab fa-linkedin"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form and Map Section -->
    <section id="contact-form" class="contact-form-section" style="padding: 72px 0; background: var(--light-gray);">
        <div class="container">
            <div style="display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr); gap: 40px; align-items: start;">
                <!-- Contact Form -->
                <div class="contact-form-container">
                    <div style="background: white; padding: 36px; border-radius: 14px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); border: 1px solid var(--border-color);">
                        <div style="text-align: center; margin-bottom: 30px;">
                            <div style="width: 56px; height: 56px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; color: white; font-size: 1.4rem;">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <h2 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 10px; font-weight: 600;">
                                <?php _e('Send Us a Message', 'kilismile'); ?>
                            </h2>
                            <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.95rem;">
                                <?php _e('Ready to make a difference? Fill out the form below and we\'ll get back to you within 24 hours.', 'kilismile'); ?>
                            </p>
                        </div>

                        <form id="contact-form-element" method="post" action="">
                            <?php wp_nonce_field('kilismile_contact_nonce', 'contact_nonce'); ?>
                            <input type="hidden" name="submit_contact" value="1">

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div class="form-group">
                                    <label for="contact_name" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                        <?php _e('Full Name', 'kilismile'); ?> <span style="color: var(--primary-green);">*</span>
                                    </label>
                                    <input type="text" 
                                           id="contact_name" 
                                           name="contact_name" 
                                           required 
                                           style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fafffe;"
                                           aria-describedby="contact_name_error">
                                    <div id="contact_name_error" class="error-message" style="color: #d32f2f; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                                </div>

                                <div class="form-group">
                                    <label for="contact_email" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                        <?php _e('Email Address', 'kilismile'); ?> <span style="color: var(--primary-green);">*</span>
                                    </label>
                                    <input type="email" 
                                           id="contact_email" 
                                           name="contact_email" 
                                           required 
                                           style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fafffe;"
                                           aria-describedby="contact_email_error">
                                    <div id="contact_email_error" class="error-message" style="color: #d32f2f; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div class="form-group">
                                    <label for="contact_phone" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                        <?php _e('Phone Number', 'kilismile'); ?>
                                    </label>
                                    <input type="tel" 
                                           id="contact_phone" 
                                           name="contact_phone" 
                                           style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fafffe;">
                                </div>

                                <div class="form-group">
                                    <label for="contact_organization" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                        <?php _e('Organization (Optional)', 'kilismile'); ?>
                                    </label>
                                    <input type="text" 
                                           id="contact_organization" 
                                           name="contact_organization" 
                                           style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fafffe;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                <div class="form-group">
                                    <label for="contact_interest" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                        <?php _e('Interest Area', 'kilismile'); ?>
                                    </label>
                                    <select id="contact_interest" 
                                            name="contact_interest" 
                                            style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fafffe;">
                                        <option value=""><?php _e('Select an option...', 'kilismile'); ?></option>
                                        <option value="volunteer"><?php _e('Volunteering Opportunities', 'kilismile'); ?></option>
                                        <option value="partnership"><?php _e('Partnership & Collaboration', 'kilismile'); ?></option>
                                        <option value="donation"><?php _e('Donations & Fundraising', 'kilismile'); ?></option>
                                        <option value="programs"><?php _e('Our Programs & Services', 'kilismile'); ?></option>
                                        <option value="media"><?php _e('Media & Press Inquiries', 'kilismile'); ?></option>
                                        <option value="general"><?php _e('General Information', 'kilismile'); ?></option>
                                        <option value="other"><?php _e('Other', 'kilismile'); ?></option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="contact_subject" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                        <?php _e('Subject', 'kilismile'); ?> <span style="color: var(--primary-green);">*</span>
                                    </label>
                                    <input type="text" 
                                           id="contact_subject" 
                                           name="contact_subject" 
                                           required 
                                           style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; transition: all 0.3s ease; background: #fafffe;"
                                           aria-describedby="contact_subject_error">
                                    <div id="contact_subject_error" class="error-message" style="color: #d32f2f; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 25px;">
                                <label for="contact_message" style="display: block; margin-bottom: 8px; font-weight: 600; color: var(--dark-green); font-size: 0.9rem;">
                                    <?php _e('Your Message', 'kilismile'); ?> <span style="color: var(--primary-green);">*</span>
                                </label>
                                <textarea id="contact_message" 
                                          name="contact_message" 
                                          rows="6" 
                                          required 
                                          style="width: 100%; padding: 12px 15px; border: 2px solid #e8f5e8; border-radius: 8px; font-size: 1rem; resize: vertical; min-height: 120px; transition: all 0.3s ease; background: #fafffe;"
                                          placeholder="<?php _e('Please share your message, questions, or how we can help you...', 'kilismile'); ?>"
                                          aria-describedby="contact_message_error"></textarea>
                                <div id="contact_message_error" class="error-message" style="color: #d32f2f; font-size: 0.85rem; margin-top: 5px; display: none;"></div>
                            </div>

                            <div class="form-group consent-group" style="margin-bottom: 25px;">
                                <label class="checkbox-label" style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; font-size: 0.9rem; line-height: 1.4;">
                                    <input type="checkbox" name="contact_consent" value="yes" required style="margin-top: 2px;">
                                    <span><?php _e('I agree to the privacy policy and consent to being contacted by Kilismile Organization regarding my inquiry.', 'kilismile'); ?></span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" 
                                    name="submit_contact"
                                    class="contact-submit-btn" 
                                    style="width: 100%; padding: 15px; background: var(--primary-green); color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 10px; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                                <span><?php _e('Send Message', 'kilismile'); ?></span>
                                <i class="fas fa-paper-plane" aria-hidden="true"></i>
                            </button>

                            <!-- Form Status Messages -->
                            <?php if (isset($_GET['contact_success'])) : ?>
                                <div id="form-success" class="form-message" style="margin-top: 20px; padding: 15px; background: #e8f5e9; color: var(--dark-green); border-radius: 8px; border-left: 4px solid var(--primary-green);">
                                    <i class="fas fa-check-circle" style="margin-right: 10px; color: var(--primary-green);" aria-hidden="true"></i>
                                    <?php _e('Thank you! Your message has been sent successfully. We\'ll get back to you within 24 hours.', 'kilismile'); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['contact_error'])) : ?>
                                <div id="form-error" class="form-message" style="margin-top: 20px; padding: 15px; background: #ffebee; color: #d32f2f; border-radius: 8px; border-left: 4px solid #d32f2f;">
                                    <i class="fas fa-exclamation-circle" style="margin-right: 10px;" aria-hidden="true"></i>
                                    <?php 
                                    switch ($_GET['contact_error']) {
                                        case 'missing_fields':
                                            _e('Please fill in all required fields.', 'kilismile');
                                            break;
                                        case 'invalid_email':
                                            _e('Please enter a valid email address.', 'kilismile');
                                            break;
                                        case 'email_failed':
                                            _e('There was an error sending your message. Please try again.', 'kilismile');
                                            break;
                                        default:
                                            _e('An error occurred. Please try again.', 'kilismile');
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Map and Quick Contact -->
                <div class="map-container">
                    <div id="map-section" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid rgba(76, 175, 80, 0.1); margin-bottom: 25px;">
                        <div style="text-align: center; margin-bottom: 25px;">
                            <div style="width: 50px; height: 50px; background: var(--primary-green); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: white; font-size: 1.2rem;">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h3 style="color: var(--dark-green); font-size: 1.4rem; margin-bottom: 8px; font-weight: 600;">
                                <?php _e('Find Our Office', 'kilismile'); ?>
                            </h3>
                            <p style="color: var(--text-secondary); line-height: 1.6; font-size: 0.9rem;">
                                <?php _e('Located in the heart of Moshi, easily accessible by public transport and private vehicle.', 'kilismile'); ?>
                            </p>
                        </div>

                        <!-- Interactive Map Placeholder -->
                        <div class="map-placeholder" style="background: linear-gradient(135deg, #e8f5e8, #f1f8e9); height: 250px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; position: relative; overflow: hidden; border: 2px solid rgba(76, 175, 80, 0.1);">
                            <div style="text-align: center; color: var(--dark-green);">
                                <i class="fas fa-map-marked-alt" style="font-size: 2.5rem; margin-bottom: 10px; color: var(--primary-green);" aria-hidden="true"></i>
                                <p style="margin: 0; font-size: 1rem; font-weight: 600;"><?php _e('Interactive Map Coming Soon', 'kilismile'); ?></p>
                                <p style="margin: 5px 0 0 0; font-size: 0.85rem; opacity: 0.8;"><?php _e('Moshi, Kilimanjaro, Tanzania', 'kilismile'); ?></p>
                            </div>
                        </div>

                        <!-- Location Details -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; text-align: center;">
                            <div style="background: var(--light-gray); padding: 15px; border-radius: 8px;">
                                <i class="fas fa-car" style="color: var(--primary-green); font-size: 1.2rem; margin-bottom: 8px;"></i>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Parking Available', 'kilismile'); ?></div>
                            </div>
                            <div style="background: var(--light-gray); padding: 15px; border-radius: 8px;">
                                <i class="fas fa-bus" style="color: var(--primary-green); font-size: 1.2rem; margin-bottom: 8px;"></i>
                                <div style="font-size: 0.85rem; color: var(--text-secondary);"><?php _e('Public Transport', 'kilismile'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Contact Card -->
                    <div style="background: var(--primary-green); padding: 30px; border-radius: 15px; color: white; text-align: center; box-shadow: 0 10px 30px rgba(76, 175, 80, 0.3);">
                        <div style="margin-bottom: 20px;">
                            <i class="fas fa-phone-alt" style="font-size: 2rem; margin-bottom: 10px;"></i>
                            <h4 style="color: white; margin-bottom: 8px; font-size: 1.2rem; font-weight: 600;">
                                <?php _e('Need Immediate Help?', 'kilismile'); ?>
                            </h4>
                            <p style="color: rgba(255,255,255,0.9); font-size: 0.9rem; line-height: 1.5;">
                                <?php _e('Our team is available for urgent inquiries and emergency support.', 'kilismile'); ?>
                            </p>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <a href="tel:+<?php echo esc_attr(str_replace(array(' ', '-', '(', ')'), '', get_theme_mod('quick_contact_phone', '+255763495575'))); ?>" style="display: block; background: rgba(255,255,255,0.2); color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-bottom: 10px; transition: all 0.3s ease;">
                                <i class="fas fa-phone" style="margin-right: 8px;"></i>
                                <?php echo esc_html(get_theme_mod('quick_contact_phone', '+255 763 495 575')); ?>
                            </a>
                            <a href="mailto:<?php echo esc_attr(get_theme_mod('quick_contact_email', 'kilismile21@gmail.com')); ?>" style="display: block; background: rgba(255,255,255,0.2); color: white; padding: 12px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                                <i class="fas fa-envelope" style="margin-right: 8px;"></i>
                                <?php echo esc_html(get_theme_mod('quick_contact_email', 'kilismile21@gmail.com')); ?>
                            </a>
                        </div>
                        
                        <div style="border-top: 1px solid rgba(255,255,255,0.3); padding-top: 20px;">
                            <div style="font-size: 0.85rem; color: rgba(255,255,255,0.8); margin-bottom: 10px;">
                                <?php _e('Emergency Hours:', 'kilismile'); ?>
                            </div>
                            <div style="font-weight: 600; font-size: 0.9rem;">
                                <?php _e('24/7 Available', 'kilismile'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" style="padding: 80px 0; background: var(--light-gray);">
        <div class="container">
            <h2 style="text-align: center; color: var(--dark-green); font-size: 2.5rem; margin-bottom: 20px;">
                <?php _e('Frequently Asked Questions', 'kilismile'); ?>
            </h2>
            <p style="text-align: center; color: var(--text-secondary); font-size: 1.1rem; max-width: 600px; margin: 0 auto 60px; line-height: 1.6;">
                <?php _e('Quick answers to common questions about our organization and services.', 'kilismile'); ?>
            </p>

            <div style="max-width: 800px; margin: 0 auto;">
                <!-- FAQ Items -->
                <div class="faq-item" style="background: white; margin-bottom: 15px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <button class="faq-question" onclick="toggleFAQ(this)" style="width: 100%; padding: 25px; text-align: left; background: none; border: none; font-size: 1.1rem; font-weight: 600; color: var(--dark-green); cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <?php _e('How can I get involved with Kilismile Organization?', 'kilismile'); ?>
                        <i class="fas fa-chevron-down" style="transition: transform 0.3s ease;" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer" style="padding: 0 25px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                        <div style="padding-bottom: 25px; color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('There are many ways to get involved! You can volunteer your time, make a donation, participate in our events, or become a community partner. Visit our volunteer page or contact us directly to learn about current opportunities.', 'kilismile'); ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item" style="background: white; margin-bottom: 15px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <button class="faq-question" onclick="toggleFAQ(this)" style="width: 100%; padding: 25px; text-align: left; background: none; border: none; font-size: 1.1rem; font-weight: 600; color: var(--dark-green); cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <?php _e('What areas do your programs cover?', 'kilismile'); ?>
                        <i class="fas fa-chevron-down" style="transition: transform 0.3s ease;" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer" style="padding: 0 25px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                        <div style="padding-bottom: 25px; color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('Our programs primarily serve communities in the Kilimanjaro region, with our main focus in Moshi and surrounding rural areas. We also have satellite programs in Arusha and Dodoma regions.', 'kilismile'); ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item" style="background: white; margin-bottom: 15px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <button class="faq-question" onclick="toggleFAQ(this)" style="width: 100%; padding: 25px; text-align: left; background: none; border: none; font-size: 1.1rem; font-weight: 600; color: var(--dark-green); cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <?php _e('How are donations used?', 'kilismile'); ?>
                        <i class="fas fa-chevron-down" style="transition: transform 0.3s ease;" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer" style="padding: 0 25px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                        <div style="padding-bottom: 25px; color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('We maintain full transparency in our financial operations. 85% of donations go directly to programs, 10% to administrative costs, and 5% to fundraising activities. You can view our annual financial reports on our transparency page.', 'kilismile'); ?>
                        </div>
                    </div>
                </div>

                <div class="faq-item" style="background: white; margin-bottom: 15px; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <button class="faq-question" onclick="toggleFAQ(this)" style="width: 100%; padding: 25px; text-align: left; background: none; border: none; font-size: 1.1rem; font-weight: 600; color: var(--dark-green); cursor: pointer; display: flex; justify-content: space-between; align-items: center;">
                        <?php _e('Do you offer internship opportunities?', 'kilismile'); ?>
                        <i class="fas fa-chevron-down" style="transition: transform 0.3s ease;" aria-hidden="true"></i>
                    </button>
                    <div class="faq-answer" style="padding: 0 25px; max-height: 0; overflow: hidden; transition: all 0.3s ease;">
                        <div style="padding-bottom: 25px; color: var(--text-secondary); line-height: 1.6;">
                            <?php _e('Yes! We offer internship programs for students in public health, social work, medicine, and related fields. Internships are available year-round with flexible durations. Contact our programs team for more information.', 'kilismile'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Enhanced Email System Integration -->
    <section class="email-system-forms" style="background: #f8f9fa; padding: 80px 0;">
        <div class="container" style="max-width: 1320px; margin: 0 auto; padding: 0 24px;">
            
            <!-- Form Selection Tabs -->
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="color: var(--dark-green); font-size: 2.5rem; margin-bottom: 15px; font-weight: 700;">
                    More Ways to Connect
                </h2>
                <p style="color: var(--text-secondary); font-size: 1.1rem; margin-bottom: 40px;">
                    Choose the form that best fits your needs
                </p>
                
                <!-- Tab Navigation -->
                <div class="form-tabs" style="display: flex; justify-content: center; gap: 10px; margin-bottom: 40px; flex-wrap: wrap;">
                    <button class="tab-btn active" data-tab="volunteer" 
                            style="background: var(--primary-green); color: white; border: none; padding: 15px 30px; border-radius: 30px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);">
                        Volunteer Application
                    </button>
                    <button class="tab-btn" data-tab="newsletter" 
                            style="background: white; color: var(--primary-green); border: 2px solid var(--primary-green); padding: 15px 30px; border-radius: 30px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        Newsletter Signup
                    </button>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content" style="max-width: 800px; margin: 0 auto;">

                <!-- Volunteer Application Form -->
                <div id="volunteer-tab" class="tab-content active" style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.1);">
                    <h3 style="color: var(--dark-green); margin-bottom: 30px; font-size: 1.8rem; font-weight: 600; text-align: center;">
                        Apply to volunteer
                    </h3>
                    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 1.1rem;">
                        Join our mission to improve oral health in underserved communities
                    </p>
                    <?php echo kilismile_volunteer_form(array(
                        'class' => 'kilismile-volunteer-form enhanced-form',
                        'submit_text' => 'Submit Application'
                    )); ?>
                </div>

                <!-- Newsletter Signup Form -->
                <div id="newsletter-tab" class="tab-content" style="background: white; padding: 50px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.1); display: none;">
                    <h3 style="color: var(--dark-green); margin-bottom: 30px; font-size: 1.8rem; font-weight: 600; text-align: center;">
                        Stay updated
                    </h3>
                    <p style="text-align: center; color: var(--text-secondary); margin-bottom: 30px; font-size: 1.1rem;">
                        Get the latest news about our programs and impact
                    </p>
                    <?php echo kilismile_newsletter_form(array(
                        'show_name' => true,
                        'show_interests' => true,
                        'class' => 'kilismile-newsletter-form enhanced-form',
                        'submit_text' => 'Subscribe Now'
                    )); ?>
                </div>
                
            </div>
        </div>
    </section>
</main>

<!-- Enhanced Form Styling -->
<style>
    .contact-page .container {
        max-width: 1320px;
        width: min(1320px, calc(100% - 48px));
        margin: 0 auto;
    }

    .contact-page .contact-hero .container,
    .contact-page .contact-info .container,
    .contact-page .contact-form-section .container,
    .contact-page .faq-section .container,
    .contact-page .email-system-forms .container {
        width: min(1320px, calc(100% - 48px));
    }
.enhanced-form {
    max-width: 100%;
}

.enhanced-form .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.enhanced-form .form-group {
    margin-bottom: 25px;
    position: relative;
}

.enhanced-form label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--dark-green);
    font-size: 0.95rem;
}

.enhanced-form input,
.enhanced-form textarea,
.enhanced-form select {
    width: 100%;
    padding: 15px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fafafa;
    box-sizing: border-box;
}

.enhanced-form input:focus,
.enhanced-form textarea:focus,
.enhanced-form select:focus {
    outline: none;
    border-color: var(--primary-green);
    background: white;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.enhanced-form textarea {
    min-height: 120px;
    resize: vertical;
}

.enhanced-form .btn {
    background: linear-gradient(135deg, var(--primary-green), var(--accent-green));
    color: white;
    border: none;
    padding: 18px 40px;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
}

.enhanced-form .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(76, 175, 80, 0.4);
}

.enhanced-form .btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.form-message {
    margin-top: 20px;
    padding: 15px 20px;
    border-radius: 10px;
    font-weight: 600;
    display: none;
}

.form-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
    display: block;
}

.form-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
    display: block;
}

.form-message i {
    margin-right: 8px;
}

/* Tab Styling */
.tab-btn:hover {
    background: var(--primary-green) !important;
    color: white !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3) !important;
}

.tab-btn.active {
    background: var(--primary-green) !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .enhanced-form .form-row {
        grid-template-columns: 1fr !important;
    }
    
    .form-tabs {
        flex-direction: column !important;
        align-items: center;
    }
    
    .tab-btn {
        width: 100%;
        max-width: 300px;
        margin-bottom: 10px !important;
    }
    
    .email-system-forms h2 {
        font-size: 2rem !important;
    }
}
</style>

<!-- Tab and Email System Functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabBtns = document.querySelectorAll('.email-system-forms .tab-btn');
    const tabContents = document.querySelectorAll('.email-system-forms .tab-content');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'white';
                b.style.color = 'var(--primary-green)';
                b.style.boxShadow = 'none';
            });
            
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.style.display = 'none';
            });
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            this.style.background = 'var(--primary-green)';
            this.style.color = 'white';
            this.style.boxShadow = '0 4px 15px rgba(76, 175, 80, 0.3)';
            
            const targetContent = document.getElementById(tabName + '-tab');
            if (targetContent) {
                targetContent.classList.add('active');
                targetContent.style.display = 'block';
            }
        });
    });
});
</script>

<script>
// Enhanced form validation and submission
document.getElementById('contact-form-element').addEventListener('submit', function(e) {
    // Don't prevent default - let the form submit normally for server-side processing
    
    // Clear previous errors
    document.querySelectorAll('.error-message').forEach(el => {
        el.style.display = 'none';
        el.textContent = '';
    });
    
    document.querySelectorAll('input, select, textarea').forEach(el => {
        el.style.borderColor = '#e8f5e8';
    });
    
    let isValid = true;
    
    // Validate required fields
    const requiredFields = ['contact_name', 'contact_email', 'contact_subject', 'contact_message'];
    
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(field + '_error');
        
        if (!input.value.trim()) {
            showError(input, errorDiv, 'This field is required.');
            isValid = false;
        }
    });
    
    // Validate email format
    const email = document.getElementById('contact_email');
    const emailError = document.getElementById('contact_email_error');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email.value && !emailRegex.test(email.value)) {
        showError(email, emailError, 'Please enter a valid email address.');
        isValid = false;
    }
    
    // Validate consent checkbox
    const consent = document.querySelector('input[name="contact_consent"]');
    if (!consent.checked) {
        alert('<?php _e('Please agree to the privacy policy before submitting.', 'kilismile'); ?>');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
        return false;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('.contact-submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin" style="margin-right: 8px;"></i> <?php _e('Sending...', 'kilismile'); ?>';
    submitBtn.disabled = true;
    submitBtn.style.background = '#888';
});

function showError(input, errorDiv, message) {
    input.style.borderColor = '#d32f2f';
    input.style.background = '#ffebee';
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

// Enhanced form field focus effects
document.querySelectorAll('input, select, textarea').forEach(field => {
    field.addEventListener('focus', function() {
        this.style.borderColor = 'var(--primary-green)';
        this.style.background = '#fafffe';
        this.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.1)';
    });
    
    field.addEventListener('blur', function() {
        if (!this.classList.contains('error')) {
            this.style.borderColor = '#e8f5e8';
            this.style.background = '#fafffe';
            this.style.boxShadow = 'none';
        }
    });
});

// FAQ functionality
function toggleFAQ(button) {
    const faqItem = button.parentElement;
    const answer = faqItem.querySelector('.faq-answer');
    const icon = button.querySelector('i');
    
    const isOpen = answer.style.maxHeight && answer.style.maxHeight !== '0px';
    
    // Close all other FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        const otherAnswer = item.querySelector('.faq-answer');
        const otherIcon = item.querySelector('.faq-question i');
        
        if (item !== faqItem) {
            otherAnswer.style.maxHeight = '0px';
            otherIcon.style.transform = 'rotate(0deg)';
        }
    });
    
    // Toggle current item
    if (isOpen) {
        answer.style.maxHeight = '0px';
        icon.style.transform = 'rotate(0deg)';
    } else {
        answer.style.maxHeight = answer.scrollHeight + 'px';
        icon.style.transform = 'rotate(180deg)';
    }
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
</script>

<style>
    /* Enhanced hover effects and animations */
    .contact-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .contact-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.12);
    }
    
    .contact-card:hover .icon {
        transform: scale(1.05);
    }
    
    .contact-submit-btn:hover:not(:disabled) {
        background: var(--dark-green) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.4);
    }
    
    .social-link:hover {
        background: var(--dark-green);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .faq-question:hover {
        background: rgba(76, 175, 80, 0.05);
    }
    
    /* Hero section animations */
    .contact-hero h1 {
        animation: fadeInUp 0.8s ease-out;
    }
    
    .contact-hero p {
        animation: fadeInUp 0.8s ease-out 0.2s both;
    }
    
    .contact-hero > div > div:last-child {
        animation: fadeInUp 0.8s ease-out 0.4s both;
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
    
    /* Form field enhancements */
    input, select, textarea {
        transition: all 0.3s ease;
    }
    
    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .contact-hero {
            padding: 120px 0 60px !important;
        }
        
        .contact-form-section > .container > div {
            grid-template-columns: 1fr !important;
            gap: 40px;
        }
        
        .contact-form-container form > div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
        
        .contact-info > .container > div {
            grid-template-columns: 1fr !important;
            gap: 16px;
        }
        
        .faq-section h2 {
            font-size: 2rem !important;
        }
        
        .map-container > div:last-child > div:nth-child(3) {
            grid-template-columns: 1fr !important;
        }
    }
    
    @media (max-width: 480px) {
        .contact-card {
            padding: 15px !important;
        }
        
        .contact-info > .container > div {
            grid-template-columns: 1fr !important;
            gap: 15px;
        }
        
        .contact-form-container > div {
            padding: 25px 20px !important;
        }
        
        .map-container > div:first-child {
            padding: 25px 20px !important;
        }
    }
</style>

<?php get_footer(); ?>


