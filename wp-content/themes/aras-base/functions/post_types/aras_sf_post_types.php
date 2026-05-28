<?php

/*** Post Type: Partners ***/
function cptui_register_my_cpts_partner()
{
	$labels = array(
		"name"                  => __('Partners', 'aras'),
		"singular_name"         => __('Partner', 'aras'),
		'menu_name'             => __('Partners', 'aras'),
		'name_admin_bar'        => __('Partners', 'aras'),
		'archives'              => __('Partner Archives', 'aras'),
		'attributes'            => __('Partner Attributes', 'aras'),
		'parent_item_colon'     => __('Partners:', 'aras'),
		'all_items'             => __('All Partners', 'aras'),
		'add_new_item'          => __('Add New Partner', 'aras'),
		'add_new'               => __('Add New', 'aras'),
		'new_item'              => __('New Partner', 'aras'),
		'edit_item'             => __('Edit Partner', 'aras'),
		'update_item'           => __('Update Partner', 'aras'),
		'view_item'             => __('View Partner', 'aras'),
		'view_items'            => __('View Partners', 'aras'),
		'search_items'          => __('Search Partners', 'aras'),
		'not_found'             => __('Not found', 'aras'),
		'not_found_in_trash'    => __('Not found in Trash', 'aras'),
		'featured_image'        => __('Featured Image', 'aras'),
		'set_featured_image'    => __('Set featured image', 'aras'),
		'remove_featured_image' => __('Remove featured image', 'aras'),
		'use_featured_image'    => __('Use as featured image', 'aras'),
		'insert_into_item'      => __('Insert into Partner', 'aras'),
		'uploaded_to_this_item' => __('Uploaded to this Partner', 'aras'),
		'items_list'            => __('Partner list', 'aras'),
		'items_list_navigation' => __('Partner list navigation', 'aras'),
		'filter_items_list'     => __('Filter Partner list', 'aras'),
	);
	$args = array(
		'label'                 => __('Partner', 'aras'),
		'description'           => __('Partner Post Type', 'aras'),
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
