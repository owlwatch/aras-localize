<?php
/**
 * Plugin Name: Aras Support Matrix
 * Description: Support matrix UI and REST-backed data model for Aras Innovator compatibility data.
 * Version: 0.1.0
 * Author: Owl Watch Consulting
 */

if (! defined('ABSPATH')) {
	exit;
}

define('ARAS_SUPPORT_MATRIX_PATH', plugin_dir_path(__FILE__));
define('ARAS_SUPPORT_MATRIX_URL', plugin_dir_url(__FILE__));

$aras_support_matrix_includes = array(
	__DIR__ . '/includes/class-aras-support-matrix-vite-service.php',
	__DIR__ . '/includes/class-aras-support-matrix-post-types.php',
	__DIR__ . '/includes/class-aras-support-matrix-rest.php',
	__DIR__ . '/includes/class-aras-support-matrix-importer.php',
	__DIR__ . '/includes/class-aras-support-matrix-plugin.php',
);

foreach ($aras_support_matrix_includes as $aras_support_matrix_include) {
	if (! is_readable($aras_support_matrix_include)) {
		wp_die(
			esc_html(
				sprintf(
					'Aras Support Matrix failed to load a required file: %s',
					$aras_support_matrix_include
				)
			)
		);
	}

	require_once $aras_support_matrix_include;
}

register_activation_hook(__FILE__, array('ArasSupportMatrixPlugin', 'activate'));
register_deactivation_hook(__FILE__, array('ArasSupportMatrixPlugin', 'deactivate'));

ArasSupportMatrixPlugin::instance();
