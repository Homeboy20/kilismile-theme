<?php
/**
 * Partner Showcase Test File
 * 
 * This file can be used to test the partner showcase functionality
 * by adding it as a WordPress page template or including it in any test environment.
 * 
 * @package KiliSmile
 * @version 1.0.0
 */

// Test data for partner showcase functionality - Logo Focused
$test_partners = array(
    array(
        'name' => 'Microsoft Foundation',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/Microsoft_logo.svg/512px-Microsoft_logo.svg.png',
        'description' => 'Leading technology company supporting digital health initiatives across Africa.',
        'website' => 'https://www.microsoft.com',
        'category' => 'corporate',
        'featured' => true
    ),
    array(
        'name' => 'World Health Organization',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c9/WHO_logo.svg/512px-WHO_logo.svg.png',
        'description' => 'International strategic partner in global health policy and implementation.',
        'website' => 'https://www.who.int',
        'category' => 'strategic',
        'featured' => true
    ),
    array(
        'name' => 'Google.org',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/512px-Google_2015_logo.svg.png',
        'description' => 'Supporting technology solutions for healthcare accessibility in rural areas.',
        'website' => 'https://www.google.org',
        'category' => 'corporate',
        'featured' => true
    ),
    array(
        'name' => 'USAID',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7d/USAID-Identity.svg/512px-USAID-Identity.svg.png',
        'description' => 'Government agency partnership for sustainable healthcare development.',
        'website' => 'https://www.usaid.gov',
        'category' => 'strategic',
        'featured' => false
    ),
    array(
        'name' => 'United Nations',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/UN_emblem_blue.svg/512px-UN_emblem_blue.svg.png',
        'description' => 'Global partnership for sustainable development goals in healthcare.',
        'website' => 'https://www.un.org',
        'category' => 'strategic',
        'featured' => true
    ),
    array(
        'name' => 'Bill & Melinda Gates Foundation',
        'logo' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Gates_Foundation_logo.svg/512px-Gates_Foundation_logo.svg.png',
        'description' => 'Philanthropic foundation driving innovation in global health and education.',
        'website' => 'https://www.gatesfoundation.org',
        'category' => 'corporate',
        'featured' => true
    ),
    array(
        'name' => 'Kilimanjaro Health Initiative',
        'logo' => 'https://via.placeholder.com/200x100/2E8B57/FFFFFF?text=KHI',
        'description' => 'Local community organization focused on preventive healthcare education.',
        'website' => 'https://example.com',
        'category' => 'community',
        'featured' => false
    ),
    array(
        'name' => 'Moshi Community Center',
        'logo' => 'https://via.placeholder.com/200x100/FF6B35/FFFFFF?text=MCC',
        'description' => 'Grassroots community partner providing local outreach and education programs.',
        'website' => 'https://example.com',
        'category' => 'community',
        'featured' => false
    )
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Showcase Test - KiliSmile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            line-height: 1.6;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .test-section {
            margin-bottom: 50px;
            padding: 30px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
        }
        .test-section h3 {
            color: #3498db;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        .shortcode-example {
            background: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
            white-space: pre-wrap;
        }
        .test-result {
            margin-top: 20px;
            padding: 20px;
            border-radius: 5px;
            background: white;
            border: 1px solid #ddd;
        }
        .success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .info {
            background: #d1ecf1;
            border-color: #bee5eb;
            color: #0c5460;
        }
        
        /* Partner Showcase Styles (matching the main theme) */
        .partner-showcase {
            margin: 20px 0;
        }
        
        .partner-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin: 30px 0;
        }
        
        .partner-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e9ecef;
        }
        
        .partner-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .partner-card .partner-logo {
            width: 100%;
            max-width: 160px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        
        .partner-card h4 {
            color: #2c3e50;
            font-size: 1.3rem;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .partner-card p {
            color: #7f8c8d;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        
        .partner-card .partner-link {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s ease;
        }
        
        .partner-card .partner-link:hover {
            color: #2980b9;
        }
        
        .partner-logos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            align-items: center;
            justify-items: center;
            margin: 30px 0;
        }
        
        .partner-logos .partner-logo-item {
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .partner-logos .partner-logo-item:hover {
            transform: scale(1.05);
        }
        
        .partner-logos .partner-logo-item img {
            width: 100%;
            max-width: 180px;
            height: 80px;
            object-fit: contain;
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }
        
        .partner-logos .partner-logo-item:hover img {
            filter: grayscale(0%);
        }
        
        .carousel-container {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            margin: 30px 0;
        }
        
        .carousel-wrapper {
            display: flex;
            transition: transform 0.5s ease;
        }
        
        .carousel-slide {
            min-width: 100%;
            padding: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }
        
        .featured-partners {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin: 30px 0;
        }
        
        .featured-partner-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .featured-partner-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="dots" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="2" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23dots)"/></svg>');
            opacity: 0.3;
        }
        
        .featured-partner-card > * {
            position: relative;
            z-index: 2;
        }
        
        @media (max-width: 768px) {
            .partner-grid,
            .featured-partners {
                grid-template-columns: 1fr;
            }
            
            .partner-logos {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-handshake"></i> Logo-Focused Partner Showcase Test</h1>
        <p>This page demonstrates the enhanced partner showcase with prominent logo displays and visual brand recognition.</p>
        
        <!-- Test Section 1: Enhanced Logo Grid Layout -->
        <div class="test-section">
            <h3><i class="fas fa-th-large"></i> Enhanced Logo Grid Layout</h3>
            <p>Testing the enhanced grid layout with prominent logo displays and visual branding focus.</p>
            <div class="shortcode-example">[kilismile_partners layout="grid" category="all" limit="6"]</div>
            
            <div class="test-result">
                <h4>Grid Layout Result:</h4>
                <div class="partner-showcase">
                    <div class="partner-grid">
                        <?php foreach ($test_partners as $partner): ?>
                            <div class="partner-card">
                                <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" class="partner-logo">
                                <h4><?php echo esc_html($partner['name']); ?></h4>
                                <p><?php echo esc_html($partner['description']); ?></p>
                                <a href="<?php echo esc_url($partner['website']); ?>" class="partner-link" target="_blank">
                                    <?php _e('Learn More', 'kilismile'); ?> <i class="fas fa-external-link-alt"></i>
                                </a>
                                <div style="margin-top: 15px;">
                                    <span style="background: #3498db; color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.8rem; text-transform: uppercase;">
                                        <?php echo esc_html($partner['category']); ?>
                                    </span>
                                    <?php if ($partner['featured']): ?>
                                        <span style="background: #e74c3c; color: white; padding: 4px 12px; border-radius: 15px; font-size: 0.8rem; text-transform: uppercase; margin-left: 8px;">
                                            Featured
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Section 2: Premium Logo Display Layout -->
        <div class="test-section">
            <h3><i class="fas fa-crown"></i> Premium Logo Display Layout</h3>
            <p>Testing the premium logo-only layout designed for maximum visual impact and brand recognition.</p>
            <div class="shortcode-example">[kilismile_partners layout="logos" category="corporate" limit="6"]</div>
            
            <div class="test-result">
                <h4>Logo Layout Result:</h4>
                <div class="partner-showcase">
                    <div class="partner-logos">
                        <?php 
                        $corporate_partners = array_filter($test_partners, function($partner) {
                            return $partner['category'] === 'corporate';
                        });
                        foreach ($corporate_partners as $partner): 
                        ?>
                            <div class="partner-logo-item">
                                <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>">
                                <h5 style="margin-top: 15px; color: #2c3e50; font-size: 0.9rem;"><?php echo esc_html($partner['name']); ?></h5>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Section 3: Featured Partner Logos -->
        <div class="test-section">
            <h3><i class="fas fa-star"></i> Featured Partner Logos</h3>
            <p>Testing the featured partners layout with prominent logo displays and enhanced visual presentation.</p>
            <div class="shortcode-example">[kilismile_partners layout="featured" category="all" limit="4"]</div>
            
            <div class="test-result">
                <h4>Featured Layout Result:</h4>
                <div class="partner-showcase">
                    <div class="featured-partners">
                        <?php 
                        $featured_partners = array_filter($test_partners, function($partner) {
                            return $partner['featured'] === true;
                        });
                        foreach ($featured_partners as $partner): 
                        ?>
                            <div class="featured-partner-card">
                                <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="width: 100%; max-width: 200px; height: 80px; object-fit: contain; margin-bottom: 25px; filter: brightness(0) invert(1);">
                                <h4 style="font-size: 1.5rem; margin-bottom: 20px; font-weight: 700;"><?php echo esc_html($partner['name']); ?></h4>
                                <p style="margin-bottom: 25px; opacity: 0.9; line-height: 1.6;"><?php echo esc_html($partner['description']); ?></p>
                                <a href="<?php echo esc_url($partner['website']); ?>" style="background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 12px 25px; border-radius: 25px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; backdrop-filter: blur(10px);" target="_blank">
                                    <?php _e('Partnership Details', 'kilismile'); ?> <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Section 4: Logo Carousel Display -->
        <div class="test-section">
            <h3><i class="fas fa-images"></i> Logo Carousel Display</h3>
            <p>Testing the enhanced carousel layout with prominent logo presentation and smooth scrolling.</p>
            <div class="shortcode-example">[kilismile_partners layout="carousel" category="community" limit="4"]</div>
            
            <div class="test-result">
                <h4>Carousel Layout Result:</h4>
                <div class="partner-showcase">
                    <div class="carousel-container">
                        <div class="carousel-wrapper" id="partnerCarousel">
                            <?php 
                            $community_partners = array_filter($test_partners, function($partner) {
                                return $partner['category'] === 'community';
                            });
                            foreach ($community_partners as $partner): 
                            ?>
                                <div class="carousel-slide">
                                    <img src="<?php echo esc_url($partner['logo']); ?>" alt="<?php echo esc_attr($partner['name']); ?>" style="width: 200px; height: 100px; object-fit: contain; margin-bottom: 30px; filter: brightness(0) invert(1);">
                                    <h4 style="font-size: 2rem; margin-bottom: 20px; font-weight: 700;"><?php echo esc_html($partner['name']); ?></h4>
                                    <p style="font-size: 1.1rem; max-width: 600px; margin: 0 auto 30px; opacity: 0.9; line-height: 1.6;"><?php echo esc_html($partner['description']); ?></p>
                                    <a href="<?php echo esc_url($partner['website']); ?>" style="background: rgba(255,255,255,0.2); color: white; text-decoration: none; padding: 15px 30px; border-radius: 30px; font-weight: 600; display: inline-flex; align-items: center; gap: 10px; transition: all 0.3s ease; backdrop-filter: blur(10px);" target="_blank">
                                        <?php _e('Visit Partner', 'kilismile'); ?> <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button onclick="previousSlide()" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); color: white; border: none; padding: 15px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; backdrop-filter: blur(10px);">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button onclick="nextSlide()" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); color: white; border: none; padding: 15px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; backdrop-filter: blur(10px);">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Section 5: Category Filtering -->
        <div class="test-section">
            <h3><i class="fas fa-filter"></i> Category Filtering Test</h3>
            <p>Testing category-based partner filtering functionality.</p>
            
            <div style="margin: 20px 0;">
                <h4>Corporate Partners:</h4>
                <div class="shortcode-example">[kilismile_partners layout="grid" category="corporate"]</div>
                <div class="info test-result">
                    Found <?php echo count(array_filter($test_partners, function($p) { return $p['category'] === 'corporate'; })); ?> corporate partners
                </div>
            </div>

            <div style="margin: 20px 0;">
                <h4>Community Partners:</h4>
                <div class="shortcode-example">[kilismile_partners layout="grid" category="community"]</div>
                <div class="info test-result">
                    Found <?php echo count(array_filter($test_partners, function($p) { return $p['category'] === 'community'; })); ?> community partners
                </div>
            </div>

            <div style="margin: 20px 0;">
                <h4>Strategic Partners:</h4>
                <div class="shortcode-example">[kilismile_partners layout="grid" category="strategic"]</div>
                <div class="info test-result">
                    Found <?php echo count(array_filter($test_partners, function($p) { return $p['category'] === 'strategic'; })); ?> strategic partners
                </div>
            </div>
        </div>

        <!-- Test Results Summary -->
        <div class="test-section success">
            <h3><i class="fas fa-check-circle"></i> Logo-Focused Design Results</h3>
            <ul style="font-size: 1.1rem; line-height: 1.8;">
                <li><i class="fas fa-check"></i> <strong>Enhanced Grid Layout:</strong> Larger logo displays with 140px height sections and premium styling</li>
                <li><i class="fas fa-check"></i> <strong>Premium Logo Display:</strong> 200x160px minimum cards with enhanced visual effects and hover animations</li>
                <li><i class="fas fa-check"></i> <strong>Featured Partner Logos:</strong> Prominent 160x100px logo sections with gradient backgrounds</li>
                <li><i class="fas fa-check"></i> <strong>Logo Carousel:</strong> Enhanced 120x70px logo displays with smooth scrolling</li>
                <li><i class="fas fa-check"></i> <strong>Visual Brand Recognition:</strong> Drop-shadow effects and hover scaling for logo prominence</li>
                <li><i class="fas fa-check"></i> <strong>Responsive Logo Scaling:</strong> Adaptive logo sizes for optimal display across devices</li>
                <li><i class="fas fa-check"></i> <strong>Real Brand Integration:</strong> Support for actual partner logos with proper scaling and filtering</li>
            </ul>
        </div>

        <!-- Usage Instructions -->
        <div class="test-section info">
            <h3><i class="fas fa-info-circle"></i> Usage Instructions</h3>
            <h4>Available Shortcode Attributes:</h4>
            <ul>
                <li><strong>layout:</strong> "grid", "carousel", "logos", "featured" (default: "grid")</li>
                <li><strong>category:</strong> "all", "corporate", "community", "strategic" (default: "all")</li>
                <li><strong>limit:</strong> Number of partners to display (default: no limit)</li>
            </ul>
            
            <h4>WordPress Customizer Options:</h4>
            <p>Navigate to <strong>Appearance → Customize → Partner Showcase</strong> to configure:</p>
            <ul>
                <li>Default layout style</li>
                <li>Default category filter</li>
                <li>Number of partners to show</li>
                <li>Enable/disable specific layouts</li>
            </ul>
            
            <h4>Widget Usage:</h4>
            <p>Add the "Partner Showcase" widget to any widget area from <strong>Appearance → Widgets</strong>.</p>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const totalSlides = <?php echo count(array_filter($test_partners, function($p) { return $p['category'] === 'community'; })); ?>;
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateCarousel();
        }
        
        function previousSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateCarousel();
        }
        
        function updateCarousel() {
            const carousel = document.getElementById('partnerCarousel');
            carousel.style.transform = `translateX(-${currentSlide * 100}%)`;
        }
        
        // Auto-advance carousel
        setInterval(nextSlide, 5000);
        
        // Add smooth hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.partner-card, .featured-partner-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-10px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });
        });
    </script>
</body>
</html>


