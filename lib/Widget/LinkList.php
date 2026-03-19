<?php
namespace Aras\Localize\Widget;

use Aras\Localize\Util\BaseLocalize;

class LinkList extends BaseLocalize {
    const SHORTCODE = 'aras_localize_link_list';
    const OPTIONS_PAGE = 'aras-localize';
    const FIELD_ENABLE_LINK_LIST = 'enable_link_list';

    public function register() {
        add_shortcode(self::SHORTCODE, [$this, 'render_shortcode']);
    }

    public function render_shortcode($atts = []) {
        if (!$this->is_enabled()) {
            return '';
        }

        if (is_admin() || is_feed() || is_robots()) {
            return '';
        }

        if (function_exists('wp_doing_ajax') && wp_doing_ajax()) {
            return '';
        }

        $languages = $this->get_all_languages();
        if (empty($languages)) {
            return '';
        }

        $current_url = $this->get_current_url();
        if (empty($current_url)) {
            return '';
        }

        $atts = shortcode_atts([
            'class' => '',
            'show_labels' => 'true',
            'show_current' => 'true',
        ], $atts, self::SHORTCODE);

        $class = 'aras-localize-link-list';
        if (!empty($atts['class'])) {
            $extra_classes = preg_split('/\\s+/', $atts['class']);
            $extra_classes = array_filter(array_map('sanitize_html_class', $extra_classes));
            if (!empty($extra_classes)) {
                $class .= ' ' . implode(' ', $extra_classes);
            }
        }

        $show_labels = $atts['show_labels'] !== 'false';
        $show_current = $atts['show_current'] !== 'false';
        $current_language = $this->get_current_language();

        $output = '<ul class="' . esc_attr($class) . '">';

        foreach ($languages as $code) {
            $url = $this->get_current_url($code);
            if (empty($url)) {
                continue;
            }

            $is_current = ($code === $current_language);
            
            if (!$show_current && $is_current) {
                continue;
            }

            $label = $show_labels ? $this->get_language_label($code) : strtoupper($code);
            $item_class = 'language-' . $code;
            if ($is_current) {
                $item_class .= ' current-language';
            }

            $output .= '<li class="' . esc_attr($item_class) . '">';
            // if ($is_current) {
            //     $output .= '<span>' . esc_html($label) . '</span>';
            // } else {
                $output .= '<a href="' . esc_url($url) . '" hreflang="' . esc_attr($code) . '">' . esc_html($label) . '</a>';
            // }
            $output .= '</li>';
        }

        $output .= '</ul>';

        return $output;
    }

    /**
     * Get the current language code based on URL
     * @return string
     */
    private function get_current_language() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return $this->sourceLanguage;
        }

        $request_path = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $request_path);
        
        $languages = $this->get_languages();
        
        if (isset($parts[1]) && in_array($parts[1], $languages, true)) {
            return $parts[1];
        }

        return $this->sourceLanguage;
    }

    /**
     * Get a human-readable label for a language code
     * @param string $code
     * @return string
     */
    private function get_language_label($code) {
        $labels = apply_filters('aras_localize_language_labels', [
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'pt' => 'Português',
            'ru' => 'Русский',
            'ja' => '日本語',
            'zh' => '中文',
            'ko' => '한국어',
            'ar' => 'العربية',
            'hi' => 'हिन्दी',
            'nl' => 'Nederlands',
            'sv' => 'Svenska',
            'da' => 'Dansk',
            'no' => 'Norsk',
            'fi' => 'Suomi',
            'pl' => 'Polski',
            'tr' => 'Türkçe',
            'he' => 'עברית',
            'th' => 'ไทย',
            'vi' => 'Tiếng Việt',
            'cs' => 'Čeština',
            'hu' => 'Magyar',
            'ro' => 'Română',
            'bg' => 'Български',
            'hr' => 'Hrvatski',
            'sk' => 'Slovenčina',
            'sl' => 'Slovenščina',
            'et' => 'Eesti',
            'lv' => 'Latviešu',
            'lt' => 'Lietuvių',
            'uk' => 'Українська',
            'el' => 'Ελληνικά',
            'ca' => 'Català',
            'eu' => 'Euskera',
            'gl' => 'Galego',
        ]);

        return isset($labels[$code]) ? $labels[$code] : strtoupper($code);
    }

    /**
     * Check if the link list widget is enabled
     * @return bool
     */
    private function is_enabled() {
        if (function_exists('get_field')) {
            $enabled = get_field(self::FIELD_ENABLE_LINK_LIST, 'option');
            return $enabled !== false;
        }

        return true;
    }
}