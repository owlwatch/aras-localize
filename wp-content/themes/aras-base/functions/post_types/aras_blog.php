<?php
// Register Featured Blog Taxonomy
function featured_blog_taxonomy()
{
	$labels = array(
		'name'                       => _x('Featured Blogs', 'Taxonomy General Name', 'aras'),
		'singular_name'              => _x('Featured Blog', 'Taxonomy Singular Name', 'aras'),
		'menu_name'                  => __('Featured', 'aras'),
		'all_items'                  => __('Featured Blogs', 'aras'),
		'parent_item'                => __('Parent Featured Blog', 'aras'),
		'parent_item_colon'          => __('Parent Featured Blog:', 'aras'),
		'new_item_name'              => __('New Featured Blog Name', 'aras'),
		'add_new_item'               => __('Add New Featured Blog', 'aras'),
		'edit_item'                  => __('Edit Featured Blog', 'aras'),
		'update_item'                => __('Update Featured Blog', 'aras'),
		'view_item'                  => __('View Featured Blog', 'aras'),
		'separate_items_with_commas' => __('Separate items with commas', 'aras'),
		'add_or_remove_items'        => __('Add or remove items', 'aras'),
		'choose_from_most_used'      => __('Choose from the most used', 'aras'),
		'popular_items'              => __('Popular Featured Blogs', 'aras'),
		'search_items'               => __('Search Featured Blogs', 'aras'),
		'not_found'                  => __('Not Found', 'aras'),
		'no_terms'                   => __('No Featured Blogs', 'aras'),
		'items_list'                 => __('Featured Blog list', 'aras'),
		'items_list_navigation'      => __('Featured Blog list navigation', 'aras'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
		//'rewrite'            => array(
		//	'slug' => 'featured-blogs',
		//	'with_front' => false,
		//),
	);
	register_taxonomy('featured-blog', array('post'), $args);
}
add_action('init', 'featured_blog_taxonomy', 1);
