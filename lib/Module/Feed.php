<?php
namespace Aras\Localize\Module;

use Aras\Localize\Util\Common;

class Feed {
    public function register() {
        add_action('wp_head', [$this, 'maybe_remove_feed_links'], 1);
        add_action('template_redirect', [$this, 'maybe_disable_feed'], 0);
    }

    public function maybe_remove_feed_links() {
        if (is_admin() || is_robots()) {
            return;
        }

        if ($this->get_current_language() === Common::get_source_language()) {
            return;
        }

        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'feed_links_extra', 3);
    }

    public function maybe_disable_feed() {
        if (is_admin() || !is_feed() || is_robots()) {
            return;
        }

        if (function_exists('wp_doing_ajax') && wp_doing_ajax()) {
            return;
        }

        if ($this->get_current_language() === Common::get_source_language()) {
            return;
        }

        global $wp_query;

        if (isset($wp_query) && is_object($wp_query)) {
            $wp_query->set_404();
        }

        status_header(404);
        nocache_headers();

        exit;
    }

    private function get_current_language() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return Common::get_source_language();
        }

        $request_path = wp_parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (!is_string($request_path) || $request_path === '') {
            return Common::get_source_language();
        }

        $parts = explode('/', $request_path);
        $languages = Common::get_languages();

        if (isset($parts[1]) && in_array($parts[1], $languages, true)) {
            return $parts[1];
        }

        return Common::get_source_language();
    }
}