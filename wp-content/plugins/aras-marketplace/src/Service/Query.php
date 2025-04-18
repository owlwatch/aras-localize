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
			}
		}
	}


}