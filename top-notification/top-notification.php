<?php
/*
* Plugin Name:  Top Notification
* Plugin URI:  https://wordpress.org/plugins/top-notification
* Description: “A top notification plugin."
* Version: 1.0.0
* Author: Sinthia Neha
* Author URI:  https://sinthianeha
* Requires at least: 6.1
* Requires PHP: 7
* License: GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Text domain: my-plugin
*/

defined( 'ABSPATH' ) || exit;


add_action('wp_enqueue_scripts', function () {
    if (is_front_page()) {
        wp_enqueue_style(
            'top-notification-style',
            plugin_dir_url(__FILE__) . 'public/css/main.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'top-notification-script',
            plugin_dir_url(__FILE__) . 'public/js/main.js',
            [],
            '1.0.0',
            true
        );
    }
});

add_action('wp_head', function () {
    if (is_front_page()) {
        $notif_query = new WP_Query([
            'post_type' => 'notification-message',
            'posts_per_page' => 1,
            'post_status' => 'publish',
        ]);

        if ($notif_query->have_posts()) {
            $notif_query->the_post();
            $bg_color = get_post_meta(get_the_ID(), '_notification_bg_color', true) ?: '#f8d7da';
            $text_color = get_post_meta(get_the_ID(), '_notification_text_color', true) ?: '#721c24';
            $font_size = get_post_meta(get_the_ID(), '_notification_font_size', true) ?: '16px';
            ?>
            
            <style>
                #top-notification {
                    background-color: <?php echo esc_attr($bg_color); ?>;
                    color: <?php echo esc_attr($text_color); ?>;
                    font-size: <?php echo esc_attr($font_size); ?>;
                }
            </style>
            <div id="top-notification" style="display:none;">
                <span id="notification-message">
                    <strong><?php echo esc_html(get_the_title()); ?>:</strong>
                    <?php echo wp_kses_post(get_the_content()); ?>
                </span>
                <button onclick="hideNotification()">✖</button>
            </div>
            <?php
            wp_reset_postdata();
        }
    }
});



add_action('init', function(){
    register_post_type('notification-message', [
        'labels' => [
            'name' => 'Top Notification',
            'singular_name' => 'Top Notification',
            'add_new_item' => 'New Notification',
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title'],
    ]);
});

add_action('add_meta_boxes', function () {
    add_meta_box(
        'notification_color_box',
        'Background Color',
        'render_notification_color_field',
        'notification-message',
        'side',
        'default'
    );
});



function render_notification_color_field($post) {
    $bg_value = get_post_meta($post->ID, '_notification_bg_color', true);
    $text_value = get_post_meta($post->ID, '_notification_text_color', true);
    $font_size = get_post_meta($post->ID, '_notification_font_size', true);

    echo '<label for="notification-bg-color">Choose a background color:</label><br>';
    echo '<input type="color" id="notification-bg-color" name="notification-bg-color" value="' . esc_attr($bg_value ?: '#f8d7da') . '" /><br><br>';

    echo '<label for="notification-text-color">Choose a text color:</label><br>';
    echo '<input type="color" id="notification-text-color" name="notification-text-color" value="' . esc_attr($text_value ?: '#721c24') . '" /> <br><br>';

    echo '<label for="notification-font-size">Font size (e.g., 16px):</label><br>';
    echo '<input type="text" id="notification-font-size" name="notification-font-size" value="' . esc_attr($font_size ?: '16px') . '" />';
}


add_action('save_post', function ($post_id) {
    if (isset($_POST['notification-bg-color'])) {
        update_post_meta($post_id, '_notification_bg_color', sanitize_hex_color($_POST['notification-bg-color']));
    }

    if (isset($_POST['notification-text-color'])) {
        update_post_meta($post_id, '_notification_text_color', sanitize_hex_color($_POST['notification-text-color']));
    }

    if (isset($_POST['notification-font-size'])) {
        update_post_meta($post_id, '_notification_font_size', sanitize_text_field($_POST['notification-font-size']));
    }
});





