<?php
/**
 * Template Name: Gallery Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <!-- Hero Section -->
    <section class="gallery-hero" style="background: var(--light-gray); color: var(--text-primary); padding: 100px 0 60px; text-align: center; border-bottom: 3px solid var(--primary-green);">
        <div class="container">
            <h1 style="font-size: 3rem; margin-bottom: 20px; color: var(--dark-green);">
                <?php _e('Photo Gallery', 'kilismile'); ?>
            </h1>
            <p style="font-size: 1.2rem; max-width: 600px; margin: 0 auto; line-height: 1.6; color: var(--text-secondary);">
                <?php _e('Capturing moments of hope, healing, and transformation in our communities.', 'kilismile'); ?>
            </p>
        </div>
    </section>

    <!-- Gallery Filters -->
    <section class="gallery-filters" style="background: white; padding: 40px 0; border-bottom: 1px solid #f0f0f0;">
        <div class="container">
            <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 15px;">
                <button onclick="filterGallery('all')" class="filter-btn active" data-filter="all" 
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: var(--primary-green); color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('All Photos', 'kilismile'); ?>
                </button>
                <button onclick="filterGallery('healthcare')" class="filter-btn" data-filter="healthcare"
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Healthcare', 'kilismile'); ?>
                </button>
                <button onclick="filterGallery('education')" class="filter-btn" data-filter="education"
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Education', 'kilismile'); ?>
                </button>
                <button onclick="filterGallery('community')" class="filter-btn" data-filter="community"
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Community', 'kilismile'); ?>
                </button>
                <button onclick="filterGallery('events')" class="filter-btn" data-filter="events"
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Events', 'kilismile'); ?>
                </button>
                <button onclick="filterGallery('volunteers')" class="filter-btn" data-filter="volunteers"
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php _e('Volunteers', 'kilismile'); ?>
                </button>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-grid" style="padding: 80px 0;">
        <div class="container">
            <div id="photo-gallery" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 60px;">
                <?php
                // Sample gallery data - in real implementation, this would come from custom post type or media gallery
                $gallery_items = array(
                    array(
                        'id' => 1,
                        'title' => 'Healthcare Outreach Program',
                        'category' => 'healthcare',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E8F5E8" width="400" height="300"/><circle fill="%234CAF50" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Healthcare</text></svg>',
                        'description' => 'Mobile clinic providing essential healthcare services to rural communities.'
                    ),
                    array(
                        'id' => 2,
                        'title' => 'School Health Education',
                        'category' => 'education',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E3F2FD" width="400" height="300"/><circle fill="%232196F3" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Education</text></svg>',
                        'description' => 'Teaching children about hygiene and health practices in local schools.'
                    ),
                    array(
                        'id' => 3,
                        'title' => 'Community Health Workshop',
                        'category' => 'community',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23FFF3E0" width="400" height="300"/><circle fill="%23FF9800" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Community</text></svg>',
                        'description' => 'Community members learning about preventive healthcare measures.'
                    ),
                    array(
                        'id' => 4,
                        'title' => 'Annual Fundraising Event',
                        'category' => 'events',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23F3E5F5" width="400" height="300"/><circle fill="%239C27B0" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Events</text></svg>',
                        'description' => 'Successful fundraising event bringing the community together.'
                    ),
                    array(
                        'id' => 5,
                        'title' => 'Volunteer Training Session',
                        'category' => 'volunteers',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E8F5E8" width="400" height="300"/><circle fill="%234CAF50" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Volunteers</text></svg>',
                        'description' => 'Training new volunteers for community health programs.'
                    ),
                    array(
                        'id' => 6,
                        'title' => 'Medical Equipment Donation',
                        'category' => 'healthcare',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E8F5E8" width="400" height="300"/><circle fill="%234CAF50" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Medical Equip</text></svg>',
                        'description' => 'Receiving and distributing donated medical equipment to health centers.'
                    ),
                    array(
                        'id' => 7,
                        'title' => 'Student Health Screening',
                        'category' => 'education',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E3F2FD" width="400" height="300"/><circle fill="%232196F3" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Health Screen</text></svg>',
                        'description' => 'Conducting health screenings for students in partner schools.'
                    ),
                    array(
                        'id' => 8,
                        'title' => 'Community Clean-up Day',
                        'category' => 'community',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23FFF3E0" width="400" height="300"/><circle fill="%23FF9800" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Clean-up</text></svg>',
                        'description' => 'Community volunteers working together to improve local environment.'
                    ),
                    array(
                        'id' => 9,
                        'title' => 'Health Awareness Campaign',
                        'category' => 'events',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23F3E5F5" width="400" height="300"/><circle fill="%239C27B0" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Awareness</text></svg>',
                        'description' => 'Public awareness campaign about preventive healthcare.'
                    ),
                    array(
                        'id' => 10,
                        'title' => 'International Volunteers',
                        'category' => 'volunteers',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E8F5E8" width="400" height="300"/><circle fill="%234CAF50" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Int\'l Volunteers</text></svg>',
                        'description' => 'International volunteers joining our community programs.'
                    ),
                    array(
                        'id' => 11,
                        'title' => 'Mother and Child Health',
                        'category' => 'healthcare',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E8F5E8" width="400" height="300"/><circle fill="%234CAF50" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Mother & Child</text></svg>',
                        'description' => 'Specialized care for mothers and children in rural areas.'
                    ),
                    array(
                        'id' => 12,
                        'title' => 'Digital Health Education',
                        'category' => 'education',
                        'image' => 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"><rect fill="%23E3F2FD" width="400" height="300"/><circle fill="%232196F3" cx="200" cy="150" r="50"/><text x="200" y="200" text-anchor="middle" fill="%23333" font-family="Arial" font-size="14">Digital Health</text></svg>',
                        'description' => 'Using technology to improve health education delivery.'
                    )
                );

                foreach ($gallery_items as $item) : ?>
                    <div class="gallery-item" data-category="<?php echo esc_attr($item['category']); ?>" 
                         style="border-radius: 15px; overflow: hidden; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer;"
                         onclick="openLightbox(<?php echo $item['id']; ?>)">
                        <div class="gallery-image" style="height: 250px; background: url('<?php echo esc_url($item['image']); ?>') center/cover; position: relative; overflow: hidden;">
                            <div class="gallery-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(45, 90, 65, 0.8); opacity: 0; transition: all 0.3s ease; display: flex; align-items: flex-end; padding: 20px;">
                                <div style="color: white;">
                                    <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?php echo esc_html($item['title']); ?></h3>
                                    <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;"><?php echo esc_html($item['description']); ?></p>
                                </div>
                            </div>
                            <div style="position: absolute; top: 15px; right: 15px; background: var(--primary-green); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;">
                                <?php echo esc_html($item['category']); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More Button -->
            <div style="text-align: center;">
                <button id="load-more-btn" onclick="loadMorePhotos()" 
                        style="padding: 15px 40px; background: var(--primary-green); color: white; border: none; border-radius: 30px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                    <i class="fas fa-images" style="margin-right: 8px;" aria-hidden="true"></i>
                    <?php _e('Load More Photos', 'kilismile'); ?>
                </button>
            </div>
        </div>
    </section>

    <!-- Impact Statistics -->
    <section class="gallery-stats" style="background: var(--primary-green); color: white; padding: 80px 0;">
        <div class="container">
            <h2 style="text-align: center; margin-bottom: 60px; font-size: 2.5rem;">
                <?php _e('Our Impact in Pictures', 'kilismile'); ?>
            </h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px;">
                <div style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: bold; margin-bottom: 10px;">
                        <span class="counter" data-count="500">0</span>+
                    </div>
                    <p style="font-size: 1.1rem; opacity: 0.9;"><?php _e('Photos Captured', 'kilismile'); ?></p>
                </div>
                
                <div style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: bold; margin-bottom: 10px;">
                        <span class="counter" data-count="50">0</span>+
                    </div>
                    <p style="font-size: 1.1rem; opacity: 0.9;"><?php _e('Events Documented', 'kilismile'); ?></p>
                </div>
                
                <div style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: bold; margin-bottom: 10px;">
                        <span class="counter" data-count="25">0</span>+
                    </div>
                    <p style="font-size: 1.1rem; opacity: 0.9;"><?php _e('Communities Reached', 'kilismile'); ?></p>
                </div>
                
                <div style="text-align: center;">
                    <div style="font-size: 3rem; font-weight: bold; margin-bottom: 10px;">
                        <span class="counter" data-count="1000">0</span>+
                    </div>
                    <p style="font-size: 1.1rem; opacity: 0.9;"><?php _e('Lives Touched', 'kilismile'); ?></p>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Lightbox Modal -->
<div id="lightbox-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; padding: 20px; box-sizing: border-box;">
    <div style="position: relative; height: 100%; display: flex; align-items: center; justify-content: center;">
        <!-- Close Button -->
        <button onclick="closeLightbox()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; color: white; font-size: 2rem; cursor: pointer; z-index: 10001;">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
        
        <!-- Navigation Arrows -->
        <button onclick="previousImage()" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: white; font-size: 2rem; padding: 15px 20px; cursor: pointer; border-radius: 50%; z-index: 10001;">
            <i class="fas fa-chevron-left" aria-hidden="true"></i>
        </button>
        
        <button onclick="nextImage()" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.2); border: none; color: white; font-size: 2rem; padding: 15px 20px; cursor: pointer; border-radius: 50%; z-index: 10001;">
            <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </button>
        
        <!-- Image Container -->
        <div id="lightbox-content" style="max-width: 90%; max-height: 90%; text-align: center;">
            <img id="lightbox-image" style="max-width: 100%; max-height: 80vh; object-fit: contain; border-radius: 10px;">
            <div id="lightbox-info" style="color: white; margin-top: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
                <h3 id="lightbox-title" style="font-size: 1.5rem; margin-bottom: 10px;"></h3>
                <p id="lightbox-description" style="font-size: 1rem; opacity: 0.9; line-height: 1.6;"></p>
            </div>
        </div>
    </div>
</div>

<script>
// Gallery data for lightbox
const galleryData = <?php echo json_encode($gallery_items); ?>;
let currentImageIndex = 0;
let filteredGallery = galleryData;

// Filter gallery functionality
function filterGallery(category) {
    // Update filter buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--primary-green)';
    });
    
    document.querySelector(`[data-filter="${category}"]`).classList.add('active');
    document.querySelector(`[data-filter="${category}"]`).style.background = 'var(--primary-green)';
    document.querySelector(`[data-filter="${category}"]`).style.color = 'white';
    
    // Filter gallery items
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, 100);
        } else {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
                item.style.display = 'none';
            }, 300);
        }
    });
    
    // Update filtered gallery for lightbox
    filteredGallery = category === 'all' ? galleryData : galleryData.filter(item => item.category === category);
}

// Lightbox functionality
function openLightbox(imageId) {
    const imageData = galleryData.find(item => item.id === imageId);
    if (imageData) {
        currentImageIndex = filteredGallery.findIndex(item => item.id === imageId);
        showLightboxImage(imageData);
        document.getElementById('lightbox-modal').style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox() {
    document.getElementById('lightbox-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function showLightboxImage(imageData) {
    document.getElementById('lightbox-image').src = imageData.image;
    document.getElementById('lightbox-title').textContent = imageData.title;
    document.getElementById('lightbox-description').textContent = imageData.description;
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % filteredGallery.length;
    showLightboxImage(filteredGallery[currentImageIndex]);
}

function previousImage() {
    currentImageIndex = (currentImageIndex - 1 + filteredGallery.length) % filteredGallery.length;
    showLightboxImage(filteredGallery[currentImageIndex]);
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (document.getElementById('lightbox-modal').style.display === 'block') {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') previousImage();
    }
});

// Load more photos functionality
function loadMorePhotos() {
    const button = document.getElementById('load-more-btn');
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php _e('Loading...', 'kilismile'); ?>';
    
    // Simulate loading delay
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> <?php _e('All Photos Loaded', 'kilismile'); ?>';
        button.disabled = true;
        button.style.opacity = '0.7';
    }, 1500);
}

// Counter animation
function animateCounters() {
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.dataset.count);
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                counter.textContent = target;
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current);
            }
        }, 16);
    });
}

// Intersection Observer for counter animation
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounters();
            observer.unobserve(entry.target);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const statsSection = document.querySelector('.gallery-stats');
    if (statsSection) {
        observer.observe(statsSection);
    }
    
    // Initialize gallery items with animation
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
    .gallery-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }
    
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
    }
    
    #load-more-btn:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
    }
    
    @media (max-width: 768px) {
        .gallery-filters > div {
            flex-direction: column;
            align-items: center;
        }
        
        .filter-btn {
            width: 200px;
            text-align: center;
        }
        
        #photo-gallery {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        
        .gallery-stats > div > div {
            grid-template-columns: repeat(2, 1fr);
        }
        
        #lightbox-modal {
            padding: 10px;
        }
        
        #lightbox-modal button {
            font-size: 1.5rem !important;
            padding: 10px 15px !important;
        }
    }
</style>

<?php get_footer(); ?>
