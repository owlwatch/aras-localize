<?php

/*** Post Event Type: Events ***/
function cptui_register_my_cpts_demo()
{
	$labels = array(
		"name"                  => __('Demo', 'aras'),
		"singular_name"         => __('Demo', 'aras'),
		'menu_name'             => __('Demos', 'aras'),
		'name_admin_bar'        => __('Demo', 'aras'),
		'archives'              => __('Demo Archives', 'aras'),
		'attributes'            => __('Demo Attributes', 'aras'),
		'parent_item_colon'     => __('Demo:', 'aras'),
		'all_items'             => __('All Demos', 'aras'),
		'add_new_item'          => __('Add New Demo', 'aras'),
		'add_new'               => __('Add New', 'aras'),
		'new_item'              => __('New Demo', 'aras'),
		'edit_item'             => __('Edit Demo', 'aras'),
		'update_item'           => __('Update Demo', 'aras'),
		'view_item'             => __('View Demo', 'aras'),
		'view_items'            => __('View Demos', 'aras'),
		'search_items'          => __('Search Demos', 'aras'),
		'not_found'             => __('Not found', 'aras'),
		'not_found_in_trash'    => __('Not found in Trash', 'aras'),
		'featured_image'        => __('Featured Image', 'aras'),
		'set_featured_image'    => __('Set featured image', 'aras'),
		'remove_featured_image' => __('Remove featured image', 'aras'),
		'use_featured_image'    => __('Use as featured image', 'aras'),
		'insert_into_item'      => __('Insert into Demo', 'aras'),
		'uploaded_to_this_item' => __('Uploaded to this Demo', 'aras'),
		'items_list'            => __('Demo list', 'aras'),
		'items_list_navigation' => __('Demo list navigation', 'aras'),
		'filter_items_list'     => __('Filter Demo list', 'aras'),
	);
	$args = array(
		'label'                 => __('Demo', 'aras'),
		'description'           => __('Demo Post Type', 'aras'),
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
