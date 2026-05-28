<?php

/*** Post Type: Author Profiles ***/
function cptui_register_my_cpts_user_profile()
{
	$labels = array(
		"name"                  => __('Author Profiles', 'aras'),
		"singular_name"         => __('Author Profile', 'aras'),
		'menu_name'             => __('Author Profiles', 'aras'),
		'name_admin_bar'        => __('Author Profiles', 'aras'),
		'archives'              => __('Author Profile Archives', 'aras'),
		'attributes'            => __('Author Profile Attributes', 'aras'),
		'parent_item_colon'     => __('Author Profile:', 'aras'),
		'all_items'             => __('All Author Profiles', 'aras'),
		'add_new_item'          => __('Add New Author Profile', 'aras'),
		'add_new'               => __('Add New', 'aras'),
		'new_item'              => __('New Author Profile', 'aras'),
		'edit_item'             => __('Edit Author Profile', 'aras'),
		'update_item'           => __('Update Author Profile', 'aras'),
		'view_item'             => __('View Author Profile', 'aras'),
		'view_items'            => __('View Author Profiles', 'aras'),
		'search_items'          => __('Search Author Profiles', 'aras'),
		'not_found'             => __('Not found', 'aras'),
		'not_found_in_trash'    => __('Not found in Trash', 'aras'),
		'featured_image'        => __('Featured Image', 'aras'),
		'set_featured_image'    => __('Set featured image', 'aras'),
		'remove_featured_image' => __('Remove featured image', 'aras'),
		'use_featured_image'    => __('Use as featured image', 'aras'),
		'insert_into_item'      => __('Insert into Author Profile', 'aras'),
		'uploaded_to_this_item' => __('Uploaded to this Author Profile', 'aras'),
		'items_list'            => __('Author Profile list', 'aras'),
		'items_list_navigation' => __('Author Profile list navigation', 'aras'),
		'filter_items_list'     => __('Filter Author Profile list', 'aras'),
	);
	$args = array(
		'label'                 => __('Author Profile', 'aras'),
		'description'           => __('Author Profile Post Type', 'aras'),
		'labels'                => $labels,
		'supports'              => array('title', 'custom-fields', 'revisions'),
		'public'                => false,
		'hierarchical'          => false,
		'show_ui'               => true,
		'show_in_nav_menus'     => true,
		"show_in_rest"          => false,
		'has_archive'           => false,
		'menu_icon'             => 'dashicons-id',
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'post',
		'posts_per_page'        => -1
	);
	register_post_type('user-profile', $args);
}
add_action('init', 'cptui_register_my_cpts_user_profile', 0);

// add some filters to automatically create a 'user-profile' when a user is created
// or updated
add_action('user_register', 'aras_create_user_profile');
add_action('profile_update', 'aras_create_user_profile');

function aras_create_user_profile($user_id)
{
	// check if the user already has a profile
	$profile = get_posts(array(
		'post_type' => 'user-profile',
		'meta_key' => 'user',
		'meta_value' => $user_id,
	));
	if ($profile) {
		return $profile[0]->ID;
	}

	// create a new profile
	$profile = array(
		'post_title' => get_the_author_meta('display_name', $user_id),
		// match the slug to the user's login
		'post_name' => get_the_author_meta('user_login', $user_id),
		'post_type' => 'user-profile',
		'post_status' => 'publish',
	);
	
	// insert the post into the database
	$profile_id = wp_insert_post($profile);
	update_post_meta($profile_id, 'user', $user_id);
	return $profile_id;
}

// there is an existing field group for user's that has a field called
// author_information. we need a script that runs if the user is an
// admin and the $_REQUEST has a 'migrate-user-profiles' key set
// that will copy the author_information field to the
// user-profile post type
add_action('init', 'aras_migrate_user_profiles');

function aras_migrate_user_profiles()
{
	if (is_admin() && isset($_REQUEST['migrate-user-profiles'])) {
		// get all the users
		$users = get_users(array(
			'fields' => array('ID')
		));
		foreach ($users as $user) {
			// get the author_information field
			$author_information = get_field('author_information', 'user_' . $user->ID);
			if ($author_information) {
				// create a new profile
				$profile_id = aras_create_user_profile($user->ID);
				// update the profile with the author information
				update_field('author_information', $author_information, $profile_id);
			}
		}
	}
}