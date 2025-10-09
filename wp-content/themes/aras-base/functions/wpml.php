<?php
namespace Aras\WPML;

use WP_Query;

class WPML_Helper
{
	private static $instance = null;
	private function __constructor(){}
	public static function get_instance()
	{
		if( !isset( static::$instance ) ){
			static::$instance = new self();
		}
		return static::$instance;
	}


	protected array $languages = [];

	public function get_wp_query( $args, $languages=[] )
	{
		$this->languages = $languages;

		// add the filters
		add_filter('posts_clauses', [$this, 'filter_clauses'], 10, 2);

		$args['suppress_filters'] = false;
		$query = new WP_Query( $args );
		
		// remove the filters
		remove_filter('posts_clauses', [$this, 'filter_clauses'], 10);

		return $query;
	}

	public function filter_clauses( array $clauses, WP_Query $query )
	{
		global $wpdb;

		// Join the WPML translations table
		$clauses['join'] .= " 
			LEFT JOIN {$wpdb->prefix}icl_translations icl_t ON {$wpdb->posts}.ID = icl_t.element_id 
			AND icl_t.element_type = CONCAT('post_', {$wpdb->posts}.post_type)";

		$where = implode( ' OR ', array_map( function($lang_code){
			return "icl_t.language_code = '{$lang_code}'";
		}, $this->languages ));

		// Prioritize French translations if available
		$clauses['where'] .= " AND ( $where )";

		// Group by original post ID to avoid duplicates
		$clauses['groupby'] = "icl_t.trid";

		// Order by French first, then default language
		$clauses['orderby'] = "{$wpdb->posts}.post_date DESC, icl_t.language_code = '{$this->languages[0]}' DESC";

		return $clauses;
	}
}

function get_wp_query( array $args, array $languages )
{
	global $sitepress, $wpml_query_filter;
	if( isset( $sitepress) ){
		remove_filter('parse_query', array($sitepress, 'parse_query'), 10);
		remove_filter('pre_get_posts', array($sitepress, 'pre_get_posts'), 10);
		remove_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10 );
		remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10 );
		$query = WPML_Helper::get_instance()->get_wp_query( $args, $languages );
		add_filter('parse_query', array($sitepress, 'parse_query'), 10);
		add_filter('pre_get_posts', array($sitepress, 'pre_get_posts'), 10);
		add_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10, 2 );
		add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
	}
	else {
		$query = new WP_Query( $args );
	}
	return $query;
}

add_filter('wpseo_canonical', 'Aras\\WPML\\wpml_custom_canonical_url');

function wpml_custom_canonical_url( $canonical ) {
    if ( is_singular() && function_exists('wpml_permalink_filter') ) {
        // Get the current post ID
        $post_id = get_queried_object_id();
        
        // Get the current language
        $current_language = apply_filters( 'wpml_current_language', NULL );

        // Get the permalink for the current post in the current language
        $canonical = apply_filters( 'wpml_permalink', get_permalink( $post_id ), $current_language );
    }

	if( is_tax(['format', 'application', 'industry', 'topic']) ){
		$link = get_post_type_archive_link('resource');
		// are we on a paged url?
		$paged = get_query_var('paged');
		if( $paged ){
			$link.= "/page/$paged";
		}
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		$canonical = add_query_arg($tax->query_var, get_queried_object()->slug, $link );
	}
    
    return $canonical;
}

// add a hidden input for the "lang" to user edit forms
add_action('show_user_profile', 'Aras\\WPML\\add_lang_to_user_profile');
add_action('edit_user_profile', 'Aras\\WPML\\add_lang_to_user_profile');

function add_lang_to_user_profile( $user )
{
	if (function_exists('icl_object_id')) {
        $current_language = apply_filters('wpml_current_language', null);
        echo '<input type="hidden" name="lang" value="' . esc_attr($current_language) . '">';
    }
}

// we need to redirect to english for the admin page=wpseo_page
// add_action('admin_init', 'Aras\\WPML\\redirect_wpseo_page');

// function redirect_wpseo_page()
// {
// 	if (is_admin() && isset($_GET['page']) && $_GET['page'] === 'wpseo_page_settings') {
// 		// check current language
// 		$current_language = apply_filters('wpml_current_language', null);
// 		if ($current_language !== 'en') {
// 			// redirect to english version of the page
// 			$url = add_query_arg('lang', 'en', $_SERVER['REQUEST_URI']);
// 			$url = add_query_arg('warn_about_wpseo_settings_languages', '1', $url);
// 			wp_redirect($url);
// 			exit;
// 		}
// 	}
// }