<?php

namespace Aras;

function getViteService(): ViteService
{
	static $viteService = null;
	if ($viteService) {
		return $viteService;
	}
	require_once(get_template_directory() . '/functions/vite-service.php');
	$viteService = new ViteService(
		get_stylesheet_directory() . '/dist',
		get_stylesheet_directory_uri() . '/dist/',
		'http://localhost:5176'
	);
	return $viteService;
}

add_action('init', function () {
	if( is_admin() ) {
		return;
	}
	$vite = getViteService();
	// enqueue the main entry point
	$vite->enqueue('src/index.js');
});

add_action('wp_enqueue_scripts', function () {
	// enqueue jquery
	wp_enqueue_script('jquery');
});

// add admin styles
add_action('admin_init', function () {
	$vite = getViteService();
	$vite->enqueue('src/admin.scss', 'admin_head');

	$urls = $vite->cssUrls('src/index.js');
	if (is_array($urls) && count($urls) > 0) {
		foreach ($urls as $url) {
			add_editor_style( $url );
		}
	}
});
