<?php

/*** Post Event Type: Events ***/
function cptui_register_my_cpts_demo()
{
	$labels = array(
		"name"                  => __('Demo', ''),
		"singular_name"         => __('Demo', ''),
		'menu_name'             => __('Demos', ''),
		'name_admin_bar'        => __('Demo', ''),
		'archives'              => __('Demo Archives', ''),
		'attributes'            => __('Demo Attributes', ''),
		'parent_item_colon'     => __('Demo:', ''),
		'all_items'             => __('All Demos', ''),
		'add_new_item'          => __('Add New Demo', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Demo', ''),
		'edit_item'             => __('Edit Demo', ''),
		'update_item'           => __('Update Demo', ''),
		'view_item'             => __('View Demo', ''),
		'view_items'            => __('View Demos', ''),
		'search_items'          => __('Search Demos', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Demo', ''),
		'uploaded_to_this_item' => __('Uploaded to this Demo', ''),
		'items_list'            => __('Demo list', ''),
		'items_list_navigation' => __('Demo list navigation', ''),
		'filter_items_list'     => __('Filter Demo list', ''),
	);
	$args = array(
		'label'                 => __('Demo', ''),
		'description'           => __('Demo Post Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		"taxonomies"            => array(),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => false,
		'menu_icon'             => 'dashicons-controls-play',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_event_type' => 'post',
		'rewrite'            => array(
			'slug' => 'demo',
			'with_front' => false,
		),
	);
	register_post_type('demo', $args);
}
add_action('init', 'cptui_register_my_cpts_demo', 0);
