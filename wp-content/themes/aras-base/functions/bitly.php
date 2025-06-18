<?php

// is the request for a 'mkt.aras.com' url, or was it referred by a 'mkt.aras.com' url?
if( !isset($_REQUEST['debug-bitly']) && strpos($_SERVER['HTTP_HOST'], 'mkt.aras.com') === false ) {
	return;
}

$is_debug = isset($_REQUEST['debug-bitly']);

// get our bitly array[link] = destination map from cache
$bitly_link_map = wp_cache_get('bitly_link_map', 'aras');

if( !$bitly_link_map ){
	$bitly_link_map = [];
	// otherwise, lets read the get_template_directory() . '/data/bitly-legacy-links.csv'
	// into an array of associative arrays, using the first row of the csv as the key
	// for the following rows
	$bitly_legacy_links = array_map('str_getcsv', file(get_template_directory() . '/data/bitly-legacy-links.csv'));
	$keys = array_shift($bitly_legacy_links);

	// reverse the order of $bitly_legacy_links
	$bitly_legacy_links = array_reverse($bitly_legacy_links);

	$link_index = array_search('Link', $keys);
	$custom_link_index = array_search('Custom Link', $keys);
	$destination_index = array_search('Destination URL', $keys);

	if( $link_index === false || $destination_index === false ) {
		error_log("Link or Destination column not found in header row");
		if( $is_debug ){
			die('Link or Destination column not found in header row');
		}
		return;
	}

	foreach( $bitly_legacy_links as $i => $row ) {
		if( count($keys) != count($row) ) {
			error_log("Row $i has a different number of columns than the header row");
			continue;
		}
		// get the path from the custom link
		$link = !empty($row[$custom_link_index]) ? $row[$custom_link_index] : $row[$link_index];
		$link_path = parse_url($link, PHP_URL_PATH);
		$bitly_link_map[$link_path] = $row[$destination_index];
	}

	wp_cache_set('bitly_link_map', $bitly_link_map, 'aras');
}


$path = strtok( $_SERVER['REQUEST_URI'], '?' );

if( $is_debug ){
	header('Content-Type: application/json');
	echo json_encode([
		'path' => $path,
		'destination' => !empty($bitly_link_map[$path]) ? $bitly_link_map[$path] : 'Not Found',
		'bitly_link_map' => $bitly_link_map
	]);
	exit;
}

// check our request path against the bitly link map
if( !empty( $bitly_link_map[$path] ) ) {
	
	// if we have a match, redirect to the destination
	// header('Location: ' . $bitly_link_map[$_SERVER['REQUEST_URI']]);
	wp_redirect( $bitly_link_map[$path], 301, 'Bit.ly Legacy Redirects' );
	exit;
}