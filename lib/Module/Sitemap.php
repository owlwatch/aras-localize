<?php
namespace Aras\Localize\Module;

use Aras\Localize\Util\Common;

class Sitemap {
    const ALTERNATES_KEY = 'aras_localize_alternates';
    const FIELD_ENABLE_SITEMAP = 'enable_sitemap';
    const FIELD_SITEMAP_URL_FORMAT = 'sitemap_url_format';
    const URL_FORMAT_ALTERNATES = 'alternates';
    const URL_FORMAT_SEPARATE = 'separate';

    public function register() {
        add_action( 'init', [ $this, 'init_sitemap_hooks' ], 99 );
        add_action( 'init', [ $this, 'check_yoast_compatibility' ] );
    }

    /**
     * Get the sitemap settings fields.
     *
     * @return array
     */
    public function get_acf_fields() {
        return [
            [
                'key' => 'field_aras_localize_sitemap_tab',
                'label' => 'Sitemap',
                'type' => 'tab',
                'placement' => 'top',
            ],
            [
                'key' => 'field_aras_localize_enable_sitemap',
                'label' => 'Enable sitemap localization',
                'name' => self::FIELD_ENABLE_SITEMAP,
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 1,
            ],
            [
                'key' => 'field_aras_localize_sitemap_url_format',
                'label' => 'Localized URL format',
                'name' => self::FIELD_SITEMAP_URL_FORMAT,
                'type' => 'radio',
                'choices' => [
                    self::URL_FORMAT_ALTERNATES => 'Alternate links',
                    self::URL_FORMAT_SEPARATE => 'Separate sitemap entries',
                ],
                'default_value' => self::URL_FORMAT_ALTERNATES,
                'layout' => 'vertical',
                'conditional_logic' => [
                    [
                        [
                            'field' => 'field_aras_localize_enable_sitemap',
                            'operator' => '==',
                            'value' => '1',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Initialize sitemap hooks after Yoast has loaded.
     *
     * @return void
     */
    public function init_sitemap_hooks() {
        if ( ! $this->is_enabled() || ! $this->is_yoast_active() ) {
            return;
        }

        if ( self::URL_FORMAT_ALTERNATES === $this->get_url_format() ) {
            add_filter( 'wpseo_sitemap_urlset', [ $this, 'add_xhtml_namespace' ] );
        }

        add_filter( 'wpseo_sitemap_entry', [ $this, 'add_language_variants_to_entry' ], 10, 3 );
        add_filter( 'wpseo_sitemap_url', [ $this, 'add_language_variants_to_url' ], 10, 2 );
    }

    /**
     * Check if Yoast SEO is active and compatible.
     *
     * @return void
     */
    public function check_yoast_compatibility() {
        if ( $this->is_enabled() && ! $this->is_yoast_active() ) {
            add_action( 'admin_notices', [ $this, 'yoast_missing_notice' ] );
        }
    }

    /**
     * Show admin notice if Yoast SEO is not active.
     *
     * @return void
     */
    public function yoast_missing_notice() {
        echo '<div class="notice notice-warning"><p><strong>Aras Localize Sitemap:</strong> Yoast SEO plugin is required for sitemap localization features.</p></div>';
    }

    /**
     * Add the xhtml namespace needed for hreflang alternate links.
     *
     * @param string $urlset The opening urlset tag.
     * @return string
     */
    public function add_xhtml_namespace( $urlset ) {
        if ( strpos( $urlset, 'xmlns:xhtml=' ) !== false ) {
            return $urlset;
        }

        return str_replace(
            'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
            'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml"',
            $urlset
        );
    }

    /**
     * Attach alternate language URLs to the Yoast sitemap entry data.
     *
     * @param array  $url  The sitemap URL data.
     * @param string $type The Yoast sitemap entry type.
     * @param mixed  $obj  The related object for this sitemap entry.
     * @return array
     */
    public function add_language_variants_to_entry( $url, $type, $obj ) {
        if ( ! is_array( $url ) || empty( $url['loc'] ) ) {
            return $url;
        }

        $languages = Common::get_languages();
        if ( empty( $languages ) ) {
            return $url;
        }

        $all_languages = Common::get_all_languages();
        $alternates = [];

        foreach ( $all_languages as $lang_code ) {
            $localized_url = $this->get_localized_url( $url['loc'], $lang_code );

            if ( empty( $localized_url ) ) {
                continue;
            }

            $alternates[ $lang_code ] = $localized_url;
        }

        if ( ! empty( $alternates ) ) {
            $source_language = Common::get_source_language();
            $default_url = $this->get_localized_url( $url['loc'], $source_language );

            if ( ! empty( $default_url ) ) {
                $alternates['x-default'] = $default_url;
            }

            $url[ self::ALTERNATES_KEY ] = $alternates;
        }

        return $url;
    }

    /**
     * Add alternate language URLs to each sitemap URL node.
     *
     * @param string $output The rendered <url> XML.
     * @param array  $url    The sitemap URL data.
     * @return string
     */
    public function add_language_variants_to_url( $output, $url ) {
        if ( ! is_array( $url ) || empty( $url[ self::ALTERNATES_KEY ] ) || ! is_array( $url[ self::ALTERNATES_KEY ] ) ) {
            return $output;
        }

        if ( self::URL_FORMAT_SEPARATE === $this->get_url_format() ) {
            return $this->add_separate_language_entries( $output, $url[ self::ALTERNATES_KEY ] );
        }

        $alternate_links = [];

        foreach ( $url[ self::ALTERNATES_KEY ] as $lang_code => $localized_url ) {
            if ( empty( $localized_url ) || ! is_string( $localized_url ) ) {
                continue;
            }

            $alternate_links[] = sprintf(
                "\t\t<xhtml:link rel=\"alternate\" hreflang=\"%s\" href=\"%s\" />\n",
                esc_attr( $lang_code ),
                esc_url( $localized_url )
            );
        }

        if ( empty( $alternate_links ) ) {
            return $output;
        }

        return preg_replace( '/(\s*<\/url>\s*)$/', implode( '', $alternate_links ) . '$1', $output, 1 ) ?: $output;
    }

    /**
     * Append each translated URL as its own sitemap entry.
     *
     * @param string               $output     The rendered source-language <url> XML.
     * @param array<string,string> $alternates Localized URLs keyed by language code.
     * @return string
     */
    private function add_separate_language_entries( $output, $alternates ) {
        $entries = [];
        $source_language = Common::get_source_language();

        foreach ( $alternates as $lang_code => $localized_url ) {
            if ( 'x-default' === $lang_code || $source_language === $lang_code || empty( $localized_url ) || ! is_string( $localized_url ) ) {
                continue;
            }

            $entry = preg_replace_callback(
                '/(<loc>).*?(<\/loc>)/s',
                function ( $matches ) use ( $localized_url ) {
                    return $matches[1] . esc_url( $localized_url ) . $matches[2];
                },
                $output,
                1
            );

            if ( is_string( $entry ) && $entry !== $output ) {
                $entries[] = $entry;
            }
        }

        return $output . implode( '', $entries );
    }

    /**
     * Get localized URL for a given language code.
     *
     * @param string $url       The sitemap URL.
     * @param string $lang_code The language code.
     * @return string
     */
    private function get_localized_url( $url, $lang_code ) {
        $source_language = Common::get_source_language();
        $parsed_url = wp_parse_url( $url );

        if ( ! is_array( $parsed_url ) || empty( $parsed_url['host'] ) ) {
            return $url;
        }

        $scheme = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] : 'https';
        $host = $parsed_url['host'];
        $path = isset( $parsed_url['path'] ) ? $parsed_url['path'] : '/';
        $query = isset( $parsed_url['query'] ) ? '?' . $parsed_url['query'] : '';
        $fragment = isset( $parsed_url['fragment'] ) ? '#' . $parsed_url['fragment'] : '';
        $port = isset( $parsed_url['port'] ) ? ':' . $parsed_url['port'] : '';

        if ( $lang_code === $source_language ) {
            $path = $this->remove_language_from_path( $path );
        } else {
            $path = $this->add_language_to_path( $path, $lang_code );
        }

        return $scheme . '://' . $host . $port . $path . $query . $fragment;
    }

    /**
     * Remove language code from URL path.
     *
     * @param string $path The URL path.
     * @return string
     */
    private function remove_language_from_path( $path ) {
        $languages = Common::get_languages();
        $parts = explode( '/', trim( $path, '/' ) );

        if ( ! empty( $parts[0] ) && in_array( $parts[0], $languages, true ) ) {
            array_shift( $parts );
        }

        $clean_path = '/' . implode( '/', array_filter( $parts, 'strlen' ) );

        return $clean_path === '' ? '/' : $clean_path;
    }

    /**
     * Add language code to URL path.
     *
     * @param string $path      The URL path.
     * @param string $lang_code The language code to add.
     * @return string
     */
    private function add_language_to_path( $path, $lang_code ) {
        $languages = Common::get_languages();
        $parts = explode( '/', trim( $path, '/' ) );

        if ( ! empty( $parts[0] ) && in_array( $parts[0], $languages, true ) ) {
            array_shift( $parts );
        }

        array_unshift( $parts, $lang_code );

        return '/' . implode( '/', array_filter( $parts, 'strlen' ) );
    }

    /**
     * Check if Yoast SEO is active.
     *
     * @return bool
     */
    private function is_yoast_active() {
        return defined( 'WPSEO_VERSION' ) && class_exists( 'WPSEO_Sitemaps' );
    }

    /**
     * Check whether sitemap localization is enabled.
     *
     * @return bool
     */
    private function is_enabled() {
        if ( ! function_exists( 'get_field' ) ) {
            return true;
        }

        // Preserve the previously unconditional behavior until this new option
        // has explicitly been saved on an existing installation.
        if ( null === get_option( 'options_' . self::FIELD_ENABLE_SITEMAP, null ) ) {
            return true;
        }

        $enabled = get_field( self::FIELD_ENABLE_SITEMAP, 'option' );

        return (bool) $enabled;
    }

    /**
     * Get the configured localized URL output format.
     *
     * @return string
     */
    private function get_url_format() {
        if ( ! function_exists( 'get_field' ) ) {
            return self::URL_FORMAT_ALTERNATES;
        }

        $format = get_field( self::FIELD_SITEMAP_URL_FORMAT, 'option' );

        return self::URL_FORMAT_SEPARATE === $format
            ? self::URL_FORMAT_SEPARATE
            : self::URL_FORMAT_ALTERNATES;
    }
}
