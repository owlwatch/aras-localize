<?php

namespace Aras;

require_once( get_stylesheet_directory() . '/config.php');
require_once( get_stylesheet_directory() . '/src/asset-loader.php');
require_once( get_stylesheet_directory() . '/src/scripts.php');

add_action('after_setup_theme', function () {
	load_theme_textdomain('aras', get_stylesheet_directory() . '/languages');
});

/**
 * Increase timeout for Marketo API requests
 * Prevents "cURL error 28: Operation timed out" errors for slower Marketo submissions
 */
add_filter('http_request_args', function( $args, $url ) {
	if ( strpos( $url, 'marketo' ) !== false ) {
		$args['timeout'] = 90; // Increased from default 30 seconds to 90 seconds
	}
	return $args;
}, 10, 2 );

add_filter('https_request_args', function( $args, $url ) {
	if ( strpos( $url, 'marketo' ) !== false ) {
		$args['timeout'] = 90; // Increased from default 30 seconds to 90 seconds
	}
	return $args;
}, 10, 2 );
