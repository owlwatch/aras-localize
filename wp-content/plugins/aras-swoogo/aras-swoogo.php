<?php
/**
 * Plugin Name: Aras - Swoogo Integration
 * Description: Adds integration with the Swoogo platform
 * Version: 1.0
 * Author: Owl Watch Consulting
 */
namespace Aras\Swoogo;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// define some constants
define( 'ARAS_SWOOGO_PATH', plugin_dir_path( __FILE__ ) );
define( 'ARAS_SWOOGO_URL', plugin_dir_url( __FILE__ ) );

require_once( __DIR__ . '/vendor/autoload.php' );

// load the plugin
$app = App::getInstance();