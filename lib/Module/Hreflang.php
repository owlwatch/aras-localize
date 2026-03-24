<?php
namespace Aras\Localize\Module;

use Aras\Localize\Util\Common;

class Hreflang {

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

        $languages = Common::get_languages();
        if (empty($languages)) {
            return;
        }

        $current_url = Common::get_current_url();
        if (empty($current_url)) {
            return;
        }

        $source_language = Common::get_source_language();

        echo '<link rel="alternate" hreflang="x-default" href="' . esc_url(Common::get_current_url($source_language)) . '" />' . "\n";
        echo '<link rel="alternate" hreflang="'.esc_attr($source_language).'" href="' . esc_url(Common::get_current_url($source_language)) . '" />' . "\n";
        foreach ($languages as $code) {
            $url = Common::get_current_url($code);
            if (empty($url)) {
                continue;
            }
            echo '<link rel="alternate" hreflang="' . esc_attr($code) . '" href="' . esc_url($url) . '" />' . "\n";
        }

    }
}