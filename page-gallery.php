<?php
/**
 * Template Name: Gallery Page
 *
 * @package KiliSmile
 * @version 1.0.0
 */

get_header(); 

// Get gallery items and settings
$gallery_items = kilismile_get_gallery_items();
$gallery_categories = kilismile_get_gallery_categories();
$gallery_stats = array(
    'total_images' => count($gallery_items),
    'total_categories' => count($gallery_categories)
);

// Gallery settings from theme options
$items_per_page = get_option('kilismile_gallery_items_per_page', 12);
$lightbox_enabled = get_option('kilismile_gallery_lightbox_enabled', true);
$layout = get_option('kilismile_gallery_layout', 'grid');

// Calculate display items for initial load
$initial_items = array_slice($gallery_items, 0, $items_per_page);
$has_more_items = count($gallery_items) > $items_per_page;

?>

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
            <!-- View Mode Toggle -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 20px;">
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="font-weight: 600; color: var(--text-primary); margin-right: 10px;"><?php _e('View:', 'kilismile'); ?></span>
                    <button onclick="setViewMode('grid')" id="grid-view-btn" class="view-mode-btn active" 
                            style="padding: 8px 16px; border: 2px solid var(--primary-green); background: var(--primary-green); color: white; border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 0.9rem;">
                        <i class="fas fa-th" style="margin-right: 5px;"></i><?php _e('Grid', 'kilismile'); ?>
                    </button>
                    <button onclick="setViewMode('preview')" id="preview-view-btn" class="view-mode-btn" 
                            style="padding: 8px 16px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 20px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; font-size: 0.9rem;">
                        <i class="fas fa-list" style="margin-right: 5px;"></i><?php _e('Preview', 'kilismile'); ?>
                    </button>
                </div>
                <div style="font-size: 0.9rem; color: var(--text-secondary);">
                    <span id="gallery-count"><?php echo count($gallery_items); ?></span> <?php _e('images', 'kilismile'); ?>
                </div>
            </div>
            
            <!-- Category Filters -->
            <div style="display: flex; justify-content: center; flex-wrap: wrap; gap: 15px;">
                <button onclick="filterGallery('all')" class="filter-btn active" data-filter="all" 
                        style="padding: 12px 25px; border: 2px solid var(--primary-green); background: var(--primary-green); color: white; border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                    <?php printf(__('All Photos (%d)', 'kilismile'), $gallery_stats['total_images']); ?>
                </button>
                <?php 
                $category_labels = array(
                    'healthcare' => __('Healthcare', 'kilismile'),
                    'education' => __('Education', 'kilismile'),
                    'community' => __('Community', 'kilismile'),
                    'events' => __('Events', 'kilismile'),
                    'volunteers' => __('Volunteers', 'kilismile'),
                    'outreach' => __('Outreach', 'kilismile'),
                    'training' => __('Training', 'kilismile'),
                    'awareness' => __('Awareness', 'kilismile'),
                );
                
                foreach ($gallery_categories as $category) : ?>
                    <button onclick="filterGallery('<?php echo esc_attr($category['name']); ?>')" 
                            class="filter-btn" data-filter="<?php echo esc_attr($category['name']); ?>"
                            style="padding: 12px 25px; border: 2px solid var(--primary-green); background: transparent; color: var(--primary-green); border-radius: 25px; cursor: pointer; font-weight: 600; transition: all 0.3s ease;">
                        <?php printf('%s (%d)', esc_html($category['label']), $category['count']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-grid" style="padding: 80px 0;">
        <div class="container">
            <?php
            // Force migration reset if requested
            if (current_user_can('manage_options') && isset($_GET['reset_gallery'])) {
                delete_option('kilismile_gallery_migrated');
                for ($i = 1; $i <= 20; $i++) {
                    delete_option("kilismile_gallery_image_{$i}");
                    delete_option("kilismile_gallery_title_{$i}");
                    delete_option("kilismile_gallery_description_{$i}");
                    delete_option("kilismile_gallery_category_{$i}");
                    delete_option("kilismile_gallery_featured_{$i}");
                }
                // Trigger migration
                kilismile_migrate_gallery_data();
                echo '<div style="background: #d4edda; color: #155724; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #c3e6cb;"><strong>Gallery data reset and migrated successfully!</strong> <a href="' . remove_query_arg('reset_gallery') . '">Refresh page</a></div>';
            }
            
            // Debug: Show gallery data if admin and debug parameter is set
            if (current_user_can('manage_options') && isset($_GET['debug_gallery'])) {
                $migration_status = get_option('kilismile_gallery_migrated', false);
                echo '<div style="background: #f0f0f0; padding: 20px; margin: 20px; border-radius: 5px; font-family: monospace;">';
                echo '<h3>Gallery Debug Info:</h3>';
                echo '<p><strong>Migration Status:</strong> ' . ($migration_status ? 'Complete' : 'Pending') . '</p>';
                echo '<p><strong>Total Items Found:</strong> ' . count($gallery_items) . '</p>';
                echo '<p><strong>Initial Items:</strong> ' . count($initial_items) . '</p>';
                echo '<p><strong>Has More Items:</strong> ' . ($has_more_items ? 'Yes' : 'No') . '</p>';
                echo '<p><strong>Gallery Stats:</strong></p><pre>' . print_r($gallery_stats, true) . '</pre>';
                echo '<p><strong>Settings:</strong> Items per page: ' . $items_per_page . ', Lightbox: ' . ($lightbox_enabled ? 'Yes' : 'No') . ', Layout: ' . $layout . '</p>';
                if (!empty($gallery_items)) {
                    echo '<p><strong>First Item Data:</strong></p><pre>' . print_r($gallery_items[0], true) . '</pre>';
                }
                // Show sample of options data
                echo '<p><strong>Sample Options Data:</strong></p>';
                for ($i = 1; $i <= 3; $i++) {
                    echo '<p>Item ' . $i . ': Image=' . get_option("kilismile_gallery_image_{$i}") . ', Title=' . get_option("kilismile_gallery_title_{$i}") . '</p>';
                }
                echo '<p><strong>Actions:</strong> <a href="' . add_query_arg('reset_gallery', '1') . '">Reset & Migrate Gallery Data</a></p>';
                echo '</div>';
            }
            ?>
            
            <!-- Grid Layout Container -->
            <div id="photo-gallery" class="gallery-grid-layout" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px; margin-bottom: 60px;">
                <?php
                // If no gallery items found, show placeholder message
                if (empty($gallery_items)) : ?>
                    <div style="text-align: center; padding: 60px 20px; color: var(--text-secondary);">
                        <h3><?php _e('No Gallery Images Found', 'kilismile'); ?></h3>
                        <p><?php _e('Check back soon for inspiring photos from our community programs and activities.', 'kilismile'); ?></p>
                    </div>
                <?php else :
                    foreach ($initial_items as $item) : ?>

                    <div class="gallery-item" data-category="<?php echo esc_attr($item['category']); ?>" 
                         data-id="<?php echo esc_attr($item['id']); ?>"
                         style="border-radius: 15px; overflow: hidden; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; <?php echo $item['featured'] ? 'order: -1;' : ''; ?>"
                         onclick="<?php echo $lightbox_enabled ? 'openLightbox(' . $item['id'] . ')' : ''; ?>">
                        <div class="gallery-image" style="height: 250px; background: url('<?php echo esc_url($item['image_url']); ?>') center/cover; position: relative; overflow: hidden;">
                            <?php if ($item['featured']) : ?>
                                <div style="position: absolute; top: 15px; left: 15px; background: var(--accent-orange); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;">
                                    <i class="fas fa-star" style="margin-right: 5px;"></i><?php _e('Featured', 'kilismile'); ?>
                                </div>
                            <?php endif; ?>
                            <div class="gallery-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(45, 90, 65, 0.8); opacity: 0; transition: all 0.3s ease; display: flex; align-items: flex-end; padding: 20px;">
                                <div style="color: white;">
                                    <h3 style="font-size: 1.1rem; margin-bottom: 5px;"><?php echo esc_html($item['title']); ?></h3>
                                    <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;"><?php echo esc_html($item['description']); ?></p>
                                </div>
                            </div>
                            <div style="position: absolute; top: 15px; right: 15px; background: var(--primary-green); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;">
                                <?php 
                                $category_labels = array(
                                    'healthcare' => __('Healthcare', 'kilismile'),
                                    'education' => __('Education', 'kilismile'),
                                    'community' => __('Community', 'kilismile'),
                                    'events' => __('Events', 'kilismile'),
                                    'volunteers' => __('Volunteers', 'kilismile'),
                                    'outreach' => __('Outreach', 'kilismile'),
                                    'training' => __('Training', 'kilismile'),
                                    'awareness' => __('Awareness', 'kilismile'),
                                );
                                echo esc_html($category_labels[$item['category']] ?? ucfirst($item['category'])); 
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; 
                endif; ?>
            </div>
            
            <!-- Preview Layout Container -->
            <div id="photo-preview" class="gallery-preview-layout" style="display: none; margin-bottom: 60px;">
                <!-- Preview items will be populated by JavaScript -->
            </div>
            
            <?php if ($has_more_items && !empty($gallery_items)) : ?>
                <!-- Load More Button -->
                <div style="text-align: center; margin-top: 40px;">
                    <button id="load-more-btn" onclick="loadMoreGallery()" 
                            style="padding: 15px 40px; background: var(--primary-green); color: white; border: none; border-radius: 30px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fas fa-images" style="margin-right: 8px;" aria-hidden="true"></i>
                        <span id="load-more-text"><?php _e('Load More Photos', 'kilismile'); ?></span>
                        <i id="load-more-spinner" class="fas fa-spinner fa-spin" style="display: none; margin-left: 10px;"></i>
                    </button>
                    <div id="load-more-status" style="margin-top: 10px; color: var(--text-secondary); font-size: 0.9rem;"></div>
                </div>
            <?php endif; ?>
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
                        <span class="counter" data-count="<?php echo $gallery_stats['total_categories']; ?>"><?php echo $gallery_stats['total_categories']; ?></span>
                    </div>
                    <p style="font-size: 1.1rem; opacity: 0.9;"><?php _e('Gallery Categories', 'kilismile'); ?></p>
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
        
        <!-- View Mode Toggle -->
        <div style="position: absolute; top: 20px; left: 20px; z-index: 10001;">
            <button onclick="toggleLightboxMode()" id="lightbox-mode-btn" style="background: rgba(255,255,255,0.2); border: none; color: white; font-size: 1rem; padding: 10px 15px; cursor: pointer; border-radius: 25px; backdrop-filter: blur(10px);">
                <i class="fas fa-expand" aria-hidden="true"></i> <span id="lightbox-mode-text">Full Size</span>
            </button>
        </div>
        
        <!-- Image Container -->
        <div id="lightbox-content" style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div id="lightbox-image-container" style="flex: 1; display: flex; align-items: center; justify-content: center; width: 100%; position: relative; overflow: hidden;">
                <img id="lightbox-image" style="max-width: 95%; max-height: 85vh; object-fit: contain; border-radius: 10px; cursor: zoom-in; transition: all 0.3s ease;" onclick="toggleImageZoom()">
            </div>
            <div id="lightbox-info" style="color: white; padding: 20px; max-width: 800px; text-align: center; background: rgba(0,0,0,0.5); border-radius: 10px; margin: 20px; backdrop-filter: blur(10px);">
                <h3 id="lightbox-title" style="font-size: 1.5rem; margin-bottom: 10px; font-weight: 600;"></h3>
                <p id="lightbox-description" style="font-size: 1rem; opacity: 0.9; line-height: 1.6; margin: 0;"></p>
                <div id="lightbox-meta" style="margin-top: 15px; font-size: 0.9rem; opacity: 0.7; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                    <span id="lightbox-category"></span>
                    <span id="lightbox-dimensions"></span>
                    <span id="lightbox-position"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Gallery data and state
const galleryData = <?php echo json_encode($gallery_items); ?>;
const gallerySettings = {
    itemsPerPage: <?php echo intval($items_per_page); ?>,
    lightboxEnabled: <?php echo $lightbox_enabled ? 'true' : 'false'; ?>,
    layout: '<?php echo esc_js($layout); ?>',
    totalItems: <?php echo count($gallery_items); ?>,
    nonce: '<?php echo wp_create_nonce('kilismile_gallery_nonce'); ?>'
};

let currentImageIndex = 0;
let filteredGallery = galleryData;
let currentFilter = 'all';
let currentPage = 1;
let isLoading = false;
let currentViewMode = 'grid';
let lightboxMode = 'fit'; // 'fit' or 'full'
let imageZoomed = false;

// View mode functionality
function setViewMode(mode) {
    currentViewMode = mode;
    
    // Update view mode buttons
    document.querySelectorAll('.view-mode-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.style.background = 'transparent';
        btn.style.color = 'var(--primary-green)';
    });
    
    const activeBtn = document.getElementById(mode + '-view-btn');
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.style.background = 'var(--primary-green)';
        activeBtn.style.color = 'white';
    }
    
    // Switch layouts
    const gridContainer = document.getElementById('photo-gallery');
    const previewContainer = document.getElementById('photo-preview');
    
    if (mode === 'grid') {
        gridContainer.style.display = 'grid';
        previewContainer.style.display = 'none';
    } else if (mode === 'preview') {
        gridContainer.style.display = 'none';
        previewContainer.style.display = 'block';
        populatePreviewLayout();
    }
}

