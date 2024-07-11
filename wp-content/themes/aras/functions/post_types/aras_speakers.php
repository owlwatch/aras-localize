<?php

/*** Post Type: Speakers ***/
function cptui_register_my_cpts_speaker()
{
	$labels = array(
		"name"                  => __('Speakers', ''),
		"singular_name"         => __('Speaker', ''),
		'menu_name'             => __('Speakers', ''),
		'name_admin_bar'        => __('Speakers', ''),
		'archives'              => __('Speaker Archives', ''),
		'attributes'            => __('Speaker Attributes', ''),
		'parent_item_colon'     => __('Speaker:', ''),
		'all_items'             => __('All Speakers', ''),
		'add_new_item'          => __('Add New Speaker', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Speaker', ''),
		'edit_item'             => __('Edit Speaker', ''),
		'update_item'           => __('Update Speaker', ''),
		'view_item'             => __('View Speaker', ''),
		'view_items'            => __('View Speakers', ''),
		'search_items'          => __('Search Speakers', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Speaker', ''),
		'uploaded_to_this_item' => __('Uploaded to this Speaker', ''),
		'items_list'            => __('Speaker list', ''),
		'items_list_navigation' => __('Speaker list navigation', ''),
		'filter_items_list'     => __('Filter Speaker list', ''),
	);
	$args = array(
		'label'                 => __('Speaker', ''),
		'description'           => __('Speaker Post Type', ''),
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
