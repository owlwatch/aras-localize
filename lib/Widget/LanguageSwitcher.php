<?php
namespace Aras\Localize\Widget;

class LanguageSwitcher {
    const SHORTCODE = 'aras_localize_switcher';
    const OPTIONS_PAGE = 'aras-localize';
    const FIELD_ENABLE_SWITCHER = 'enable_switcher';

    public function register() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets'], 20);
        add_shortcode(self::SHORTCODE, [$this, 'render_shortcode']);

        add_action('init', [$this, 'replace_theme_shortcode'], 20);
    }

    public function enqueue_assets() {
        if (is_admin()) {
            return;
        }

        if (!$this->is_enabled()) {
            return;
        }

        $project_key = get_option('project_key');
        if (empty($project_key)) {
            return;
        }

        $handle = 'aras-localize-switcher';
        $script_url = plugins_url('assets/aras-localize.js', $this->get_plugin_file());
        $style_url = plugins_url('assets/aras-localize.css', $this->get_plugin_file());

        wp_enqueue_style($handle, $style_url, [], \ARAS_LOCALIZE_VERSION);
        wp_enqueue_script($handle, $script_url, ['localizeFallback'], \ARAS_LOCALIZE_VERSION, true);

        wp_localize_script($handle, 'ArasLocalize', [
            'selector' => '.aras-localize-switcher',
        ]);
    }

    public function render_shortcode($atts = []) {
        if (!$this->is_enabled()) {
            return '';
        }

        $atts = shortcode_atts([
            'class' => '',
        ], $atts, self::SHORTCODE);

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

    public function replace_theme_shortcode() {
        remove_shortcode('custom_language_dropdown');
        add_shortcode('custom_language_dropdown', [$this, 'render_shortcode']);
    }

    private function is_enabled() {
        if (function_exists('get_field')) {
            $enabled = get_field(self::FIELD_ENABLE_SWITCHER, 'option');
            return $enabled !== false;
        }

        return true;
    }

    private function get_plugin_file() {
        return dirname(__DIR__, 2) . '/aras-localize.php';
    }
}
