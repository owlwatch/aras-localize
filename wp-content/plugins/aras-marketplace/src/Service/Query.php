<?php
namespace Aras\Marketplace\Service;

class Query
{
	/**
	 * Track the main marketplace query while WPML filters are temporarily replaced.
	 *
	 * @var int|null
	 */
	protected $wpml_query_id = null;

	/**
	 * Active language codes used for the temporary all-language query.
	 *
	 * @var array
	 */
	protected $wpml_language_codes = [];

	/**
	 * The constructor
	 */
	public function __construct() {
		// register mp-search as a query var
		add_filter( 'query_vars', array( $this, 'addQueryVars' ) );
		add_action( 'parse_query', array( $this, 'prepareMarketplaceSearchQuery' ), 8 );
		add_action( 'pre_get_posts', array( $this, 'filterMarketplacePosts' ) );
	}

	// @todo - automatically enable the extended search with the wpessid query var

	/**
	 * Add the query vars
	 */
	public function addQueryVars( $vars ) {
		$vars[] = 'mp-search';
		return $vars;
	}

	/**
	 * Filter the marketplace posts
	 */
	public function filterMarketplacePosts( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( is_post_type_archive( 'mp-solution' ) || 
			 is_tax( 'mp-solution-category' ) || 
			 is_tax( 'mp-solution-type' ) ||
			 is_tax( 'mp-contributor' )
		) {
			
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'solution_title' );
			$query->set( 'order', 'ASC' );
			// maybe filter by search
			if ( $search = get_query_var( 'mp-search' ) ) {
				// add query_var to enable acf search
				$query->query_vars['s'] = $search;
				$query->set( 's', $search );
				// also need to make sure the conditional is_search is true
				$query->is_search = true;
			}
		}
	}

	/**
	 * Prepare marketplace search to run across all WPML languages before WPML filters the main query.
	 */
	public function prepareMarketplaceSearchQuery( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		if ( ! $this->isMarketplaceQueryVars( $query ) || ! $query->get( 'mp-search' ) ) {
			return;
		}

		$this->ensureMarketplaceSearchSettingsId( $query );

		$languages = apply_filters( 'wpml_active_languages', null );
		if ( ! is_array( $languages ) || empty( $languages ) ) {
			return;
		}

		$this->wpml_query_id = spl_object_id( $query );
		$this->wpml_language_codes = array_keys( $languages );

		$this->removeWpmlQueryFilters();
		add_filter( 'posts_clauses', array( $this, 'filterWpmlClauses' ), 10, 2 );
		add_filter( 'posts_results', array( $this, 'restoreWpmlQueryFilters' ), 10, 2 );
	}

	/**
	 * Apply the same WPML translation-table logic used by the theme helper.
	 */
	public function filterWpmlClauses( $clauses, $query ) {
		global $wpdb;

		if ( $this->wpml_query_id !== spl_object_id( $query ) || empty( $this->wpml_language_codes ) ) {
			return $clauses;
		}

		$language_conditions = array_map(
			function( $language_code ) use ( $wpdb ) {
				return $wpdb->prepare( 'icl_t.language_code = %s', $language_code );
			},
			$this->wpml_language_codes
		);

		$clauses['join'] .= "
			LEFT JOIN {$wpdb->prefix}icl_translations icl_t ON {$wpdb->posts}.ID = icl_t.element_id
			AND icl_t.element_type = CONCAT('post_', {$wpdb->posts}.post_type)";

		$clauses['where'] .= ' AND ( ' . implode( ' OR ', $language_conditions ) . ' )';
		$clauses['groupby'] = 'icl_t.trid';

		return $clauses;
	}

	/**
	 * Restore WPML query filters after the main marketplace search query has run.
	 */
	public function restoreWpmlQueryFilters( $posts, $query ) {
		if ( $this->wpml_query_id !== spl_object_id( $query ) ) {
			return $posts;
		}

		remove_filter( 'posts_clauses', array( $this, 'filterWpmlClauses' ), 10 );
		remove_filter( 'posts_results', array( $this, 'restoreWpmlQueryFilters' ), 10 );
		$this->addWpmlQueryFilters();
		$this->wpml_query_id = null;
		$this->wpml_language_codes = [];

		return $posts;
	}

	/**
	 * Whether query vars indicate a marketplace archive/taxonomy request.
	 */
	protected function isMarketplaceQueryVars( $query ) {
		$post_type = $query->get( 'post_type' );
		$taxonomy  = $query->get( 'taxonomy' );

		return $post_type === 'mp-solution' ||
			$taxonomy === 'mp-solution-category' ||
			$taxonomy === 'mp-solution-type' ||
			$taxonomy === 'mp-contributor';
	}

	/**
	 * Remove WPML filters for the current main query so the custom clauses can take over.
	 */
	protected function removeWpmlQueryFilters() {
		global $sitepress, $wpml_query_filter;

		if ( ! isset( $sitepress, $wpml_query_filter ) ) {
			return;
		}

		remove_filter( 'parse_query', array( $sitepress, 'parse_query' ), 10 );
		remove_filter( 'pre_get_posts', array( $sitepress, 'pre_get_posts' ), 10 );
		remove_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10 );
		remove_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10 );

		error_log('WPML filters removed for marketplace search query.' );
	}

	/**
	 * Re-attach WPML filters after the marketplace main query completes.
	 */
	protected function addWpmlQueryFilters() {
		global $sitepress, $wpml_query_filter;

		if ( ! isset( $sitepress, $wpml_query_filter ) ) {
			return;
		}

		add_filter( 'parse_query', array( $sitepress, 'parse_query' ), 10 );
		add_filter( 'pre_get_posts', array( $sitepress, 'pre_get_posts' ), 10 );
		add_filter( 'posts_join', array( $wpml_query_filter, 'posts_join_filter' ), 10, 2 );
		add_filter( 'posts_where', array( $wpml_query_filter, 'posts_where_filter' ), 10, 2 );
	}

	/**
	 * Ensure the WP Extended Search settings ID is present even if the request URL omitted it.
	 */
	protected function ensureMarketplaceSearchSettingsId( $query ) {
		$settings_id = get_query_var( 'wpessid' );

		if ( ! $settings_id ) {
			$settings_id = isset( $_GET['wpessid'] ) ? sanitize_text_field( wp_unslash( $_GET['wpessid'] ) ) : '';
		}

		if ( ! $settings_id ) {
			$settings_id = get_field( 'marketplace_essid', 'option' );
		}

		if ( ! $settings_id ) {
			return;
		}

		$query->set( 'wpessid', $settings_id );
		$query->query_vars['wpessid'] = $settings_id;
		$_GET['wpessid'] = $settings_id;
		$_REQUEST['wpessid'] = $settings_id;
	}

	/**
	 * Join to include term relationships
	 */
	public function postsJoin( $join ) {
		global $wpdb;
		if( !get_query_var('mp-search') ) {
			return $join;
		}
		$join .= " LEFT JOIN {$wpdb->term_relationships} AS tr ON {$wpdb->posts}.ID = tr.object_id
				   LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				   LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id ";
		return $join;
	}

	/**
	 * Where clause to filter by search terms
	 */
	public function postsWhere( $where ) {
		global $wpdb;

		if( !get_query_var('mp-search') ) {
			return $where;
		}
		if ( $search = get_query_var( 'mp-search' ) ) {
			$where .= $wpdb->prepare( " OR ( t.name LIKE %s ) ", '%' . $wpdb->esc_like( $search ) . '%' );
		}

		return $where;
	}

}
