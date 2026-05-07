<?php
namespace Aras\Localize\Module;

use Aras\Localize\Util\Common;

class Prerender {
    const FIELD_ENABLE_PRERENDER = 'enable_prerender';
    const FIELD_PRERENDER_SERVER = 'prerender_server';
    const FIELD_PRERENDER_AUTH_TOKEN = 'prerender_auth_token';
    const FIELD_PRERENDER_USER_AGENTS = 'prerender_user_agents';
    const SUB_FIELD_USER_AGENT_PATTERN = 'pattern';

    /**
     * Preserve flag intent after sanitizing global request vars.
     *
     * @var array<string, bool>
     */
    private $request_flags = [
        'prerender' => false,
        'nocache' => false,
    ];

    /**
     * Register the prerender request handler.
     *
     * @return void
     */
    public function register() {
        add_action('init', [$this, 'sanitize_request_flags'], 0);
        add_action('template_redirect', [$this, 'maybe_proxy_request'], 0);
    }

    /**
     * Remove internal query flags from global request state.
     *
     * This ensures WordPress canonical, hreflang and other URL builders do not
     * include operational params like `prerender` and `nocache`.
     *
     * @return void
     */
    public function sanitize_request_flags() {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
        if ($request_uri !== '') {
            $uri_parts = wp_parse_url($request_uri);
            $query = [];

            if (isset($uri_parts['query']) && is_string($uri_parts['query']) && $uri_parts['query'] !== '') {
                parse_str($uri_parts['query'], $query);
            }

            $this->capture_request_flags($query);

            if ($this->strip_request_flags($query)) {
                $path = isset($uri_parts['path']) ? (string) $uri_parts['path'] : '/';
                $normalized_query = http_build_query($query);
                $_SERVER['REQUEST_URI'] = $normalized_query !== '' ? $path . '?' . $normalized_query : $path;
                $_SERVER['QUERY_STRING'] = $normalized_query;
            }
        }

        if (is_array($_GET)) {
            $this->capture_request_flags($_GET);
            $this->strip_request_flags($_GET);
        }

        if (is_array($_REQUEST)) {
            $this->capture_request_flags($_REQUEST);
            $this->strip_request_flags($_REQUEST);
        }
    }

    /**
     * Proxy the current request to the configured prerender server when enabled.
     *
     * @return void
     */
    public function maybe_proxy_request() {
        if (!$this->is_enabled() || is_admin() || is_feed() || is_robots()) {
            return;
        }

        if ((function_exists('wp_doing_ajax') && wp_doing_ajax()) || (defined('REST_REQUEST') && REST_REQUEST)) {
            return;
        }

        if (!$this->should_prerender_request()) {
            return;
        }

        // we need the full url with the query string
        error_log( 'Prerendering request for URL: ' . Common::get_current_url() );
        
        $server = $this->get_prerender_server();
        $current_url = Common::get_current_url(false);
        // for testing, lets make sure that xplm.local is converted to xplm.com
        if (strpos($current_url, 'xplm.local') !== false) {
            $current_url = str_replace('xplm.local', 'xplm.com', $current_url);
        }
        if ($current_url !== '') {
            $current_url = remove_query_arg('nocache', $current_url);
        }

        if ($server === '' || $current_url === '') {
            return;
        }

        
        $target_url = $server . rawurlencode($current_url);

        // Preserve nocache behavior after sanitizing global request vars.
        if ($this->request_has_flag('nocache')) {
            $target_url = add_query_arg('cache', 'false', $target_url);
        }

        $response = wp_remote_get($target_url, [
            'timeout' => 20,
            'redirection' => 5,
            'headers' => $this->get_forward_headers(),
        ]);

        if (is_wp_error($response)) {
            error_log( 'Prerender request failed: ' . $response->get_error_message() );
            return;
        }


        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);

        if( $status_code !== 200 ){
            error_log( 'Prerender request returned non-200 status: ' . $status_code . ' for ' . $current_url );
            return;
        }

        if ($status_code > 0) {
            status_header($status_code);
        }

        // $this->send_response_headers($headers);
        
        // Post-process the response to fix JSON-LD and Open Graph locale values.
        $processed_body = $this->process_json_ld_response($body);
        $processed_body = $this->process_og_locale_response($processed_body);
        
