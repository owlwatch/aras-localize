<?php

function custom_menu_order($menu_ord)
{
	if (!$menu_ord) {
		return true;
	}

	return array(
		'index.php', // Dashboard
		'global-content-media', // Global Content ***********
		'upload.php', // Media

		'separator1', // First separator

		'edit.php?post_type=page', // page
		'edit.php?post_type=lp', // lp
		'edit.php', // Posts
		'edit.php?post_type=demo', // lp
		'edit.php?post_type=resource', // resource
		'edit.php?post_type=event', // event
		'edit.php?post_type=glossary', // glossary
		'edit.php?post_type=mp-solution', // marketplace solution
		'edit.php?post_type=news', // news
		'edit.php?post_type=documentation', // documentation
		'edit.php?post_type=partners', // salesforce
		'edit.php?post_type=speaker', // resource
		'edit-comments.php', // Comments

		'separator2', // Second separator

		'tm/menu/main.php', // WPML
		'gf_edit_forms', // Gravity Forms
		'wpseo_dashboard', // Yoast
		'users.php', // Users
		'options-general.php', // Settings
		'tools.php', // Tools
		'types_access', // Toolset

		'separator-last', // Last separator

		'plugins.php', // Plugins
		'themes.php', // Appearance
	);
}
add_filter('custom_menu_order', 'custom_menu_order');
add_filter('menu_order', 'custom_menu_order');


/*Create custom option pages*/
if (function_exists('acf_add_options_page')) {

	acf_add_options_page(array(
		'page_title'   => 'Global Content',
		'menu_title'  => 'Global Content',
		'menu_slug'   => 'global-content-media',
		'capability'  => 'edit_posts',
		'redirect'    => false,
		'position'    => 2,
		'icon_url'    => 'dashicons-admin-site-alt3'
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Blog Settings',
		'menu_title'    => 'Blog Settings',
		'parent_slug'    => 'edit.php',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Resource Settings',
		'menu_title'    => 'Resource Settings',
		'parent_slug'    => 'edit.php?post_type=resource',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Events Settings',
		'menu_title'    => 'Events Settings',
		'parent_slug'    => 'edit.php?post_type=event',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Glossary Settings',
		'menu_title'    => 'Glossary Settings',
		'parent_slug'    => 'edit.php?post_type=glossary',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Documentation Settings',
		'menu_title'    => 'Documentation Settings',
		'parent_slug'    => 'edit.php?post_type=documentation',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'News Settings',
		'menu_title'    => 'News Settings',
		'parent_slug'    => 'edit.php?post_type=news',
	));


	acf_add_options_sub_page(array(
		'page_title'    => 'Discussion Settings',
		'menu_title'    => 'Discussion Settings',
		'parent_slug'   => 'options-discussion.php',
	));

	acf_add_options_sub_page(array(
		'page_title'     => 'Partner Settings',
		'menu_title'    => 'Partner Settings',
		'parent_slug'    => 'edit.php?post_type=partners',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Academic Users Settings',
		'menu_title'    => 'Academic Users Settings',
		'parent_slug'    => 'edit.php?post_type=partners',
	));
}




function salesforce_custom_menu()
{
	// Add a new top-level menu (parent menu)
	add_menu_page(
		__('Salesforce Integrations', 'textdomain'), // Page title
		__('Salesforce', 'textdomain'), // Menu title
		'manage_options',                // Capability
		'edit.php?post_type=partners', 	 	// Menu slug
		'',																// Function
		'dashicons-media-code',       // Icon URL
		9                                // Position
	);
	add_submenu_page(
		'edit.php?post_type=partners',                   // Parent slug
		__('Partners', 'textdomain'),   // Page title
		__('Partners', 'textdomain'),   // Menu title
		'manage_options',                // Capability
		'edit.php?post_type=partners',                   // Menu slug (should match parent menu slug)
		''          // Function (callback to display content)
	);
	add_submenu_page(
		'edit.php?post_type=partners',                   // Parent slug
		__('Academic Users', 'textdomain'), // Page title
		__('Academic Users', 'textdomain'), // Menu title
		'manage_options',                // Capability
		'edit.php?post_type=sf-academic-users' // Menu slug
	);
}
add_action('admin_menu', 'salesforce_custom_menu');
