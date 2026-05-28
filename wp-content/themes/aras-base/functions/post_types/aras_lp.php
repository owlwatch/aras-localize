<?php

/*** Post Event Type: Events ***/
function cptui_register_my_cpts_lp()
{
	$labels = array(
		"name"                  => __('Landing Pages', 'aras'),
		"singular_name"         => __('Landing Page', 'aras'),
		'menu_name'             => __('Landing Pages', 'aras'),
		'name_admin_bar'        => __('Landing Pages', 'aras'),
		'archives'              => __('Landing Page Archives', 'aras'),
		'attributes'            => __('Landing Page Attributes', 'aras'),
		'parent_item_colon'     => __('Landing Page:', 'aras'),
		'all_items'             => __('All Landing Pages', 'aras'),
		'add_new_item'          => __('Add New Landing Page', 'aras'),
		'add_new'               => __('Add New', 'aras'),
		'new_item'              => __('New Landing Page', 'aras'),
		'edit_item'             => __('Edit Landing Page', 'aras'),
		'update_item'           => __('Update Landing Page', 'aras'),
		'view_item'             => __('View Landing Page', 'aras'),
		'view_items'            => __('View Landing Pages', 'aras'),
		'search_items'          => __('Search Landing Pages', 'aras'),
		'not_found'             => __('Not found', 'aras'),
		'not_found_in_trash'    => __('Not found in Trash', 'aras'),
		'featured_image'        => __('Featured Image', 'aras'),
		'set_featured_image'    => __('Set featured image', 'aras'),
		'remove_featured_image' => __('Remove featured image', 'aras'),
		'use_featured_image'    => __('Use as featured image', 'aras'),
		'insert_into_item'      => __('Insert into Landing Page', 'aras'),
		'uploaded_to_this_item' => __('Uploaded to this Landing Page', 'aras'),
		'items_list'            => __('Landing Page list', 'aras'),
		'items_list_navigation' => __('Landing Page list navigation', 'aras'),
		'filter_items_list'     => __('Filter Landing Page list', 'aras'),
	);
	$args = array(
		'label'                 => __('Landing Page', 'aras'),
		'description'           => __('Landing Page Post Type', 'aras'),
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
