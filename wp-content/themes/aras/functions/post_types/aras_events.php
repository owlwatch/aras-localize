<?php

/*** Post Event Type: Events ***/
function cptui_register_my_cpts_event()
{
	$labels = array(
		"name"                  => __('Events', ''),
		"singular_name"         => __('Event', ''),
		'menu_name'             => __('Events', ''),
		'name_admin_bar'        => __('Events', ''),
		'archives'              => __('Event Archives', ''),
		'attributes'            => __('Event Attributes', ''),
		'parent_item_colon'     => __('Event Event:', ''),
		'all_items'             => __('All Events', ''),
		'add_new_item'          => __('Add New Event', ''),
		'add_new'               => __('Add New', ''),
		'new_item'              => __('New Event', ''),
		'edit_item'             => __('Edit Event', ''),
		'update_item'           => __('Update Event', ''),
		'view_item'             => __('View Event', ''),
		'view_items'            => __('View Events', ''),
		'search_items'          => __('Search Events', ''),
		'not_found'             => __('Not found', ''),
		'not_found_in_trash'    => __('Not found in Trash', ''),
		'featured_image'        => __('Featured Image', ''),
		'set_featured_image'    => __('Set featured image', ''),
		'remove_featured_image' => __('Remove featured image', ''),
		'use_featured_image'    => __('Use as featured image', ''),
		'insert_into_item'      => __('Insert into Event', ''),
		'uploaded_to_this_item' => __('Uploaded to this Event', ''),
		'items_list'            => __('Event list', ''),
		'items_list_navigation' => __('Event list navigation', ''),
		'filter_items_list'     => __('Filter Event list', ''),
	);
	$args = array(
		'label'                 => __('Event', ''),
		'description'           => __('Event Post Event Type', ''),
		'labels'                => $labels,
		'supports'              => array('title', 'editor', 'custom-fields', 'thumbnail', 'page-attributes', 'revisions'),
		"taxonomies"            => array("industry", "event_region", "featured-event"),
		'public'                => true,
		'hierarchical'          => true,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => 'events',
		'menu_icon'             => 'dashicons-calendar-alt',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_event_type' => 'post',
		'posts_per_page'        => -1,
		'rewrite'            => array(
			'slug' => 'events/all',
			'with_front' => false,
		),
	);
	register_post_type('event', $args);
}
add_action('init', 'cptui_register_my_cpts_event', 0);


// Register Event Region Taxonomy
function event_region_taxonomy()
{
	$labels = array(
		'name'                       => _x('Event Regions', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Event Region', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Event Regions', 'text_domain'),
		'all_items'                  => __('Event Regions', 'text_domain'),
		'parent_item'                => __('Parent Event Region', 'text_domain'),
		'parent_item_colon'          => __('Parent Event Region:', 'text_domain'),
		'new_item_name'              => __('New Event Region', 'text_domain'),
		'add_new_item'               => __('Add New Event Region', 'text_domain'),
		'edit_item'                  => __('Edit Event Region', 'text_domain'),
		'update_item'                => __('Update Event Region', 'text_domain'),
		'view_item'                  => __('View Event Region', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove event regions', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Event Regions', 'text_domain'),
		'search_items'               => __('Search Event Regions', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Event Regions', 'text_domain'),
		'items_list'                 => __('Event Region list', 'text_domain'),
		'items_list_navigation'      => __('Event Region list navigation', 'text_domain'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy('event_region', array('event'), $args);
}
add_action('init', 'event_region_taxonomy', 1);

// Register Event Type Taxonomy
function event_type_taxonomy()
{
	$labels = array(
		'name'                       => _x('Event Types', 'Taxonomy General Name', 'text_domain'),
		'singular_name'              => _x('Event Type', 'Taxonomy Singular Name', 'text_domain'),
		'menu_name'                  => __('Event Type', 'text_domain'),
		'all_items'                  => __('All Event Types', 'text_domain'),
		'parent_item'                => __('Parent Event Type', 'text_domain'),
		'parent_item_colon'          => __('Parent Event Type:', 'text_domain'),
		'new_item_name'              => __('New Event Type Name', 'text_domain'),
		'add_new_item'               => __('Add New Event Type', 'text_domain'),
		'edit_item'                  => __('Edit Event Type', 'text_domain'),
		'update_item'                => __('Update Event Type', 'text_domain'),
		'view_item'                  => __('View Event Type', 'text_domain'),
		'separate_items_with_commas' => __('Separate items with commas', 'text_domain'),
		'add_or_remove_items'        => __('Add or remove items', 'text_domain'),
		'choose_from_most_used'      => __('Choose from the most used', 'text_domain'),
		'popular_items'              => __('Popular Event Types', 'text_domain'),
		'search_items'               => __('Search Event Types', 'text_domain'),
		'not_found'                  => __('Not Found', 'text_domain'),
		'no_terms'                   => __('No Event Types', 'text_domain'),
		'items_list'                 => __('Event Type list', 'text_domain'),
		'items_list_navigation'      => __('Event Type list navigation', 'text_domain'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'query_var'                  => true,
		'show_tagcloud'              => false,
	);
	register_taxonomy('event_type', array('event'), $args);
}
add_action('init', 'event_type_taxonomy', 1);
