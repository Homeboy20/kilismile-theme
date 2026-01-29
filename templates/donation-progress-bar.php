<?php
/**
 * Donation Progress Bar Template
 * 
 * @package KiliSmile
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="kilismile-donation-progress-container" style="margin-bottom: 30px;">
    <?php if (!empty($campaign_title)) : ?>
        <h4 style="margin-bottom: 15px; color: var(--dark-green); text-align: center; font-weight: 600;">
            <?php echo esc_html($campaign_title); ?>
        </h4>
    <?php endif; ?>
    
    <div class="donation-stats" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
        <div class="donation-raised">
            <span style="font-weight: 600; color: var(--dark-green);">
                <?php echo esc_html($currency_symbol); ?><?php echo number_format($amount_raised, $currency === 'TZS' ? 0 : 2); ?>
            </span>
            <span style="font-size: 0.9rem; color: #666;">
                <?php _e('raised', 'kilismile'); ?>
            </span>
        </div>
        
        <?php if ($campaign_goal > 0) : ?>
            <div class="donation-goal">
                <span style="font-size: 0.9rem; color: #666;">
                    <?php _e('Goal:', 'kilismile'); ?>
                </span>
                <span style="font-weight: 600; color: var(--dark-green);">
                    <?php echo esc_html($currency_symbol); ?><?php echo number_format($campaign_goal, $currency === 'TZS' ? 0 : 2); ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($campaign_goal > 0) : ?>
        <div class="progress-bar-outer" style="height: 10px; background-color: #eee; border-radius: 5px; overflow: hidden;">
            <div class="progress-bar-inner" style="height: 100%; width: <?php echo esc_attr($percentage); ?>%; background-color: var(--primary-green); border-radius: 5px; transition: width 1s ease-in-out;"></div>
        </div>
        
        <div class="progress-details" style="display: flex; justify-content: space-between; margin-top: 8px;">
            <div class="percentage">
                <span style="font-weight: 600; color: var(--primary-green);">
                    <?php echo esc_html($percentage); ?>%
                </span>
                <span style="font-size: 0.9rem; color: #666;">
                    <?php _e('of goal', 'kilismile'); ?>
                </span>
            </div>
            
            <div class="donor-count">
                <span style="font-weight: 600; color: var(--primary-green);">
                    <?php echo esc_html($donor_count); ?>
                </span>
                <span style="font-size: 0.9rem; color: #666;">
                    <?php echo _n('donor', 'donors', $donor_count, 'kilismile'); ?>
                </span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($campaign_description)) : ?>
        <div class="campaign-description" style="margin-top: 15px; font-size: 0.95rem; color: #666; line-height: 1.5; text-align: center;">
            <?php echo esc_html($campaign_description); ?>
        </div>
    <?php endif; ?>
</div>


