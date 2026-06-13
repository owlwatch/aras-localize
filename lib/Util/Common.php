<?php
namespace Aras\Localize\Util;

use Aras\Localize\ACF;
use Aras\Localize\API\LocalizeAPI;

class Common {
    const TRANSIENT_KEY = 'aras_localize_hreflang_languages';
    const SOURCE_LANGUAGE_TRANSIENT_KEY = 'aras_localize_source_language';
    const TRANSIENT_TTL = 21600;

    protected static $sourceLanguage = 'en';

    /**
     * Get all available languages (excluding source language)
     * @return array
     */
    public static function get_languages() {
        $filtered = apply_filters('aras_localize_hreflang_languages', null);
        if (is_array($filtered)) {
            return array_values(array_unique(array_map([self::class, 'normalize_language_code'], $filtered)));
        }

        $cached_source_language = get_transient(self::SOURCE_LANGUAGE_TRANSIENT_KEY);
        if (is_string($cached_source_language) && $cached_source_language !== '') {
            self::$sourceLanguage = self::normalize_language_code($cached_source_language);
        }

        $cached = get_transient(self::TRANSIENT_KEY);
        if (is_array($cached)) {
            return $cached;
        }

        $project_key = get_option('project_key');
        if (empty($project_key)) {
            return [];
        }

        $api = LocalizeAPI::create_from_options();
        if ($api === null) {
            return [];
        }

        $project_data = $api->get_project();
        if ($project_data === false) {
            return [];
        }

        // languages are found in the project data enabledLanguages array
        if (isset($project_data['project']['enabledLanguages']) && is_array($project_data['project']['enabledLanguages'])) {
            $languages = array_values(array_unique(array_map([self::class, 'normalize_language_code'], $project_data['project']['enabledLanguages'])));

            // codes is languages without source language
            self::$sourceLanguage = self::normalize_language_code($project_data['project']['sourceLanguage'] ?? 'en');
            $codes = array_diff($languages, [self::$sourceLanguage]);
        }
        else {
            return [];
        }
        
        if (!empty($codes)) {
            set_transient(self::TRANSIENT_KEY, $codes, self::TRANSIENT_TTL);
        }

        set_transient(self::SOURCE_LANGUAGE_TRANSIENT_KEY, self::$sourceLanguage, self::TRANSIENT_TTL);

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
        return self::normalize_language_code(self::$sourceLanguage);
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

        // Always remove /prerender/ path suffix from URLs to ensure clean hreflangs
        $request_path = self::remove_prerender_path_suffix($_SERVER['REQUEST_URI']);

        // if $code is false, we want to remove the language code from the url
        if ($code === false) {
            return $scheme . '://' . $_SERVER['HTTP_HOST'] . $request_path;
        }
        $code = self::normalize_language_code((string) $code);
        $parts = explode('/', $request_path);
        $request_language = isset($parts[1]) ? self::normalize_language_code($parts[1]) : '';

        // if $code is provided, we want to add the language code to the url
        // unless its self::$sourceLanguage, in which case we want to remove any language code from the url
        if ($code === self::$sourceLanguage) {
            // remove the first part of the path if it matches any of the languages
            if ($request_language !== '' && in_array($request_language, self::get_languages(), true)) {
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
        if ($request_language !== '' && in_array($request_language, self::get_languages(), true)) {
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
            $api_key = get_field(ACF::FIELD_API_KEY, 'option');
        }
        if (empty($api_key)) {
            $api_key = get_option(ACF::FIELD_API_KEY);
        }
        return $api_key;
    }

    /**
     * Remove a trailing /prerender segment from a URL path.
     *
     * @param string $path
     * @return string
     */
    private static function remove_prerender_path_suffix($path) {
        if (!is_string($path) || $path === '') {
            return $path;
        }

        // Remove /prerender or /prerender/ from the end of the path
        $normalized_path = preg_replace('#/prerender/?$#', '', $path);
        
        if (!is_string($normalized_path)) {
            return $path;
        }

        // If the path became empty, make it a root path
        if ($normalized_path === '') {
            $normalized_path = '/';
        }

        return $normalized_path;
    }
    
    /**
     * Get language code with default country code.
     *
     * @param string $language The language code
     * @return string The language code with country code (e.g., 'en-US', 'es-ES')
     */
    public static function get_language_with_country_code($language) {
        $language = self::normalize_language_code($language);

        // Default country codes for common languages
        $language_country_map = [
            'en' => 'en-US',
            'es' => 'es-ES', 
            'fr' => 'fr-FR',
            'de' => 'de-DE',
            'it' => 'it-IT',
            'pt' => 'pt-PT',
            'ja' => 'ja-JP',
            'ko' => 'ko-KR', 
            'zh' => 'zh-CN',
            'ru' => 'ru-RU',
            'ar' => 'ar-SA',
            'nl' => 'nl-NL',
            'pl' => 'pl-PL',
            'sv' => 'sv-SE',
            'da' => 'da-DK',
            'no' => 'no-NO',
            'fi' => 'fi-FI',
            'cs' => 'cs-CZ',
            'sk' => 'sk-SK',
            'hu' => 'hu-HU',
            'ro' => 'ro-RO',
            'bg' => 'bg-BG',
            'hr' => 'hr-HR',
            'sl' => 'sl-SI',
            'et' => 'et-EE',
            'lv' => 'lv-LV',
            'lt' => 'lt-LT',
            'el' => 'el-GR',
            'tr' => 'tr-TR',
            'he' => 'he-IL',
            'th' => 'th-TH',
            'vi' => 'vi-VN',
            'id' => 'id-ID',
            'ms' => 'ms-MY',
            'hi' => 'hi-IN',
            'bn' => 'bn-BD',
            'ur' => 'ur-PK'
        ];
        
        // Return mapped language-country code or fallback to original if not found
        return isset($language_country_map[$language]) ? $language_country_map[$language] : $language;
    }

    /**
     * Normalize language codes to a consistent lowercase format.
     *
     * @param string $language
     * @return string
     */
    public static function normalize_language_code($language) {
        if (!is_string($language)) {
            return '';
        }

        return strtolower(trim($language));
    }

    /**
     * Read the current request language from the first path segment.
     *
     * @return string
     */
    public static function get_request_language() {
        if (empty($_SERVER['REQUEST_URI'])) {
            return self::get_source_language();
        }

        $request_path = wp_parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if (!is_string($request_path) || $request_path === '') {
            return self::get_source_language();
        }

        $parts = explode('/', $request_path);
        $request_language = isset($parts[1]) ? self::normalize_language_code($parts[1]) : '';

        if ($request_language !== '' && in_array($request_language, self::get_languages(), true)) {
            return $request_language;
        }

        return self::get_source_language();
    }
    
    /**
     * Get translated phrases from Localize API
     *
     * @param array $phrases Array of phrases to translate
     * @param string $target_language Target language code
     * @return array Associative array mapping original phrases to translations
     */
    public static function get_phrases($phrases, $target_language) {
        if (empty($phrases) || empty($target_language)) {
            return [];
        }
        
        $api = LocalizeAPI::create_from_options();
        if ($api === null) {
            return [];
        }
        
        return $api->get_phrases($phrases, $target_language);
    }
}
