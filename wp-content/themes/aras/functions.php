<?php

/** 
 * For more info: https://developer.wordpress.org/themes/basics/theme-functions/
 */

// Theme support options
require_once(get_template_directory() . '/functions/theme-support.php');

// WP Head and other cleanup functions
require_once(get_template_directory() . '/functions/cleanup.php');

// Register scripts and stylesheets
require_once(get_template_directory() . '/functions/enqueue-scripts.php');

// Register custom menus and menu walkers
require_once(get_template_directory() . '/functions/menu.php');

// Register sidebars/widget areas
//require_once(get_template_directory() . '/functions/sidebar.php');

// Makes WordPress comments suck less
require_once(get_template_directory() . '/functions/comments.php');

// Replace 'older/newer' post links with numbered navigation
require_once(get_template_directory() . '/functions/page-navi.php');

// Adds support for multiple languages
require_once(get_template_directory() . '/functions/translation/translation.php');

// Remove Emoji Support
// require_once(get_template_directory().'/functions/disable-emoji.php'); 

// Related post function - no need to rely on plugins
// require_once(get_template_directory().'/functions/related-posts.php'); 

// Use this as a template for custom post types
// require_once(get_template_directory().'/functions/custom-post-type.php');

// Customize the WordPress login menu
require_once(get_template_directory() . '/functions/login.php');

// Customize the WordPress admin
// require_once(get_template_directory().'/functions/admin.php'); 



/*     CUSTOM FUNCTIONS     */

// Options Pages
require_once(get_template_directory() . '/functions/aras_options_pages.php');

//Miscellaneous functions
require_once(get_template_directory() . '/functions/aras_custom_functions.php');

//Plugin adjustments
require_once(get_template_directory() . '/functions/aras_plugins.php');

// Footer Scripts
require_once(get_template_directory() . '/functions/aras_footer.php');


/*     API CUSTOM FUNCTIONS     */

//Salesforce-related API connections (Partners, Academic Users)
require_once(get_template_directory() . '/functions/apis/api_salesforce.php');

//MyI API Functions
require_once(get_template_directory() . '/functions/apis/api_myi.php');

//Gravity Forms Functions
require_once(get_template_directory() . '/functions/apis/aras_gravity_forms.php');


/*     POST TYPE CUSTOM FUNCTIONS     */

//AJAX load settings
require_once(get_template_directory() . '/functions/post_types/aras_ajax.php');

//Salseforce Post Types (Partners, Academic Users)
require_once(get_template_directory() . '/functions/post_types/aras_sf_post_types.php');

//Resources CPT and Tax
require_once(get_template_directory() . '/functions/post_types/aras_resources.php');

//Events
require_once(get_template_directory() . '/functions/post_types/aras_events.php');

//Documentation CPT
require_once(get_template_directory() . '/functions/post_types/aras_documentation.php');

//News CPT and Tax
require_once(get_template_directory() . '/functions/post_types/aras_news.php');

//Blog Tax
require_once(get_template_directory() . '/functions/post_types/aras_blog.php');

//LP CPT
require_once(get_template_directory() . '/functions/post_types/aras_lp.php');

//Speakers CPT
require_once(get_template_directory() . '/functions/post_types/aras_speakers.php');

//WPML DB Insert - complete, keeping this here and commented out for history. Associated folder - "./functions/wpml-resources"
//require_once(get_template_directory() . '/functions/wpml-resources/resource_update.php');

require_once(get_template_directory() . '/functions/scripts/index.php');