// Populate preview layout
function populatePreviewLayout() {
    const previewContainer = document.getElementById('photo-preview');
    if (!previewContainer) return;
    
    // Get currently visible items based on filter
    const visibleItems = filteredGallery.slice(0, (currentPage * gallerySettings.itemsPerPage));
    
    previewContainer.innerHTML = '';
    
    visibleItems.forEach((item, index) => {
        const previewItem = createPreviewItem(item, index);
        previewContainer.appendChild(previewItem);
    });
}

// Create preview item HTML
function createPreviewItem(item, index) {
    const div = document.createElement('div');
    div.className = 'preview-item';
    div.setAttribute('data-category', item.category);
    div.setAttribute('data-id', item.id);
    div.style.cssText = `
        display: flex;
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        opacity: 0;
        transform: translateY(20px);
    `;
    
    const categoryLabels = {
        'healthcare': '<?php _e('Healthcare', 'kilismile'); ?>',
        'education': '<?php _e('Education', 'kilismile'); ?>',
        'community': '<?php _e('Community', 'kilismile'); ?>',
        'events': '<?php _e('Events', 'kilismile'); ?>',
        'volunteers': '<?php _e('Volunteers', 'kilismile'); ?>',
        'outreach': '<?php _e('Outreach', 'kilismile'); ?>',
        'training': '<?php _e('Training', 'kilismile'); ?>',
        'awareness': '<?php _e('Awareness', 'kilismile'); ?>'
    };
    
    if (gallerySettings.lightboxEnabled) {
        div.setAttribute('onclick', `openLightbox(${item.id})`);
    }
    
    div.innerHTML = `
        <div style="width: 300px; height: 200px; flex-shrink: 0; background: url('${item.image_url}') center/cover; position: relative;">
            ${item.featured ? '<div style="position: absolute; top: 10px; left: 10px; background: var(--accent-orange); color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;"><i class="fas fa-star" style="margin-right: 3px;"></i><?php _e('Featured', 'kilismile'); ?></div>' : ''}
            <div style="position: absolute; top: 10px; right: 10px; background: var(--primary-green); color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;">
                ${categoryLabels[item.category] || item.category.charAt(0).toUpperCase() + item.category.slice(1)}
            </div>
        </div>
        <div style="flex: 1; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <h3 style="font-size: 1.3rem; margin-bottom: 10px; color: var(--dark-green); font-weight: 600;">${item.title}</h3>
                <p style="color: var(--text-secondary); line-height: 1.6; margin-bottom: 15px;">${item.description}</p>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem; color: var(--text-secondary);">
                <span><?php _e('Click to view full image', 'kilismile'); ?></span>
                <i class="fas fa-external-link-alt" style="color: var(--primary-green);"></i>
            </div>
        </div>
    `;
    
    // Animate in with delay
    setTimeout(() => {
        div.style.opacity = '1';
        div.style.transform = 'translateY(0)';
    }, index * 100);
    
    // Add hover effect
    div.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 8px 25px rgba(0,0,0,0.15)';
    });
    
    div.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
    });
    
    return div;
}

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
    
    // Update preview layout if in preview mode
    if (currentViewMode === 'preview') {
        populatePreviewLayout();
    }
    
    // Update gallery count
    updateGalleryCount();
}

