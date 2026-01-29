<?php
/*
Template Name: Resources
*/
get_header();
?>
<section class="resources-section" style="max-width:900px;margin:48px auto;padding:32px;background:#f7fafc;border-radius:16px;box-shadow:0 2px 12px #0001;">
    <h1 style="font-size:2.5rem;font-weight:700;color:#16a085;margin-bottom:24px;">Resources</h1>
    <p style="font-size:1.2rem;color:#555;margin-bottom:32px;">Access health education materials, downloads, FAQs, and other helpful resources for your well-being.</p>

    <h2 style="color:#e74c3c;">Gallery</h2>
    <div style="margin-bottom:32px;">
        <?php 
        $gallery_images = get_option('kilismile_gallery_images', '');
        if ($gallery_images) {
            $image_urls = array_filter(array_map('trim', explode(',', $gallery_images)));
            if (!empty($image_urls)) {
                echo '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;">';
                foreach ($image_urls as $index => $url) {
                    echo '<div style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.1);transition:transform 0.3s ease;" onmouseover="this.style.transform=\'translateY(-4px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                        <img src="' . esc_url($url) . '" alt="Gallery Image ' . ($index + 1) . '" style="width:100%;height:200px;object-fit:cover;cursor:pointer;" onclick="window.open(this.src, \'_blank\')">
                        <div style="padding:12px;">
                            <p style="margin:0;font-size:0.9rem;color:#666;">Healthy lifestyle image ' . ($index + 1) . '</p>
                        </div>
                    </div>';
                }
                echo '</div>';
            } else {
                echo '<div style="background:#f8f9fa;padding:24px;border-radius:8px;text-align:center;border:2px dashed #dee2e6;">
                    <span class="dashicons dashicons-format-gallery" style="font-size:3em;color:#dee2e6;margin-bottom:12px;display:block;"></span>
                    <p style="color:#888;font-style:italic;margin:0;">No gallery images available. Add images from the Theme Dashboard.</p>
                </div>';
            }
        } else {
            echo '<div style="background:#f8f9fa;padding:24px;border-radius:8px;text-align:center;border:2px dashed #dee2e6;">
                <span class="dashicons dashicons-format-gallery" style="font-size:3em;color:#dee2e6;margin-bottom:12px;display:block;"></span>
                <p style="color:#888;font-style:italic;margin:0;">No gallery images available. Add images from the Theme Dashboard.</p>
            </div>';
        }
        ?>
    </div>

    <h2 style="color:#27ae60;">Healthy Tips</h2>
    <div style="margin-bottom:32px;">
        <?php 
        $healthy_tips = get_option('kilismile_healthy_tips', 'Brush your teeth twice a day with fluoride toothpaste.');
        if ($healthy_tips) {
            $tips = array_filter(array_map('trim', explode("\n", $healthy_tips)));
            if (!empty($tips)) {
                foreach ($tips as $index => $tip) {
                    echo '<div style="background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:12px;display:flex;align-items:center;transition:transform 0.3s ease;" onmouseover="this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                        <span class="dashicons dashicons-heart" style="font-size:1.5em;color:#27ae60;margin-right:16px;"></span>
                        <div>
                            <p style="margin:0;font-size:1.1rem;color:#333;">' . esc_html($tip) . '</p>
                        </div>
                    </div>';
                }
            } else {
                echo '<div style="background:#f8f9fa;padding:24px;border-radius:8px;text-align:center;border:2px dashed #dee2e6;">
                    <span class="dashicons dashicons-heart" style="font-size:3em;color:#dee2e6;margin-bottom:12px;display:block;"></span>
                    <p style="color:#888;font-style:italic;margin:0;">No healthy tips available. Add tips from the Theme Dashboard.</p>
                </div>';
            }
        } else {
            echo '<div style="background:#f8f9fa;padding:24px;border-radius:8px;text-align:center;border:2px dashed #dee2e6;">
                <span class="dashicons dashicons-heart" style="font-size:3em;color:#dee2e6;margin-bottom:12px;display:block;"></span>
                <p style="color:#888;font-style:italic;margin:0;">No healthy tips available. Add tips from the Theme Dashboard.</p>
            </div>';
        }
        ?>
    </div>

    <h2 style="color:#e67e22;">FAQs</h2>
    <div style="margin-bottom:32px;">
        <?php 
        $faqs = get_option('kilismile_faqs', 'How often should I brush my teeth?|Twice a day, morning and night.');
        if ($faqs) {
            $faq_items = array_filter(array_map('trim', explode("\n", $faqs)));
            if (!empty($faq_items)) {
                foreach ($faq_items as $faq) {
                    $parts = explode('|', $faq, 2);
                    if (count($parts) == 2) {
                        echo '<div style="background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:16px;overflow:hidden;transition:transform 0.3s ease;" onmouseover="this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                            <div style="background:#e67e22;color:white;padding:12px 16px;display:flex;align-items:center;">
                                <span class="dashicons dashicons-editor-help" style="font-size:1.2em;margin-right:8px;"></span>
                                <strong style="font-size:1.1rem;">' . esc_html(trim($parts[0])) . '</strong>
                            </div>
                            <div style="padding:16px;background:#fff;">
                                <p style="margin:0;color:#333;line-height:1.6;">' . esc_html(trim($parts[1])) . '</p>
                            </div>
                        </div>';
                    }
                }
            } else {
                echo '<div style="background:#f8f9fa;padding:24px;border-radius:8px;text-align:center;border:2px dashed #dee2e6;">
                    <span class="dashicons dashicons-editor-help" style="font-size:3em;color:#dee2e6;margin-bottom:12px;display:block;"></span>
                    <p style="color:#888;font-style:italic;margin:0;">No FAQs available. Add FAQs from the Theme Dashboard.</p>
                </div>';
            }
        } else {
            echo '<div style="background:#f8f9fa;padding:24px;border-radius:8px;text-align:center;border:2px dashed #dee2e6;">
                <span class="dashicons dashicons-editor-help" style="font-size:3em;color:#dee2e6;margin-bottom:12px;display:block;"></span>
                <p style="color:#888;font-style:italic;margin:0;">No FAQs available. Add FAQs from the Theme Dashboard.</p>
            </div>';
        }
        ?>
    </div>

    <h2 style="color:#8e44ad;">Downloads</h2>
    <div style="margin-bottom:32px;">
        <?php 
        $downloads = get_option('kilismile_downloads', '/wp-content/uploads/healthy-tips-guide.pdf|Healthy Tips Guide');
        if ($downloads) {
            $download_items = array_filter(array_map('trim', explode("\n", $downloads)));
            foreach ($download_items as $download) {
                $parts = explode('|', $download, 2);
                if (count($parts) == 2) {
                    $url = trim($parts[0]);
                    $title = trim($parts[1]);
                    $file_extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
                    $file_size = '';
                    
                    // Get file size if local file
                    if (strpos($url, home_url()) !== false) {
                        $file_path = str_replace(home_url(), ABSPATH, $url);
                        if (file_exists($file_path)) {
                            $size_bytes = filesize($file_path);
                            if ($size_bytes >= 1048576) {
                                $file_size = round($size_bytes / 1048576, 1) . ' MB';
                            } elseif ($size_bytes >= 1024) {
                                $file_size = round($size_bytes / 1024, 1) . ' KB';
                            } else {
                                $file_size = $size_bytes . ' bytes';
                            }
                        }
                    }
                    
                    // Choose appropriate icon
                    $icon = 'dashicons-media-document';
                    $icon_color = '#8e44ad';
                    switch($file_extension) {
                        case 'pdf':
                            $icon = 'dashicons-pdf';
                            $icon_color = '#e74c3c';
                            break;
                        case 'doc':
                        case 'docx':
                            $icon = 'dashicons-media-text';
                            $icon_color = '#2980b9';
                            break;
                        case 'txt':
                            $icon = 'dashicons-media-text';
                            $icon_color = '#95a5a6';
                            break;
                    }
                    
                    echo '<div style="display:flex;align-items:center;background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);margin-bottom:12px;transition:transform 0.3s ease;" onmouseover="this.style.transform=\'translateY(-2px)\'" onmouseout="this.style.transform=\'translateY(0)\'">
                        <span class="dashicons ' . $icon . '" style="font-size:2em;color:' . $icon_color . ';margin-right:16px;"></span>
                        <div style="flex:1;">
                            <h3 style="margin:0;font-size:1.1rem;color:#333;">' . esc_html($title) . '</h3>
                            <div style="font-size:0.9rem;color:#666;margin-top:4px;">
                                <span style="text-transform:uppercase;font-weight:bold;">' . strtoupper($file_extension) . '</span>
                                ' . ($file_size ? '<span style="margin-left:12px;">' . $file_size . '</span>' : '') . '
                            </div>
                        </div>
                        <a href="' . esc_url($url) . '" target="_blank" style="background:#27ae60;color:white;text-decoration:none;padding:8px 16px;border-radius:4px;font-weight:bold;transition:background 0.3s ease;" onmouseover="this.style.background=\'#219a52\'" onmouseout="this.style.background=\'#27ae60\'">Download</a>
                    </div>';
                }
            }
        } else {
            echo '<p style="color:#888;font-style:italic;">No downloads available. Add files from the Theme Dashboard.</p>';
        }
        ?>
    </div>
</section>
<?php get_footer(); ?>


