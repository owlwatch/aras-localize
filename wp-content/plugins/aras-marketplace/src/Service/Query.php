<?php
namespace Aras\Marketplace\Service;

class Query
{
	/**
	 * The constructor
	 */
	public function __construct() {
		// register mp-search as a query var
		add_filter( 'query_vars', array( $this, 'addQueryVars' ) );
		add_action( 'pre_get_posts', array( $this, 'filterMarketplacePosts' ) );
	}

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