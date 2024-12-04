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

		if( $heading->hasAttribute('id') ){
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

function aras_convert_gist_links_to_oembeds($content){
	// parse for direct links
	return preg_replace('/<a[^>]+href=[\'"](https:\/\/gist\.github\..+?)[\'"][^>]*?>gist[^<]+<\/a>/i', '$1', $content);
}

add_filter('the_content', 'aras_convert_gist_links_to_oembeds', 1, 1);