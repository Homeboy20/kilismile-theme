<?php
/**
 * Template Name: Partners Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="partners-hero" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%); color: white; padding: 120px 0 80px; text-align: center; position: relative; overflow: hidden;">
        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><defs><pattern id=\"dots\" width=\"20\" height=\"20\" patternUnits=\"userSpaceOnUse\"><circle cx=\"10\" cy=\"10\" r=\"2\" fill=\"rgba(255,255,255,0.1)\"/></pattern></defs><rect width=\"100\" height=\"100\" fill=\"url(%23dots)\"/></svg>'); opacity: 0.7;"></div>
        <div class="container" style="position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h1 style="font-size: 3.5rem; margin-bottom: 20px; font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                <?php _e('Our Partners', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.3rem; max-width: 800px; margin: 0 auto 40px; line-height: 1.6; opacity: 0.9;">
                <?php _e('Meet the incredible organizations and companies that share our vision for global health equity. Together, we\'re making a lasting impact on communities across Tanzania and beyond.', 'kilismile'); ?>
            </p>
            
            <!-- Partner Categories Filter -->
            <div class="partner-filter" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 40px;">
                <button class="filter-btn active" data-category="all" style="background: rgba(255,255,255,0.2); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 10px 20px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                    <?php _e('All Partners', 'kilismile'); ?>
                </button>
                <button class="filter-btn" data-category="corporate" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 10px 20px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                    <?php _e('Corporate', 'kilismile'); ?>
                </button>
                <button class="filter-btn" data-category="community" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 10px 20px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                    <?php _e('Community', 'kilismile'); ?>
                </button>
                <button class="filter-btn" data-category="strategic" style="background: rgba(255,255,255,0.1); color: white; border: 2px solid rgba(255,255,255,0.3); padding: 10px 20px; border-radius: 25px; cursor: pointer; transition: all 0.3s ease; backdrop-filter: blur(10px);">
                    <?php _e('Strategic', 'kilismile'); ?>
                </button>
            </div>
        </div>
    </section>

    <!-- Featured Partners Section -->
    <section class="featured-partners" style="padding: 100px 0; background: #fff;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Featured Partners', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Showcasing our most impactful partnerships through their distinctive brands and shared commitment to global health education.', 'kilismile'); ?>
                </p>
            </div>
            
            <?php echo kilismile_render_partner_showcase('featured', 'all', 6); ?>
        </div>
    </section>

    <!-- Partner Logos Showcase -->
    <section class="partner-logos-showcase" style="padding: 80px 0; background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 60px;">
                <h2 style="color: #2c3e50; font-size: 2.2rem; margin-bottom: 15px; font-weight: 700;">
                    <?php _e('Trusted by Leading Organizations', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.1rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Our partnerships represent a network of trusted organizations committed to making healthcare accessible worldwide.', 'kilismile'); ?>
                </p>
            </div>
            
            <?php echo kilismile_render_partner_showcase('logos', 'all', 12); ?>
        </div>
    </section>

    <!-- Corporate Partners Section -->
    <section class="corporate-partners-section" style="padding: 100px 0; background: #f8f9fa;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Corporate Partners', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Forward-thinking companies that demonstrate corporate social responsibility through meaningful partnerships.', 'kilismile'); ?>
                </p>
            </div>
            
            <?php echo kilismile_render_partner_showcase('grid', 'corporate'); ?>
        </div>
    </section>

    <!-- Community Partners Section -->
    <section class="community-partners-section" style="padding: 100px 0; background: #fff;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Community Partners', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Local organizations and community groups that work hand-in-hand with us to serve those who need it most.', 'kilismile'); ?>
                </p>
            </div>
            
            <?php echo kilismile_render_partner_showcase('carousel', 'community'); ?>
        </div>
    </section>

    <!-- Strategic Partners Section -->
    <section class="strategic-partners-section" style="padding: 100px 0; background: #f8f9fa;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Strategic Partners', 'kilismile'); ?>
                </h2>
                <p style="color: #7f8c8d; font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6;">
                    <?php _e('Government agencies, international organizations, and institutions that amplify our impact through strategic collaboration.', 'kilismile'); ?>
                </p>
            </div>
            
            <?php echo kilismile_render_partner_showcase('logos', 'strategic'); ?>
        </div>
    </section>

    <!-- Partnership Benefits -->
    <section class="partnership-benefits" style="padding: 100px 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="text-align: center; margin-bottom: 80px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                    <?php _e('Partnership Benefits', 'kilismile'); ?>
                </h2>
                <p style="font-size: 1.2rem; max-width: 700px; margin: 0 auto; line-height: 1.6; opacity: 0.9;">
                    <?php _e('Discover the mutual benefits and impact opportunities that come with partnering with Kilismile.', 'kilismile'); ?>
                </p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">
                <div class="benefit-card" style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                        <i class="fas fa-chart-line" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>
                    <h3 style="margin-bottom: 20px; font-size: 1.5rem;"><?php _e('Measurable Impact', 'kilismile'); ?></h3>
                    <p style="opacity: 0.9; line-height: 1.6;">
                        <?php _e('Track concrete outcomes with detailed metrics and regular progress updates that demonstrate real-world impact.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                        <i class="fas fa-users" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>
                    <h3 style="margin-bottom: 20px; font-size: 1.5rem;"><?php _e('Community Engagement', 'kilismile'); ?></h3>
                    <p style="opacity: 0.9; line-height: 1.6;">
                        <?php _e('Connect with local communities and create meaningful volunteer opportunities for your team or organization.', 'kilismile'); ?>
                    </p>
                </div>

                <div class="benefit-card" style="background: rgba(255,255,255,0.1); padding: 40px; border-radius: 20px; text-align: center; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2);">
                    <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                        <i class="fas fa-award" style="font-size: 2rem;" aria-hidden="true"></i>
                    </div>
                    <h3 style="margin-bottom: 20px; font-size: 1.5rem;"><?php _e('Recognition & Visibility', 'kilismile'); ?></h3>
                    <p style="opacity: 0.9; line-height: 1.6;">
                        <?php _e('Gain recognition for your commitment to social impact and enhance your organization\'s reputation.', 'kilismile'); ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Become a Partner CTA -->
    <section class="become-partner-cta" style="padding: 100px 0; background: #fff;">
        <div class="container" style="max-width: 800px; margin: 0 auto; padding: 0 20px; text-align: center;">
            <h2 style="color: #2c3e50; font-size: 2.5rem; margin-bottom: 20px; font-weight: 700;">
                <?php _e('Become Our Partner', 'kilismile'); ?>
            </h2>
            <p style="color: #7f8c8d; font-size: 1.2rem; margin-bottom: 40px; line-height: 1.6;">
                <?php _e('Join our growing network of partners and help us create lasting change in global health education. Together, we can reach more communities and save more lives.', 'kilismile'); ?>
            </p>

            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                <a href="/corporate" style="background: #3498db; color: white; text-decoration: none; padding: 15px 30px; border-radius: 30px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(52, 152, 219, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(52, 152, 219, 0.3)'">
                    <?php _e('Corporate Partnership', 'kilismile'); ?>
                </a>
                <a href="/partnerships" style="background: transparent; color: #3498db; text-decoration: none; padding: 15px 30px; border-radius: 30px; font-weight: 600; border: 2px solid #3498db; transition: all 0.3s ease;" onmouseover="this.style.background='#3498db'; this.style.color='white'" onmouseout="this.style.background='transparent'; this.style.color='#3498db'">
                    <?php _e('General Partnership', 'kilismile'); ?>
                </a>
            </div>
        </div>
    </section>
</main>

<script>
// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const partnerSections = document.querySelectorAll('.corporate-partners-section, .community-partners-section, .strategic-partners-section');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active button
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.style.background = 'rgba(255,255,255,0.1)';
            });
            this.classList.add('active');
            this.style.background = 'rgba(255,255,255,0.2)';
            
            // Show/hide sections based on filter
            if (category === 'all') {
                partnerSections.forEach(section => {
                    section.style.display = 'block';
                });
            } else {
                partnerSections.forEach(section => {
                    if (section.classList.contains(`${category}-partners-section`)) {
                        section.style.display = 'block';
                        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    } else {
                        section.style.display = 'none';
                    }
                });
            }
        });
    });
});
</script>

<style>
/* Responsive Design */
@media (max-width: 768px) {
    .partners-hero h1 {
        font-size: 2.5rem !important;
    }
    
    .partners-hero p {
        font-size: 1.1rem !important;
    }
    
    .partner-filter {
        flex-direction: column !important;
        align-items: center;
    }
    
    .partner-filter .filter-btn {
        width: 200px;
    }
    
    .partnership-benefits > div > div {
        grid-template-columns: 1fr !important;
        gap: 30px;
    }
    
    .become-partner-cta > div > div {
        flex-direction: column !important;
        align-items: center;
    }
    
    .become-partner-cta a {
        width: 250px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .partners-hero {
        padding: 80px 0 60px !important;
    }
    
    .partners-hero h1 {
        font-size: 2rem !important;
    }
    
    .featured-partners,
    .corporate-partners-section,
    .community-partners-section,
    .strategic-partners-section,
    .partnership-benefits,
    .become-partner-cta {
        padding: 60px 0 !important;
    }
    
    .featured-partners h2,
    .corporate-partners-section h2,
    .community-partners-section h2,
    .strategic-partners-section h2,
    .partnership-benefits h2,
    .become-partner-cta h2 {
        font-size: 2rem !important;
    }
}

/* Smooth animations */
.benefit-card {
    transition: transform 0.3s ease;
}

.benefit-card:hover {
    transform: translateY(-10px);
}
</style>

<?php get_footer(); ?>


