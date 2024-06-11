<?php
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
		'page_title'     => 'Glossary Settings',
		'menu_title'    => 'Glossary Settings',
		'parent_slug'    => 'edit.php?post_type=glossary',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Partner Settings',
		'menu_title'    => 'Partner Settings',
		'parent_slug'    => 'edit.php?post_type=partners',
	));
	acf_add_options_sub_page(array(
		'page_title'     => 'Academic Users Settings',
		'menu_title'    => 'Academic Users Settings',
		'parent_slug'    => 'edit.php?post_type=sf-academic-users',
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
}
