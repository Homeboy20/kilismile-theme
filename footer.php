    </div><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="container">
            <!-- Main Footer Content -->
            <div class="footer-main">
                <!-- Organization Info -->
                <div class="footer-col footer-about">
                    <?php if (get_theme_mod('kilismile_show_footer_logo', true)) : ?>
                    <div class="footer-logo">
                        <?php 
                        $footer_logo_id = get_theme_mod('kilismile_footer_logo', '');
                        $footer_logo_size = get_theme_mod('kilismile_footer_logo_size', 60);
                        
                        if ($footer_logo_id) :
                            $footer_logo = wp_get_attachment_image_src($footer_logo_id, 'full');
                            if ($footer_logo) :
                        ?>
                            <img src="<?php echo esc_url($footer_logo[0]); ?>" 
                                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                                 width="<?php echo esc_attr($footer_logo_size); ?>" 
                                 height="<?php echo esc_attr($footer_logo_size); ?>"
                                 class="footer-logo-image">
                        <?php 
                            endif;
                        elseif (has_custom_logo()) : 
                            $custom_logo_id = get_theme_mod('custom_logo');
                            $logo = wp_get_attachment_image_src($custom_logo_id, 'full');
                            if ($logo) :
                        ?>
                            <img src="<?php echo esc_url($logo[0]); ?>" 
                                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                                 width="<?php echo esc_attr($footer_logo_size); ?>" 
                                 height="<?php echo esc_attr($footer_logo_size); ?>"
                                 class="footer-logo-image">
                        <?php 
                            endif;
                        else : 
                        ?>
                            <img src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/logo.png" 
                                 alt="<?php echo esc_attr(get_bloginfo('name')); ?>" 
                                 width="<?php echo esc_attr($footer_logo_size); ?>" 
                                 height="<?php echo esc_attr($footer_logo_size); ?>"
                                 class="footer-logo-image">
                        <?php endif; ?>
                        <h3 class="footer-title"><?php bloginfo('name'); ?></h3>
                    </div>
                    <?php endif; ?>
                    
                    <p class="footer-description">
                        <?php echo esc_html(get_theme_mod('kilismile_footer_description', 'Promoting oral and general health education services to children and elderly populations in Tanzania. Building healthier communities through education and care.')); ?>
                    </p>
                    <div class="footer-credentials">
                        <p><strong><?php _e('Reg No:', 'kilismile'); ?></strong> <?php echo esc_html(get_theme_mod('kilismile_registration', '07NGO/R/6067')); ?></p>
                        <p><strong><?php _e('Registered On:', 'kilismile'); ?></strong> <?php _e('25/04/2024', 'kilismile'); ?></p>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-col footer-links">
                    <h4 class="footer-heading"><?php _e('Quick Links', 'kilismile'); ?></h4>
                    <ul class="footer-menu">
                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php _e('Home', 'kilismile'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/about')); ?>"><?php _e('About Us', 'kilismile'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/programs')); ?>"><?php _e('Our Programs', 'kilismile'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/volunteer')); ?>"><?php _e('Get Involved', 'kilismile'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/news')); ?>"><?php _e('News & Events', 'kilismile'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>"><?php _e('Contact', 'kilismile'); ?></a></li>
                    </ul>
                </div>

                <!-- Services -->
                <div class="footer-col footer-services">
                    <h4 class="footer-heading"><?php _e('Our Services', 'kilismile'); ?></h4>
                    <ul class="footer-menu">
                        <li><?php _e('Oral Health Education', 'kilismile'); ?></li>
                        <li><?php _e('Teacher Training', 'kilismile'); ?></li>
                        <li><?php _e('Health Screening', 'kilismile'); ?></li>
                        <li><?php _e('Community Outreach', 'kilismile'); ?></li>
                        <li><?php _e('Prevention Programs', 'kilismile'); ?></li>
                    </ul>
                </div>

                <!-- Contact & Social -->
                <div class="footer-col footer-contact">
                    <h4 class="footer-heading"><?php _e('Get In Touch', 'kilismile'); ?></h4>
                    <?php 
                    $address = get_theme_mod('kilismile_address', 'P.O. Box 928, Moshi, Kilimanjaro, Tanzania');
                    $phone = get_theme_mod('kilismile_phone', '0763495575/0735495575');
                    $email = get_theme_mod('kilismile_email', 'kilismile21@gmail.com');
                    ?>
                    
                    <div class="contact-info">
                        <?php if ($address) : ?>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                                <span><?php echo esc_html($address); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($phone) : ?>
                            <div class="contact-item">
                                <i class="fas fa-phone" aria-hidden="true"></i>
                                <a href="tel:<?php echo esc_attr(str_replace(['/', ' '], '', $phone)); ?>">
                                    <?php echo esc_html($phone); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($email) : ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                                <a href="mailto:<?php echo esc_attr($email); ?>">
                                    <?php echo esc_html($email); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Social Media -->
                    <div class="social-section">
                        <h5 class="social-title"><?php _e('Follow Us', 'kilismile'); ?></h5>
                        <div class="social-links">
                            <?php 
                            $instagram = get_theme_mod('kilismile_instagram', 'https://instagram.com/kili_smile');
                            $facebook = get_theme_mod('kilismile_facebook', '');
                            $twitter = get_theme_mod('kilismile_twitter', '');
                            ?>
                            
                            <?php if ($instagram) : ?>
                                <a href="<?php echo esc_url($instagram); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   aria-label="<?php _e('Follow us on Instagram', 'kilismile'); ?>">
                                    <i class="fab fa-instagram" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($facebook) : ?>
                                <a href="<?php echo esc_url($facebook); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   aria-label="<?php _e('Follow us on Facebook', 'kilismile'); ?>">
                                    <i class="fab fa-facebook-f" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($twitter) : ?>
                                <a href="<?php echo esc_url($twitter); ?>" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   aria-label="<?php _e('Follow us on Twitter', 'kilismile'); ?>">
                                    <i class="fab fa-twitter" aria-hidden="true"></i>
                                </a>
                            <?php endif; ?>
                            
                            <a href="mailto:<?php echo esc_attr($email); ?>" 
                               aria-label="<?php _e('Send us an email', 'kilismile'); ?>">
                                <i class="fas fa-envelope" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        <p>
                            &copy; <?php echo date('Y'); ?> <strong><?php bloginfo('name'); ?></strong>. 
                            <?php _e('All rights reserved.', 'kilismile'); ?>
                        </p>
                    </div>
                    
                    <div class="footer-legal">
                        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php _e('Privacy Policy', 'kilismile'); ?></a>
                        <span class="separator">|</span>
                        <a href="<?php echo esc_url(home_url('/terms-of-service')); ?>"><?php _e('Terms of Service', 'kilismile'); ?></a>
                        <span class="separator">|</span>
                        <a href="<?php echo esc_url(home_url('/accessibility')); ?>"><?php _e('Accessibility', 'kilismile'); ?></a>
                    </div>
                    
                    <div class="footer-tagline">
                        <p><?php echo esc_html(get_theme_mod('kilismile_footer_copyright', '"No health without oral health"')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div><!-- #page -->

<!-- Back to Top Button -->
<button id="back-to-top" 
        class="back-to-top" 
        aria-label="<?php _e('Back to top', 'kilismile'); ?>">
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
</button>

<?php wp_footer(); ?>

</body>
</html>

<?php
/**
 * Footer fallback menu function
 */
function kilismile_footer_fallback_menu() {
    echo '<ul>';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">' . __('About Us', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/programs')) . '">' . __('Programs', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . __('Contact', 'kilismile') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/donate')) . '">' . __('Donate', 'kilismile') . '</a></li>';
    echo '</ul>';
}
?>