        echo $processed_body;
        exit;
    }

    /**
     * Determine whether the current request should be prerendered.
     *
     * @return bool
     */
    private function should_prerender_request() {
        if ($this->get_current_language() === Common::get_source_language()) {
            return false;
        }

        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
        if ($user_agent === '') {
            return false;
        }

        foreach ($this->get_user_agent_patterns() as $pattern) {
            if ($pattern === '') {
                continue;
            }

            $result = @preg_match($pattern, $user_agent);
            if ($result === 1) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the current language code from the request URL.
     *
     * @return string
     */
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

    /**
     * Get configured user agent regex patterns.
     *
     * @return array
     */
    private function get_user_agent_patterns() {
        $rows = $this->get_option_field(self::FIELD_PRERENDER_USER_AGENTS);
        if (!is_array($rows)) {
            return [];
        }

        $patterns = [];

        foreach ($rows as $row) {
            if (!is_array($row) || empty($row[self::SUB_FIELD_USER_AGENT_PATTERN])) {
                continue;
            }

            $patterns[] = trim((string) $row[self::SUB_FIELD_USER_AGENT_PATTERN]);
        }

        return $patterns;
    }

    /**
     * Get prerender server setting.
     *
     * @return string
     */
    private function get_prerender_server() {
        return trim((string) $this->get_option_field(self::FIELD_PRERENDER_SERVER));
    }

    /**
     * Get prerender authorization token setting.
     *
     * @return string
     */
    private function get_prerender_auth_token() {
        return trim((string) $this->get_option_field(self::FIELD_PRERENDER_AUTH_TOKEN));
    }

    /**
     * Check whether prerendering is enabled.
     *
     * @return bool
     */
    private function is_enabled() {
        return (bool) $this->get_option_field(self::FIELD_ENABLE_PRERENDER);
    }

    /**
     * Read a field from ACF options or core options.
     *
     * @param string $field_name
     * @return mixed
     */
    private function get_option_field($field_name) {
        if (function_exists('get_field')) {
            return get_field($field_name, 'option');
        }

        return get_option($field_name);
    }

    /**
     * Forward selected headers to the prerender service.
     *
     * @return array
     */
    private function get_forward_headers() {
        $headers = [];

        $auth_token = $this->get_prerender_auth_token();
        if ($auth_token !== '') {
            $headers['Authorization'] = 'Bearer ' . $auth_token;
        }

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $headers['User-Agent'] = (string) $_SERVER['HTTP_USER_AGENT'];
        }

        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $headers['Accept-Language'] = (string) $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        return $headers;
    }

    /**
     * Capture known internal flags before they are removed from globals.
     *
     * @param array $params
     * @return void
     */
    private function capture_request_flags($params) {
        if (!is_array($params)) {
            return;
        }

        foreach (['prerender', 'nocache'] as $flag) {
            if (!array_key_exists($flag, $params)) {
                continue;
            }

            $value = $params[$flag];
            if ($value === '' || $value === null) {
                $this->request_flags[$flag] = true;
                continue;
            }

            $normalized = strtolower((string) $value);
            if (in_array($normalized, ['0', 'false', 'no', 'off'], true)) {
                continue;
            }

            $this->request_flags[$flag] = true;
        }
    }

    /**
     * Remove internal flags from a parameter array.
     *
     * @param array $params
     * @return bool Whether any params were removed.
     */
    private function strip_request_flags(&$params) {
        if (!is_array($params)) {
            return false;
        }

        $removed = false;
        foreach (['prerender', 'nocache'] as $flag) {
            if (array_key_exists($flag, $params)) {
                unset($params[$flag]);
                $removed = true;
            }
        }

        return $removed;
    }

    /**
     * Check if an internal request flag was present/enabled.
     *
     * @param string $flag
     * @return bool
     */
    private function request_has_flag($flag) {
        return !empty($this->request_flags[$flag]);
    }

    /**
     * Post-process the prerender response to fix JSON-LD inLanguage values.
     *
     * @param string $html The HTML response body
     * @return string The processed HTML with corrected inLanguage values
     */
    private function process_json_ld_response($html) {
        $current_language = $this->get_current_language();
        
        // Find all JSON-LD script tags
        $pattern = '/<script\s+type=["\']application\/ld\+json["\'][^>]*>\s*({.*?})\s*<\/script>/is';
        
        $processed_html = preg_replace_callback($pattern, function($matches) use ($current_language) {
            $json_content = $matches[1];
            
            // Decode the JSON
            $data = json_decode($json_content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                // If JSON is invalid, return original
                return $matches[0];
            }
            
            // Update inLanguage values recursively
            $updated_data = $this->update_in_language_recursive($data, $current_language);
            
            // Translate text content in the JSON-LD
            $translated_data = $this->translate_json_ld_content($updated_data, $current_language);
            
            // Encode back to JSON
            $updated_json = json_encode($translated_data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
            if ($updated_json === false) {
                // If encoding fails, return original
                return $matches[0];
            }
            
            // Replace the JSON content in the script tag
            $script_tag = str_replace($json_content, $updated_json, $matches[0]);
            
            return $script_tag;
        }, $html);
        
        return $processed_html ?: $html;
    }

    /**
     * Post-process prerender HTML to update og:locale meta tags.
     *
     * @param string $html The HTML response body
     * @return string The processed HTML with corrected og:locale values
     */
    private function process_og_locale_response($html) {
        if (!is_string($html) || $html === '') {
            return $html;
        }

        $current_language = $this->get_current_language();
        $locale = Common::get_language_with_country_code($current_language);
        // Open Graph locale convention uses underscore (en_US).
        $og_locale = str_replace('-', '_', $locale);

        $pattern = '/<meta\s+[^>]*property=["\']og:locale["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i';

        $processed_html = preg_replace_callback($pattern, function($matches) use ($og_locale) {
            return str_replace($matches[1], $og_locale, $matches[0]);
        }, $html);

        return $processed_html ?: $html;
    }
    
    /**
     * Recursively update inLanguage values in JSON-LD data.
     *
     * @param mixed $data The data to process
     * @param string $language The target language code
     * @return mixed The processed data
     */
    private function update_in_language_recursive($data, $language) {
        if (is_array($data)) {
            $result = [];
            
            foreach ($data as $key => $value) {
                if ($key === 'inLanguage') {
                    // Update inLanguage to the current language with country code
                    $result[$key] = Common::get_language_with_country_code($language);
                } else {
                    // Recursively process nested data
                    $result[$key] = $this->update_in_language_recursive($value, $language);
                }
            }
            
            return $result;
        }
        
        return $data;
    }
    
    /**
     * Translate text content in JSON-LD data.
     *
     * @param mixed $data The JSON-LD data to process
     * @param string $language The target language code
     * @return mixed The processed data with translations
     */
    private function translate_json_ld_content($data, $language) {
        // Skip translation for source language
        if ($language === Common::get_source_language()) {
            return $data;
        }
        
        // Collect all translatable text
        $translatable_text = [];
        $this->collect_translatable_text($data, $translatable_text);
        
        if (empty($translatable_text)) {
            return $data;
        }
        
        // Get translations from API
        $translations = Common::get_phrases($translatable_text, $language);

        if (empty($translations)) {
            return $data;
        }
        
        // Apply translations to the data
        return $this->apply_translations($data, $translations);
    }
    
    /**
     * Recursively collect translatable text from JSON-LD data.
     *
     * @param mixed $data The data to process
     * @param array &$translatable_text Reference to array collecting translatable strings
     * @return void
     */
    private function collect_translatable_text($data, &$translatable_text) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                // Check if this is a translatable field
                if (in_array($key, ['name', 'description', 'text'], true) && is_string($value) && !empty($value)) {
                    $translatable_text[] = $value;
                } else {
                    // Recursively process nested data
                    $this->collect_translatable_text($value, $translatable_text);
                }
            }
        }
    }
    
    /**
     * Recursively apply translations to JSON-LD data.
     *
     * @param mixed $data The data to process
     * @param array $translations Translation mapping
     * @return mixed The processed data with translations applied
     */
    private function apply_translations($data, $translations) {
        if (is_array($data)) {
            $result = [];
            
            foreach ($data as $key => $value) {
                // Check if this is a translatable field with available translation
                if (in_array($key, ['name', 'description', 'text'], true) && is_string($value) && isset($translations[$value])) {
                    $result[$key] = $translations[$value];
                } else {
                    // Recursively process nested data
                    $result[$key] = $this->apply_translations($value, $translations);
                }
            }
            
            return $result;
        }
        
        return $data;
    }
    
    /**
     * Send response headers from the upstream prerender response.
     *
     * @param mixed $headers
     * @return void
     */
    private function send_response_headers($headers) {
        $excluded = [
            'connection',
            'content-length',
            'host',
            'transfer-encoding',
        ];

        if (is_object($headers) && method_exists($headers, 'getAll')) {
            $headers = $headers->getAll();
        }

        if (!is_array($headers)) {
            return;
        }

        foreach ($headers as $name => $value) {
            $header_name = strtolower((string) $name);

            if (in_array($header_name, $excluded, true)) {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $header_value) {
                    header($name . ': ' . $header_value, false);
                }
                continue;
            }

            header($name . ': ' . $value);
        }
    }
}
