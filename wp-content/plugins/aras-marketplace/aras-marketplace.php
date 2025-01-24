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

// load the plugin
$app = App::getInstance();