// Update gallery count display
function updateGalleryCount() {
    const countElement = document.getElementById('gallery-count');
    if (countElement) {
        const visibleCount = currentFilter === 'all' ? galleryData.length : filteredGallery.length;
        countElement.textContent = visibleCount;
    }
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
    // Reset lightbox state
    lightboxMode = 'fit';
    imageZoomed = false;
    
    const img = document.getElementById('lightbox-image');
    const container = document.getElementById('lightbox-image-container');
    
    // Reset image styles
    img.style.transform = 'scale(1)';
    img.style.cursor = 'zoom-in';
    img.style.maxWidth = '95%';
    img.style.maxHeight = '85vh';
    
    // Reset container
    container.style.overflow = 'hidden';
    container.style.cursor = 'default';
    container.scrollLeft = 0;
    container.scrollTop = 0;
    
    // Reset mode button
    const btn = document.getElementById('lightbox-mode-btn');
    const text = document.getElementById('lightbox-mode-text');
    const icon = btn.querySelector('i');
    
    icon.className = 'fas fa-expand';
    text.textContent = 'Full Size';
    btn.title = 'Show full size';
    
    // Hide modal
    document.getElementById('lightbox-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function showLightboxImage(imageData) {
    const img = document.getElementById('lightbox-image');
    const container = document.getElementById('lightbox-image-container');
    
    // Reset zoom state
    imageZoomed = false;
    img.style.cursor = 'zoom-in';
    img.style.transform = 'scale(1)';
    
    // Set image source
    img.src = imageData.image_url_full || imageData.image_url;
    
    // Update info
    document.getElementById('lightbox-title').textContent = imageData.title;
    document.getElementById('lightbox-description').textContent = imageData.description;
    
    // Update meta information
    const categoryLabels = {
        'healthcare': '<?php _e('Healthcare', 'kilismile'); ?>',
        'education': '<?php _e('Education', 'kilismile'); ?>',
        'community': '<?php _e('Community', 'kilismile'); ?>',
        'events': '<?php _e('Events', 'kilismile'); ?>',
        'volunteers': '<?php _e('Volunteers', 'kilismile'); ?>',
        'outreach': '<?php _e('Outreach', 'kilismile'); ?>',
        'training': '<?php _e('Training', 'kilismile'); ?>',
        'awareness': '<?php _e('Awareness', 'kilismile'); ?>'
    };
    
    document.getElementById('lightbox-category').textContent = categoryLabels[imageData.category] || imageData.category;
    document.getElementById('lightbox-position').textContent = `${currentImageIndex + 1} of ${filteredGallery.length}`;
    
    // Load image to get dimensions
    const tempImg = new Image();
    tempImg.onload = function() {
        document.getElementById('lightbox-dimensions').textContent = `${this.width} × ${this.height}px`;
    };
    tempImg.src = imageData.image_url_full || imageData.image_url;
    
    // Apply current mode
    applyLightboxMode();
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % filteredGallery.length;
    showLightboxImage(filteredGallery[currentImageIndex]);
}

function previousImage() {
    currentImageIndex = (currentImageIndex - 1 + filteredGallery.length) % filteredGallery.length;
    showLightboxImage(filteredGallery[currentImageIndex]);
}

// Toggle between fit and full-size display modes
function toggleLightboxMode() {
    lightboxMode = lightboxMode === 'fit' ? 'full' : 'fit';
    applyLightboxMode();
    
    const btn = document.getElementById('lightbox-mode-btn');
    const text = document.getElementById('lightbox-mode-text');
    const icon = btn.querySelector('i');
    
    if (lightboxMode === 'full') {
        icon.className = 'fas fa-compress';
        text.textContent = 'Fit Screen';
        btn.title = 'Fit to screen';
    } else {
        icon.className = 'fas fa-expand';
        text.textContent = 'Full Size';
        btn.title = 'Show full size';
    }
}

// Apply the current display mode
function applyLightboxMode() {
    const img = document.getElementById('lightbox-image');
    const container = document.getElementById('lightbox-image-container');
    
    if (lightboxMode === 'full') {
        img.style.maxWidth = 'none';
        img.style.maxHeight = 'none';
        img.style.width = 'auto';
        img.style.height = 'auto';
        container.style.overflow = 'auto';
        container.style.cursor = 'grab';
        
        // Enable dragging for full-size images
        makeDraggable(container, img);
    } else {
        img.style.maxWidth = '95%';
        img.style.maxHeight = '85vh';
        img.style.width = 'auto';
        img.style.height = 'auto';
        container.style.overflow = 'hidden';
        container.style.cursor = 'default';
        
        // Reset position
        img.style.transform = imageZoomed ? 'scale(2)' : 'scale(1)';
    }
}

// Toggle image zoom
function toggleImageZoom() {
    const img = document.getElementById('lightbox-image');
    
    if (lightboxMode === 'fit') {
        imageZoomed = !imageZoomed;
        
        if (imageZoomed) {
            img.style.transform = 'scale(2)';
            img.style.cursor = 'zoom-out';
        } else {
            img.style.transform = 'scale(1)';
            img.style.cursor = 'zoom-in';
        }
    }
}

// Make image draggable in full-size mode
function makeDraggable(container, img) {
    let isDragging = false;
    let startX, startY, scrollLeft, scrollTop;
    
    container.style.cursor = 'grab';
    
    container.onmousedown = function(e) {
        isDragging = true;
        container.style.cursor = 'grabbing';
        startX = e.pageX - container.offsetLeft;
        startY = e.pageY - container.offsetTop;
        scrollLeft = container.scrollLeft;
        scrollTop = container.scrollTop;
        e.preventDefault();
    };
    
    container.onmouseleave = function() {
        isDragging = false;
        container.style.cursor = 'grab';
    };
    
    container.onmouseup = function() {
        isDragging = false;
        container.style.cursor = 'grab';
    };
    
    container.onmousemove = function(e) {
        if (!isDragging) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const y = e.pageY - container.offsetTop;
        const walkX = (x - startX) * 2;
        const walkY = (y - startY) * 2;
        container.scrollLeft = scrollLeft - walkX;
        container.scrollTop = scrollTop - walkY;
    };
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (document.getElementById('lightbox-modal').style.display === 'block') {
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') previousImage();
        if (e.key === 'f' || e.key === 'F') toggleLightboxMode();
        if (e.key === 'z' || e.key === 'Z') toggleImageZoom();
        if (e.key === ' ') {
            e.preventDefault();
            toggleImageZoom();
        }
    }
});

