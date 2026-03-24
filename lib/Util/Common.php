<?php
namespace Aras\Localize\Util;

class Common {
    const TRANSIENT_KEY = 'aras_localize_hreflang_languages';
    const TRANSIENT_TTL = 21600;

    protected static $sourceLanguage = 'en';

    /**
     * Get all available languages (excluding source language)
     * @return array
     */
    public static function get_languages() {
        $filtered = apply_filters('aras_localize_hreflang_languages', null);
        if (is_array($filtered)) {
            return $filtered;
        }

        $cached = get_transient(self::TRANSIENT_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $project_key = get_option('project_key');
        if (empty($project_key)) {
            return [];
        }

        $api_key = self::get_api_key();
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

            // codes is languages without source language
            self::$sourceLanguage = $payload['data']['project']['sourceLanguage'] ?? 'en';
            $codes = array_diff($languages, [self::$sourceLanguage]);
        }
        else {
            return [];
        }
        
        if (!empty($codes)) {
            set_transient(self::TRANSIENT_KEY, $codes, self::TRANSIENT_TTL);
        }

        return $codes;
    }

    /**
     * Get all languages including source language
     * @return array
     */
    public static function get_all_languages() {
        $languages = self::get_languages();
        array_unshift($languages, self::$sourceLanguage);
        return $languages;
    }

    /**
     * Get the source language
     * @return string
     */
    public static function get_source_language() {
        return self::$sourceLanguage;
    }

    /**
     * Get the current URL for a specific language code
     * @param string|false $code Language code, false for current URL without language
     * @return string
     */
    public static function get_current_url($code = false) {
        if (empty($_SERVER['HTTP_HOST']) || empty($_SERVER['REQUEST_URI'])) {
            return '';
        }

        $scheme = 'http';
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)) {
            $scheme = 'https';
        }

        // if $code is false, we want to remove the language code from the url
        if ($code === false) {
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        $request_path = $_SERVER['REQUEST_URI'];
        $parts = explode('/', $request_path);

        // if $code is provided, we want to add the language code to the url
        // unless its self::$sourceLanguage, in which case we want to remove any language code from the url
        if ($code === self::$sourceLanguage) {
            // remove the first part of the path if it matches any of the languages
            if (isset($parts[1]) && in_array($parts[1], self::get_languages(), true)) {
                // remove the language code from the url
                $remaining_parts = array_slice($parts, 2);
                $clean_path = '/' . implode('/', $remaining_parts);
                // Clean up any double slashes
                $clean_path = preg_replace('#/+#', '/', $clean_path);
                return $scheme . '://' . $_SERVER['HTTP_HOST'] . $clean_path;
            }
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . $request_path;
        }

        // add the language code to the url
        if (isset($parts[1]) && in_array($parts[1], self::get_languages(), true)) {
            // replace the language code in the url
            $parts[1] = $code;
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . implode('/', $parts);
        }
        else {
            // add the language code to the url
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' . $code . $request_path;
        }
    }

    /**
     * Get the API key from ACF or options
     * @return string
     */
    public static function get_api_key() {
        $api_key = '';
        if (function_exists('get_field')) {
            $api_key = get_field('localize_api_key', 'option');
        }
        if (empty($api_key)) {
            $api_key = get_option('localize_api_key');
        }
        return $api_key;
    }
}