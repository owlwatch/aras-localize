<?php
/**
 * Plugin Name: Aras - Localize
 * Description: Custom LocalizeJS language switcher styled to match Aras branding.
 * Version: 1.0.0
 * Author: Aras
 */

if (!defined('ABSPATH')) {
    exit;
}

const ARAS_LOCALIZE_VERSION = '1.0.0';

function aras_localize_enqueue_assets() {
    if (is_admin()) {
        return;
    }

    $project_key = get_option('project_key');
    if (empty($project_key)) {
        return;
    }

    $handle = 'aras-localize-switcher';
    $script_url = plugins_url('assets/aras-localize.js', __FILE__);
    $style_url = plugins_url('assets/aras-localize.css', __FILE__);

    wp_enqueue_style($handle, $style_url, [], ARAS_LOCALIZE_VERSION);
    wp_enqueue_script($handle, $script_url, ['localizeFallback'], ARAS_LOCALIZE_VERSION, true);

    wp_localize_script($handle, 'ArasLocalize', [
        'selector' => '.aras-localize-switcher',
    ]);
}
add_action('wp_enqueue_scripts', 'aras_localize_enqueue_assets', 20);

function aras_localize_switcher_shortcode($atts = []) {
    $atts = shortcode_atts([
        'class' => '',
    ], $atts, 'aras_localize_switcher');

    $class = 'aras-localize-switcher';
    if (!empty($atts['class'])) {
        $extra_classes = preg_split('/\\s+/', $atts['class']);
        $extra_classes = array_filter(array_map('sanitize_html_class', $extra_classes));
        if (!empty($extra_classes)) {
            $class .= ' ' . implode(' ', $extra_classes);
        }
    }

    return '<div class="' . esc_attr($class) . '"></div>';
}
add_shortcode('aras_localize_switcher', 'aras_localize_switcher_shortcode');

// how can we be sure that the add_shortcode in the theme isn't run after this?
add_action('init', function() {
    remove_shortcode('custom_language_dropdown');
    add_shortcode('custom_language_dropdown', 'aras_localize_switcher_shortcode');
}, 20);