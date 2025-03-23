<?php

function aras_add_ids_to_headings($content)
{
	// only for blogs for now
	if (!is_singular('post')) {
		return $content;
	}

	// Load the content into a DOMDocument for manipulation
	$dom = new DOMDocument();
	// Suppress errors due to malformed HTML
	libxml_use_internal_errors(true);
	$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
	libxml_clear_errors();

	// Get all heading tags (h1 to h6)
	$headings = [];
	foreach (range(1, 6) as $i) {
		$headings = array_merge($headings, iterator_to_array($dom->getElementsByTagName("h$i")));
	}

	// Keep track of IDs to avoid duplicates
	$used_ids = [];

	// Loop through all heading elements
	foreach ($headings as $heading) {

		if ($heading->hasAttribute('id')) {
			continue;
		}

		$text_content = $heading->textContent;
		// Sanitize the heading text to create a valid ID
		$id = sanitize_title($text_content);

		// Ensure the ID is unique
		$original_id = $id;
		$counter = 2;
		while (in_array($id, $used_ids)) {
			$id = $original_id . '-' . $counter;
			$counter++;
		}

		// Assign the unique ID
		$heading->setAttribute('id', $id);
		$used_ids[] = $id;
	}

	// Save the updated HTML
	$content = $dom->saveHTML($dom->getElementsByTagName('body')->item(0));

	// Return the modified content without the added <html> and <body> tags
	return preg_replace('~(?:<body>|</body>)~i', '', $content);
};

// add_filter('the_content', 'aras_add_ids_to_headings', 20, 1);

function aras_convert_gist_links_to_oembeds($content)
{
	// parse for direct links
	return preg_replace('/<a[^>]+href=[\'"](https:\/\/gist\.github\..+?)[\'"][^>]*?>gist[^<]+<\/a>/i', '$1', $content);
}

add_filter('the_content', 'aras_convert_gist_links_to_oembeds', 1, 1);

function aras_custom_japanese_excerpt_length($length)
{
	// Check if the current language is Japanese
	if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE == 'ja-jp') {

		return 3; // Set the number of words you want for Japanese excerpts
	}
	return $length; // Default excerpt length for other languages
}
// add_filter('excerpt_length', 'aras_custom_japanese_excerpt_length', 999999999);

function aras_wp_trim_words_for_japanese($text, $num_words, $more, $original_text)
{
	// Check if the content is in Japanese
	$is_japanese = (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE == 'ja-jp');

	if ($is_japanese) {
		// $text = $original_text;
		$characters_per_word = 4; // Japanese characters are double the size of English characters
		$max_length = $num_words * $characters_per_word; // Adjust this number as needed for your site
		// Custom length for Japanese excerpts
		$text_length = mb_strlen($text, 'UTF-8');

		if ($text_length > $max_length) {
			$text = mb_substr($text, 0, $max_length, 'UTF-8') . $more;
		}
	}
	return $text;
}
add_filter('wp_trim_words', 'aras_wp_trim_words_for_japanese', 10, 4);

// lets trim the value for gravity form fields
// with the name of 'description'

add_filter('vx_crm_post_fields', 'aras_trim_gravity_form_fields', 10, 4);

function aras_trim_gravity_form_fields($entry, $entry_id, $gf, $form )
{
	// fields to limit to 255 characters
	$fields = [
		'Last Touch Conversion Page',
		'Referrer'
	];

	// look for any fields in entry that have the name "Last Touch Conversion Page"
	// and ensure the length is no longer than 255 characters
	foreach( $form['fields'] as $field ) {
		if( in_array( $field->label, $fields) ) {
			$entry[ $field->id ] = substr( $entry[ $field->id ], 0, 255 );
		}
	}
	return $entry;
}

add_filter( 'gform_field_validation', function ( $result, $value, $form, $field ) {
    if ( $field->type === 'email' ) {
        if ( ! filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
            $result['is_valid'] = false;
            $result['message']  = 'Please enter a valid email address.';
        }
    }
    return $result;
}, 10, 4 );

// allow for mailto links in the URL field
add_filter('acf/validate_value', function( $valid, $value, $field, $input ){
	if( $field['type'] === 'url' ){
		// we also want to allow mailto:email values
		if( strpos($value, 'mailto:') === 0 ){
			// split the to get the email and test with filter_var
			$email = str_replace('mailto:', '', $value);
			if( filter_var($email, FILTER_VALIDATE_EMAIL) ){
				$valid = true;
			}
		}
	}
	return $valid;
}, 11, 4);