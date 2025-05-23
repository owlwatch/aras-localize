<?php
/**
 * Plugin Name: Aras - Competitor Tables
 * Description: Adds competitor tables for the website
 * Version: 1.0
 * Author: Owl Watch Consulting
 */
namespace Aras\Competitors;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// define some constants
define( 'ARAS_COMPETITORS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ARAS_COMPETITORS_URL', plugin_dir_url( __FILE__ ) );

require_once( __DIR__ . '/vendor/autoload.php' );

// load the plugin
function app(){
	return App::getInstance();
}

app(); // initialize the app