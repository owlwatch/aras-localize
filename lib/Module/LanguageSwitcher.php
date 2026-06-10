<?php
namespace Aras\Localize\Module;

use Aras\Localize\Util\Common;

class LanguageSwitcher {
    const SHORTCODE = 'aras_localize_switcher';
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
            'availableLanguages' => array_values(Common::get_languages()),
            'sourceLanguage' => Common::get_source_language(),
        ]);
    }

    public function render_shortcode($atts = []) {
        if (!$this->is_enabled()) {
            return '';
        }

        $languages = Common::get_all_languages();
        if (empty($languages)) {
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

        $current_language = $this->get_current_language();
        $current_code = strtoupper(explode('-', $current_language)[0]);

        $output = '<div class="' . esc_attr($class) . '" data-current-lang="' . esc_attr($current_language) . '">';
        $output .= '<div class="aras-localize">';
        $output .= '<button type="button" class="aras-localize__toggle" aria-haspopup="listbox" aria-expanded="false" data-localize-ignore="true" translate="no">';
        $output .= '<span class="aras-localize__code">' . esc_html($current_code) . '</span>';
        $output .= '<span class="aras-localize__chevron" aria-hidden="true"></span>';
        $output .= '</button>';
        $output .= '<ul class="aras-localize__menu" role="listbox">';

        foreach ($languages as $code) {
            $url = Common::get_current_url($code);
            if (empty($url)) {
                continue;
            }

            $is_current = $code === $current_language;
            $item_class = 'aras-localize__item';
            $option_class = 'aras-localize__option';

            if ($is_current) {
                $option_class .= ' is-active';
            }

            $output .= '<li class="' . esc_attr($item_class) . '" role="option" aria-selected="' . ($is_current ? 'true' : 'false') . '">';
            $output .= '<a href="' . esc_url($url) . '" class="' . esc_attr($option_class) . '" data-lang="' . esc_attr($code) . '" data-url="' . esc_url($url) . '" data-localize-ignore="true" translate="no">';
            $output .= esc_html($this->get_language_label($code));
            $output .= '</a>';
            $output .= '</li>';
        }

        $output .= '</ul>';
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    public function replace_theme_shortcode() {
        remove_shortcode('custom_language_dropdown');
        add_shortcode('custom_language_dropdown', [$this, 'render_shortcode']);
    }

    public function get_acf_fields() {
        return [
            [
                'key' => 'field_aras_localize_switcher_tab',
                'label' => 'Language Switcher',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_aras_localize_enable_switcher',
                'label' => 'Enable switcher',
                'name' => self::FIELD_ENABLE_SWITCHER,
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ],
        ];
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

    /**
     * Get the current language code based on the request URL.
     *
     * @return string
     */
    private function get_current_language() {
        $languages = Common::get_languages();
        $source_language = Common::get_source_language();

        if (empty($_SERVER['REQUEST_URI'])) {
            return $source_language;
        }

        $parts = explode('/', $_SERVER['REQUEST_URI']);

        if (isset($parts[1]) && in_array($parts[1], $languages, true)) {
            return $parts[1];
        }

        return $source_language;
    }

    /**
     * Get a human-readable label for a language code.
     *
     * @param string $code
     * @return string
     */
    private function get_language_label($code) {
        $labels = apply_filters('aras_localize_language_labels', [
            'en' => 'English',
            'es' => 'Espanol',
            'fr' => 'Francais',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Portugues',
            'ru' => 'Russkiy',
            'ja' => 'Japanese',
            'zh' => 'Chinese',
            'ko' => 'Korean',
            'ar' => 'Arabic',
            'hi' => 'Hindi',
            'nl' => 'Nederlands',
            'sv' => 'Svenska',
            'da' => 'Dansk',
            'no' => 'Norsk',
            'fi' => 'Suomi',
            'pl' => 'Polski',
            'tr' => 'Turkce',
            'he' => 'Hebrew',
            'th' => 'Thai',
            'vi' => 'Tieng Viet',
            'cs' => 'Cestina',
            'hu' => 'Magyar',
            'ro' => 'Romana',
            'bg' => 'Bulgarian',
            'hr' => 'Hrvatski',
            'sk' => 'Slovencina',
            'sl' => 'Slovenscina',
            'et' => 'Eesti',
            'lv' => 'Latviesu',
            'lt' => 'Lietuviu',
            'uk' => 'Ukrainian',
            'el' => 'Greek',
            'ca' => 'Catala',
            'eu' => 'Euskera',
            'gl' => 'Galego',
        ]);

        return isset($labels[$code]) ? $labels[$code] : strtoupper($code);
    }
}
