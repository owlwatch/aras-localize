<?php

/*** Post Type: Resources ***/
function cptui_register_my_cpts_resource()
{
	$labels = array(
		"name"                  => __('Resources', ''),
		"singular_name"         => __('Resource', ''),
		'menu_name'             => __('Resources', ''),
		'name_admin_bar'        => __('Resources', ''),
		'archives'              => __('Resource Archives', ''),
		'attributes'            => __('Resource Attributes', ''),
		'parent_item_colon'     => __('Resource Resource:', ''),
		'all_items'             => __('All Resources', ''),
		'add_new_item'          => __('Add New Resource', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Resource', ''),
		'edit_item'             => __('Edit Resource', ''),
		'update_item'           => __('Update Resource', ''),
		'view_item'             => __('View Resource', ''),
		'view_items'            => __('View Resources', ''),
		'search_items'          => __('Search Resources', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Resource', ''),
		'uploaded_to_this_item' => __('Uploaded to this Resource', ''),
		'items_list'            => __('Resource list', ''),
		'items_list_navigation' => __('Resource list navigation', ''),
		'filter_items_list'     => __('Filter Resource list', ''),
	);
	$args = array(
		'label'                 => __('Resource', ''),
		'description'           => __('Resource Post Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => 'resources',
		'menu_icon'             => 'dashicons-grid-view',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'posts_per_page'        => -1,
		'rewrite'            => array(
			'slug'       => 'resources/all',
			'with_front' => false,
		),
	);
	register_post_type('resource', $args);
}
add_action('init', 'cptui_register_my_cpts_resource', 0);


// Register Industry Taxonomy
function industry_taxonomy()
{
	$labels = array(
		'name'                       => _x('Industries', 'Taxonomy General Name', 'aras'),
		'singular_name'              => _x('Industry', 'Taxonomy Singular Name', 'aras'),
		'menu_name'                  => __('Industries', 'aras'),
		'all_items'                  => __('Industries', 'aras'),
		'parent_item'                => __('Parent Industry', 'aras'),
		'parent_item_colon'          => __('Parent Industry:', 'aras'),
		'new_item_name'              => __('New Industry Name', 'aras'),
		'add_new_item'               => __('Add New Industry', 'aras'),
		'edit_item'                  => __('Edit Industry', 'aras'),
		'update_item'                => __('Update Industry', 'aras'),
		'view_item'                  => __('View Industry', 'aras'),
		'separate_items_with_commas' => __('Separate items with commas', 'aras'),
		'add_or_remove_items'        => __('Add or remove items', 'aras'),
		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
		'popular_items'              => __('Popular Industries', 'aras'),
		'search_items'               => __('Search Industries', 'aras'),
		'not_found'                  => __('Not Found', 'aras'),
		'no_terms'                   => __('No Industries', 'aras'),
		'items_list'                 => __('Industry list', 'aras'),
		'items_list_navigation'      => __('Industry list navigation', 'aras'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy('industry', array('resource'), $args);
}
add_action('init', 'industry_taxonomy', 1);

// Register Application Taxonomy
function application_taxonomy()
{
	$labels = array(
		'name'                       => _x('Applications', 'Taxonomy General Name', 'aras'),
		'singular_name'              => _x('Application', 'Taxonomy Singular Name', 'aras'),
		'menu_name'                  => __('Applications', 'aras'),
		'all_items'                  => __('All Applications', 'aras'),
		'parent_item'                => __('Parent Application', 'aras'),
		'parent_item_colon'          => __('Parent Application:', 'aras'),
		'new_item_name'              => __('New Application Name', 'aras'),
		'add_new_item'               => __('Add New Application', 'aras'),
		'edit_item'                  => __('Edit Application', 'aras'),
		'update_item'                => __('Update Application', 'aras'),
		'view_item'                  => __('View Application', 'aras'),
		'separate_items_with_commas' => __('Separate applications with commas', 'aras'),
		'add_or_remove_items'        => __('Add or remove applications', 'aras'),
		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
		'popular_items'              => __('Popular Applications', 'aras'),
		'search_items'               => __('Search Applications', 'aras'),
		'not_found'                  => __('Not Found', 'aras'),
		'no_terms'                   => __('No Applications', 'aras'),
		'items_list'                 => __('Application list', 'aras'),
		'items_list_navigation'      => __('Application list navigation', 'aras'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy('application', array('resource'), $args);
}
add_action('init', 'application_taxonomy', 1);

// Register Topic Taxonomy
function topic_taxonomy()
{
	$labels = array(
		'name'                       => _x('Topics', 'Taxonomy General Name', 'aras'),
		'singular_name'              => _x('Topic', 'Taxonomy Singular Name', 'aras'),
		'menu_name'                  => __('Topics', 'aras'),
		'all_items'                  => __('All Topics', 'aras'),
		'parent_item'                => __('Parent Topic', 'aras'),
		'parent_item_colon'          => __('Parent Topic:', 'aras'),
		'new_item_name'              => __('New Topic Name', 'aras'),
		'add_new_item'               => __('Add New Topic', 'aras'),
		'edit_item'                  => __('Edit Topic', 'aras'),
		'update_item'                => __('Update Topic', 'aras'),
		'view_item'                  => __('View Topic', 'aras'),
		'separate_items_with_commas' => __('Separate topics with commas', 'aras'),
		'add_or_remove_items'        => __('Add or remove topics', 'aras'),
		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
		'popular_items'              => __('Popular Topics', 'aras'),
		'search_items'               => __('Search Topics', 'aras'),
		'not_found'                  => __('Not Found', 'aras'),
		'no_terms'                   => __('No Topics', 'aras'),
		'items_list'                 => __('Topic list', 'aras'),
		'items_list_navigation'      => __('Topic list navigation', 'aras'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy('topic', array('resource'), $args);
}
add_action('init', 'topic_taxonomy', 1);

// Register Location Taxonomy
//function location_taxonomy()
//{
//	$labels = array(
//		'name'                       => _x('Locations', 'Taxonomy General Name', 'aras'),
//		'singular_name'              => _x('Location', 'Taxonomy Singular Name', 'aras'),
//		'menu_name'                  => __('Locations', 'aras'),
//		'all_items'                  => __('Locations', 'aras'),
//		'parent_item'                => __('Parent Location', 'aras'),
//		'parent_item_colon'          => __('Parent Location:', 'aras'),
//		'new_item_name'              => __('New Location', 'aras'),
//		'add_new_item'               => __('Add New Location', 'aras'),
//		'edit_item'                  => __('Edit Location', 'aras'),
//		'update_item'                => __('Update Location', 'aras'),
//		'view_item'                  => __('View Location', 'aras'),
//		'separate_items_with_commas' => __('Separate items with commas', 'aras'),
//		'add_or_remove_items'        => __('Add or remove locations', 'aras'),
//		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
//		'popular_items'              => __('Popular Locations', 'aras'),
//		'search_items'               => __('Search Locations', 'aras'),
//		'not_found'                  => __('Not Found', 'aras'),
//		'no_terms'                   => __('No Locations', 'aras'),
//		'items_list'                 => __('Location list', 'aras'),
//		'items_list_navigation'      => __('Location list navigation', 'aras'),
//	);
//	$args = array(
//		'labels'                     => $labels,
//		'hierarchical'               => true,
//		'public'                     => true,
//		'show_ui'                    => true,
//		'show_admin_column'          => true,
//		'show_in_nav_menus'          => true,
//		'query_var'                  => true,
//		'show_tagcloud'              => false,
//	);
//	register_taxonomy('location', array('resource'), $args);
//}
//add_action('init', 'location_taxonomy', 1);
//
// Register Format Taxonomy
function format_taxonomy()
{
	$labels = array(
		'name'                       => _x('Formats', 'Taxonomy General Name', 'aras'),
		'singular_name'              => _x('Format', 'Taxonomy Singular Name', 'aras'),
		'menu_name'                  => __('Format', 'aras'),
		'all_items'                  => __('All Formats', 'aras'),
		'parent_item'                => __('Parent Format', 'aras'),
		'parent_item_colon'          => __('Parent Format:', 'aras'),
		'new_item_name'              => __('New Format Name', 'aras'),
		'add_new_item'               => __('Add New Format', 'aras'),
		'edit_item'                  => __('Edit Format', 'aras'),
		'update_item'                => __('Update Format', 'aras'),
		'view_item'                  => __('View Format', 'aras'),
		'separate_items_with_commas' => __('Separate items with commas', 'aras'),
		'add_or_remove_items'        => __('Add or remove items', 'aras'),
		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
		'popular_items'              => __('Popular Formats', 'aras'),
		'search_items'               => __('Search Formats', 'aras'),
		'not_found'                  => __('Not Found', 'aras'),
		'no_terms'                   => __('No Formats', 'aras'),
		'items_list'                 => __('Format list', 'aras'),
		'items_list_navigation'      => __('Format list navigation', 'aras'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy('format', array('resource'), $args);
}
add_action('init', 'format_taxonomy', 1);


// Register Featured Resource Taxonomy
function featured_resource_taxonomy()
{
	$labels = array(
		'name'                       => _x('Special Resources', 'Taxonomy General Name', 'aras'),
		'singular_name'              => _x('Special Resource', 'Taxonomy Singular Name', 'aras'),
		'menu_name'                  => __('Special Resources', 'aras'),
		'all_items'                  => __('Special Resources', 'aras'),
		'parent_item'                => __('Parent Special Resource', 'aras'),
		'parent_item_colon'          => __('Parent Special Resource:', 'aras'),
		'new_item_name'              => __('New Special Resource Name', 'aras'),
		'add_new_item'               => __('Add New Special Resource', 'aras'),
		'edit_item'                  => __('Edit Special Resource', 'aras'),
		'update_item'                => __('Update Special Resource', 'aras'),
		'view_item'                  => __('View Special Resource', 'aras'),
		'separate_items_with_commas' => __('Separate items with commas', 'aras'),
		'add_or_remove_items'        => __('Add or remove items', 'aras'),
		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
		'popular_items'              => __('Popular Special Resources', 'aras'),
		'search_items'               => __('Search Special Resources', 'aras'),
		'not_found'                  => __('Not Found', 'aras'),
		'no_terms'                   => __('No Special Resources', 'aras'),
		'items_list'                 => __('Special Resource list', 'aras'),
		'items_list_navigation'      => __('Special Resource list navigation', 'aras'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
		//'rewrite'            => array('slug' => 'featured', 'with_front' => false),
	);
	register_taxonomy('featured-resource', array('resource'), $args);
}
add_action('init', 'featured_resource_taxonomy', 1);

function exclude_category($query)
{
	if (!is_admin() && is_post_type_archive('resource') && $query->is_main_query()) {
		$tax_query = array(
			array(
				'taxonomy' => 'featured-resource',
				'field'    => 'id',
				'terms'    => array(1672), // Exclude posts with term ID 1672
				'operator' => 'NOT IN', // Exclude rather than include
			),
		);
		$query->set('tax_query', $tax_query);
	}
}
add_action('pre_get_posts', 'exclude_category');

function ensure_taxonomy_archive_takes_precedence_over_post_type_archive( $query ){
	if( $query->is_post_type_archive('resource') && is_tax() ){
		global $wp_query;
		$wp_query->is_post_type_archive = false;
	}
}

add_action('pre_get_posts', 'ensure_taxonomy_archive_takes_precedence_over_post_type_archive');

// lets add a meta box on the edit page for resources
// that shows the places the resource is selected in the
// automatic cards ACF flexible content field
function add_resource_selections_metabox()
{
	add_meta_box(
		'resource_selections',
		__('Automatic Cards', 'aras'),
		'resource_selections_metabox',
		'resource',
		'side',
		'default'
	);
}

add_action('add_meta_boxes', 'add_resource_selections_metabox');

function resource_selections_metabox($_post)
{
	// search for the id of this post as the meta_value
	// for keys that match the regex /.*cards_\d+_content_item$/

	global $wpdb;
	$sql = $wpdb->prepare('SELECT post_id FROM wp_postmeta WHERE meta_key LIKE %s AND meta_value = %d', '%_cards_%_content_item', $_post->ID);
	$results = $wpdb->get_results($sql);

	if (empty($results)) {
		echo '<p>Not found in any Automatic Cards</p>';
		return;
	}

	$query = new WP_Query([
		'post_type' => 'any',
		'post__in' => array_map(function($result){
			return $result->post_id;
		}, $results),
		'posts_per_page' => -1,
	]);

	if (!empty($query->posts)) {
		echo '<p>Found in the following posts:</p>';
		echo '<ul>';
		foreach( $query->posts as $p){
			echo '<li><a href="' . get_edit_post_link($p->ID) . '">' . get_the_title($p->ID) . '</a></li>';
		}
		echo '</ul>';
	}
	else {
		echo '<p>Not found in any Automatic Cards</p>';
	}
}