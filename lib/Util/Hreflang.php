<?php
namespace Aras\Localize\Util;

class Hreflang {
    const TRANSIENT_KEY = 'aras_localize_hreflang_languages';
    const TRANSIENT_TTL = 21600;

    private $sourceLanguage = 'en';

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
        foreach ($languages as $code) {
            $url = $this->get_current_url($code);
            if (empty($url)) {
                continue;
            }
            echo '<link rel="alternate" hreflang="' . esc_attr($code) . '" href="' . esc_url($url) . '" />' . "\n";
        }

    }

    private function get_languages() {
        $filtered = apply_filters('aras_localize_hreflang_languages', null);
        if (is_array($filtered)) {
            if (!empty($codes)) {
                return $codes;
            }
        }

        $cached = get_transient(self::TRANSIENT_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $project_key = get_option('project_key');
        if (empty($project_key)) {
            return [];
        }

        $api_key = '';
        if (function_exists('get_field')) {
            $api_key = get_field('localize_api_key', 'option');
        }
        if (empty($api_key)) {
            $api_key = get_option('localize_api_key');
        }
        if (empty($api_key)) {
            return [];
        }

        $endpoint = 'https://api.localizejs.com/v2.0/projects/' . rawurlencode($project_key);
        $response = wp_remote_get($endpoint, [
            'timeout' => 6,
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Accept' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            return [];
        }

        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            return [];
        }

        $payload = json_decode($body, true);

        if (!is_array($payload)) {
            return [];
        }

        // languages are found in the $payload['data']['project']['enabledLanguages'] array
        if (isset($payload['data']['project']['enabledLanguages']) && is_array($payload['data']['project']['enabledLanguages'])) {
            $languages = $payload['data']['project']['enabledLanguages'];

            // codes is languages without $payload['data']['project']['sourceLanguage']
            $this->sourceLanguage = $payload['data']['project']['sourceLanguage'] ?? 'en';
            $codes = array_diff($languages, [$this->sourceLanguage]);
        }
        else {
            return [];
        }
        
        if (!empty($codes)) {
            set_transient(self::TRANSIENT_KEY, $codes, self::TRANSIENT_TTL);
        }

        return $codes;
    }

   
    private function get_current_url( $code=false) {
        if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
            return '';
        }

        $scheme = 'http';
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)) {
            $scheme = 'https';
        }

        // if $code is false, we want to remove the language code from the url
        if( $code === false ) {
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        // if $code is provided, we want to add the language code to the url
        // unless its $this->sourceLanguage, in which case we want to remove any language code from the url
        if( $code === $this->sourceLanguage ) {
            // remove the first part of the path if it matches any of the languages
            $parts = explode('/', $_SERVER['REQUEST_URI']);
            if( isset( $parts[1] ) && in_array($parts[1], $this->get_languages(), true) ){
                // remove the language code from the url
                return $scheme . '://' . $_SERVER['HTTP_HOST'] . implode( '/', array_slice($parts, 2) );
            }
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        // add the language code to the url
        $parts = explode('/', $_SERVER['REQUEST_URI']);
        if( isset( $parts[1] ) && in_array($parts[1], $this->get_languages(), true) ){
            // replace the language code in the url
            $parts[1] = $code;
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . implode( '/', $parts );
        }
        else {
            // add the language code to the url
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $code . $_SERVER['REQUEST_URI'];
        }
    }
}
