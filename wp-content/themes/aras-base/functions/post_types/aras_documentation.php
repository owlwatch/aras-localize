<?php

/*** Post Type: Documentation ***/
function cptui_register_my_cpts_documentation()
{
	$labels = array(
		"name"                  => __('Documentation', ''),
		"singular_name"         => __('Documentation', ''),
		'menu_name'             => __('Documentation', ''),
		'name_admin_bar'        => __('Documentation', ''),
		'archives'              => __('Documentation Archives', ''),
		'attributes'            => __('Documentation Attributes', ''),
		'parent_item_colon'     => __('Documentation:', ''),
		'all_items'             => __('All Documentation', ''),
		'add_new_item'          => __('Add New Documentation', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Documentation', ''),
		'edit_item'             => __('Edit Documentation', ''),
		'update_item'           => __('Update Documentation', ''),
		'view_item'             => __('View Documentation', ''),
		'view_items'            => __('View Documentation', ''),
		'search_items'          => __('Search Documentation', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Documentation', ''),
		'uploaded_to_this_item' => __('Uploaded to this Documentation', ''),
		'items_list'            => __('Documentation list', ''),
		'items_list_navigation' => __('Documentation list navigation', ''),
		'filter_items_list'     => __('Filter Documentation list', ''),
	);
	$args = array(
		'label'                 => __('Documentation', ''),
		'description'           => __('Documentation Post Type', ''),
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
