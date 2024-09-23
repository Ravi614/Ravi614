<?php
/**
 * Plugin Name: Custom Floating Social Buttons
 * Description: Adds floating social share buttons on the left or right side of the screen.
 * Version: 1.0.0
 * Author: Ravi Shankar
 * License: GPL2+
 */

// Enqueue styles and scripts
function custom_floating_social_enqueue_scripts() {
    // Enqueue Font Awesome for icons
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
    
    // Enqueue styles
    wp_enqueue_style('custom-floating-social-style', plugins_url('css/style.css', __FILE__));
    
    // Enqueue scripts (jQuery dependency included)
    wp_enqueue_script('jquery');
    wp_enqueue_script('custom-floating-social-script', plugins_url('js/script.js', __FILE__), array('jquery'), null, true);

    // Localize script for passing PHP variables to JavaScript
    wp_localize_script('custom-floating-social-script', 'custom_floating_social_vars', array(
        'display_side' => get_option('floating_social_display_side', 'left'), // Default to 'left' if not set
    ));
}

add_action('wp_enqueue_scripts', 'custom_floating_social_enqueue_scripts');

// Function to display floating social buttons
function custom_floating_social_buttons() {
    $whatsapp_contact_number = get_option('whatsapp_contact_number');
    $youtube_channel_url = get_option('youtube_channel_url');
    $facebook_username = get_option('facebook_username');
    $twitter_username = get_option('twitter_username');
    $instagram_username = get_option('instagram_username');
    $display_side = get_option('floating_social_display_side', 'left');

    // Check if any of the social links are empty
    if (empty($whatsapp_contact_number) && empty($youtube_channel_url) && empty($facebook_username) && empty($twitter_username) && empty($instagram_username)) {
        return; // Exit function if all fields are empty
    }

    // Start outputting the buttons
    ?>
    <div class="custom-floating-social-buttons custom-floating-<?php echo esc_attr($display_side); ?>">
        <?php if (!empty($whatsapp_contact_number)) : ?>
            <a href="#" class="custom-floating-button whatsapp" data-number="<?php echo esc_attr($whatsapp_contact_number); ?>" title="Share via WhatsApp"><i class="fa fa-whatsapp"></i></a>
        <?php endif; ?>

        <?php if (!empty($youtube_channel_url)) : ?>
            <a href="<?php echo esc_url($youtube_channel_url); ?>" class="custom-floating-button youtube" title="Visit our YouTube Channel" target="_blank"><i class="fa fa-youtube"></i></a>
        <?php endif; ?>

        <?php if (!empty($facebook_username)) : ?>
            <a href="https://facebook.com/<?php echo esc_attr($facebook_username); ?>" class="custom-floating-button facebook" title="Share via Facebook" target="_blank"><i class="fa fa-facebook"></i></a>
        <?php endif; ?>

        <?php if (!empty($twitter_username)) : ?>
            <a href="https://twitter.com/intent/tweet?url=<?php echo esc_url(get_permalink()); ?>&text=<?php echo esc_attr(get_the_title()); ?>" class="custom-floating-button twitter" title="Share via Twitter" target="_blank"><i class="fa fa-twitter"></i></a>
        <?php endif; ?>

        <?php if (!empty($instagram_username)) : ?>
            <a href="https://instagram.com/<?php echo esc_attr($instagram_username); ?>" class="custom-floating-button instagram" title="Visit our Instagram Profile" target="_blank"><i class="fa fa-instagram"></i></a>
        <?php endif; ?>
    </div>
    <?php
}

add_action('wp_footer', 'custom_floating_social_buttons');

// Register plugin settings
function custom_floating_social_register_settings() {
    register_setting('custom-floating-social-settings', 'whatsapp_contact_number', 'sanitize_text_field');
    register_setting('custom-floating-social-settings', 'youtube_channel_url', 'esc_url_raw');
    register_setting('custom-floating-social-settings', 'facebook_username', 'sanitize_text_field');
    register_setting('custom-floating-social-settings', 'twitter_username', 'sanitize_text_field');
    register_setting('custom-floating-social-settings', 'instagram_username', 'sanitize_text_field');
    register_setting('custom-floating-social-settings', 'floating_social_display_side', array(
        'type' => 'string',
        'default' => 'left',
        'sanitize_callback' => function($value) {
            return in_array($value, array('left', 'right')) ? $value : 'left';
        },
    ));
}

add_action('admin_init', 'custom_floating_social_register_settings');

// Add plugin settings page
function custom_floating_social_add_settings_page() {
    add_options_page(
        'Custom Floating Social Buttons Settings',
        'Floating Social Buttons',
        'manage_options',
        'custom-floating-social-settings',
        'custom_floating_social_render_settings_page'
    );
}

add_action('admin_menu', 'custom_floating_social_add_settings_page');

// Render settings page content
function custom_floating_social_render_settings_page() {
    ?>
    <div class="wrap">
        <h1>Custom Floating Social Buttons Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom-floating-social-settings'); // Match the options group name here
            do_settings_sections('custom-floating-social-settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">WhatsApp Contact Number</th>
                    <td><input type="text" name="whatsapp_contact_number" value="<?php echo esc_attr(get_option('whatsapp_contact_number')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube Channel URL</th>
                    <td><input type="text" name="youtube_channel_url" value="<?php echo esc_attr(get_option('youtube_channel_url')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Facebook Username</th>
                    <td><input type="text" name="facebook_username" value="<?php echo esc_attr(get_option('facebook_username')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Twitter Username</th>
                    <td><input type="text" name="twitter_username" value="<?php echo esc_attr(get_option('twitter_username')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Instagram Username</th>
                    <td><input type="text" name="instagram_username" value="<?php echo esc_attr(get_option('instagram_username')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Display Side</th>
                    <td>
                        <label><input type="radio" name="floating_social_display_side" value="left" <?php checked(get_option('floating_social_display_side', 'left'), 'left'); ?> /> Left</label><br />
                        <label><input type="radio" name="floating_social_display_side" value="right" <?php checked(get_option('floating_social_display_side'), 'right'); ?> /> Right</label>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
