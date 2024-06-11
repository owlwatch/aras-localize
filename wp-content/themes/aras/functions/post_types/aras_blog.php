<?php
// Register Featured Blog Taxonomy
function featured_blog_taxonomy()
{
	$labels = array(
		'name'                       => _x('Featured Blogs', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Featured Blog', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Featured', 'text_domain'),
		'all_items'                  => __('Featured Blogs', 'text_domain'),
		'parent_item'                => __('Parent Featured Blog', 'text_domain'),
		'parent_item_colon'          => __('Parent Featured Blog:', 'text_domain'),
		'new_item_name'              => __('New Featured Blog Name', 'text_domain'),
		'add_new_item'               => __('Add New Featured Blog', 'text_domain'),
		'edit_item'                  => __('Edit Featured Blog', 'text_domain'),
		'update_item'                => __('Update Featured Blog', 'text_domain'),
		'view_item'                  => __('View Featured Blog', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Featured Blogs', 'text_domain'),
		'search_items'               => __('Search Featured Blogs', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Featured Blogs', 'text_domain'),
		'items_list'                 => __('Featured Blog list', 'text_domain'),
		'items_list_navigation'      => __('Featured Blog list navigation', 'text_domain'),
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