// Load more photos functionality - updated for dynamic content
function loadMoreGallery() {
    if (isLoading) return;
    
    isLoading = true;
    currentPage++;
    
    // Update button state
    const loadMoreBtn = document.getElementById('load-more-btn');
    const loadMoreText = document.getElementById('load-more-text');
    const loadMoreSpinner = document.getElementById('load-more-spinner');
    const loadMoreStatus = document.getElementById('load-more-status');
    
    if (loadMoreBtn) {
        loadMoreBtn.disabled = true;
        if (loadMoreText) loadMoreText.textContent = '<?php _e('Loading...', 'kilismile'); ?>';
        if (loadMoreSpinner) loadMoreSpinner.style.display = 'inline-block';
    }
    
    // Make AJAX request
    const formData = new FormData();
    formData.append('action', 'kilismile_load_more_gallery');
    formData.append('page', currentPage);
    formData.append('category', currentFilter);
    formData.append('nonce', gallerySettings.nonce);
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.items.length > 0) {
            const galleryContainer = document.getElementById('photo-gallery');
            
            // Add new items to gallery
            data.data.items.forEach(item => {
                const galleryItem = createGalleryItem(item);
                galleryContainer.appendChild(galleryItem);
                
                // Add to galleryData
                galleryData.push(item);
            });
            
            // Update filtered gallery
            if (currentFilter === 'all') {
                filteredGallery = galleryData;
            } else {
                filteredGallery = galleryData.filter(item => item.category === currentFilter);
            }
            
            // Update preview layout if in preview mode
            if (currentViewMode === 'preview') {
                populatePreviewLayout();
            }
            
            // Update gallery count
            updateGalleryCount();
            
            // Update button state
            if (!data.data.has_more) {
                if (loadMoreBtn) loadMoreBtn.style.display = 'none';
                if (loadMoreStatus) {
                    loadMoreStatus.textContent = '<?php _e('All images loaded', 'kilismile'); ?>';
                }
            } else {
                if (loadMoreBtn) {
                    loadMoreBtn.disabled = false;
                    if (loadMoreText) loadMoreText.textContent = '<?php _e('Load More Photos', 'kilismile'); ?>';
                    if (loadMoreSpinner) loadMoreSpinner.style.display = 'none';
                }
            }
        } else {
            console.error('No more items or error:', data);
            if (loadMoreBtn) loadMoreBtn.style.display = 'none';
            if (loadMoreStatus) {
                loadMoreStatus.textContent = '<?php _e('All images loaded', 'kilismile'); ?>';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (loadMoreStatus) {
            loadMoreStatus.textContent = '<?php _e('Error loading images', 'kilismile'); ?>';
        }
    })
    .finally(() => {
        isLoading = false;
        if (loadMoreBtn && loadMoreBtn.style.display !== 'none') {
            loadMoreBtn.disabled = false;
            if (loadMoreText) loadMoreText.textContent = '<?php _e('Load More Photos', 'kilismile'); ?>';
            if (loadMoreSpinner) loadMoreSpinner.style.display = 'none';
        }
    });
}

