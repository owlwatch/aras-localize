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
				$query->set( 's', $search );

				// expand the search into the "brief_description"
				$query->set( 'meta_query', array(
					'relation' => 'OR',
					array(
						'key' => 'brief_description',
						'value' => $search,
						'compare' => 'LIKE'
					)
				));

				// can we search by a the name of a taxonomy term
				// // that this has?
				// $taxonomies = array( 'mp-solution-category', 'mp-solution-type', 'mp-contributor' );
				// $tax_query = array();
				// foreach( $taxonomies as $taxonomy ) {
				// 	$tax_query[] = array(
				// 		'taxonomy' => $taxonomy,
				// 		'field' => 'name',
				// 		'terms' => $search,
				// 		'operator' => 'IN'
				// 	);
				// }
				// $query->set( 'tax_query', $tax_query );
			}
		}
	}


}