<?php

/*** Post Type: News ***/
function cptui_register_my_cpts_news()
{
	$labels = array(
		"name"                  => __('News', ''),
		"singular_name"         => __('News', ''),
		'menu_name'             => __('News', ''),
		'name_admin_bar'        => __('News', ''),
		'archives'              => __('News Archives', ''),
		'attributes'            => __('News Attributes', ''),
		'parent_item_colon'     => __('News:', ''),
		'all_items'             => __('All News', ''),
		'add_new_item'          => __('Add New News', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New News', ''),
		'edit_item'             => __('Edit News', ''),
		'update_item'           => __('Update News', ''),
		'view_item'             => __('View News', ''),
		'view_items'            => __('View News', ''),
		'search_items'          => __('Search News', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into News', ''),
		'uploaded_to_this_item' => __('Uploaded to this News', ''),
		'items_list'            => __('News list', ''),
		'items_list_navigation' => __('News list navigation', ''),
		'filter_items_list'     => __('Filter News list', ''),
	);
	$args = array(
		'label'                 => __('News', ''),
		'description'           => __('News Post Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => 'news',
		'menu_icon'             => 'dashicons-align-left',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'posts_per_page'        => -1,
		'rewrite'            => array(
			'slug'       => 'news/press-releases/%year%/%monthnum%',
			'with_front' => false,
		),
	);
	register_post_type('news', $args);
}
add_action('init', 'cptui_register_my_cpts_news', 0);


function news_permalink_structure($post_link, $post)
{
	if ($post->post_type === 'news') {
		$year = get_the_date('Y', $post);
		$month = get_the_date('m', $post);
		$post_link = str_replace('%year%', $year, $post_link);
		$post_link = str_replace('%monthnum%', $month, $post_link);
	}
	return $post_link;
}
add_filter('post_type_link', 'news_permalink_structure', 10, 2);

///////////  // Add custom rewrite rules
///////////  function custom_rewrite_rules_news()
///////////  {
///////////  	add_rewrite_rule(
///////////  		'news/press-releases/([0-9]{4})/([0-9]{2})/(.+)/?$',
///////////  		'index.php?post_type=news&year=$matches[1]&monthnum=$matches[2]&name=$matches[3]',
///////////  		'top'
///////////  	);
///////////  }
///////////  add_action('init', 'custom_rewrite_rules_news');
///////////  // Filter post type link to include year and month in the URL
///////////  function custom_post_type_permalink($permalink, $post, $leavename)
///////////  {
///////////  	if ($post->post_type == 'news') {
///////////  		$year = get_the_date('Y', $post->ID);
///////////  		$month = get_the_date('m', $post->ID);
///////////  
///////////  		$permalink = home_url("news/press-releases/$year/$month/$post->post_name/");
///////////  	}
///////////  	return $permalink;
///////////  }
///////////  add_filter('post_type_link', 'custom_post_type_permalink', 10, 3);
///////////  



// Register Type Taxonomy
function news_type_taxonomy()
{
	$labels = array(
		'name'                       => _x('News Types', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('News Type', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('News Types', 'text_domain'),
		'all_items'                  => __('News Types', 'text_domain'),
		'parent_item'                => __('Parent News Type', 'text_domain'),
		'parent_item_colon'          => __('Parent News Type:', 'text_domain'),
		'new_item_name'              => __('New News Type Name', 'text_domain'),
		'add_new_item'               => __('Add New News Type', 'text_domain'),
		'edit_item'                  => __('Edit News Type', 'text_domain'),
		'update_item'                => __('Update News Type', 'text_domain'),
		'view_item'                  => __('View News Type', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular News Types', 'text_domain'),
		'search_items'               => __('Search News Types', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No News Types', 'text_domain'),
		'items_list'                 => __('News Type list', 'text_domain'),
		'items_list_navigation'      => __('News Type list navigation', 'text_domain'),
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
		//'rewrite'            => array('slug' => 'industries', 'with_front' => false),
	);
	register_taxonomy('news_type', array('news'), $args);
}
add_action('init', 'news_type_taxonomy', 1);
