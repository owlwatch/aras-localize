<?php

namespace Aras;

require_once( get_stylesheet_directory() . '/config.php');
require_once( get_stylesheet_directory() . '/src/asset-loader.php');
require_once( get_stylesheet_directory() . '/src/scripts.php');

add_action('after_setup_theme', function () {
	load_theme_textdomain('aras', get_stylesheet_directory() . '/languages');
});
