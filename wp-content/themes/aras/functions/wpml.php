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

function adjust_canonical_for_non_translated_pages($canonical) {
	if (function_exists('icl_object_id') && function_exists('wpml_get_language_information')) {
        global $post;
        
        // Get the current language
        $language_info = wpml_get_language_information(null);
        $current_language = $language_info['language_code'];

        // Get the translated post ID in the current language
        $translated_post_id = apply_filters('wpml_object_id', $post->ID, $post->post_type, false, $current_language);

        // If the post does not exist in the current language, modify the canonical URL to retain the language code
        if (!$translated_post_id) {
            // Ensure the canonical URL keeps the language code
            $canonical = add_query_arg('lang', $current_language, get_permalink($post->ID));
        }
    }

    return $canonical;
}

add_filter('wpseo_canonical', 'Aras\\WPML\\adjust_canonical_for_non_translated_pages');