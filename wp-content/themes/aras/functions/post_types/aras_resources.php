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
		'name'                       => _x('Industries', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Industry', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Industries', 'text_domain'),
		'all_items'                  => __('Industries', 'text_domain'),
		'parent_item'                => __('Parent Industry', 'text_domain'),
		'parent_item_colon'          => __('Parent Industry:', 'text_domain'),
		'new_item_name'              => __('New Industry Name', 'text_domain'),
		'add_new_item'               => __('Add New Industry', 'text_domain'),
		'edit_item'                  => __('Edit Industry', 'text_domain'),
		'update_item'                => __('Update Industry', 'text_domain'),
		'view_item'                  => __('View Industry', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Industries', 'text_domain'),
		'search_items'               => __('Search Industries', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Industries', 'text_domain'),
		'items_list'                 => __('Industry list', 'text_domain'),
		'items_list_navigation'      => __('Industry list navigation', 'text_domain'),
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
		'name'                       => _x('Applications', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Application', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Applications', 'text_domain'),
		'all_items'                  => __('All Applications', 'text_domain'),
		'parent_item'                => __('Parent Application', 'text_domain'),
		'parent_item_colon'          => __('Parent Application:', 'text_domain'),
		'new_item_name'              => __('New Application Name', 'text_domain'),
		'add_new_item'               => __('Add New Application', 'text_domain'),
		'edit_item'                  => __('Edit Application', 'text_domain'),
		'update_item'                => __('Update Application', 'text_domain'),
		'view_item'                  => __('View Application', 'text_domain'),
		'separate_items_with_commas' => __('Separate applications with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove applications', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Applications', 'text_domain'),
		'search_items'               => __('Search Applications', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Applications', 'text_domain'),
		'items_list'                 => __('Application list', 'text_domain'),
		'items_list_navigation'      => __('Application list navigation', 'text_domain'),
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
		'name'                       => _x('Topics', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Topic', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Topics', 'text_domain'),
		'all_items'                  => __('All Topics', 'text_domain'),
		'parent_item'                => __('Parent Topic', 'text_domain'),
		'parent_item_colon'          => __('Parent Topic:', 'text_domain'),
		'new_item_name'              => __('New Topic Name', 'text_domain'),
		'add_new_item'               => __('Add New Topic', 'text_domain'),
		'edit_item'                  => __('Edit Topic', 'text_domain'),
		'update_item'                => __('Update Topic', 'text_domain'),
		'view_item'                  => __('View Topic', 'text_domain'),
		'separate_items_with_commas' => __('Separate topics with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove topics', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Topics', 'text_domain'),
		'search_items'               => __('Search Topics', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Topics', 'text_domain'),
		'items_list'                 => __('Topic list', 'text_domain'),
		'items_list_navigation'      => __('Topic list navigation', 'text_domain'),
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
//		'name'                       => _x('Locations', 'Taxonomy General Name', 'text_domain'),
//		'singular_name'              => _x('Location', 'Taxonomy Singular Name', 'text_domain'),
//		'menu_name'                  => __('Locations', 'text_domain'),
//		'all_items'                  => __('Locations', 'text_domain'),
//		'parent_item'                => __('Parent Location', 'text_domain'),
//		'parent_item_colon'          => __('Parent Location:', 'text_domain'),
//		'new_item_name'              => __('New Location', 'text_domain'),
//		'add_new_item'               => __('Add New Location', 'text_domain'),
//		'edit_item'                  => __('Edit Location', 'text_domain'),
//		'update_item'                => __('Update Location', 'text_domain'),
//		'view_item'                  => __('View Location', 'text_domain'),
//		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
//		'add_or_remove_items'        => __('Add or remove locations', 'text_domain'),
//		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
//		'popular_items'              => __('Popular Locations', 'text_domain'),
//		'search_items'               => __('Search Locations', 'text_domain'),
//		'not_found'                  => __('Not Found', 'text_domain'),
//		'no_terms'                   => __('No Locations', 'text_domain'),
//		'items_list'                 => __('Location list', 'text_domain'),
//		'items_list_navigation'      => __('Location list navigation', 'text_domain'),
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
		'name'                       => _x('Formats', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Format', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Format', 'text_domain'),
		'all_items'                  => __('All Formats', 'text_domain'),
		'parent_item'                => __('Parent Format', 'text_domain'),
		'parent_item_colon'          => __('Parent Format:', 'text_domain'),
		'new_item_name'              => __('New Format Name', 'text_domain'),
		'add_new_item'               => __('Add New Format', 'text_domain'),
		'edit_item'                  => __('Edit Format', 'text_domain'),
		'update_item'                => __('Update Format', 'text_domain'),
		'view_item'                  => __('View Format', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Formats', 'text_domain'),
		'search_items'               => __('Search Formats', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Formats', 'text_domain'),
		'items_list'                 => __('Format list', 'text_domain'),
		'items_list_navigation'      => __('Format list navigation', 'text_domain'),
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
		'name'                       => _x('Special Resources', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Special Resource', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Special Resources', 'text_domain'),
		'all_items'                  => __('Special Resources', 'text_domain'),
		'parent_item'                => __('Parent Special Resource', 'text_domain'),
		'parent_item_colon'          => __('Parent Special Resource:', 'text_domain'),
		'new_item_name'              => __('New Special Resource Name', 'text_domain'),
		'add_new_item'               => __('Add New Special Resource', 'text_domain'),
		'edit_item'                  => __('Edit Special Resource', 'text_domain'),
		'update_item'                => __('Update Special Resource', 'text_domain'),
		'view_item'                  => __('View Special Resource', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Special Resources', 'text_domain'),
		'search_items'               => __('Search Special Resources', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Special Resources', 'text_domain'),
		'items_list'                 => __('Special Resource list', 'text_domain'),
		'items_list_navigation'      => __('Special Resource list navigation', 'text_domain'),
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