// Create gallery item HTML
function createGalleryItem(item) {
    const div = document.createElement('div');
    div.className = 'gallery-item';
    div.setAttribute('data-category', item.category);
    div.setAttribute('data-id', item.id);
    div.style.cssText = 'border-radius: 15px; overflow: hidden; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: all 0.3s ease; cursor: pointer; opacity: 0; transform: translateY(20px);' + (item.featured ? ' order: -1;' : '');
    
    if (gallerySettings.lightboxEnabled) {
        div.setAttribute('onclick', `openLightbox(${item.id})`);
    }
    
    const categoryLabels = {
        'healthcare': '<?php _e('Healthcare', 'kilismile'); ?>',
        'education': '<?php _e('Education', 'kilismile'); ?>',
        'community': '<?php _e('Community', 'kilismile'); ?>',
        'events': '<?php _e('Events', 'kilismile'); ?>',
        'volunteers': '<?php _e('Volunteers', 'kilismile'); ?>',
        'outreach': '<?php _e('Outreach', 'kilismile'); ?>',
        'training': '<?php _e('Training', 'kilismile'); ?>',
        'awareness': '<?php _e('Awareness', 'kilismile'); ?>'
    };
    
    div.innerHTML = `
        <div class="gallery-image" style="height: 250px; background: url('${item.image_url}') center/cover; position: relative; overflow: hidden;">
            ${item.featured ? '<div style="position: absolute; top: 15px; left: 15px; background: var(--accent-orange); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600;"><i class="fas fa-star" style="margin-right: 5px;"></i><?php _e('Featured', 'kilismile'); ?></div>' : ''}
            <div class="gallery-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(45, 90, 65, 0.8); opacity: 0; transition: all 0.3s ease; display: flex; align-items: flex-end; padding: 20px;">
                <div style="color: white;">
                    <h3 style="font-size: 1.1rem; margin-bottom: 5px;">${item.title}</h3>
                    <p style="font-size: 0.9rem; opacity: 0.9; margin: 0;">${item.description}</p>
                </div>
            </div>
            <div style="position: absolute; top: 15px; right: 15px; background: var(--primary-green); color: white; padding: 5px 10px; border-radius: 15px; font-size: 0.8rem; font-weight: 600; text-transform: capitalize;">
                ${categoryLabels[item.category] || item.category.charAt(0).toUpperCase() + item.category.slice(1)}
            </div>
        </div>
    `;
    
    // Animate in
    setTimeout(() => {
        div.style.opacity = '1';
        div.style.transform = 'translateY(0)';
    }, 100);
    
    return div;
}

