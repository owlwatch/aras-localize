<?php
namespace Aras;

/**
 * Write a walker that will use the meta sitemap_link_text
 * for the link text if it exists, otherwise it uses post title
 */
class Sitemap_Walker extends \Walker_Page {
	
	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 2.1.0
	 *
	 * @param string $output   Used to append additional content (passed by reference).
	 * @param WP_Post $page     Page data object. Not used.
	 * @param int $depth        Depth of page. Not Used.
	 * @param array $args       Arguments.
	 * @param int $current_page Page ID. Not Used.
	 */
	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {
		$link_text = get_post_meta( $page->ID, 'sitemap_link_text', true );
		if ( ! empty( $link_text ) ) {
			$page->post_title = $link_text;
		}
		else if( ($link_text = get_field('hero_headline', $page->ID)) ) {
			$page->post_title = $link_text;
		}
		parent::start_el( $output, $page, $depth, $args, $current_page );
	}
}


/**
 * Functions for modifying the HTML sitemap
 * which is generated from the `wp_list_pages` function.
 */
class Sitemap {

	// singleton
	private static $instance = null;

	public static function instance() {
		if( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_filter('wp_list_pages_excludes', [$this, 'exclude_from_sitemap'], 10, 1);
	}

	 /**
	 * Function that filters the exclusions for the wp_list_pages
	 * by finding all post ids that have a meta key of `exclude_from_sitemap` = 1
	 * 
	 * @param array $exclude
	 * @param array $args
	 * 
	 * @return array
	 */
	public function exclude_from_sitemap($exclude) {
		global $wpdb;
		$excluded_post_ids = $wpdb->get_col($wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",
			'exclude_from_html_sitemap',
			1
		));

		// also exclude any posts that are set to "noindex" through Yoast
		$excluded_post_ids = array_merge($excluded_post_ids, $wpdb->get_col($wpdb->prepare(
			"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",
			'_yoast_wpseo_meta-robots-noindex',
			1
		)));
		
		return array_merge($exclude, $excluded_post_ids);
	}


}

Sitemap::instance();