<?php

/*** Post Type: Academic Users ***/
function cptui_register_my_cpts_academic_users()
{
	$labels = array(
		"name"                  => __('Academic Users', ''),
		"singular_name"         => __('Academic User', ''),
		'menu_name'             => __('Academic Users', ''),
		'name_admin_bar'        => __('Academic Users', ''),
		'archives'              => __('Academic User Archives', ''),
		'attributes'            => __('Academic User Attributes', ''),
		'parent_item_colon'     => __('Academic Users:', ''),
		'all_items'             => __('All Academic Users', ''),
		'add_new_item'          => __('Add New Academic User', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Academic User', ''),
		'edit_item'             => __('Edit Academic User', ''),
		'update_item'           => __('Update Academic User', ''),
		'view_item'             => __('View Academic User', ''),
		'view_items'            => __('View Academic Users', ''),
		'search_items'          => __('Search Academic Users', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Academic User', ''),
		'uploaded_to_this_item' => __('Uploaded to this Academic User', ''),
		'items_list'            => __('Academic User list', ''),
		'items_list_navigation' => __('Academic User list navigation', ''),
		'filter_items_list'     => __('Filter Academic User list', ''),
	);
	$args = array(
		'label'                 => __('Academic User', ''),
		'description'           => __('Academic User Post Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		"taxonomies"            => array(""),
		'public'                => false,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => 'academic-program/academic-users',
		'menu_icon'             => 'dashicons-format-aside',
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'posts_per_page'        => -1,
		'rewrite'               => array(
			'slug' => 'academic-program/academic-users',
			'with_front' => false,
		),
		'show_in_menu'     => false,
	);
	register_post_type('sf-academic-users', $args);
}
add_action('init', 'cptui_register_my_cpts_academic_users', 0);

/*** Post Type: Partners ***/
function cptui_register_my_cpts_partner()
{
	$labels = array(
		"name"                  => __('Partners', ''),
		"singular_name"         => __('Partner', ''),
		'menu_name'             => __('Partners', ''),
		'name_admin_bar'        => __('Partners', ''),
		'archives'              => __('Partner Archives', ''),
		'attributes'            => __('Partner Attributes', ''),
		'parent_item_colon'     => __('Partners:', ''),
		'all_items'             => __('All Partners', ''),
		'add_new_item'          => __('Add New Partner', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Partner', ''),
		'edit_item'             => __('Edit Partner', ''),
		'update_item'           => __('Update Partner', ''),
		'view_item'             => __('View Partner', ''),
		'view_items'            => __('View Partners', ''),
		'search_items'          => __('Search Partners', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Partner', ''),
		'uploaded_to_this_item' => __('Uploaded to this Partner', ''),
		'items_list'            => __('Partner list', ''),
		'items_list_navigation' => __('Partner list navigation', ''),
		'filter_items_list'     => __('Filter Partner list', ''),
	);
	$args = array(
		'label'                 => __('Partner', ''),
		'description'           => __('Partner Post Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'page-attributes', 'revisions'),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => 'partners/find-a-partner',
		'with_front'						=> false,
		'menu_icon'             => 'dashicons-format-aside',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'posts_per_page'        => -1,
		'rewrite'               => array(
			'slug' => 'partners/find-a-partner/all',
			'with_front' => false,
		),
		'show_in_menu'     => false,
	);
	register_post_type('partners', $args);
}
add_action('init', 'cptui_register_my_cpts_partner', 0);
