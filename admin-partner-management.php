<?php
/**
 * Advanced Partner Management Admin Interface
 * 
 * @package KiliSmile
 * @version 2.0.0
 */

// Add Enhanced Partner Management Menu
add_action('admin_menu', 'kilismile_add_enhanced_partner_management_menu');
function kilismile_add_enhanced_partner_management_menu() {
    add_menu_page(
        'Partner Management',
        'Partners',
        'manage_options',
        'partner-management',
        'kilismile_enhanced_partner_management_page',
        'dashicons-groups',
        25
    );
    
    add_submenu_page(
        'partner-management',
        'All Partners',
        'All Partners',
        'manage_options',
        'partner-management',
        'kilismile_enhanced_partner_management_page'
    );
    
    add_submenu_page(
        'partner-management',
        'Add New Partner',
        'Add New',
        'manage_options',
        'partner-add-new',
        'kilismile_add_partner_page'
    );
    
    add_submenu_page(
        'partner-management',
        'Partner Analytics',
        'Analytics',
        'manage_options',
        'partner-analytics',
        'kilismile_partner_analytics_page'
    );
    
    add_submenu_page(
        'partner-management',
        'Import/Export',
        'Import/Export',
        'manage_options',
        'partner-import-export',
        'kilismile_partner_import_export_page'
    );
}

// Enhanced script enqueuing
add_action('admin_enqueue_scripts', 'kilismile_enhanced_partner_management_scripts');
function kilismile_enhanced_partner_management_scripts($hook) {
    if (strpos($hook, 'partner-') === false && $hook !== 'toplevel_page_partner-management') {
        return;
    }
    
    // Core scripts
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui-sortable');
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '3.9.1', true);
    wp_enqueue_script('aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js', array(), '2.3.4', true);
    
    // Custom scripts
    wp_enqueue_script('kilismile-enhanced-partner-management', get_template_directory_uri() . '/assets/js/enhanced-partner-management.js', array('jquery', 'jquery-ui-sortable'), '2.0.0', true);
    
    // Styles
    wp_enqueue_style('aos', 'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css', array(), '2.3.4');
    wp_enqueue_style('kilismile-partner-admin', get_template_directory_uri() . '/assets/css/partner-admin.css', array(), '2.0.0');
    
    // Localized data
    wp_localize_script('kilismile-enhanced-partner-management', 'partnerAdmin', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kilismile_partner_nonce'),
        'strings' => array(
            'confirmDelete' => __('Are you sure you want to delete this partner?', 'kilismile'),
            'confirmBulkDelete' => __('Are you sure you want to delete selected partners?', 'kilismile'),
            'selectPartners' => __('Please select partners first.', 'kilismile'),
            'uploadSuccess' => __('Logo uploaded successfully!', 'kilismile'),
            'uploadError' => __('Upload failed. Please try again.', 'kilismile'),
            'saveSuccess' => __('Partner saved successfully!', 'kilismile'),
            'updateSuccess' => __('Partners updated successfully!', 'kilismile')
        )
    ));
}

