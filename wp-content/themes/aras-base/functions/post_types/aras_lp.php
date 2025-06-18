<?php

/*** Post Event Type: Events ***/
function cptui_register_my_cpts_lp()
{
	$labels = array(
		"name"                  => __('Landing Pages', ''),
		"singular_name"         => __('Landing Page', ''),
		'menu_name'             => __('Landing Pages', ''),
		'name_admin_bar'        => __('Landing Pages', ''),
		'archives'              => __('Landing Page Archives', ''),
		'attributes'            => __('Landing Page Attributes', ''),
		'parent_item_colon'     => __('Landing Page:', ''),
		'all_items'             => __('All Landing Pages', ''),
		'add_new_item'          => __('Add New Landing Page', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Landing Page', ''),
		'edit_item'             => __('Edit Landing Page', ''),
		'update_item'           => __('Update Landing Page', ''),
		'view_item'             => __('View Landing Page', ''),
		'view_items'            => __('View Landing Pages', ''),
		'search_items'          => __('Search Landing Pages', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Landing Page', ''),
		'uploaded_to_this_item' => __('Uploaded to this Landing Page', ''),
		'items_list'            => __('Landing Page list', ''),
		'items_list_navigation' => __('Landing Page list navigation', ''),
		'filter_items_list'     => __('Filter Landing Page list', ''),
	);
	$args = array(
		'label'                 => __('Landing Page', ''),
		'description'           => __('Landing Page Post Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		"taxonomies"            => array("industry", "event_region", "featured-event"),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => false,
		'menu_icon'             => 'dashicons-format-aside',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_event_type' => 'post',
		'posts_per_page'        => -1,
		'rewrite'            => array(
			'slug' => 'lp',
			'with_front' => false,
		),
	);
	register_post_type('lp', $args);
}
add_action('init', 'cptui_register_my_cpts_lp', 0);