// Legacy function for compatibility  
function loadMorePhotos() {
    loadMoreGallery();
}

// Touch gesture support for mobile
function addTouchSupport() {
    const lightboxModal = document.getElementById('lightbox-modal');
    let startX = 0, startY = 0, endX = 0, endY = 0;
    
    lightboxModal.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    
    lightboxModal.addEventListener('touchend', function(e) {
        endX = e.changedTouches[0].clientX;
        endY = e.changedTouches[0].clientY;
        
        const deltaX = endX - startX;
        const deltaY = endY - startY;
        const threshold = 50;
        
        // Horizontal swipes for navigation
        if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > threshold) {
            if (deltaX > 0) {
                previousImage(); // Swipe right = previous
            } else {
                nextImage(); // Swipe left = next
            }
        }
        
        // Vertical swipe down to close
        if (deltaY > threshold && Math.abs(deltaX) < 100) {
            closeLightbox();
        }
    });
    
    // Double tap to zoom
    let tapCount = 0;
    const img = document.getElementById('lightbox-image');
    
    img.addEventListener('touchend', function(e) {
        tapCount++;
        if (tapCount === 1) {
            setTimeout(() => {
                if (tapCount === 1) {
                    // Single tap - do nothing special
                } else if (tapCount === 2) {
                    // Double tap - zoom
                    toggleImageZoom();
                }
                tapCount = 0;
            }, 300);
        }
        e.preventDefault();
    });
}

