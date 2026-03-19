<?php
namespace Aras\Localize\Util;

class Hreflang extends BaseLocalize {

    public function register() {
        add_action('wp_head', [$this, 'render'], 5);
    }

    public function render() {
        if (is_admin() || is_feed() || is_robots()) {
            return;
        }

        if (function_exists('wp_doing_ajax') && wp_doing_ajax()) {
            return;
        }

        $languages = $this->get_languages();
        if (empty($languages)) {
            return;
        }

        $current_url = $this->get_current_url();
        if (empty($current_url)) {
            return;
        }

        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($this->get_current_url($this->sourceLanguage)) . '" />' . "\n";
        echo '<link rel="alternate" hreflang="'.esc_attr($this->sourceLanguage).'" href="' . esc_url($this->get_current_url($this->sourceLanguage)) . '" />' . "\n";
        foreach ($languages as $code) {
            $url = $this->get_current_url($code);
            if (empty($url)) {
                continue;
            }
            echo '<link rel="alternate" hreflang="' . esc_attr($code) . '" href="' . esc_url($url) . '" />' . "\n";
        }

    }
}
