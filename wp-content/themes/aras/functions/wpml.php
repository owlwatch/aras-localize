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
	return WPML_Helper::get_instance()->get_wp_query( $args, $languages );
}

add_filter('wpseo_canonical', 'Aras\\WPML\\wpml_custom_canonical_url');

function wpml_custom_canonical_url( $canonical ) {
    if ( function_exists('wpml_permalink_filter') ) {
        // Get the current post ID
        $post_id = get_queried_object_id();
        
        // Get the current language
        $current_language = apply_filters( 'wpml_current_language', NULL );

        // Get the permalink for the current post in the current language
        $canonical = apply_filters( 'wpml_permalink', get_permalink( $post_id ), $current_language );
    }
    
    return $canonical;
}