// Initialize gallery on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set initial gallery count
    updateGalleryCount();
    
    // Set default grid view
    setViewMode('grid');
    
    // Add touch support for mobile
    addTouchSupport();
    
    // Initialize counter animation when in view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                observer.unobserve(entry.target);
            }
        });
    });
    
    const statsSection = document.querySelector('.gallery-stats');
    if (statsSection) {
        observer.observe(statsSection);
    }
});

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
    
    /* Enhanced Lightbox Styles */
    #lightbox-modal {
        backdrop-filter: blur(5px);
    }
    
    #lightbox-image {
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    #lightbox-image:hover {
        box-shadow: 0 15px 40px rgba(0,0,0,0.7);
    }
    
    #lightbox-mode-btn:hover {
        background: rgba(255,255,255,0.3) !important;
        transform: translateY(-2px);
    }
    
    #lightbox-info {
        transition: all 0.3s ease;
    }
    
    /* Smooth scrolling for full-size mode */
    #lightbox-image-container {
        scroll-behavior: smooth;
    }
    
    /* Loading state for images */
    #lightbox-image {
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 50 50"><circle cx="25" cy="25" r="20" fill="none" stroke="%23fff" stroke-width="2" stroke-dasharray="31.416" stroke-dashoffset="31.416"><animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/><animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/></circle></svg>') center/50px no-repeat;
    }
    
    @media (max-width: 768px) {
        .gallery-filters > div {
            flex-direction: column;
            align-items: center;
        }
        
        .gallery-filters > div > div:first-child {
            flex-direction: column;
            text-align: center;
        }
        
        .filter-btn {
            width: 200px;
            text-align: center;
        }
        
        #photo-gallery {
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        }
        
        .preview-item {
            flex-direction: column !important;
        }
        
        .preview-item > div:first-child {
            width: 100% !important;
            height: 250px !important;
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
        
        #lightbox-mode-btn {
            font-size: 0.9rem !important;
            padding: 8px 12px !important;
        }
        
        #lightbox-mode-text {
            display: none;
        }
        
        #lightbox-info {
            margin: 10px !important;
            padding: 15px !important;
        }
        
        #lightbox-meta {
            flex-direction: column !important;
            gap: 10px !important;
            text-align: center;
        }
        
        #lightbox-image {
            max-height: 70vh !important;
        }
    }
</style>

<?php get_footer(); ?>


