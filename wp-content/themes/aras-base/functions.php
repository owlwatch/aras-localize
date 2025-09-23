<?php
/** 
 * For more info: https://developer.wordpress.org/themes/basics/theme-functions/
 */

global $aras_base_config;
$aras_base_config = apply_filters('aras_config', [
	'version' => '1.0.0',
	'textdomain' => 'aras',
	'api_url' => 'https://api.aras.com',
	'site_url' => 'https://www.aras.com',
	'features' => []
]);

function is_aras_feature_enabled( $feature ) {
	global $aras_base_config;
	return in_array( $feature, $aras_base_config['features'] );
}


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
if( is_aras_feature_enabled('salesforce') ) {
	//Salesforce-related API connections (Partners, Academic Users)
	require_once(get_template_directory() . '/functions/apis/api_salesforce.php');
}

//MyI API Functions
if( is_aras_feature_enabled('myi') ) {
	// MyI API Functions
	require_once(get_template_directory() . '/functions/apis/api_myi.php');
}

if( is_aras_feature_enabled('gravity_forms') ) {
	//Gravity Forms Functions
	require_once(get_template_directory() . '/functions/apis/class.aras_gravity_forms.php');
}

// Swoogo Functions
if( is_aras_feature_enabled('swoogo') ) {
	// Swoogo API Functions
	require_once(get_template_directory() . '/functions/apis/api_swoogo.php');
}

// Verint API Functions
if( is_aras_feature_enabled('verint') ) {
	// Verint API Functions
	require_once(get_template_directory() . '/functions/apis/api_verint.php');
}


/*     POST TYPE CUSTOM FUNCTIONS     */

//AJAX load settings
require_once(get_template_directory() . '/functions/post_types/aras_ajax.php');

// Salseforce Post Types (Partners, Academic Users)
if( is_aras_feature_enabled('salesforce') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_sf_post_types.php');
}

//Resources CPT and Tax
if( is_aras_feature_enabled('resources') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_resources.php');
}

//Events
if( is_aras_feature_enabled('events') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_events.php');
}

//Documentation CPT
if( is_aras_feature_enabled('documentation') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_documentation.php');
}

//News CPT and Tax
if( is_aras_feature_enabled('news') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_news.php');
}

//Blog Tax
if( is_aras_feature_enabled('blog') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_blog.php');
}

//LP CPT
if( is_aras_feature_enabled('lp') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_lp.php');
}

//Demo CPT
if( is_aras_feature_enabled('demo') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_demo.php');
}

//Speakers CPT
if( is_aras_feature_enabled('speakers') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_speakers.php');
}

// User Profiles
if( is_aras_feature_enabled('user_profiles') ) {
	require_once(get_template_directory() . '/functions/post_types/aras_user-profiles.php');
}

if( is_aras_feature_enabled('quotes') ) {
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

if( is_aras_feature_enabled('qualified') ) {
	// Qualified API Functions
	require_once(get_template_directory() . '/functions/qualified.php');
}

if( is_aras_feature_enabled('google_ads_enhanced_conversions') ) {
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