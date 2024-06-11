<?php
//function flush_all_rewrite_rules()
//{
//	flush_rewrite_rules();
//}
//add_action('init', 'flush_all_rewrite_rules');


/* Add brand colors to the color picker in the WYSIWYG editor  */
function my_mce4_options($init)
{
	$custom_colours = '
  	"1e212b", "Dark Blue",
  	"ce2127", "Red",
  	"d49623", "Gold",
  	"f6f6f6", "Light Grey",
  	"999999", "Grey",
  	"000000", "Black",
  	"ffffff", "White",		
	';
	$init['textcolor_map'] = '[' . $custom_colours . ']';
	// 8 swatches per row - if more than 8 swatches above, add a row
	$init['textcolor_rows'] = 1;
	return $init;
}
add_filter('tiny_mce_before_init', 'my_mce4_options');


/* Function to sanitize a text string into lowercase_and_underscores  */
function sanitize_css_class($string)
{
	$sanitized_string = preg_replace('/[^a-zA-Z0-9_-]/', '_', $string);
	$sanitized_string = preg_replace('/^[^a-zA-Z0-9_-]+/', '', $sanitized_string);
	$sanitized_string = trim($sanitized_string);
	$sanitized_string = str_replace(' ', '_', $sanitized_string);
	$sanitized_string = strtolower($sanitized_string);
	return $sanitized_string;
}
/* Function to sanitize a text string into lowercase-and-dashes  */
function sanitize_and_convert_to_slug($text)
{
	$text = strtolower($text);
	$text = str_replace(' ', '-', $text);
	$text = preg_replace('/[^a-z0-9\-]/', '', $text);
	return $text;
}





/********************************************************* */
/********************* BLOG COMMENTS ********************* */
/********************************************************* */

function custom_comment_moderation_recipients($emails, $comment_id)
{
	// Get the email addresses from the ACF field
	$additional_emails = get_field('comment_moderation_emails', 'option');
	// Get the site admin email
	$admin_email = get_option('admin_email');
	// Get the post author email
	$comment = get_comment($comment_id);
	$post_author_email = get_the_author_meta('user_email', $comment->user_id);

	// Remove the admin email and post author email from the default list of recipients
	$emails = array_diff($emails, array($admin_email, $post_author_email));
	if ($additional_emails) {
		// Convert the comma-separated list into an array
		$additional_emails_array = array_map('trim', explode(',', $additional_emails));
		// Remove the admin email and post author email from the additional emails array
		$additional_emails_array = array_diff($additional_emails_array, array($admin_email, $post_author_email));
		// Merge the additional emails with the filtered ones
		$emails = array_merge($emails, $additional_emails_array);
	}

	return $emails;
}
add_filter('comment_moderation_recipients', 'custom_comment_moderation_recipients', 10, 2);


// Save first name and last name as comment meta data
function save_comment_meta_data($comment_ID)
{
	if (isset($_POST['first_name'])) {
		add_comment_meta($comment_ID, 'first_name', $_POST['first_name']);
	}
	if (isset($_POST['last_name'])) {
		add_comment_meta($comment_ID, 'last_name', $_POST['last_name']);
	}
}
add_action('comment_post', 'save_comment_meta_data');

// Add custom columns to comments list table
function custom_comment_columns($columns)
{
	// Add custom columns
	$columns['first_name'] = __('First Name');
	$columns['last_name'] = __('Last Name');

	return $columns;
}
add_filter('manage_edit-comments_columns', 'custom_comment_columns');

// Display custom column values
function custom_comment_column_content($column, $comment_ID)
{
	switch ($column) {
		case 'first_name':
			$first_name = get_comment_meta($comment_ID, 'first_name', true);
			if (!empty($first_name)) {
				echo esc_html($first_name);
			} else {
				echo __('N/A');
			}
			break;
		case 'last_name':
			$last_name = get_comment_meta($comment_ID, 'last_name', true);
			if (!empty($last_name)) {
				echo esc_html($last_name);
			} else {
				echo __('N/A');
			}
			break;
		default:
			break;
	}
}
add_action('manage_comments_custom_column', 'custom_comment_column_content', 10, 2);
