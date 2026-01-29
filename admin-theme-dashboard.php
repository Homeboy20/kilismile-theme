<?php
// Theme Dashboard Admin Page
add_action('admin_menu', function() {
    add_menu_page(
        'Theme Dashboard',
        'Theme Dashboard',
        'manage_options',
        'kilismile_theme_dashboard',
        'kilismile_theme_dashboard_page',
        'dashicons-admin-generic',
        2
    );
});

// Enqueue media uploader scripts
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook != 'toplevel_page_kilismile_theme_dashboard') return;
    wp_enqueue_media();
    wp_enqueue_script('kilismile-dashboard-js', get_template_directory_uri() . '/assets/js/dashboard.js', array('jquery'), '1.0.0', true);
    wp_localize_script('kilismile-dashboard-js', 'kilismile_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('kilismile_dashboard_nonce')
    ));
});

function kilismile_theme_dashboard_page() {
    // Handle form submission
    if (isset($_POST['kilismile_dashboard_submit']) && check_admin_referer('kilismile_dashboard_save')) {
        update_option('kilismile_gallery_images', sanitize_text_field($_POST['gallery_images']));
        update_option('kilismile_healthy_tips', sanitize_textarea_field($_POST['healthy_tips']));
        update_option('kilismile_faqs', sanitize_textarea_field($_POST['faqs']));
        update_option('kilismile_downloads', sanitize_textarea_field($_POST['downloads']));
        update_option('kilismile_corporate_stats', sanitize_textarea_field($_POST['corporate_stats']));
        update_option('kilismile_corporate_partners', sanitize_textarea_field($_POST['corporate_partners']));
        update_option('kilismile_community_partners', sanitize_textarea_field($_POST['community_partners']));
        update_option('kilismile_strategic_partners', sanitize_textarea_field($_POST['strategic_partners']));
        update_option('kilismile_success_stories', sanitize_textarea_field($_POST['success_stories']));
        echo '<div class="notice notice-success is-dismissible"><p><span class="dashicons dashicons-yes" style="color:#27ae60;font-size:1.3em;vertical-align:middle;"></span> Content updated successfully!</p></div>';
    }

    $gallery_images = get_option('kilismile_gallery_images', '/wp-content/uploads/gallery1.jpg,/wp-content/uploads/gallery2.jpg,/wp-content/uploads/gallery3.jpg');
    $healthy_tips = get_option('kilismile_healthy_tips', "Brush your teeth twice a day.\nEat a balanced diet.\nVisit your dentist regularly.");
    $faqs = get_option('kilismile_faqs', "How often should I brush my teeth?|Twice a day\nWhat foods are best for oral health?|Fruits and vegetables");
    $downloads = get_option('kilismile_downloads', "/wp-content/uploads/healthy-tips-guide.pdf|Healthy Tips Guide\n/wp-content/uploads/oral-health-checklist.pdf|Oral Health Checklist");
    $corporate_stats = get_option('kilismile_corporate_stats', "45|Corporate Partners\n$2.5M+|Corporate Investment\n150K+|Lives Impacted\n98%|Partner Satisfaction");
    $corporate_partners = get_option('kilismile_corporate_partners', "TechCorp Solutions|Gold Sponsor|fas fa-building\nMediHealth Group|Platinum Sponsor|fas fa-heartbeat\nEcoSustain Ltd|Silver Sponsor|fas fa-leaf\nEduTech Innovations|Bronze Sponsor|fas fa-graduation-cap");
    $community_partners = get_option('kilismile_community_partners', "Moshi Community Center|Community Partner|fas fa-home\nTanzania Health Network|Health Partner|fas fa-hospital\nLocal Schools Alliance|Education Partner|fas fa-school");
    $strategic_partners = get_option('kilismile_strategic_partners', "WHO Tanzania|Strategic Alliance|fas fa-globe\nDental Association Tanzania|Professional Partner|fas fa-tooth\nHealth Ministry Tanzania|Government Partner|fas fa-landmark");
    $success_stories = get_option('kilismile_success_stories', "Our partnership with Kilismile has transformed our CSR program. We've seen unprecedented employee engagement and meaningful impact metrics that resonate with our stakeholders.|Sarah Johnson|CSR Director, TechCorp Global\nThe employee volunteer program through Kilismile has been a game-changer. Our team building and corporate culture have never been stronger.|Michael Chen|HR Director, HealthForward Inc.");
    ?>
    <div class="wrap" style="max-width:900px;margin:auto;">
        <h1 style="margin-bottom:32px;font-size:2.2rem;font-weight:700;color:#16a085;">Theme Dashboard</h1>
        <form method="post">
            <?php wp_nonce_field('kilismile_dashboard_save'); ?>
            <div style="display:flex;flex-wrap:wrap;gap:24px;">
                <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                    <h2 style="color:#2980b9;font-size:1.3rem;font-weight:600;margin-bottom:12px;"><span class="dashicons dashicons-format-gallery" style="font-size:1.5em;vertical-align:middle;"></span> Gallery Images</h2>
                    
                    <!-- Image Upload Area -->
                    <div id="image-upload-area" style="border:2px dashed #ddd;border-radius:8px;padding:24px;text-align:center;margin-bottom:16px;background:#f9f9f9;">
                        <span class="dashicons dashicons-cloud-upload" style="font-size:3em;color:#aaa;display:block;margin-bottom:8px;"></span>
                        <p style="margin:0;color:#666;">Drop images here or <button type="button" id="upload-gallery-btn" class="button">Browse Files</button></p>
                    </div>
                    
                    <!-- Image Previews -->
                    <div id="image-previews" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:16px;">
                        <?php 
                        $gallery_urls = explode(',', $gallery_images);
                        foreach($gallery_urls as $url) {
                            if(trim($url)) {
                                echo '<div class="image-preview" style="position:relative;width:80px;height:80px;">
                                    <img src="' . esc_url(trim($url)) . '" style="width:100%;height:100%;object-fit:cover;border-radius:4px;">
                                    <button type="button" class="remove-image" style="position:absolute;top:-8px;right:-8px;background:#e74c3c;color:white;border:none;border-radius:50%;width:20px;height:20px;cursor:pointer;font-size:12px;">×</button>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    
                    <input type="hidden" name="gallery_images" id="gallery_images" value="<?php echo esc_attr($gallery_images); ?>" />
                    <small style="color:#888;">Upload images to your gallery. Click × to remove.</small>
                </div>
                <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                    <h2 style="color:#27ae60;font-size:1.3rem;font-weight:600;margin-bottom:12px;"><span class="dashicons dashicons-heart" style="font-size:1.5em;vertical-align:middle;"></span> Healthy Tips</h2>
                    <textarea name="healthy_tips" rows="5" style="width:100%;margin-bottom:8px;"><?php echo esc_textarea($healthy_tips); ?></textarea>
                    <small style="color:#888;">One tip per line.</small>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:24px;margin-top:24px;">
                <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                    <h2 style="color:#e67e22;font-size:1.3rem;font-weight:600;margin-bottom:12px;"><span class="dashicons dashicons-editor-help" style="font-size:1.5em;vertical-align:middle;"></span> FAQs</h2>
                    <textarea name="faqs" rows="5" style="width:100%;margin-bottom:8px;"><?php echo esc_textarea($faqs); ?></textarea>
                    <small style="color:#888;">Format: Question|Answer (one per line)</small>
                </div>
                <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                    <h2 style="color:#8e44ad;font-size:1.3rem;font-weight:600;margin-bottom:12px;"><span class="dashicons dashicons-download" style="font-size:1.5em;vertical-align:middle;"></span> Downloads</h2>
                    
                    <!-- File Upload Area -->
                    <div id="file-upload-area" style="border:2px dashed #ddd;border-radius:8px;padding:24px;text-align:center;margin-bottom:16px;background:#f9f9f9;">
                        <span class="dashicons dashicons-media-document" style="font-size:3em;color:#aaa;display:block;margin-bottom:8px;"></span>
                        <p style="margin:0;color:#666;">Drop files here or <button type="button" id="upload-files-btn" class="button">Browse Files</button></p>
                        <small style="color:#999;">Supported: PDF, DOC, DOCX, TXT</small>
                    </div>
                    
                    <!-- File Previews -->
                    <div id="file-previews" style="margin-bottom:16px;">
                        <?php 
                        $downloads_data = explode("\n", $downloads);
                        foreach($downloads_data as $download) {
                            if(trim($download) && strpos($download, '|') !== false) {
                                $parts = explode('|', $download, 2);
                                $url = trim($parts[0]);
                                $title = trim($parts[1]);
                                $file_extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                                $file_icon = 'dashicons-media-document';
                                if($file_extension == 'pdf') $file_icon = 'dashicons-pdf';
                                
                                echo '<div class="file-preview" style="display:flex;align-items:center;background:#f8f9fa;padding:12px;border-radius:8px;margin-bottom:8px;">
                                    <span class="dashicons ' . $file_icon . '" style="font-size:1.5em;color:#8e44ad;margin-right:12px;"></span>
                                    <div style="flex:1;">
                                        <strong>' . esc_html($title) . '</strong><br>
                                        <small style="color:#666;">' . esc_html(basename($url)) . '</small>
                                    </div>
                                    <button type="button" class="remove-file" data-url="' . esc_attr($url) . '" style="background:#e74c3c;color:white;border:none;border-radius:4px;padding:4px 8px;cursor:pointer;">Remove</button>
                                </div>';
                            }
                        }
                        ?>
                    </div>
                    
                    <input type="hidden" name="downloads" id="downloads_field" value="<?php echo esc_attr($downloads); ?>" />
                    <small style="color:#888;">Upload files for download. Supported formats: PDF, DOC, DOCX, TXT</small>
                </div>
            </div>

            <!-- Partner Showcase Management -->
            <div style="margin-top:40px;">
                <h2 style="color:#2c3e50;font-size:1.8rem;font-weight:700;margin-bottom:24px;border-bottom:2px solid #ecf0f1;padding-bottom:12px;">
                    <span class="dashicons dashicons-groups" style="font-size:1.5em;vertical-align:middle;color:#3498db;"></span> Partner Showcase Management
                </h2>
                
                <div style="display:flex;flex-wrap:wrap;gap:24px;margin-bottom:32px;">
                    <!-- Corporate Partners -->
                    <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                        <h3 style="color:#3498db;font-size:1.3rem;font-weight:600;margin-bottom:12px;">
                            <span class="dashicons dashicons-building" style="font-size:1.2em;vertical-align:middle;"></span> Corporate Partners
                        </h3>
                        <textarea name="corporate_partners" style="width:100%;min-height:120px;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;" placeholder="Format: Company Name|Sponsor Level|Icon Class|Logo URL (optional) - one per line&#10;Example:&#10;TechCorp Solutions|Gold Sponsor|fas fa-building|/path/to/logo.png"><?php echo esc_textarea($corporate_partners); ?></textarea>
                        <small style="color:#888;">Corporate sponsors and business partners</small>
                    </div>

                    <!-- Community Partners -->
                    <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                        <h3 style="color:#27ae60;font-size:1.3rem;font-weight:600;margin-bottom:12px;">
                            <span class="dashicons dashicons-heart" style="font-size:1.2em;vertical-align:middle;"></span> Community Partners
                        </h3>
                        <textarea name="community_partners" style="width:100%;min-height:120px;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;" placeholder="Format: Partner Name|Partner Type|Icon Class|Logo URL (optional) - one per line&#10;Example:&#10;Moshi Community Center|Community Partner|fas fa-home"><?php echo esc_textarea($community_partners); ?></textarea>
                        <small style="color:#888;">Local community organizations and NGOs</small>
                    </div>

                    <!-- Strategic Partners -->
                    <div style="flex:1 1 350px;background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;min-width:320px;">
                        <h3 style="color:#e74c3c;font-size:1.3rem;font-weight:600;margin-bottom:12px;">
                            <span class="dashicons dashicons-networking" style="font-size:1.2em;vertical-align:middle;"></span> Strategic Partners
                        </h3>
                        <textarea name="strategic_partners" style="width:100%;min-height:120px;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;" placeholder="Format: Partner Name|Partnership Type|Icon Class|Logo URL (optional) - one per line&#10;Example:&#10;WHO Tanzania|Strategic Alliance|fas fa-globe"><?php echo esc_textarea($strategic_partners); ?></textarea>
                        <small style="color:#888;">Government agencies, international organizations</small>
                    </div>
                </div>

                <!-- Partner Showcase Preview -->
                <div style="background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;margin-bottom:24px;">
                    <h3 style="color:#f39c12;font-size:1.3rem;font-weight:600;margin-bottom:16px;">
                        <span class="dashicons dashicons-visibility" style="font-size:1.2em;vertical-align:middle;"></span> Partner Showcase Options
                    </h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:20px;">
                        <div style="background:#f8f9fa;padding:16px;border-radius:8px;text-align:center;">
                            <h4 style="color:#2c3e50;margin-bottom:8px;">Grid Layout</h4>
                            <code>[partner_showcase layout="grid"]</code>
                        </div>
                        <div style="background:#f8f9fa;padding:16px;border-radius:8px;text-align:center;">
                            <h4 style="color:#2c3e50;margin-bottom:8px;">Carousel Layout</h4>
                            <code>[partner_showcase layout="carousel"]</code>
                        </div>
                        <div style="background:#f8f9fa;padding:16px;border-radius:8px;text-align:center;">
                            <h4 style="color:#2c3e50;margin-bottom:8px;">Logo Grid</h4>
                            <code>[partner_showcase layout="logos"]</code>
                        </div>
                        <div style="background:#f8f9fa;padding:16px;border-radius:8px;text-align:center;">
                            <h4 style="color:#2c3e50;margin-bottom:8px;">Featured Partners</h4>
                            <code>[partner_showcase layout="featured"]</code>
                        </div>
                    </div>
                    <div style="background:#e8f5e8;padding:16px;border-radius:8px;border-left:4px solid #27ae60;">
                        <h4 style="color:#2c3e50;margin-bottom:12px;">Shortcode Options:</h4>
                        <ul style="margin:0;color:#555;">
                            <li><strong>category:</strong> all, corporate, community, strategic</li>
                            <li><strong>limit:</strong> number of partners to show (e.g., limit="6")</li>
                            <li><strong>layout:</strong> grid, carousel, logos, featured</li>
                        </ul>
                        <p style="margin:12px 0 0;font-size:0.9rem;"><strong>Example:</strong> <code>[partner_showcase category="corporate" layout="featured" limit="6"]</code></p>
                    </div>
                </div>

                <!-- Corporate Statistics -->
                <div style="background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;">
                    <h3 style="color:#9b59b6;font-size:1.3rem;font-weight:600;margin-bottom:12px;">
                        <span class="dashicons dashicons-chart-line" style="font-size:1.2em;vertical-align:middle;"></span> Corporate Statistics
                    </h3>
                    <textarea name="corporate_stats" style="width:100%;min-height:120px;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;" placeholder="Format: Value|Label (one per line)&#10;Example:&#10;45|Corporate Partners&#10;$2.5M+|Corporate Investment"><?php echo esc_textarea($corporate_stats); ?></textarea>
                    <small style="color:#888;">Statistics displayed on corporate page (max 4 recommended)</small>
                </div>
            </div>

            <!-- Corporate Content Management -->
            <div style="margin-top:40px;">
                <h2 style="color:#2c3e50;font-size:1.8rem;font-weight:700;margin-bottom:24px;border-bottom:2px solid #ecf0f1;padding-bottom:12px;">
                    <span class="dashicons dashicons-format-quote" style="font-size:1.5em;vertical-align:middle;color:#e74c3c;"></span> Corporate Success Stories
                </h2>
                
                <!-- Success Stories -->
                <div style="background:#fff;border-radius:16px;box-shadow:0 2px 12px #0001;padding:24px 20px;">
                    <h3 style="color:#e74c3c;font-size:1.3rem;font-weight:600;margin-bottom:12px;">
                        <span class="dashicons dashicons-format-quote" style="font-size:1.2em;vertical-align:middle;"></span> Partnership Testimonials
                    </h3>
                    <textarea name="success_stories" style="width:100%;min-height:120px;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;" placeholder="Format: Quote|Author Name|Author Title (separated by double line breaks between stories)&#10;Example:&#10;Our partnership with Kilismile has transformed our CSR program.|Sarah Johnson|CSR Director, TechCorp Global&#10;&#10;The volunteer program has been amazing for team building.|Michael Chen|HR Director, HealthForward"><?php echo esc_textarea($success_stories); ?></textarea>
                    <small style="color:#888;">Testimonials from corporate partners (separate multiple stories with double line breaks)</small>
                </div>
            </div>

            <p style="margin-top:32px;text-align:center;"><input type="submit" name="kilismile_dashboard_submit" class="button button-primary button-hero" value="Save All Content" style="font-size:1.2rem;padding:12px 32px;border-radius:8px;" /></p>
        </form>
    </div>
    <?php
}


