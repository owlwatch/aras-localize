<?php
/**
 * Plugin Name: Aras Marketplace
 * Description: Add Marketplace functionality to the Aras website.
 * Version: 1.0
 * Author: Owl Watch Consulting
 */
namespace Aras\Marketplace;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// define some constants
define( 'ARAS_MARKETPLACE_PATH', plugin_dir_path( __FILE__ ) );
define( 'ARAS_MARKETPLACE_URL', plugin_dir_url( __FILE__ ) );

require_once( __DIR__ . '/vendor/autoload.php' );

function app(){
	return App::getInstance();
}

function get_first_term($taxonomy, $post_id=null ){
	if( $post_id === null ){
		$post_id = get_the_ID();
	}
	$terms = get_the_terms($post_id, $taxonomy);
	if( $terms && ! is_wp_error($terms) ){
		return $terms[0];
	}
	return false;
}

app(); // initialize the app
