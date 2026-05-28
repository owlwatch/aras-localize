<?php

/*** Post Type: Documentation ***/
function cptui_register_my_cpts_documentation()
{
	$labels = array(
		"name"                  => __('Documentation', 'aras'),
		"singular_name"         => __('Documentation', 'aras'),
		'menu_name'             => __('Documentation', 'aras'),
		'name_admin_bar'        => __('Documentation', 'aras'),
		'archives'              => __('Documentation Archives', 'aras'),
		'attributes'            => __('Documentation Attributes', 'aras'),
		'parent_item_colon'     => __('Documentation:', 'aras'),
		'all_items'             => __('All Documentation', 'aras'),
		'add_new_item'          => __('Add New Documentation', 'aras'),
		'add_new'               => __('Add New', 'aras'),
		'new_item'              => __('New Documentation', 'aras'),
		'edit_item'             => __('Edit Documentation', 'aras'),
		'update_item'           => __('Update Documentation', 'aras'),
		'view_item'             => __('View Documentation', 'aras'),
		'view_items'            => __('View Documentation', 'aras'),
		'search_items'          => __('Search Documentation', 'aras'),
		'not_found'             => __('Not found', 'aras'),
		'not_found_in_trash'    => __('Not found in Trash', 'aras'),
		'featured_image'        => __('Featured Image', 'aras'),
		'set_featured_image'    => __('Set featured image', 'aras'),
		'remove_featured_image' => __('Remove featured image', 'aras'),
		'use_featured_image'    => __('Use as featured image', 'aras'),
		'insert_into_item'      => __('Insert into Documentation', 'aras'),
		'uploaded_to_this_item' => __('Uploaded to this Documentation', 'aras'),
		'items_list'            => __('Documentation list', 'aras'),
		'items_list_navigation' => __('Documentation list navigation', 'aras'),
		'filter_items_list'     => __('Filter Documentation list', 'aras'),
	);
	$args = array(
		'label'                 => __('Documentation', 'aras'),
		'description'           => __('Documentation Post Type', 'aras'),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		"taxonomies"            => array("category", "post_tag"),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => 'support/documentation',
		'menu_icon'             => 'dashicons-book',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
		'posts_per_page'        => -1,
		'rewrite'            => array(
			'slug' => 'support/documentation',
			'with_front' => false,
		),
	);
	register_post_type('documentation', $args);
}
add_action('init', 'cptui_register_my_cpts_documentation', 0);
