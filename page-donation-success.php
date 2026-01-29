<?php
/**
 * Template Name: Donation Success
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Success Hero Section -->
    <section class="success-hero" style="background: linear-gradient(135deg, var(--primary-green), var(--accent-green)); color: white; padding: 140px 0 80px; text-align: center;">
        <div class="container">
            <div style="max-width: 600px; margin: 0 auto;">
                <div style="width: 100px; height: 100px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                    <i class="fas fa-check" style="color: var(--primary-green); font-size: 3rem;"></i>
                </div>
                
                <h1 style="font-size: clamp(2rem, 4vw, 2.8rem); margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Thank You for Your Generous Donation!', 'kilismile'); ?>
                </h1>
                
                <p style="font-size: 1.2rem; margin-bottom: 30px; line-height: 1.6; opacity: 0.95;">
                    <?php _e('Your support makes a real difference in the lives of people in Tanzania. We are grateful for your commitment to improving health education and community well-being.', 'kilismile'); ?>
                </p>
                
                <?php
                // Display donation details if available from AzamPay plugin
                $donation_id = isset($_GET['donation_id']) ? sanitize_text_field($_GET['donation_id']) : '';
                $amount = isset($_GET['amount']) ? sanitize_text_field($_GET['amount']) : '';
                $currency = isset($_GET['currency']) ? sanitize_text_field($_GET['currency']) : 'TZS';
                $donor_name = isset($_GET['donor']) ? sanitize_text_field($_GET['donor']) : '';
                
                if ($donation_id || $amount) :
                ?>
                <div style="background: rgba(255,255,255,0.15); padding: 25px; border-radius: 12px; backdrop-filter: blur(10px); margin-bottom: 30px;">
                    <?php if ($amount) : ?>
                        <div style="font-size: 2rem; font-weight: 700; margin-bottom: 10px;">
                            <?php echo esc_html($currency . ' ' . number_format((float)$amount)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($donor_name) : ?>
                        <div style="font-size: 1.1rem; margin-bottom: 10px; opacity: 0.9;">
                            <?php echo sprintf(__('Thank you, %s!', 'kilismile'), esc_html($donor_name)); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($donation_id) : ?>
                        <div style="font-size: 0.9rem; opacity: 0.8;">
                            <?php echo sprintf(__('Donation ID: %s', 'kilismile'), esc_html($donation_id)); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; backdrop-filter: blur(10px); margin-bottom: 30px;">
                    <p style="margin: 0; font-size: 1rem; opacity: 0.9;">
                        <?php _e('A confirmation email has been sent to your email address with your donation receipt and transaction details.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- What Happens Next Section -->
    <section class="next-steps" style="padding: 80px 0; background: white;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="color: var(--dark-green); font-size: 2rem; margin-bottom: 15px; font-weight: 600;">
                    <?php _e('What Happens Next', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Here\'s how your donation will be put to work in our mission to improve health education in Tanzania.', 'kilismile'); ?>
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <!-- Step 1 -->
                <div class="step-card" style="background: #f8f9fa; padding: 30px; border-radius: 12px; text-align: center; border-top: 4px solid var(--primary-green);">
                    <div style="width: 60px; height: 60px; background: var(--primary-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="color: white; font-size: 1.5rem; font-weight: bold;">1</span>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; font-weight: 600;">
                        <?php _e('Immediate Processing', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        <?php _e('Your donation is immediately allocated to our most urgent health education programs and community outreach initiatives.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="step-card" style="background: #f8f9fa; padding: 30px; border-radius: 12px; text-align: center; border-top: 4px solid var(--accent-green);">
                    <div style="width: 60px; height: 60px; background: var(--accent-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="color: white; font-size: 1.5rem; font-weight: bold;">2</span>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; font-weight: 600;">
                        <?php _e('Program Implementation', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        <?php _e('Within 30 days, your contribution will be actively supporting health screenings, educational workshops, and community programs.', 'kilismile'); ?>
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="step-card" style="background: #f8f9fa; padding: 30px; border-radius: 12px; text-align: center; border-top: 4px solid var(--light-green);">
                    <div style="width: 60px; height: 60px; background: var(--light-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <span style="color: white; font-size: 1.5rem; font-weight: bold;">3</span>
                    </div>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.3rem; font-weight: 600;">
                        <?php _e('Impact Updates', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); line-height: 1.6; margin: 0;">
                        <?php _e('You\'ll receive quarterly updates showing exactly how your donation has helped improve lives in Tanzanian communities.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Share Success Section -->
    <section class="share-success" style="padding: 60px 0; background: var(--light-gray);">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <h2 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 20px; font-weight: 600;">
                <?php _e('Help Us Spread the Word', 'kilismile'); ?>
            </h2>
            <p style="color: var(--text-secondary); font-size: 1rem; margin-bottom: 30px; line-height: 1.6;">
                <?php _e('Share your support and inspire others to join our mission of improving health education in Tanzania.', 'kilismile'); ?>
            </p>
            
            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-bottom: 30px;">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(home_url('/donate')); ?>" 
                   target="_blank"
                   class="social-share-btn" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #3b5998; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-facebook-f"></i>
                    <?php _e('Share on Facebook', 'kilismile'); ?>
                </a>
                
                <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(__('I just donated to @KiliSmile to support health education in Tanzania! Join me: ', 'kilismile')); ?>&url=<?php echo urlencode(home_url('/donate')); ?>" 
                   target="_blank"
                   class="social-share-btn" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #1da1f2; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-twitter"></i>
                    <?php _e('Share on Twitter', 'kilismile'); ?>
                </a>
                
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(home_url('/donate')); ?>" 
                   target="_blank"
                   class="social-share-btn" 
                   style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 20px; background: #0077b5; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    <i class="fab fa-linkedin-in"></i>
                    <?php _e('Share on LinkedIn', 'kilismile'); ?>
                </a>
            </div>
            
            <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 4px solid var(--primary-green);">
                <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem;">
                    <?php _e('Invite Friends to Donate', 'kilismile'); ?>
                </h3>
                <p style="color: var(--text-secondary); margin-bottom: 15px; line-height: 1.5;">
                    <?php _e('Copy this link to share with friends and family:', 'kilismile'); ?>
                </p>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" 
                           value="<?php echo home_url('/donate'); ?>" 
                           readonly
                           id="share-link"
                           style="flex: 1; padding: 10px; border: 2px solid #e0e0e0; border-radius: 6px; background: #f8f9fa;">
                    <button onclick="copyShareLink()" 
                            class="btn btn-primary" 
                            style="padding: 10px 20px; background: var(--primary-green); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        <i class="fas fa-copy"></i>
                        <?php _e('Copy', 'kilismile'); ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Continue Engagement Section -->
    <section class="continue-engagement" style="padding: 60px 0; background: white;">
        <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 40px;">
                <h2 style="color: var(--dark-green); font-size: 1.8rem; margin-bottom: 15px; font-weight: 600;">
                    <?php _e('Stay Connected with Our Mission', 'kilismile'); ?>
                </h2>
                <p style="color: var(--text-secondary); font-size: 1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('There are many ways to continue supporting our work beyond financial donations.', 'kilismile'); ?>
                </p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px;">
                <!-- Newsletter -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--primary-green);">
                    <i class="fas fa-envelope" style="font-size: 2.5rem; color: var(--primary-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Newsletter Updates', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Get monthly updates on our programs and see your impact in action.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo home_url('/newsletter'); ?>" 
                       class="btn btn-outline" 
                       style="display: inline-block; padding: 10px 20px; border: 2px solid var(--primary-green); color: var(--primary-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <?php _e('Subscribe', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Volunteer -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--accent-green);">
                    <i class="fas fa-hands-helping" style="font-size: 2.5rem; color: var(--accent-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Volunteer Opportunities', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Donate your time and skills to directly help our programs.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo home_url('/volunteer'); ?>" 
                       class="btn btn-outline" 
                       style="display: inline-block; padding: 10px 20px; border: 2px solid var(--accent-green); color: var(--accent-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <?php _e('Learn More', 'kilismile'); ?>
                    </a>
                </div>

                <!-- Follow Us -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--light-green);">
                    <i class="fas fa-share-alt" style="font-size: 2.5rem; color: var(--light-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Follow Our Journey', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Stay updated on social media for daily stories and updates.', 'kilismile'); ?>
                    </p>
                    <div style="display: flex; gap: 10px; justify-content: center;">
                        <a href="#" style="color: var(--light-green); font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="color: var(--light-green); font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="color: var(--light-green); font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>

                <!-- Corporate -->
                <div class="engagement-card" style="background: #f8f9fa; padding: 25px; border-radius: 10px; text-align: center; border-left: 4px solid var(--dark-green);">
                    <i class="fas fa-building" style="font-size: 2.5rem; color: var(--dark-green); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--dark-green); margin-bottom: 15px; font-size: 1.2rem; font-weight: 600;">
                        <?php _e('Corporate Partnership', 'kilismile'); ?>
                    </h3>
                    <p style="color: var(--text-secondary); margin-bottom: 20px; line-height: 1.5; font-size: 0.95rem;">
                        <?php _e('Explore partnership opportunities for your organization.', 'kilismile'); ?>
                    </p>
                    <a href="<?php echo home_url('/partnerships'); ?>" 
                       class="btn btn-outline" 
                       style="display: inline-block; padding: 10px 20px; border: 2px solid var(--dark-green); color: var(--dark-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                        <?php _e('Partner With Us', 'kilismile'); ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA -->
    <section class="final-cta" style="padding: 50px 0; background: var(--primary-green); color: white; text-align: center;">
        <div class="container">
            <h2 style="font-size: 1.5rem; margin-bottom: 15px; color: white; font-weight: 600;">
                <?php _e('Thank You for Being Part of the Solution', 'kilismile'); ?>
            </h2>
            <p style="margin-bottom: 25px; opacity: 0.95; line-height: 1.5; max-width: 600px; margin-left: auto; margin-right: auto;">
                <?php _e('Together, we\'re building healthier communities and brighter futures across Tanzania. Your generosity makes it all possible.', 'kilismile'); ?>
            </p>
            <a href="<?php echo esc_url(home_url('/')); ?>" 
               class="btn btn-secondary" 
               style="display: inline-block; padding: 12px 25px; background: white; color: var(--primary-green); text-decoration: none; border-radius: 6px; font-weight: 600;">
                <?php _e('Return to Homepage', 'kilismile'); ?>
            </a>
        </div>
    </section>
</main>

<script>
function copyShareLink() {
    const shareInput = document.getElementById('share-link');
    shareInput.select();
    shareInput.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Show success message
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> <?php _e('Copied!', 'kilismile'); ?>';
        button.style.background = '#28a745';
        
        setTimeout(() => {
            button.innerHTML = originalText;
            button.style.background = 'var(--primary-green)';
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
}

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.step-card, .engagement-card');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });
    
    cards.forEach((card) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

<style>
.social-share-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.engagement-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.step-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

@media (max-width: 768px) {
    .success-hero {
        padding-top: 120px !important;
    }
    
    .success-hero h1 {
        font-size: 1.8rem !important;
    }
    
    .next-steps,
    .share-success,
    .continue-engagement {
        padding: 50px 0 !important;
    }
    
    .social-share-btn {
        font-size: 0.9rem !important;
        padding: 10px 15px !important;
    }
}
</style>

<?php get_footer(); ?>


