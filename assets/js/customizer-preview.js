/**
 * Kilismile Customizer Live Preview
 */
(function($) {
    'use strict';
    
    // Live preview for hero title and related settings
    if (wp && wp.customize) {
        // Health Quotes Section Previews
        wp.customize('kilismile_show_health_quotes', function(value) {
            value.bind(function(newVal) {
                if (newVal) {
                    $('.health-quotes-section').show();
                } else {
                    $('.health-quotes-section').hide();
                }
            });
        });
        
        wp.customize('kilismile_health_quotes_title', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-section .section-header h2').text(newVal);
            });
        });
        
        wp.customize('kilismile_health_quotes_subtitle', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-section .section-header p').text(newVal);
            });
        });
        
        // Quote 1
        wp.customize('kilismile_quote1_text', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(1) blockquote').text('"' + newVal + '"');
            });
        });
        
        wp.customize('kilismile_quote1_author', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(1) .quote-source h4').text(newVal);
            });
        });
        
        wp.customize('kilismile_quote1_source', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(1) .quote-source div').text(newVal);
            });
        });
        
        // Quote 2
        wp.customize('kilismile_quote2_text', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(2) blockquote').text('"' + newVal + '"');
            });
        });
        
        wp.customize('kilismile_quote2_author', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(2) .quote-source h4').text(newVal);
            });
        });
        
        wp.customize('kilismile_quote2_source', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(2) .quote-source div').text(newVal);
            });
        });
        
        // Quote 3
        wp.customize('kilismile_quote3_text', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(3) blockquote').text('"' + newVal + '"');
            });
        });
        
        wp.customize('kilismile_quote3_author', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(3) .quote-source h4').text(newVal);
            });
        });
        
        wp.customize('kilismile_quote3_source', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-grid .quote-card:nth-child(3) .quote-source div').text(newVal);
            });
        });
        
        // Featured Quote
        wp.customize('kilismile_featured_quote_text', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-section .container > div:last-child blockquote').text('"' + newVal + '"');
            });
        });
        
        wp.customize('kilismile_featured_quote_author', function(value) {
            value.bind(function(newVal) {
                $('.health-quotes-section .container > div:last-child > div').text('— ' + newVal);
            });
        });

        // Hero Section Preview
        wp.customize('kilismile_hero_title', function(value) {
            value.bind(function(newVal) {
                $('.hero-title-redesign').text(newVal);
            });
        });

        // Live preview for hero subtitle
        wp.customize('kilismile_hero_subtitle', function(value) {
            value.bind(function(newVal) {
                $('.hero-subtitle-redesign').text(newVal);
            });
        });

        // Live preview for hero description
        wp.customize('kilismile_hero_description', function(value) {
            value.bind(function(newVal) {
                $('.hero-description-redesign').text(newVal);
            });
        });

        // Live preview for hero background image
        wp.customize('kilismile_hero_background', function(value) {
            value.bind(function(newVal) {
                updateHeroBackground(newVal);
            });
        });
        
        // Live preview for hero overlay gradient start color
        wp.customize('kilismile_hero_overlay_start', function(value) {
            value.bind(function(newVal) {
                updateHeroBackground();
            });
        });
        
        // Live preview for hero overlay gradient end color
        wp.customize('kilismile_hero_overlay_end', function(value) {
            value.bind(function(newVal) {
                updateHeroBackground();
            });
        });
        
        // Live preview for hero overlay opacity
        wp.customize('kilismile_hero_overlay_opacity', function(value) {
            value.bind(function(newVal) {
                updateHeroBackground();
            });
        });
        
        // Live preview for primary button text
        wp.customize('kilismile_primary_btn_text', function(value) {
            value.bind(function(newVal) {
                $('.btn-primary-redesign').contents().filter(function() {
                    return this.nodeType === 3; // Text nodes only
                }).last().replaceWith(newVal);
            });
        });
        
        // Live preview for primary button URL
        wp.customize('kilismile_primary_btn_url', function(value) {
            value.bind(function(newVal) {
                $('.btn-primary-redesign').attr('href', newVal);
            });
        });
        
        // Live preview for secondary button text
        wp.customize('kilismile_secondary_btn_text', function(value) {
            value.bind(function(newVal) {
                $('.btn-secondary-redesign').contents().filter(function() {
                    return this.nodeType === 3; // Text nodes only
                }).last().replaceWith(newVal);
            });
        });
        
        // Live preview for secondary button URL
        wp.customize('kilismile_secondary_btn_url', function(value) {
            value.bind(function(newVal) {
                $('.btn-secondary-redesign').attr('href', newVal);
            });
        });
        
        // Live preview for impact badge visibility
        wp.customize('kilismile_show_hero_badge', function(value) {
            value.bind(function(newVal) {
                if (newVal) {
                    $('.impact-badge').show();
                } else {
                    $('.impact-badge').hide();
                }
            });
        });
        
        // Live preview for stats visibility
        wp.customize('kilismile_show_hero_stats', function(value) {
            value.bind(function(newVal) {
                if (newVal) {
                    $('.hero-stats').show();
                } else {
                    $('.hero-stats').hide();
                }
            });
        });
        
        // Live preview for scroll indicator visibility
        wp.customize('kilismile_show_scroll_indicator', function(value) {
            value.bind(function(newVal) {
                if (newVal) {
                    $('.scroll-indicator').show();
                } else {
                    $('.scroll-indicator').hide();
                }
            });
        });

        // Logo Size Controls
        wp.customize('kilismile_logo_size', function(value) {
            value.bind(function(newval) {
                console.log('Logo size changed to:', newval);
                
                // Update CSS custom property
                $(':root').css('--logo-size', newval + 'px');
                
                // Define all possible logo selectors
                var logoSelectors = [
                    '.site-logo img',
                    '.custom-logo-link img', 
                    '.custom-logo',
                    '#siteLogo',
                    '.site-branding img',
                    'header .logo img',
                    '.header-logo img'
                ];
                
                // Apply styles to all logo elements
                logoSelectors.forEach(function(selector) {
                    $(selector).css({
                        'width': newval + 'px',
                        'height': newval + 'px',
                        'max-width': newval + 'px',
                        'max-height': newval + 'px',
                        'object-fit': 'contain'
                    });
                });
                
                // Also update any inline styles
                $('style#kilismile-priority-logo').remove();
                $('head').append('<style id="kilismile-priority-logo">' +
                    '.site-logo img, .custom-logo-link img, #siteLogo, .custom-logo {' +
                    'width: ' + newval + 'px !important;' +
                    'height: ' + newval + 'px !important;' +
                    'max-width: ' + newval + 'px !important;' +
                    'max-height: ' + newval + 'px !important;' +
                    'object-fit: contain !important;' +
                    '}' +
                    ':root { --logo-size: ' + newval + 'px !important; }' +
                    '</style>'
                );
                
                // Log for debugging
                console.log('Found logo elements:', $(logoSelectors.join(', ')).length);
            });
        });
        
        // Test Logo Size Control (backup)
        wp.customize('kilismile_test_logo_size', function(value) {
            value.bind(function(newval) {
                wp.customize('kilismile_logo_size').set(newval);
            });
        });
        
        // Helper function to update hero background with all settings
        function updateHeroBackground(backgroundImage) {
            // If background image is not provided, use the current one
            if (backgroundImage === undefined) {
                backgroundImage = $('.hero-section-redesign').css('background-image');
                
                // If we can't extract the image URL, use the default
                if (!backgroundImage || backgroundImage === 'none' || backgroundImage.indexOf('url') === -1) {
                    backgroundImage = kilismile.templateUrl + '/assets/images/hero-background.svg';
                } else {
                    // Extract the URL from the background-image property
                    var matches = backgroundImage.match(/url\(['"]?([^'")]+)['"]?\)/);
                    if (matches && matches[1]) {
                        backgroundImage = matches[1];
                    } else {
                        backgroundImage = kilismile.templateUrl + '/assets/images/hero-background.svg';
                    }
                }
            }
            
            // Get overlay colors and opacity
            var overlayStart = wp.customize('kilismile_hero_overlay_start')();
            var overlayEnd = wp.customize('kilismile_hero_overlay_end')();
            var overlayOpacity = wp.customize('kilismile_hero_overlay_opacity')() / 100;
            
            // Convert decimal opacity to hex
            var opacityHex = Math.round(overlayOpacity * 255).toString(16).padStart(2, '0');
            
            if (backgroundImage) {
                $('.hero-section-redesign').css({
                    'background-image': 'linear-gradient(135deg, ' + 
                        overlayStart + opacityHex + ' 0%, ' + 
                        overlayEnd + opacityHex + ' 100%), url(' + backgroundImage + ')',
                    'background-size': 'cover',
                    'background-position': 'center',
                    'background-attachment': 'fixed'
                });
            } else {
                $('.hero-section-redesign').css({
                    'background-image': 'linear-gradient(135deg, ' + 
                        overlayStart + ' 0%, ' + 
                        overlayEnd + ' 100%), url(' + kilismile.templateUrl + '/assets/images/hero-background.svg)'
                });
            }
        }
    }
})(jQuery);