// Enhanced main partner management page
function kilismile_enhanced_partner_management_page() {
    $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'all';
    $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $category_filter = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';
    $level_filter = isset($_GET['level']) ? sanitize_text_field($_GET['level']) : 'all';
    
    // Get partners with filters
    $args = array(
        'search' => $search,
        'category' => $category_filter,
        'partnership_level' => $level_filter,
        'status' => $current_tab === 'all' ? 'all' : $current_tab,
        'limit' => 20
    );
    
    $partners = kilismile_get_partners($args);
    $stats = kilismile_get_partner_stats();
    ?>
    
    <div class="wrap partner-management-wrap">
        <h1 class="wp-heading-inline">
            <i class="dashicons dashicons-groups"></i>
            Partner Management
            <span class="partner-count">(<?php echo $stats['total']; ?> total)</span>
        </h1>
        
        <a href="<?php echo admin_url('admin.php?page=partner-add-new'); ?>" class="page-title-action">
            <i class="dashicons dashicons-plus-alt"></i> Add New Partner
        </a>
        
        <hr class="wp-header-end">
        
        <!-- Stats Dashboard -->
        <div class="partner-stats-dashboard">
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-icon"><i class="dashicons dashicons-groups"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>Total Partners</p>
                    </div>
                </div>
                
                <div class="stat-card featured">
                    <div class="stat-icon"><i class="dashicons dashicons-star-filled"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['featured']; ?></h3>
                        <p>Featured Partners</p>
                    </div>
                </div>
                
                <div class="stat-card expiring">
                    <div class="stat-icon"><i class="dashicons dashicons-warning"></i></div>
                    <div class="stat-content">
                        <h3><?php echo $stats['expiring_soon']; ?></h3>
                        <p>Expiring Soon</p>
                    </div>
                </div>
                
                <div class="stat-card analytics">
                    <div class="stat-icon"><i class="dashicons dashicons-chart-line"></i></div>
                    <div class="stat-content">
                        <h3><?php echo array_sum(array_column($stats['most_clicked'], 'click_count')); ?></h3>
                        <p>Total Clicks</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filters and Search -->
        <div class="partner-filters-section">
            <div class="filters-row">
                <div class="search-box">
                    <input type="text" id="partner-search" placeholder="Search partners..." value="<?php echo esc_attr($search); ?>">
                    <button type="button" id="search-btn" class="button">
                        <i class="dashicons dashicons-search"></i>
                    </button>
                </div>
                
                <div class="filter-selects">
                    <select id="category-filter">
                        <option value="all">All Categories</option>
                        <option value="corporate" <?php selected($category_filter, 'corporate'); ?>>Corporate</option>
                        <option value="community" <?php selected($category_filter, 'community'); ?>>Community</option>
                        <option value="strategic" <?php selected($category_filter, 'strategic'); ?>>Strategic</option>
                        <option value="government" <?php selected($category_filter, 'government'); ?>>Government</option>
                        <option value="international" <?php selected($category_filter, 'international'); ?>>International</option>
                        <option value="academic" <?php selected($category_filter, 'academic'); ?>>Academic</option>
                    </select>
                    
                    <select id="level-filter">
                        <option value="all">All Levels</option>
                        <option value="platinum" <?php selected($level_filter, 'platinum'); ?>>Platinum</option>
                        <option value="gold" <?php selected($level_filter, 'gold'); ?>>Gold</option>
                        <option value="silver" <?php selected($level_filter, 'silver'); ?>>Silver</option>
                        <option value="bronze" <?php selected($level_filter, 'bronze'); ?>>Bronze</option>
                        <option value="basic" <?php selected($level_filter, 'basic'); ?>>Basic</option>
                    </select>
                </div>
                
                <div class="view-toggles">
                    <button type="button" id="grid-view" class="button view-btn active">
                        <i class="dashicons dashicons-grid-view"></i>
                    </button>
                    <button type="button" id="list-view" class="button view-btn">
                        <i class="dashicons dashicons-list-view"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Bulk Actions -->
        <div class="bulk-actions-section">
            <div class="bulk-actions-row">
                <label class="screen-reader-text" for="bulk-action-selector-top">Select bulk action</label>
                <select id="bulk-action-selector">
                    <option value="-1">Bulk Actions</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="feature">Feature</option>
                    <option value="unfeature">Remove from Featured</option>
                    <option value="delete">Delete</option>
                </select>
                
                <button type="button" id="apply-bulk-action" class="button">Apply</button>
                
                <div class="bulk-select-actions">
                    <button type="button" id="select-all" class="button">Select All</button>
                    <button type="button" id="select-none" class="button">Select None</button>
                </div>
            </div>
        </div>
        
        <!-- Partners Grid/List -->
        <div id="partners-container" class="partners-grid">
            <?php if (empty($partners)): ?>
                <div class="no-partners-found">
                    <div class="no-partners-icon">
                        <i class="dashicons dashicons-groups"></i>
                    </div>
                    <h3>No Partners Found</h3>
                    <p>Start building your network by adding your first partner.</p>
                    <a href="<?php echo admin_url('admin.php?page=partner-add-new'); ?>" class="button button-primary">
                        <i class="dashicons dashicons-plus-alt"></i> Add Your First Partner
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($partners as $partner): ?>
                    <?php echo kilismile_render_partner_admin_card($partner); ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Loading Overlay -->
        <div id="loading-overlay" class="loading-overlay" style="display: none;">
            <div class="loading-spinner">
                <i class="dashicons dashicons-update spin"></i>
                <p>Loading...</p>
            </div>
        </div>
    </div>
    
    <style>
    .partner-management-wrap {
        background: #f1f1f1;
        margin: 20px 0 0 -20px;
        padding: 20px;
        min-height: calc(100vh - 32px);
    }
    
    .partner-stats-dashboard {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    
    .stat-card {
        display: flex;
        align-items: center;
        padding: 20px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card.total { background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%); }
    .stat-card.featured { background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333; }
    .stat-card.expiring { background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%); }
    .stat-card.analytics { background: linear-gradient(135deg, #4ecdc4 0%, #44a08d 100%); }
    
    .stat-icon {
        font-size: 2rem;
        margin-right: 15px;
        opacity: 0.8;
    }
    
    .stat-content h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }
    
    .stat-content p {
        margin: 5px 0 0 0;
        opacity: 0.9;
    }
    
    .partner-filters-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin: 20px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .filters-row {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
    }
    
    .search-box {
        flex: 1;
        min-width: 300px;
        display: flex;
        gap: 10px;
    }
    
    .search-box input {
        flex: 1;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .filter-selects {
        display: flex;
        gap: 10px;
    }
    
    .filter-selects select {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .view-toggles {
        display: flex;
        gap: 5px;
    }
    
    .view-btn {
        padding: 8px 12px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 4px;
        cursor: pointer;
    }
    
    .view-btn.active {
        background: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }
    
    .bulk-actions-section {
        background: white;
        border-radius: 8px;
        padding: 15px 20px;
        margin: 20px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .bulk-actions-row {
        display: flex;
        gap: 15px;
        align-items: center;
    }
    
    .bulk-select-actions {
        margin-left: auto;
        display: flex;
        gap: 10px;
    }
    
    .partners-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 20px;
        margin: 20px 0;
    }
    
    .partners-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .no-partners-found {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .no-partners-icon {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
    
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    
    .loading-spinner {
        background: white;
        padding: 30px;
        border-radius: 8px;
        text-align: center;
    }
    
    .loading-spinner i {
        font-size: 2rem;
        color: #4CAF50;
        margin-bottom: 10px;
        display: block;
    }
    
    .spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    @media (max-width: 768px) {
        .filters-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-box {
            min-width: auto;
        }
        
        .partners-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    <?php
}

// Render individual partner admin card
function kilismile_render_partner_admin_card($partner) {
    $level_colors = array(
        'platinum' => '#e5e5e5',
        'gold' => '#ffd700',
        'silver' => '#c0c0c0',
        'bronze' => '#cd7f32',
        'basic' => '#4CAF50'
    );
    
    $status_colors = array(
        'active' => '#4CAF50',
        'inactive' => '#f44336',
        'pending' => '#ff9800',
        'expired' => '#9e9e9e'
    );
    
    $logo_url = !empty($partner['logo_url']) ? $partner['logo_url'] : '';
    $level_color = $level_colors[$partner['partnership_level']] ?? $level_colors['basic'];
    $status_color = $status_colors[$partner['status']] ?? $status_colors['active'];
    
    ob_start();
    ?>
    <div class="partner-admin-card" data-partner-id="<?php echo $partner['id']; ?>">
        <div class="partner-card-header">
            <div class="partner-select">
                <input type="checkbox" class="partner-checkbox" value="<?php echo $partner['id']; ?>">
            </div>
            
            <div class="partner-badges">
                <?php if ($partner['featured']): ?>
                    <span class="badge featured">
                        <i class="dashicons dashicons-star-filled"></i> Featured
                    </span>
                <?php endif; ?>
                
                <span class="badge level" style="background-color: <?php echo $level_color; ?>; color: <?php echo in_array($partner['partnership_level'], ['platinum', 'silver']) ? '#333' : '#fff'; ?>;">
                    <?php echo ucfirst($partner['partnership_level']); ?>
                </span>
                
                <span class="badge status" style="background-color: <?php echo $status_color; ?>;">
                    <?php echo ucfirst($partner['status']); ?>
                </span>
            </div>
            
            <div class="partner-actions">
                <div class="dropdown">
                    <button type="button" class="button action-toggle">
                        <i class="dashicons dashicons-menu"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="<?php echo admin_url('admin.php?page=partner-add-new&edit=' . $partner['id']); ?>">
                            <i class="dashicons dashicons-edit"></i> Edit
                        </a>
                        <?php if ($partner['status'] === 'active'): ?>
                            <a href="#" class="partner-action" data-action="deactivate" data-id="<?php echo $partner['id']; ?>">
                                <i class="dashicons dashicons-hidden"></i> Deactivate
                            </a>
                        <?php else: ?>
                            <a href="#" class="partner-action" data-action="activate" data-id="<?php echo $partner['id']; ?>">
                                <i class="dashicons dashicons-visibility"></i> Activate
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($partner['featured']): ?>
                            <a href="#" class="partner-action" data-action="unfeature" data-id="<?php echo $partner['id']; ?>">
                                <i class="dashicons dashicons-star-empty"></i> Unfeature
                            </a>
                        <?php else: ?>
                            <a href="#" class="partner-action" data-action="feature" data-id="<?php echo $partner['id']; ?>">
                                <i class="dashicons dashicons-star-filled"></i> Feature
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url($partner['website']); ?>" target="_blank" rel="noopener">
                            <i class="dashicons dashicons-external"></i> Visit Website
                        </a>
                        
                        <hr>
                        
                        <a href="#" class="partner-action delete" data-action="delete" data-id="<?php echo $partner['id']; ?>">
                            <i class="dashicons dashicons-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="partner-card-body">
            <div class="partner-logo">
                <?php if ($logo_url): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($partner['name']); ?>">
                <?php else: ?>
                    <div class="logo-placeholder">
                        <i class="dashicons dashicons-building"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="partner-info">
                <h3 class="partner-name"><?php echo esc_html($partner['name']); ?></h3>
                
                <div class="partner-meta">
                    <span class="meta-item">
                        <i class="dashicons dashicons-category"></i>
                        <?php echo ucfirst($partner['category']); ?>
                    </span>
                    
                    <span class="meta-item">
                        <i class="dashicons dashicons-businessman"></i>
                        <?php echo ucfirst(str_replace('_', ' ', $partner['partnership_type'])); ?>
                    </span>
                    
                    <?php if (!empty($partner['partnership_value'])): ?>
                    <span class="meta-item">
                        <i class="dashicons dashicons-money-alt"></i>
                        $<?php echo number_format($partner['partnership_value']); ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($partner['short_description'])): ?>
                <p class="partner-description">
                    <?php echo esc_html(wp_trim_words($partner['short_description'], 20)); ?>
                </p>
                <?php endif; ?>
                
                <div class="partner-stats">
                    <div class="stat-item">
                        <i class="dashicons dashicons-visibility"></i>
                        <span><?php echo $partner['click_count']; ?> clicks</span>
                    </div>
                    
                    <?php if ($partner['end_date']): ?>
                    <div class="stat-item">
                        <i class="dashicons dashicons-calendar-alt"></i>
                        <span>Expires: <?php echo date('M j, Y', strtotime($partner['end_date'])); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .partner-admin-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .partner-admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    
    .partner-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    
    .partner-badges {
        display: flex;
        gap: 8px;
        flex: 1;
        justify-content: center;
    }
    
    .badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }
    
    .badge.featured {
        background: #ffd700;
        color: #333;
    }
    
    .dropdown {
        position: relative;
    }
    
    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        min-width: 150px;
        z-index: 100;
        display: none;
    }
    
    .dropdown-menu a {
        display: block;
        padding: 8px 12px;
        text-decoration: none;
        color: #333;
        font-size: 0.9rem;
    }
    
    .dropdown-menu a:hover {
        background: #f5f5f5;
    }
    
    .dropdown-menu a.delete {
        color: #f44336;
    }
    
    .dropdown-menu hr {
        margin: 5px 0;
        border: none;
        border-top: 1px solid #eee;
    }
    
    .partner-card-body {
        padding: 20px;
    }
    
    .partner-logo {
        text-align: center;
        margin-bottom: 15px;
    }
    
    .partner-logo img {
        max-width: 120px;
        max-height: 60px;
        object-fit: contain;
    }
    
    .logo-placeholder {
        width: 120px;
        height: 60px;
        background: #f5f5f5;
        border: 2px dashed #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: #999;
        font-size: 1.5rem;
    }
    
    .partner-name {
        margin: 0 0 10px 0;
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        color: #333;
    }
    
    .partner-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
        justify-content: center;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.85rem;
        color: #666;
        background: #f8f9fa;
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    .partner-description {
        color: #666;
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 15px;
        text-align: center;
    }
    
    .partner-stats {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.8rem;
        color: #666;
    }
    </style>
    <?php
    
    return ob_get_clean();
}

// Add New Partner Page
function kilismile_add_partner_page() {
    $editing = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $partner = null;
    
    if ($editing) {
        $partner = kilismile_get_partner($editing);
        if (!$partner) {
            wp_die('Partner not found.');
        }
    }
    
    // Handle form submission
    if (isset($_POST['submit_partner'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'kilismile_partner_form')) {
            wp_die('Security check failed.');
        }
        
        $partner_data = array(
            'name' => sanitize_text_field($_POST['name']),
            'website' => esc_url_raw($_POST['website']),
            'description' => wp_kses_post($_POST['description']),
            'short_description' => sanitize_textarea_field($_POST['short_description']),
            'category' => sanitize_text_field($_POST['category']),
            'partnership_type' => sanitize_text_field($_POST['partnership_type']),
            'partnership_level' => sanitize_text_field($_POST['partnership_level']),
            'contact_person' => sanitize_text_field($_POST['contact_person']),
            'contact_email' => sanitize_email($_POST['contact_email']),
            'contact_phone' => sanitize_text_field($_POST['contact_phone']),
            'logo_url' => esc_url_raw($_POST['logo_url']),
            'logo_alt_url' => esc_url_raw($_POST['logo_alt_url']),
            'logo_position' => sanitize_text_field($_POST['logo_position']),
            'partnership_value' => floatval($_POST['partnership_value']),
            'start_date' => sanitize_text_field($_POST['start_date']) ?: null,
            'end_date' => sanitize_text_field($_POST['end_date']) ?: null,
            'featured' => isset($_POST['featured']) ? 1 : 0,
            'status' => sanitize_text_field($_POST['status']),
            'priority' => intval($_POST['priority']),
            'tags' => sanitize_text_field($_POST['tags'])
        );
        
        // Handle social media links
        $social_links = array();
        if (isset($_POST['social_links']) && is_array($_POST['social_links'])) {
            foreach ($_POST['social_links'] as $platform => $url) {
                if (!empty($url)) {
                    $social_links[$platform] = esc_url_raw($url);
                }
            }
        }
        $partner_data['social_links'] = json_encode($social_links);
        
        if ($editing) {
            $result = kilismile_update_partner($editing, $partner_data);
            $message = $result ? 'Partner updated successfully!' : 'Failed to update partner.';
        } else {
            $result = kilismile_add_partner($partner_data);
            $message = $result ? 'Partner added successfully!' : 'Failed to add partner.';
        }
        
        echo '<div class="notice notice-' . ($result ? 'success' : 'error') . ' is-dismissible"><p>' . $message . '</p></div>';
        
        if ($result && !$editing) {
            // Reset form after successful addition
            $partner = null;
        } elseif ($result && $editing) {
            // Refresh partner data after update
            $partner = kilismile_get_partner($editing);
        }
    }
    
    $social_platforms = array(
        'facebook' => 'Facebook',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok'
    );
    
    $partner_social = array();
    if ($partner && !empty($partner['social_links'])) {
        $partner_social = json_decode($partner['social_links'], true) ?: array();
    }
    ?>
    
    <div class="wrap partner-form-wrap">
        <h1>
            <i class="dashicons dashicons-<?php echo $editing ? 'edit' : 'plus-alt'; ?>"></i>
            <?php echo $editing ? 'Edit Partner' : 'Add New Partner'; ?>
        </h1>
        
        <form method="post" class="partner-form" id="partner-form">
            <?php wp_nonce_field('kilismile_partner_form'); ?>
            
            <div class="form-sections">
                <!-- Basic Information -->
                <div class="form-section">
                    <h2><i class="dashicons dashicons-info"></i> Basic Information</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Partner Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo $partner ? esc_attr($partner['name']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="website">Website URL *</label>
                            <input type="url" id="website" name="website" value="<?php echo $partner ? esc_attr($partner['website']) : ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="short_description">Short Description</label>
                        <textarea id="short_description" name="short_description" rows="3" placeholder="Brief description for cards and previews"><?php echo $partner ? esc_textarea($partner['short_description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Full Description</label>
                        <?php
                        wp_editor(
                            $partner ? $partner['description'] : '',
                            'description',
                            array(
                                'textarea_rows' => 8,
                                'media_buttons' => true,
                                'teeny' => false
                            )
                        );
                        ?>
                    </div>
                </div>
                
                <!-- Partnership Details -->
                <div class="form-section">
                    <h2><i class="dashicons dashicons-businessman"></i> Partnership Details</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="corporate" <?php selected($partner['category'] ?? '', 'corporate'); ?>>Corporate</option>
                                <option value="community" <?php selected($partner['category'] ?? '', 'community'); ?>>Community</option>
                                <option value="strategic" <?php selected($partner['category'] ?? '', 'strategic'); ?>>Strategic</option>
                                <option value="government" <?php selected($partner['category'] ?? '', 'government'); ?>>Government</option>
                                <option value="international" <?php selected($partner['category'] ?? '', 'international'); ?>>International</option>
                                <option value="academic" <?php selected($partner['category'] ?? '', 'academic'); ?>>Academic</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="partnership_type">Partnership Type</label>
                            <select id="partnership_type" name="partnership_type">
                                <option value="financial" <?php selected($partner['partnership_type'] ?? '', 'financial'); ?>>Financial</option>
                                <option value="strategic" <?php selected($partner['partnership_type'] ?? '', 'strategic'); ?>>Strategic</option>
                                <option value="media" <?php selected($partner['partnership_type'] ?? '', 'media'); ?>>Media</option>
                                <option value="technology" <?php selected($partner['partnership_type'] ?? '', 'technology'); ?>>Technology</option>
                                <option value="community" <?php selected($partner['partnership_type'] ?? '', 'community'); ?>>Community</option>
                                <option value="government" <?php selected($partner['partnership_type'] ?? '', 'government'); ?>>Government</option>
                                <option value="academic" <?php selected($partner['partnership_type'] ?? '', 'academic'); ?>>Academic</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="partnership_level">Partnership Level</label>
                            <select id="partnership_level" name="partnership_level">
                                <option value="basic" <?php selected($partner['partnership_level'] ?? '', 'basic'); ?>>Basic</option>
                                <option value="bronze" <?php selected($partner['partnership_level'] ?? '', 'bronze'); ?>>Bronze</option>
                                <option value="silver" <?php selected($partner['partnership_level'] ?? '', 'silver'); ?>>Silver</option>
                                <option value="gold" <?php selected($partner['partnership_level'] ?? '', 'gold'); ?>>Gold</option>
                                <option value="platinum" <?php selected($partner['partnership_level'] ?? '', 'platinum'); ?>>Platinum</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="partnership_value">Partnership Value ($)</label>
                            <input type="number" id="partnership_value" name="partnership_value" min="0" step="0.01" value="<?php echo $partner ? esc_attr($partner['partnership_value']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date" value="<?php echo $partner ? esc_attr($partner['start_date']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date" value="<?php echo $partner ? esc_attr($partner['end_date']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="form-section">
                    <h2><i class="dashicons dashicons-phone"></i> Contact Information</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_person">Contact Person</label>
                            <input type="text" id="contact_person" name="contact_person" value="<?php echo $partner ? esc_attr($partner['contact_person']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" id="contact_email" name="contact_email" value="<?php echo $partner ? esc_attr($partner['contact_email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_phone">Contact Phone</label>
                            <input type="tel" id="contact_phone" name="contact_phone" value="<?php echo $partner ? esc_attr($partner['contact_phone']) : ''; ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Logo & Media -->
                <div class="form-section">
                    <h2><i class="dashicons dashicons-format-image"></i> Logo & Media</h2>
                    
                    <div class="form-row">
                        <div class="form-group logo-upload-group">
                            <label for="logo_url">Primary Logo</label>
                            <div class="logo-upload-container">
                                <input type="hidden" id="logo_url" name="logo_url" value="<?php echo $partner ? esc_attr($partner['logo_url']) : ''; ?>">
                                <div class="logo-preview" id="logo-preview">
                                    <?php if ($partner && $partner['logo_url']): ?>
                                        <img src="<?php echo esc_url($partner['logo_url']); ?>" alt="Logo preview">
                                    <?php else: ?>
                                        <div class="no-logo">
                                            <i class="dashicons dashicons-format-image"></i>
                                            <p>No logo selected</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="logo-actions">
                                    <button type="button" id="upload-logo" class="button">Select Logo</button>
                                    <button type="button" id="remove-logo" class="button" style="<?php echo (!$partner || !$partner['logo_url']) ? 'display:none;' : ''; ?>">Remove</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group logo-upload-group">
                            <label for="logo_alt_url">Alternative Logo</label>
                            <div class="logo-upload-container">
                                <input type="hidden" id="logo_alt_url" name="logo_alt_url" value="<?php echo $partner ? esc_attr($partner['logo_alt_url']) : ''; ?>">
                                <div class="logo-preview" id="logo-alt-preview">
                                    <?php if ($partner && $partner['logo_alt_url']): ?>
                                        <img src="<?php echo esc_url($partner['logo_alt_url']); ?>" alt="Alt logo preview">
                                    <?php else: ?>
                                        <div class="no-logo">
                                            <i class="dashicons dashicons-format-image"></i>
                                            <p>No alt logo</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="logo-actions">
                                    <button type="button" id="upload-logo-alt" class="button">Select Alt Logo</button>
                                    <button type="button" id="remove-logo-alt" class="button" style="<?php echo (!$partner || !$partner['logo_alt_url']) ? 'display:none;' : ''; ?>">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="logo_position">Logo Position</label>
                        <select id="logo_position" name="logo_position">
                            <option value="center" <?php selected($partner['logo_position'] ?? '', 'center'); ?>>Center</option>
                            <option value="left" <?php selected($partner['logo_position'] ?? '', 'left'); ?>>Left</option>
                            <option value="right" <?php selected($partner['logo_position'] ?? '', 'right'); ?>>Right</option>
                        </select>
                    </div>
                </div>
                
                <!-- Social Media -->
                <div class="form-section">
                    <h2><i class="dashicons dashicons-share"></i> Social Media Links</h2>
                    
                    <div class="social-links-grid">
                        <?php foreach ($social_platforms as $platform => $label): ?>
                            <div class="form-group">
                                <label for="social_<?php echo $platform; ?>">
                                    <i class="dashicons dashicons-<?php echo $platform === 'twitter' ? 'twitter' : 'share'; ?>"></i>
                                    <?php echo $label; ?>
                                </label>
                                <input type="url" id="social_<?php echo $platform; ?>" name="social_links[<?php echo $platform; ?>]" value="<?php echo esc_attr($partner_social[$platform] ?? ''); ?>" placeholder="https://<?php echo $platform; ?>.com/...">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Settings -->
                <div class="form-section">
                    <h2><i class="dashicons dashicons-admin-generic"></i> Settings</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status">
                                <option value="active" <?php selected($partner['status'] ?? '', 'active'); ?>>Active</option>
                                <option value="inactive" <?php selected($partner['status'] ?? '', 'inactive'); ?>>Inactive</option>
                                <option value="pending" <?php selected($partner['status'] ?? '', 'pending'); ?>>Pending</option>
                                <option value="expired" <?php selected($partner['status'] ?? '', 'expired'); ?>>Expired</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="priority">Display Priority</label>
                            <input type="number" id="priority" name="priority" min="0" max="100" value="<?php echo $partner ? esc_attr($partner['priority']) : '50'; ?>">
                            <small>Higher numbers appear first (0-100)</small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tags">Tags</label>
                        <input type="text" id="tags" name="tags" value="<?php echo $partner ? esc_attr($partner['tags']) : ''; ?>" placeholder="Separate tags with commas">
                        <small>e.g., technology, innovation, local business</small>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="featured" value="1" <?php checked($partner['featured'] ?? 0, 1); ?>>
                            <strong>Featured Partner</strong>
                            <small>Featured partners appear prominently on the website</small>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" name="submit_partner" class="button button-primary button-large">
                    <i class="dashicons dashicons-<?php echo $editing ? 'update' : 'plus-alt'; ?>"></i>
                    <?php echo $editing ? 'Update Partner' : 'Add Partner'; ?>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=partner-management'); ?>" class="button button-secondary button-large">
                    <i class="dashicons dashicons-arrow-left-alt"></i>
                    Back to Partners
                </a>
            </div>
        </form>
    </div>
    
    <style>
    .partner-form-wrap {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background: #f1f1f1;
    }
    
    .partner-form {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .form-sections {
        display: grid;
        gap: 30px;
    }
    
    .form-section {
        border: 1px solid #e1e1e1;
        border-radius: 8px;
        padding: 20px;
        background: #fafafa;
    }
    
    .form-section h2 {
        margin: 0 0 20px 0;
        color: #333;
        font-size: 1.3rem;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 10px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group label {
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        outline: none;
    }
    
    .form-group small {
        margin-top: 5px;
        color: #666;
        font-size: 12px;
    }
    
    .checkbox-group label {
        flex-direction: row;
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }
    
    .checkbox-group input[type="checkbox"] {
        width: auto;
        margin: 0;
    }
    
    .logo-upload-container {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        transition: border-color 0.3s ease;
    }
    
    .logo-upload-container:hover {
        border-color: #4CAF50;
    }
    
    .logo-preview {
        margin-bottom: 15px;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .logo-preview img {
        max-width: 200px;
        max-height: 100px;
        object-fit: contain;
    }
    
    .no-logo {
        color: #999;
        font-size: 14px;
    }
    
    .no-logo i {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
    }
    
    .logo-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
    
    .social-links-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 1px solid #e1e1e1;
    }
    
    .button-large {
        padding: 12px 24px;
        font-size: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .social-links-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Logo upload functionality
        function setupLogoUpload(buttonId, previewId, inputId, removeId) {
            $('#' + buttonId).on('click', function(e) {
                e.preventDefault();
                
                var mediaUploader = wp.media({
                    title: 'Select Logo',
                    button: { text: 'Use This Logo' },
                    multiple: false,
                    library: { type: 'image' }
                });
                
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#' + inputId).val(attachment.url);
                    $('#' + previewId).html('<img src="' + attachment.url + '" alt="Logo preview">');
                    $('#' + removeId).show();
                });
                
                mediaUploader.open();
            });
            
            $('#' + removeId).on('click', function(e) {
                e.preventDefault();
                $('#' + inputId).val('');
                $('#' + previewId).html('<div class="no-logo"><i class="dashicons dashicons-format-image"></i><p>No logo selected</p></div>');
                $(this).hide();
            });
        }
        
        setupLogoUpload('upload-logo', 'logo-preview', 'logo_url', 'remove-logo');
        setupLogoUpload('upload-logo-alt', 'logo-alt-preview', 'logo_alt_url', 'remove-logo-alt');
    });
    </script>
    <?php
}

// Partner Analytics Page
function kilismile_partner_analytics_page() {
    $stats = kilismile_get_partner_stats();
    $most_clicked = kilismile_get_partners(array('orderby' => 'click_count', 'order' => 'DESC', 'limit' => 10));
    ?>
    
    <div class="wrap analytics-wrap">
        <h1>
            <i class="dashicons dashicons-chart-line"></i>
            Partner Analytics
        </h1>
        
        <div class="analytics-dashboard">
            <!-- Overview Stats -->
            <div class="analytics-section">
                <h2>Overview</h2>
                <div class="analytics-grid">
                    <div class="analytics-card">
                        <div class="card-header">
                            <h3>Total Partners</h3>
                            <i class="dashicons dashicons-groups"></i>
                        </div>
                        <div class="card-value"><?php echo $stats['total']; ?></div>
                    </div>
                    
                    <div class="analytics-card">
                        <div class="card-header">
                            <h3>Featured Partners</h3>
                            <i class="dashicons dashicons-star-filled"></i>
                        </div>
                        <div class="card-value"><?php echo $stats['featured']; ?></div>
                    </div>
                    
                    <div class="analytics-card">
                        <div class="card-header">
                            <h3>Total Clicks</h3>
                            <i class="dashicons dashicons-visibility"></i>
                        </div>
                        <div class="card-value"><?php echo array_sum(array_column($most_clicked, 'click_count')); ?></div>
                    </div>
                    
                    <div class="analytics-card">
                        <div class="card-header">
                            <h3>Expiring Soon</h3>
                            <i class="dashicons dashicons-warning"></i>
                        </div>
                        <div class="card-value"><?php echo $stats['expiring_soon']; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="analytics-section">
                <h2>Partnership Distribution</h2>
                <div class="charts-grid">
                    <div class="chart-container">
                        <h3>By Category</h3>
                        <canvas id="categoryChart"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3>By Partnership Level</h3>
                        <canvas id="levelChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Top Performers -->
            <div class="analytics-section">
                <h2>Top Performing Partners</h2>
                <div class="top-partners-table">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Partner</th>
                                <th>Category</th>
                                <th>Level</th>
                                <th>Clicks</th>
                                <th>Last Click</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($most_clicked as $partner): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html($partner['name']); ?></strong>
                                        <?php if ($partner['featured']): ?>
                                            <span class="featured-badge">Featured</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo ucfirst($partner['category']); ?></td>
                                    <td>
                                        <span class="level-badge level-<?php echo $partner['partnership_level']; ?>">
                                            <?php echo ucfirst($partner['partnership_level']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo $partner['click_count']; ?></td>
                                    <td>
                                        <?php 
                                        if ($partner['last_click']) {
                                            echo human_time_diff(strtotime($partner['last_click']), current_time('timestamp')) . ' ago';
                                        } else {
                                            echo 'Never';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    .analytics-wrap {
        background: #f1f1f1;
        margin: 20px 0 0 -20px;
        padding: 20px;
        min-height: calc(100vh - 32px);
    }
    
    .analytics-dashboard {
        max-width: 1200px;
    }
    
    .analytics-section {
        background: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .analytics-section h2 {
        margin: 0 0 20px 0;
        color: #333;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 10px;
    }
    
    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .analytics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 8px;
        transition: transform 0.3s ease;
    }
    
    .analytics-card:hover {
        transform: translateY(-5px);
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 1rem;
        opacity: 0.9;
    }
    
    .card-header i {
        font-size: 1.5rem;
        opacity: 0.7;
    }
    
    .card-value {
        font-size: 2.5rem;
        font-weight: 700;
        line-height: 1;
    }
    
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 30px;
    }
    
    .chart-container {
        text-align: center;
    }
    
    .chart-container h3 {
        margin-bottom: 20px;
        color: #333;
    }
    
    .chart-container canvas {
        max-height: 300px;
    }
    
    .top-partners-table {
        overflow-x: auto;
    }
    
    .featured-badge {
        background: #ffd700;
        color: #333;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-left: 10px;
    }
    
    .level-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }
    
    .level-platinum { background: #e5e5e5; color: #333; }
    .level-gold { background: #ffd700; color: #333; }
    .level-silver { background: #c0c0c0; color: #333; }
    .level-bronze { background: #cd7f32; }
    .level-basic { background: #4CAF50; }
    
    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
        
        .charts-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Get chart data via AJAX
        $.post(ajaxurl, {
            action: 'kilismile_get_analytics_data',
            nonce: '<?php echo wp_create_nonce("kilismile_analytics_nonce"); ?>'
        }, function(response) {
            if (response.success) {
                createCharts(response.data);
            }
        });
        
        function createCharts(data) {
            // Category Chart
            new Chart(document.getElementById('categoryChart'), {
                type: 'doughnut',
                data: {
                    labels: Object.keys(data.by_category),
                    datasets: [{
                        data: Object.values(data.by_category),
                        backgroundColor: [
                            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                            '#9966FF', '#FF9F40', '#FF6384', '#C7B42C'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Level Chart
            new Chart(document.getElementById('levelChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(data.by_level),
                    datasets: [{
                        label: 'Partners',
                        data: Object.values(data.by_level),
                        backgroundColor: [
                            '#e5e5e5', '#ffd700', '#c0c0c0', '#cd7f32', '#4CAF50'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
    </script>
    <?php
}

// Import/Export Page
function kilismile_partner_import_export_page() {
    // Handle export
    if (isset($_POST['export_partners'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'kilismile_export_partners')) {
            wp_die('Security check failed.');
        }
        
        $partners = kilismile_get_partners(array('limit' => -1));
        
        $filename = 'partners-export-' . date('Y-m-d-H-i-s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($partners, JSON_PRETTY_PRINT);
        exit;
    }
    
    // Handle import
    if (isset($_POST['import_partners']) && isset($_FILES['import_file'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'kilismile_import_partners')) {
            wp_die('Security check failed.');
        }
        
        $file = $_FILES['import_file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $content = file_get_contents($file['tmp_name']);
            $partners = json_decode($content, true);
            
            if ($partners && is_array($partners)) {
                $imported = 0;
                $skipped = 0;
                
                foreach ($partners as $partner_data) {
                    // Remove ID and timestamps for import
                    unset($partner_data['id'], $partner_data['created_at'], $partner_data['updated_at']);
                    
                    if (kilismile_add_partner($partner_data)) {
                        $imported++;
                    } else {
                        $skipped++;
                    }
                }
                
                echo '<div class="notice notice-success"><p>Import completed! ' . $imported . ' partners imported, ' . $skipped . ' skipped.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Invalid file format. Please upload a valid JSON file.</p></div>';
            }
        } else {
            echo '<div class="notice notice-error"><p>File upload failed. Please try again.</p></div>';
        }
    }
    ?>
    
    <div class="wrap import-export-wrap">
        <h1>
            <i class="dashicons dashicons-migrate"></i>
            Import/Export Partners
        </h1>
        
        <div class="import-export-sections">
            <!-- Export Section -->
            <div class="ie-section export-section">
                <h2><i class="dashicons dashicons-download"></i> Export Partners</h2>
                <p>Download all your partners data as a JSON file. This includes all partner information, settings, and metadata.</p>
                
                <form method="post">
                    <?php wp_nonce_field('kilismile_export_partners'); ?>
                    <button type="submit" name="export_partners" class="button button-primary button-large">
                        <i class="dashicons dashicons-download"></i>
                        Export All Partners
                    </button>
                </form>
            </div>
            
            <!-- Import Section -->
            <div class="ie-section import-section">
                <h2><i class="dashicons dashicons-upload"></i> Import Partners</h2>
                <p>Upload a JSON file to import partners. The file should be in the same format as exported from this system.</p>
                
                <div class="import-warnings">
                    <h4>⚠️ Important Notes:</h4>
                    <ul>
                        <li>This will add new partners to your existing ones</li>
                        <li>Duplicate partners (same name) will be skipped</li>
                        <li>Make sure to backup your existing data first</li>
                        <li>Only JSON files from this system are supported</li>
                    </ul>
                </div>
                
                <form method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('kilismile_import_partners'); ?>
                    
                    <div class="import-form">
                        <div class="file-input-group">
                            <label for="import_file">Select JSON File:</label>
                            <input type="file" id="import_file" name="import_file" accept=".json" required>
                        </div>
                        
                        <button type="submit" name="import_partners" class="button button-primary button-large">
                            <i class="dashicons dashicons-upload"></i>
                            Import Partners
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <style>
    .import-export-wrap {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background: #f1f1f1;
    }
    
    .import-export-sections {
        display: grid;
        gap: 30px;
    }
    
    .ie-section {
        background: white;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .ie-section h2 {
        margin: 0 0 15px 0;
        color: #333;
        font-size: 1.3rem;
        border-bottom: 2px solid #4CAF50;
        padding-bottom: 10px;
    }
    
    .ie-section p {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }
    
    .import-warnings {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .import-warnings h4 {
        margin: 0 0 10px 0;
        color: #856404;
    }
    
    .import-warnings ul {
        margin: 0;
        padding-left: 20px;
        color: #856404;
    }
    
    .import-warnings li {
        margin-bottom: 5px;
    }
    
    .import-form {
        display: grid;
        gap: 20px;
    }
    
    .file-input-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }
    
    .file-input-group input[type="file"] {
        width: 100%;
        padding: 10px;
        border: 2px dashed #ddd;
        border-radius: 6px;
        background: #fafafa;
    }
    
    .button-large {
        padding: 12px 24px;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .export-section {
        border-left: 4px solid #4CAF50;
    }
    
    .import-section {
        border-left: 4px solid #2196F3;
    }
    </style>
    <?php
}

// (Legacy simple partner management code removed to prevent duplication and parse error)

// Partner Management Page (legacy version removed; using enhanced page kilismile_enhanced_partner_management_page)
function kilismile_partner_management_page() {
    $current_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'corporate';
    $partners = kilismile_get_enhanced_partner_data($current_category);
    ?>
    <div class="wrap">
        <h1 style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px;">
            <span class="dashicons dashicons-groups" style="font-size: 2rem; color: #3498db;"></span>
            <?php _e('Partner Management System', 'kilismile'); ?>
        </h1>
        
        <!-- Category Tabs -->
        <div class="nav-tab-wrapper" style="margin-bottom: 30px;">
            <a href="?page=kilismile-partner-management&category=corporate" class="nav-tab <?php echo $current_category === 'corporate' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-building"></span> Corporate Partners
            </a>
            <a href="?page=kilismile-partner-management&category=community" class="nav-tab <?php echo $current_category === 'community' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-heart"></span> Community Partners
            </a>
            <a href="?page=kilismile-partner-management&category=strategic" class="nav-tab <?php echo $current_category === 'strategic' ? 'nav-tab-active' : ''; ?>">
                <span class="dashicons dashicons-admin-site-alt3"></span> Strategic Partners
            </a>
        </div>
        
        <!-- Partner Management Form -->
        <form method="post" action="" enctype="multipart/form-data">
            <?php wp_nonce_field('kilismile_partner_management'); ?>
            <input type="hidden" name="partner_category" value="<?php echo esc_attr($current_category); ?>">
            
            <div class="postbox" style="margin-bottom: 30px;">
                <div class="postbox-header">
                    <h2 class="hndle ui-sortable-handle">
                        <span class="dashicons dashicons-admin-customizer"></span>
                        <?php printf(__('%s Partner Management', 'kilismile'), ucfirst($current_category)); ?>
                    </h2>
                </div>
                <div class="inside" style="padding: 20px;">
                    
                    <!-- Strategic Positions Info -->
                    <div class="notice notice-info" style="margin-bottom: 25px;">
                        <p><strong>Strategic Logo Positions:</strong> Partner logos will be displayed in the following locations:</p>
                        <ul style="margin-left: 20px;">
                            <li>• <strong>Homepage Footer:</strong> Logo carousel for all featured partners</li>
                            <li>• <strong>Partners Page:</strong> Full showcase with all layouts</li>
                            <li>• <strong>About Page:</strong> Partner logo grid in "Our Partners" section</li>
                            <li>• <strong>Corporate Page:</strong> Featured corporate partner logos</li>
                            <li>• <strong>Widget Areas:</strong> Partner showcase widgets in sidebars</li>
                        </ul>
                    </div>
                    
                    <!-- Partners Container -->
                    <div id="partners-container">
                        <?php if (empty($partners)): ?>
                            <div class="partner-item" style="background: #f9f9f9; border: 2px dashed #ddd; border-radius: 10px; padding: 25px; margin-bottom: 20px;">
                                <h3 style="color: #666; text-align: center; margin: 0;">
                                    <span class="dashicons dashicons-plus-alt"></span>
                                    Add Your First <?php echo ucfirst($current_category); ?> Partner
                                </h3>
                            </div>
                        <?php endif; ?>
                        
                        <?php foreach ($partners as $index => $partner): ?>
                            <?php kilismile_render_partner_form_item($partner, $index); ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Add Partner Button -->
                    <div style="text-align: center; margin: 30px 0;">
                        <button type="button" id="add-partner" class="button button-primary button-large" style="padding: 10px 30px; font-size: 16px; border-radius: 8px;">
                            <span class="dashicons dashicons-plus-alt"></span>
                            Add New <?php echo ucfirst($current_category); ?> Partner
                        </button>
                    </div>
                    
                    <!-- Save Button -->
                    <div style="text-align: center; padding-top: 30px; border-top: 2px solid #f0f0f0;">
                        <input type="submit" name="kilismile_partner_submit" class="button button-primary button-hero" value="Save All Partners" style="font-size: 18px; padding: 15px 40px; border-radius: 10px; background: linear-gradient(135deg, #3498db, #2980b9);">
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Logo Guidelines -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle ui-sortable-handle">
                    <span class="dashicons dashicons-format-image"></span>
                    Logo Upload Guidelines
                </h2>
            </div>
            <div class="inside" style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                    <div style="background: #e8f5e8; padding: 20px; border-radius: 10px; border-left: 4px solid #27ae60;">
                        <h4 style="color: #27ae60; margin-top: 0;"><span class="dashicons dashicons-yes"></span> Recommended</h4>
                        <ul style="margin: 0;">
                            <li>PNG or SVG format for transparency</li>
                            <li>Minimum 200px width</li>
                            <li>Horizontal orientation preferred</li>
                            <li>High contrast for visibility</li>
                            <li>File size under 500KB</li>
                        </ul>
                    </div>
                    
                    <div style="background: #fff3cd; padding: 20px; border-radius: 10px; border-left: 4px solid #f39c12;">
                        <h4 style="color: #f39c12; margin-top: 0;"><span class="dashicons dashicons-warning"></span> Display Sizes</h4>
                        <ul style="margin: 0;">
                            <li><strong>Grid Layout:</strong> 180x120px max</li>
                            <li><strong>Logo Layout:</strong> 160x90px max</li>
                            <li><strong>Featured Layout:</strong> 160x100px max</li>
                            <li><strong>Carousel:</strong> 120x70px max</li>
                            <li><strong>Footer:</strong> 100x60px max</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Partner Item Template -->
    <script type="text/template" id="partner-item-template">
        <?php kilismile_render_partner_form_item(array(), '{{INDEX}}'); ?>
    </script>
    
    <style>
    .partner-item {
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 25px;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .partner-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #3498db;
    }
    
    .logo-preview {
        max-width: 200px;
        max-height: 100px;
        object-fit: contain;
        border: 2px solid #f0f0f0;
        border-radius: 8px;
        padding: 10px;
        background: white;
    }
    
    .remove-partner {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #e74c3c;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .partner-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    
    @media (max-width: 768px) {
        .partner-form-grid {
            grid-template-columns: 1fr;
        }
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        let partnerIndex = <?php echo count($partners); ?>;
        
        // Add new partner
        $('#add-partner').on('click', function() {
            const template = $('#partner-item-template').html();
            const html = template.replace(/\{\{INDEX\}\}/g, partnerIndex);
            $('#partners-container').append(html);
            partnerIndex++;
        });
        
        // Remove partner
        $(document).on('click', '.remove-partner', function() {
            $(this).closest('.partner-item').fadeOut(300, function() {
                $(this).remove();
            });
        });
        
        // Logo upload functionality
        $(document).on('click', '.upload-logo-btn', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const preview = button.siblings('.logo-preview');
            const input = button.siblings('input[type="hidden"]');
            
            const mediaUploader = wp.media({
                title: 'Select Partner Logo',
                button: {
                    text: 'Use This Logo'
                },
                library: {
                    type: 'image'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                const attachment = mediaUploader.state().get('selection').first().toJSON();
                preview.attr('src', attachment.url).show();
                input.val(attachment.url);
                button.text('Change Logo');
            });
            
            mediaUploader.open();
        });
        
        // Remove logo
        $(document).on('click', '.remove-logo-btn', function() {
            const button = $(this);
            const preview = button.siblings('.logo-preview');
            const input = button.siblings('input[type="hidden"]');
            const uploadBtn = button.siblings('.upload-logo-btn');
            
            preview.hide();
            input.val('');
            uploadBtn.text('Upload Logo');
        });
    });
    </script>
    <?php
}

// Render individual partner form item
function kilismile_render_partner_form_item($partner = array(), $index = 0) {
    $partner = wp_parse_args($partner, array(
        'id' => '',
        'name' => '',
        'description' => '',
        'website' => '',
        'level' => '',
        'type' => '',
        'logo_url' => '',
        'featured' => false,
        'display_order' => $index + 1
    ));
    
    $template_mode = $index === '{{INDEX}}';
    ?>
    <div class="partner-item">
        <button type="button" class="remove-partner" title="Remove Partner">
            <span class="dashicons dashicons-no"></span>
        </button>
        
        <div style="display: grid; grid-template-columns: 200px 1fr; gap: 25px; align-items: start;">
            <!-- Logo Upload Section -->
            <div style="text-align: center;">
                <h4 style="margin: 0 0 15px; color: #2c3e50;">Partner Logo</h4>
                <div style="border: 2px dashed #ddd; border-radius: 10px; padding: 20px; background: #fafafa;">
                    <?php if (!$template_mode && !empty($partner['logo_url'])): ?>
                        <img src="<?php echo esc_url($partner['logo_url']); ?>" class="logo-preview" style="display: block; margin: 0 auto 15px;">
                    <?php else: ?>
                        <img class="logo-preview" style="display: none; margin: 0 auto 15px;">
                    <?php endif; ?>
                    
                    <input type="hidden" name="partners[<?php echo $index; ?>][logo_url]" value="<?php echo esc_attr($partner['logo_url']); ?>">
                    
                    <button type="button" class="button upload-logo-btn" style="margin-bottom: 10px;">
                        <?php echo !$template_mode && !empty($partner['logo_url']) ? 'Change Logo' : 'Upload Logo'; ?>
                    </button>
                    
                    <?php if (!$template_mode && !empty($partner['logo_url'])): ?>
                        <br><button type="button" class="button remove-logo-btn" style="color: #dc3545;">Remove Logo</button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Partner Information -->
            <div>
                <div class="partner-form-grid">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Partner Name *</label>
                        <input type="text" name="partners[<?php echo $index; ?>][name]" value="<?php echo esc_attr($partner['name']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;" required>
                        <input type="hidden" name="partners[<?php echo $index; ?>][id]" value="<?php echo esc_attr($partner['id'] ?: uniqid('partner_')); ?>">
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Website URL</label>
                        <input type="url" name="partners[<?php echo $index; ?>][website]" value="<?php echo esc_attr($partner['website']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;" placeholder="https://example.com">
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Partnership Level</label>
                        <select name="partners[<?php echo $index; ?>][level]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
                            <option value="">Select Level</option>
                            <option value="Platinum Sponsor" <?php selected($partner['level'], 'Platinum Sponsor'); ?>>Platinum Sponsor</option>
                            <option value="Gold Sponsor" <?php selected($partner['level'], 'Gold Sponsor'); ?>>Gold Sponsor</option>
                            <option value="Silver Sponsor" <?php selected($partner['level'], 'Silver Sponsor'); ?>>Silver Sponsor</option>
                            <option value="Bronze Sponsor" <?php selected($partner['level'], 'Bronze Sponsor'); ?>>Bronze Sponsor</option>
                            <option value="Strategic Partner" <?php selected($partner['level'], 'Strategic Partner'); ?>>Strategic Partner</option>
                            <option value="Community Partner" <?php selected($partner['level'], 'Community Partner'); ?>>Community Partner</option>
                        </select>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px;">Partner Type</label>
                        <input type="text" name="partners[<?php echo $index; ?>][type]" value="<?php echo esc_attr($partner['type']); ?>" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;" placeholder="e.g., Healthcare Provider">
                    </div>
                </div>
                
                <div style="margin-top: 15px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 5px;">Description</label>
                    <textarea name="partners[<?php echo $index; ?>][description]" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; resize: vertical;" placeholder="Brief description of the partnership..."><?php echo esc_textarea($partner['description']); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 20px; margin-top: 15px; align-items: center;">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="partners[<?php echo $index; ?>][featured]" value="1" <?php checked($partner['featured']); ?>>
                        <strong>Featured Partner</strong> (Display prominently)
                    </label>
                    
                    <div>
                        <label style="font-weight: 600; margin-right: 8px;">Display Order:</label>
                        <input type="number" name="partners[<?php echo $index; ?>][display_order]" value="<?php echo esc_attr($partner['display_order']); ?>" min="1" style="width: 70px; padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>


