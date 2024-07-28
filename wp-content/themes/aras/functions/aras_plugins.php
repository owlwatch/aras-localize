<?php


/***************************************************************/
/********************          ACF          ********************/
/***************************************************************/

// /* For development - ensure all ACF is compliant with ACF 6.2.7 */
// add_action('acf/will_remove_unsafe_html', 'print_backtrace_for_unsafe_html_removal', 10, 4);
// add_action('acf/removed_unsafe_html', 'print_backtrace_for_unsafe_html_removal', 10, 4);
// function print_backtrace_for_unsafe_html_removal($function, $selector, $field_object, $post_id)
// {
//   echo '<h4 style="color:red">Detected Potentially Unsafe HTML Modification</h4>';
//   echo '<pre>';
//   debug_print_backtrace();
//   echo '</pre>';
// }

/* Sets save point for ACF fields */
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
function my_acf_json_save_point($path)
{
	$path = get_stylesheet_directory() . '/acf-json';
	return $path;
}

// Adjust ACF post object fields to only filter by post title instead of by content.
function add_search_only_titles_filter($args, $field, $post_id)
{
	add_filter('posts_search', '__search_by_title_only', 500, 2);
	return $args;
}
function __search_by_title_only($search, &$wp_query)
{
	global $wpdb;
	if (empty($search))
		return $search; // skip processing - no search term in query
	$q = $wp_query->query_vars;
	$n = !empty($q['exact']) ? '' : '%';
	$search =
		$searchand = '';
	foreach ((array) $q['search_terms'] as $term) {
		$term = esc_sql(like_escape($term));
		$search .= "{$searchand}($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
		$searchand = ' AND ';
	}
	if (!empty($search)) {
		$search = " AND ({$search}) ";
		if (!is_user_logged_in())
			$search .= " AND ($wpdb->posts.post_password = '') ";
	}
	return $search;
}
add_filter('acf/fields/post_object/query', 'add_search_only_titles_filter', 10, 3);


////Apply the language custom field to all posts on refresh. Enable this to resync all of them at once; should not be needed anymore.
//function auto_apply_language_custom_field()
//{
//	$args = array(
//		'post_type' => 'post',
//		'posts_per_page' => -1,
//	);
//	$posts = get_posts($args);
//	foreach ($posts as $post) {
//		$language = apply_filters('wpml_post_language_details', NULL, $post->ID);
//		if ($language) {
//			update_field('post_lang_code', $language['language_code'], $post->ID);
//		}
//	}
//}
//// Run the function once to sync them all
//auto_apply_language_custom_field();

// During saves, check if the wpml_current_language field has a value and add it if not
function set_language_custom_field($post_id)
{
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	// Check if this is a revision.
	if (wp_is_post_revision($post_id)) return;
	// Get the language of the current page.
	$language = apply_filters('wpml_current_language', NULL);
	// If language is found, update the custom field.
	if ($language) {
		update_field('post_lang_code', $language, $post_id);
	}
}
add_action('save_post', 'set_language_custom_field');



/****************************************************************/
/********************          WPML          ********************/
/****************************************************************/


// WPML Media workaround to re-enable SVG URLs from SVG Support. This fixes an issue with using SVG images on Resources for some reason.
//https://wpml.org/errata/svg-support-activating-wpml-media-strips-uploads-folders-from-svg-image-url/
add_filter('wp_get_attachment_metadata', 'wpml_compsupp6933_fix_attachment_metadata_file_path', 10, 2);
function wpml_compsupp6933_fix_attachment_metadata_file_path($data, $attachment_id)
{
	// Only apply the workaround if WPML Media and SVG Support plugins are active
	if (class_exists('WPML_Media') && function_exists('bodhi_svgs_generate_svg_attachment_metadata')) {
		if (isset($data['file']) && !preg_match('/\d{4}\/\d{2}\//', $data['file'])) {
			// Get the upload directory info
			$upload_dir_info = wp_upload_dir();
			// Extract the year and month from the basedir
			$year_month = date('Y/m', strtotime(get_post_field('post_date', $attachment_id)));
			// Prepend the year and month to the file
			$data['file'] = $year_month . '/' . $data['file'];
		}
	}
	return $data;
}

// Custom shortcode to display language dropdown filter
function custom_language_dropdown_filter()
{
	ob_start();
?>
	<?php
	// Get the current language
	$current_language = apply_filters('wpml_current_language', NULL);
	$currentURL = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	//if ($_SERVER['QUERY_STRING']) {
	//	$currentURL .= "?" . $_SERVER['QUERY_STRING'];
	//}
	// Get the list of available languages
	$languages = apply_filters('wpml_active_languages', NULL);
	?>
	<?php if (!empty($languages)) : ?>
		<button aria-label="language options" class="language-dropdown language-dropdown-button" type="button">
			<?php if ($current_language == 'fr-fr') {
				echo 'FR';
			} elseif ($current_language == 'de-de') {
				echo 'DE';
			} elseif ($current_language == 'ja-jp') {
				echo 'JP';
			} else {
				echo strtoupper($current_language);
			} ?>
		</button>
		<div class="language-dropdown-pane dropdown-pane language-dropdown-container" data-dropdown data-auto-focus="true" data-hover="true" data-hover-pane="true">
			<?php foreach ($languages as $code => $language) : ?>
				<?php
				$newURL = preg_replace('~/(en|fr-fr|de-de|ja-jp)(/|\?.*)?~', '/' . $code . '$2', $currentURL, 1);
				?>
				<a aria-label="<?php echo $language['native_name']; ?>" href="<?php echo $newURL; ?>"><img src="<?php echo $language['country_flag_url']; ?>" alt="<?php echo $language['native_name']; ?> Flag"><?php echo $language['native_name']; ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

<?php
	return ob_get_clean();
}
add_shortcode('custom_language_dropdown', 'custom_language_dropdown_filter');
// Add Shortcode
function featuredImageShortcode($atts)
{
	global $post;
	return get_the_post_thumbnail($post->ID, "full");
}
add_shortcode('featured_image', 'featuredImageShortcode');



/***************************************************************/
/*******************          YOAST          *******************/
/***************************************************************/


//Disable Yoast Redirect functions -- doing this because Yoast redirects are not compatible with this site, redirects are contained in Redirection
//Posts/pages
add_filter('Yoast\WP\SEO\post_redirect_slug_change', '__return_true');
//Taxonomies
add_filter('Yoast\WP\SEO\term_redirect_slug_change', '__return_true');
//Notification: Page moved to trash
add_filter('Yoast\WP\SEO\enable_notification_post_trash', '__return_false');
//Notification: Page URL changed
add_filter('Yoast\WP\SEO\enable_notification_post_slug_change', '__return_false');
//Notification: Tax moved to trash
add_filter('Yoast\WP\SEO\enable_notification_term_delete', '__return_false');
//Notification: Tax URL changed
add_filter('Yoast\WP\SEO\enable_notification_term_slug_change', '__return_false');

/*
 * Remove Yoast SEO Filters
 */
function aras_remove_yoast_seo_admin_filters() {
	global $wpseo_meta_columns;
	if ($wpseo_meta_columns) {
		remove_action('restrict_manage_posts', array($wpseo_meta_columns, 'posts_filter_dropdown'));
		remove_action('restrict_manage_posts', array($wpseo_meta_columns, 'posts_filter_dropdown_readability'));
	}
}
add_action('admin_init', 'aras_remove_yoast_seo_admin_filters', 20);
