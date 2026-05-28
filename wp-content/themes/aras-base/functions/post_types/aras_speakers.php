<?php

/*** Post Type: Speakers ***/
function cptui_register_my_cpts_speaker()
{
	$labels = array(
		"name"                  => __('Speakers', 'aras'),
		"singular_name"         => __('Speaker', 'aras'),
		'menu_name'             => __('Speakers', 'aras'),
		'name_admin_bar'        => __('Speakers', 'aras'),
		'archives'              => __('Speaker Archives', 'aras'),
		'attributes'            => __('Speaker Attributes', 'aras'),
		'parent_item_colon'     => __('Speaker:', 'aras'),
		'all_items'             => __('All Speakers', 'aras'),
		'add_new_item'          => __('Add New Speaker', 'aras'),
		'add_new'               => __('Add New', 'aras'),
		'new_item'              => __('New Speaker', 'aras'),
		'edit_item'             => __('Edit Speaker', 'aras'),
		'update_item'           => __('Update Speaker', 'aras'),
		'view_item'             => __('View Speaker', 'aras'),
		'view_items'            => __('View Speakers', 'aras'),
		'search_items'          => __('Search Speakers', 'aras'),
		'not_found'             => __('Not found', 'aras'),
		'not_found_in_trash'    => __('Not found in Trash', 'aras'),
		'featured_image'        => __('Featured Image', 'aras'),
		'set_featured_image'    => __('Set featured image', 'aras'),
		'remove_featured_image' => __('Remove featured image', 'aras'),
		'use_featured_image'    => __('Use as featured image', 'aras'),
		'insert_into_item'      => __('Insert into Speaker', 'aras'),
		'uploaded_to_this_item' => __('Uploaded to this Speaker', 'aras'),
		'items_list'            => __('Speaker list', 'aras'),
		'items_list_navigation' => __('Speaker list navigation', 'aras'),
		'filter_items_list'     => __('Filter Speaker list', 'aras'),
	);
	$args = array(
		'label'                 => __('Speaker', 'aras'),
		'description'           => __('Speaker Post Type', 'aras'),
		'labels'                => $labels,
		'supports'              => array('title', 'custom-fields', 'revisions'),
		'public'                => false,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => false,
		'menu_icon'             => 'dashicons-editor-quote',
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'posts_per_page'        => -1,
		'rewrite'            => array(
			'slug'       => 'speakers/all',
			'with_front' => false,
		),
	);
	register_post_type('speaker', $args);
}
add_action('init', 'cptui_register_my_cpts_speaker', 0);
