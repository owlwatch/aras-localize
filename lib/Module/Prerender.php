<?php
namespace Aras\Localize\Module;

use Aras\Localize\Util\Common;

class Prerender {
    const FIELD_ENABLE_PRERENDER = 'enable_prerender';
    const FIELD_PRERENDER_SERVER = 'prerender_server';
    const FIELD_PRERENDER_USER_AGENTS = 'prerender_user_agents';
    const SUB_FIELD_USER_AGENT_PATTERN = 'pattern';

    /**
     * Register the prerender request handler.
     *
     * @return void
     */
    public function register() {
        add_action('template_redirect', [$this, 'maybe_proxy_request'], 0);
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

        $server = $this->get_prerender_server();
        $current_url = Common::get_current_url(false);
        // for testing, lets make sure that xplm.local is converted to xplm.com
        if (strpos($current_url, 'xplm.local') !== false) {
            $current_url = str_replace('xplm.local', 'xplm.com', $current_url);
        }
        if ($current_url !== '') {
            $current_url = remove_query_arg('prerender', $current_url);
        }

        if ($server === '' || $current_url === '') {
            return;
        }

        $target_url = $server . rawurlencode($current_url);
        $response = wp_remote_get($target_url, [
            'timeout' => 20,
            'redirection' => 5,
            'headers' => $this->get_forward_headers(),
        ]);

        if (is_wp_error($response)) {
            // error_log( 'Prerender request failed: ' . $response->get_error_message() );
            return;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $headers = wp_remote_retrieve_headers($response);

        if ($status_code > 0) {
            status_header($status_code);
        }

        error_log( 'Prerender response: ' . $status_code . ' for ' . $current_url );
        error_log( 'Response headers: ' . print_r($headers, true) );
        error_log( 'Response body: ' . substr($body, 0, 500) ); // log first 500 chars of body for debugging

        $this->send_response_headers($headers);
        echo $body;
        exit;
    }

    /**
     * Determine whether the current request should be prerendered.
     *
     * @return bool
     */
    private function should_prerender_request() {
        if (isset($_REQUEST['prerender'])) {
            return true;
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

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $headers['User-Agent'] = (string) $_SERVER['HTTP_USER_AGENT'];
        }

        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $headers['Accept-Language'] = (string) $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }

        return $headers;
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
