<?php
/** 
 * For more info: https://developer.wordpress.org/themes/basics/theme-functions/
 */

$config = apply_filters('aras_config', [
	'version' => '1.0.0',
	'textdomain' => 'aras',
	'api_url' => 'https://api.aras.com',
	'site_url' => 'https://www.aras.com',
	'features' => []
]);

global $aras_base_config;
$aras_base_config = $config;


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

// WPML helper functions
require_once(get_template_directory() . '/functions/wpml.php');

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
if( in_array( 'salesforce', $config['features'] ) ) {
	//Salesforce-related API connections (Partners, Academic Users)
	require_once(get_template_directory() . '/functions/apis/api_salesforce.php');
}

//MyI API Functions
if( in_array( 'myi', $config['features'] ) ) {
	// MyI API Functions
	require_once(get_template_directory() . '/functions/apis/api_myi.php');
}

if( in_array('gravity_forms', $config['features']) ) {
	//Gravity Forms Functions
	require_once(get_template_directory() . '/functions/apis/class.aras_gravity_forms.php');
}

// Swoogo Functions
if( in_array('swoogo', $config['features']) ) {
	// Swoogo API Functions
	require_once(get_template_directory() . '/functions/apis/api_swoogo.php');
}

// Verint API Functions
if( in_array('verint', $config['features']) ) {
	// Verint API Functions
	require_once(get_template_directory() . '/functions/apis/api_verint.php');
}


/*     POST TYPE CUSTOM FUNCTIONS     */

//AJAX load settings
require_once(get_template_directory() . '/functions/post_types/aras_ajax.php');

// Salseforce Post Types (Partners, Academic Users)
if( in_array( 'salesforce', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_sf_post_types.php');
}

//Resources CPT and Tax
if( in_array( 'resources', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_resources.php');
}

//Events
if( in_array( 'events', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_events.php');
}

//Documentation CPT
if( in_array( 'documentation', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_documentation.php');
}

//News CPT and Tax
if( in_array( 'news', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_news.php');
}

//Blog Tax
if( in_array( 'blog', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_blog.php');
}

//LP CPT
if( in_array( 'lp', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_lp.php');
}

//Demo CPT
if( in_array( 'demo', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_demo.php');
}

//Speakers CPT
if( in_array( 'speakers', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_speakers.php');
}

// User Profiles
if( in_array( 'user_profiles', $config['features'] ) ) {
	require_once(get_template_directory() . '/functions/post_types/aras_user-profiles.php');
}

if( in_array('quotes', $config['features']) ) {
	// Quotes
	require_once(get_template_directory() . '/functions/post_types/aras_quote.php');
}

//WPML DB Insert - complete, keeping this here and commented out for history. Associated folder - "./functions/wpml-resources"
//require_once(get_template_directory() . '/functions/wpml-resources/resource_update.php');

require_once(get_template_directory() . '/functions/scripts/index.php');

require_once(get_template_directory() . '/functions/shortcodes.php');

require_once(get_template_directory() . '/functions/filters.php');

require_once(get_template_directory() . '/functions/seo.php');

require_once(get_template_directory() . '/functions/bitly.php');

if( in_array('qualified', $config['features']) ) {
	// Qualified API Functions
	require_once(get_template_directory() . '/functions/qualified.php');
}

if( in_array('google_ads_enhanced_conversions', $config['features']) ) {
	// Google Ads Enhanced Conversions
	require_once(get_template_directory() . '/functions/google-ads-enhanced-conversions.php');
}

require_once(get_template_directory() . '/functions/sitemap.php');

require_once(get_template_directory() . '/functions/debug.php');

if( isset($_REQUEST['create_api_json']) ){
	// Create api_json directory
	if( !is_dir( get_template_directory() . '/api_json/' ) ) {
		mkdir( get_template_directory() . '/api_json/', 0755, true );
	}
}